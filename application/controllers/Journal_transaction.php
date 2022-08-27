<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Journal Transaction Controller
 *  */
class Journal_transaction extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 8, $this->session->userdata('staff_id'));
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['accounts_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        }
        $this->load->model('journal_transaction_model');
        $this->load->model('accounts_model');
    }

    public function jsonList2() {
        $this->data['data'] = $this->journal_transaction_model->get();
        echo json_encode($this->data);
    }

    public function jsonList() {
        $data['draw'] = intval($this->input->post('draw'));
        $data['data'] = $this->journal_transaction_model->get_dTable();
        $filtered_records_cnt = $this->journal_transaction_model->get_found_rows();
        $all_data = $this->journal_transaction_model->get();
        //total records
        $data['recordsTotal'] = $all_data;
        $data['recordsFiltered'] = current($filtered_records_cnt);
        echo json_encode($data);
    }

    
   public function create() {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('description', 'Description', 'required');
       // $this->form_validation->set_rules('ref_no', 'Ref', 'required');
        $this->form_validation->set_rules('transaction_date', 'Transaction Date', 'required');

        $journal_transaction_id = $this->input->post("id");
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $this->load->model("journal_transaction_line_model");

            $this->db->trans_begin();
            if (is_numeric($journal_transaction_id)) {
              $this->journal_transaction_model->set();
              $message=$this->create_jtrls($journal_transaction_id);
              $this->journal_transaction_line_model->delete_lines($journal_transaction_id);
                if ($this->db->trans_status() && $message ==1) {
                    $this->db->trans_commit();
                    $feedback['success'] = TRUE;
                    $feedback['message'] = "General journal entry details successfully updated";

                   
                    // $feedback['new_transaction'] = $this->journal_transaction_model->get($journal_transaction_id);
                    // $feedback['new_transaction']['journal_transaction_line'] = $this->journal_transaction_line_model->get("journal_transaction_id=$journal_transaction_id");
                }else{
                    $this->db->trans_rollback();
                    $feedback['success'] = FALSE;
                    if($message==false){
                    $feedback['message'] = "Failed, general journal entry details could not be updated";
                     //$this->helpers->activity_logs($_SESSION['id'],8,"Creating Journal",$feedback['message']." -# ".$_POST['journal_transaction_id'],NULL,null);
                    } else {
                    $feedback['message'] = $message;
                      //$this->helpers->activity_logs($_SESSION['id'],8,"Creating Journal",$feedback['message']." -# ".$_POST['journal_transaction_id'],NULL,null);

                    }
                }
               
            } else {
                $journal_transaction_id = $this->journal_transaction_model->set();
                $message=$this->create_jtrls($journal_transaction_id);
                if ($this->db->trans_status() && $message ==1) {
                    $this->db->trans_commit();
                    $feedback['success'] = TRUE;
                    $feedback['message'] = "General journal entry details successfully saved";
                      //$this->helpers->activity_logs($_SESSION['id'],8,"Creating Journal",$feedback['message']." -# ".$_POST['journal_transaction_id'],NULL,null);

                    // $feedback['new_transaction'] = $this->journal_transaction_model->get($journal_transaction_id);
                    // $feedback['new_transaction']['journal_transaction_lines'] = $this->journal_transaction_line_model->get("journal_transaction_id=$journal_transaction_id");
                }else{
                    $this->db->trans_rollback();
                    $feedback['success'] = FALSE;
                    if($message==false){
                    $feedback['message'] = "Failed, couldn't update general entry details";
                    
                      //$this->helpers->activity_logs($_SESSION['id'],8,"Creating Journal",$feedback['message']." -# ".$_POST['journal_transaction_id'],NULL,null);

                    } else {
                    $feedback['message'] = $message;
                    }
                }
            }
        }
        echo json_encode($feedback);
    }

        private function create_jtrls($journal_transaction_id) {
        $data = $this->input->post('journal_transaction_line');
        foreach ($data as $key => $value) {//it is a new entry, so we insert afresh
            if (isset($value['account_id']) && is_numeric($value['account_id']) && ((isset($value['debit_amount']) && is_numeric($value['debit_amount'])) || (isset($value['credit_amount']) && is_numeric($value['credit_amount'])))) {

                $debit_or_credit = $this->accounts_model->get_normal_side($value['account_id']);
                $normal_side=($debit_or_credit=='credit_amount')?2:1;
                if(is_numeric($value['debit_amount']) &&($normal_side==2)){
                $response = $this->helpers->account_balance($value['account_id'],$value['debit_amount']);
                }elseif (is_numeric($value['credit_amount']) &&($normal_side==1)) {
                $response = $this->helpers->account_balance($value['account_id'],$value['credit_amount']);
                }else{
                $response =TRUE;  
                }

                if($response===TRUE){
                $message = $this->journal_transaction_line_model->set2($journal_transaction_id,$value);
                }else{
                $message=$response;
                break;
                }
            }
        }
        return $message;
    }

    public function view($journal_transaction_id) {
        $this->data['detail'] = $this->journal_transaction_model->get2($journal_transaction_id);
        if (empty($this->data['detail'])) {
            show_404();
        }

        $this->data['title'] = "#".$this->data['detail']['id']." {".$this->data['detail']['type_name']."} transactions";
        $this->data['sub_title'] = "#".$this->data['detail']["id"];

        $this->template->title = $this->data['title'];
        $this->template->content->view('accounts/transaction/view', $this->data);
        // Publish the template
        $this->template->publish();
    }
    
    public function reverse_transaction() {
        $this->load->model('journal_transaction_model');
        $this->form_validation->set_rules("reverse_msg", "Reason", array("required"), array("required" => "%s must be entered"));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                   $data = array();
                    $data['reversed_by'] = $_SESSION['id'];
                    $data['reversed_date'] = date("Y-m-d H:i:s");
                    $data['reverse_msg'] = $this->input->post('reverse_msg');
                    $data['status_id'] = 3;
                if ($this->journal_transaction_model->reverse_main($data,$_POST['id'])) {
                    $this->journal_transaction_model->reverse_lines($data,$_POST['id']);
                    $feedback['success'] = true;
                    $feedback['message'] = "Transaction successfully cancled";
                } else {
                    $feedback['message'] = "There was a problem reversing the transaction";
                }
            } 
        }
        echo json_encode($feedback);
    }

    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have the rights to delete this record";
        $response['success'] = FALSE;

        $journal_transaction_id = $this->input->post('id');
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        if (($response['success'] = $this->journal_transaction_model->delete($journal_transaction_id)) === true) {
            $response['message'] = "General journal entry details successfully deleted";
        }
        // }
        echo json_encode($response);
    }

}
