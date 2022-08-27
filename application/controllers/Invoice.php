<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Invoice Controller
 * @author Allan J. Odeke <allanjodeke@gmtconsults.com>
 *  */
class Invoice extends CI_Controller {

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
        $this->load->model('invoice_model');
    }

    public function jsonlist() {
        $data['data'] = $this->invoice_model->get();
        echo json_encode($data);
    }

    public function view($id) {
        $neededcss = array("fieldset.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->data['invoice'] = $this->invoice_model->get($id);
        if (empty($this->data['invoice'])) {
            redirect("my404");
        }

        $this->data['title'] = $this->data['sub_title'] = "Invoice #" . $this->data['invoice']['id'];
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('accounts/invoice/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function create() {
        $this->form_validation->set_rules('description', 'Narrative', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('invoice_date', 'Date Invoice', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('due_date', 'Due Date', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('discount_account_id', 'Discount account', array('callback_required_discount_acc'), array('required_discount_acc' => '%s must be selected'));
        $this->form_validation->set_rules('discount_amount', 'Discount allowed', array('numeric'), array('numeric' => '%s must be a number'));
        $this->form_validation->set_rules('receivable_account_id', 'Debtors account', array('required'), array('required' => '%s must be selected'));
        $feedback['success'] = false;
        $data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $organisation_id = isset($_SESSION['organisation_id']) ? $_SESSION['organisation_id'] : 0;
            $upload_location = 'organisation_' . $organisation_id . '/income/invoice/';
            if (is_numeric($this->input->post('id')) && in_array('3', $this->data['accounts_privilege'])) {
                if ($this->invoice_model->update($this->helpers->upload_file($upload_location))) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Invoice details successfully updated";
                    $feedback['invoice'] = $this->invoice_model->get($this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the invoice details";
                }
            } else {
                if (in_array('1', $this->data['accounts_privilege'])) {
                    $invoice_id = $this->invoice_model->set($this->helpers->upload_file($upload_location));
                    //insert the invoice list items
                    if (is_numeric($invoice_id)) {
                        $this->do_journal_transaction($invoice_id);
                        $feedback['success'] = true;
                        $feedback['message'] = "Invoice details successfully saved";
                    } else {
                        $this->invoice_model->abs_delete($invoice_id);
                        $feedback['message'] = "There was a problem saving the invoice data";
                    }
                }
            }
        }
        echo json_encode($feedback);
    }

    public function required_discount_acc($discount_account_id) {
        $validated = FALSE;
            if ($this->input->post('discount') !== NULL) {
                $validated = TRUE;
            } else {
                $validated = !($discount_account_id === NULL);
            }
            return $validated;
    }
    
    private function do_journal_transaction($invoice_id) {
        //first start by inserting the invoice line items
        $this->load->model('invoice_line_model');

        if ($this->invoice_line_model->set($invoice_id)) {
            //then go and post to the respective accounts
            $this->load->model('journal_transaction_model');
            $data = [
                'transaction_date' => $this->input->post('invoice_date'),
                'description' => $this->input->post('description'),
                'ref_no' => $this->input->post('ref_no'),
                'ref_id' => $invoice_id,
                'status_id' => 1,
                'journal_type_id' => 16
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);
            //then we prepare the journal transaction lines for the invoice lines
            $invoice_line_items = $this->input->post('invoice_line_item');
            $this->load->model('accounts_model');
            $transaction_lines = [];
            $total_amount = 0;
            foreach ($invoice_line_items as $invoice_line_item) {
                $normal_balance_side = $this->accounts_model->get_normal_side($invoice_line_item['account_id']);
                $total_amount = ($total_amount + $invoice_line_item['amount']);
                $transaction_line = [
                    $normal_balance_side => $invoice_line_item['amount'],
                    'narrative' => $invoice_line_item['narrative'],
                    'account_id' => $invoice_line_item['account_id'],
                    'status_id' => 1
                ];
                $transaction_lines[] = $transaction_line;
            }
            $normal_balance_side = $this->accounts_model->get_normal_side($this->input->post('receivable_account_id'));
            $transaction_lines[] = [
                $normal_balance_side => $total_amount,
                'narrative' => $this->input->post('description'),
                'account_id' => $this->input->post('receivable_account_id'),
                'status_id' => 1
            ];
                 //if there was any discount for a given invoice, we should record it here
                if(is_numeric($this->input->post('discount_account_id')) && is_numeric($this->input->post('discount_amount'))){
                    $normal_balance_side2 = $this->accounts_model->get_normal_side($this->input->post('discount_account_id'));
                    $transaction_lines[] = [
                        $normal_balance_side2 => $this->input->post('discount_amount'),
                        'narrative' => $this->input->post('narrative'),
                        'account_id' => $this->input->post('discount_account_id'),
                        'status_id' => 1
                    ];
                }
            $this->load->model('journal_transaction_line_model');
            $this->journal_transaction_line_model->set($journal_transaction_id, $transaction_lines);
        }
    }

    public function delete() {
        $response['success'] = FALSE;
        if (in_array('4', $this->data['privileges'])) {
            if ($this->invoice_model->delete($this->input->post('id'))) {
                $response['success'] = TRUE;
                $response['message'] = "Invoice successfully deleted.";
            } else {
                $response['message'] = "Invoice details could not be deleted, contact support.";
            }
            echo json_encode($response);
        }
    }

    public function change_status() {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Invoice details could not be " . $msg . "activated, please contact IT support.";
        $response['success'] = FALSE;
        if ($this->invoice_model->deactivate($this->input->post('id'))) {
            $response['message'] = "Invoice details successfully been " . $msg . "activated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }

}
