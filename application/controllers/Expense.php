<?php

/**
 * Description of Chart of accounts
 *
 * @author reagan
 */
class Expense extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('expense_model');
        $this->load->library(array("form_validation", "helpers"));
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 8, $this->session->userdata('staff_id'));
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['accounts_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        }
    }

    public function jsonList() {
        $where = FALSE;
        if ($this->input->post('organisation_id') !== NULL) {
            $where = "organisation_id = " . $this->input->post('organisation_id');
        }
        $data['data'] = $this->expense_model->get($where);
        echo json_encode($data);
    }

    public function view($id) {
        //$this->load->model('transactionChannel_model');
        $neededcss = array("fieldset.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->data['expense'] = $this->expense_model->get($id);
        if (empty($this->data['expense'])) {
            redirect("my404");
        }

        //$this->data['transaction_channels'] = $this->transactionChannel_model->get();
        $this->data['title'] = $this->data['sub_title'] = "Expense #" . $this->data['expense']['id'];
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('accounts/expense/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function create() {
        $this->form_validation->set_rules('description', 'Narrative', array('required'), array('required' => '%s must be entered'));
        //$this->form_validation->set_rules('amount', 'Expense Amount', array('required', 'numeric'), array('required' => '%s must be entered'));
        //$this->form_validation->set_rules('discount_account_id', 'Discount account', array('callback_required_discount_acc'), array('required_discount_acc' => '%s must be selected'));
        //$this->form_validation->set_rules('discount', 'Discount', array('numeric'), array('numeric' => '%s must be a number'));
        $this->form_validation->set_rules('cash_account_id', 'Cash account', array('required'), array('required' => '%s must be selected'));
        $feedback['success'] = false;
        $feedback['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $organisation_id = isset($_SESSION['organisation_id']) ? $_SESSION['organisation_id'] : 0;
            $upload_location = 'organisation_' . $organisation_id . '/expense/';
            $expense_id = $this->input->post('id');
            if (is_numeric($expense_id) && in_array('3', $this->data['accounts_privilege'])) {
                if ($this->expense_model->update($this->helpers->upload_file($upload_location))) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Expense transaction details successfully updated";
                    $feedback['expense'] = $this->expense_model->get($expense_id);
                } else {
                    $feedback['message'] = "There was a problem updating the expense transaction details";
                }
            } else {
                if (in_array('1', $this->data['accounts_privilege'])) {
                    $expense_id = $this->expense_model->set($this->helpers->upload_file($upload_location));
                    //insert the expense list items
                    if (is_numeric($expense_id)) {
                        $this->do_journal_transaction($expense_id);
                        $feedback['success'] = true;
                        $feedback['message'] = "Expense transaction details successfully saved";
                    } else {
                        $this->expense_model->abs_delete($expense_id);
                        $feedback['message'] = "There was a problem saving the expense transaction data";
                    }
                }
            }
        }
        echo json_encode($feedback);
    }

    private function do_journal_transaction($expense_id) {
        //first start by inserting the expense line items
        $this->load->model('expense_line_model');

        if ($this->expense_line_model->set($expense_id)) {
            //then go and post to the respective accounts
            $this->load->model('journal_transaction_model');
            $data = [
                'transaction_date' => $this->input->post('payment_date'),
                'description' => $this->input->post('description'),
                'ref_no' => $this->input->post('receipt_no'),
                'ref_id' => $expense_id,
                'status_id' => 1,
                'journal_type_id' => 2
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);
            //then we prepare the journal transaction lines for the expense lines
            $expense_line_items = $this->input->post('expense_line_item');
            $this->load->model('accounts_model');
            $transaction_lines = [];
            $total_amount = 0;
            foreach ($expense_line_items as $expense_line_item) {
                $normal_balance_side = $this->accounts_model->get_normal_side($expense_line_item['account_id']);
                $total_amount = ($total_amount + $expense_line_item['amount']);
                $transaction_line = [
                    $normal_balance_side => $expense_line_item['amount'],
                    'narrative' => $expense_line_item['narrative'],
                    'account_id' => $expense_line_item['account_id'],
                    'status_id' => 1
                ];
                $transaction_lines[] = $transaction_line;
            }
            $normal_balance_side = $this->accounts_model->get_normal_side($this->input->post('cash_account_id'), true);
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

    public function required_discount_acc($discount_account_id) {
        $validated = FALSE;
            if ($this->input->post('discount') !== NULL) {
                $validated = TRUE;
            } else {
                $validated = !($discount_account_id === NULL);
            }
            return $validated;
    }
    
    public function delete() {
        $response['success'] = FALSE;
        if (in_array('4', $this->data['accounts_privilege'])) {
            if ($this->expense_model->delete($this->input->post('id'))) {
                $response['success'] = TRUE;
                $response['message'] = "Expense successfully deleted.";
            } else {
                $response['message'] = "Expense details could not be deleted, contact support.";
            }
            echo json_encode($response);
        }
    }

    public function change_status() {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Expense details could not be " . $msg . "activated, please contact IT support.";
        $response['success'] = FALSE;
        if ($this->expense_model->deactivate($this->input->post('id'))) {
            $response['message'] = "Expense details successfully been " . $msg . "activated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }

}
