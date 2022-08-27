<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Bill Controller
 * @author Allan J. Odeke <allanjodeke@gmtconsults.com>
 *  */
class Bill_payment extends CI_Controller {

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
            $this->data['privileges'] = array_column($this->data['privilege_list'], "privilege_code");
        }
        $this->load->model('bill_payment_model');
    }

    public function jsonlist() {
        $data['data'] = $this->bill_payment_model->get();
        echo json_encode($data);
    }

    public function view($id) {
        $neededcss = array("");
        $neededjs = array("plugins/validate/jquery.validate.min.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->data['bill_payment_detail'] = $this->bill_payment_model->get($id);
        if (empty($this->data['bill_payment_detail'])) {
            redirect("my404");
        }

        $this->data['title'] = $this->data['sub_title'] = "Bill payment transaction #" . $this->data['bill_payment_detail']['id'] . " details";
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('accounts/bill/payment/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function create() {
        $this->form_validation->set_rules('description', 'Payment memo', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('payment_date', 'Billing Date', array('required'), array('required' => '%s must be entered'));
        //$this->form_validation->set_rules('ref_no', 'Ref No.', array('required'), array('required' => '%s must be entered'));
        //$this->form_validation->set_rules('amount', 'Bill Amount', array('required', 'numeric'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('cash_account_id', 'Cash account', array('required'), array('required' => '%s must be selected'));
        $feedback['success'] = false;
        $data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $organisation_id = isset($_SESSION['organisation_id']) ? $_SESSION['organisation_id'] : 0;
            $upload_location = 'organisation_' . $organisation_id . '/expense/bill/payment/';
            if (is_numeric($this->input->post('id')) && in_array('3', $this->data['privileges'])) {
                if ($this->bill_payment_model->update($this->helpers->upload_file($upload_location))) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Bill details successfully updated";
                    $feedback['bill'] = $this->bill_payment_model->get($this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the bill details";
                }
            } else {
                if (in_array('1', $this->data['privileges'])) {
                    $bill_id = $this->bill_payment_model->set($this->helpers->upload_file($upload_location));
                    //insert the bill list items
                    if (is_numeric($bill_id)) {
                        $this->do_journal_transaction($bill_id);
                        $feedback['success'] = true;
                        $feedback['message'] = "Bill details successfully saved";
                    } else {
                        $this->bill_payment_model->abs_delete($bill_id);
                        $feedback['message'] = "There was a problem saving the bill data";
                    }
                }
            }
        }
        echo json_encode($feedback);
    }

    private function do_journal_transaction($bill_payment_id) {
        //first start by inserting the bill line items
        $this->load->model('bill_payment_line_model');

        if ($this->bill_payment_line_model->set($bill_payment_id)) {
            //then go and post to the respective accounts
            $this->load->model('journal_transaction_model');
            $data = [
                'transaction_date' => $this->input->post('payment_date'),
                'description' => $this->input->post('description'),
                'ref_no' => $this->input->post('ref_no'),
                'ref_id' => $bill_payment_id,
                'status_id' => 1,
                'journal_type_id' => 15
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);
            //then we prepare the journal transaction lines for the bill payment lines
            $bill_payment_lines = $this->input->post('bill_payment_line');
            $this->load->model('accounts_model');
            $transaction_lines = [];
            $total_amount = 0;
            //here we record how much is being cleared for each bill
            foreach ($bill_payment_lines as $bill_payment_line) {
                //we have to debit the payable account when paying the bill
                $normal_balance_side = $this->accounts_model->get_normal_side($bill_payment_line['supplier_account_id'],true);
                $total_amount = ($total_amount + $bill_payment_line['amount']);
                $transaction_line = [
                    $normal_balance_side => $bill_payment_line['amount'],
                    'narrative' => $bill_payment_line['narrative'],
                    'account_id' => $bill_payment_line['supplier_account_id'],
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
        if (in_array('4', $this->data['privileges'])) {
            if ($this->bill_payment_model->delete($this->input->post('id'))) {
                $response['success'] = TRUE;
                $response['message'] = "Bill payment successfully deleted.";
            } else {
                $response['message'] = "Bill payment details could not be deleted, contact support.";
            }
            echo json_encode($response);
        }
    }

    public function change_status() {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Bill payment details could not be " . $msg . "activated, please contact IT support.";
        $response['success'] = FALSE;
        if ($this->bill_payment_model->deactivate($this->input->post('id'))) {
            $response['message'] = "Bill payment details successfully been " . $msg . "activated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }

}
