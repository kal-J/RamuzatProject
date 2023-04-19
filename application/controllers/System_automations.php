<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Description of System_automations
 *
 * @author Eric
 */

use HTTP\Request2; // Only when installed with PEAR

class System_automations extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('helpers');
        $this->load->model('loan_state_model');
        $this->load->model('Loan_attached_saving_accounts_model');
        $this->load->model('Loan_guarantor_model');
        $this->load->model('repayment_schedule_model');
        $this->load->model('transaction_model');
        $this->load->model('loan_installment_payment_model');
        $this->load->model('journal_transaction_model');
        $this->load->model('loan_product_model');
        $this->load->model('DepositProduct_model');
        $this->load->model('savings_account_model');
        $this->load->model('client_loan_model');
        $this->load->model("Savings_account_model");
        $this->load->model("miscellaneous_model");
        $this->load->model('dashboard_model');

        $this->load->model('member_model');
        $this->load->model('member_fees_model');
        $this->load->model("saving_fees_model");
        $this->load->model("savings_product_fee_model");
        $this->load->model('applied_member_fees_model');
        $this->load->model('subscription_plan_model');
        $this->load->model('client_subscription_model');
        $this->load->model("automated_fees_model");
        $this->load->model("organisation_model");
        $this->load->library("session");

        ini_set('memory_limit', '200M');
        ini_set('upload_max_filesize', '200M');
        ini_set('post_max_size', '200M');
        ini_set('max_input_time', 3600);
        ini_set('max_execution_time', 3600);
    }

    public function sentepaytest($value = '')
    {
        $request = new HTTP_Request2();
        $request->setUrl('https://sentepay.com/app/api/transact/collect');
        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig(array(
            'follow_redirects' => true
        ));

        $headers = array(
            'X-Authorization' => 'XJxkAHR8QI6JLqDIHLfPLUzmHyUk9FstU8ez52FJ',
        );
        $request->setHeader($headers);

        $request->addPostParameter(array(
            'currency' => 'UGX',
            'provider' => 'MTN_UG',
            'amount' => '5000',
            'msisdn' => '256775959489',
            'narrative' => 'SAVINGS DEPOSIT',
            'ext_ref' => '1234',
            'callback_url' => 'http://callback.url/ordercompleter',
            'customer_names' => 'AJUNA REAGAN',
            'customer_email' => 'reagan@gmtconsults.com'
        ));
        try {
            $response = $request->send();
            if ($response->getStatus() == 200) {
                echo $response->getBody();
            } else {
                echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
                    $response->getReasonPhrase();
            }
        } catch (HTTP_Request2_Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // loan arrears script
    public function index()
    {
        $this->put_loans_in_of_arrears();
        echo "<br/>";
        $this->put_loans_out_of_arrears();
    }


    public function put_loans_in_of_arrears()
    {
        $response = "";

        $in_arrear_loans = $this->repayment_schedule_model->inarrears_loans();
        if (!empty($in_arrear_loans) && $in_arrear_loans != '') {
            $response = $this->loan_state_model->have_them_in_arrears($in_arrear_loans);
        } else {
            $response = "No in arrears loans found today " . date('d-m-Y H:i:s');
        }

        print($response);
        return 0;
    }

    public function put_loans_out_of_arrears()
    {
        $response = "";
        $out_of_arrears_loans = $this->repayment_schedule_model->out_of_arrears_loans();
        if (!empty($out_of_arrears_loans) && $out_of_arrears_loans != '') {
            $response = $this->loan_state_model->have_them_out_of_arrears($out_of_arrears_loans);
        } else {
            $response = "No loans out of arrears found today " . date('d-m-Y H:i:s');
        }

        print($response);
        return 0;
    }

    public function callback()
    {
        $text_response = $this->helpers->notification(2, "HELLO REAGAN", false);
    }
    public function clear_pending_payouts()
    {
        $this->load->library("beyonic_transactions");
        $pending_loans = $this->client_loan_model->get_excel_data(20);
        $count = 1;
        if (!empty($pending_loans) && $pending_loans != '') {
            foreach ($pending_loans as $loans) {
                $data = $this->beyonic_transactions->get_payment_id($loans['checkout_request_id']);
                $message = $data->rejected_reason ? $data->rejected_reason : ($data->last_error ? $data->last_error : $data->cancelled_reason);

                if ($data->state == "processed") {
                    $this->loan_state_model->have_them_active($loans['id']);
                }
                $this->loan_state_model->update_payments_request($loans['id'], $loans['checkout_request_id'], $message, $data->state);
                $count++;
            }
        }
        print($count);
    }

    // SentePay clear pending payouts
    public function clear_pending_sente_pay_payouts()
    {
        $this->load->library("sente_pay");

        $this->db->trans_begin();

        $pending_loans = $this->client_loan_model->get_excel_data(20);
        $count = 1;
        if (!empty($pending_loans) && $pending_loans != '') {
            foreach ($pending_loans as $loans) {
                $sente_pay_trans_data = [
                    'ext_ref' => $loans['ext_ref'],
                    'refNo' => $loans['ref_no']
                ];

                //print_r($sente_pay_trans_data); die();
                //echo json_encode(json_decode($loans['loan_disbursement_data'], true)); die;
                $data = $this->sente_pay->check_payment_status($sente_pay_trans_data);

                $message = isset($data['message']) ? $data['message'] : $data;
                //$state_id = 4;
                if (isset($data['status_code']) && $data['status_code'] >= 202) { // Failed payment
                    //$state_id = 4;

                }

                if (isset($data['status_code']) && $data['status_code'] == 200) {

                    $this->disburse_loan(json_decode($loans['loan_disbursement_data'], true));

                    $this->loan_state_model->have_them_active($loans['id']);

                    $response['success'] = TRUE;
                }

                $this->loan_state_model->update_sente_pay_payments_request($loans['id'], $loans['ext_ref'], $message, $message);

                $count++;
            }
        }

        if ($this->db->trans_status() && isset($response)) {
            $this->db->trans_commit();
            echo json_encode($response);
        } else {
            $this->db->trans_rollback();
            echo json_encode(['success' => FALSE]);
        }

        // print($count);
    }

    //auto loan installment repayments
    public function auto_installment_repayment()
    {
        $i = $total_amount = $total_amount1 = $penalty_value = $penalty_value1 = $total_sum_on_ac = 0;
        $state = 4;
        $installment_paid = false;

        $due_installments = $this->repayment_schedule_model->due_installments_data(false, false); #only active loans
        //print_r($due_installments);die;
        foreach ($due_installments as $key => $due_installment) {

            //checking the attachment of savings account to a loan account
            $attached_savings_accounts = $this->Loan_attached_saving_accounts_model->get('a.loan_id=' . $due_installment['client_loan_id']);

            $total_amount1 = ($penalty_value1 + $due_installment['due_principal'] + $due_installment['due_interest']);

            if (is_array($attached_savings_accounts) && !empty($attached_savings_accounts)) {
                //penalty check for an installment
                $penalty_value1 = $this->get_total_penalty($due_installment['id']);
                //print_r($penalty_value);die();
                $total_amount1 = ($penalty_value1 + $due_installment['due_principal'] + $due_installment['due_interest']);
                //fecting the details of a savings account
                foreach ($attached_savings_accounts as $key => $savings_account) {
                    $savings_data[$key] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $savings_account['saving_account_id']);
                    $current_balance = $savings_data[$key]['cash_bal'];
                    if ($current_balance > 0) {
                        if ($current_balance >= $total_amount1) {
                            $total_amount = $total_amount1;
                            $due_interest = $due_installment['due_interest'];
                            $due_principal = $due_installment['due_principal'];
                            $penalty_value = $penalty_value1;
                            $demanded_penalty = 0;
                            $state = 1;
                        } else {
                            if ($current_balance >= ($due_installment['due_principal'] + $due_installment['due_interest'])) {
                                $total_amount = $current_balance;
                                $due_interest = $due_installment['due_interest'];
                                $due_principal = $due_installment['due_principal'];
                                $penalty_value = $current_balance - ($due_installment['due_principal'] + $due_installment['due_interest']);
                                $demanded_penalty = $penalty_value1 - $penalty_value;
                                $state = 2;
                            } elseif ($current_balance >= $due_installment['due_principal']) {
                                $total_amount = $current_balance;
                                $due_principal = $due_installment['due_principal'];
                                $due_interest = $current_balance - $due_installment['due_principal'];
                                $penalty_value = 0;
                                $demanded_penalty = $penalty_value1;
                                $state = 2;
                            } else {
                                $total_amount = $current_balance;
                                $due_principal = $current_balance;
                                $due_interest = 0;
                                $penalty_value = 0;
                                $demanded_penalty = $penalty_value1;
                                $state = 2;
                            }
                        }

                        $deduction_data['amount'] = $total_amount;
                        $deduction_data['account_no_id'] = $savings_account['saving_account_id'];
                        $deduction_data['narrative'] = 'Automatic payment deduction made to clear your loan [' . $due_installment['loan_no'] . '] Installment';
                        # call the tranfer function
                        $this->db->trans_begin();
                        $transaction_data = $this->transaction_model->deduct_savings($deduction_data);
                        if (is_array($transaction_data)) {
                            $loan_payment_data['paid_interest'] = $due_interest;
                            $loan_payment_data['paid_principal'] = $due_principal;
                            $loan_payment_data['repayment_schedule_id'] = $due_installment['id'];
                            $loan_payment_data['paid_penalty'] = $penalty_value;
                            $loan_payment_data['expected_penalty'] = $penalty_value1;
                            $loan_payment_data['paid_total'] = $total_amount;
                            $loan_payment_data['expected_total'] = $total_amount1;
                            $loan_payment_data['demanded_penalty'] = $demanded_penalty;
                            $loan_payment_data['client_loan_id'] = $due_installment['client_loan_id'];


                            $loan_payment_data['prev_demanded_penalty'] = $due_installment['demanded_penalty'];
                            $loan_payment_data['prev_payment_status'] = $due_installment['payment_status'];
                            $loan_payment_data['prev_payment_date'] = $due_installment['actual_payment_date'];
                            $loan_payment_data['state'] = $state;
                            $loan_payment_data['unique_id'] = time();
                            #$loan_payment_data['transaction_channel_id']=;
                            $payment_id = $this->loan_installment_payment_model->auto_payment($loan_payment_data);
                            if (is_numeric($payment_id)) {
                                $this->repayment_schedule_model->clear_installment($loan_payment_data);
                                $loan_payment_data['account_no_id'] = $savings_account['saving_account_id'];
                                $loan_payment_data['comment'] = 'Loan payment by the system';
                                $loan_payment_data['transaction_no'] = $transaction_data['transaction_no'];
                                $loan_payment_data['transaction_id'] = $transaction_data['transaction_id'];
                                $this->loan_payment_journal_transaction($loan_payment_data);
                                if ($penalty_value > 0) {
                                    $this->penalty_journal_transaction($loan_payment_data);
                                }
                                $expected_payment = $this->repayment_schedule_model->sum_interest_principal($due_installment['client_loan_id']);
                                $paid_amount = $this->loan_installment_payment_model->sum_paid_installment($due_installment['client_loan_id']);
                                if ($paid_amount['already_paid_sum'] >= $expected_payment['total_payment']) {
                                    $loan_payment_data['comment'] = 'Payment deducted from savings and full obligation met';
                                    $loan_payment_data['state_id'] = 10;
                                    $this->loan_state_model->set($loan_payment_data);

                                    $message = number_format($total_amount, 2) . "/= as loan payment for installment " . $due_installment['installment_number'] . " of loan number " . $due_installment['loan_no'] . " has been deducted from your account " . $savings_data[$key]['account_no'] . "- " . date('d-m-Y H:i:s') . " settling the whole loan";
                                } else {
                                    $message = number_format($total_amount, 2) . "/= as loan payment for installment " . $due_installment['installment_number'] . " of loan number " . $due_installment['loan_no'] . " has been deducted from your account " . $savings_data[$key]['account_no'] . "- " . date('d-m-Y H:i:s');
                                }

                                #email notification
                                $email_message = "Payment of amount " . number_format($total_amount, 2) . "/= has been made from your account " . $savings_data[$key]['account_no'] . " as loan payment for installment " . $due_installment['installment_number'] . " of loan number " . $due_installment['loan_no'] . "  today " . date('d-m-Y H:i:s');

                                $this->helpers->send_email($savings_data[$key]['id'], $email_message, false);
                                #check for the sms module
                                if (!empty($result = $this->miscellaneous_model->check_org_module(22, 1))) {
                                    $this->helpers->notification($due_installment['client_loan_id'], $message);
                                }
                                $installment_paid = true;
                            }
                            if ($this->db->trans_status()) {
                                $this->db->trans_commit();
                            } else {
                                $this->db->trans_rollback();
                            }
                            //if not array was returned then an error happened therefore rollback
                        } else {
                            $this->db->trans_rollback();
                        }
                        break; //not to cross check yet its payment happened on the first loop
                    } else {
                        #email notification
                        $email_message = "<strong>Failed Payment</strong><br> Payment of amount " . number_format($total_amount, 2) . "/= for installment <strong>" . $due_installment['installment_number'] . " of loan number " . $due_installment['loan_no'] . "</strong> has failed due to insufficient balance on your account " . $savings_data[$key]['account_no'] . " as of " . date('d-m-Y H:i:s') . ". Please deposit money on your account to settle the installment to avoid extra charges.";

                        $this->helpers->send_email($savings_data[$key]['id'], $email_message, false);
                        #check for the sms module
                        $message = "Failed payment of " . number_format($total_amount, 2) . "/= for installment " . $due_installment['installment_number'] . " of loan number " . $due_installment['loan_no'] . " due to insufficient balance on your account " . $savings_data[$key]['account_no'] . " as of " . date('d-m-Y H:i:s');
                        if (!empty($result = $this->miscellaneous_model->check_org_module(22, 1))) {
                            //$this->helpers->notification($due_installment['client_loan_id'], $message);
                        }
                        $total_sum_on_ac += $current_balance;
                    }
                    $i++;
                } #end of loop for savings account data

                if ($total_sum_on_ac >= $total_amount) {
                    # pick money on both accounts and clear
                }
            } else {
                #no savings account attached so automatic payment not possible,next installment
                #email notification
                $now = date('Y-m-d');
                if ($now > $due_installment['repayment_date']) {
                    $email_message = "<strong>Due Payment</strong><br> Payment of amount " . number_format($total_amount, 2) . "/= for installment <strong>" . $due_installment['installment_number'] . " of loan number " . $due_installment['loan_no'] . " was expected on " . $due_installment['repayment_date'] . "</strong>, Please remember to settle the installment to avoid extra charges.";
                } else {
                    $email_message = "<strong>Due Payment</strong><br> Payment of amount " . number_format($total_amount, 2) . "/= for installment <strong>" . $due_installment['installment_number'] . " of loan number " . $due_installment['loan_no'] . "</strong> is expected today, Please remember to settle the installment to avoid extra charges.";
                }

                $this->helpers->send_email($due_installment['client_loan_id'], $email_message);
                #check for the sms module
                $message = "Due payment of " . number_format($total_amount, 2) . "/= for installment " . $due_installment['installment_number'] . " of loan number " . $due_installment['loan_no'] . " is expected";
                if (!empty($result = $this->miscellaneous_model->check_org_module(22, 1))) {
                    //$this->helpers->notification($due_installment['client_loan_id'], $message);
                }
            } #end if for savings account

            $i = $total_amount = $penalty_value = $total_sum_on_ac = 0;
        }
    }

    public function get_total_penalty($installment_id)
    {
        $total_penalty = 0;
        $due_installments_data = $this->repayment_schedule_model->due_installments_data($installment_id);
        if (!empty($due_installments_data)) {
            $over_due_principal = $due_installments_data['due_principal'];
            if ($due_installments_data['demanded_penalty'] > 0) {
                $number_of_late_days = $due_installments_data['due_days2'];
            } else {
                $number_of_late_days = $due_installments_data['due_days'] - $due_installments_data['grace_period_after'];
            }

            $penalty_rate = (($due_installments_data['penalty_rate']) / 100);

            if ($due_installments_data['penalty_rate_charged_per'] == 3) {
                $number_of_late_period = intdiv($number_of_late_days, 30);
            } elseif ($due_installments_data['penalty_rate_charged_per'] == 2) {
                $number_of_late_period = intdiv($number_of_late_days, 7);
            } else {
                $number_of_late_period = $number_of_late_days;
            }

            $penalty_value = ($over_due_principal * $number_of_late_period * $penalty_rate);

            $data['data']['penalty_value'] = $due_installments_data['demanded_penalty'] > 0 ? round($penalty_value + $due_installments_data['demanded_penalty'], 0) : round($penalty_value, 0);
        } else {
            $data['data']['penalty_value'] = $due_installments_data['demanded_penalty'];
        }
        $total_penalty += $data['data']['penalty_value'];
        return $total_penalty;
    }

    //Beginning of upgrades by Joshua Nabuka - (Automated payments for subscriptions, Memberships and Savings)
    public function create_subscription_schedule()
    {
        $this->load->model("fiscal_model");

        $subscription_schedule_id = '';
        $fiscal_active = $this->fiscal_model->current_fiscal_year();

        $new_period_checker = $this->automated_fees_model->get(false, $fiscal_active['start_date'], 'fms_subscription_schedule', "subscription_plan", 'subscription_fee_id');

        echo json_encode($new_period_checker);
        die;

        if (empty($new_period_checker)) {
            $data = $this->miscellaneous_model->get_all_member_subscription_schedule();
        } else {
            $data = $this->miscellaneous_model->get_member_subscription_schedule(
                $fiscal_active['start_date'],
                $fiscal_active['end_date'],
                'fms_subscription_schedule',
                'member_id'
            );
        }


        $subscription_plan = $this->subscription_plan_model->get(1);
        if (!empty($data)) {
            foreach ($data as $user_data) {
                $subscription_data['member_id'] = $user_data['id'];
                $subscription_data['amount'] = $subscription_plan['amount_payable'];
                $subscription_data['subscription_fee_id'] = $subscription_plan['id'];
                $subscription_data['last_payment_date'] = null;
                $subscription_data['subscription_date'] = $fiscal_active['start_date'];
                $subscription_data['created_by'] = 1;
                $subscription_data['state'] = 20;

                $subscription_schedule_id = $this->automated_fees_model->set($subscription_data, 'fms_subscription_schedule');
            }
        }

        if ($subscription_schedule_id != '') {
            echo "Successfull schedule creation. **** " . strval($subscription_schedule_id['last_id']);
        } else {
            echo "No schedules were created.";
        }
    }

    public function auto_subscription_payment()
    {
        $make_subscription = false;
        $subscription_defaulters = $this->automated_fees_model->get_defaulters('fms_subscription_schedule', 'state');
        $subscription_plan = $this->subscription_plan_model->get(1);
        //$savings_data=$this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7 AND a.member_id=124');
        foreach ($subscription_defaulters as $defaulter) {
            $savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7 AND a.member_id=' . $defaulter['client_id']);
            foreach ($savings_data as $savings) {
                $current_balance = $savings['cash_bal'];
                if ($current_balance >= $defaulter['amount']) {
                    $deduction_data['amount'] = $defaulter['amount'];
                    $deduction_data['account_no_id'] = $savings['id'];
                    $deduction_data['narrative'] = 'Automatic deduction payment made to clear your ' . ucfirst($subscription_plan['plan_name']);

                    $subscription_data['client_id'] = $defaulter['id'];
                    $subscription_data['payment_date'] = date('Y-m-d H:i:s');
                    $subscription_data['payment_id'] = 5;
                    $subscription_data['modified_by'] = 1;

                    $transaction_data = $this->transaction_model->deduct_savings($deduction_data);

                    $make_subscription = $this->client_subscription_model->auto_update($subscription_data);

                    $journal_data['payment_id'] = $defaulter['id'];
                    $journal_data['transaction_date'] = date('d-m-Y');
                    $journal_data['deposit_Product_id'] = $savings['deposit_Product_id'];
                    $journal_data['income_account_id'] = $subscription_plan['income_account_id'];
                    $journal_data['amount'] = $defaulter['amount'];
                    $journal_data['journal_id'] = 11;
                    $journal_data['narrative'] = $savings['member_name'] . ' - Subscription payment made from a client\'s savings';

                    $this->automated_journal_transaction($transaction_data, $journal_data);

                    break;
                }
            }
        }

        if ($make_subscription == true) {
            echo "Successfull payments";
        } else {
            echo "No payments to make";
        }
    }

    public function sync_subscription_tables()
    {
        $subscription_schedule_id = [];
        $fiscal_active = $this->dashboard_model->get_current_fiscal_year(1, 1);
        $data = $this->miscellaneous_model->get_member_subscription_schedule(
            $fiscal_active['start_date'],
            $fiscal_active['end_date'],
            'fms_subscription_schedule',
            'member_id'
        );
        $subscription_plan = $this->member_fees_model->get();

        foreach ($subscription_plan as $plan) {
            foreach ($data as $user_data) {
                $subscription_data['member_id'] = $user_data['id'];
                $subscription_data['amount'] = $plan['amount_payable'];
                $subscription_data['subscription_fee_id'] = $plan['id'];
                $subscription_data['last_payment_date'] = null;
                $subscription_data['subscription_date'] = $fiscal_active['start_date'];
                $subscription_data['created_by'] = 1;
                $subscription_data['state'] = 20;

                //populating the membership schedule

                $subscription_schedule_id = $this->applied_member_fees_model->set($subscription_data);
            }
        }

        if ($subscription_schedule_id != null) {
            echo "Schedule created successfully " . strval($subscription_schedule_id['transaction_id']);
        } else {
            echo "No clients to sync ";
        }
    }

    public function automated_membership()
    {
        $pay_membership = false;
        $membership_defaulters = $this->miscellaneous_model->get_defaulters('fms_applied_member_fees', 'fee_paid');

        // not returning mandatory fees only
        $membership_plan = $this->member_fees_model->get();

        foreach ($membership_defaulters as $defaulter) {
            $user_savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7 AND a.member_id=' . $defaulter['member_id']);
            foreach ($membership_plan as $plan) {
                if ($defaulter['member_fee_id'] == $plan['id']) {
                    foreach ($user_savings_data as $savings) {
                        $current_balance = $savings['cash_bal'];
                        if ($current_balance >= $defaulter['amount']) {
                            $deduction_data['amount'] = $defaulter['amount'];
                            $deduction_data['account_no_id'] = $savings['id'];
                            $deduction_data['narrative'] = 'Automatic deduction payment made to clear your ' . ucfirst($this->member_fees_model->get($plan['id'])['feename']) . ' Subscription';

                            $membership_data['member_id'] = $defaulter['id'];
                            $membership_data['payment_date'] = date('Y-m-d H:i:s');
                            $membership_data['payment_id'] = 5;
                            $membership_data['modified_by'] = 1;

                            $transaction_data = $this->transaction_model->deduct_savings($deduction_data);

                            $pay_membership = $this->applied_member_fees_model->auto_update($membership_data);

                            $journal_data['payment_id'] = $defaulter['id'];
                            $journal_data['transaction_date'] = date('d-m-Y');
                            $journal_data['deposit_Product_id'] = $savings['deposit_Product_id'];
                            $journal_data['income_account_id'] = $plan['income_account_id'];
                            $journal_data['amount'] = $defaulter['amount'];
                            $journal_data['journal_id'] = 12;
                            $journal_data['narrative'] = $savings['member_name'] . ' - Membership payment made from a client\'s savings';


                            $this->automated_journal_transaction($transaction_data, $journal_data);

                            break;
                        }
                    }
                }
            }
        }

        if ($pay_membership == true) {
            echo "Successful membership payments";
        } else {
            echo "No membership payments to make";
        }
    }

    public function automated_savings()
    {
        $this->load->model("fiscal_model");
        $fiscal_active = $this->fiscal_model->current_fiscal_year();
        $defined_date = $fiscal_active['start_date'];
        //echo $defined_date;
        $create_savings = [];
        $update_date = '';
        $transaction_data = [];
        $date = '';
        $savings_product = $this->savings_product_fee_model->get('s.status_id=1 AND chargetrigger_id=2');
        //print_r($savings_product);die();
        foreach ($savings_product as $product) {
            $savings_accs = $this->miscellaneous_model->set_data($product['saving_product_id']);
            // print_r($savings_product);die();

            foreach ($savings_accs as $data) {
                $savings_accounts = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7 AND a.id=' . $data['savings_account_id']);

                $current_balance = $savings_accounts[0]['cash_bal'];

                $last_pay_date['last_payment_date'] = $last_pay_date1['last_payment_date'] = ($data['last_payment_date'] < $defined_date) ? $defined_date : $data['last_payment_date'];
                //print_r($last_pay_date['last_payment_date']);

                if ($product['repayment_made_every'] == 1) {
                    $frequency = $product['repayment_frequency'];
                    $date = date('Y-m-d', strtotime(' -' . $frequency . ' days'));
                    $last_pay_date['last_payment_date'] = date('Y-m-d', strtotime($last_pay_date['last_payment_date'] . ' +' . $frequency . ' days'));
                } elseif ($product['repayment_made_every'] == 2) {
                    $frequency = $product['repayment_frequency'];
                    $date = date('Y-m-d', strtotime(' -' . $frequency . ' week'));
                    $last_pay_date['last_payment_date'] = date('Y-m-d', strtotime($last_pay_date['last_payment_date'] . ' +' . $frequency . ' week'));
                } elseif ($product['repayment_made_every'] == 3) {
                    $frequency = $product['repayment_frequency'];
                    $date = date('Y-m-d', strtotime(' -' . $frequency . ' months'));
                    $last_pay_date['last_payment_date'] = date('Y-m-d', strtotime($last_pay_date['last_payment_date'] . ' +' . $frequency . ' months'));
                }

                if ($last_pay_date1['last_payment_date'] <= $date) {
                    if ($current_balance >= $product['charge_amount']) {
                        $deduction_data['amount'] = $product['charge_amount'];
                        $deduction_data['account_no_id'] = $data['savings_account_id'];
                        $deduction_data['transaction_date'] = date('d-m-Y', strtotime($last_pay_date['last_payment_date']));
                        $deduction_data['narrative'] = date('F-Y', strtotime($last_pay_date['last_payment_date'])) . ' - ' . $product['feename'];

                        $transaction_data = $this->transaction_model->deduct_savings($deduction_data);

                        $update_date = $this->miscellaneous_model->update_data($last_pay_date, $data['id']);

                        $journal_data['payment_id'] = null;
                        $journal_data['transaction_date'] = date('d-m-Y', strtotime($last_pay_date['last_payment_date']));
                        $journal_data['deposit_Product_id'] = $product['saving_product_id'];
                        $journal_data['income_account_id'] = $product['savings_fees_income_account_id'];
                        $journal_data['amount'] = $product['charge_amount'];
                        $journal_data['journal_id'] = 13;
                        $journal_data['narrative'] = date('F-Y', strtotime($last_pay_date['last_payment_date'])) . ' - ' . $savings_accounts[0]['member_name'] . ' ' . $product['feename'];

                        $this->automated_journal_transaction($transaction_data, $journal_data);
                    }
                }
            }
        }

        if ($update_date == true) {
            echo "Successful maintenance payments: " . json_encode($update_date, true);
            echo json_encode($transaction_data, true);
        } else {
            echo "No savings accounts were added";
        }
    }

    //auto subscription
    /* public function auto_subscription()
    {
        $members = $this->member_model->get_member();
        //print_r($members);die();
        foreach ($members as $key => $value) { //looping through members
            if (isset($value['subscription_plan_id']) && $value['subscription_plan_id'] != '') {
                $savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7 AND a.member_id=' . $value['id']);

                $subscription_plan = $this->subscription_plan_model->get($value['subscription_plan_id']);
                $client_subscription_data = $this->client_subscription_model->client_recent_payment($value['id']);
                $fiscal_active = $this->dashboard_model->get_current_fiscal_year($value['organisation_id'], 1);

                if ($subscription_plan['repayment_made_every'] == 1) {
                    $dStart = new DateTime($payment_date1);
                    $dEnd = new DateTime($payment_date2);
                    $dDiff = $dStart->diff($dEnd);
                    $recent_payment_was = '';
                } elseif ($subscription_plan['repayment_made_every'] == 2) {
                    $dStart = new DateTime($payment_date1);
                    $dEnd = new DateTime($payment_date2);
                    $dDiff = $dStart->diff($dEnd);
                    $recent_payment_was = '';
                } elseif ($subscription_plan['repayment_made_every'] == 3) {
                    $dStart = new DateTime($payment_date1);
                    $dEnd = new DateTime($payment_date2);
                    $dDiff = $dStart->diff($dEnd);
                    $recent_payment_was = '';
                }


                foreach ($savings_data as $key => $value2) { //looping through a members savings account
                    $current_balance = $value2['cash_bal'];
                    // $current_balance=($value2['cash_bal']-$value2['min_balance']);#Uncomment this if the organisation allows payment on an account with saving after reserving the minimum balance

                    if ($current_balance >= $subscription_plan['amount_payable']) {
                        $deduction_data['amount'] = $subscription_plan['amount_payable'];
                        $deduction_data['account_no_id'] = $value2['id'];
                        $deduction_data['narrative'] = 'Automatic deduction payment made to clear your ' . ucfirst($subscription_plan['plan_name']);

                        # call the tranfer function
                        $this->db->trans_begin();
                        $transaction_data = $this->transaction_model->deduct_savings($deduction_data);
                        if (is_array($transaction_data)) {
                            $deduction_data['amount'] = $subscription_plan['amount_payable'];
                            $deduction_data['client_id'] = $value['id'];

                            $deduction_data['narrative'] = 'Payment  made to clear ' . ucfirst($subscription_plan['plan_name']);
                            $subscription_payment_id = $this->client_subscription_model->set2($deduction_data);

                            if (is_numeric($subscription_payment_id)) {
                                $transaction_data['member_id'] = $value['id'];
                                $transaction_data['account_no_id'] = $value2['id'];
                                $transaction_data['comment'] = 'Payment made for ' . ucfirst($subscription_plan['plan_name']);
                                $transaction_data['amount'] = $subscription_plan['amount_payable'];
                                $this->subscription_journal_transaction($subscription_payment_id, $transaction_data);
                                $message = "Payment of amount " . number_format($subscription_plan['amount_payable'], 2) . "/= has been made from your account " . $value2['account_no'] . " today " . date('d-m-Y H:i:s');
                                $this->helpers->send_email($value2['id'], $message, false);
                                #check for the sms module
                                if (!empty($result = $this->miscellaneous_model->check_org_module(22, 1))) {
                                    $this->helpers->notification($value2['id'], $message, false);
                                }
                                //complete the transaction and check the status of the db
                                if ($this->db->trans_status()) {
                                    $this->db->trans_commit();
                                    break; //Jump out of the first loop to avoid double payment
                                } else {
                                    $this->db->trans_rollback();
                                }
                            } else {
                                $this->db->trans_rollback();
                            }
                        } else {
                            $this->db->trans_rollback();
                        }
                    }
                } //end of the savings loop
            } //if checking whether member has plan attached
        } //end of the member loop
    }
 */
    //Membership payment schedule - Joshua Nabuka
    public function fees()
    {
        $this->load->model("member_model");
        $this->load->model("Staff_model");
        $this->load->model("fiscal_model");
        $this->load->model("miscellaneous_model");
        $this->load->model("Automated_fees_model");
        $fiscal_active = $this->fiscal_model->current_fiscal_year();

        $defaulters = $this->Automated_fees_model->get(false, $fiscal_active['start_date']);
        $defaulter = $this->Automated_fees_model->get("state=5");

        $member = $this->Automated_fees_model->get_details("state=5");

        $data = $this->miscellaneous_model->get_member_subscription_schedule(
            $fiscal_active['start_date'],
            $fiscal_active['end_date'],
            'fms_membership_schedule',
            'member_id'
        );
        echo json_encode($member, true);
    }

    //accounting concepts
    private function loan_payment_journal_transaction($transaction_data)
    {
        $payment_date = date('d-m-Y');

        $savings_account = $this->savings_account_model->get($transaction_data['account_no_id']);
        $client_loan = $this->client_loan_model->get_client_data($transaction_data['client_loan_id']);

        $principal_amount = round($transaction_data['paid_principal'], 2);
        $interest_amount = round($transaction_data['paid_interest'], 2);
        //then we prepare the journal transaction lines
        if (!empty($client_loan)) {
            $this->load->model('accounts_model');
            $this->load->model('transactionChannel_model');
            $this->load->model('journal_transaction_line_model');

            $data = [
                'transaction_date' => $payment_date,
                'description' => strtoupper($transaction_data['comment']),
                'ref_no' => $transaction_data['transaction_no'],
                'ref_id' => $transaction_data['transaction_id'],
                'status_id' => 1,
                'journal_type_id' => 6
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);

            $loan_product_details = $this->loan_product_model->get_accounts($client_loan['loan_product_id']);
            $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);

            $debit_or_credit1 = $this->accounts_model->get_normal_side($loan_product_details['loan_receivable_account_id'], true);
            $debit_or_credit3 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);
            $debit_or_credit4 = $this->accounts_model->get_normal_side($loan_product_details['interest_receivable_account_id'], true);
            $debit_or_credit5 = $this->accounts_model->get_normal_side($loan_product_details['interest_income_account_id'], true);

            //if principal has been received
            if ($principal_amount != null && !empty($principal_amount) && $principal_amount != '0') {
                $data[0] = [
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'transaction_date' => $payment_date,
                    $debit_or_credit1 => $principal_amount,
                    'narrative' => strtoupper("Loan principal payment on " . $payment_date . " done by the system"),
                    'account_id' => $loan_product_details['loan_receivable_account_id'],
                    'status_id' => 1
                ];
                $data[1] = [
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'transaction_date' => $payment_date,
                    $debit_or_credit3 => $principal_amount,
                    'narrative' => strtoupper("Loan principal payment on " . $payment_date . " done by the system"),
                    'account_id' => $savings_product_details['savings_liability_account_id'],
                    'status_id' => 1
                ];
            }

            //if interest has been received
            if ($interest_amount != null && !empty($interest_amount) && $interest_amount != '0') {
                $data[2] = [
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'transaction_date' => $payment_date,
                    $debit_or_credit4 => $interest_amount,
                    'narrative' => strtoupper("Loan interest payment on " . $payment_date . " done by the system"),
                    'account_id' => $loan_product_details['interest_receivable_account_id'],
                    'status_id' => 1
                ];
                $data[3] = [
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'transaction_date' => $payment_date,
                    $debit_or_credit3 => $interest_amount,
                    'narrative' => strtoupper("Loan interest payment on " . $payment_date . " done by the system"),
                    'account_id' => $savings_product_details['savings_liability_account_id'],
                    'status_id' => 1
                ];
            }
            $this->journal_transaction_line_model->set($journal_transaction_id, $data);
        }
    }

    //loan penalty
    private function penalty_journal_transaction($transaction_data)
    {
        $savings_account = $this->savings_account_model->get($transaction_data['account_no_id']);
        $client_loan = $this->client_loan_model->get_client_data($transaction_data['client_loan_id']);
        $payment_date = date('d-m-Y');
        $penalty_amount = round($transaction_data['paid_penalty'], 2);
        //then we prepare the journal transaction lines
        if (!empty($client_loan) && $penalty_amount != null && !empty($penalty_amount) && $penalty_amount != '0') {
            $this->load->model('accounts_model');
            $this->load->model('transactionChannel_model');
            $this->load->model('journal_transaction_line_model');

            $data = [
                'ref_no' => $transaction_data['transaction_no'],
                'ref_id' => $transaction_data['transaction_id'],
                'transaction_date' => $payment_date,
                'description' => $transaction_data['comment'],
                'status_id' => 1,
                'journal_type_id' => 5
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);

            $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);
            $loan_product_details = $this->loan_product_model->get_accounts($client_loan['loan_product_id']);

            $debit_or_credit2 = $this->accounts_model->get_normal_side($loan_product_details['penalty_income_account_id']);
            $debit_or_credit3 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);

            //if penalty has been recieved
            if ($penalty_amount != null && !empty($penalty_amount) && $penalty_amount != '0') {
                $data[0] =
                    [
                        'reference_no' => $transaction_data['transaction_no'],
                        'reference_id' => $transaction_data['transaction_id'],
                        'transaction_date' => $payment_date,
                        $debit_or_credit2 => $penalty_amount,
                        'narrative' => strtoupper("Loan penalty payment on " . $payment_date . " done by the system"),
                        'account_id' => $loan_product_details['penalty_income_account_id'],
                        'status_id' => 1
                    ];
                $data[1] = [
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'transaction_date' => $payment_date,
                    $debit_or_credit3 => $penalty_amount,
                    'narrative' => strtoupper("Loan penalty payment on " . $payment_date . " done by the system"),
                    'account_id' => $savings_product_details['savings_liability_account_id'],
                    'status_id' => 1
                ];
            }
            $this->journal_transaction_line_model->set($journal_transaction_id, $data);
        }
    }

    public function savings_schedule_script()
    {
        $this->load->model("DepositProduct_model");
        $this->load->model("savings_schedule_model");

        $mandatory_products = $this->DepositProduct_model->get_products('mandatory_saving=1');
        if ($mandatory_products != '') { //Products with mandatory savings exit?

            foreach ($mandatory_products as $key => $product) {
                $current_sche_date = ($product['schedule_current_date'] && $product['schedule_current_date'] != '0000-00-00') ? $product['schedule_current_date'] : $product['schedule_start_date'];
                if ($schedule_dates = $this->next_schedule($current_sche_date, $product['saving_frequency'], $product['saving_made_every'])) {
                    if ($schedule_data = $this->prepare_schedule_data($product['id'], $schedule_dates)) {
                        if ($this->savings_schedule_model->set($schedule_data)) {
                            $data['id'] = $product['id'];
                            $data['schedule_current_date'] = $schedule_dates['end'];
                            $this->DepositProduct_model->update_schedule_details($data);
                        }
                    }
                }
            }
        }
    }

    private function prepare_schedule_data($product_id, $schedule_dates)
    {
        $this->load->model("savings_schedule_model");

        $excluded_accounts = [];
        $schedule_data = [];

        $accounts_with_schedule_already = $this->savings_schedule_model->get("a.from_date='" . $schedule_dates['start'] . "' AND a.to_date='" . $schedule_dates['end'] . "'");
        if ($accounts_with_schedule_already && $accounts_with_schedule_already != '') {
            foreach ($accounts_with_schedule_already as $key => $value) {
                $excluded_accounts[] = $value['saving_acc_id'];
            }
            $accounts_to_schedule = $this->savings_schedule_model->get_accounts_under_mandatory_saving("b.id=" . $product_id, $excluded_accounts);
        } else { //no excluded accounts
            $accounts_to_schedule = $this->savings_schedule_model->get_accounts_under_mandatory_saving("b.id=" . $product_id);
        }

        if (isset($accounts_to_schedule) && $accounts_to_schedule != '') { //if the accounts exist
            foreach ($accounts_to_schedule as $key => $account) {
                $schedule_data[] = array(
                    'saving_acc_id' => $account['id'],
                    'from_date' => $schedule_dates['start'],
                    'to_date' => $schedule_dates['end1'],
                    'date_created' => time(),
                    'created_by' => 1 //system user
                );
            }
            return $schedule_data;
        } else {
            return false;
        }
    }

    private function next_schedule($current_schedule_date, $savings_freq, $savings_every)
    {
        $schedule_interval = '';
        if (date('Y-m-d') > $current_schedule_date) { //still under the current schedule

            if ($savings_every == 1) {
                $schedule_interval = $savings_freq . ' day';
            } elseif ($savings_every == 2) {
                $schedule_interval = $savings_freq . ' week';
            } elseif ($savings_every == 3) {
                $schedule_interval = $savings_freq . ' month';
            }

            if ($schedule_interval != '') {
                $new_savings_schedule['start'] = $current_schedule_date;
                $new_savings_schedule['end'] = date('Y-m-d', strtotime($schedule_interval, strtotime($current_schedule_date)));
                $new_savings_schedule['end1'] = date('Y-m-d', strtotime('-1 day', strtotime($new_savings_schedule['end'])));
                return $new_savings_schedule;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function savings_reminder()
    {
        $this->load->model("DepositProduct_model");
        $mandatory_products = $this->DepositProduct_model->get_products('mandatory_saving=1');
        $having_clause = '';
        if ($mandatory_products != '') { //Products with mandatory savings exit?
            foreach ($mandatory_products as $key => $product) {
                if ($product['reminder_made_every'] == 1) { //On due date and once before and after
                    $having_clause = "DATEDIFF('" . date('Y-m-d') . "', a.to_date) = -" . $product['reminder_frequency'] . "  OR DATEDIFF('" . date('Y-m-d') . "', a.to_date) =0 OR  DATEDIFF('" . date('Y-m-d') . "', a.to_date) =" . $product['reminder_frequency'] . "";
                } elseif ($product['reminder_made_every'] == 2) { //On due date and once after
                    $having_clause = "DATEDIFF('" . date('Y-m-d') . "', a.to_date) = 0 OR DATEDIFF('" . date('Y-m-d') . "', a.to_date) =" . $product['reminder_frequency'] . " ";
                } elseif ($product['reminder_made_every'] == 3) { //On due date and daily before and after
                    $having_clause = "(DATEDIFF('" . date('Y-m-d') . "', a.to_date) >= -" . $product['reminder_frequency'] . " AND DATEDIFF('" . date('Y-m-d') . "', a.to_date) <= 0) OR (DATEDIFF('" . date('Y-m-d') . "', a.to_date) > 0 AND DATEDIFF('" . date('Y-m-d') . "', a.to_date) <=" . $product['reminder_frequency'] . " )";
                } elseif ($product['reminder_made_every'] == 4) { //On due date and daily after
                    $having_clause = "(DATEDIFF('" . date('Y-m-d') . "', a.to_date) >= 0 AND DATEDIFF('" . date('Y-m-d') . "', a.to_date) <=" . $product['reminder_frequency'] . "  )";
                }

                if ($having_clause != '') {
                    $this->send_reminder("a.fulfillment_code=1 AND b.deposit_Product_id=" . $product['id'], $having_clause);
                }
            }
        }
    }

    public function send_reminder($where_clause, $having_clause)
    {
        $this->load->model("savings_schedule_model");
        $this->load->model("organisation_model");
        $members_to_notify = $this->savings_schedule_model->get($where_clause, $having_clause);
        $emails = [];
        $tels = [];
        $data['org'] = $this->organisation_model->get(1);
        $data['branch'] = $this->organisation_model->get_org(1);
        $organisation = $data['org']['name'];
        $contact_number = $data['branch']['office_phone'];
        $pattern = "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix";

        $subject = $organisation . "- Savings reminder";
        foreach ($members_to_notify as $key => $value) {
            $client_name = ucfirst(strtolower($value['firstname']));
            $period = date('jS F, Y', strtotime($value['from_date'])) . " to " . date('jS F, Y', strtotime($value['to_date']));
            if ($value['email'] != '') {
                if (preg_match($pattern, $value['email'])) {
                    $message = "Dear " . $client_name . ",<br>" . $organisation . " kindly reminds you to remit your savings for " . $period . ".<br>Thank you for saving with us.";
                    $this->helpers->send_multiple_email(1, $value['email'], $message, $subject);
                    $emails[] = $value['email'];
                }
            }

            if ($value['mobile_number'] != '') { //"Dear ".$client_name.",
                $sms = $organisation . " kindly reminds you to remit your savings for period " . $period . ".
Thank you for saving with us. Contact " . $contact_number;
                if (preg_match("/^[\+]+[0-9]{12,12}$/", $value['mobile_number'])) {
                    $tels[] = $value['mobile_number'];
                    $this->helpers->send_sms($value['mobile_number'], $sms);
                } elseif (preg_match("/^[07]+[0-9]{9,10}$/", $value['mobile_number'])) {
                    $tels[] = '+256' . substr($value['mobile_number'], -9);
                    $this->helpers->send_sms('+256' . substr($value['mobile_number'], -9), $sms);
                }
            }
        }
    }

    public function prepare_interest_payments()
    {
        $this->load->model("fiscal_model");
        $fiscal_active = $this->fiscal_model->current_fiscal_year();
        $op_dates = $this->savings_account_model->get_min_date('account_state.state_id=7 AND sp.interestpaid=1 AND date_opened IS NOT NULL AND last_interest_cal_date IS NULL');

        if (!empty($op_dates['min_open_date'])) {
            $min_date = ($op_dates['min_open_date'] >= $fiscal_active['start_date']) ? $op_dates['min_open_date'] : $fiscal_active['start_date'];
            for ($i = $min_date; $i < date('Y-m-d'); $i = date('Y-m-d', strtotime($i . '+1 month'))) {
                // GET ALL ACTIVE SAVINGS ACCOUNT
                if ($i < date("Y-m-d")) {
                    $accounts = $this->Savings_account_model->get('account_state.state_id=7 AND sp.interestpaid=1 AND last_interest_cal_date IS NULL AND MONTH(date_opened) =' . intval(date("n", strtotime($i))) . ' AND YEAR(date_opened) =' . intval(date("Y", strtotime($i))));
                    if (!empty($accounts)) {
                        $this->calculate_interest_and_save($accounts, $i);
                    }
                }
            }
            $this->continue_interest_payments();
        } else {
            $this->continue_interest_payments();
        }
    }

    public function continue_interest_payments()
    {
        $this->load->model("fiscal_model");
        $fiscal_active = $this->fiscal_model->current_fiscal_year();
        $last_dates = $this->savings_account_model->get_min_date('account_state.state_id=7 AND sp.interestpaid=1 AND date_opened IS NOT NULL AND last_interest_cal_date IS NOT NULL');

        if (!empty($last_dates['min_last_date'])) {
            $date_considered = $last_dates['min_last_date'];
            $min_date = ($date_considered >= $fiscal_active['start_date']) ? $date_considered : $fiscal_active['start_date'];
            $max_date = (date('Y-m-d') < $fiscal_active['end_date']) ? date('Y-m-d') : $fiscal_active['end_date'];
            for ($i = $min_date; $i < $max_date; $i = date('Y-m-d', strtotime($i . '+1 month'))) {
                // GET ALL ACTIVE SAVINGS ACCOUNT
                $check_date = date('Y-m-d', strtotime($i . '+1 month'));

                if ($check_date <= date("Y-m-d")) {
                    $accounts = $this->Savings_account_model->get('account_state.state_id=7 AND sp.interestpaid=1 AND last_interest_cal_date IS NOT NULL AND MONTH(last_interest_cal_date) =' . intval(date("n", strtotime($i))) . ' AND YEAR(last_interest_cal_date) =' . intval(date("Y", strtotime($i))));
                    if (!empty($accounts)) {
                        $this->calculate_interest_and_save($accounts, $i);
                    }
                }
            }
        }
    }

    private function calculate_interest_and_save($accounts, $date_considered)
    {
        $this->load->model("Interest_payment_points_model");
        $total_interest_amount = 0;
        $end_date = $date_considered;
        $check = 0;
        foreach ($accounts as $acc) {
            //check if an account is updated
            // if ($acc['last_interest_cal_date']!=NULL && (intval(date("n",strtotime($acc['last_interest_cal_date'])))>intval(date("n",strtotime($date_considered))))) {
            //  echo "error";
            // }else{
            $check = 1;
            if ($acc['last_interest_cal_date'] != null || $acc['date_opened'] != null) {
                // GET ITS LAST PAY DATE
                $last_pay_date = $acc['last_interest_cal_date'] == null ? $acc['date_opened'] : $acc['last_interest_cal_date'];
                // COMPUTE END DATE *** We consider monthly payouts for now ***
                $interest_end_date = $this->compute_end_date($acc['last_interest_cal_date'], $acc['date_opened'], $acc['daysinyear'], $acc['wheninterestispaid']);

                if ($acc['last_interest_cal_date'] == null) {
                    $ym = date('Y-m', strtotime($last_pay_date));
                    $end_date = date("Y-m-d", strtotime($ym . "-" . $acc['wheninterestispaid']));
                } else {
                    $last_pay_date1 = date("Y-m-d", strtotime($last_pay_date . '+1 month'));
                    $ym = date('Y-m', strtotime($last_pay_date1));
                    $end_date = date("Y-m-d", strtotime($ym . "-" . $acc['wheninterestispaid']));
                }

                $this->savings_account_model->update_last_pay_date($end_date, $acc['id']);

                $diff = $this->get_date_diff($last_pay_date, $end_date);
                $last_date = $last_pay_date;
                $interest_rate = $acc['producttype'] == 2 ? $this->get_interest_rate($acc['deposit_Product_id'], $acc['term_length']) : $acc['defaultinterestrate'];
                $monthly_interest_rate = ($interest_rate / 100) / 12;
                if ($diff >= 14) {
                    $interest_amount = 0;

                    // prepare method of calculation
                    if ($acc['account_balance_for_interest_cal'] == 1) {
                        for ($i = $last_date; $i < $interest_end_date; $i = date('Y-m-d', strtotime($i . ' +1 day'))) {
                            $balance = $this->transaction_model->get_sums("account_no_id=" . $acc['id'] . " AND status_id=1 AND transaction_date <='" . $i . "'");
                            $interest_amount += ($monthly_interest_rate / 30) * $balance['cash_bal'];
                        }
                    } elseif ($acc['account_balance_for_interest_cal'] == 3) {
                        $month = date("n", strtotime($interest_end_date));
                        $deno = 12 - (intval($month) - 1);
                        $balance = $this->transaction_model->get_sums("account_no_id=" . $acc['id'] . " AND status_id=1 AND transaction_date <='" . $interest_end_date . "'");
                        $interest_amount += $monthly_interest_rate * (($deno / 12) * $balance['cash_bal']);
                    } else {
                        $balance = $this->transaction_model->get_sums("account_no_id=" . $acc['id'] . " AND status_id=1 AND transaction_date <='" . $interest_end_date . "'");
                        $interest_amount += $monthly_interest_rate * $balance['cash_bal'];
                    }

                    //post results
                    $final_interest_amount = round($interest_amount, 0);
                    if ($final_interest_amount >= 1) {
                        $data[] = array(
                            'transaction_no' => 'IL' . date('ymdhms') . mt_rand(10, 99),
                            'savings_account_id' => $acc['id'],
                            'qualifying_amount' => $balance['cash_bal'],
                            'interest_amount' => $final_interest_amount,
                            'date_calculated' => $end_date,
                            'status_id' => 2,
                            'created_by' => 1, //system
                            'date_created' => time(),
                        );
                        $total_interest_amount += $final_interest_amount;
                    }
                }
            }
        }

        if ($check == 1) {
            if (!empty($data)) {
                $this->Interest_payment_points_model->set($data);
                if ($total_interest_amount > 0) {
                    $this->computed_interest_journal_transaction($total_interest_amount, date('d-m-Y', strtotime($end_date)), $acc['interest_earned_payable_account_id'], $acc['interest_paid_expense_account_id']);
                }
            }
            $check = 0;
        }
    }

    private function get_interest_rate($product_id, $term_lenght)
    {
        $interest_rate = 0;
        $rate_ranges = $this->DepositProduct_model->get_range_rates("product_id=" . $product_id);
        foreach ($rate_ranges as $rate) {
            if ($rate['min_range'] != 0.0) {
                if ($term_lenght >= $rate['min_range'] && $term_lenght <= $rate['max_range']) {
                    $interest_rate = $rate['range_amount'];
                }
            } elseif ($rate['max_range'] == 0.0 && $rate['min_range'] != 0.0) {
                if ($term_lenght >= $rate['max_range']) {
                    $interest_rate = $rate['range_amount'];
                }
            }
        }
        return $interest_rate;
    }

    public function compute_end_date($last_date, $date_opened, $daysinyear, $whenintispaid)
    {
        //print_r($daysinyear);die();
        if ($daysinyear == 1) {
            if ($last_date == null) {
                $ym = date('Y-m', strtotime($date_opened));
                $end_date = date("Y-m-d", strtotime($ym . "-" . $whenintispaid));
            } else {
                $days = intval(date('d', strtotime($last_date)));
                if ($days == $whenintispaid) {
                    $end_date = date('Y-m-d', strtotime($last_date . ' +1 month'));
                } else {
                    $new_date = date('Y-m-d', strtotime($last_date . ' +1 month'));
                    $ym = date('Y-m', strtotime($new_date));
                    $end_date = date("Y-m-d", strtotime($ym . "-" . $whenintispaid));
                }
            }
        } else {
            if ($last_date == null) {
                $ym = date('Y-m', strtotime($date_opened));
                $end_date = date("Y-m-d", strtotime($ym . "-" . $whenintispaid));
            } else {
                $end_date = date('Y-m-d', strtotime($last_date . ' +30 days'));
            }
        }
        return $end_date;
    }

    public function get_date_diff($date11, $date22)
    {
        $date1 = date_create($date11);
        $date2 = date_create($date22);
        $diff = date_diff($date1, $date2);
        return $diff->format("%R%a");
    }

    private function computed_interest_journal_transaction($amount, $transaction_date, $payable_account, $expense_account)
    {
        $this->load->model('accounts_model');
        $this->load->model('journal_transaction_line_model');

        $transaction_data['transaction_no'] = 'IL' . date('ymdhms');
        $transaction_data['transaction_id'] = null;

        $data = [
            'ref_no' => $transaction_data['transaction_no'],
            'ref_id' => $transaction_data['transaction_id'],
            'transaction_date' => $transaction_date,
            'description' => 'Interest Payable on ' . $transaction_date,
            'status_id' => 1,
            'journal_type_id' => 30
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);

        $data[0] =
            [
                'reference_no' => $transaction_data['transaction_no'],
                'reference_id' => $transaction_data['transaction_id'],
                'transaction_date' => $transaction_date,
                'credit_amount' => $amount,
                'narrative' => strtoupper("Savings Interest Payable on " . $transaction_date . " Created by the system"),
                'account_id' => $payable_account,
                'status_id' => 1
            ];
        $data[1] = [
            'reference_no' => $transaction_data['transaction_no'],
            'reference_id' => $transaction_data['transaction_id'],
            'transaction_date' => $transaction_date,
            'debit_amount' => $amount,
            'narrative' => strtoupper("Savings Interest Expense on " . $transaction_date . " Created by the system"),
            'account_id' => $expense_account,
            'status_id' => 1
        ];

        $this->journal_transaction_line_model->set($journal_transaction_id, $data);
    }

    public function payout_saving_interest()
    {
        $this->load->model('Transaction_model');
        $this->load->model('Interest_payment_points_model');

        $payouts_array = $this->savings_account_model->get_payouts("pi.status_id=2");
        if (is_array($payouts_array) && !empty($payouts_array)) {
            foreach ($payouts_array as $key => $value) {
                $narrative = strtoupper('Savings Interest Credited -[' . date('F-Y', strtotime($value['date_calculated'])) . ' ]');
                $transaction = $this->Transaction_model->set_interest_payout($value['savings_account_id'], 2, $value['interest_amount'], $value['transaction_no'], $narrative, $value['date_calculated']);
                if (is_array($transaction) && !empty($transaction)) {
                    $this->payout_interest_journal_transaction($transaction, $value['date_calculated'], $value['account_no'], $value['member_name'], $value['interest_amount'], $value['interest_earned_payable_account_id'], $value['savings_liability_account_id']);
                    $this->Interest_payment_points_model->change_status($value['id'], 1, $value['date_calculated']);
                    $message = "Your account [" . $value['account_no'] . "] has been credited with " . number_format($value['interest_amount'], 2) . "/= .Reason: Savings Interest for the month of " . date('F-Y', strtotime($value['date_calculated'])) . "  today " . date('d-m-Y H:i:s');
                    $this->helpers->send_email($value['savings_account_id'], $message, false);
                    #check for the sms module
                    if (!empty($result = $this->miscellaneous_model->check_org_module(22, 1))) {
                        $this->helpers->notification($value['savings_account_id'], $message, false);
                    }
                }
            }
        }
    }

    private function payout_interest_journal_transaction($transaction_data, $date_calculated, $account_no, $member_name, $amount, $payable_account, $savings_liability_account)
    {
        $this->load->model('journal_transaction_line_model');
        $transaction_date = date("d-m-Y", strtotime($date_calculated));
        $data = [
            'ref_no' => $transaction_data['transaction_no'],
            'ref_id' => $transaction_data['transaction_id'],
            'transaction_date' => $transaction_date,
            'description' => strtoupper(date('F-Y', strtotime($date_calculated)) . '-Interest Payment [ ' . $member_name . ' -' . $account_no . ' ] on ' . $transaction_date),
            'status_id' => 1,
            'journal_type_id' => 31
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        $data[0] =
            [
                'reference_no' => $transaction_data['transaction_no'],
                'reference_id' => $transaction_data['transaction_id'],
                'transaction_date' => $transaction_date,
                'credit_amount' => $amount,
                'narrative' => strtoupper(date('F-Y', strtotime($date_calculated)) . '-Interest Payment [ ' . $member_name . ' -' . $account_no . ' ] on ' . $transaction_date),
                'account_id' => $savings_liability_account,
                'status_id' => 1
            ];
        $data[1] = [
            'reference_no' => $transaction_data['transaction_no'],
            'reference_id' => $transaction_data['transaction_id'],
            'transaction_date' => $transaction_date,
            'debit_amount' => $amount,
            'narrative' => strtoupper(date('F-Y', strtotime($date_calculated)) . '-Interest Payment [ ' . $member_name . ' -' . $account_no . ' ] on ' . $transaction_date),
            'account_id' => $payable_account,
            'status_id' => 1
        ];

        $this->journal_transaction_line_model->set($journal_transaction_id, $data);
    }

    public function auto_savings_penalty()
    {
        $this->load->model('Transaction_model');

        //get savings product
        $saving_products = $this->DepositProduct_model->get('mandatory_saving = 1 AND penalty = 1');

        foreach ($saving_products as $deposit_product) {
            if ($deposit_product['saving_made_every'] == 3) {
                $saving_frequency = $deposit_product['saving_frequency'] . ' months';
            } elseif ($deposit_product['saving_made_every'] == 2) {
                $saving_frequency = $deposit_product['saving_frequency'] . ' weeks';
            } elseif ($deposit_product['saving_made_every'] == 1) {
                $saving_frequency = $deposit_product['saving_frequency'] . ' days';
            }

            $min_date =  $this->miscellaneous_model->get_min_last_penalty_pay_date('state_id = 7 AND product_id=' . $deposit_product['id']);

            $start_date = $min_date['min_last_saving_penalty_pay_date'];
            $end_date = date('Y-m-d', strtotime($min_date['min_last_saving_penalty_pay_date'] . ' + ' . $saving_frequency . ' - 1 days'));

            while ($end_date < date('Y-m-d')) {
                $filter = 'deposit_Product_id=' . $deposit_product['id'] . " AND (ifnull( e.deposit ,0) ) - (ifnull( payments ,0) ) <" . "'" . $deposit_product['min_saving_amount'] . "'";
                $fliter_penalty_applicable = " transaction_date >=" .  "'" . $start_date . "'" . " AND transaction_date <=" . "'" . $end_date . "'";

                $fliter_penalty_not_applicable = " transaction_date >=" .  "'" . $start_date . "'" . " AND transaction_date <=" . "'" . $end_date . "'";

                //fetch savings_accounts without pending penalties
                $data_penalty_not_applicable = $this->Transaction_model->get_savings_accounts($fliter_penalty_not_applicable, $filter);

                //fetch savings_accounts with pending penalties
                $data_penalty_applicable = $this->Transaction_model->get_savings_accounts($fliter_penalty_applicable, $filter);

                foreach ($data_penalty_not_applicable as $savings_account) {
                    // update last_saving_penalty_pay_date
                    $update_data = array();
                    $update_data['last_saving_penalty_pay_date'] = date('Y-m-d', strtotime($end_date . ' + 1 days'));
                    $this->miscellaneous_model->update_last_penalty_pay_date($update_data, 'savings_account_id=' . "'" . $savings_account['id'] . "'");
                }
                //echo 'Penalty for missed saving between ' . $start_date . ' and ' . $end_date ;echo "<br>";
                // print_r($data_penalty_applicable); echo "<br><br><br><hr>";

                foreach ($data_penalty_applicable as $savings_account) {
                    $savings_acc_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $savings_account['id']);

                    // calculate penalty
                    if ($deposit_product['penalty_calculated_as'] == 1) {
                        $penalty = ($deposit_product['penalty_amount'] * $deposit_product['min_saving_amount']) / 100;
                    } else {
                        $penalty = $deposit_product['penalty_amount'];
                    }

                    if ($savings_acc_data['cash_bal'] > $penalty) {

                        // deduct penalty
                        $deduct = array();
                        $deduct['account_no_id'] = $savings_account['id'];
                        $deduct['amount'] = $penalty;
                        $deduct['narrative'] = 'Penalty for missed saving between ' . $start_date . ' and ' . $end_date;

                        $transaction_data = $this->Transaction_model->deduct_savings($deduct);

                        $journal_data['payment_id'] = $transaction_data['transaction_id'];
                        $journal_data['transaction_date'] = date('d-m-Y');
                        $journal_data['deposit_Product_id'] = $deposit_product['id'];
                        $journal_data['income_account_id'] = $deposit_product['penalty_income_account_id'];
                        $journal_data['amount'] = $penalty;
                        $journal_data['journal_id'] = 14;
                        $journal_data['narrative'] = $deduct['narrative'];

                        $this->automated_journal_transaction($transaction_data, $journal_data);

                        // update last_saving_penalty_pay_date
                        $update_data = array();
                        $update_data['last_saving_penalty_pay_date'] = date('Y-m-d', strtotime($end_date . ' + 1 days'));
                        $this->miscellaneous_model->update_last_penalty_pay_date($update_data, 'savings_account_id=' . "'" . $savings_acc_data['id'] . "'");
                    }
                }

                $start_date = date('Y-m-d', strtotime($end_date . ' + 1 days'));
                $end_date = date('Y-m-d', strtotime($start_date . ' + ' . $saving_frequency . ' - 1 days'));
            }
        }
    }

    ///======================Savings Account Opening Fees=====================
    public function savings_account_fees_payment()
    {
        $this->load->model("fiscal_model");

        $fiscal_active = $this->fiscal_model->current_fiscal_year();

        $defined_date = $fiscal_active['start_date'];
        //echo $defined_date;
        $create_savings = [];
        $update_date = '';
        $transaction_data = [];
        $date = '';
        $savings_product = $this->savings_product_fee_model->get('sf.status_id=1 AND chargetrigger_id=7');
        //print_r($savings_product);die();
        foreach ($savings_product as $product) {

            $savings_accs = $this->miscellaneous_model->set_data("product_id=" . $product['saving_product_id'] . " AND opening_fee_paid=0");
            //print_r($savings_accs);die();

            foreach ($savings_accs as $data) {
                $savings_accounts = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7 AND a.id=' . $data['savings_account_id']);

                $current_balance = $savings_accounts[0]['cash_bal'];

                if ($current_balance >= $product['charge_amount']) {
                    $deduction_data['amount'] = $product['charge_amount'];
                    $deduction_data['account_no_id'] = $data['savings_account_id'];
                    $deduction_data['narrative'] = 'Payment made to clear ' . $product['feename'];

                    $transaction_data = $this->transaction_model->deduct_savings($deduction_data);

                    $journal_data['payment_id'] = $savings_accounts[0]['id'];
                    $journal_data['transaction_date'] = date('d-m-Y');
                    $journal_data['deposit_Product_id'] = $product['saving_product_id'];
                    $journal_data['income_account_id'] = $product['savings_fees_income_account_id'];
                    $journal_data['amount'] = $product['charge_amount'];
                    $journal_data['journal_id'] = 13;
                    $journal_data['narrative'] = $savings_accounts[0]['member_name'] . '- ' . $deduction_data['narrative'];

                    $this->automated_journal_transaction($transaction_data, $journal_data);

                    $last_pay_data['opening_fee_paid'] = 1;
                    $last_pay_data['opening_fee_pay_date'] = date("Y-m-d");
                    $update_date = $this->miscellaneous_model->update_data($last_pay_data, $data['id']);
                }
            }
        }

        if ($update_date == true) {
            echo "Account Openning Fees Paid: " . json_encode($update_date, true);
            echo json_encode($transaction_data, true);
        } else {
            echo "No Payment has been made!";
        }
    }

    public function birthdary()
    {
        $birthday = date('Y-m-d');
        $members = $this->member_model->get_due_birthdays($birthday);
        // print_r($members);die;
        foreach ($members as $key => $value) {
            $message = "Happy Birthday " . $value['firstname'] . ", we would like to thank you for your loyalty and support. Enjoy your day!. " . $value['organisation_name'];

            $message1 = "Happy Birthday " . $value['firstname'] . ", we wish you another year of countless blessings as you celebrate your birthday!. May all your dreams come true. " . $value['organisation_name'];

            $message2 = "Happy Birthday " . $value['firstname'] . ", we realize how important today is. Birthdays come once in a year, so we celebrate you. Have an amazing year. " . $value['organisation_name'];
            $messages = array($message2, $message1, $message);
            $rand_messages = array_rand($messages, 2);
            echo $messages[$rand_messages[0]];
            die;
        }
    }

    // Annual Subscriptions  automatic deduction 

    public function membership_annual_subscriptions($feesId = FALSE)
    {
        $feesId = $this->input->post('fees_id') != "" ? $this->input->post('fees_id') : 1;

        $this->load->model("fiscal_model");
        $this->load->model('Transaction_model');
        $this->load->model('Loan_guarantor_model');
        $this->load->model('client_subscription_model');
        $fiscal_active = $this->fiscal_model->get();
        $currentYear = date('Y');

        $where = 'state_id=7';
        $memberSavingDetails = $this->Loan_guarantor_model->get_guarantor_savings2($where);

        $feesPlan = $this->member_fees_model->get('fms_member_fees.id=' . $feesId);

        foreach ($memberSavingDetails as $allActiveSavings) {
            $actualSavingBalance = $allActiveSavings['cash_bal'] != "" ? $allActiveSavings['cash_bal'] : 0;
            // scenario where fees has been paid atleast once .
            $lastSubscriptionDate = $this->client_subscription_model->get_max_subscription_date('client_id=' . $allActiveSavings['id']);

            if (!empty($lastSubscriptionDate && is_array($lastSubscriptionDate))) {
                foreach ($lastSubscriptionDate as $lastTransDate) {
                    $lastPaid = explode('-', $lastTransDate['max_transaction_date']);

                    if ($lastPaid[0] != $currentYear && $allActiveSavings['id'] != "") {
                        foreach ($feesPlan as $allFeesPlans) {
                            $autoDeductedFeeAmount = $allFeesPlans['amount'];

                            $fees_deducted = array();
                            $fees_deducted['account_no_id'] = $allActiveSavings['id'];
                            $fees_deducted['amount'] = $autoDeductedFeeAmount;
                            $fees_deducted['narrative'] = 'Auto deducted amount for ' . $allFeesPlans['feename'] . ' Annual subscription for [' . $allActiveSavings['member_name'] . '] for ' . $currentYear;

                            $transaction_data = $this->Transaction_model->deduct_savings($fees_deducted);

                            if (is_array($transaction_data)) {
                                $actionDate = date('Y-m-d');
                                $data['subscription_date'] = $actionDate;
                                $data['payment_date'] = $actionDate;
                                $data['payment_id'] = 5;
                                $data['transaction_no'] = date('ymdhms') . mt_rand(10, 99);
                                $data['narrative'] = $fees_deducted['narrative'];
                                $data['amount'] = $fees_deducted['amount'];
                                $data['client_id'] = $fees_deducted['account_no_id'];
                                $data['status_id'] = 1;
                                $data['sub_fee_paid'] = 1;
                                $data['date_created'] = time();
                                $data['created_by'] = 1;
                                $data['modified_by'] = 1;
                                $data['feeid'] = $feesId;
                                $this->client_subscription_model->set2($data);
                            }
                            $journal_data['payment_id'] = $allActiveSavings['id'];
                            $journal_data['transaction_date'] = date('d-m-Y');
                            $journal_data['deposit_Product_id'] = $allActiveSavings['deposit_Product_id'];
                            $journal_data['income_account_id'] = $allFeesPlans['income_account_id'];
                            $journal_data['amount'] = $fees_deducted['amount'];
                            $journal_data['journal_id'] = 11;
                            $journal_data['narrative'] =   $fees_deducted['narrative'] = 'Auto deducted amount for ' . $allFeesPlans['feename'] . ' Annual subscription for [' . $allActiveSavings['member_name'] . '] for ' . $currentYear;

                            $this->automated_journal_transaction($transaction_data, $journal_data);
                        }
                    }
                }
            }
            // No Autopayment run  for any annual subscription then run for the current year .

            else {
                foreach ($feesPlan as $allFeesPlans) {
                    $autoDeductedFeeAmount = $allFeesPlans['amount'];

                    $fees_deducted = array();
                    $fees_deducted['account_no_id'] = $allActiveSavings['id'];
                    $fees_deducted['amount'] = $autoDeductedFeeAmount;
                    $fees_deducted['narrative'] = 'Auto deducted amount for ' . $allFeesPlans['feename'] . ' Annual subscription for [' . $allActiveSavings['member_name'] . '] for' . $fiscal_active[0]['start_date'] . $fiscal_active[0]['end_date'];

                    $transaction_data = $this->Transaction_model->deduct_savings($fees_deducted);

                    if (is_array($transaction_data)) {
                        $actionDate = date('Y-m-d');
                        $data['subscription_date'] = $actionDate;
                        $data['payment_date'] = $actionDate;
                        $data['payment_id'] = 5;
                        $data['transaction_no'] = date('ymdhms') . mt_rand(10, 99);
                        $data['narrative'] = $fees_deducted['narrative'];
                        $data['amount'] = $fees_deducted['amount'];
                        $data['client_id'] = $fees_deducted['account_no_id'];
                        $data['status_id'] = 1;
                        $data['date_created'] = time();
                        $data['created_by'] = 1;
                        $data['modified_by'] = 1;
                        $this->client_subscription_model->set2($data);
                    }
                }
                // No Autopayment run  for any annual subscription then run for the current year .
            }
        }
    }

    //Subscription and savings fees journal entry

    private function automated_journal_transaction($transaction_data, $journal_data)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('member_model');
        if (isset($journal_data['transaction_date']) && $journal_data['transaction_date'] != '') {
            $payment_date = $journal_data['transaction_date'];
        } else {
            $payment_date = date('d-m-Y');
        }

        $data = [
            'transaction_date' => $payment_date,
            'description' => strtoupper($journal_data['narrative']),
            'ref_no' => $transaction_data['transaction_no'],
            'ref_id' => $journal_data['payment_id'],
            'status_id' => 1,
            'journal_type_id' => $journal_data['journal_id']
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        //then we prepare the journal transaction lines

        $this->load->model('accounts_model');
        $this->load->model('subscription_plan_model');
        $this->load->model('DepositProduct_model');
        $this->load->model('savings_account_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('journal_transaction_line_model');

        $debit_or_credit1 = $this->accounts_model->get_normal_side($journal_data['income_account_id'], false);

        $savings_product_details = $this->DepositProduct_model->get_products($journal_data['deposit_Product_id']);
        $debit_or_credit2 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);
        $data = [
            [
                'reference_no' => $transaction_data['transaction_no'],
                'reference_id' => $journal_data['payment_id'],
                'transaction_date' => $payment_date,
                $debit_or_credit1 => $journal_data['amount'],
                'narrative' => strtoupper($journal_data['narrative']),
                'account_id' => $journal_data['income_account_id'],
                'status_id' => 1
            ],
            [
                'reference_no' => $transaction_data['transaction_no'],
                'reference_id' => $journal_data['payment_id'],
                'transaction_date' => $payment_date,
                $debit_or_credit2 => $journal_data['amount'],
                'narrative' => strtoupper($journal_data['narrative']),
                'account_id' => $savings_product_details['savings_liability_account_id'],
                'status_id' => 1
            ]
        ];
        $this->journal_transaction_line_model->set($journal_transaction_id, $data);
    }

    public function attach_savings_accounts_to_loans()
    {
        $this->Loan_attached_saving_accounts_model->attach_savings_accounts_to_loans();
    }

    public function disburse_loan($post_data)
    {
        $this->load->model('Loan_attached_saving_accounts_model');
        $this->load->model('loan_reversal_model');
        $this->load->model('organisation_model');
        $this->load->model('client_loan_model');

        $post_data['state_id'] = 7;
        $post_data['action_date'] = date('Y-m-d');

        $_POST = $post_data;

        $this->unique_id = $unique_id = $this->generate_unique_id();
        $org = $this->organisation_model->get($_SESSION['organisation_id']);

        $old_loan_state = $this->loan_reversal_model->get_max_loan_state();
        $old_linked_loan_state = $this->loan_reversal_model->get_max_loan_state($this->input->post('linked_loan_id'));

        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] = array_column($this->data['module_list'], "module_id");
        $response['message'] = "Loan application could not be disbursed, contact IT support.";
        $response['success'] = FALSE;

        if ($org['loans_to_savings'] == 1) {
            $attached_savings_accounts = $this->Loan_attached_saving_accounts_model->get('a.loan_id=' . $this->input->post('client_loan_id'));
            // print_r($attached_savings_accounts); die;
            if (!empty($attached_savings_accounts)) {
                $response = $this->record_disburse($attached_savings_accounts[0], $unique_id);
            } else {
                $response['message'] = 'Savings account not attached to the loan';
                $this->helpers->activity_logs($_SESSION['id'], 4, "Disbursing loan", $response['message'] . " # " . $this->input->post('client_loan_id'), NULL, $this->input->post('client_loan_id'));
            }
        } else {
            //$org = $this->organisation_model->get($_SESSION['organisation_id']);

            $response = $this->record_disburse(false, $unique_id);
        }

        // Post to trans_tracking table
        $loan_data = $this->client_loan_model->get_disbursement_updated_fields($this->input->post('client_loan_id'));
        $trans_data = [
            'action_type_id' => 7,
            'loan_approval_note' => $loan_data['approval_note'],
            'loan_approved_installments' => $loan_data['approved_installments'],
            'loan_approved_repayment_frequency' => $loan_data['approved_repayment_frequency'],
            'loan_approved_repayment_made_every' => $loan_data['approved_repayment_made_every'],
            'loan_amount_approved' => $loan_data['amount_approved'],
            'loan_suggested_disbursement_date' => $loan_data['suggested_disbursement_date'],
            'loan_approval_date' => $loan_data['approval_date'],
            'loan_approved_by' => $loan_data['approved_by'],
            'loan_source_fund_account_id' => $loan_data['source_fund_account_id'],
            'loan_disbursed_amount' => $loan_data['disbursed_amount'],
            'linked_loan_id' => $this->input->post('linked_loan_id'),
            'unique_id' => $unique_id,
            'client_loan_id' => $this->input->post('client_loan_id'),
            'loan_state' => $old_loan_state,
            'linked_loan_state_id' => $old_linked_loan_state,
            'created_by' => $_SESSION['id'],
            'date_created' => date('Y-m-d h:i:s'),
            'modified_by' => $_SESSION['id'],
            'status_id' => 1
        ];
        $this->loan_reversal_model->set_trans_tracking($trans_data);


        return $response;
    }

    private function record_disburse($attached_savings_accounts = false, $unique_id = false)
    {

        $this->load->model('transaction_model');
        $this->load->model('loan_state_model');
        $this->load->model('repayment_schedule_model');
        $this->load->model('client_loan_model');
        $this->load->model('applied_loan_fee_model');
        $this->load->model('loan_approval_model');
        $this->load->model('organisation_model');
        $response = array();
        $this->db->trans_start();
        #adding loan fees for this loan disbursement
        $loan_fees_sum = 0;
        if ($this->input->post('loanFees') != NULL && $this->input->post('loanFees') != '') {

            $loanFees = $this->input->post('loanFees');
            foreach ($loanFees as $key => $value) { //it is a new entry, so we insert afresh

                if (isset($value['remove_or_not'])) {
                    unset($value['remove_or_not']);

                    $loan_fees_sum += $value['amount'];

                    $value['date_created'] = time();
                    $value['client_loan_id'] = $this->input->post('client_loan_id');
                    $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
                    $this->applied_loan_fee_model->set2($value, $unique_id);
                }
            }
            if ($attached_savings_accounts == false) {
                $this->do_journal_transaction_loan_fees($this->input->post('action_date'), $this->input->post('client_loan_id'), $unique_id);
            }
        }
        // die();
        #the transaction queries start
        $this->loan_state_model->set(false, $unique_id);

        $this->repayment_schedule_model->set(false, $unique_id);
        if ($this->input->post('steps') == 1) {

            $this->client_loan_model->approve($_POST['client_loan_id'], $unique_id);
            $this->loan_approval_model->set($_POST['client_loan_id'], $unique_id);
        }
        $this->client_loan_model->update_source_fund(false, $unique_id);

        if ($attached_savings_accounts != false) {

            if ($this->input->post('unpaid_principal') != NULL && $this->input->post('unpaid_principal') != '' && $this->input->post('unpaid_principal') != '0') {
                $principal_amount = round($this->input->post('principal_value') - $this->input->post('unpaid_principal'), 2);
            } else {
                $principal_amount = round($this->input->post('principal_value'), 2);
            }

            $deduction_data['amount'] = $principal_amount;
            $deduction_data['transaction_type_id'] = 2;
            $deduction_data['transaction_date'] = $this->input->post('action_date');
            $deduction_data['account_no_id'] = $attached_savings_accounts['saving_account_id'];
            $deduction_data['narrative'] = 'LOAN DEPOSIT';
            $transaction_data = $this->transaction_model->deduct_savings($deduction_data, $unique_id);
        }

        if ($attached_savings_accounts != false) {
            if ($this->input->post('preffered_payment_id') == 1) {
                $charge_trigger_id = array('2', '3', '4');
            } elseif ($this->input->post('preffered_payment_id') == 2) {
                $charge_trigger_id = array('2', '3', '5');
            } elseif ($this->input->post('preffered_payment_id') == 4) {
                $charge_trigger_id = array('2', '3', '6');
            } else {
                $charge_trigger_id = array('2', '3', '4', '5', '6', '8', '9', '10');
            }
            $this->helpers->deduct_charges($_POST['client_loan_id'], $charge_trigger_id, false, $this->input->post('action_date'), $unique_id);
        }
        if ($attached_savings_accounts != false) {
            $deduction_data['transaction_type_id'] = 1;
            $deduction_data['transaction_date'] = $this->input->post('action_date');
            $deduction_data['narrative'] = 'LOAN WITHDRAW';

            $org = $this->organisation_model->get($_SESSION['organisation_id']);
            if ($org['deduct_loan_fees_from_loan'] == 1) {
                $deduction_data['amount'] = $deduction_data['amount'] - $loan_fees_sum;
            }

            $transaction_data = $this->transaction_model->deduct_savings($deduction_data, $unique_id);
            $deduction_data['account_no'] = $attached_savings_accounts['account_no'];
            $this->do_journal_transaction($deduction_data, $loan_fees_sum, $attached_savings_accounts, $unique_id);
        } else {
            $this->do_journal_transaction(false, false, false, $unique_id);
        }
        //closing off the parent loan if it's a Top up loan
        if ($this->input->post('linked_loan_id') != NULL && $this->input->post('linked_loan_id') != '') {
            $filter['client_loan_id'] = $this->input->post('linked_loan_id');
            $filter['state_id'] = 14;
            $filter['comment'] = 'Loan closed due to a refinance / Top Up';
            $this->helpers->activity_logs($_SESSION['id'], 4, "Topped up loan", " # " . $this->input->post('client_loan_id'), NULL, $this->input->post('client_loan_id'));

            $this->loan_state_model->set($filter, $unique_id);
            $this->repayment_schedule_model->clear_installment($this->input->post('linked_loan_id'), 'refinance', $unique_id);
        }

        #the transaction queries end here
        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            $response['success'] = TRUE;
            $response['message'] = "Loan application successfully disbursed.";
            if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] != '') {
                $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['client_loan_id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);
                $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id =" . $_POST['group_loan_id']);
            } else {
                $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
                $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id IS NULL");

                $data['org'] = $this->organisation_model->get(1);
                $data['branch'] = $this->organisation_model->get_org(1);
                $organisation = $data['org']['name'];
                $contact_number = $data['branch']['office_phone'];

                $message = "Your loan with loan number " . $response['client_loan']['loan_no'] . " has been disbursed today on " . date('d-m-Y') . " Remember to go with a disbursement sheet.";

                $email_response = $this->helpers->send_email($this->input->post('client_loan_id'), $message);

                if (!empty($result = $this->miscellaneous_model->check_org_module(22))) {
                    $message = $message . ".
" . $organisation . ", Contact " . $contact_number;
                    $text_response = $this->helpers->notification($this->input->post('client_loan_id'), $message);
                    $response['message'] = $response['message'] . $text_response;
                }
            }
        } else {
            $this->db->trans_rollback();
        }

        return $response;
    }


    public function do_journal_transaction_loan_fees($transaction_date, $loan_id, $unique_id = false)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('accounts_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('journal_transaction_line_model');
        $this->load->model('client_loan_model');
        $this->load->model('applied_loan_fee_model');
        $client_loan = $this->client_loan_model->get_client_data($loan_id);
        $update = false;
        $membere_id = $this->input->post('member_id');
        $data = [
            'transaction_date' => $transaction_date,
            'description' => "Loan Fees Payment [ " . $client_loan['loan_no'] . " ][ " . $client_loan['member_name'] . " ]",
            'ref_no' => $client_loan['loan_no'],
            'ref_id' => $loan_id,
            'status_id' => 1,
            'journal_type_id' => 28,
            'unique_id' => $unique_id
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        //then we prepare the journal transaction lines
        $linked_account_id = $this->input->post('source_fund_account_id');

        $debit_or_credit2 = $this->accounts_model->get_normal_side($linked_account_id, false);

        $where = "a.client_loan_id=" . $loan_id . " AND a.paid_or_not=0";
        $attached_fees = $this->applied_loan_fee_model->get($where);


        foreach ($attached_fees as $fee) {
            $debit_or_credit1 = $this->accounts_model->get_normal_side($fee['income_account_id'], false);
            $data = [
                [
                    $debit_or_credit1 => $fee['amount'],
                    'transaction_date' => $transaction_date,
                    'reference_no' =>  $client_loan['loan_no'],
                    'reference_id' => $loan_id,
                    'member_id' => $membere_id,
                    'reference_key' => $client_loan['loan_no'],
                    'narrative' => 'Income received from ' . $fee['feename'] . ' on ' . $transaction_date . " [ " . $client_loan['loan_no'] . " ][ " . $client_loan['member_name'] . " ]",
                    'account_id' => $fee['income_account_id'],
                    'status_id' => 1,
                    'unique_id' => $unique_id
                ],
                [
                    $debit_or_credit2 => $fee['amount'],
                    'transaction_date' => $transaction_date,
                    'reference_no' =>  $client_loan['loan_no'],
                    'reference_id' => $loan_id,
                    'member_id' => $membere_id,
                    'reference_key' => $client_loan['loan_no'],
                    'narrative' => 'Income received from ' . $fee['feename'] . ' on ' . $transaction_date . " [ " . $client_loan['loan_no'] . " ][ " . $client_loan['member_name'] . " ]",
                    'account_id' => $linked_account_id,
                    'status_id' => 1,
                    'unique_id' => $unique_id
                ]
            ];
            if ($this->journal_transaction_line_model->set($journal_transaction_id, $data)) {
                $update = $this->applied_loan_fee_model->mark_charge_paid($fee['id'], $unique_id);
            }
        }
        if ($update == true) {
            return true;
        } else {
            return false;
        }
    }

    private function do_journal_transaction($transaction_data = false, $total_loan_fees = false, $attached_savings_accounts = false, $unique_id = false)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('loan_product_model');
        $client_loan = $this->client_loan_model->get_client_data($this->input->post('client_loan_id'));
        $membere_id = $this->input->post('member_id');
        if ($this->input->post('unpaid_principal') != NULL && $this->input->post('unpaid_principal') != '' && $this->input->post('unpaid_principal') != '0') {
            $principal_amount = round($this->input->post('principal_value') - $this->input->post('unpaid_principal'), 0);
        } else {
            $principal_amount = round($this->input->post('principal_value'), 2);
        }
        $repayment_schedules = $this->input->post('repayment_schedule');
        $interest_amount_total = 0;
        foreach ($repayment_schedules as $key => $value) {
            $interest_amount_total += $value['interest_amount'];
        }
        $interest_amount = round($interest_amount_total, 2);
        $data = [
            'transaction_date' => $this->input->post('action_date'),
            'description' =>  strtoupper("Loan Disbursement on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
            'ref_no' => $client_loan['loan_no'],
            'ref_id' => $this->input->post('client_loan_id'),
            'status_id' => 1,
            'journal_type_id' => 4,
            'unique_id' => $unique_id
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        //then we prepare the journal transaction lines
        if (!empty($client_loan)) {

            $this->load->model('accounts_model');
            $this->load->model('journal_transaction_line_model');
            $this->load->model('savings_account_model');

            $loan_product_details = $this->loan_product_model->get_accounts($client_loan['loan_product_id']);
            $Loan_account_details = $this->accounts_model->get($loan_product_details['loan_receivable_account_id']);
            $source_fund_ac_details = $this->accounts_model->get($this->input->post('source_fund_account_id'));
            $Interest_receivable_ac_details = $this->accounts_model->get($loan_product_details['interest_receivable_account_id']);
            $Interest_income_ac_details = $this->accounts_model->get($loan_product_details['interest_income_account_id']);

            $index_key = 6;
            $interest_data = $this->repayment_schedule_model->get($this->input->post('client_loan_id'));

            $debit_or_credit1 = ($Loan_account_details['normal_balance_side'] == 1) ? 'debit_amount' : 'credit_amount';
            $debit_or_credit2 = ($source_fund_ac_details['normal_balance_side'] == 1) ? 'credit_amount' : 'debit_amount'; //Although the normal balancing side is debit side, in this scenario money is being given out so we shall instead credit it.
            $debit_or_credit3 = ($Interest_income_ac_details['normal_balance_side'] == 1) ? 'debit_amount' : 'credit_amount';
            $debit_or_credit4 = ($Interest_receivable_ac_details['normal_balance_side'] == 1) ? 'debit_amount' : 'credit_amount';
            //for Top up loan purpose
            $debit_or_credit5 = ($Interest_receivable_ac_details['normal_balance_side'] == 1) ? 'credit_amount' : 'debit_amount';
            $debit_or_credit6 = ($Interest_income_ac_details['normal_balance_side'] == 1) ? 'credit_amount' : 'debit_amount';

            if (isset($transaction_data['account_no_id']) && $transaction_data['account_no_id'] != '') { //used if money passes through member savings
                $savings_product_details = $this->savings_account_model->get($transaction_data['account_no_id']);
                $debit_or_credit7 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id']);
                $debit_or_credit8 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);
            }

            if ($attached_savings_accounts && $total_loan_fees > 0) {

                $org = $this->organisation_model->get($_SESSION['organisation_id']);
                if ($org['deduct_loan_fees_from_loan'] == 1) {
                    $principal_less_fees = $principal_amount - $total_loan_fees;
                } else {
                    $principal_less_fees = $principal_amount;
                }
                $debit_or_credit2_amount = $principal_less_fees;
                $debit_or_credit8_amount = $principal_less_fees;
            } else {
                $debit_or_credit2_amount = $principal_amount;
                $debit_or_credit8_amount = $principal_amount;
            }

            $data[0] =   [
                'reference_no' =>  $client_loan['loan_no'],
                'reference_id' => $this->input->post('client_loan_id'),
                'transaction_date' => $this->input->post('action_date'),
                'member_id' => $membere_id,
                'reference_key' => $client_loan['loan_no'],
                $debit_or_credit2 => $debit_or_credit2_amount, ##
                'narrative' =>  strtoupper("Loan Disbursement on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
                'account_id' => $this->input->post('source_fund_account_id'),
                'status_id' => 1,
                'unique_id' => $unique_id
            ];

            if (isset($transaction_data['account_no_id']) && $transaction_data['account_no_id'] != '') { //used if money passes through member savings
                $data[1] =  [
                    'reference_no' => $client_loan['loan_no'],
                    'reference_id' => $this->input->post('client_loan_id'),
                    'transaction_date' => $this->input->post('action_date'),
                    'member_id' => $membere_id,
                    'reference_key' => $transaction_data['account_no'],
                    $debit_or_credit7 => $principal_amount,
                    'narrative' => strtoupper("Loan Disbursement on " . $this->input->post('action_date')),
                    'account_id' => $savings_product_details['savings_liability_account_id'],
                    'status_id' => 1,
                    'unique_id' => $unique_id
                ];
                $data[2] =  [
                    'reference_no' => $client_loan['loan_no'],
                    'reference_id' => $this->input->post('client_loan_id'),
                    'transaction_date' => $this->input->post('action_date'),
                    'member_id' => $membere_id,
                    'reference_key' => $transaction_data['account_no'],
                    $debit_or_credit8 => $debit_or_credit8_amount, ##
                    'narrative' => strtoupper("Loan Disbursement on " . $this->input->post('action_date')),
                    'account_id' => $savings_product_details['savings_liability_account_id'],
                    'status_id' => 1,
                    'unique_id' => $unique_id
                ];
            }

            $data[3] = [
                'reference_no' => $client_loan['loan_no'],
                'reference_id' => $this->input->post('client_loan_id'),
                'transaction_date' => $this->input->post('action_date'),
                'member_id' => $membere_id,
                'reference_key' => $client_loan['loan_no'],
                $debit_or_credit1 => $principal_amount,
                'narrative' =>  strtoupper("Loan Disbursement on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
                'account_id' => $loan_product_details['loan_receivable_account_id'],
                'status_id' => 1,
                'unique_id' => $unique_id
            ];

            if ($this->input->post('linked_loan_id') != NULL && $this->input->post('linked_loan_id') != '') {
                // check for unpaid interest
                $parent_loan_partial_install = $this->repayment_schedule_model->get_due_schedules('repayment_schedule.client_loan_id=' . $this->input->post('linked_loan_id') . ' AND payment_status=2');
                //print_r($parent_loan_partial_install);die;
                //if there is unpaid interest
                $LINKED_LOAN = $this->client_loan_model->get_client_data($this->input->post('linked_loan_id'));

                if (!empty($parent_loan_partial_install)) {
                    if ($parent_loan_partial_install[0]['interest_amount'] != null && !empty($parent_loan_partial_install[0]['interest_amount']) && $parent_loan_partial_install[0]['interest_amount'] != '0') {

                        $parent_loan_product_details = $this->loan_product_model->get_accounts($LINKED_LOAN['loan_product_id']);
                        $debit_or_credit10 = $this->accounts_model->get_normal_side($parent_loan_product_details['interest_receivable_account_id'], true);
                        $debit_or_credit11 = $this->accounts_model->get_normal_side($parent_loan_product_details['interest_income_account_id'], true);

                        $data[4] = [
                            'reference_no' => $parent_loan_partial_install[0]['id'],
                            'reference_id' => $parent_loan_partial_install[0]['client_loan_id'],
                            'transaction_date' => $this->input->post('action_date'),
                            'member_id' => $membere_id,
                            'reference_key' => $client_loan['loan_no'],
                            $debit_or_credit10 => $parent_loan_partial_install[0]['interest_amount'],
                            'narrative' => strtoupper("Parent Loan unpaid interest write off on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
                            'account_id' => $parent_loan_product_details['interest_receivable_account_id'],
                            'status_id' => 1,
                            'unique_id' => $unique_id
                        ];
                        $data[5] = [
                            'reference_no' => $parent_loan_partial_install[0]['id'],
                            'reference_id' => $parent_loan_partial_install[0]['client_loan_id'],
                            'transaction_date' => $this->input->post('action_date'),
                            'member_id' => $membere_id,
                            'reference_key' => $client_loan['loan_no'],
                            $debit_or_credit11 => $parent_loan_partial_install[0]['interest_amount'],
                            'narrative' => strtoupper("Parent Loan unpaid interest write off on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
                            'account_id' => $parent_loan_product_details['interest_income_account_id'],
                            'status_id' => 1,
                            'unique_id' => $unique_id
                        ];
                    }
                }
                $this->load->model('journal_transaction_line_model');
                $parent_loan = $this->repayment_schedule_model->get2('repayment_schedule.client_loan_id=' . $this->input->post('linked_loan_id') . ' AND status_id=1 AND payment_status=4');

                foreach ($parent_loan as $key => $value) {
                    $line_data['status_id'] = 3;
                    $line_data['unique_id'] = $unique_id;
                    $RRR = $this->journal_transaction_line_model->update_status_topup($line_data, $LINKED_LOAN['loan_no'], $value['id'], $value['repayment_date']);
                }
            }

            foreach ($interest_data as $key => $value) {
                $index_key += 2;
                $transaction_date = date('d-m-Y', strtotime($value['repayment_date']));
                $data[$index_key - 1] = [
                    'reference_no' => $client_loan['loan_no'],
                    'reference_id' => $value['id'],
                    'transaction_date' => $transaction_date,
                    'member_id' => $membere_id,
                    'reference_key' => $client_loan['loan_no'],
                    $debit_or_credit3 => $value['interest_amount'],
                    'narrative' => strtoupper("Interest on Loan Disbursed on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
                    'account_id' => $loan_product_details['interest_income_account_id'],
                    'status_id' => 1,
                    'unique_id' => $unique_id
                ];

                $data[$index_key] =  [
                    'reference_no' => $client_loan['loan_no'],
                    'reference_id' => $value['id'],
                    'transaction_date' => $transaction_date,
                    'member_id' => $membere_id,
                    'reference_key' => $client_loan['loan_no'],
                    $debit_or_credit4 => $value['interest_amount'],
                    'narrative' => strtoupper("Interest on Loan Disbursed on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
                    'account_id' => $loan_product_details['interest_receivable_account_id'],
                    'status_id' => 1,
                    'unique_id' => $unique_id
                ];
            }
            $this->journal_transaction_line_model->set($journal_transaction_id, $data);
        }
    }

    private function generate_unique_id()
    {
        $key = implode('-', str_split(substr(strtolower(md5(microtime() . rand(1000, 9999))), 0, 30), 6));
        $unique_id = join("", explode('-', $key));

        return $unique_id;
    }

    public function fix_interest()
    {
        $this->load->model('journal_transaction_line_model');
        $data = $this->journal_transaction_line_model->fix_interest();


        echo json_encode($data);
    }

    public function daily_penalty_calculations($days = 1, $narrative = "AUTOMATIC PENALTY APPLIED TO LOAN")
    {
        $in_arrear_loans = $this->repayment_schedule_model->get_loans_with_late_payments();

        $this->db->trans_begin();

        foreach ($in_arrear_loans as $key => $loan) {
            $loan_schedules_penalty_data = $this->loan_penalty_calcution($loan['client_loan_id'], $days);
            foreach ($loan_schedules_penalty_data as $key => $penalty_data) {
                if (isset($penalty_data['penalty_value']) && ($penalty_data['penalty_value'] > 0)) {
                    //echo json_encode($penalty_data); die;
                    $this->do_journal_transaction_daily_penalty($penalty_data, $narrative);
                }
            }
        }


        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            echo json_encode([
                'message' => 'Done recording penalties'
            ]);
        } else {
            $this->db->trans_rollback();
            echo json_encode([
                'message' => 'Failed recording penalties'
            ]);
        }
    }

    private function loan_penalty_calcution($client_loan_id, $days)
    {
        $data['data'] = $this->repayment_schedule_model->get_loan_schedule_data(" client_loan_id=$client_loan_id ");

        $loan_details = $this->client_loan_model->get_client_loan($client_loan_id);
        $penalty_applicable_after_due_date = $loan_details['penalty_applicable_after_due_date'];
        $fixed_penalty_amount = $loan_details['fixed_penalty_amount'];
        $penalty_calculation_method_id = $loan_details['penalty_calculation_method_id'];
        $last_pay_date = $loan_details['last_pay_date'];
        // $next_pay_date = $loan_details['next_pay_date'];

        foreach ($data['data'] as $key => $value) {
            $due_installments_data = $this->repayment_schedule_model->due_installments_data($value['id']);
            if (!empty($due_installments_data)) {
                $over_due_principal = $due_installments_data['due_principal'];
                if ($value['demanded_penalty'] > 0) {
                    $number_of_late_days = $due_installments_data['due_days2'];
                } else {
                    $number_of_late_days = $due_installments_data['due_days'] - $due_installments_data['grace_period_after'];
                }

                ##
                if (intval($penalty_calculation_method_id) == 1) {
                    $penalty_rate = (($due_installments_data['penalty_rate']) / 100);
                } else {
                    $penalty_rate = 1;
                }

                $number_of_late_period = $days; // $number_of_late_days; defaults to 1 since penalies will be computed daily.

                if (intval($penalty_calculation_method_id) == 2) { // Fixed amount Penalty

                    $penalty_value = ($fixed_penalty_amount * $number_of_late_period);

                    $penalty_value = $due_installments_data['penalty_rate_charged_per'] == 4 ? ($due_installments_data['paid_penalty_amount'] > 0 ? 0 : ($fixed_penalty_amount * $number_of_late_period)) : ($fixed_penalty_amount * $number_of_late_period);
                } else {
                    $penalty_value = ($over_due_principal * $number_of_late_period * $penalty_rate);

                    $penalty_value = $due_installments_data['penalty_rate_charged_per'] == 4 ? ($due_installments_data['paid_penalty_amount'] > 0 ? 0 : ($over_due_principal * $number_of_late_period * $penalty_rate)) : ($over_due_principal * $number_of_late_period * $penalty_rate);
                }


                if ((intval($penalty_applicable_after_due_date) == 1)) {

                    if ($last_pay_date >= date('Y-m-d')) {
                        $penalty_value = 0;
                    }
                }

                $data['data'][$key]['penalty_value'] = $value['demanded_penalty'] > 0 ? round($penalty_value + $value['demanded_penalty'], 0) : round($penalty_value, 0);
            } else {
                $data['data'][$key]['penalty_value'] = $value['demanded_penalty'];
            }
        }
        //echo json_encode($data); die;
        return $data['data'];
    }

    public function do_journal_transaction_daily_penalty($penalty_data, $narrative)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('journal_transaction_line_model');
        $this->load->model('client_loan_model');
        $client_loan = $this->client_loan_model->get_client_data($penalty_data['client_loan_id']);
        $data = [
            'transaction_date' => date("d-m-Y"),
            'description' => $narrative,
            'ref_no' => $client_loan['loan_no'],
            'ref_id' => $penalty_data['client_loan_id'],
            'status_id' => 1,
            'journal_type_id' => 5,
            // 'unique_id' => $unique_id
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);

        $data = [
            [
                'credit_amount' => $penalty_data['penalty_value'],
                'transaction_date' => date("d-m-Y"),
                'reference_no' =>  $client_loan['loan_no'],
                'reference_id' => $penalty_data['client_loan_id'],
                //'member_id' => $membere_id,
                'reference_key' => $client_loan['loan_no'],
                'narrative' => $narrative,
                'account_id' => 4, // Penalty Income Account
                'status_id' => 1,
                //'unique_id' => $unique_id
            ],
            [
                'debit_amount' => $penalty_data['penalty_value'],
                'transaction_date' => date("d-m-Y"),
                'reference_no' =>  $client_loan['loan_no'],
                'reference_id' => $penalty_data['client_loan_id'],
                //'member_id' => $membere_id,
                'reference_key' => $client_loan['loan_no'],
                'narrative' => $narrative,
                'account_id' => 136, // Penalty Receivable asset account
                'status_id' => 1,
                //'unique_id' => $unique_id
            ]
        ];
        $this->journal_transaction_line_model->set($journal_transaction_id, $data);
    }
}
