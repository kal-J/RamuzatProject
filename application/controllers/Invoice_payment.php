<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Invoice Payment Controller
 * @author Allan J. Odeke <allanjodeke@gmtconsults.com>
 *  */
class Invoice_payment extends CI_Controller {

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
        $this->load->model('invoice_payment_model');
    }

    public function jsonlist() {
        $data['data'] = $this->invoice_payment_model->get();
        echo json_encode($data);
    }

    public function view($id) {
        $neededcss = array("");
        $neededjs = array("plugins/validate/jquery.validate.min.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->data['invoice_payment_detail'] = $this->invoice_payment_model->get($id);
        if (empty($this->data['invoice_payment_detail'])) {
            redirect("my404");
        }

        $this->data['title'] = $this->data['sub_title'] = "Invoice payment transaction #" . $this->data['invoice_payment_detail']['id'] . " details";
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('accounts/invoice/payment/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function create() {
        $this->form_validation->set_rules('description', 'Payment memo', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('payment_date', 'Invoiceing Date', array('required'), array('required' => '%s must be entered'));
        //$this->form_validation->set_rules('ref_no', 'Ref No.', array('required'), array('required' => '%s must be entered'));
        //$this->form_validation->set_rules('amount', 'Invoice Amount', array('required', 'numeric'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('cash_account_id', 'Cash account', array('required'), array('required' => '%s must be selected'));
        $feedback['success'] = false;
        $data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $organisation_id = isset($_SESSION['organisation_id']) ? $_SESSION['organisation_id'] : 0;
            $upload_location = 'organisation_' . $organisation_id . '/income/invoice/payment/';
            if (is_numeric($this->input->post('id')) && in_array('3', $this->data['accounts_privilege'])) {
                if ($this->invoice_payment_model->update($this->helpers->upload_file($upload_location))) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Invoice details successfully updated";
                    $feedback['invoice'] = $this->invoice_payment_model->get($this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the invoice details";
                }
            } else {
                if (in_array('1', $this->data['accounts_privilege'])) {
                    $invoice_id = $this->invoice_payment_model->set($this->helpers->upload_file($upload_location));
                    //insert the invoice list items
                    if (is_numeric($invoice_id)) {
                        $this->do_journal_transaction($invoice_id);
                        $feedback['success'] = true;
                        $feedback['message'] = "Invoice details successfully saved";
                    } else {
                        $this->invoice_payment_model->abs_delete($invoice_id);
                        $feedback['message'] = "There was a problem saving the invoice data";
                    }
                }
            }
        }
        echo json_encode($feedback);
    }

    private function do_journal_transaction($invoice_payment_id) {
        //first start by inserting the invoice line items
        $this->load->model('invoice_payment_line_model');

        if ($this->invoice_payment_line_model->set($invoice_payment_id)) {
            //then go and post to the respective accounts
            $this->load->model('journal_transaction_model');
            $data = [
                'transaction_date' => $this->input->post('payment_date'),
                'description' => $this->input->post('description'),
                'ref_no' => $this->input->post('ref_no'),
                'ref_id' => $invoice_payment_id,
                'status_id' => 1,
                'journal_type_id' => 17
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);
            //then we prepare the journal transaction lines for the invoice payment lines
            $invoice_payment_lines = $this->input->post('invoice_payment_line');
            $this->load->model('accounts_model');
            $transaction_lines = [];
            $total_amount = 0;
            //here we record how much is being cleared for each invoice
            foreach ($invoice_payment_lines as $invoice_payment_line) {
                //we have to debit the payable account when paying the invoice
                $normal_balance_side = $this->accounts_model->get_normal_side($invoice_payment_line['receivable_account_id'],true);
                $total_amount = ($total_amount + $invoice_payment_line['amount']);
                $transaction_line = [
                    $normal_balance_side => $invoice_payment_line['amount'],
                    'narrative' => $invoice_payment_line['narrative'],
                    'account_id' => $invoice_payment_line['receivable_account_id'],
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
            if ($this->invoice_payment_model->delete($this->input->post('id'))) {
                $response['success'] = TRUE;
                $response['message'] = "Invoice payment successfully deleted.";
            } else {
                $response['message'] = "Invoice payment details could not be deleted, contact support.";
            }
            echo json_encode($response);
        }
    }

    public function change_status() {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Invoice payment details could not be " . $msg . "activated, please contact IT support.";
        $response['success'] = FALSE;
        if ($this->invoice_payment_model->deactivate($this->input->post('id'))) {
            $response['message'] = "Invoice payment details successfully been " . $msg . "activated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }

}
