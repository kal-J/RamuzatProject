<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Automated_fees extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model("applied_member_fees_model");
        $this->load->model("Loan_guarantor_model");
        $this->load->model("transaction_model");
        $this->load->model("fiscal_model");
        $this->load->model("Automated_fees_model");
        $this->load->model("member_fees_model");
        $this->load->model("member_model");
        $this->load->library("helpers");
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 2, $_SESSION['staff_id']);
        $this->data['subs_list'] = $this->helpers->user_privileges(9, $_SESSION['staff_id']);
        $this->data['membership_list'] = $this->helpers->user_privileges(21, $_SESSION['staff_id']);

        $this->data['module_access'] = $this->helpers->org_access_module(2, $_SESSION['organisation_id']);
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['member_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
            $this->data['subscription_privilege'] = array_column($this->data['subs_list'], "privilege_code");
            $this->data['membership_privilege'] = array_column($this->data['membership_list'], "privilege_code");
        }
    }

    ///view membership details

    public function view($member_id)
    {

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css", "custom.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        //$this->data['detail'] = $this->Summary_report_model->get_trans($journal_type_id);

        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);

        $this->data['title'] = "Membership Report Details";
        $this->data['member_id'] = $member_id;
        $this->template->content->view('fees/reports/membership/details_view', $this->data);
        // Publish the template
        $this->template->publish();
    }


    ///view subscription details

    public function view_subscription($member_id)
    {

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css", "custom.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        //$this->data['detail'] = $this->Summary_report_model->get_trans($journal_type_id);
        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);

        $this->data['title'] = "Subscription Report Details";
        $this->data['member_id'] = $member_id;
        $this->template->content->view('fees/reports/subscription/details_view', $this->data);
        // Publish the template
        $this->template->publish();
    }


    //Membership fees schedule
    public function membership_schedule()
    {   //fiscal year
        $membership_schedule_id = [];
        $fiscal_active = $this->fiscal_model->current_fiscal_year();

        $defaulters = $this->Automated_fees_model->get(false, $fiscal_active['start_date'], 'fms_membership_schedule', 'member_fees', 'member_fee_id');

        if (empty($defaulters)) {
            $data = $this->miscellaneous_model->get_all_member_subscription_schedule();
        } else {
            $data = $this->miscellaneous_model->get_member_subscription_schedule(
                $fiscal_active['start_date'],
                $fiscal_active['end_date'],
                'fms_membership_schedule',
                'member_id'
            );
        }

        $fees_data = $this->member_fees_model->member_fees();
        foreach ($fees_data as $fees) {

            if (!empty($data)) {
                foreach ($data as $user_data) {
                    $membership_data['member_id'] = $user_data['id'];
                    $membership_data['member_fee_id'] = $fees['id'];
                    $membership_data['amount'] = $fees['amount'];
                    $membership_data['last_payment_date'] = NULL;
                    $membership_data['subscription_date'] = $fiscal_active['start_date'];
                    $membership_data['required_fee'] = $fees['requiredfee'];
                    $membership_data['state'] = 20;
                    $membership_data['created_by'] = 1;

                    $membership_schedule_id = $this->Automated_fees_model->set($membership_data, 'fms_membership_schedule');
                }
            }
        }

        if ($membership_schedule_id != '')
            echo "Successfull schedule creation. " . strval($membership_schedule_id['last_id']);
        else
            echo "No schedules were created... All members are on the list.";
    }

    //sync to update schedule with new members
    public function sync_membership_tables()
    {
        $membership_schedule_id = [];

        $fiscal_active = $this->fiscal_model->current_fiscal_year();
        $fiscal_date = $fiscal_active['start_date'];


        $schedule = $this->Automated_fees_model->get(false, false, "membership_schedule", "member_fees", "member_fee_id");

        if (empty($schedule)) {
            $schedule['subscription_date'] = $fiscal_date;
            $this->membership_schedule();
        } else {

            foreach ($schedule as $user) {

                $membership_plan = $this->member_fees_model->get($user['member_fee_id']);

                $next_pay_date['next_payment_date'] = $user['subscription_date'];

                if ($membership_plan['repayment_made_every'] == 1) {
                    $frequency = $membership_plan['repayment_frequency'];
                    $date = date('Y-m-d', strtotime(' -' . $frequency . ' days'));
                    $next_pay_date['next_payment_date'] = date('Y-m-d', strtotime($next_pay_date['next_payment_date'] . ' +' . $frequency . ' days'));
                } else if ($membership_plan['repayment_made_every'] == 2) {
                    $frequency = $membership_plan['repayment_frequency'];
                    $date = date('Y-m-d', strtotime(' -' . $frequency . ' week'));
                    $next_pay_date['next_payment_date'] = date('Y-m-d', strtotime($next_pay_date['next_payment_date'] . ' +' . $frequency . ' week'));
                } else if ($membership_plan['repayment_made_every'] == 3) {
                    $frequency = $membership_plan['repayment_frequency'];
                    $date = date('Y-m-d', strtotime(' -' . $frequency . ' months'));
                    $next_pay_date['next_payment_date'] = date('Y-m-d', strtotime($next_pay_date['next_payment_date'] . ' +' . $frequency . ' months'));
                }

                if ($user['subscription_date'] < $next_pay_date['next_payment_date']) {

                    $membership_data['member_id'] = $user['member_id'];
                    $membership_data['member_fee_id'] = $membership_plan['id'];
                    $membership_data['amount'] = $membership_plan['amount'];
                    $membership_data['subscription_date'] = $next_pay_date['next_payment_date'];
                    $membership_data['required_fee'] = $membership_plan['requiredfee'];
                    $membership_data['state'] = 20;
                    $membership_data['created_by'] = 1;

                    //populating the membership schedule

                    $membership_schedule_id = $this->Automated_fees_model->set($membership_data, 'fms_membership_schedule');
                }
            }
        }

        if ($membership_schedule_id != null)

            echo "Schedule created successfully " . strval($membership_schedule_id['last_id']);

        else
            echo "No clients to sync ";
    }


    //auto membership
    public function auto_membership_payment()
    {
        $fiscal_active = $this->fiscal_model->current_fiscal_year();

        $defaulters = $this->Automated_fees_model->get(false, $fiscal_active['start_date'], 'fms_membership_schedule', 'member_fees', 'member_fee_id');

        $membership_plan = $this->member_fees_model->get();

        foreach ($defaulters as $defaulter) {
            $user_savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7 AND a.member_id=' . $defaulter['member_id']);

            foreach ($membership_plan as $plan) {
                if ($defaulter['member_fee_id'] == $plan['id'] && $defaulter['state'] == 20) {
                    foreach ($user_savings_data as $savings) {
                        $current_balance = $savings['cash_bal'];
                        if ($current_balance >= $defaulter['amount']) {
                            $deduction_data['amount'] = $defaulter['amount'];
                            $deduction_data['account_no_id'] = $savings['id'];
                            $deduction_data['narrative'] = 'Automatic deduction payment made to clear your ' . ucfirst($this->member_fees_model->get($plan['id'])['feename']) . ' Subscription';

                            $membership_data['member_id'] = $defaulter['id'];
                            $membership_data['payment_date'] = date('Y-m-d H:i:s');
                            $membership_data['payment_id'] = 5;
                            $membership_data['transaction_no'] = date('ymdhms') . mt_rand(100, 999);;
                            $membership_data['member_fee_id'] = $plan['id'];
                            $membership_data['fee_paid'] = 1;
                            $membership_data['amount'] = $plan['amount'];
                            $membership_data['requiredfee'] = $plan['requiredfee'];
                            $membership_data['created_by'] = 1;

                            $transaction_data = $this->transaction_model->deduct_savings($deduction_data);

                            $pay_membership = $this->applied_member_fees_model->set($membership_data);
                            $this->Automated_fees_model->update_schedule($membership_data);

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
    }

    public function jsonList()
    {
        $state = $this->input->post('state_id');
        $data['data'] = $this->Automated_fees_model->get(false, false, "membership_schedule", "member_fees", "member_fee_id");
        echo json_encode($data);
    }

    public function jsonList2()
    {
        $data['data'] = $this->Automated_fees_model->get_subscription("state=20", false, "subscription_schedule", "subscription_plan", "subscription_fee_id");
        echo json_encode($data);
    }

    public function user_memberhip()
    {
        $data['data'] = $this->Automated_fees_model->get_details("state=20", "membership_schedule", "member_fees", "member_fee_id");
        echo json_encode($data);
    }

    public function user_subscription()
    {
        $data['data'] = $this->Automated_fees_model->get_details("state=20", "subscription_schedule", "subscription_plan", "subscription_fee_id");
        echo json_encode($data);
    }

    public function get_attached_fees()
    {
        $id = $this->input->post('id');
        $member_fee_id = $this->input->post('member_fee_id');
        $data['data'] = $this->Automated_fees_model->get_paid_details("cs.member_id=$id AND cs.member_fee_id=$member_fee_id");
        echo json_encode($data);
    }


    public function get_member_schedules()
    {
        $id = $this->input->post('id');
        $data['data'] = $this->Automated_fees_model->get_details("state=5 AND a.member_id=$id", "membership_schedule", "member_fees", "member_fee_id");
        echo json_encode($data);
    }

    public function getSummary()
    {
        $state = $this->input->post('state_id');
        $data['data'] = $this->Automated_fees_model->get_summary("20");
        if ($state == 1) {
            $data['data'] = $this->Automated_fees_model->get_summary("20");
        } else if ($state == 2) {
            $data['data'] = $this->Automated_fees_model->get_summary("21");
        } else if ($state == 3) {
            $data['data'] = $this->Automated_fees_model->get_summary("9");
        }

        echo json_encode($data);
    }


    public function getSubSummary()
    {
        $state = $this->input->post('state_id');
        $data['data'] = $this->Automated_fees_model->get_sub_summary("20");
        if ($state == 1) {
            $data['data'] = $this->Automated_fees_model->get_sub_summary("20");
        } else if ($state == 2) {
            $data['data'] = $this->Automated_fees_model->get_sub_summary("21");
        } else if ($state == 3) {
            $data['data'] = $this->Automated_fees_model->get_sub_summary("9");
        }

        echo json_encode($data);
    }


    //Subscription journal entry
    private function automated_journal_transaction($transaction_data, $journal_data)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('member_model');
        $payment_date = date('d-m-Y');

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
}
