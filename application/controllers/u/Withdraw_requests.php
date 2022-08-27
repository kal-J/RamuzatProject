<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Withdraw_requests extends CI_Controller {

    protected $mm_channel_data;
    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("Savings_account_model");
        $this->load->model("DepositProduct_model");
        $this->load->model("Savings_product_fee_model");
        $this->load->model("Staff_model");
        $this->load->model("Organisation_format_model");
        $this->load->model("Member_model");
        $this->load->model("Transaction_model");
        $this->load->model("TransactionChannel_model");
        $this->load->model("Loan_guarantor_model");
        $this->load->model("payment_model");
        $this->load->model("Group_member_model");
        $this->load->model("payment_engine_model");
        $this->load->library(array("form_validation", "helpers"));
        $this->load->model('Withdraw_requests_model');
        $this->load->model('Transaction_model');

        $this->mm_channel_data=$this->payment_engine_model->get_requirement(1);
       $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
       $data['module']=$this->organisation_model->get_module_access(6,$this->session->userdata('organisation_id'));
       if(empty($data['module'])){
            redirect('my404');
       }
    }
 


    public function get_withdraw_requestsToJson($status_id = 1) {
        $data['data'] = $this->Withdraw_requests_model->get_requests($status_id);
        echo json_encode($data);
    }

    public function get_memeber_withdraw_requestsToJson($status_id = 1) {
        $data['data'] = [];
        $this->form_validation->set_rules('client_id', 'Id', 'required');
         if ($this->form_validation->run() === true) {
           $data['data'] = $this->Withdraw_requests_model->get_all_member_requests();

         }
        echo json_encode($data);
    }

    public function accept_withdraw() {
        $response['success'] = FALSE;
        $this->form_validation->set_rules('id', 'Id', 'required');
        $this->form_validation->set_rules('narrative', 'Narrative', 'required|trim');
        $this->form_validation->set_rules('account_no_id', 'Account number', 'required');
        if ($this->form_validation->run() === true) {
          // print_r($_POST); die;
            $data = $this->Withdraw_requests_model->accept_withdraw();
            if($data) {
              //data to do a journal transaction
              $account_no_id = $this->input->post('account_no_id');
              $amount = $this->input->post('amount');
              // $member_id = $this->input->post('member_id'); 
              $member_id = $this->savings_account_model->get2($_POST['account_no_id']);
              // $narrative = "Cash withdrawn via member withdraw request";
              // $_POST['narrative'] = $narrative;
              $transaction_data = $this->Transaction_model->set();
              $this->withdraw_journal_transaction($transaction_data,$member_id);
              if ($this->input->post('charges') !==NULL && $this->input->post('charges') !='') {
                $this->we_charges_journal_transaction($transaction_data,$member_id);
              }
              // notify the member
              $response['success'] = TRUE;
              $response['message'] = "Successfully accepted member withdraw request";

            }else {
              $response['message'] = "Failed to carryout this operation, please contact support for assistance";
            }
            echo json_encode($response);
        } else {
          $response['message'] = validation_errors();
          echo json_encode($response);
        }
    }

    public function decline_withdraw() {
        $response['success'] = FALSE;
        $this->form_validation->set_rules('id', 'Id', 'required');
        $this->form_validation->set_rules('decline_note', 'decline note', 'required|trim');
        if ($this->form_validation->run() === true) {
            $data = $this->Withdraw_requests_model->decline_withdraw();
            if($data) {
              // notify the member
              $response['success'] = TRUE;
              $response['message'] = "Successfully declined member withdraw request";

            }else {
              $response['message'] = "Failed to carryout this operation, please contact support for assistance";
            }
            echo json_encode($response);
        } else {
          $response['message'] = validation_errors();
          echo json_encode($response);
        }
    }
    

    public function withdraw_request() {
        $response['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        $this->form_validation->set_rules('account_no_id', 'Account Number id', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        $this->form_validation->set_rules('reason', 'Reason', 'required|trim|min_length[5]');
        $this->form_validation->set_rules('member_id', 'Member id', 'required');
        if ($this->form_validation->run() === true) {
          $this->Withdraw_requests_model->save_request();
        } else {
            $response['message'] = validation_errors();
            echo json_encode($response);
        }
    }
    private function withdraw_journal_transaction($transaction_data,$member_id) {
        $this->load->model('journal_transaction_model');
        $this->load->model('savings_account_model');
        $this->load->model('DepositProduct_model');
        if ($this->input->post('transaction_date') != NULL && $this->input->post('transaction_date') !='' ) {
           $date=$this->input->post('transaction_date');
        }else{
            $date = date('d-m-Y');
        }
        $savings_account = $this->savings_account_model->get($this->input->post('account_no_id'));

        $withdraw_amount = round($this->input->post('amount'), 2);
        //then we prepare the journal transaction lines
        if (!empty($savings_account)) {
            $this->load->model('accounts_model');
            $this->load->model('transactionChannel_model');
            $this->load->model('journal_transaction_line_model');

            $data = [
                'transaction_date' => $date,
                'description' => $this->input->post('narrative'),
                'ref_no' => $transaction_data['transaction_no'],
                'ref_id' => $transaction_data['transaction_id'],
                'status_id' => 1,
                'journal_type_id' => 8
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);

            $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
            $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);

            $debit_or_credit1 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);
            $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id'], true);

            //if deposit amount has been received
            if ($withdraw_amount != null && !empty($withdraw_amount) && $withdraw_amount != '0') {
                $data[0] = [
                    $debit_or_credit1 => $withdraw_amount,
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'member_id' => $member_id['member_id'],
                    'reference_key' => $member_id['account_no'],
                    'transaction_date' => $date,
                    'narrative' => "Withdraw transaction made on " . $date,
                    'account_id' => $savings_product_details['savings_liability_account_id'],
                    'status_id' => 1
                ];
                $data[1] = [
                    $debit_or_credit2 => $withdraw_amount,
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'member_id' => $member_id['member_id'],
                    'reference_key' => $member_id['account_no'],
                    'transaction_date' => $date,
                    'narrative' => "Withdraw transaction made on " . $date,
                    'account_id' => $transaction_channel['linked_account_id'],
                    'status_id' => 1
                ];
                $this->journal_transaction_line_model->set($journal_transaction_id, $data);
            }//end of the if
        }
    }

    private function we_charges_journal_transaction($transaction_data,$member_id) {
        $this->load->model('journal_transaction_model');
        $this->load->model('Savings_product_fee_model');
        $this->load->model('savings_account_model');
        $this->load->model('DepositProduct_model');

        if ($this->input->post('transaction_date') != NULL && $this->input->post('transaction_date') !='' ) {
           $transaction_date=$this->input->post('transaction_date');
        }else{
            $transaction_date = date('d-m-Y');
        }
        $charges = $this->input->post('charges');
        $savings_account = $this->savings_account_model->get($this->input->post('account_no_id'));
        if ($this->input->post('transaction_type_id') == 3) {
            $journal_type_id = 20;
        } else {
            $journal_type_id = 9;
        }
        //then we prepare the journal transaction lines
        if (!empty($charges)) {
            $this->load->model('accounts_model');
            $this->load->model('transactionChannel_model');
            $this->load->model('journal_transaction_line_model');

            $data = [
                'transaction_date' => $transaction_date,
                'description' => $this->input->post('narrative'),
                'ref_no' => $transaction_data['transaction_no'],
                'ref_id' => $transaction_data['transaction_id'],
                'status_id' => 1,
                'journal_type_id' => $journal_type_id
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);

            $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);

            $debit_or_credit2 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);

            //if charges have been received
            foreach ($charges as $key => $value) {
                $savings_product_fee_details = $this->Savings_product_fee_model->get_accounts($value['charge_id']);

                $debit_or_credit1 = $this->accounts_model->get_normal_side($savings_product_fee_details['savings_fees_income_account_id']);
                $data[0] = [
                    $debit_or_credit1 => $value['charge_amount'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'member_id' => $member_id['member_id'],
                    'reference_key' => $member_id['account_no'],
                    'transaction_date' => $transaction_date,
                    'narrative' => "Charge on withdraw transaction made on " . $transaction_date,
                    'account_id' => $savings_product_fee_details['savings_fees_income_account_id'],
                    'status_id' => 1
                ];
                $data[1] = [
                    $debit_or_credit2 => $value['charge_amount'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'member_id' => $member_id['member_id'],
                    'reference_key' => $member_id['account_no'],
                    'transaction_date' => $transaction_date,
                    'narrative' => "Charge on withdraw transaction made on " . $transaction_date,
                    'account_id' => $savings_product_details['savings_liability_account_id'],
                    'status_id' => 1
                ];
                $this->journal_transaction_line_model->set($journal_transaction_id, $data);
            }//end of foreach
        }
    }
}
