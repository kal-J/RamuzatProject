<?php

/**
 * Description of transaction
 *
 * @author diphas and modified by reagan
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class Share_transaction extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model('Share_transaction_model');
        $this->load->model('organisation_model');
        $this->load->model('Share_state_model');
        $this->load->model('Share_issuance_fees_model');
        $this->load->model('savings_account_model');
        $this->load->model('journal_transaction_model');
        $this->load->model('Shares_model');
        $this->load->model('Share_issuance_model');
        $this->load->model('DepositProduct_model');
        $this->load->model('accounts_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('journal_transaction_line_model');
        $this->load->model("shares_model");
        $this->load->model('Fiscal_month_model');
        $this->load->model("miscellaneous_model");
        $this->load->model("Loan_guarantor_model");
        $this->load->model("Transaction_model");
    }

    public function jsonList($where = FALSE)
    {

        if (isset($_POST['acc_id'])) {
            $where = "share_account_id = " . $this->input->post('acc_id') . " AND  tn.status_id = " . $this->input->post('status_id') . " ";
        } else {
            $where = "tn.status_id = " . $this->input->post('status_id') . " ";
        }

        $this->data['data'] = $this->Share_transaction_model->get($where);
        //print_r($this->data); die;
        echo json_encode($this->data);
    }

    public function total_deposits()
    {
        $where = FALSE;
        if ($this->input->post('transaction_id') !== NULL) {
            $where = "transaction_id = " . $this->input->post('transaction_id');
        }
        $data['data'] = $this->Share_transaction_model->total_deposits($where);
        echo json_encode($data);
    }
    //get share report data
    public function full_share_report_data($transaction_status = 1)
    {

        $data['data'] = $this->Share_transaction_model->get2();
        echo json_encode($data);
    }

    public function create()
    {
        $this->form_validation->set_rules('amount', 'Amount', array('required'), array('required' => '%s must be entered'));
        if ($this->input->post('payment_id') == 5) {
            $this->form_validation->set_rules('account_no_id', 'Savings account ', array('required'), array('required' => '%s must be selected'));
        } else {
            $this->form_validation->set_rules('transaction_channel_id', 'Transaction Channel ', array('required'), array('required' => '%s must be selected'));
        }
        $this->form_validation->set_rules('narrative', 'Narrative', array('required'), array('required' => '%s must be entered'));

        $this->form_validation->set_rules('share_issuance_id', 'Application id', array('required'), array('required' => '%s must be provided'));

        $this->form_validation->set_rules('share_acc_id', 'Share account id', array('required'), array('required' => '%s must be provided'));

        $this->form_validation->set_rules('transaction_date', 'transaction date', array('required'), array('required' => '%s must be provided'));
        $feedback['success'] = false;
        $msg_type = "Payment";
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {

            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Share_transaction_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = $msg_type . "  successful";
                    //buying shares 

                    //$feedback['transaction'] = $this->Share_transaction_model->get($_POST['id']);
                } else {
                    $feedback['message'] = $msg_type . " failed";
                }
            } else {
                $charges = $this->input->post('charges');
                $total_charges = 0;
                if ($charges) {
                    foreach ($charges as $key => $value) {
                        $total_charges += $value['charge_amount'];
                    }
                }
                if ($this->input->post('payment_id') == 5) {


                    $savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $this->input->post('account_no_id'));
                    $current_balance = $savings_data['cash_bal'];
                    if ($current_balance >= ($this->input->post('amount') + $total_charges)) {
                        $transaction_data1 = $this->Share_transaction_model->set();
                        $transaction_data2 = $this->Transaction_model->set($this->input->post('account_no_id'), 4, $this->input->post('amount'), "SH" . $transaction_data1['last_id'], "[Share Payment]");
                        $trans_no_or_id = $transaction_data2['transaction_no'];
                        $transaction_data['transaction_no'] = $transaction_data1['transaction_no'];
                        $transaction_data['share_account_id'] = $transaction_data2['transaction_no'];
                        if (is_array($transaction_data)) {
                            $this->share_call_journal_transaction($transaction_data);
                            if ($this->input->post('charges') !== NULL && $this->input->post('charges') != '') {
                                $tran['transaction_no'] = $transaction_data1['transaction_no'];
                                $tran['transaction_id'] = $transaction_data2['transaction_no'];
                                $transaction_data2 = $this->Transaction_model->set($this->input->post('account_no_id'), 4, $total_charges, "SH" . $transaction_data1['last_id'], "[Share Charges]");

                                $this->share_charges_journal_transaction($tran, 9);

                                $this->helpers->activity_logs($_SESSION['id'], 12, "Buying Shares", $feedback['message'] . " # " .  $tran['transaction_no'], NULL,  $tran['transaction_id']);
                            }
                            $feedback['success'] = true;
                            $feedback['message'] = $msg_type . "  successful";

                            $this->helpers->activity_logs($_SESSION['id'], 12, "Buying Shares", $feedback['message'] . " -# " . $this->input->post('share_account_no_id'), NULL, $this->input->post('share_account_no_id'));
                        } else {
                            $feedback['message'] = "There was a problem with payment, try again";
                            $this->helpers->activity_logs($_SESSION['id'], 12, "Editing shares", $feedback['message'] . " # " . $transaction_data1['transaction_no'], NULL, $transaction_data1['transaction_no']);
                        }
                    } else {
                        $feedback['message'] = "Insufficient balance to complete the Transaction";
                        $this->helpers->activity_logs($_SESSION['id'], 12, "Editing shares", $feedback['message'], NULL, NULL);
                    }
                } else {
                    $transaction_data1 = $this->Share_transaction_model->set();
                    $trans_no_or_id = $transaction_data1['share_account_id'];
                    $transaction_data['transaction_no'] = $transaction_data1['transaction_no'];
                    $transaction_data['share_account_id'] = $trans_no_or_id;
                    if (is_array($transaction_data)) {
                        $this->share_call_journal_transaction($transaction_data);
                        if ($this->input->post('charges') !== NULL && $this->input->post('charges') != '') {
                            $transa = $this->Share_transaction_model->set(false, 11, $total_charges, false, $transaction_data1['last_id']);
                            $tran['transaction_no'] = $transa['transaction_no'];
                            $tran['transaction_id'] = $transa['share_account_id'];
                            $this->share_charges_journal_transaction($tran, 9);
                        }

                        $feedback['success'] = true;
                        $feedback['message'] = $msg_type . "  successful";
                        $this->helpers->activity_logs($_SESSION['id'], 12, "Buying", $feedback['message'], $transaction_data['transaction_no'], " # " . $transaction_data['share_account_id']);
                    } else {
                        $feedback['message'] = "There was a problem with payment, try again";
                        $this->helpers->activity_logs($_SESSION['id'], 12, "Buying", $feedback['message'] . " -# " . $this->input->post('transaction_no'), NULL, $this->input->post('transaction_no'));
                    }
                }
            }
        }
        echo json_encode($feedback);
    }

    public function refund()
    {
        $this->form_validation->set_rules('amount', 'Amount', array('required'), array('required' => '%s must be entered'));
        if ($this->input->post('payment_id') == 5) {
            $this->form_validation->set_rules('account_no_id', 'Savings account ', array('required'), array('required' => '%s must be selected'));
        } else {
            $this->form_validation->set_rules('transaction_channel_id', 'Transaction Channel ', array('required'), array('required' => '%s must be selected'));
        }
        $this->form_validation->set_rules('narrative', 'Narrative', array('required'), array('required' => '%s must be entered'));

        $this->form_validation->set_rules('share_issuance_id', 'Application id', array('required'), array('required' => '%s must be provided'));

        $this->form_validation->set_rules('share_acc_id', 'Share account id', array('required'), array('required' => '%s must be provided'));

        $this->form_validation->set_rules('transaction_date', 'transaction date', array('required'), array('required' => '%s must be provided'));
        $feedback['success'] = false;
        $msg_type = "Payment";
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if ($this->input->post('payment_id') == 5) {
                $transaction_data1 = $this->Share_transaction_model->set(false, 13, false);
                $transaction_data2 = $this->Transaction_model->set($this->input->post('account_no_id'), 2, $this->input->post('amount'), "SH" . $transaction_data1['last_id'], "[Share Refund] " . $this->input->post('narrative'));
                $trans_no_or_id = $transaction_data2['transaction_no'];
                $transaction_data['transaction_no'] = $transaction_data1['transaction_no'];
                $transaction_data['share_account_id'] = $trans_no_or_id;

                if (is_array($transaction_data)) {
                    $this->share_refund_journal_transaction($transaction_data);
                    if ($this->input->post('charges') !== NULL && $this->input->post('charges') != '') {
                        $tran['transaction_no'] = $transaction_data1['transaction_no'];
                        $tran['transaction_id'] = $transaction_data2['transaction_no'];
                        $this->share_charges_journal_transaction($tran, 13);
                    }
                    $feedback['success'] = true;
                    $feedback['message'] = $msg_type . "  successful";
                    $this->helpers->activity_logs($_SESSION['id'], 12, "Refunding shares", $feedback['message'] . " # " . $transaction_data1['transaction_no'], NULL, $transaction_data1['transaction_no']);
                } else {
                    $feedback['message'] = "There was a problem with payment, try again";

                    $this->helpers->activity_logs($_SESSION['id'], 12, "Refunding shares", $feedback['message'] . " # " . $transaction_data1['transaction_no'], NULL, $transaction_data1['transaction_no']);
                }
            } else {
                $transaction_data1 = $this->Share_transaction_model->set(false, 13, false);
                $trans_no_or_id = $transaction_data1['share_account_id'];
                $transaction_data['transaction_no'] = $transaction_data1['transaction_no'];
                $transaction_data['share_account_id'] = $trans_no_or_id;
                if (is_array($transaction_data)) {
                    $this->share_refund_journal_transaction($transaction_data);
                    if ($this->input->post('charges') !== NULL && $this->input->post('charges') != '') {
                        $tran['transaction_no'] = $transaction_data1['transaction_no'];
                        $tran['transaction_id'] = $trans_no_or_id;
                        $this->share_charges_journal_transaction($tran, 13);
                    }
                    $feedback['success'] = true;
                    $feedback['message'] = $msg_type . "  successful";
                    $this->helpers->activity_logs($_SESSION['id'], 12, "Refunding shares", $feedback['message'] . " -# " . $transaction_data1['transaction_no'], NULL, $transaction_data1['transaction_no']);
                } else {
                    $feedback['message'] = "There was a problem with payment, try again";
                    $this->helpers->activity_logs($_SESSION['id'], 12, "Refunding shares", $feedback['message'] . " -# " . $transaction_data1['transaction_no'], NULL, $transaction_data1['transaction_no']);
                }
            }
        }
        echo json_encode($feedback);
    }

    public function create3()
    {
        $this->load->model("shares_model");
        $this->load->model("miscellaneous_model");
        $this->load->model("Loan_guarantor_model");
        $this->load->model("Transaction_model");
        $this->form_validation->set_rules('amount', 'Amount', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('narrative', 'Narrative', array('required'), array('required' => '%s must be entered'));

        $this->form_validation->set_rules('share_issuance_id', 'Application id', array('required'), array('required' => '%s must be provided'));

        $this->form_validation->set_rules('shares_account_id', 'Share account ', array('required'), array('required' => '%s must be selected'));
        $this->form_validation->set_rules('transaction_date', 'transaction date', array('required'), array('required' => '%s must be provided'));
        $feedback['success'] = false;
        $msg_type = "Transfer";
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $charges = $this->input->post('charges');
            $total_charges = 0;
            if ($charges) {
                foreach ($charges as $key => $value) {
                    $total_charges += $value['charge_amount'];
                }
            }
            if ($this->input->post('payment_id') == 5) {


                $savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $this->input->post('account_no_id'));
                $current_balance = $savings_data['cash_bal'];

                if ($current_balance >= ($this->input->post('amount') + $total_charges)) {
                    $transaction_data1 = $this->Share_transaction_model->set(false, 10, false);
                    $transaction_data2 = $this->Share_transaction_model->set($this->input->post('shares_account_id'), false, false, false, $transaction_data1['last_id']);
                    $transaction_data3 = $this->Transaction_model->set($this->input->post('account_no_id'), 4, $this->input->post('amount'), "SH" . $transaction_data1['last_id'], "[Share Payment]");
                    $trans_no_or_id = $transaction_data2['transaction_no'];
                    $transaction_data['transaction_no'] = $transaction_data1['transaction_no'];
                    $transaction_data['share_account_id'] = $transaction_data2['transaction_no'];

                    if (is_array($transaction_data)) {

                        $this->share_call_journal_transaction($transaction_data);
                        if ($this->input->post('charges') !== NULL && $this->input->post('charges') != '') {
                            $tran['transaction_no'] = $transaction_data1['transaction_no'];
                            $tran['transaction_id'] = $transaction_data2['transaction_no'];
                            $transaction_data2 = $this->Transaction_model->set($this->input->post('account_no_id'), 4, $total_charges, "SH" . $transaction_data1['last_id'], "[Share Charges]");

                            $this->share_charges_journal_transaction($tran, 9);
                        }
                        $feedback['success'] = true;
                        $feedback['message'] = $msg_type . "  successful";
                        $this->helpers->activity_logs($_SESSION['id'], 12, "Selling or transfering shares", $feedback['message'] . " -# " . $transaction_data1['transaction_no'], NULL, $transaction_data1['transaction_no']);
                    } else {
                        $feedback['message'] = "There was a problem with payment, try again";
                        $this->helpers->activity_logs($_SESSION['id'], 12, "Selling or transfering shares", $feedback['message'] . " -# " . $transaction_data1['transaction_no'], NULL, $transaction_data1['transaction_no']);
                    }
                } else {
                    $feedback['message'] = "Insufficient balance to complete the Transaction";
                    $this->helpers->activity_logs($_SESSION['id'], 12, "Selling or transfering shares", $feedback['message'], NULL, NULL);
                }
            } else {

                $transaction_data = $this->Share_transaction_model->set(false, 10, false);
                $transaction_data1 = $this->Share_transaction_model->set($this->input->post('shares_account_id'), false, false, false, $transaction_data['last_id']);
                $trans_no_or_id = $transaction_data1['transaction_no'];
                $transaction_data['transaction_no'] = $transaction_data['transaction_no'];
                $transaction_data['share_account_id'] = $trans_no_or_id;

                if (is_array($transaction_data)) {
                    //$this->share_call_journal_transaction($transaction_data);
                    if ($this->input->post('charges') !== NULL && $this->input->post('charges') != '') {
                        // $transaction_data = $this->Share_transaction_model->set($this->input->post('shares_account_id'), 11,$total_charges,false,$transaction_data1['last_id']);
                        $tran['transaction_no'] = $transaction_data['transaction_no'];
                        $tran['transaction_id'] = $transaction_data['share_account_id'];
                        $this->share_charges_journal_transaction($tran, 10);
                    }
                    $feedback['success'] = true;
                    $feedback['message'] = "Share Transfer successful";
                    $this->helpers->activity_logs($_SESSION['id'], 12, "Selling or transfering shares", $feedback['message'] . " -# " . $transaction_data1['transaction_no'], NULL, $transaction_data1['transaction_no']);
                } else {
                    $feedback['message'] = "There was a problem with transfer, try again";
                    $this->helpers->activity_logs($_SESSION['id'], 12, "Selling or transfering shares", $feedback['message'] . " -# " . $transaction_data1['transaction_no'], NULL, $transaction_data1['transaction_no']);
                }
                $tran['transaction_no'] = $transaction_data['transaction_no'];
                $tran['transaction_id'] = $transaction_data['share_account_id'];
                $this->share_sacco_journal_transaction($tran);
            }
        }
        echo json_encode($feedback);
    }


    //modified method for transferring funds off sacco shares account
    public function share_sacco_journal_transaction($transaction_data)
    {
        if ($this->input->post('transaction_date') != NULL && $this->input->post('transaction_date') != '') {
            $transaction_date = $this->input->post('transaction_date');
        } else {
            $transaction_date = date('d-m-Y');
        }

        $deposit_amount = round($this->input->post('amount'), 2);

        $narrative = "SHARE TRANSFER .. " . $transaction_date . " [ " . $this->input->post('narrative') . " ]";

        $data = [
            'transaction_date' => $this->input->post('transaction_date'),
            'description' => $narrative,
            'ref_no' => $transaction_data['transaction_no'],
            'ref_id' => $transaction_data['transaction_id'],
            'status_id' => 1,
            'journal_type_id' => 24
        ];

        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);

        $share_issuance_acct1 = $this->Share_issuance_model->get($this->input->post('share_issuance_id'));
        $debit_or_credit1 = $this->accounts_model->get_normal_side($share_issuance_acct1['share_capital_account_id']);

        $share_issuance_acct2 = $this->Share_issuance_model->get($this->input->post('transfer_share_issuance_id'));
        $debit_or_credit2 = $this->accounts_model->get_normal_side($share_issuance_acct2['share_capital_account_id'], true);

        //if deposit amount has been received
        if ($deposit_amount != null && $deposit_amount != '0') {
            $data[0] = [
                $debit_or_credit1 => $deposit_amount,
                'transaction_date' => $this->input->post('transaction_date'),
                'reference_no' => $transaction_data['transaction_no'],
                'reference_id' => $transaction_data['transaction_id'],
                'narrative' => $narrative,
                'account_id' => $share_issuance_acct1['share_capital_account_id'],
                'status_id' => 1
            ];
            $data[1] = [
                $debit_or_credit2 => $deposit_amount,
                'transaction_date' => $this->input->post('transaction_date'),
                'reference_no' => $transaction_data['transaction_no'],
                'reference_id' => $transaction_data['transaction_id'],
                'narrative' => $narrative,
                'account_id' => $share_issuance_acct2['share_capital_account_id'],
                'status_id' => 1
            ];
            $this->journal_transaction_line_model->set($journal_transaction_id, $data);
        } //end of the i
    }

    // public function notification($acc_id,$message){
    //   $accounts_data=$this->savings_account_model->get($acc_id);
    //   $contacts=$this->member_model->get_member_contact($accounts_data['member_id']);
    //   if (!empty($contacts) && array_key_exists('mobile_number', $contacts)) {
    //       # send SMS
    //       if ($this->helpers->send_sms($contacts['mobile_number'],$message)) {
    //         return " and client notified";
    //       }else{
    //         return " but client couldn't be notified";
    //       }
    //   }else{
    //     return " but client couldn't be notified, no contact";
    //   }
    // }


    private function share_charges_journal_transaction($transaction_data, $transaction_type_id)
    {

        if ($this->input->post('transaction_date') != NULL && $this->input->post('transaction_date') != '') {
            $transaction_date = $this->input->post('transaction_date');
        } else {
            $transaction_date = date('d-m-Y');
        }
        $charges = $this->input->post('charges');

        if ($transaction_type_id == 10) {
            $journal_type_id = 33;
            $narrative = "SHARE TRANSFER CHARGE [ " . $transaction_date . " ] " . $this->input->post('narrative');
        } else {
            $journal_type_id = 32;
            $narrative = "SHARE TRANSACTION CHARGE [ " . $transaction_date . " ] " . $this->input->post('narrative');
        }

        //then we prepare the journal transaction lines
        if (!empty($charges)) {
            $this->load->model('accounts_model');
            $this->load->model('transactionChannel_model');
            $this->load->model('Share_issuance_model');
            $this->load->model('journal_transaction_line_model');

            $data = [
                'transaction_date' => $transaction_date,
                'description' => $narrative,
                'ref_no' => $transaction_data['transaction_no'],
                'ref_id' => $transaction_data['transaction_id'],
                'status_id' => 1,
                'journal_type_id' => $journal_type_id
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);
            if ($transaction_type_id == 10) {
                $share_issuance_cat_details = $this->Share_issuance_model->get($this->input->post('share_issuance_id'));
                $debit_or_credit2 = $this->accounts_model->get_normal_side($share_issuance_cat_details['share_capital_account_id'], true);
                $linked_account_id = $share_issuance_cat_details['share_capital_account_id'];
            } else {
                if ($this->input->post('payment_id') == 5) {
                    $savings_account = $this->savings_account_model->get_savings_acc_details($this->input->post('account_no_id'));
                    $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);
                    $debit_or_credit2 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);
                    $linked_account_id = $savings_product_details['savings_liability_account_id'];
                } else {
                    $share_issuance_cat_details = $this->Share_issuance_model->get($this->input->post('share_issuance_id'));
                    $debit_or_credit2 = $this->accounts_model->get_normal_side($share_issuance_cat_details['share_capital_account_id'], true);
                    $linked_account_id = $share_issuance_cat_details['share_capital_account_id'];
                }

                //if charges have been received
                foreach ($charges as $key => $value) {
                    $share_inssuance_fees = $this->Share_issuance_fees_model->get_fees($value['id']);

                    $debit_or_credit1 = $this->accounts_model->get_normal_side($share_inssuance_fees['share_fees_income_account_id']);
                    $data[0] = [
                        $debit_or_credit1 => $value['charge_amount'],
                        'reference_no' => $transaction_data['transaction_no'],
                        'reference_id' => $transaction_data['transaction_id'],
                        'member_id' => $this->input->post('member_id'),
                        'reference_key' => $this->input->post('share_account_no'),
                        'transaction_date' => $transaction_date,
                        'narrative' => $narrative,
                        'account_id' => $share_inssuance_fees['share_fees_income_account_id'],
                        'status_id' => 1
                    ];
                    $data[1] = [
                        $debit_or_credit2 => $value['charge_amount'],
                        'reference_no' => $transaction_data['transaction_no'],
                        'reference_id' => $transaction_data['transaction_id'],
                        'member_id' => $this->input->post('member_id'),
                        'reference_key' => $this->input->post('share_account_no'),
                        'transaction_date' => $transaction_date,
                        'narrative' => $narrative,
                        'account_id' => $linked_account_id,
                        'status_id' => 1
                    ];
                    $this->journal_transaction_line_model->set($journal_transaction_id, $data);
                } //end of foreach
            }
        }
    }

    public function reverse_transaction()
    {
        $this->load->model('journal_transaction_model');
        $this->form_validation->set_rules("reverse_msg", "Reason", array("required"), array("required" => "%s must be entered"));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Share_transaction_model->reverse()) {
                    if ($this->input->post('payment_id') == 5) {
                        $this->load->model('Transaction_model');
                        $this->Transaction_model->reverse("SH" . $_POST['id']);
                    }
                    $ref_no = $this->input->post('transaction_no');
                    $this->journal_transaction_model->reverse(false, $ref_no, "(22,24)");

                    $feedback['success'] = true;
                    $feedback['message'] = "Transaction successfully cancled";
                    $this->helpers->activity_logs($_SESSION['id'], 12, "Reversing  tranaction", $feedback['message'] . " -# " . $this->input->post('transaction_no'), $this->input->post('transaction_no'), $this->input->post('transaction_no'));
                } else {
                    $feedback['message'] = "There was a problem reversing the transaction";
                    $this->helpers->activity_logs($_SESSION['id'], 12, "Reversing  tranaction", $feedback['message'] . " -# " . $this->input->post('transaction_no'), $this->input->post('transaction_no'), $this->input->post('transaction_no'));
                }
            }
        }
        echo json_encode($feedback);
    }

    public function change_status()
    {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
        //  if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        if (($response['success'] = $this->Share_transaction_model->delete($this->input->post('id'))) === true) {
            $response['message'] = "Transaction successfully deleted";
        }
        //  }
        echo json_encode($response);
    }


    private function share_call_journal_transaction($transaction_data)
    {
        $deposit_amount = round($this->input->post('amount'), 2);


        $narrative = "SHARE PURCHASE .. " . $this->input->post('transaction_date') . " [ " . $this->input->post('narrative') . " ]";

        $data = [
            'transaction_date' => $this->input->post('transaction_date'),
            'description' => $narrative,
            'ref_no' => $transaction_data['transaction_no'],
            'ref_id' => $transaction_data['share_account_id'],
            'status_id' => 1,
            'journal_type_id' => 22
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);

        if ($this->input->post('payment_id') == 5) {
            $savings_account = $this->savings_account_model->get($this->input->post('account_no_id'));
            $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);

            $linked_account_id = $savings_product_details['savings_liability_account_id'];
            $debit_or_credit2 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);

            $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
            $debit_or_credit1 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id'], true);
            $share_account_to_credit = $transaction_channel['linked_account_id'];
        } else {

            $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
            $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);
            $linked_account_id = $transaction_channel['linked_account_id'];

            $share_issuance_cat_details = $this->Share_issuance_model->get($this->input->post('share_issuance_id'));
            $share_account_to_credit = $share_issuance_cat_details['share_capital_account_id'];
            $debit_or_credit1 = $this->accounts_model->get_normal_side($share_issuance_cat_details['share_capital_account_id']);
        }



        //if deposit amount has been received
        if ($deposit_amount != null && !empty($deposit_amount) && $deposit_amount != '0') {
            $data[0] = [
                $debit_or_credit1 => $deposit_amount,
                'transaction_date' => $this->input->post('transaction_date'),
                'reference_no' => $transaction_data['transaction_no'],
                'reference_id' => $transaction_data['share_account_id'],
                'member_id' => $this->input->post('member_id'),
                'reference_key' => $this->input->post('share_account_no'),
                'narrative' => $narrative,
                'account_id' => $share_account_to_credit,
                'status_id' => 1
            ];
            $data[1] = [
                $debit_or_credit2 => $deposit_amount,
                'transaction_date' => $this->input->post('transaction_date'),
                'reference_no' => $transaction_data['transaction_no'],
                'reference_id' => $transaction_data['share_account_id'],
                'member_id' => $this->input->post('member_id'),
                'reference_key' => $this->input->post('share_account_no'),
                'narrative' => $narrative,
                'account_id' => $linked_account_id,
                'status_id' => 1
            ];
            $this->journal_transaction_line_model->set($journal_transaction_id, $data);
        } //end of the if
    }


    private function share_refund_journal_transaction($transaction_data)
    {
        $refund_amount = round($this->input->post('amount'), 2);

        $narrative = "SHARE REFUND .. " . $this->input->post('transaction_date') . " [ " . $this->input->post('narrative') . " ]";

        $data = [
            'transaction_date' => $this->input->post('transaction_date'),
            'description' => $narrative,
            'ref_no' => $transaction_data['transaction_no'],
            'ref_id' => $transaction_data['share_account_id'],
            'status_id' => 1,
            'journal_type_id' => 23
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);

        if ($this->input->post('payment_id') == 5) {
            $savings_account = $this->savings_account_model->get($this->input->post('account_no_id'));
            $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);

            $linked_account_id = $savings_product_details['savings_liability_account_id'];
            $debit_or_credit2 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], false);
        } else {

            $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
            $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id'], true);
            $linked_account_id = $transaction_channel['linked_account_id'];
        }

        $share_issuance_cat_details = $this->Share_issuance_model->get($this->input->post('share_issuance_id'));
        $debit_or_credit1 = $this->accounts_model->get_normal_side($share_issuance_cat_details['share_capital_account_id'], true);

        //if deposit amount has been received
        if ($refund_amount != null && !empty($refund_amount) && $refund_amount != '0') {
            $data[0] = [
                $debit_or_credit1 => $refund_amount,
                'transaction_date' => $this->input->post('transaction_date'),
                'reference_no' => $transaction_data['transaction_no'],
                'reference_id' => $transaction_data['share_account_id'],
                'member_id' => $this->input->post('member_id'),
                'reference_key' => $this->input->post('share_account_no'),
                'narrative' => $narrative,
                'account_id' => $share_issuance_cat_details['share_capital_account_id'],
                'status_id' => 1
            ];
            $data[1] = [
                $debit_or_credit2 => $refund_amount,
                'transaction_date' => $this->input->post('transaction_date'),
                'reference_no' => $transaction_data['transaction_no'],
                'reference_id' => $transaction_data['share_account_id'],
                'member_id' => $this->input->post('member_id'),
                'reference_key' => $this->input->post('share_account_no'),
                'narrative' => $narrative,
                'account_id' => $linked_account_id,
                'status_id' => 1
            ];
            $this->journal_transaction_line_model->set($journal_transaction_id, $data);
        }
    }


    public function print_receipt($id = false, $client_type = false)
    {
        if (empty($this->session->userdata('id'))) {
            redirect("welcome", "refresh");
        }
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['trans'] = $this->Share_transaction_model->get_transaction($id);
        $this->data['account'] = $this->Loan_guarantor_model->get_guarantor_savings('client_type=' . $client_type);

        $this->load->view('savings_account/deposits/savings_printout', $this->data);
    }

    public function import()
    {
        $this->load->model("share_transaction_model");
        $this->load->model("Share_fees_model");
        $feedback['success'] = false;
        $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
            $arr_file = explode('.', $_FILES['file']['name']);
            $extension = end($arr_file);
            if ('csv' == $extension) {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
            $spreadsheet = $reader->load($_FILES['file']['tmp_name']);

            $transaction_date = explode('-', $this->input->post('transaction_date'), 3);
            $date_formated = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) . " " . date("h:i:s")  : null;
            //$data['transaction_date'] =$this->helpers->get_date_time($data['transaction_date']);

            $failed = $passed = 0;
            $failed_data = array();
            foreach ($spreadsheet->getWorksheetIterator() as $worksheet_check) {
                $gethighestRow = $worksheet_check->getHighestRow();
                $getSheetByName = $worksheet_check->getTitle();
                $gethighestColumn = $worksheet_check->getHighestColumn();
                for ($row = 2; $row <= $gethighestRow; $row++) {
                    $share_issuance_id = $worksheet_check->getCellByColumnAndRow(1, $row)->getValue();
                    $share_account_id = $worksheet_check->getCellByColumnAndRow(2, $row)->getValue();
                    $client_no = $worksheet_check->getCellByColumnAndRow(3, $row)->getValue();
                    $name_check = $worksheet_check->getCellByColumnAndRow(5, $row)->getValue();
                    $amount_check = $worksheet_check->getCellByColumnAndRow(6, $row)->getValue();

                    $share_account_no = array('' => "");

                    if (!empty($client_no) && ($amount_check > 0)) {
                        $share_account_no = $this->member_model->get_member_info2($client_no);
                    }
                    if (empty($share_account_id) || (!is_numeric($amount_check) && !empty($amount_check)) || empty($share_issuance_id) || (empty($share_account_no))) {

                        if (empty($share_account_id) && ($amount_check <= 0)) {
                            $message = "Check both (Account ID and Amount). Row Number ( " . $row . " )";
                        } else if (empty($share_account_id)) {
                            $message = "Check Account ID . Row Number ( " . $row . " )";
                        } else if (!is_numeric($amount_check)) {
                            $message = "Amount is not a number . Row Number ( " . $row . " )";
                        } else if (!is_numeric($share_issuance_id)) {
                            $message = "Share Issuance ID is not a number . Row Number ( " . $row . " )";
                        } else if (empty($share_account_no)) {
                            $message = "Client No does not have an Account Number or Client no does not  EXIST . Row Number ( " . $row . " )";
                        } else {
                            $message = "Something is wrong with this record. Row Number ( " . $row . " )";
                        }
                        $failed_data[] = array(
                            'row_id' => $row,
                            'share_account_no_id' => $share_account_id,
                            'client_name' => $name_check,
                            'amount' => $amount_check,
                            'share_issuance_id' => $share_issuance_id,
                            'message' => $message
                        );
                        //$failed_data = array_merge($failed_data, $data1);
                        //$this->Transaction_model->bulk_error_log($failed_data);
                        $failed++;
                    }
                }
                
            }

            if (count($failed_data) < 1) {
                foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow();
                    $highestColumn = $worksheet->getHighestColumn();
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $share_account_id = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                        $client_no = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                        $name = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                        $amount = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                        $share_issuance_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        $share_account_no = $this->member_model->get_member_info2($client_no);
                        if (!empty($amount) && ($amount > 0)) {

                            $data = array(
                                'transaction_no' => date('ymdhms') . mt_rand(000, 999),
                                'share_account_id' => $share_account_id,
                                'credit' => $amount,
                                'transaction_type_id' => 9,
                                'transaction_date' => $date_formated,
                                'narrative' => $this->input->post('narrative') . "[ " . $name . " ]" . " as of " . $this->input->post('transaction_date'),
                                'date_created' => time(),
                                'created_by' => $_SESSION['id'],
                                'status_id' => 1,
                                'share_issuance_id' => $share_issuance_id,
                                'payment_id' => $this->input->post('payment_id')
                            );

                            /* $charges = $this->Share_fees_model->get(array('share_fees.id' => $share_account_no['share_issuance_id'],'share_fees.chargetrigger_id' => '4', 'share_fees.status_id' => '1', 'share_fees.status_id' => '1'));
                             */
                            $transaction_data = $this->db->insert('fms_share_transactions', $data);
                            $bulk_data = array();
                            $bulk_data = $data;
                            $bulk_data['transaction_channel_id'] = $this->input->post('transaction_channel_id');
                            $bulk_data['transaction_date'] = $this->input->post('transaction_date');
                            if ($transaction_data) {
                                $this->bulk_share_journal_transaction($bulk_data);
                            }

                            $passed++;
                        }
                    }
                }
                $response = "Records Imported successfully";
                $feedback['message'] = "( " . $passed . " ) " . $response . " ( " . $failed . " ) Failed , Check error log table";
                //$feedback['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings("(j.state_id = 5 OR j.state_id = 7)");
                $feedback['success'] = true;

                $this->helpers->activity_logs($_SESSION['id'], 12, "Importing share bulk deposit details", $feedback['message'], "# " . $bulk_data['transaction_no'], "# " . $bulk_data['transaction_no']);
            } else {
                $feedback['message'] = "( " . $failed . " ) records with errors , Check the error log. Fix them and Upload again";
                $feedback['success'] = false;
                $feedback['failed'] = $failed_data;

            }
        }
        
        echo json_encode($feedback);
    }


    //journal line 
    private function bulk_share_journal_transaction($bulk_data, $charges = false)
    {
        $this->load->model('accounts_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('Share_issuance_model');
        $this->load->model('journal_transaction_line_model');

        $refund_amount = round($bulk_data['credit'], 2);
        //print_r($refund_amount);die();

        $narrative = "SHARE REFUND .. " . $this->input->post('transaction_date') . " [ " . $this->input->post('narrative') . " ]";

        $data = [
            'transaction_date' => $this->input->post('transaction_date'),
            'description' => $narrative,
            'ref_no' => $bulk_data['transaction_no'],
            'ref_id' => $bulk_data['share_account_id'],
            'status_id' => 1,
            'journal_type_id' => 23
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        $transaction_channel = $this->transactionChannel_model->get($bulk_data['transaction_channel_id']);

        $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id'], false);

        $linked_account_id = $transaction_channel['linked_account_id'];

        $share_issuance_cat_details = $this->Share_issuance_model->get($bulk_data['share_issuance_id']);

        $debit_or_credit1 = $this->accounts_model->get_normal_side($share_issuance_cat_details['share_capital_account_id'], false);

        //if deposit amount has been received
        if ($refund_amount != null && !empty($refund_amount) && $refund_amount != '0') {
            $data[0] = [
                $debit_or_credit1 => $refund_amount,
                'transaction_date' => $bulk_data['transaction_date'],
                'reference_no' => $bulk_data['transaction_no'],
                'reference_id' => $bulk_data['share_account_id'],
                'narrative' => $bulk_data['narrative'],
                'account_id' => $share_issuance_cat_details['share_capital_account_id'],
                'status_id' => 1
            ];
            $data[1] = [
                $debit_or_credit2 => $refund_amount,
                'transaction_date' => $bulk_data['transaction_date'],
                'reference_no' => $bulk_data['transaction_no'],
                'reference_id' => $bulk_data['share_account_id'],
                'narrative' => $bulk_data['narrative'],
                'account_id' => $linked_account_id,
                'status_id' => 1
            ];

            $this->journal_transaction_line_model->set($journal_transaction_id, $data);
        }
    }



    public function export_excel()
    {
        //modified by Ambrose

        if (is_numeric($this->input->post('share_issuance_id')) && !empty($this->input->post('share_issuance_id'))) {
            $share_issuance_id = $this->input->post('share_issuance_id');

            $this->load->model("Shares_model");
            // $dataArray = $this->Loan_guarantor_model->get_guarantor_savings("(j.state_id = 5 OR j.state_id = 7)");
            $where = "share_issuance_id = " . $share_issuance_id;
            $dataArray = $this->shares_model->get_excel_data($where);

            // create php excel object
            $spreadsheet = new Spreadsheet();
            // set active sheet
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'Category');
            $sheet->setCellValue('B1', 'Account ID');
            $sheet->setCellValue('C1', 'Member No');
            $sheet->setCellValue('D1', 'Account Number');
            $sheet->setCellValue('E1', 'Account Name');
            $sheet->setCellValue('F1', 'Amount');

            $sheet->getStyle("A1:F1")->getFont()->setBold(true);

            $rowCount = 2;
            foreach ($dataArray as $data) {

                $sheet->setCellValue('A' . $rowCount, $data['share_issuance_id']);
                $sheet->setCellValue('B' . $rowCount, $data['id']);
                $sheet->setCellValue('C' . $rowCount, $data['client_no']);
                $sheet->setCellValue('D' . $rowCount, $data['share_account_no']);
                $sheet->setCellValue('E' . $rowCount, mb_strtoupper($data['member_name'], 'UTF-8'));

                $rowCount++;
            }
            $writer = new Xlsx($spreadsheet);
            $filename = 'Member Share Template';
            //cleans the file for unsupported file
            ob_end_clean();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            if ($writer) {
                $feedback['message'] = "Exported successfully";

                $this->helpers->activity_logs($_SESSION['id'], 12, "Exporting Shares Template", $feedback['message'], NULL, "# " . $filename);
            } else if (!$writer) {
                $feedback['message'] = "Exporting Failed";
                $this->helpers->activity_logs($_SESSION['id'], 12, "Exporting Shares Template", $feedback['message'], NULL, "# " . $filename);
            }
        }
    }

    public  function shares_report_pdf_print_out()
    {

        $this->load->model('branch_model');
        $this->load->model('organisation_model');
        $this->load->model('Share_transaction_model');
        $this->load->helper('pdf_helper');
        $transaction_status = $_POST['transaction_status'];
        $data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = " Share Report Transaction Summary";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['data'] = $this->Share_transaction_model->get2();
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $post_data = array(
            'transaction_status' => $_POST['transaction_status'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        );

        $data['filters'] = $post_data;
        $data['the_page_data'] = $this->load->view('shares/transaction/report/share_report_pdf_print_out', $data, TRUE);

        // echo json_encode($)

        echo json_encode($data);
        //$this->load->view('includes/pdf_template', $data);
    }

    public function export_excel_share_report($state_id, $status_id)
    {
        $this->load->model('Share_transaction_model');

        $_POST['state_id'] = $state_id;
        $_POST['status_id'] = $status_id;
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        $transaction_status = (int) $_POST['transaction_status'];

        $document_title = $transaction_status == 1 ? 'Periodic Shares Payment Report' : ($transaction_status == 2 ? 'Non Periodic Share Payment Report' : ''
        );

        $dataArray = $this->Share_transaction_model->get2();

        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', "$document_title " . "From: " . $start_date . " To: " . $end_date);
        $sheet->mergeCells("A1:I1");
        $sheet->getStyle("A1:I1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:I1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center

        $sheet->setCellValue('A2', 'SHARE ACCOUNT NO');
        $sheet->setCellValue('B2', 'ACCOUNT NAME');
        $sheet->setCellValue('C2', 'PRICE PER SHARE');
        $sheet->setCellValue('D2', 'NUMBER OF SHARES');
        $sheet->setCellValue('E2', 'SHARES BOUGHT');
        $sheet->setCellValue('F2', 'SHARES REFUNDED');
        $sheet->setCellValue('G2', 'SHARES TRANSFERED');
        $sheet->setCellValue('H2', 'TOTAL SHARES CHARGES');
        $sheet->setCellValue('I2', 'TOTAL AMOUNT');

        $sheet->getStyle("A2:I2")->getFont()->setBold(true);

        $rowCount   =   3;

        foreach ($dataArray as $data) {
            $sheet->setCellValue('A' . $rowCount, $data['share_account_no']);
            $sheet->setCellValue('B' . $rowCount, mb_strtoupper($data['member_name'], 'UTF-8'));
            $sheet->setCellValue('C' . $rowCount, $data['price_per_share'] ? $data['price_per_share'] : 0);
            $sheet->setCellValue('D' . $rowCount, round($data['total_amount'] / $data['price_per_share'], 2));
            $sheet->setCellValue('E' . $rowCount, $data['shares_bought']);
            $sheet->setCellValue('F' . $rowCount, $data['shares_refund']);
            $sheet->setCellValue('G' . $rowCount, $data['shares_transfer']);
            $sheet->setCellValue('H' . $rowCount, $data['charges']);
            $sheet->setCellValue('I' . $rowCount, $data['total_amount']);

            $rowCount++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('C2:C' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('D2:D' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('E2:E' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('E2:E' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F2:F' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G2:G' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H2:H' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('I2:I' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'I' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTAL');
        $sheet->getStyle($total_row)->getFont()->setBold(true);

        // calculate totals
        $sheet->setCellValue('E' . ($highestRow + 2), '=SUM(E2:E' . $highestRow . ')');
        $sheet->setCellValue('F' . ($highestRow + 2), '=SUM(F2:F' . $highestRow . ')');
        $sheet->setCellValue('G' . ($highestRow + 2), '=SUM(G2:G' . $highestRow . ')');
        $sheet->setCellValue('H' . ($highestRow + 2), '=SUM(H2:H' . $highestRow . ')');
        $sheet->setCellValue('I' . ($highestRow + 2), '=SUM(I2:I' . $highestRow . ')');

        $sheet->getStyle('E' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('I' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');

        $writer = new Xlsx($spreadsheet);

        $filename = $document_title . " Summary Report FROM " . $start_date . " TO " . $end_date;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
}
