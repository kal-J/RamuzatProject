<?php

/**
 * Description of Income
 *
 * @author Allan
 */
class Income extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('income_model');
        $this->load->model('miscellaneous_model');
        $this->load->library(array("form_validation", "helpers"));
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 8, $this->session->userdata('staff_id'));
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['accounts_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        }
    }

    public function jsonList() {
        $data['data'] = $this->income_model->get();
        echo json_encode($data);
    }

    public function view($id) {
        //$this->load->model('transactionChannel_model');
        $neededcss = array("fieldset.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->data['income'] = $this->income_model->get($id);
        if (empty($this->data['income'])) {
            redirect("my404");
        }

        //$this->data['transaction_channels'] = $this->transactionChannel_model->get();
        $this->data['title'] = $this->data['sub_title'] = "Income #$id";
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('accounts/income/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function create() {
        $this->form_validation->set_rules('description', 'Narrative', array('required'), array('required' => '%s must be entered'));
        //$this->form_validation->set_rules('amount', 'Income Amount', array('required', 'numeric'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('cash_account_id', 'Cash account', array('required'), array('required' => '%s must be selected'));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $organisation_id = isset($_SESSION['organisation_id']) ? $_SESSION['organisation_id'] : 0;
            $upload_location = 'organisation_' . $organisation_id . '/income_docs/';
            $income_id = $this->input->post('id');
            if (is_numeric($income_id) && in_array('3', $this->data['accounts_privilege'])) {
                if ($this->income_model->update($this->helpers->upload_file($upload_location))) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Income transaction details successfully updated";
                    $feedback['income'] = $this->income_model->get($income_id);
                } else {
                    $feedback['message'] = "There was a problem updating the income transaction details";
                }
            } else {
                if (in_array('1', $this->data['accounts_privilege'])) {
                    $income_id = $this->income_model->set($this->helpers->upload_file($upload_location));
                    //insert the income list items
                    if (is_numeric($income_id)) {
                        $this->do_journal_transaction($income_id);
                        $feedback['success'] = true;
                        $feedback['message'] = "Income transaction details successfully saved";
                    } else {
                        $this->income_model->abs_delete($income_id);
                        $feedback['message'] = "There was a problem saving the income transaction data";
                    }
                }
            }
        }
        echo json_encode($feedback);
    }

    private function do_journal_transaction($income_id) {
        //first start by inserting the income line items
        $this->load->model('income_line_model');

        if ($this->income_line_model->set($income_id)) {
            //then go and post to the respective accounts
            $this->load->model('journal_transaction_model');
            $data = [
                'transaction_date' => $this->input->post('receipt_date'),
                'description' => $this->input->post('description'),
                'ref_no' => $this->input->post('receipt_no'),
                'ref_id' => $income_id,
                'status_id' => 1,
                'journal_type_id' => 14
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);
            //then we prepare the journal transaction lines for the income lines
            $income_line_items = $this->input->post('income_line_item');
            $this->load->model('accounts_model');
            $transaction_lines = [];
            $total_amount = 0;
            foreach ($income_line_items as $income_line_item) {
                $normal_balance_side = $this->accounts_model->get_normal_side($income_line_item['account_id']);
                $total_amount = ($total_amount + $income_line_item['amount']);
                $transaction_line = [
                    $normal_balance_side => $income_line_item['amount'],
                    'narrative' => $income_line_item['narrative'],
                    'account_id' => $income_line_item['account_id'],
                    'status_id' => 1
                ];
                $transaction_lines[] = $transaction_line;
            }
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
        $response['message'] = "Income source could not be deleted, contact support.";
        $response['success'] = FALSE;
        if ($this->income_model->delete($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] = "Income source successfully deleted.";
        }
        echo json_encode($response);
    }

    public function change_status() {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Income source details could not be " . $msg . " activated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->income_model->deactivate($this->input->post('id'))) {
            $response['message'] = "Income source details successfully " . $msg . "activated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }

}
