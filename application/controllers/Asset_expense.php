<?php

/**
 * Description of Asset_expense
 *
 * @author Ajuna Reagan
 */
class Asset_expense extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('inventory_model');
        $this->load->library(array("form_validation", "helpers"));
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 8, $this->session->userdata('staff_id'));
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['privileges'] = array_column($this->data['privilege_list'], "privilege_code");
        }
    }

    public function jsonList() {
        $data['data'] = $this->inventory_model->get_asset_expense();
        echo json_encode($data);
    }

    public function create() {
        $this->form_validation->set_rules("expense_type_id", "Income Type", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("transaction_date", "Date recorded", array("required"), array("required" => "%s must be entered/selected"));
        $this->form_validation->set_rules("narrative", "Narrative", array("required"), array("required" => "%s must be entered"));
        $this->form_validation->set_rules("asset_id", "Asset", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("expense_account_id", "Expense account", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("fund_source_account_id", "Fund Source Account", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("amount", "Amount", array("required", "numeric"), array("required" => "%s must be entered"));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
                $transaction_data = $this->inventory_model->set_expense();
                if ($transaction_data!=FALSE) {
                    $this->do_journal_transaction($transaction_data);
                    $feedback['success'] = true;
                    $feedback['message'] = "Expense details successfully saved";
                } else {
                    $feedback['message'] = "There was a problem saving Expense Transaction";
                }
            }
        echo json_encode($feedback);
    }

    
     public function edit_transaction() {
        $this->form_validation->set_rules("expense_type_id", "Expense Type", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("transaction_date", "Date recorded", array("required"), array("required" => "%s must be entered/selected"));
        $this->form_validation->set_rules("narrative", "Narrative", array("required"), array("required" => "%s must be entered"));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->inventory_model->update_expense()) {
                    $journal =$this->inventory_model->get_journal_transaction();
                    $this->inventory_model->edit_journal_transaction($journal['id']);
                    $feedback['success'] = true;
                    $feedback['message'] = "Expense details successfully updated";

                      $this->helpers->activity_logs($_SESSION['id'],6,"Expense details",$feedback['message'],NULL,$this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating Expense transaction";

                     $this->helpers->activity_logs($_SESSION['id'],6,"Expense details",$feedback['message'],NULL,$this->input->post('id'));
                }
            } 
        }
        echo json_encode($feedback);
    }

     private function do_journal_transaction($transaction_data){
        $this->load->model('journal_transaction_model');
        $this->load->model('accounts_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('journal_transaction_line_model');
        $data = [
            'transaction_date'=> $this->input->post('transaction_date'),
            'description'=> $this->input->post('narrative'),
            'ref_no'=> $transaction_data['transaction_no'],
            'ref_id'=> $transaction_data['transaction_id'],
            'status_id'=> 1,
            'journal_type_id'=> 2
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        //then we prepare the journal transaction lines
               if($this->input->post('payment_id')==3){
               $debit_or_credit2 = $this->accounts_model->get_normal_side($this->input->post('fund_source_account_id'), false);
               } else {
                $debit_or_credit2 = $this->accounts_model->get_normal_side($this->input->post('fund_source_account_id'), true);
               }
                $debit_or_credit1 = $this->accounts_model->get_normal_side($this->input->post('expense_account_id'), false);
                $data = [
                    [
                        $debit_or_credit1=>$this->input->post('amount'),
                        'narrative'=> $this->input->post('transaction_date')." ".$this->input->post('narrative'),
                        'reference_no'=>$transaction_data['transaction_no'],
                        'reference_id'=>$transaction_data['transaction_id'],
                        'transaction_date'=>$this->input->post('transaction_date'),
                        'account_id'=> $this->input->post('expense_account_id'),
                        'status_id'=> 1
                    ],
                    [
                        $debit_or_credit2=> $this->input->post('amount'),
                        'narrative'=> $this->input->post('transaction_date')." ".$this->input->post('narrative'),
                        'reference_no'=>$transaction_data['transaction_no'],
                        'reference_id'=>$transaction_data['transaction_id'],
                        'transaction_date'=>$this->input->post('transaction_date'),
                        'account_id'=> $this->input->post('fund_source_account_id'),
                        'status_id'=> 1
                    ]
                ];
            $this->journal_transaction_line_model->set($journal_transaction_id,$data);
    }

    public function delete() {
        $response['message'] = "Expense  details could not be deleted, contact support.";
        $response['success'] = FALSE;
        if ($this->inventory_model->delete($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] = "Expense details successfully deleted.";
        }
        echo json_encode($response);

          $this->helpers->activity_logs($_SESSION['id'],6,"Deleting the status",$response['message'],NULL,$this->input->post('id'));
    }

    public function change_status() {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Expense details could not be " . $msg . "activated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->inventory_model->deactivate($this->input->post('id'))) {
            $response['message'] = "Expense details successfully " . $msg . "activated.";
            $response['success'] = TRUE;
            echo json_encode($response);
             $this->helpers->activity_logs($_SESSION['id'],6,"Changed the status",$response['message'],NULL,$this->input->post('id'));
        }
    }

}
