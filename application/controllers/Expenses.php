<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Expenses Controller
 *  */
class Expenses extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        $this->load->library("helpers");
        /* if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        } */
        $this->load->model('accounts_model');
        $this->load->model('reports_model');
        $this->load->model('journal_transaction_line_model');
        $this->load->model('dashboard_model');
    }

    
 

    public function index()
    {
        $this->load->view('expenses/index.html');
       
    }
    public function transactions()
    {
        $fiscal_year = $this->dashboard_model->get_current_fiscal_year(1, 1);
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


}
