<?php
defined('BASEPATH') or exit('No direct script access allowed');

use GuzzleHttp\Client;

class Dashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->data['savings_list'] = $this->helpers->user_privileges($module_id = 6, $_SESSION['staff_id']);
        $this->data['fiscal_list'] = $this->helpers->user_privileges($module_id = 20, $_SESSION['staff_id']);
        $this->data['client_loan_list'] = $this->helpers->user_privileges($module_id = 4, $_SESSION['staff_id']);
        $this->data['privilege_list'] = $this->helpers->user_privileges(2, $_SESSION['staff_id']);

        $this->load->library("helpers");
        $this->load->model("Dashboard_model");
        $this->load->model('reports_model');
        $this->load->model('Fiscal_month_model');
        $this->load->model('Repayment_schedule_model');
        $this->load->model('Loan_installment_payment_model');
        $this->load->model('Sms_model');
        $this->load->model("miscellaneous_model");
        $this->load->model("Transaction_date_control_model");

        if (is_array($this->data['fiscal_list'])) {

            $this->data['fiscal_privilege'] = array_column($this->data['fiscal_list'], "privilege_code");
        } else {
            $this->data['fiscal_privilege'] = 0;
        }

        if (is_array($this->data['client_loan_list'])) {
            $this->data['client_loan_privilege'] = array_column($this->data['client_loan_list'], "privilege_code");
        } else {
            $this->data['client_loan_privilege'] = 0;
        }

        if (is_array($this->data['privilege_list'])) {
            $this->data['member_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        } else {
            $this->data['member_privilege'] = 0;
        }
        if (is_array($this->data['savings_list'])) {
            $this->data['savings_privilege'] = array_column($this->data['savings_list'], "privilege_code");
        } else {
            $this->data['savings_privilege'] = 0;
        }

        $this->data['sorted_users'] = $this->Dashboard_model->get_all_system_users('status=1');
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['member_referral'] = isset($this->data['org']['member_referral']) ? $this->data['org']['member_referral'] : null;
        $this->data['allowed_transaction_dates'] = $this->Transaction_date_control_model->generate_allowed_dates();


        $this->data['lock_month_access'] = $this->helpers->org_access_module($module_id = 23, $_SESSION['organisation_id']);
        if (!empty($this->data['lock_month_access'])) {
            $this->data['active_month'] = $this->Fiscal_month_model->get_active_month();
        }
    }

    public function index()
    {
        $neededcss = array("fieldset.css", "plugins/highcharts/code/css/highslide.css", "plugins/daterangepicker/daterangepicker-bs3.css", "plugins/steps/jquery.steps.css", "plugins/select2/select2.min.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/highcharts/code/highcharts.js", "plugins/highcharts/code/highcharts-3d.js", "plugins/highcharts/code/modules/exporting.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/modules/export-data.js", "plugins/highcharts/code/modules/series-label.js", "plugins/steps/jquery.steps.min.js", "plugins/select2/select2.full.min.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->load->library("num_format_helper");
        $this->load->model('Staff_model');
        $this->load->model('subscription_plan_model');
        $this->load->model('Loan_product_model');
        $this->load->model('penalty_calculation_method_model');
        $this->load->model('loan_fees_model');
        $this->load->model('member_model');
        $this->load->model('loan_collateral_model');
        $this->load->model('member_collateral_model');
        $this->load->model('loan_product_fee_model');
        $this->load->model('loan_guarantor_model');
        $this->load->model('user_income_type_model');
        $this->load->model('user_expense_type_model');
        $this->load->model('loan_doc_type_model');
        $this->load->model('TransactionChannel_model');
        $this->load->model('saving_fees_model');
        $this->load->model('savings_account_model');
        $this->load->model('accounts_model');
        $this->load->model('shares_model');
        $this->load->model('Branch_model');

        $this->data['months'] = $this->miscellaneous_model->get_months();
        $this->data['title'] = $this->data['sub_title'] = "Dashboard";
        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] = array_column($this->data['module_list'], "module_id");
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['branch_list'] = $this->Branch_model->get();

        // client creation
        $this->data['subscription_plans'] = $this->subscription_plan_model->get("subscription_plan.organisation_id = " . $_SESSION['organisation_id']);
        $this->data['new_client_no'] = $this->num_format_helper->new_client_no();
        $this->data['marital_statuses'] = $this->miscellaneous_model->get_marital_status_options();
        $this->data['staff_list'] = $this->Staff_model->get_registeredby("status_id=1");
        $this->data['pay_with'] = $this->accounts_model->get_pay_with("10");
        // end of client creation

        //==================== START LOAN DATA ==================== 
        $this->data['type'] = $this->data['sub_type'] = 'client_loan';
        $this->data['case2'] = 'client_loan';
        $this->data['staffs'] = $this->Staff_model->get_registeredby("status_id=1");
        $this->data['loanProducts'] = $this->Loan_product_model->get_product("loan_product.status_id=1 AND loan_product.available_to_id=3 OR loan_product.available_to_id=1");
        $this->data['members'] = $this->member_model->get_member_by_user_id("fms_member.status_id=1");
        $this->data['penalty_calculation_method'] = $this->penalty_calculation_method_model->get();
        $this->data['repayment_made_every'] = $this->miscellaneous_model->get();
        $this->data['marital_statuses'] = $this->miscellaneous_model->get_marital_status_options();
        $this->data['new_loan_acc_no'] = $this->num_format_helper->new_loan_acc_no();
        $this->data['all_collaterals'] = $this->member_collateral_model->get_not_attached_to_active_loan('status_id=1');
        $this->data['collateral_types'] = $this->loan_collateral_model->get_collateral_type();
        $this->data['available_loan_fees'] = $this->loan_product_fee_model->get();
        $this->data['loan_doc_types'] = $this->loan_doc_type_model->get();
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 3');
        $this->data['relationship_types'] = $this->miscellaneous_model->get_relationship_type();
        $this->data['guarantors'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + 
        ifnull( transfer ,0) +ifnull(charges, 0) + ifnull( amount_locked, 0) ) >= 0 and j.state_id = 7 AND a.client_type=1");


        $this->data['share_guarantors'] = $this->shares_model->get("share_state.state_id = 7");

        $this->data['share_accs'] = $this->shares_model->get("share_state.state_id = 7");


        $this->data['savings_accs'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0) +ifnull(charges, 0)+ ifnull( amount_locked, 0) ) > 0 and j.state_id = 7 AND a.client_type=1");
        $this->data['income_items'] = $this->user_income_type_model->get();
        $this->data['expense_items'] = $this->user_expense_type_model->get();

        $this->data['available_loan_range_fees'] = $this->loan_fees_model->get_range_fees();
        //==================== END LOAN DATA==================== 

        //==================== START SAVINGS DATA ==================== 
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id IN(1,2,4,6,7,8)');
        $this->data['tchannel'] = $this->TransactionChannel_model->get();
        $this->data['available_savings_range_fees'] = $this->saving_fees_model->get_range_fees();
        $this->data['access_side'] = 'Dashboard';
        $this->data['saving_accounts'] = $this->savings_account_model->get_savings_account();


        // print_r($this->data['saving_accounts']);die;
        //==================== END SAVINGS DATA==================== 

        $this->template->title = $this->data['title'];
        $this->data['fiscal_new'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 2);
        $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['fiscal_all'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], $status = FALSE);
        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['sms_module'] = $this->miscellaneous_model->check_org_module(22);
        if ($_SESSION['role_id'] == 4) {
            $this->template->content->view('dashboard/credit_officer_dash', $this->data);
        } else {
            $this->template->content->view('dashboard/home', $this->data);
        }
        // Publish the template
        $this->template->publish();
    }

    public function test()
    {
        $data['amount_paid'] = $this->Loan_installment_payment_model->loarn_sums();
        echo json_encode($data);
    }

    public function get_dashboard_figures()
    {
        $data = $this->Dashboard_model->get_dashboard_figures();
        echo json_encode(
            $data
        );
    }

    public function compute_dashboard_figures()
    {
        $fiscal_year = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $_POST['start_date'] = $fiscal_year['start_date'];
        $_POST['end_date'] = date('Y-m-d');

        $data = [];
        $data['ajax_data'] = $this->compute_ajax_data();
        $data['indicators_data'] = $this->compute_indicators_data();

        $inserted_id = $this->Dashboard_model->save_dashboard_figures(['data' => json_encode($data), 'created_by' => 1]);
        if ($inserted_id) {
            http_response_code(200);
            echo json_encode([
                'success' => true
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false
            ]);
        }
    }

    public function compute_indicators_data()
    {
        $fiscal_year = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $start_date = $fiscal_year['start_date'];
        $end_date = date('Y-m-d');
        /* ========================== Income vs Expenses =================================== */
        $data['income_expense'] = $this->get_line_graph_data($start_date, $end_date);
        $between_interest = "(repayment_date BETWEEN '" . ($start_date) . "' AND '" . ($end_date) . "')";
        $between_install = "(payment_date BETWEEN '" . ($start_date) . "' AND '" . ($end_date) . "')";
        $sums['savings_totals'] = $this->Dashboard_model->client_savings_sums();
        $sums['share_totals'] = $this->Dashboard_model->client_share_sums();

        $sums['total_assets'] = $this->reports_model->get_category_sums(1, FALSE);
        $data['projected_interest_amount'] = $this->Repayment_schedule_model->sum_interest_principal_report($between_interest, "state_id IN (7,13)");
        $data['interest_amount_in_suspence'] = $this->Repayment_schedule_model->sum_interest_principal_report($between_interest, "state_id IN (13)");
        $data['amount_disbursed'] = $this->Repayment_schedule_model->sum_interest_principal_report($between_interest, "state_id IN (7,8,9,10,12,13,14) AND payment_status <> 5");
        //used for other dashboard reports and 
        $data['amount_paid'] = $this->Loan_installment_payment_model->sum_paid_installment($between_install . " AND state_id IN (7,13)");
        $data['amount_paid_13'] = $this->Loan_installment_payment_model->sum_paid_installment($between_install . " AND state_id IN (13)");
        $data['principal_disbursed'] = $data['amount_disbursed']['principal_sum'];
        $data['projected_intrest_earnings'] = abs($data['projected_interest_amount']['interest_sum']) - abs($data['amount_paid']['already_interest_amount']);
        $data['intrest_in_suspense'] = abs($data['interest_amount_in_suspence']['interest_sum']) - abs($data['amount_paid_13']['already_interest_amount']);
        $data['change_in_Portfolio'] =  abs($sums['total_assets']['amount']) - $data['amount_disbursed']['principal_sum'] - $data['amount_paid']['already_principal_amount'];
        $data['deposits_sum'] = abs($sums['savings_totals']['total_credit']);
        $data['withdraw_sum'] = abs($sums['savings_totals']['total_debit']);
        $data['savings_sums'] = abs($sums['savings_totals']['total_credit']) - abs($sums['savings_totals']['total_debit']);


        $data['share_deposits_sum'] = abs($sums['share_totals']['total_share_credit']);
        $data['share_withdraw_sum'] = abs($sums['share_totals']['total_share_debit']);
        $data['share_sums'] = abs($sums['share_totals']['total_share_credit']) - abs($sums['share_totals']['total_share_debit']);

        // for active or in arrears
        $data['amt_disbursed_active_arrears'] = $this->Repayment_schedule_model->sum_interest_principal_report($between_interest, "state_id IN (7,13)");
        $data['amt_paid_active_arrears'] = $this->Loan_installment_payment_model->sum_paid_installment("state_id IN (7,13)");
        $data['gross_loan_portfolio'] = $data['amt_disbursed_active_arrears']['principal_sum'] - $data['amt_paid_active_arrears']['already_principal_amount'];

        // total principal balance
        $data['amt_disbursed_for_principal_balance'] = $this->Repayment_schedule_model->sum_interest_principal_report($between_interest, "state_id =13");
        $data['amt_paid_for_principal_balance'] = $this->Loan_installment_payment_model->sum_paid_installment("state_id =13");
        $data['total_principal_balance'] = $data['amt_disbursed_for_principal_balance']['principal_sum'] - $data['amt_paid_for_principal_balance']['already_principal_amount'];
        if (($data['total_principal_balance'] || $data['gross_loan_portfolio']) != 0) {
            $data['portfolio_at_risk'] = (abs($data['total_principal_balance']) / abs($data['gross_loan_portfolio'])) * 100;
            $data['value_at_risk'] = $data['total_principal_balance'];
        } else {
            $data['portfolio_at_risk'] = 0;
        }

        // for extra ordinary writeoff
        $data['amt_disbursed_written_off'] = $this->Repayment_schedule_model->sum_interest_principal_report($between_interest, "state_id =8");
        $data['amt_paid_written_off'] = $this->Loan_installment_payment_model->sum_paid_installment("state_id =8");
        $data['extraordinary_writeoff'] = $data['amt_disbursed_written_off']['principal_sum'] - $data['amt_paid_written_off']['already_principal_amount'];
        $data['penalty_total'] = $this->penalty_calculation($between_interest);

        $data['loan_sms_total'] = $this->Sms_model->get_totals("sms.message_type='Loan'");
        $data['savings_sms_total'] = $this->Sms_model->get_totals("sms.message_type='Savings'");
        $data['total_sms'] = $this->Sms_model->get_totals();

        return $data;
    }
    public function get_indicators_data()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        /* ========================== Income vs Expenses =================================== */
        $data['income_expense'] = $this->get_line_graph_data($start_date, $end_date);
        $between_interest = "(repayment_date BETWEEN '" . ($start_date) . "' AND '" . ($end_date) . "')";
        $between_install = "(payment_date BETWEEN '" . ($start_date) . "' AND '" . ($end_date) . "')";
        $sums['savings_totals'] = $this->Dashboard_model->client_savings_sums();
        $sums['share_totals'] = $this->Dashboard_model->client_share_sums();

        $sums['total_assets'] = $this->reports_model->get_category_sums(1, FALSE);
        $data['projected_interest_amount'] = $this->Repayment_schedule_model->sum_interest_principal_report($between_interest, "state_id IN (7,13)");
        $data['interest_amount_in_suspence'] = $this->Repayment_schedule_model->sum_interest_principal_report($between_interest, "state_id IN (13)");
        $data['amount_disbursed'] = $this->Repayment_schedule_model->sum_interest_principal_report($between_interest, "state_id IN (7,8,9,10,12,13,14) AND payment_status <> 5");
        //used for other dashboard reports and 
        $data['amount_paid'] = $this->Loan_installment_payment_model->sum_paid_installment($between_install . " AND state_id IN (7,13)");
        $data['amount_paid_13'] = $this->Loan_installment_payment_model->sum_paid_installment($between_install . " AND state_id IN (13)");
        $data['principal_disbursed'] = $data['amount_disbursed']['principal_sum'];
        $data['projected_intrest_earnings'] = abs($data['projected_interest_amount']['interest_sum']) - abs($data['amount_paid']['already_interest_amount']);
        $data['intrest_in_suspense'] = abs($data['interest_amount_in_suspence']['interest_sum']) - abs($data['amount_paid_13']['already_interest_amount']);
        $data['change_in_Portfolio'] =  abs($sums['total_assets']['amount']) - $data['amount_disbursed']['principal_sum'] - $data['amount_paid']['already_principal_amount'];
        $data['deposits_sum'] = abs($sums['savings_totals']['total_credit']);
        $data['withdraw_sum'] = abs($sums['savings_totals']['total_debit']);
        $data['savings_sums'] = abs($sums['savings_totals']['total_credit']) - abs($sums['savings_totals']['total_debit']);


        $data['share_deposits_sum'] = abs($sums['share_totals']['total_share_credit']);
        $data['share_withdraw_sum'] = abs($sums['share_totals']['total_share_debit']);
        $data['share_sums'] = abs($sums['share_totals']['total_share_credit']) - abs($sums['share_totals']['total_share_debit']);

        // for active or in arrears
        $data['amt_disbursed_active_arrears'] = $this->Repayment_schedule_model->sum_interest_principal_report($between_interest, "state_id IN (7,13)");
        $data['amt_paid_active_arrears'] = $this->Loan_installment_payment_model->sum_paid_installment("state_id IN (7,13)");
        $data['gross_loan_portfolio'] = $data['amt_disbursed_active_arrears']['principal_sum'] - $data['amt_paid_active_arrears']['already_principal_amount'];

        // total principal balance
        $data['amt_disbursed_for_principal_balance'] = $this->Repayment_schedule_model->sum_interest_principal_report($between_interest, "state_id =13");
        $data['amt_paid_for_principal_balance'] = $this->Loan_installment_payment_model->sum_paid_installment("state_id =13");
        $data['total_principal_balance'] = $data['amt_disbursed_for_principal_balance']['principal_sum'] - $data['amt_paid_for_principal_balance']['already_principal_amount'];
        if (($data['total_principal_balance'] || $data['gross_loan_portfolio']) != 0) {
            $data['portfolio_at_risk'] = (abs($data['total_principal_balance']) / abs($data['gross_loan_portfolio'])) * 100;
            $data['value_at_risk'] = $data['total_principal_balance'];
        } else {
            $data['portfolio_at_risk'] = 0;
        }

        // for extra ordinary writeoff
        $data['amt_disbursed_written_off'] = $this->Repayment_schedule_model->sum_interest_principal_report($between_interest, "state_id =8");
        $data['amt_paid_written_off'] = $this->Loan_installment_payment_model->sum_paid_installment("state_id =8");
        $data['extraordinary_writeoff'] = $data['amt_disbursed_written_off']['principal_sum'] - $data['amt_paid_written_off']['already_principal_amount'];
        $data['penalty_total'] = $this->penalty_calculation($between_interest);

        $data['loan_sms_total'] = $this->Sms_model->get_totals("sms.message_type='Loan'");
        $data['savings_sms_total'] = $this->Sms_model->get_totals("sms.message_type='Savings'");
        $data['total_sms'] = $this->Sms_model->get_totals();

        echo json_encode($data);
    }
    //penalty calculation
    private function penalty_calculation($between_interest)
    {
        $penalty_total = 0;
        $due_installments_data = $this->Repayment_schedule_model->due_installments_data($between_interest);
        foreach ($due_installments_data as $key => $value) {
            $over_due_principal = $value['due_principal'];
            $number_of_late_days = $value['due_days'] - $value['grace_period_after'];
            $penalty_rate = (($value['penalty_rate']) / 100);
            $penalty_value = ($over_due_principal * $number_of_late_days * $penalty_rate) - $value['paid_penalty_amount'];
            $penalty_total += $penalty_value;
        }
        $penalty_data['penalty_total'] = $penalty_total;
        return $penalty_data;
    }

    private function get_line_graph_data($start_date, $end_date)
    {
        $graph_data['yAxis']['title']['text'] = "UGX";
        $period_dates = [];

        if (($graph_periods = $this->helpers->get_graph_periods($end_date, $start_date)) !== TRUE) {
            $graph_data['title']['text'] = "Income vs Expenses (" . $graph_periods['date_range'] . ")";
            $graph_data['xAxis'] = $graph_periods['xAxis'];
            $period_dates = $graph_periods['period_dates'];
        }
        if (!empty($period_dates)) {
            //we get the data for the specified periods
            //1. for the income
            $graph_data['datasets'][] = $this->get_period_acc_sums($period_dates, 4);
            //2. for the expenses
            $graph_data['datasets'][] = $this->get_period_acc_sums($period_dates, 5);
        }
        if (!empty($graph_data)) {
            return $graph_data;
        } else {
            return false;
        }
    }

    private function get_period_acc_sums($period_dates, $category_id)
    {

        $categories = [
            4 => ['name' => "Income", 'color' => "#019123", "marker" => ["symbol" => "square"]],
            5 => ['name' => "Expenses", 'color' => "#e50202", "marker" => ["symbol" => "diamond"]]
        ];
        $datasets = $categories[$category_id];
        foreach ($period_dates as $period_date) {
            if (isset($period_date['start_obj']) && isset($period_date['end_obj'])) {
                if (is_object($period_date['start_obj']) && is_object($period_date['end_obj'])) {
                    $start_obj = $period_date['start_obj'];
                    $end_obj = $period_date['end_obj'];
                    $between = "( jtl.transaction_date BETWEEN '" . ($start_obj->format('Y-m-d')) . "' AND '" . ($end_obj->format('Y-m-d')) . "')";
                    $category_sum = $this->reports_model->get_account_sums_highcharts($between . " AND  `category_id`=$category_id", TRUE);
                    $datasets['data'][] = empty($category_sum) ? 0 : ((int)$category_sum[0]['amount']);
                }
            }
        }
        return $datasets;
    }

    public function compute_ajax_data()
    {
        $this->load->model('member_model');
        $this->load->model('client_loan_model');
        $this->load->model('savings_account_model');
        $between = "( loan_state.action_date BETWEEN '" . ($this->input->post('start_date')) . "' AND '" . ($this->input->post('end_date')) . "')";

        $data['client_count_active'] = $this->member_model->get_count("status_id=1");
        $data['client_count_inactive'] = $this->member_model->get_count("status_id=2");

        // loans 
        $data['savings_count'] = $this->savings_account_model->get_count("account_state.state_id=7");
        $data['loan_count_active'] = $this->client_loan_model->get_sum_count("loan_state.state_id=7 AND $between");
        $data['loan_count_writeoff'] = $this->client_loan_model->get_sum_count("loan_state.state_id=8 AND $between");
        $data['loan_count_pend_approval'] = $this->client_loan_model->get_sum_count("loan_state.state_id=5 AND $between");
        $data['loan_count_approved'] = $this->client_loan_model->get_sum_count("loan_state.state_id=6 AND $between");
        $data['loan_count_arrias'] = $this->client_loan_model->get_sum_count("loan_state.state_id=13 AND $between");
        $data['loan_count_partial'] = $this->client_loan_model->get_sum_count("loan_state.state_id=1 AND $between");
        $data['loan_count_locked'] = $this->client_loan_model->get_sum_count("loan_state.state_id=12 AND $between");
        $data['loan_count_pend_payments'] = $this->client_loan_model->get_sum_count("loan_state.state_id=20 AND $between");

        $loan_state_totals = $this->client_loan_model->state_totals();
        $data['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id IS NULL");
        $data['loans_dataset'] = [];
        $states = ['Partial', 'Rejected', 'Canceled', 'Withdrawn', 'Pending', 'Approved', 'Active', 'Written Off', 'Paid Off', 'Obligations met', 'Rescheduled', 'Locked', 'In arrears', 'Refinanced', 'Closed', 'Matured', 'Dormant', 'Deleted', 'Deactivated', 'Pending Payment'];
        foreach ($loan_state_totals as $key => $value) {
            $data['loans_dataset']['xAxis'][] = $states[$value['state_id'] - 1];
            $data['loans_dataset']['yAxis'][] = abs($value['number']);
        }


        $sums['savings_totals'] = $this->Dashboard_model->client_savings_sums();

        $savings_data['deposits_sum'] = abs(($sums['savings_totals']['total_credit'] != 0) ? $sums['savings_totals']['total_credit'] : 1);
        $savings_data['withdraw_sum'] = abs($sums['savings_totals']['total_debit']);
        $savings_data['savings_sums'] = abs($sums['savings_totals']['total_credit']) - abs($sums['savings_totals']['total_debit']);
        $data['savings_dataset']['deposits_percentage'] = ($savings_data['savings_sums'] / $savings_data['deposits_sum']) * 100;
        $data['savings_dataset']['withdraw_percentage'] = ($savings_data['withdraw_sum'] / $savings_data['deposits_sum']) * 100;

        return $data;
    }
    public function ajax_data()
    {
        $this->load->model('member_model');
        $this->load->model('client_loan_model');
        $this->load->model('savings_account_model');
        $between = "( loan_state.action_date BETWEEN '" . ($this->input->post('start_date')) . "' AND '" . ($this->input->post('end_date')) . "')";

        $data['client_count_active'] = $this->member_model->get_count("status_id=1");
        $data['client_count_inactive'] = $this->member_model->get_count("status_id=2");

        // loans 
        $data['savings_count'] = $this->savings_account_model->get_count("account_state.state_id=7");
        $data['loan_count_active'] = $this->client_loan_model->get_sum_count("loan_state.state_id=7 AND $between");
        $data['loan_count_writeoff'] = $this->client_loan_model->get_sum_count("loan_state.state_id=8 AND $between");
        $data['loan_count_pend_approval'] = $this->client_loan_model->get_sum_count("loan_state.state_id=5 AND $between");
        $data['loan_count_approved'] = $this->client_loan_model->get_sum_count("loan_state.state_id=6 AND $between");
        $data['loan_count_arrias'] = $this->client_loan_model->get_sum_count("loan_state.state_id=13 AND $between");
        $data['loan_count_partial'] = $this->client_loan_model->get_sum_count("loan_state.state_id=1 AND $between");
        $data['loan_count_locked'] = $this->client_loan_model->get_sum_count("loan_state.state_id=12 AND $between");
        $data['loan_count_pend_payments'] = $this->client_loan_model->get_sum_count("loan_state.state_id=20 AND $between");

        $loan_state_totals = $this->client_loan_model->state_totals();
        $data['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id IS NULL");
        $data['loans_dataset'] = [];
        $states = ['Partial', 'Rejected', 'Canceled', 'Withdrawn', 'Pending', 'Approved', 'Active', 'Written Off', 'Paid Off', 'Obligations met', 'Rescheduled', 'Locked', 'In arrears', 'Refinanced', 'Closed', 'Matured', 'Dormant', 'Deleted', 'Deactivated', 'Pending Payment'];
        foreach ($loan_state_totals as $key => $value) {
            $data['loans_dataset']['xAxis'][] = $states[$value['state_id'] - 1];
            $data['loans_dataset']['yAxis'][] = abs($value['number']);
        }


        $sums['savings_totals'] = $this->Dashboard_model->client_savings_sums();

        $savings_data['deposits_sum'] = abs(($sums['savings_totals']['total_credit'] != 0) ? $sums['savings_totals']['total_credit'] : 1);
        $savings_data['withdraw_sum'] = abs($sums['savings_totals']['total_debit']);
        $savings_data['savings_sums'] = abs($sums['savings_totals']['total_credit']) - abs($sums['savings_totals']['total_debit']);
        $data['savings_dataset']['deposits_percentage'] = ($savings_data['savings_sums'] / $savings_data['deposits_sum']) * 100;
        $data['savings_dataset']['withdraw_percentage'] = ($savings_data['withdraw_sum'] / $savings_data['deposits_sum']) * 100;

        echo json_encode($data);
    }

    public function get_sms_balance()
    {
        $url = 'https://kal.codes/sms-api/login';
        $client = new Client();
        $data = [
            "email" => "admin@ramuzatcompany.com",
            "password" => "L8g4JgfcrcjthSW",
        ];

        $response = $client->post($url, [
            'form_params' => $data,
        ]);

        $body = $response->getBody()->getContents();
        $arr_body = json_decode($body, true);

        //$user = $arr_body["data"]["user"];

        echo json_encode(['balance' => number_format($arr_body['user']['acc_balance'])]);
    }
}
