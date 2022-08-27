<?php

/**
 * Description of transaction
 *
 * @author diphas  and reagan
 */
class Transaction extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model('Transaction_model');
        $this->load->model('organisation_model');
        $this->load->model('Loan_guarantor_model');
        $this->load->model('Group_member_model');
    }

    public function jsonList($where = FALSE) {
        if ($this->input->post('transaction_id') !== NULL) {
            $where = "transaction_id = " . $this->input->post('transaction_id');
        }
        if (isset($_POST['acc_id']) === TRUE) {
            $where = "account_no_id = " . $this->input->post('acc_id');
        }
        $this->data['data'] = $this->Transaction_model->get($where);
        echo json_encode($this->data);
    }

    public function total_deposits() {
        $where = FALSE;
        if ($this->input->post('transaction_id') !== NULL) {
            $where = "transaction_id = " . $this->input->post('transaction_id');
        }
        $data['data'] = $this->Transaction_model->total_deposits($where);
        echo json_encode($data);
    }

    public function create() {
        $this->load->model("miscellaneous_model");
        $this->form_validation->set_rules('amount', 'Amount', array('required'), array('required' => '%s must be entered'));
        if ($this->input->post('transaction_type_id') == 3) {
            $this->form_validation->set_rules('savings_account_id', 'savings account', array('required'), array('required' => '%s must be selected'));
        } else {
            $this->form_validation->set_rules('transaction_channel_id', 'Amount', array('required'), array('required' => '%s must be entered'));
        }
        $this->form_validation->set_rules('transaction_type_id', 'Transaction type', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('narrative', 'Narrative', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('account_no_id', 'Account Number', array('required'), array('required' => '%s must be provided'));
        $this->form_validation->set_rules('transaction_date', 'transaction date', array('required'), array('required' => '%s must be provided'));
        //$this->form_validation->set_rules('opening_balance', 'Opening_balance', array('required'), array('required' => '%s must be provided'));
        $this->form_validation->set_rules('state_id', 'Account state', array('required'), array('required' => '%s must be set'));
        $feedback['success'] = false;
        if ($this->input->post('transaction_type_id') == 1) {
            $msg_type = "Withdraw";
        } else if ($this->input->post('transaction_type_id') == 2) {
            $msg_type = "Deposit";
        } else if ($this->input->post('transaction_type_id') == 3) {
            $msg_type = "Transfer";
        } else {
            $msg_type = "";
        }

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $this->db->trans_begin();

            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Transaction_model->update()) {
                    $feedback['message'] = $msg_type . " update successful";
                    //$feedback['transaction'] = $this->Transaction_model->get($_POST['id']);
                } else {
                    $feedback['message'] = $msg_type . " failed";
                }
            } else {
                $account_no_id = FALSE;
                $transaction_type_id = FALSE;
                if ($this->input->post('transaction_type_id') == 3) {
                    $account_no_id = $this->input->post('account_no_id');
                }
                $transaction_data = $this->Transaction_model->set($account_no_id, $transaction_type_id);
                
                if (is_array($transaction_data)) {
                    $feedback['success'] = true;
                    $feedback['message'] = $msg_type . "  successful";
                    if ($this->input->post('transaction_type_id') == 1) {
                        $this->withdraw_journal_transaction($transaction_data);
                        if ($this->input->post('charges') !== NULL && $this->input->post('charges') != '') {
                            $this->we_charges_journal_transaction($transaction_data);
                        }
                    }
                    if ($this->input->post('transaction_type_id') == 2) {
                        $this->deposit_journal_transaction($transaction_data);
                        if ($this->input->post('charges') !== NULL && $this->input->post('charges') != '') {
                            $this->de_charges_journal_transaction($transaction_data);
                        }
                    }
                    if ($this->input->post('transaction_type_id') == 3) {
                        $this->Transaction_model->set($this->input->post('savings_account_id'), 2);
                        if ($this->input->post('charges') !== NULL && $this->input->post('charges') != '') {
                            $this->we_charges_journal_transaction($transaction_data);
                        }
                    }

                    $acc_id = $this->input->post('account_no_id');
                    if (!empty($result = $this->miscellaneous_model->check_org_module(22))) {
                        $acc_id2 = $this->input->post('savings_account_id');
                        #SMS notification set up
                        $content = (!empty($acc_id2)) ? ' from your account ' . $this->input->post('account_no') . ' to account ' . $this->input->post('savings_account_no') : ' on account ' . $this->input->post('account_no');
                        if (!empty($acc_id)) {
                            $message = $msg_type . "/= of amount " . $this->input->post('amount') . " has been made" . $content . " today " . date('d-m-Y H:i:s') . ". Thanks";
                            $text_response = $this->helpers->notification($acc_id, $message, false);
                            $feedback['message'] = $feedback['message'] . $text_response;
                        }
                        if (!empty($acc_id2)) {
                            $message = $msg_type . "/= of amount " . $this->input->post('amount') . " has been made to your account " . $this->input->post('savings_account_no') . " today " . date('d-m-Y H:i:s') . " from account " . $this->input->post('account_no') . ". Thanks";
                            $text_response = $this->helpers->notification($acc_id2, $message, false);
                        }
                    }//End of the check for the sms module

                    if ($this->db->trans_status()) {
                        $this->db->trans_commit();
                        $feedback['success'] = TRUE;

                        if (isset($_POST['account_details'])) {
                            if ($this->input->post('client_type') == 2) {
                                $feedback['group_members'] = $this->Group_member_model->get_group_member_savings('g.id=' . $this->input->post('group_member_id'), $acc_id);
                                $feedback['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings2("(j.state_id = 5 OR j.state_id = 7)", $acc_id);
                            } else {
                                $feedback['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings("(j.state_id = 5 OR j.state_id = 7)", $acc_id);
                            }
                        } else {
                            $feedback['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings("(j.state_id = 5 OR j.state_id = 7)");
                        }
                        if (isset($_POST['print'])) {
                            $feedback['insert_id'] = $transaction_data['transaction_id'];
                            $feedback['client_type'] = $this->input->post('client_type');
                        }

                    }else {
                        $this->db->trans_rollback();
                        $feedback['success'] = FALSE;
                        $feedback['message'] = "An Error happened while recording the transaction. Please Try again later";
                    }
                } else {
                    $feedback['message'] = "There was a problem transaction. Please try again";
                }
            }
        }
        echo json_encode($feedback);
    }

    function change_status() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
        //  if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        if (($response['success'] = $this->Transaction_model->delete($this->input->post('id'))) === true) {
            $response['message'] = "Transaction successfully deleted";
        }
        //  }
        echo json_encode($response);
    }

    private function deposit_journal_transaction($transaction_data) {
        $this->load->model('journal_transaction_model');
        $this->load->model('savings_account_model');
        $this->load->model('DepositProduct_model');
        if ($this->input->post('transaction_date') != NULL && $this->input->post('transaction_date') !='' ) {
           $date=$this->input->post('transaction_date');
        }else{
            $date = date('d-m-Y');
        }
        $savings_account = $this->savings_account_model->get_savings_acc_details($this->input->post('account_no_id'));
        $total_charges = round($this->input->post('total_charges'));
        $deposited_amount = round($this->input->post('amount'), 2);
        $deposit_amount = $deposited_amount - $total_charges;

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
                'journal_type_id' => 7
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);

            $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
            $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);

            $debit_or_credit1 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id']);
            $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);

            //if deposit amount has been received
            if ($deposit_amount != null && !empty($deposit_amount) && $deposit_amount != '0') {
                $data[0] = [
                    $debit_or_credit1 => $deposit_amount,
                    'narrative' => "Deposit transaction made on " . $date,
                    'account_id' => $savings_product_details['savings_liability_account_id'],
                    'status_id' => 1
                ];
                $data[1] = [
                    $debit_or_credit2 => $deposit_amount,
                    'narrative' => "Deposit transaction made on " . $date,
                    'account_id' => $transaction_channel['linked_account_id'],
                    'status_id' => 1
                ];
                $this->journal_transaction_line_model->set($journal_transaction_id, $data);
            }//end of the if
        }
    }

    public function print_receipt($id = false, $client_type = false) {
        if (empty($this->session->userdata('id'))) {
            redirect("welcome", "refresh");
        }
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['trans'] = $this->Transaction_model->get_transaction($id);
        $this->data['account'] = $this->Loan_guarantor_model->get_guarantor_savings('client_type=' . $client_type);

        $this->load->view('savings_account/deposits/savings_printout', $this->data);
    }

    private function withdraw_journal_transaction($transaction_data) {
        $this->load->model('journal_transaction_model');
        $this->load->model('savings_account_model');
        $this->load->model('DepositProduct_model');
        if ($this->input->post('transaction_date') != NULL && $this->input->post('transaction_date') !='' ) {
           $date=$this->input->post('transaction_date');
        }else{
            $date = date('d-m-Y');
        }
        $savings_account = $this->savings_account_model->get_savings_acc_details($this->input->post('account_no_id'));

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
                    'narrative' => "Withdraw transaction made on " . $date,
                    'account_id' => $savings_product_details['savings_liability_account_id'],
                    'status_id' => 1
                ];
                $data[1] = [
                    $debit_or_credit2 => $withdraw_amount,
                    'narrative' => "Withdraw transaction made on " . $date,
                    'account_id' => $transaction_channel['linked_account_id'],
                    'status_id' => 1
                ];
                $this->journal_transaction_line_model->set($journal_transaction_id, $data);
            }//end of the if
        }
    }

    private function de_charges_journal_transaction($transaction_data) {
        $this->load->model('journal_transaction_model');
        $this->load->model('Savings_product_fee_model');
        
        if ($this->input->post('transaction_date') != NULL && $this->input->post('transaction_date') !='' ) {
           $transaction_date=$this->input->post('transaction_date');
        }else{
            $transaction_date = date('d-m-Y');
        }
        $charges = $this->input->post('charges');
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
                'journal_type_id' => 10
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);

            $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
            $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);

            //if charges have been received
            foreach ($charges as $key => $value) {
                $savings_product_fee_details = $this->Savings_product_fee_model->get_accounts($value['charge_id']);

                $debit_or_credit1 = $this->accounts_model->get_normal_side($savings_product_fee_details['savings_fees_income_account_id']);
                $data[0] = [
                    $debit_or_credit1 => $value['charge_amount'],
                    'narrative' => "Charge on deposit made on " . $transaction_date,
                    'account_id' => $savings_product_fee_details['savings_fees_income_account_id'],
                    'status_id' => 1
                ];
                $data[1] = [
                    $debit_or_credit2 => $value['charge_amount'],
                    'narrative' => "Charge on deposit made on " . $transaction_date,
                    'account_id' => $transaction_channel['linked_account_id'],
                    'status_id' => 1
                ];
                $this->journal_transaction_line_model->set($journal_transaction_id, $data);
            }//end of foreach
        }
    }

    private function we_charges_journal_transaction($transaction_data) {
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
        $savings_account = $this->savings_account_model->get_savings_acc_details($this->input->post('account_no_id'));
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
                    'narrative' => "Charge on withdraw transaction made on " . $transaction_date,
                    'account_id' => $savings_product_fee_details['savings_fees_income_account_id'],
                    'status_id' => 1
                ];
                $data[1] = [
                    $debit_or_credit2 => $value['charge_amount'],
                    'narrative' => "Charge on withdraw transaction made on " . $transaction_date,
                    'account_id' => $savings_product_details['savings_liability_account_id'],
                    'status_id' => 1
                ];
                $this->journal_transaction_line_model->set($journal_transaction_id, $data);
            }//end of foreach
        }
    }

}
