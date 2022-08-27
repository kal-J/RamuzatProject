<?php
class SummaryReports extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->data['privilege_list'] = $this->helpers->user_privileges(10, $this->session->userdata('staff_id'));

        $this->data['module_access'] = $this->helpers->org_access_module(10, $_SESSION['organisation_id']);
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['billing_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        }

        $this->load->model("logs_model");
        $this->load->model('journal_transaction_model');
        $this->load->model('Summary_report_model');
        $this->load->model('accounts_model');
        $this->load->model('Staff_model');

        $this->load->library("num_format_helper");
        $this->load->model('miscellaneous_model');
        $this->load->model("organisation_format_model");
        $this->load->model("RolePrivilege_model");
        $this->load->model("organisation_model");
        $this->load->model("branch_model");

        $fiscal_year = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        if (empty($fiscal_year)) {
            redirect('dashboard');
        } else {
            $this->data['fiscal_active'] = array_merge($fiscal_year, ['start_date2' => date("d-m-Y", strtotime($fiscal_year['start_date'])), 'end_date2' => date("d-m-Y", strtotime($fiscal_year['end_date']))]);
            $this->data['lock_month_access'] = $this->helpers->org_access_module($module_id = 23, $_SESSION['organisation_id']);
            if (!empty($this->data['lock_month_access'])) {
                $this->data['active_month'] = $this->Fiscal_month_model->get_active_month();
                if (empty($this->data['active_month'])) {
                    redirect('dashboard');
                }
            }
        }
    }

    public function index()
    {
        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] = array_column($this->data['module_list'], "module_id");

        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['branch'] = $this->branch_model->get($_SESSION['branch_id']);

        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $fiscal_year = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['members'] = $this->Staff_model->get_staff();

        $this->data['title'] = $this->data['sub_title'] = "Summary Reports";

        $this->template->title = $this->data['title'];

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js", "plugins/printjs/print.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css", "custom.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        //$this->data['summary'] = $this->jsonList(date('d-m-Y', strtotime($fiscal_year['start_date'])),date('d-m-Y', strtotime($fiscal_year['end_date'])));

        $this->template->content->view('summary_reports/index', $this->data);
        // Publish the template
        $this->template->publish();
    }


    public function jsonList()
    {
        $this->load->model('reports_model');

        $data['savings_deposits'] = $this->Summary_report_model->get_dTable("journal_type_id IN (7)");
        $data['savings_withdraws'] = $this->Summary_report_model->get_dTable("journal_type_id IN (8,9,10)");

        $data['general_expenses'] = $this->Summary_report_model->get_dTable("journal_type_id IN (2,3,15)");

        $data_list['sum_income'] = $this->reports_model->get_category_sums(4, FALSE);
        $data_list['sum_expense'] = $this->reports_model->get_category_sums(5, FALSE);

        $data['profitloss_sums'] = ['total_income' => ($data_list['sum_income']['credit_sum'] - $data_list['sum_income']['debit_sum']), 'total_expense' => ($data_list['sum_expense']['debit_sum'] - $data_list['sum_expense']['credit_sum'])];

        $data['report_type'] = 2;

        $data['disbursted_loans'] = $this->Summary_report_model->get_dTable("journal_type_id IN (4)");
        $data['loan_penalty'] = $this->Summary_report_model->get_dTable("journal_type_id IN (5)");
        $data['bad_loans'] = $this->Summary_report_model->get_dTable("journal_type_id IN (19)");
        $data['loan_payments'] = $this->Summary_report_model->get_dTable("journal_type_id IN (6)");
        $data['loan_charges'] = $this->Summary_report_model->get_dTable("journal_type_id IN (28)");
        $data['mantanance_fees'] = $this->Summary_report_model->get_dTable("journal_type_id IN (13)");
        $data['invoice_payments'] = $this->Summary_report_model->get_dTable("journal_type_id IN (16,17)");
        $data['savings_charges'] = $this->Summary_report_model->get_dTable("journal_type_id IN (9,10,20)");
        $data['subscription_membership'] = $this->Summary_report_model->get_dTable("journal_type_id IN (11,12)");
        $data['share_charges'] = $this->Summary_report_model->get_dTable("journal_type_id IN (32,33)");
        $data['share_payments'] = $this->Summary_report_model->get_dTable("journal_type_id IN (22)");
        $data['share_transfer'] = $this->Summary_report_model->get_dTable("journal_type_id IN (24)");
        $data['asset_transactions'] = $this->Summary_report_model->get_dTable("journal_type_id IN (29,34)");
        $data['paid_interests'] = $this->Summary_report_model->get_dTable("journal_type_id IN (30,31)");
        echo json_encode($data);
    }

    public function view($journal_type)
    {
        if (strpos($journal_type, '-') !== false) {
            $journal_type_id = str_replace('-', ',', $journal_type);
        } else {
            $journal_type_id = $journal_type;
        }

        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css", "custom.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        //$this->data['detail'] = $this->Summary_report_model->get_trans($journal_type_id);

        $this->data['title'] = "Report Details";
        $this->data['journal_types'] = $journal_type_id;

        $end_date = date_create($this->input->post("end_date"));
        $start_date = date_create($this->input->post("start_date"));

        $this->data['start_date1'] = date_format($start_date, 'Y-m-d');
        $this->data['end_date1'] = date_format($end_date, 'Y-m-d');

        $this->template->content->view('summary_reports/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function jsonList2()
    {
        $journal_type_id = $this->input->post("journal_type_id");
        $this->data['data'] = $this->Summary_report_model->get_trans($journal_type_id);
        echo json_encode($this->data);
    }
}
