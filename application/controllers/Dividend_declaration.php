<?php

/**
 * Description of Dividend_declaration
 *
 * @author Allan J. Odeke   Modified by Ajuna Reagan
 */
class Dividend_declaration extends CI_Controller {

   public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->data['privilege_list'] = $this->helpers->user_privileges(8, $this->session->userdata('staff_id'));
        $this->data['fiscal_list'] = $this->helpers->user_privileges(20, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module(8, $_SESSION['organisation_id']);
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['accounts_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
            $this->data['fiscal_privilege'] = array_column($this->data['fiscal_list'], "privilege_code");
        }
        $this->load->model('dividend_declaration_model');
        $this->load->model('miscellaneous_model');
        $this->load->model('Shares_model');
        $this->load->model('staff_model');
        $this->load->library('helpers');
        $fiscal_year = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
            if(empty($fiscal_year)){
                redirect('dashboard');
            }else{
            $this->data['fiscal_year'] = array_merge($fiscal_year,['start_date2'=>date("d-m-Y", strtotime($fiscal_year['start_date'])),'end_date2'=>date("d-m-Y", strtotime($fiscal_year['end_date']))]);
            $this->data['lock_month_access'] = $this->helpers->org_access_module($module_id = 23, $_SESSION['organisation_id']);
                if(!empty($this->data['lock_month_access'])){
                    $this->data['active_month'] = $this->Fiscal_month_model->get_active_month();
                    if(empty($this->data['active_month'])){
                       redirect('dashboard');
                    }
                } 
            }
        }

    public function jsonlist() {
        $where = FALSE;
        if ($this->input->post('organisation_id') !== NULL) {
            $where = "organisation_id = " . $this->input->post('organisation_id');
        }
        $data['data'] = $this->dividend_declaration_model->get($where);
        echo json_encode($data);
    }
    public function jsonList2() {
        $record_date = $this->input->post('record_date');
        $data['data'] = $this->Shares_model->dividend_accounts(false,$record_date);
        echo json_encode($data);
    }

    public function create() {

        $this->form_validation->set_rules('declaration_date', 'Declaration Date', array('required', 'valid_date[d-m-Y]'), array('required' => '%s must be entered', 'valid_date' => '%s should be in the format dd-mm-yyy'));
        $this->form_validation->set_rules('record_date', 'Date of Record', array('required', 'valid_date[d-m-Y]'), array('required' => '%s must be entered', 'valid_date' => '%s should be in the format dd-mm-yyy'));
        $this->form_validation->set_rules('payment_date', 'Date of payment', array('required', 'valid_date[d-m-Y]'), array('required' => '%s must be entered', 'valid_date' => '%s should be in the format dd-mm-yyy'));
        $this->form_validation->set_rules('total_dividends', 'Total Dividends Declared', array('required', 'numeric'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('dividend_per_share', 'Dividend Per Share', array('required', 'numeric'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('retained_earnings_acc_id', 'Retained Earnings A/C', array('required'), array('required' => '%s must be selected'));
        $this->form_validation->set_rules('dividends_payable_acc_id', 'Dividends Payable A/C', array('required'), array('required' => '%s must be selected'));
        $this->form_validation->set_rules('notes', 'Notes', array('required'), array('required' => '%s must be entered'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $price_per_share=floor($this->input->post('dividend_per_share'));
            $total_computed_share=($price_per_share * $this->input->post('no_share'));
            $organisation_id = isset($_SESSION['organisation_id']) ? $_SESSION['organisation_id'] : 0;
            $upload_location = 'organisation_' . $organisation_id . '/accounts/dividend_declaration/';
            $dividend_declaration_id = $this->input->post('id');
            if (is_numeric($dividend_declaration_id)) {

               $this->dividend_declaration_model->get_delete_jtr(false,$dividend_declaration_id,21);



                if ($this->dividend_declaration_model->set(false,$total_computed_share)) {
                    $this->do_journal_transaction($dividend_declaration_id);
                    $feedback['success'] = true;
                    $feedback['message'] = "Dividend declaration details successfully updated";

                     $this->helpers->activity_logs($_SESSION['id'],8,"Editing dividend declaration",$feedback['message'],$organisation_id,$organisation_id);
            
                    $feedback['declaration'] = $this->dividend_declaration_model->get($this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the dividend declaration details";
                     $this->helpers->activity_logs($_SESSION['id'],8,"Editing dividend declaration",$feedback['message'],null,null);
                }
            } else {
                $dividend_declaration_id = $this->dividend_declaration_model->set(false,$total_computed_share);
                if (is_numeric($dividend_declaration_id)) {
                    $this->do_journal_transaction($dividend_declaration_id);
                    $feedback['success'] = true;
                    $feedback['message'] = "Dividend declaration details successfully saved";
                     $this->helpers->activity_logs($_SESSION['id'],8,"Creating dividend declaration",$feedback['message'],$dividend_declaration_id,$dividend_declaration_id);
                } else {
                    $feedback['message'] = "There was a problem saving the dividend declaration data";
                     $this->helpers->activity_logs($_SESSION['id'],8,"Creating dividend declaration",$feedback['message'],null,null);
                }
            }
        }
        echo json_encode($feedback);
    }

    private function do_journal_transaction($dividend_declaration_id) {
        //first start by inserting the bill line items
        //then go and post to the respective accounts
        $this->load->model('journal_transaction_model');
        $data = [
            'transaction_date' => $this->input->post('declaration_date'),
            'description' => $this->input->post('notes'),
            'ref_no' => "NIL",
            'ref_id' => $dividend_declaration_id,
            'status_id' => 1,
            'journal_type_id' => 21
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);

        if (is_numeric($journal_transaction_id)) {
            $this->load->model('accounts_model');
            $price_per_share=floor($this->input->post('dividend_per_share'));
            $total_computed_share=($price_per_share * $this->input->post('no_share'));
            $transaction_lines = [];
            $total_amount = $total_computed_share;
            //credit the  dividends payable account (liability account) in recognition of the liability
            $normal_balance_side = $this->accounts_model->get_normal_side($this->input->post('dividends_payable_acc_id'));
            $transaction_lines[] = [
                $normal_balance_side => $total_amount,
                'transaction_date' => $this->input->post('declaration_date'),
                'reference_no' => NULL,
                'reference_id' => $dividend_declaration_id,
                'narrative' => "Dividends declared on " . $this->input->post('declaration_date') . ". payable on " . $this->input->post('payment_date'),
                'account_id' => $this->input->post('dividends_payable_acc_id'),
                'status_id' => 1
            ];
            //debit the retained earnings account
            $normal_balance_side2 = $this->accounts_model->get_normal_side($this->input->post('retained_earnings_acc_id'), true);
            $transaction_lines[] = [
                $normal_balance_side2 => $total_amount,
                'transaction_date' => $this->input->post('declaration_date'),
                'reference_no' => NULL,
                'reference_id' => $dividend_declaration_id,
                'narrative' => "Recognizing dividends payable on " . $this->input->post('payment_date'),
                'account_id' => $this->input->post('retained_earnings_acc_id'),
                'status_id' => 1
            ];
            $this->load->model('journal_transaction_line_model');
            $this->journal_transaction_line_model->set($journal_transaction_id, $transaction_lines);
        }
    }

    private function do_payment_journal($transaction_data,$declaration_id,$amount,$dividends_payable_acc_id,$dividends_cash_acc_id) {
        $this->load->model('journal_transaction_model');
        $data = [
            'transaction_date' => $this->input->post('transaction_date'),
            'description' => $this->input->post('narrative'),
            'ref_no' => $transaction_data['transaction_no'],
            'ref_id' => $declaration_id,
            'status_id' => 1,
            'journal_type_id' => 25
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);

        if (is_numeric($journal_transaction_id)) {
            $this->load->model('accounts_model');
            $transaction_lines = [];
            //debit the  dividends payable account (liability account) to cancel out the liability
            $normal_balance_side = $this->accounts_model->get_normal_side($dividends_payable_acc_id, TRUE);
            $narrative = "Effecting payment for dividends declared on " . $this->input->post('declaration_date');
            $transaction_lines[] = [
                $normal_balance_side => $amount,
                'transaction_date' => $this->input->post('transaction_date'),
                'reference_no' => $transaction_data['transaction_no'],
                'reference_id' => $transaction_data['transaction_id'],
                'narrative' => $narrative,
                'account_id' => $dividends_payable_acc_id,
                'status_id' => 1
            ];
            //credit the cash account to reflect the money being paid out to the shareholders
            $normal_balance_side2 = $this->accounts_model->get_normal_side($dividends_cash_acc_id, TRUE);
            $transaction_lines[] = [
                $normal_balance_side2 => $amount,
                'transaction_date' => $this->input->post('transaction_date'),
                'reference_no' => $transaction_data['transaction_no'],
                'reference_id' => $transaction_data['transaction_id'],
                'narrative' => $narrative,
                'account_id' => $dividends_cash_acc_id,
                'status_id' => 1
            ];
            $this->load->model('journal_transaction_line_model');
            return $this->journal_transaction_line_model->set($journal_transaction_id, $transaction_lines);
        }
    }

    private function do_stock_payment_journal($transaction_data,$declaration_id,$amount,$dividends_payable_acc_id,$dividends_cash_acc_id) {
        $this->load->model('journal_transaction_model');
        $data = [
            'transaction_date' => $this->input->post('transaction_date'),
            'description' => $this->input->post('narrative'),
            'ref_no' => $transaction_data['transaction_no'],
            'ref_id' => $declaration_id,
            'status_id' => 1,
            'journal_type_id' => 25
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);

        if (is_numeric($journal_transaction_id)) {
            $this->load->model('accounts_model');
            $transaction_lines = [];
            //debit the  dividends payable account (liability account) to cancel out the liability
            $normal_balance_side = $this->accounts_model->get_normal_side($dividends_payable_acc_id, TRUE);
            $narrative = "Effecting payment for dividends declared on " . $this->input->post('declaration_date');
            $transaction_lines[] = [
                $normal_balance_side => $amount,
                'transaction_date' => $this->input->post('transaction_date'),
                'reference_no' => $transaction_data['transaction_no'],
                'reference_id' => $transaction_data['last_id'],
                'narrative' => $narrative,
                'account_id' => $dividends_payable_acc_id,
                'status_id' => 1
            ];
            //credit the cash account to reflect the money being paid out to the shareholders
            $normal_balance_side2 = $this->accounts_model->get_normal_side($dividends_cash_acc_id, TRUE);
            $transaction_lines[] = [
                $normal_balance_side2 => $amount,
                'transaction_date' => $this->input->post('transaction_date'),
                'reference_no' => $transaction_data['transaction_no'],
                'reference_id' => $transaction_data['last_id'],
                'narrative' => $narrative,
                'account_id' => $dividends_cash_acc_id,
                'status_id' => 1
            ];
            $this->load->model('journal_transaction_line_model');
            return $this->journal_transaction_line_model->set($journal_transaction_id, $transaction_lines);
        }
    }

    Public function payout(){
        $this->load->model('Transaction_model');
        $this->load->model('Share_transaction_model');
        $record_date = $this->input->post('record_date');
        $share_issuance_id = $this->input->post('share_issuance_id');
        $payment_type = $this->input->post('payment_type');
        $unpaid_members= $this->dividend_declaration_model->get_unpaid_accounts(false,$record_date,$share_issuance_id);
        if(!empty($unpaid_members)){
        $this->db->trans_begin();
        foreach ($unpaid_members as $key => $member) {
            $dividend_per_share = $this->input->post('dividend_per_share');
            $no_of_shares=$member['total_amount']/$member['price_per_share'];
            $dividends= $no_of_shares*$dividend_per_share;
            $amount =round($dividends,2);
            if($payment_type == 1){
                $transaction_data = $this->Transaction_model->set($member['s_acc_id'],12,$amount);
                $this->do_payment_journal($transaction_data,$this->input->post('dividend_declaration_id'),$amount,$this->input->post('dividends_payable_acc_id'),$this->input->post('dividends_cash_acc_id'));
            }else{
                $payment_mode = 2;
                $transaction_data = $this->Share_transaction_model->set($member['share_issuance_id'],12,$amount,$payment_mode);
                $this->do_stock_payment_journal($transaction_data,$this->input->post('dividend_declaration_id'),$amount,$this->input->post('dividends_payable_acc_id'),$this->input->post('dividends_cash_acc_id'));
            }

            $this->dividend_declaration_model->set_dividend_paid($member['id'],$amount,$no_of_shares,$member['total_amount'],$payment_type);
        }
        $this->dividend_declaration_model->pay_dividend();
        //check and update
        if($this->db->trans_status()){
            $this->db->trans_commit();
            $feedback['success'] = true;
            $feedback['message'] = "Dividend payment successfull";

        }else{
            $this->db->trans_rollback();
           $feedback['success'] = false;
           $feedback['message'] = "There was a problem during dividends payment";
        }
    } else {
       $feedback['success'] = false;
       $feedback['message'] = "Dividends Payment already completed, check the table below"; 
    }
    echo json_encode($feedback);
   }

    public function pay_dividend() {
        $this->form_validation->set_rules('id', 'Dividend declaration', array('required'), array('required' => '%s must be selected'));
        $this->form_validation->set_rules('payment_notes', 'Notes', array('required'), array('required' => '%s must be selected'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $dividend_declaration_id = $this->input->post("id");
            $dividend_declaration = $this->dividend_declaration_model->get($dividend_declaration_id);
            $payment_date = $this->input->post('payment_date')!=null?$this->helpers->yr_transformer($this->input->post('payment_date')):$dividend_declaration['payment_date'];
            $feedback['success'] = $this->dividend_declaration_model->pay_dividend();
            if ($feedback['success']) {
                $this->do_payment_journal($dividend_declaration);
                $feedback['message'] = "Dividend payment details successfully saved";
                
                  $this->helpers->activity_logs($_SESSION['id'],8,"Paying devidend",$feedback['message'],$dividend_declaration_id,$dividend_declaration_id);
            } else {
                $feedback['message'] = "There was a problem saving the dividend payment data";

                 $this->helpers->activity_logs($_SESSION['id'],8,"Paying devidend",$feedback['message'],$dividend_declaration_id,$dividend_declaration_id);
            }
        }
        echo json_encode($feedback);
    }

    public function view($dividend_declaration_id) {
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js");

        $neededcss = array("fieldset.css", "plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css");

        $this->data['dividend_declaration'] = $this->dividend_declaration_model->get($dividend_declaration_id);
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 5 AND id <> 3');
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['staff_list'] = $this->staff_model->get_registeredby("status_id=1");

        $this->data['title'] = "Dividend Declaration and Payment";
        $this->data['sub_title'] = "Declaration details";

        $this->template->title = $this->data['title'];
        $this->template->content->view('accounts/dividend/declaration/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function change_status() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
        if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            $this->data['message'] = $this->dividend_declaration_model->change_status();
            if ($this->data['message'] === true) {
                $this->data['success'] = TRUE;
            }
        }
        echo json_encode($this->data);
    }

    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
        if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            if (($response['success'] = $this->dividend_declaration_model->delete($this->input->post('id'))) === true) {
                $response['message'] = "Dividend Declaration Details successfully deleted";
            }
        }
        echo json_encode($response);
    }

}
