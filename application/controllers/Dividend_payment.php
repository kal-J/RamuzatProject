<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Dividend Payment Controller
 * @author Allan J. Odeke <allanjodeke@gmtconsults.com> Modified by Reagan ajuna
 *  */
class Dividend_payment extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model("miscellaneous_model");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 8, $this->session->userdata('staff_id'));
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['accounts_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        }
        $this->load->model('dividend_payment_model');
    }

    public function jsonlist() {
        $record_date = 	$this->input->post('record_date');
        $share_issuance_id = 	$this->input->post('share_issuance_id');
        $data['data'] = $this->dividend_payment_model->get(false,$record_date,$share_issuance_id);
        echo json_encode($data);
    }

    public function get_member_dividends() {
        $record_date = date('Y-m-d');
        $data['data'] = $this->dividend_payment_model->get(false,$record_date, $this->input->post('share_issuance_id'));
        echo json_encode($data);
    }

    public function view($id) {
        $neededcss = array("");
        $neededjs = array("plugins/validate/jquery.validate.min.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->data['dividend_payment_detail'] = $this->dividend_payment_model->get($id);
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 5');
        if (empty($this->data['dividend_payment_detail'])) {
            redirect("my404");
        }

        $this->data['title'] = $this->data['sub_title'] = "Dividend payment transaction #" . $this->data['dividend_payment_detail']['id'] . " details";
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('accounts/dividend/payment/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function create() {
        $this->form_validation->set_rules('description', 'Payment memo', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('payment_date', 'Dividend Date', array('required'), array('required' => '%s must be entered'));
   
        $this->form_validation->set_rules('cash_account_id', 'Cash account', array('required'), array('required' => '%s must be selected'));
        $feedback['success'] = false;
        $data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
           
            if (is_numeric($this->input->post('id')) && in_array('3', $this->data['accounts_privilege'])) {
                if ($this->dividend_payment_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Dividend payment details successfully updated";
                    $feedback['dividend'] = $this->dividend_payment_model->get($this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the dividend payment details";
                }
            } else {
                if (in_array('1', $this->data['accounts_privilege'])) {
                    $dividend_declaration_id = $this->dividend_payment_model->set();
                    //insert the dividend list items
                    if (is_numeric($dividend_declaration_id)) {
                        $this->do_journal_transaction($dividend_declaration_id);
                        $feedback['success'] = true;
                        $feedback['message'] = "Dividend details successfully saved";
                    } else {
                        $this->dividend_payment_model->abs_delete($dividend_declaration_id);
                        $feedback['message'] = "There was a problem saving the dividend payment data";
                    }
                }
            }
        }
        echo json_encode($feedback);
    }

    private function do_journal_transaction($dividend_payment_id) {
        //first start by inserting the dividend line items
        $this->load->model('dividend_payment_line_model');

        if ($this->dividend_payment_line_model->set($dividend_payment_id)) {
            //then go and post to the respective accounts
            $this->load->model('journal_transaction_model');
            $data = [
                'transaction_date' => $this->input->post('payment_date'),
                'description' => $this->input->post('description'),
                'ref_no' => $this->input->post('ref_no'),
                'ref_id' => $dividend_payment_id,
                'status_id' => 1,
                'journal_type_id' => 17
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);
            //then we prepare the journal transaction lines for the dividend payment lines
            $dividend_payment_lines = $this->input->post('dividend_payment_line');
            $this->load->model('accounts_model');
            $transaction_lines = [];
            $total_amount = 0;
            //here we record how much is being cleared for each dividend
            foreach ($dividend_payment_lines as $dividend_payment_line) {
                //we have to debit the payable account when paying the dividend
                $normal_balance_side = $this->accounts_model->get_normal_side($dividend_payment_line['receivable_account_id'],true);
                $total_amount = ($total_amount + $dividend_payment_line['amount']);
                $transaction_line = [
                    $normal_balance_side => $dividend_payment_line['amount'],
                    'narrative' => $dividend_payment_line['narrative'],
                    'account_id' => $dividend_payment_line['receivable_account_id'],
                    'status_id' => 1
                ];
                $transaction_lines[] = $transaction_line;
            }
            
            //then we have to affect the cash/bank accounts accordingly
            $normal_balance_side = $this->accounts_model->get_normal_side($this->input->post('cash_account_id'));
            $transaction_lines[] = [
                $normal_balance_side => $total_amount,
                'narrative' => $this->input->post('description'),
                'account_id' => $this->input->post('cash_account_id'),
                'status_id' => 1
            ];
            $this->load->model('journal_transaction_line_model');
            $this->journal_transaction_line_model->set($journal_transaction_id, $transaction_lines);
        }
    }

    public function delete() {
        $response['success'] = FALSE;
        if (in_array('4', $this->data['accounts_privilege'])) {
            if ($this->dividend_payment_model->delete($this->input->post('id'))) {
                $response['success'] = TRUE;
                $response['message'] = "Dividend payment successfully deleted.";
            } else {
                $response['message'] = "Dividend payment details could not be deleted, contact support.";
            }
            echo json_encode($response);
        }
    }

    public function change_status() {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Dividend payment details could not be " . $msg . "activated, please contact IT support.";
        $response['success'] = FALSE;
        if ($this->dividend_payment_model->deactivate($this->input->post('id'))) {
            $response['message'] = "Dividend payment details successfully been " . $msg . "activated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }

     public function print(){
        $this->load->model('organisation_model');
        $this->load->model('branch_model');
        $this->load->model('dividend_payment_model');
        $this->load->model('fiscal_model');
        $filename = $this->input->post('filename');
        $paper = $this->input->post('paper');
        $orientation = $this->input->post('orientation');
        $stream = $this->input->post('stream');

        $data['title'] = $_SESSION["org_name"];
        $data['filename'] = $filename;
        $data['fiscal'] = $this->fiscal_model->get($this->input->post('fiscal_year_id'));
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
         $record_date = $this->input->post('record_date');
        $data['dividends'] = $this->dividend_payment_model->get(false,$record_date);
        $html = $this->load->view('accounts/dividend/declaration/dividend_paid/print_out',$data,true);

        $this->pdfgenerator->generate($html, $filename,$stream,$paper,$orientation);
    }

}
