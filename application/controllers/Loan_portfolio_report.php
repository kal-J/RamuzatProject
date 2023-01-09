<?php


class Loan_portfolio_report extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        /* if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        } */
        ini_set('max_execution_time', 3600);

        $this->load->library('helpers');

        $this->load->model('Transaction_model');
        $this->load->model('organisation_model');
        $this->load->model('Loan_guarantor_model');
        $this->load->model('journal_transaction_model');
        $this->load->model('Loan_reversal_model');
        $this->load->model('loan_state_model');

        $this->load->model("logs_model");
        $this->load->model('accounts_model');
        $this->load->model('Staff_model');

        $this->load->library("num_format_helper");
        $this->load->model('miscellaneous_model');
        $this->load->model("organisation_format_model");
        $this->load->model("RolePrivilege_model");
        $this->load->model("Loan_portfolio_report_model");
    }

    public function credit_officers()
    {
        $this->load->view('loan_portfolio_report/index.html');
    }



    public function get_loan_portfolio_report()
    {
        $data = $this->Loan_portfolio_report_model->get_current_report();
        echo json_encode(
            $data
        );
    }

    public function get_credit_officers_report()
    {
        $data = $this->Loan_portfolio_report_model->get_credit_officers_report();
        echo json_encode(
            $data
        );
    }

    public function compute_loan_portfolio_report()
    {
        $this->Loan_portfolio_report_model->compute_loan_portfolio_report();

        echo json_encode(['success' => true]);
    }

    public function compute_credit_officers_report()
    {
        $inserted_id = $this->Loan_portfolio_report_model->compute_credit_officers_report();

        if ($inserted_id) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false]);
        }
    }
}
