<?php

/**
 * Description of shares
 *
 * @author REAGAN AJUNA
 */
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Portfolio_aging extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        $this->load->library("num_format_helper");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }

        $this->load->model("shares_model");
        $this->load->model("RolePrivilege_model");
        $this->load->model("Fiscal_month_model");
        $this->load->model("Share_transaction_model");
        $this->load->model('organisation_model');
        $this->load->model("Share_issuance_model");
        $this->load->model("Share_state_model");
        $this->load->model("share_call_model");
        $this->load->model("fiscal_model");
        $this->load->model("Share_issuance_fees_model");
        $this->load->model("client_loan_model");
        $this->load->model("Portfolio_aging_model");
        $this->load->model('Repayment_schedule_model');
        $this->load->library(array("form_validation", "helpers"));
        $fiscal_year = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['fiscal_year'] = array_merge($fiscal_year, ['start_date2' => date("d-m-Y", strtotime($fiscal_year['start_date'])), 'end_date2' => date("d-m-Y", strtotime($fiscal_year['end_date']))]);
        $this->data['privilege_list'] = $this->helpers->user_privileges(12, $_SESSION['staff_id']);
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['share_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        }
        $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        if (empty($this->data['fiscal_active'])) {
            redirect('dashboard');
        } else {
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
        $this->load->model("Dashboard_model");
        $this->load->model("Member_model");
        $this->load->model("share_call_model");
        $this->load->model("TransactionChannel_model");
        $this->load->model("miscellaneous_model");
        $this->load->model("Savings_account_model");
        $this->load->model("Share_issuance_category_model");
        $this->load->model("Share_transaction_model");
        $this->load->model("Shares_model");
        $this->load->model('Alert_setting_model');
        $this->load->model('Portfolio_aging_model');
        $this->load->model('reports_model');
        $this->load->helper('pdf_helper');
        $this->load->model('Repayment_schedule_model');

        // Get organisation settings
        $this->data['org_settings'] = $this->organisation_model->get("id = " . $_SESSION['organisation_id'])[0];
        $this->data['tchannel'] = $this->TransactionChannel_model->get();
        $this->data['firstcall'] = $this->share_call_model->get_first_calls(null);
        $this->data['members'] = $this->Savings_account_model->get_clients('status_id=1');

        $this->data['title'] = $this->data['sub_title'] = 'Portfolio Aging';
        $neededjs = array("plugins/validate/jquery.validate.min.js", "plugins/select2/select2.full.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js", "plugins/printjs/print.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css");
        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);

        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        // organisation name 
        $this->data['org_name'] = $this->organisation_model->get('id=1');
        $this->data['fiscal_period'] = $this->fiscal_model->get('status_id=1');

        $this->data['modules'] = array_column($this->data['module_list'], "module_id");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->title = $this->data['title'];


        $this->data['all_portfolio_details'] = $this->client_loans('state_id IN(7,13)');
        $this->data['all_portfolio_details_2'] = $this->client_loans("state_id IN(11,14,15)");
        $this->data['total_outstanding_loan_portfolio'] = $this->get_total_outstanding_loan_portfolio();
        // Load a view in the content partial
        $this->template->content->view('reports/receivables/portfolio_aging_report_view', $this->data);
        // Publish the template
        $this->template->publish();
        //echo json_encode($this->data); die;
    }
    private function get_aging_receivables($start_range, $end_date, $type)
    {
        $between = "( jtl.transaction_date BETWEEN '" . ($start_range) . "' AND '" . ($end_date) . "')";
        $category_sum = $this->reports_model->get_category_sums(TRUE, 1, $between);
        //print_r($category_sum);die();
        $total_debt = $category_sum['amount'];
        return $total_debt;
    }

    public function jsonList()
    {
        $this->load->model('Portfolio_aging_model');
        $this->data['data'] = $this->Portfolio_aging_model->get();
        echo json_encode($this->data);
    }

    public function create()
    {
        $this->form_validation->set_rules('start_range_in_days', 'Start range is mandatory', array('required'));
        $this->form_validation->set_rules('end_range_in_days', 'End range is mandatory', array('required'));
        $this->form_validation->set_rules('provision_percentage', 'Provision percentage is mandatory');
        $this->form_validation->set_rules('provision_loan_loss_account_id', 'Provision for loan loss account is mandatory');
        $this->form_validation->set_rules('provision_method_id', 'Provision method is mandatory');
        $this->form_validation->set_rules('asset_account_id', 'Provision asset account is mandatory');
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                $id = $_POST['id'];
                if ($this->Portfolio_aging_model->update($id)) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan provision setting successfully updated";
                } else {
                    $feedback['message'] = "There was a problem updating the loan provision  setting, please try again or get in touch with the admin";
                }
            } else {

                $alert_setting = $this->Portfolio_aging_model->set();
                if (is_numeric($alert_setting)) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan provision setting details successfully saved";
                } else {
                    $feedback['message'] = "There was a problem saving the loan provision";
                }
            }
        }
        echo json_encode($feedback);
    }

    public function change_status()
    {
        $data = $this->input->post(NULL, TRUE);
        $response = $this->Portfolio_aging_model->change_status($data);
        $feedback['success'] = false;
        $feedback['message'] = "Loan provision could not be deactivate.";
        if ($response) {
            $feedback['success'] = true;
            $feedback['message'] = "Loan provision detail successfully deactivated.";
        }
        echo json_encode($feedback);
    }

    public function delete()
    {
        $data = $this->input->post(NULL, TRUE);
        $response = $this->Portfolio_aging_model->delete($data);
        $feedback['success'] = false;
        $feedback['message'] = "Loan provision could not be deleted.";
        if ($response) {
            $feedback['success'] = true;
            $feedback['message'] = "Loan provision detail successfully deleted.";
        }
        echo json_encode($feedback);
    }

    public function client_loans($filter = false)
    {
        //retrieves portfolio setting data
        $this->data['portfolio_aging'] = $this->Portfolio_aging_model->get('status_id=1');

        if ($filter) {
            $allLoanAccountsDetail = $this->client_loan_model->get($filter);
        } else {
            $allLoanAccountsDetail = $this->client_loan_model->get('state_id IN(7,13)');
        }

        $all_portfolio_details = [];
        $sub_total_level1_num_acc = 0;
        $sub_total_level1_outstanding_loan_portfolio = 0;
        $sub_total_level1_required_provision_amount = 0;

        foreach ($this->data['portfolio_aging']  as $pa_data) {

            $range = [
                "start_range_in_days" => $pa_data['start_range_in_days'],
                "end_range_in_days"   => $pa_data['end_range_in_days'],
                "name" => $pa_data['name']
            ];
            $start_range = $pa_data['start_range_in_days'];
            $end_range = $pa_data['end_range_in_days'];
            $aging_name = $pa_data['name'];
            $provision_percentage = $pa_data['provision_percentage'];
            $all_portfolio_range_data = [$aging_name => []];
            $provision_loan_loss_account_id = $pa_data['provision_loan_loss_account_id'];
            $asset_account_id = $pa_data['asset_account_id'];


            $sum_outstanding_loan_portfolio = 0;
            $required_provision_amount = 0;
            foreach ($allLoanAccountsDetail as $loan_details) {
                $due_date = $loan_details['next_pay_date'];

                $lastPayDate = $due_date;
                $todayDate = time();
                $dateDiff = $todayDate - strtotime($lastPayDate);
                $daysPast = floor($dateDiff / (60 * 60 * 24));

                if ($daysPast >= $start_range && $daysPast <= $end_range) {
                    $sum_outstanding_loan_portfolio += $loan_details['amount_in_demand'];
                    array_push($all_portfolio_range_data[$aging_name], $loan_details);
                }
            }
            $required_provision_amount = ($sum_outstanding_loan_portfolio * $provision_percentage) / 100;
            $sub_total_level1_num_acc += count($all_portfolio_range_data[$aging_name]);
            $sub_total_level1_outstanding_loan_portfolio += $sum_outstanding_loan_portfolio;
            $sub_total_level1_required_provision_amount += $required_provision_amount;
            $action_button = null;
            //    if($required_provision_amount > 0){
            //        $url = base_url(). 'Portfolio_aging/renderProvisonModal/'.$required_provision_amount.'/'.$provision_loan_loss_account_id.'/'.$asset_account_id;
            //        $action_button = "<a data-required_provision_amount='".$required_provision_amount."'data-provision-loan-loss-account-id='".$provision_loan_loss_account_id."' data-asset-account-id='".$asset_account_id."' data-href='".$url."' data-toggle='modal' class='btn btn-sm openBtn' title='Provision this loan amount' style='font-size:12px;color:forestgreen;'>Provision</a>";
            //    }else {
            //        $action_button = null;
            //    }

            /* array_push($all_portfolio_details, [$aging_name . " ( " . $start_range . "-" . $end_range . " ) " => ['number_of_accounts' => count($all_portfolio_range_data[$aging_name]), 'sum_outstanding_loan_portfolio' => $sum_outstanding_loan_portfolio, 'provision_percentage' => $provision_percentage, 'required_provision_amount' => $required_provision_amount, 'action_button' => $action_button]]); */
            array_push($all_portfolio_details, [$aging_name . " ( " . $start_range . "-" . $end_range . " ) " => ['number_of_accounts' => count($all_portfolio_range_data[$aging_name]), 'sum_outstanding_loan_portfolio' => $sum_outstanding_loan_portfolio  ]]);
        }
        $_POST['end_date'] = date('y-m-d');

        $amount_disbursed = $this->Repayment_schedule_model->daily_sum_interest_principal();
        $total_disbursed_loan_amount = $amount_disbursed['principal_sum'];
        return ['data' => $all_portfolio_details, "sub_total_level1_num_acc" => $sub_total_level1_num_acc, "sub_total_level1_outstanding_loan_portfolio" => $sub_total_level1_outstanding_loan_portfolio, "sub_total_level1_required_provision_amount" => $sub_total_level1_required_provision_amount, "asset_account_id" => $asset_account_id, "provision_loan_loss_account_id" => $provision_loan_loss_account_id, "total_disbursed_loan_amount" => $total_disbursed_loan_amount];
    }

    public function pdf_printout()
    {
        $this->data['org_name'] = $this->organisation_model->get('id=1');
        $this->data['fiscal_period'] = $this->fiscal_model->get('status_id=1');

        $this->data['title'] = $this->data['sub_title'] = 'Portfolio Aging';
        $neededjs = array("plugins/validate/jquery.validate.min.js", "plugins/select2/select2.full.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js", "plugins/printjs/print.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css");
        $this->load->helper('pdf_helper');
        $this->data['title'] = $_SESSION["org_name"];
        $this->data['sub_title'] = "Portfolio Aging ";
        $this->data['font'] = 'helvetica';
        $this->data['fontSize'] = 7;
        $this->data['all_portfolio_details'] = $this->client_loans('state_id IN(7,13)');
        $this->data['all_portfolio_details_2'] = $this->client_loans("state_id IN(11,14,15)");
        $this->data['the_page_data'] = $this->load->view('reports/receivables/portfolio_aging_pdf_printout', $this->data, TRUE);
        echo json_encode($this->data);
    }


    public function renderProvisonModal($required_provision_amount, $provision_loan_loss_account_id, $asset_account_id)
    {
        $this->data['required_provision_amount'] = $required_provision_amount;
        $this->data['provision_loan_loss_account_id'] = $provision_loan_loss_account_id;
        $this->data['asset_account_id'] = $asset_account_id;

        print_r($this->load->view('reports/receivables/loan_loss_provision-modal', $this->data, TRUE));
    }
    public function  loan_loss_provision()
    {
        $feedback['success'] = true;
        $feedback['message'] = "Loan provision successful";
        $data = $this->input->post(NULL, TRUE);
        if (!empty($data)) {
            $this->do_journal_transaction($data);
        } else {
            $feedback['success'] = false;
            $feedback['message'] = "Loan provision failed, contact the IT. Administrator";
        }
        echo json_encode($feedback);
    }

    public function client_loans_details($filter = false, $category_name)
    {
        //retrieves portfolio setting data
        $this->data['portfolio_aging'] = $this->Portfolio_aging_model->get('status_id=1');

        if ($filter) {
            $allLoanAccountsDetail = $this->client_loan_model->get($filter);
        } else {
            $allLoanAccountsDetail = $this->client_loan_model->get('state_id IN(7,13)');
        }

        $all_portfolio_details = [];
        $sub_total_level1_num_acc = 0;
        $sub_total_level1_outstanding_loan_portfolio = 0;
        $sub_total_level1_required_provision_amount = 0;

        foreach ($this->data['portfolio_aging']  as $pa_data) {

            $range = [
                "start_range_in_days" => $pa_data['start_range_in_days'],
                "end_range_in_days"   => $pa_data['end_range_in_days'],
                "name" => $pa_data['name']
            ];
            $start_range = $pa_data['start_range_in_days'];
            $end_range = $pa_data['end_range_in_days'];
            $aging_name = $pa_data['name'];
            $provision_percentage = $pa_data['provision_percentage'];
            $all_portfolio_range_data = [$aging_name => []];
            $provision_loan_loss_account_id = $pa_data['provision_loan_loss_account_id'];
            $asset_account_id = $pa_data['asset_account_id'];


            $sum_outstanding_loan_portfolio = 0;
            $required_provision_amount = 0;
            foreach ($allLoanAccountsDetail as $loan_details) {
                $due_date = $loan_details['next_pay_date'];
                $princ_amount = $loan_details['loan_no'];


                $lastPayDate = $due_date;
                $todayDate = time();
                $dateDiff = $todayDate - strtotime($lastPayDate);
                $daysPast = floor($dateDiff / (60 * 60 * 24));

                if ($daysPast >= $start_range && $daysPast <= $end_range) {
                    $sum_outstanding_loan_portfolio += $loan_details['amount_in_demand'];
                    array_push($all_portfolio_range_data[$aging_name], $loan_details);
                    //print_r(json_encode($all_portfolio_range_data[$aging_name]));die;   
                }
            }
            $required_provision_amount = ($sum_outstanding_loan_portfolio * $provision_percentage) / 100;
            $sub_total_level1_num_acc += count($all_portfolio_range_data[$aging_name]);
            $sub_total_level1_outstanding_loan_portfolio += $sum_outstanding_loan_portfolio;
            $sub_total_level1_required_provision_amount += $required_provision_amount;




            array_push($all_portfolio_details, [$aging_name . " ( " . $start_range . "-" . $end_range . " ) " => ['loan_data' => $all_portfolio_range_data[$aging_name]]]);
        }
        $_POST['end_date'] = date('y-m-d');

        $amount_disbursed = $this->Repayment_schedule_model->daily_sum_interest_principal();
        $total_disbursed_loan_amount = $amount_disbursed['principal_sum'];
        $category_data = null;
        if (!empty($category_name) && is_numeric($category_name)) {
            $category_data = $all_portfolio_details[$category_name - 1];
        } else {
            $category_data = $all_portfolio_details;
        }
        
        return $category_data;
    }
    public function get_category_loan_lists($category_name = false)
    {
        $this->load->model("Dashboard_model");
        $this->load->model("Member_model");
        $this->load->model("share_call_model");
        $this->load->model("TransactionChannel_model");
        $this->load->model("miscellaneous_model");
        $this->load->model("Savings_account_model");
        $this->load->model("Share_issuance_category_model");
        $this->load->model("Share_transaction_model");
        $this->load->model("Shares_model");
        $this->load->model('Alert_setting_model');
        $this->load->model('Portfolio_aging_model');
        $this->load->model('reports_model');
        $this->load->helper('pdf_helper');
        $this->load->model('Repayment_schedule_model');

        // Get organisation settings
        $this->data['org_settings'] = $this->organisation_model->get("id = " . $_SESSION['organisation_id'])[0];
      

        
        $neededjs = array("plugins/validate/jquery.validate.min.js", "plugins/select2/select2.full.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js", "plugins/printjs/print.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css");
        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);

        // organisation name 
        $this->data['org_name'] = $this->organisation_model->get('id=1');
        //print_r($this->data['org_name']);die;
        $this->data['fiscal_period'] = $this->fiscal_model->get('status_id=1');

        //$this->data['modules'] = array_column($this->data['module_list'], "module_id");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        

        if (!empty($category_name) && is_numeric($category_name)) {
            $this->data['all_portfolio_details'] = $this->client_loans_details('state_id IN(7,13)', $category_name);
            $this->template->title = $this->data['sub_title'] = 'Portfolio Aging';
        } else if(!empty($category_name) && !is_numeric($category_name)) {
            $cat_name = str_split($category_name);
            
            $this->data['all_portfolio_details'] = null;
            $this->data['all_portfolio_details_2'] = $this->client_loans_details("state_id IN(11,14,15)", $cat_name[0]);
            $this->template->title = $this->data['sub_title'] = 'Rescheduled or Reclassified Loans';
        }else{
            echo json_encode('Not numeric');
        }
        // Load a view in the content partial
        $this->template->content->view('reports/receivables/categories/table_view', $this->data);
        // Publish the template
        $this->template->publish();
        //echo json_encode($this->data); die;
    }


    public function export_to_excel()
    {

        $all_portfolio_details = $this->client_loans('state_id IN(7,13)');
        $all_portfolio_details_2 = $this->client_loans("state_id IN(11,14,15)");
        $org_name = $this->organisation_model->get('id=1');
        $fiscal_period = $this->fiscal_model->get('status_id=1');
        $fiscal_year = explode("-", $fiscal_period[0]['start_date']);
        $end_fiscal_year = explode("-", $fiscal_period[0]['end_date']);
        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:E1");
        $sheet->setCellValue('A1', "PORTFOLIO AGING REPORT");
        $sheet->getStyle("A1:E1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:E1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center

        // Organisation Name 
        $sheet->setCellValue('A2', 'Organisation Name');
        $sheet->mergeCells("B2:E2");
        $sheet->setCellValue('B2', $org_name[0]['name']);
        $sheet->getStyle("A2:E2")->getFont()->setBold(true);
        // financial year  
        $sheet->setCellValue('A3', 'FINANCIAL YEAR');
        $sheet->mergeCells("B3:D3");
        $year = $fiscal_year[0] == $end_fiscal_year[0] ? $end_fiscal_year[0] : $fiscal_year[0] . " - " . $end_fiscal_year[0];
        $sheet->setCellValue('B3', $year);
        $sheet->getStyle("A3:E3")->getFont()->setBold(true);
        //start date 
        $sheet->setCellValue('A4', 'START DATE');
        $sheet->mergeCells("B4:D4");
        $sheet->setCellValue('B4', $fiscal_period[0]['start_date']);
        $sheet->getStyle("A4:E4")->getFont()->setBold(true);
        // end date
        $sheet->setCellValue('A5', 'END DATE');
        $sheet->mergeCells("B5:D5");
        $sheet->setCellValue('B5', $fiscal_period[0]['end_date']);
        $sheet->getStyle("A5:E5")->getFont()->setBold(true);

        $sheet->setCellValue('A6', 'Classification(Days)');
        $sheet->setCellValue('B6', 'Number of Accounts');
        $sheet->setCellValue('C6', 'Outstanding Loan Portfolio (UGX)');
        $sheet->setCellValue('D6', 'Required Provision (%)');
        $sheet->setCellValue('E6', 'Required Provision (UGX)');
        $sheet->getStyle("A6:E6")->getFont()->setBold(true);


        // data
        $rowCount = 7;
        foreach ($all_portfolio_details['data'] as $key => $value) {
            foreach ($value as $key1 => $value1) {
                unset($value1['action_button']);

                $sheet->setCellValue('A' . $rowCount, $key1);
                $i = 0;
                $columnArray = ['B', 'C', 'D', 'E'];
                foreach ($value1 as $key2 => $value2) {
                    $sheet->setCellValue($columnArray[$i] . $rowCount, number_format($value2));
                    $i++;
                }
                $rowCount++;
            }
        }

        $sheet->setCellValue('A' . $rowCount, 'Sub Total');
        $sheet->setCellValue('B' . $rowCount,  number_format($all_portfolio_details['sub_total_level1_num_acc']));
        $sheet->setCellValue('C' . $rowCount,  number_format($all_portfolio_details['sub_total_level1_outstanding_loan_portfolio']));
        $sheet->setCellValue('D' . $rowCount, '');
        $sheet->setCellValue('E' . $rowCount,  number_format($all_portfolio_details['sub_total_level1_required_provision_amount']));
        $sheet->getStyle("A" . $rowCount . ":E" . $rowCount)->getFont()->setBold(true);
        $rowCount++;


        $sheet->mergeCells('A' . $rowCount . ":E" . $rowCount);
        $sheet->setCellValue('A' . $rowCount, 'Rescheduled or Reclassified loans');
        $sheet->getStyle('A' . $rowCount . ":E" . $rowCount)->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $rowCount++;


        foreach ($all_portfolio_details_2['data'] as $key => $value) {
            foreach ($value as $key1 => $value1) {
                unset($value1['action_button']);
                $sheet->setCellValue('A' . $rowCount, $key1);
                $i = 0;
                $columnArray = ['B', 'C', 'D', 'E'];
                foreach ($value1 as $key2 => $value2) {
                    $sheet->setCellValue($columnArray[$i] . $rowCount, number_format($value2));
                    $i++;
                }
                $rowCount++;
            }
        }

        $sheet->setCellValue('A' . $rowCount, 'Sub Total');
        $sheet->setCellValue('B' . $rowCount,  number_format($all_portfolio_details_2['sub_total_level1_num_acc']));
        $sheet->setCellValue('C' . $rowCount,  number_format($all_portfolio_details_2['sub_total_level1_outstanding_loan_portfolio']));
        $sheet->setCellValue('D' . $rowCount, '');
        $sheet->setCellValue('E' . $rowCount,  number_format($all_portfolio_details_2['sub_total_level1_required_provision_amount']));
        $sheet->getStyle("A" . $rowCount . ":E" . $rowCount)->getFont()->setBold(true);
        $rowCount++;
        $sheet->setCellValue('A' . $rowCount, 'Grand Total');
        $sheet->setCellValue('B' . $rowCount, number_format($all_portfolio_details['sub_total_level1_num_acc'] + $all_portfolio_details_2['sub_total_level1_num_acc']));
        $sheet->setCellValue('C' . $rowCount,  number_format($all_portfolio_details['sub_total_level1_outstanding_loan_portfolio'] + $all_portfolio_details_2['sub_total_level1_outstanding_loan_portfolio']));
        $sheet->setCellValue('D' . $rowCount, '');
        $sheet->setCellValue('E' . $rowCount,  number_format($all_portfolio_details['sub_total_level1_required_provision_amount'] + $all_portfolio_details_2['sub_total_level1_required_provision_amount']));
        $sheet->getStyle("A" . $rowCount . ":E" . $rowCount)->getFont()->setBold(true);
        $rowCount++;


        // // auto size
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }



        $writer = new Xlsx($spreadsheet);
        $filename = "Portfolio Aging Report";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
    private function do_journal_transaction($transaction_data)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('accounts_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('journal_transaction_line_model');
        $transaction_data = [
            'transaction_date' => $this->input->post('transaction_date'),
            'description' => $this->input->post('narrative'),
            //$data['transaction_no'] = date('yws').mt_rand(1000, 9999);
            'ref_no' => date('yws') . mt_rand(1000, 9999),
            'ref_id' => date('yws') . mt_rand(1000, 9999),
            'status_id' => 1,
            'journal_type_id' => 19 // Bad loans.
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($transaction_data);
        //unset($transaction_data);
        //print_r($transaction_data);die;
        //then we prepare the journal transaction lines
        //if($this->input->post('payment_id')==3){
        $debit_or_credit2 = $this->accounts_model->get_normal_side($this->input->post('provision_loan_loss_account_id'), true);

        //} else {
        $debit_or_credit1 = $this->accounts_model->get_normal_side($this->input->post('asset_account_id'), false);

        $data = [
            [
                $debit_or_credit1 => $this->input->post('required_provision_amount'),
                'narrative' => $transaction_data['transaction_date'] . " " . $this->input->post('narrative'),
                'reference_no' => $transaction_data['ref_no'],
                'reference_id' => $transaction_data['ref_id'],
                'transaction_date' => $this->input->post('transaction_date'),
                'account_id' => $this->input->post('asset_account_id'),
                'status_id' => 1
            ],
            [
                $debit_or_credit2 => $this->input->post('required_provision_amount'),
                'narrative' => $this->input->post('transaction_date') . " " . $this->input->post('narrative'),
                'reference_no' => $transaction_data['ref_no'],
                'reference_id' => $transaction_data['ref_id'],
                'transaction_date' => $this->input->post('transaction_date'),
                'account_id' => $this->input->post('provision_loan_loss_account_id'),
                'status_id' => 1
            ]
        ];
        $this->journal_transaction_line_model->set($journal_transaction_id, $data);
    }

    public function get_total_outstanding_loan_portfolio() {
        $total_outstanding_loan_portfolio = $this->client_loan_model->get_total_outstanding_loan_portfolio();
        return $total_outstanding_loan_portfolio;
    }
}
