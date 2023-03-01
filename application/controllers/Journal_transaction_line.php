<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Journal Transaction Line Controller
 *  */
class Journal_transaction_line extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        $this->load->library("helpers");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model('accounts_model');
        $this->load->model('reports_model');
        $this->load->model('journal_transaction_line_model');
        $this->load->model('dashboard_model');
    }

    public function jsonList()
    {
        $this->data['data'] = $this->journal_transaction_line_model->get();
        echo json_encode($this->data);
    }

    public function jsonList2()
    {
        $data['draw'] = intval($this->input->post('draw'));
        $data['data'] = $this->journal_transaction_line_model->get_dTable();
        $filtered_records_cnt = $this->journal_transaction_line_model->get_found_rows();
        $all_data = $this->journal_transaction_line_model->get2();
        //total records
        $data['recordsTotal'] = $all_data;
        $data['recordsFiltered'] = current($filtered_records_cnt);
        echo json_encode($data);
    }

    public function expenses()
    {
        $fiscal_year = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $filter = " 1 ";
        $start_date = $this->input->post("start_date") ? $this->input->post("start_date") : $fiscal_year['start_date'];
        $end_date = $this->input->post("end_date") ? $this->input->post("end_date") : $fiscal_year['end_date'];

        if ($start_date) {
            $filter .= " AND jtl.transaction_date >= '$start_date' ";
        }
        if ($end_date) {
            $filter .= " AND jtl.transaction_date <= '$end_date' ";
        }

        //echo $filter; die;

        $expenses = $this->journal_transaction_line_model->get_expenses($filter);
        echo json_encode(['data' => ['expenses' => $expenses, 'fiscal_year' => $fiscal_year]]);
    }

    public function jsonList3()
    {
        $data['draw'] = intval($this->input->post('draw'));
        $data['data'] = $this->journal_transaction_line_model->get_dTable3();
        //$filtered_records_cnt = $this->journal_transaction_line_model->get_found_rows();
        //$all_data = $this->journal_transaction_line_model->get2();
        //total records
        $data['recordsTotal'] = 0;
        $data['recordsFiltered'] = 0;
        echo json_encode($data);
    }

    function delete()
    {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have the rights to delete this record";
        $response['success'] = FALSE;

        $journal_transaction_line_id = $this->input->post('id');
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        if (($response['success'] = $this->journal_transaction_line_model->delete($journal_transaction_line_id)) === true) {
            $response['message'] = "General journal entry details successfully deleted";
        }
        // }
        echo json_encode($response);
    }

    public function check_acc_balance($account_id = false, $amount = false)
    {
        if ($account_id === false) {
            $account_id = $this->security->xss_clean($this->input->post("account_id"));
        }
        if ($amount === false) {
            $amount = $this->input->post('amount');
        }
        if (($this->input->post('payment_mode') != null) && ($this->input->post('payment_mode') == 3)) {
            $response = ['success' => TRUE];
        } else {
            $response = ['success' => $this->helpers->account_balance($account_id, $amount)];
        }
        if (!$response['success']) {
            echo json_encode($response['success']);
        } else {
            echo json_encode($response['success']);
        }
    }
}
