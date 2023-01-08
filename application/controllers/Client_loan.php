<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Client_loan extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 4, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 4, $_SESSION['organisation_id']);
        if (empty($this->data['module_access'])) {
            redirect('my404');
        } else {

            if (empty($this->data['privilege_list'])) {
                redirect('my404');
            } else {
                $this->data['client_loan_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
            }

            //setting default timezone
            date_default_timezone_set('Africa/Kampala');
            $this->load->model('client_loan_model');
            $this->load->model('applied_loan_fee_model');
            $this->load->model('member_model');
            $this->load->model('Staff_model');
            $this->load->model('loan_product_model');
            $this->load->model('penalty_calculation_method_model');
            $this->load->model('accounts_model');
            $this->load->model('repayment_schedule_model');
            $this->load->model('Fiscal_month_model');
            $this->load->model('miscellaneous_model');
            $this->load->model('loan_collateral_model');
            $this->load->model('member_collateral_model');
            $this->load->model('loan_fees_model');
            $this->load->model('loan_guarantor_model');
            $this->load->library('Loan_schedule_generation');
            $this->load->model('loan_product_fee_model');
            $this->load->model('shares_model');
            $this->load->model('approving_staff_model');
            $this->load->model('Transaction_date_control_model');

            $this->data['allowed_transaction_dates'] = $this->Transaction_date_control_model->generate_allowed_dates();


            $this->load->model('loan_attached_saving_accounts_model');
            $this->load->model('Transaction_model');

            $orgdata['org'] = $this->organisation_model->get(1);
            $orgdata['branch'] = $this->organisation_model->get_org(1);
            $this->organisation = $orgdata['org']['name'];
            $this->contact_number = $orgdata['branch']['office_phone'];

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
    }

    public function index($group_loan_id = false)
    {
        $this->load->model('transactionChannel_model');
        $this->load->model("loan_doc_type_model");

        $this->load->model('user_income_type_model');
        $this->load->model('user_expense_type_model');
        $this->load->model('loan_product_fee_model');
        $this->load->model('RolePrivilege_model');
        $this->load->model('dashboard_model');
        $this->load->model('branch_model');
        $this->load->model('organisation_model');
        // $this->load->model('loan_approval_setting_model');
        $this->load->library("num_format_helper");

        $this->data['case2'] = 'client_loan';
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        // $this->data['approval_info']=$this->loan_approval_setting_model->get();
        if ($group_loan_id == false) {
            $this->data['type'] = $this->data['sub_type'] = 'client_loan';
            $this->data['title'] = $this->data['sub_title'] = $this->lang->line('cont_client_name') . ' Loans';
            $this->data['modal_title'] = 'Individual Loan';
            $this->data['members'] = $this->member_model->get_member_by_user_id("fms_member.status_id=1");
            $this->data['loanProducts'] = $this->loan_product_model->get_product("loan_product.status_id=1 AND loan_product.available_to_id=3 OR loan_product.available_to_id=1");
            $this->data['state_totals'] = $this->client_loan_model->state_totals();
        } else {
            $this->load->model('group_loan_model');
            $this->load->model('Group_model');
            $this->data['group_id'] = $group_loan_id;
            $this->data['type'] = $this->data['sub_type'] = 'group_loan';
            $this->data['modal_title'] = 'Group Loan';

            $this->data['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id =" . $group_loan_id);
            $this->data['group_loan_details'] = $this->group_loan_model->get($group_loan_id);
            $this->data['title'] = $this->data['sub_title'] = $this->data['group_loan_details']['group_loan_no'] . ' - ' . $this->data['group_loan_details']['group_name'];
            $this->data['groups'] = $this->Group_model->get_group($this->data['group_loan_details']['group_id']);

            $this->data['loanProducts'] = $this->loan_product_model->get_product($this->data['group_loan_details']['loan_product_id']);
            $this->data['loan_type'] = $this->miscellaneous_model->get_loan_type();
            $this->data['members'] = $this->member_model->get_member_by_user_id("fms_member.id IN (SELECT member_id from fms_group_member WHERE status_id=1 AND group_id =" . $this->data['group_loan_details']['group_id'] . " AND member_id NOT IN  ( SELECT member_id from fms_client_loan WHERE group_loan_id = " . $group_loan_id . " ) AND status_id=1)");
        }
        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] = array_column($this->data['module_list'], "module_id");

        $this->data['loan_doc_types'] = $this->loan_doc_type_model->get();
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 3');
        $this->data['relationship_types'] = $this->miscellaneous_model->get_relationship_type();
        $this->data['collateral_types'] = $this->loan_collateral_model->get_collateral_type();

        $this->data['all_collaterals'] = $this->member_collateral_model->get_not_attached_to_active_loan('status_id=1');

        $this->data['guarantors'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + 
        ifnull( transfer ,0) +ifnull(charges, 0) + ifnull( amount_locked, 0) ) >= 0 and j.state_id = 7 AND a.client_type=1");
        $this->data['savings_accs'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0) +ifnull(charges, 0)+ ifnull( amount_locked, 0) ) > 0 and j.state_id = 7 AND a.client_type=1");
        $this->data['share_guarantors'] = $this->shares_model->get("share_state.state_id = 7");

        $this->data['share_accs'] = $this->shares_model->get("share_state.state_id = 7");
        $this->data['income_items'] = $this->user_income_type_model->get();
        $this->data['expense_items'] = $this->user_expense_type_model->get();
        $this->data['available_loan_fees'] = $this->loan_product_fee_model->get();
        $this->data['pay_with'] = $this->accounts_model->get_pay_with("10");

        //print_r($this->data['pay_with']);die();

        // $this->data['installments'] = $this->repayment_schedule_model->get('payment_status <> 1 AND repayment_schedule.status_id=1');
        $this->data['active_loans'] = $this->client_loan_model->get_loans('loan_state.state_id=7 OR loan_state.state_id=13 OR loan_state.state_id=12');
        //print_r($this->data['active_loans']);die();
        $this->data['account_list'] = $this->accounts_model->get();

        $this->data['available_loan_range_fees'] = $this->loan_fees_model->get_range_fees();
        $this->data['staffs'] = $this->Staff_model->get_registeredby("status_id=1");
        $this->data['penalty_calculation_method'] = $this->penalty_calculation_method_model->get();
        $this->data['repayment_made_every'] = $this->miscellaneous_model->get();
        //$this->data['new_loan_no'] = $this->client_loan_model->get_id();
        $this->data['new_loan_acc_no'] = $this->num_format_helper->new_loan_acc_no();
        $this->template->title = $this->data['title'];
        $this->data['tchannel'] = $this->transactionChannel_model->get();
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $rand_no = mt_rand(1000, 1200);
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/steps/jquery.steps.min.js?v=$rand_no", "plugins/printjs/print.min.js", "plugins/autoNumeric/autoNumeric.min.js", "node_modules/sweetalert2/dist/sweetalert2.all.min.js"); //,"plugins/steps/jquery.steps.fix.js"
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css", "plugins/steps/jquery.steps.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->content->view('client_loan/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function jsonList()
    {
        $data['draw'] = intval($this->input->post('draw'));
        $data['data'] = $this->client_loan_model->get_dTable();
        $filteredl_records_cnt = $this->client_loan_model->get_found_rows();
        $data['recordsTotal'] = $this->client_loan_model->get2();
        $data['recordsFiltered'] = current($filteredl_records_cnt);
        echo json_encode($data);
    }

    public function loans_payable_today()
    {
        $data['draw'] = intval($this->input->post('draw'));
        $payable_loans = $this->client_loan_model->get_loan_payable_today();
        $filteredl_records_cnt = $this->client_loan_model->get_found_rows();
        $data['recordsTotal'] = $this->client_loan_model->get2();
        $data['recordsFiltered'] = current($filteredl_records_cnt);
        $data['data'] = $payable_loans;
        echo json_encode($data);
      
        
    }
    
    public  function loan_payable_print_out()
    {
        //$this->load->model('loan_installment_payment_model');
        $this->load->model('branch_model');
        $this->load->model('organisation_model');
        $this->load->helper('pdf_helper');
        if ($this->input->post('client_loan_id')) {
            $loan_id = $this->input->post('client_loan_id');
            $data['loan_detail'] = $this->client_loan_model->get_client_loan($loan_id);
        }
        //$data['start_date'] = $this->input->post('start_date');
        $data['current_date']=$this->input->post('current_date');
        $payable_loans = [];
        $loans_all = $this->client_loan_model->get_loan_payable_today();
        foreach($loans_all as $key=>$loan_detail){
            if ($loan_detail['next_pay_date']==$data['current_date']){
                array_push($payable_loans, $loan_detail);
            //echo json_encode($loan_detail);


            }
        }
        $data['data'] = $payable_loans;
        $data['title'] = $_SESSION["org_name"];
        //$data['data'] = $this->loan_installment_payment_model->get();
        $data['sub_title'] = "Loan Payables";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $data['the_page_data'] = $this->load->view('client_loan/loans_payable/pdf_print_out', $data, TRUE);

        echo json_encode($data);
    }


    public function print_receipt()
    {
        if (empty($this->session->userdata('id'))) {
            redirect("welcome", "refresh");
        }
        $payment_id = $this->input->post('payment_id');
        $this->load->model('branch_model');
        $this->load->model('organisation_model');
        $this->load->model('loan_installment_payment_model');

        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $data['data'] = $this->loan_installment_payment_model->get_receipt();
        $data['payment_id'] = $payment_id;

        foreach ($data['data'] as $key => $value) {
            if ($value['id'] == $payment_id) {
                $data['trans'] = $value;
            }
        }

        $this->load->view('client_loan/loan_transactions/receipt_print_out', $data);
    }

    public function export_excel($state)
    {
        if ($state == 'disbursed_loans') {
            $state = null;
            $_POST['state_ids'] = [7, 8, 9, 10, 11, 12, 13, 14, 15];
        }
        $dataArray = $this->client_loan_model->get_excel_data($state);
        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        // set column headers
        $sheet->setCellValue('A1', 'LOAN ID');
        $sheet->setCellValue('B1', 'LOAN NO');
        $sheet->setCellValue('C1', 'CLIENT NAME');
        $sheet->setCellValue('D1', 'REQUESTED AMOUNT');
        $sheet->setCellValue('E1', 'DISBURSED AMOUNT');
        $sheet->setCellValue('F1', 'EXPECTED INTEREST');
        $sheet->setCellValue('G1', 'PAID PRINCIPAL');
        $sheet->setCellValue('H1', 'PAID INTEREST');
        $sheet->setCellValue('I1', 'PAID AMOUNT');
        $sheet->setCellValue('J1', 'PRINCIPAL DEMANDED');
        $sheet->setCellValue('K1', 'INTEREST DEMANDED');
        $sheet->setCellValue('L1', 'AMOUNT DEMANDED');
        $sheet->setCellValue('M1', 'DAYS DUE');
        $sheet->setCellValue('N1', 'REMAINING BAL');
        $sheet->setCellValue('O1', 'DISBURSEMENT DATE');
        $sheet->setCellValue('P1', 'NEXT PAY DATE');
        $sheet->setCellValue('Q1', 'LOAN DUE DATE');
        $sheet->setCellValue('R1', 'LOAN STATE');

        // make column headers bold
        $sheet->getStyle("A1:R1")->getFont()->setBold(true);

        $rowCount   =   2;
        // populate Data into rows
        foreach ($dataArray as $data) {
            $amount_demanded = $data['paid_amount'] > 0 ? $data['amount_in_demand'] - $data['paid_amount'] : $data['amount_in_demand'];
            $remaining_bal = $data['paid_amount'] ? ($data['expected_principal'] + $data['expected_interest']) - $data['paid_amount'] : ($data['expected_principal'] + $data['expected_interest']);
            $demanded_interest = $data['amount_in_demand'] - $data['principal_in_demand'];

            $sheet->setCellValue('A' . $rowCount, $data['id']);
            $sheet->setCellValue('B' . $rowCount, $data['loan_no']);
            $sheet->setCellValue('C' . $rowCount, mb_strtoupper($data['member_name'], 'UTF-8'));
            $sheet->setCellValue('D' . $rowCount, $data['requested_amount']);
            $sheet->setCellValue('E' . $rowCount, $data['amount_approved']);
            $sheet->setCellValue('F' . $rowCount, $data['expected_interest']);
            $sheet->setCellValue('G' . $rowCount, $data['paid_principal'] ? $data['paid_principal'] : 0);
            $sheet->setCellValue('H' . $rowCount, $data['paid_interest'] ? $data['paid_interest'] : 0);
            $sheet->setCellValue('I' . $rowCount, $data['paid_amount'] ? $data['paid_amount'] : 0);
            $sheet->setCellValue('J' . $rowCount, $data['principal_in_demand']);
            $sheet->setCellValue('K' . $rowCount, $demanded_interest);
            $sheet->setCellValue('L' . $rowCount, $amount_demanded);
            $sheet->setCellValue('M' . $rowCount, $data['days_in_demand']);
            $sheet->setCellValue('N' . $rowCount, $remaining_bal);
            $sheet->setCellValue('O' . $rowCount, date('d-M-Y', strtotime($data['action_date'])));
            $sheet->setCellValue('P' . $rowCount, $data['next_pay_date']);
            $sheet->setCellValue('Q' . $rowCount, $data['last_pay_date']);
            $sheet->setCellValue('R' . $rowCount, $data['state_name']);
            $rowCount++;
        }
        //resize columns to fit content
        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        // format numbers
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('D2:D' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('E2:E' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F2:F' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G2:G' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H2:H' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('I2:I' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('J2:J' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('K2:K' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('L2:L' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('N2:N' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'R' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTAL');
        $sheet->getStyle($total_row)->getFont()->setBold(true);
        // calculate totals
        $sheet->setCellValue('D' . ($highestRow + 2), '=SUM(D2:D' . $highestRow . ')');
        $sheet->setCellValue('E' . ($highestRow + 2), '=SUM(E2:E' . $highestRow . ')');
        $sheet->setCellValue('F' . ($highestRow + 2), '=SUM(F2:F' . $highestRow . ')');
        $sheet->setCellValue('G' . ($highestRow + 2), '=SUM(G2:G' . $highestRow . ')');
        $sheet->setCellValue('H' . ($highestRow + 2), '=SUM(H2:H' . $highestRow . ')');
        $sheet->setCellValue('I' . ($highestRow + 2), '=SUM(I2:I' . $highestRow . ')');
        $sheet->setCellValue('J' . ($highestRow + 2), '=SUM(J2:J' . $highestRow . ')');
        $sheet->setCellValue('K' . ($highestRow + 2), '=SUM(K2:K' . $highestRow . ')');
        $sheet->setCellValue('L' . ($highestRow + 2), '=SUM(L2:L' . $highestRow . ')');
        $sheet->setCellValue('N' . ($highestRow + 2), '=SUM(N2:N' . $highestRow . ')');

        $sheet->getStyle('D' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('E' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('I' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('J' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('K' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('L' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('N' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');

        $loan_state = $state == 5 ? 'Active' : ($state == 12 ? 'Locked' : ($state == 13 ? 'In-Arrears' : ''));

        $writer = new Xlsx($spreadsheet);
        $filename = $loan_state . ' Loans';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function export_excel2($state)
    {
        $dataArray = $this->client_loan_model->get_excel_data($state);
        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LOAN ID');
        $sheet->setCellValue('B1', 'LOAN NO');
        $sheet->setCellValue('C1', 'INTEREST RATE');
        $sheet->setCellValue('D1', 'CLIENT NAME');
        $sheet->setCellValue('E1', 'GENDER');
        $sheet->setCellValue('F1', 'DISBURSED AMOUNT');
        $sheet->setCellValue('G1', 'PAID PRINCIPAL');
        $sheet->setCellValue('H1', 'PAID INTEREST');
        $sheet->setCellValue('I1', 'APPLICATION DATE');
        $sheet->setCellValue('J1', 'APPROVAL DATE');
        $sheet->setCellValue('K1', 'MINIMUM COLLATERAL');
        $sheet->setCellValue('L1', 'COMMENT');

        $sheet->getStyle("A1:L1")->getFont()->setBold(true);

        $rowCount   =   2;

        foreach ($dataArray as $data) {
            $sheet->setCellValue('A' . $rowCount, $data['id']);
            $sheet->setCellValue('B' . $rowCount, $data['loan_no']);
            $sheet->setCellValue('C' . $rowCount, $data['interest_rate']);
            $sheet->setCellValue('D' . $rowCount, mb_strtoupper($data['member_name'], 'UTF-8'));
            $sheet->setCellValue('E' . $rowCount, '');
            $sheet->setCellValue('F' . $rowCount, $data['amount_approved']);
            $sheet->setCellValue('G' . $rowCount, $data['paid_principal']);
            $sheet->setCellValue('H' . $rowCount, $data['paid_interest']);
            $sheet->setCellValue('I' . $rowCount, date('d-M-Y', strtotime($data['application_date'])));
            $sheet->setCellValue('J' . $rowCount, date('d-M-Y', strtotime($data['approval_date'])));
            $sheet->setCellValue('K' . $rowCount, $data['min_collateral']);
            $sheet->setCellValue('L' . $rowCount, $data['comment']);
            $rowCount++;
        }

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('C2:C' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.0');
        $sheet->getStyle('F2:F' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G2:G' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H2:H' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('K2:K' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'L' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTAL');
        $sheet->getStyle($total_row)->getFont()->setBold(true);

        // calculate totals
        $sheet->setCellValue('F' . ($highestRow + 2), '=SUM(F2:F' . $highestRow . ')');
        $sheet->setCellValue('G' . ($highestRow + 2), '=SUM(G2:G' . $highestRow . ')');
        $sheet->setCellValue('H' . ($highestRow + 2), '=SUM(H2:H' . $highestRow . ')');

        $sheet->getStyle('F' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Obligations Met Loans';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function export_excel3($state)
    {
        $dataArray = $this->client_loan_model->get_excel_data($state);
        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LOAN ID');
        $sheet->setCellValue('B1', 'LOAN NO');
        $sheet->setCellValue('C1', 'CLIENT NAME');
        $sheet->setCellValue('D1', 'DISBURSED AMOUNT');
        $sheet->setCellValue('E1', 'EXPECTED TOTAL');
        $sheet->setCellValue('F1', 'PAID INTEREST');
        $sheet->setCellValue('G1', 'PAID PRINCIPAL');
        $sheet->setCellValue('H1', 'UNPAID AMOUNT');
        $sheet->setCellValue('I1', 'PAY OFF DATE');
        $sheet->setCellValue('J1', 'REASON');

        $sheet->getStyle("A1:J1")->getFont()->setBold(true);

        $rowCount   =   2;
        foreach ($dataArray as $data) {
            $expected_total = $data['expected_interest'] + $data['expected_principal'];
            $unpaid_amount = $data['paid_amount'] ? ($data['expected_principal'] + $data['expected_interest']) - $data['paid_amount'] : ($data['expected_principal'] + $data['expected_interest']);

            $sheet->setCellValue('A' . $rowCount, $data['id']);
            $sheet->setCellValue('B' . $rowCount, $data['loan_no']);
            $sheet->setCellValue('C' . $rowCount, mb_strtoupper($data['member_name'], 'UTF-8'));
            $sheet->setCellValue('D' . $rowCount, $data['amount_approved']);
            $sheet->setCellValue('E' . $rowCount, $expected_total);
            $sheet->setCellValue('F' . $rowCount, $data['paid_interest']);
            $sheet->setCellValue('G' . $rowCount, $data['paid_principal']);
            $sheet->setCellValue('H' . $rowCount, $unpaid_amount);
            $sheet->setCellValue('I' . $rowCount, date('d-M-Y', strtotime($data['action_date'])));
            $sheet->setCellValue('J' . $rowCount, $data['comment']);

            $rowCount++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('D2:D' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('E2:E' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F2:F' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G2:G' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H2:H' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'J' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTAL');
        $sheet->getStyle($total_row)->getFont()->setBold(true);

        // calculate totals
        $sheet->setCellValue('D' . ($highestRow + 2), '=SUM(D2:D' . $highestRow . ')');
        $sheet->setCellValue('E' . ($highestRow + 2), '=SUM(E2:E' . $highestRow . ')');
        $sheet->setCellValue('F' . ($highestRow + 2), '=SUM(F2:F' . $highestRow . ')');
        $sheet->setCellValue('G' . ($highestRow + 2), '=SUM(G2:G' . $highestRow . ')');
        $sheet->setCellValue('H' . ($highestRow + 2), '=SUM(H2:H' . $highestRow . ')');

        $sheet->getStyle('D' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('E' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Paid Off Loans';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function export_excel4($state)
    {
        $dataArray = $this->client_loan_model->get_excel_data($state);
        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LOAN ID');
        $sheet->setCellValue('B1', 'LOAN NO');
        $sheet->setCellValue('C1', 'DISBURSED AMOUNT');
        $sheet->setCellValue('D1', 'EXPECTED PRINCIPAL');
        $sheet->setCellValue('E1', 'EXPECTED INTEREST');
        $sheet->setCellValue('F1', 'PAID AMOUNT');
        $sheet->setCellValue('G1', 'REMAINING BAL');
        $sheet->setCellValue('H1', 'REFINANCED ON');
        $sheet->setCellValue('I1', 'DUE DATE');

        $sheet->getStyle("A1:I1")->getFont()->setBold(true);

        $rowCount   =   2;
        foreach ($dataArray as $data) {
            $remaining_bal = $data['paid_amount'] ? ($data['expected_principal'] + $data['expected_interest']) - $data['paid_amount'] : ($data['expected_principal'] + $data['expected_interest']);

            $sheet->setCellValue('A' . $rowCount, $data['id']);
            $sheet->setCellValue('B' . $rowCount, $data['loan_no']);
            $sheet->setCellValue('C' . $rowCount, $data['amount_approved']);
            $sheet->setCellValue('D' . $rowCount, $data['expected_principal']);
            $sheet->setCellValue('E' . $rowCount, $data['expected_interest']);
            $sheet->setCellValue('F' . $rowCount, $data['paid_amount']);
            $sheet->setCellValue('G' . $rowCount, $remaining_bal);
            $sheet->setCellValue('H' . $rowCount, date('d-M-Y', strtotime($data['action_date'])));
            $sheet->setCellValue('I' . $rowCount, date('d-M-Y', strtotime($data['last_pay_date'])));

            $rowCount++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('C2:C' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('D2:D' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('E2:E' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F2:F' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G2:G' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'I' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTAL');
        $sheet->getStyle($total_row)->getFont()->setBold(true);

        // calculate totals
        $sheet->setCellValue('C' . ($highestRow + 2), '=SUM(C2:C' . $highestRow . ')');
        $sheet->setCellValue('D' . ($highestRow + 2), '=SUM(D2:D' . $highestRow . ')');
        $sheet->setCellValue('E' . ($highestRow + 2), '=SUM(E2:E' . $highestRow . ')');
        $sheet->setCellValue('F' . ($highestRow + 2), '=SUM(F2:F' . $highestRow . ')');
        $sheet->setCellValue('G' . ($highestRow + 2), '=SUM(G2:G' . $highestRow . ')');

        $sheet->getStyle('C' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('D' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('E' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Refinanced Loans';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function export_excel5($state)
    {
        $dataArray = $this->client_loan_model->get_excel_data($state);
        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LOAN ID');
        $sheet->setCellValue('B1', 'LOAN NO');
        $sheet->setCellValue('C1', 'CLIENT NAME');
        $sheet->setCellValue('D1', 'DISBURSED AMOUNT');
        $sheet->setCellValue('E1', 'EXPECTED TOTAL');
        $sheet->setCellValue('F1', 'PAID AMOUNT');
        $sheet->setCellValue('G1', 'WRITTEN OFF AMOUNT');
        $sheet->setCellValue('H1', 'DATE WRITTEN OFF');
        $sheet->setCellValue('I1', 'REASON');

        $sheet->getStyle("A1:I1")->getFont()->setBold(true);

        $rowCount   =   2;
        foreach ($dataArray as $data) {
            $written_off_amount = $data['paid_amount'] ? ($data['expected_principal'] + $data['expected_interest']) - $data['paid_amount'] : ($data['expected_principal'] + $data['expected_interest']);

            $sheet->setCellValue('A' . $rowCount, $data['id']);
            $sheet->setCellValue('B' . $rowCount, $data['loan_no']);
            $sheet->setCellValue('C' . $rowCount, mb_strtoupper($data['member_name'], 'UTF-8'));
            $sheet->setCellValue('D' . $rowCount, $data['amount_approved']);
            $sheet->setCellValue('E' . $rowCount, $data['expected_principal'] + $data['expected_interest']);
            $sheet->setCellValue('F' . $rowCount, $data['paid_amount']);
            $sheet->setCellValue('G' . $rowCount, $written_off_amount);
            $sheet->setCellValue('H' . $rowCount, date('d-M-Y', strtotime($data['action_date'])));
            $sheet->setCellValue('I' . $rowCount, $data['comment']);

            $rowCount++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('D2:D' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('E2:E' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F2:F' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G2:G' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'I' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTAL');
        $sheet->getStyle($total_row)->getFont()->setBold(true);

        // calculate totals
        $sheet->setCellValue('D' . ($highestRow + 2), '=SUM(D2:D' . $highestRow . ')');
        $sheet->setCellValue('E' . ($highestRow + 2), '=SUM(E2:E' . $highestRow . ')');
        $sheet->setCellValue('F' . ($highestRow + 2), '=SUM(F2:F' . $highestRow . ')');
        $sheet->setCellValue('G' . ($highestRow + 2), '=SUM(G2:G' . $highestRow . ')');

        $sheet->getStyle('D' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('E' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Written Off Loans';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }


    public  function loan_payments_print_out()
    {
        $this->load->model('loan_installment_payment_model');
        $this->load->model('branch_model');
        $this->load->model('organisation_model');
        $this->load->helper('pdf_helper');
        if ($this->input->post('client_loan_id')) {
            $loan_id = $this->input->post('client_loan_id');
            $data['loan_detail'] = $this->client_loan_model->get_client_loan($loan_id);
        }
        $data['start_date'] = $this->input->post('start_date');
        if ($this->input->post('end_date')) {
            $data['end_date'] = $this->input->post('end_date');
        } else {
            $data['end_date'] = date('d-m-Y', time());
        }
        $data['title'] = $_SESSION["org_name"];
        $data['data'] = $this->loan_installment_payment_model->get();
        $data['sub_title'] = "Loan Payments";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $data['the_page_data'] = $this->load->view('client_loan/payments/pdf_print_out', $data, TRUE);

        echo json_encode($data);
    }

    public  function loan_installment_payments_statement()
    {
        $this->load->model('loan_installment_payment_model');
        $this->load->model('branch_model');
        $this->load->model('organisation_model');
        $this->load->helper('pdf_helper');
        if ($this->input->post('client_loan_id')) {
            $loan_id = $this->input->post('client_loan_id');
            $data['loan_detail'] = $this->client_loan_model->get_client_loan($loan_id);
        }
        $data['title'] = $_SESSION["org_name"];
        $data['data'] = $this->loan_installment_payment_model->get();
        $data['sub_title'] = "Loan Installment Payments Statement";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $data['the_page_data'] = $this->load->view('client_loan/loan_transactions/pdf_print_out', $data, TRUE);

        echo json_encode($data);
        //$this->load->view('includes/pdf_template', $data);
    }

    public function client_loan_report_printout($value = '')
    {
        $this->load->model('branch_model');
        $this->load->model('loan_installment_payment_model');
        $this->load->model('loan_state_model');
        $this->load->model('loan_approval_model');
        // $this->load->helper('pdf_helper');
        if ($this->input->post('client_loan_id') != NULL) {
            $loan_id = $this->input->post('client_loan_id');
            $filename = $this->input->post('filename');
            $data['sub_title'] = $filename;
            $data['title'] = $_SESSION["org_name"];
            $data['filename'] = $filename;
            $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
            $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);

            //================PRINT OUT DATA AND VIEW ===============
            $where = "a.status_id = 1 AND a.client_loan_id = " . $loan_id;
            $data['loan_guarantors'] = $this->loan_guarantor_model->get($where);
            $data['loan_collateral'] = $this->loan_collateral_model->get($where);
            $data['loan_detail'] = $this->client_loan_model->get_client_loan($loan_id);
            $data['loan_approvals'] = $this->loan_approval_model->get($loan_id);
            $data['active_state'] = $this->loan_state_model->get('state_id=7 AND client_loan_id=' . $loan_id);
            $data['applied_fees'] = $this->applied_loan_fee_model->get('a.client_loan_id=' . $loan_id);
            $data['repayment_schedules'] = $this->repayment_schedule_model->get('repayment_schedule.status_id = 1 AND client_loan_id=' . $loan_id);
            $data['paid_schedules'] = $this->loan_installment_payment_model->get($loan_id);

            $data['the_page_data'] = $this->load->view('client_loan/client_loan_printout', $data, true);

            echo json_encode($data);
        } else {
            $response['status'] = false;
            $response['message'] = 'Provide the end date';

            echo json_encode($response);
        }
    }

    public function create()
    {
        $this->load->model('loan_state_model');
        $this->load->model('payment_details_model');

        $this->form_validation->set_rules('requested_amount', 'Requested amount', array('required'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if (empty($_POST['loan_type_id']) || (isset($_POST['loan_type_id']) && $_POST['loan_type_id'] == 1)) { //updating a pure group loan or an Individual loan
                    if ($this->client_loan_model->update()) { //updating the loan in the client loan table

                        if (isset($_POST['group_loan_id']) && is_numeric($_POST['group_loan_id'])) { //checking if this loan was a group loan
                            if (isset($_POST['loan_type_id']) && $_POST['loan_type_id'] == 1) { //Do this if this is a pure group loan
                                $this->load->model('group_loan_model');
                                if ($this->group_loan_model->update()) {
                                    $this->loan_state_model->set($_POST['id']);
                                    $this->payment_details_model->deactivate($_POST['id']);
                                    if ($this->input->post('preferred_payment_id') != NULL && $this->input->post('preferred_payment_id') != '1') {
                                        $this->payment_details_model->set($_POST['id']);
                                    }
                                    $feedback['success'] = true;
                                    $feedback['message'] = "Loan application successfully updated";
                                    $feedback['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);

                                    $this->helpers->activity_logs($_SESSION['id'], 4, "Editing loan application ", $feedback['message'] . " -# " . $this->input->post('preferred_payment_id'), NULL, $this->input->post('preferred_payment_id'));
                                }
                            } else { //Do this if this is a solidarity group loan
                                $this->loan_state_model->set($_POST['id']);
                                $this->payment_details_model->deactivate($_POST['id']);
                                if ($this->input->post('preferred_payment_id') != NULL && $this->input->post('preferred_payment_id') != '1') {
                                    $this->payment_details_model->set($_POST['id']);
                                }
                                $feedback['success'] = true;
                                $feedback['message'] = "Loan application successfully updated";
                                //activity log
                                $this->helpers->activity_logs($_SESSION['id'], 4, "Editing loan application ", $feedback['message'] . " -# " . $this->input->post('preferred_payment_id'), NULL, $this->input->post('preferred_payment_id'));

                                $feedback['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);
                            }
                        } else { //Do this if this is an individual
                            $this->loan_state_model->set($_POST['id']);
                            $this->payment_details_model->deactivate($_POST['id']);
                            if ($this->input->post('preferred_payment_id') != NULL && $this->input->post('preferred_payment_id') != '1') {
                                $this->payment_details_model->set($_POST['id']);
                            }
                            $feedback['success'] = true;
                            $feedback['message'] = "Loan application successfully updated";
                            $feedback['client_loan'] = $this->client_loan_model->get_client_loan($_POST['id']);
                            $this->helpers->activity_logs($_SESSION['id'], 4, "Editing loan application", $feedback['message'] . " -# " . $this->input->post('preferred_payment_id'), NULL, $this->input->post('preferred_payment_id'));
                        }
                    } else { //failure to update the client loan table do this
                        $feedback['message'] = "There was a problem updating the Loan application, please try again or get in touch with the admin";
                        $this->helpers->activity_logs($_SESSION['id'], 4, "Editing loan application", $feedback['message'] . " -# " . $this->input->post('preferred_payment_id'), NULL, $this->input->post('preferred_payment_id'));
                    }
                } else { //updating aloan that has been pure and your taking it to solidarity  type

                    if (isset($_POST['group_loan_id']) && is_numeric($_POST['group_loan_id']) && $_POST['loan_type_id'] == 2) { //checking if it's true it is set to solidarity
                        if ($this->client_loan_model->delete_by_id()) { //Delete this pure loan from a client table before changing to solidarity
                            $this->load->model('group_loan_model');
                            if ($this->group_loan_model->update()) { //update the group loan table
                                $this->loan_state_model->set($_POST['id']);
                                $this->payment_details_model->deactivate($_POST['id']);
                                if ($this->input->post('preferred_payment_id') != NULL && $this->input->post('preferred_payment_id') != '1') {
                                    $this->payment_details_model->set($_POST['id']);
                                }
                                $feedback['success'] = true;
                                $feedback['message'] = "Loan application successfully updated";
                                $this->helpers->activity_logs($_SESSION['id'], 4, "Editing loan application", $feedback['message'] . " -# " . $this->input->post('loan_no'), NULL, $this->input->post('loan_no'));
                                $feedback['group_loan'] = 1; //redirect the user to the group loan
                            }
                        }
                    } else {
                        $feedback['message'] = "There was a problem updating the Loan application, please try again or get in touch with the admin";
                        $this->helpers->activity_logs($_SESSION['id'], 4, "Editing loan application", $feedback['message'] . " -# " . $this->input->post('loan_no'), NULL, $this->input->post('loan_no'));
                    }
                }
            } else {
                $loan_ref_no = $this->generate_loan_ref_no();
                if ($client_loan_id = $this->client_loan_model->set($loan_ref_no)) {
                    if ($this->loan_state_model->set($client_loan_id)) {
                        if ($this->input->post('preferred_payment_id') != NULL && $this->input->post('preferred_payment_id') != '1') {
                            $this->payment_details_model->set($_POST['id']);
                        }
                        $feedback['success'] = true;
                        $feedback['loan_ref_no'] = ++$loan_ref_no;
                        $feedback['message'] = "Loan application details successfully saved";
                        //activity log
                        $this->helpers->activity_logs($_SESSION['id'], 4, "Creating loan application", $feedback['message'] . " -# " . $this->input->post('loan_no'), NULL, $this->input->post('loan_no'));

                        if (isset($_POST['group_loan_id']) && is_numeric($_POST['group_loan_id'])) {
                            $this->load->model('group_loan_model');
                            $feedback['group_loan_details'] = $this->group_loan_model->get($_POST['group_loan_id']);
                            $feedback['members'] = $this->member_model->get_member_by_user_id("fms_member.id IN (SELECT member_id from fms_group_member WHERE status_id=1 AND group_id = ( SELECT group_id FROM fms_group_loan WHERE id = " . $_POST['group_loan_id'] . " ) AND member_id NOT IN  ( SELECT member_id from fms_client_loan WHERE group_loan_id = " . $_POST['group_loan_id'] . " ) AND status_id=1)");
                            $feedback['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id =" . $_POST['group_loan_id']);
                        } else {
                            $feedback['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id IS NULL");
                        }
                    } else {
                        $this->client_loan_model->delete_by_id($client_loan_id);
                        $feedback['message'] = "There was a problem saving the Loan application , please try again";

                        $this->helpers->activity_logs($_SESSION['id'], 4, "Saving loan application", $feedback['message'] . " -# " . $this->input->post('loan_no'), NULL, $this->input->post('loan_no'));
                    }
                } else {
                    $feedback['message'] = "There was a problem saving the Loan application";

                    $this->helpers->activity_logs($_SESSION['id'], 4, "Saving loan application", $feedback['message'] . " -# " . $this->input->post('client_loan_id'), NULL, $this->input->post('client_loan_id'));
                }
            }
        }
        echo json_encode($feedback);
    }



    public function create2()
    {
        //print_r($this->input->post('application_date')); die();
        $this->form_validation->set_rules('requested_amount', 'Requested amount', array('required'));
        $this->form_validation->set_rules('loan_product_id', 'Loan Product', array('required'));
        $this->form_validation->set_rules('credit_officer_id', 'Credit officer', array('required'));
        $this->form_validation->set_rules('interest_rate', 'Interest rate', array('required'));
        $this->form_validation->set_rules('grace_period', 'Grace period', array('required'));
        $this->form_validation->set_rules('offset_period', 'Offset period', array('required'));
        $this->form_validation->set_rules('offset_made_every', 'Offset made every', array('required'));
        $this->form_validation->set_rules('installments', 'Number of installments', array('required'));

        unset($_POST['add_guarantor']);

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
            $this->data['modules'] = array_column($this->data['module_list'], "module_id");
            $attached_savings_accounts = false;
            $member_guarantors = $this->input->post('member_guarantors');

            if ($this->input->post('member_guarantors')) {
                unset($_POST['member_guarantors']);
            }
            #loan application submision
            $loan_ref_no = $this->generate_loan_ref_no();
            $this->db->trans_begin();
            $client_loan_id = $this->client_loan_model->set($loan_ref_no);

            $_POST['member_guarantors'] = $member_guarantors;

            if (is_numeric($client_loan_id)) {

                #Loan state update
                $this->load->model('loan_state_model');
                $this->loan_state_model->set($client_loan_id);

                #adding client's income sources for a month
                if ($this->input->post('incomes') != NULL && $this->input->post('incomes') != '') {
                    $this->load->model('client_loan_monthly_income_model');
                    $this->client_loan_monthly_income_model->set2($client_loan_id);
                }
                #adding client's expende details
                if ($this->input->post('expenses') != NULL && $this->input->post('expenses') != '') {
                    $this->load->model('client_loan_monthly_expense_model');
                    $this->client_loan_monthly_expense_model->set2($client_loan_id);
                }

                #Adding guarantors
                if ($this->input->post('guarantors') != NULL && !empty($this->input->post('guarantors')) && $this->input->post('guarantors') != '') {

                    $this->loan_guarantor_model->set2($client_loan_id);
                } elseif ($this->input->post('topup_application') != NULL && $this->input->post('topup_application') == '1') {

                    $this->loan_guarantor_model->duplicate_entry($client_loan_id);
                }

                #Adding Members as guarantors i.e without savings or shares attached
                if ($this->input->post('member_guarantors') != NULL && !empty($this->input->post('member_guarantors')) && $this->input->post('member_guarantors') != '') {
                    $this->load->model('guarantor_model');
                    $this->guarantor_model->add($client_loan_id);
                }

                #adding the client's savings account
                if ($this->input->post('savingAccs') != NULL && $this->input->post('savingAccs') != '') {
                    $attached_savings_accounts = $this->input->post('savingAccs');
                    $this->load->model('loan_attached_saving_accounts_model');
                    $this->loan_attached_saving_accounts_model->set($client_loan_id);
                } elseif ($this->input->post('topup_application') != NULL && $this->input->post('topup_application') == '1') {
                    $attached_savings_accounts = $this->loan_attached_saving_accounts_model->get('a.loan_id=' . $this->input->post('client_loan_id'));
                    $this->load->model('loan_attached_saving_accounts_model');
                    $this->loan_attached_saving_accounts_model->duplicate_entry($client_loan_id);
                }

                #recording the approved loan details
                if ($this->input->post('loan_app_stage') == 1 || $this->input->post('loan_app_stage') == 2) {
                    $this->load->model('loan_approval_model');
                    $this->client_loan_model->approve($client_loan_id);
                    $this->loan_approval_model->set($client_loan_id);
                }

                #adding loan collateral
                $member_collaterals = array();

                if (($this->input->post('collaterals') != NULL && $this->input->post('collaterals') != '') || ($this->input->post('existing_collaterals') !== NULL && $this->input->post('existing_collaterals') !== '')) {

                    if ($this->input->post('collaterals') != NULL && $this->input->post('collaterals') != '') {

                        //need to upload
                        $organisation_id = isset($_SESSION['organisation_id']) ? $_SESSION['organisation_id'] : 'unknown';
                        $location = 'organisation_' . $organisation_id . '/loan_docs/collateral/';
                        $collaterals = $this->input->post('collaterals');
                        #multiple uploading
                        if (isset($_FILES)) {
                            foreach ($collaterals as $key => $value) {
                                if (empty($value['collateral_type_id'])) {
                                    continue;
                                }
                                $file[$key] = '';
                                if (isset($_FILES['file_name']['name']) && array_key_exists($key, $_FILES['file_name']['name'])) {
                                    $file_name = $_FILES['file_name']['name'][$key];
                                    if (!empty($file_name)) {
                                        $file[$key] = $this->do_upload($file_name, $location);
                                    }
                                }
                            }
                        }


                        foreach ($collaterals as $key => $value) {
                            if (empty($value['collateral_type_id'])) {
                                continue;
                            }
                            if (isset($file[$key])) {
                                $value['file_name'] = $file[$key];
                            }
                            $data['member_collateral_id'] = $this->member_collateral_model->add(false, $value);
                            $data['item_value'] = $value['item_value'];
                            $data['client_loan_id'] = $client_loan_id;
                            $member_collaterals[] = $data;
                        }
                    }

                    if ($this->input->post('existing_collaterals') !== NULL && $this->input->post('existing_collaterals') !== '') {
                        $existing_collaterals = $this->input->post('existing_collaterals');
                        foreach ($existing_collaterals as $key => $value) {
                            $data['member_collateral_id'] = $value['member_collateral_id'];
                            $data['item_value'] = $value['item_value'];
                            $data['client_loan_id'] = $client_loan_id;
                            $member_collaterals[] = $data;
                        }
                    }

                    #insertion in the db
                    if (!empty($member_collaterals)) {
                        $this->loan_collateral_model->set($member_collaterals);
                    }
                } elseif ($this->input->post('topup_application') != NULL && $this->input->post('topup_application') == '1') {

                    $this->loan_collateral_model->duplicate_entry($client_loan_id);
                }
                #attaching clients loan documents
                if ($this->input->post('loan_docs') != NULL && $this->input->post('loan_docs') != '') { //need to upload
                    $organisation_id = isset($_SESSION['organisation_id']) ? $_SESSION['organisation_id'] : 0;
                    $location = 'organisation_' . $organisation_id . '/loan_docs/other_docs/';
                    $loan_docs = $this->input->post('loan_docs');
                    #uploading the files
                    foreach ($loan_docs as $key => $value) {
                        $file[$key] = '';
                        if (array_key_exists($key, $_FILES['file_name']['name'])) {
                            $file_name = $_FILES['file_name']['name'][$key];
                            if (!empty($file_name)) {
                                $file[$key] = $this->do_upload($file_name, $location);
                            }
                        }
                    }
                    #db insertion
                    $this->load->model('client_loan_doc_model');
                    $this->client_loan_doc_model->set($client_loan_id, $file);
                } elseif ($this->input->post('topup_application') != NULL && $this->input->post('topup_application') == '1') {
                    $this->load->model('client_loan_doc_model');
                    $this->client_loan_doc_model->duplicate_entry($client_loan_id);
                }
                if ($this->input->post('preferred_payment_id') != NULL && $this->input->post('preferred_payment_id') != '1') {
                    $this->load->model('payment_details_model');
                    $this->payment_details_model->set($client_loan_id);
                }


                #adding loan schedule for this loan
                if ($this->input->post('repayment_schedule') != NULL && $this->input->post('repayment_schedule') != '' && $this->input->post('loan_app_stage') == 2) {


                    if ($this->input->post('linked_loan_id') != NULL && $this->input->post('linked_loan_id') != '') {
                        $filter['client_loan_id'] = $this->input->post('linked_loan_id');
                        $filter['state_id'] = 14;
                        $filter['comment'] = 'Loan closed due to a refinance';
                        $this->loan_state_model->set($filter);
                        $this->repayment_schedule_model->clear_installment($this->input->post('linked_loan_id'), 'refinance');
                    }
                    $this->repayment_schedule_model->set($client_loan_id);
                    $this->client_loan_model->update_source_fund($client_loan_id);
                    if ($attached_savings_accounts != false) {
                        $deduction_data['amount'] = $this->input->post('principal_value');
                        $deduction_data['transaction_type_id'] = 2;
                        $deduction_data['transaction_date'] = $this->input->post('action_date');
                        $deduction_data['account_no_id'] = $attached_savings_accounts[0]['saving_account_id'];
                        $deduction_data['narrative'] = 'LOAN DEPOSIT';
                        $transaction_data = $this->Transaction_model->deduct_savings($deduction_data);
                    }
                }
                #adding loan fees for this loan application
                if ($this->input->post('loanFees') != NULL && $this->input->post('loanFees') != '') {

                    $loanFees = $this->input->post('loanFees');
                    foreach ($loanFees as $key => $value) { //it is a new entry, so we insert afresh

                        if (isset($value['remove_or_not'])) {
                            unset($value['remove_or_not']);

                            $value['date_created'] = time();
                            $value['client_loan_id'] = $client_loan_id;
                            $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
                            $this->applied_loan_fee_model->set2($value);
                        }
                    }

                    if ((isset($this->data['modules']) &&  (in_array('6', $this->data['modules'])) && (in_array('5', $this->data['modules'])))) {
                        if ($this->input->post('preffered_payment_id') == 1) {
                            $charge_trigger_id = array('2', '3', '4');
                        } elseif ($this->input->post('preffered_payment_id') == 2) {
                            $charge_trigger_id = array('2', '3', '5');
                        } elseif ($this->input->post('preffered_payment_id') == 4) {
                            $charge_trigger_id = array('2', '3', '6');
                        } else {
                            $charge_trigger_id = array('2', '3', '4', '5', '6', '8', '9', '10');
                        }
                        $this->helpers->deduct_charges($client_loan_id, $charge_trigger_id, false, $this->input->post('action_date'));
                    } else {
                        $this->do_journal_transaction_loan_fees($this->input->post('action_date'), $client_loan_id);

                        if ((isset($this->data['modules']) &&  (in_array('6', $this->data['modules'])) && (in_array('5', $this->data['modules'])))) {
                            if ($this->input->post('preffered_payment_id') == 1) {
                                $charge_trigger_id = array('2', '3', '4');
                            } elseif ($this->input->post('preffered_payment_id') == 2) {
                                $charge_trigger_id = array('2', '3', '5');
                            } elseif ($this->input->post('preffered_payment_id') == 4) {
                                $charge_trigger_id = array('2', '3', '6');
                            } else {
                                $charge_trigger_id = array('2', '3', '4', '5', '6');
                            }
                            $this->helpers->deduct_charges($client_loan_id, $charge_trigger_id);
                        } else {
                            $this->do_journal_transaction_loan_fees($this->input->post('action_date'), $client_loan_id);
                        }
                    }

                    if ($attached_savings_accounts != false) {
                        $deduction_data['transaction_type_id'] = 1;
                        $deduction_data['transaction_date'] = $this->input->post('action_date');
                        $deduction_data['amount'] = $this->input->post('principal_value');
                        $deduction_data['narrative'] = 'LOAN WITHDRAW';
                        $transaction_data = $this->transaction_model->deduct_savings($deduction_data);
                        $this->do_journal_transaction($client_loan_id, $deduction_data);
                    } else {
                        $this->do_journal_transaction($client_loan_id);
                    }

                    // =====END ADDED ========

                    $feedback['active_loans'] = $this->client_loan_model->get_loans('loan_state.state_id=7 OR loan_state.state_id=13 OR loan_state.state_id=12');
                }

                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $feedback['success'] = true;
                    $feedback['loan_ref_no'] = ++$loan_ref_no;
                    $feedback['message'] = "Loan application details successfully saved";
                    if (isset($_POST['group_loan_id']) && is_numeric($_POST['group_loan_id'])) {
                        $this->load->model('group_loan_model');
                        $feedback['group_loan_details'] = $this->group_loan_model->get($_POST['group_loan_id']);
                        $feedback['members'] = $this->member_model->get_member_by_user_id("fms_member.id IN (SELECT member_id from fms_group_member WHERE status_id=1 AND group_id = ( SELECT group_id FROM fms_group_loan WHERE id = " . $_POST['group_loan_id'] . " ) AND member_id NOT IN  ( SELECT member_id from fms_client_loan WHERE group_loan_id = " . $_POST['group_loan_id'] . " ) AND status_id=1)");
                        $feedback['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id =" . $_POST['group_loan_id']);
                        //activity log
                        $this->helpers->activity_logs($_SESSION['id'], 4, "Creating loan application", $feedback['message'] . " -# " . $this->input->post('group_loan_id'), NULL, $this->input->post('group_loan_id'));
                    } else {
                        $feedback['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id IS NULL");
                        //activity log
                        $this->helpers->activity_logs($_SESSION['id'], 4, "Creating loan application", $feedback['message'] . " -# " . $this->input->post('group_loan_id'), NULL, $this->input->post('group_loan_id'));
                    }
                } else {
                    $this->db->trans_rollback();
                    $feedback['message'] = "There was a problem saving the Loan application, please try again";
                    //activity log
                    $this->helpers->activity_logs($_SESSION['id'], 4, "Creating loan application", $feedback['message'] . " -# " . $this->input->post('group_loan_id'), NULL, $this->input->post('group_loan_id'));
                }
            } else {
                $feedback['message'] = "There was a problem saving the Loan application";
                //activity log
                $this->helpers->activity_logs($_SESSION['id'], 4, "Creating loan application", $feedback['message'] . " -# " . $this->input->post('group_loan_id'), NULL, $this->input->post('group_loan_id'));
            }
        }
        echo json_encode($feedback);
    }
    public function do_journal_transaction($client_loan_id, $transaction_data = false)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('loan_product_model');
        $action_date = date('d-m-Y');
        if ($this->input->post('action_date') !== NULL) {
            $action_date = $this->input->post('action_date');
        }

        $client_loan = $this->client_loan_model->get_client_data($client_loan_id);

        if ($this->input->post('unpaid_principal') != NULL && $this->input->post('unpaid_principal') != '' && $this->input->post('unpaid_principal') != '0') {
            $principal_amount = round($this->input->post('principal_value') - $this->input->post('unpaid_principal'), 0);
        } else {
            $principal_amount = round($this->input->post('principal_value'), 2);
        }
        $data = [
            'transaction_date' =>  $action_date,
            'description' => strtoupper("Loan Disbursement on " . $action_date) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
            'ref_no' => $client_loan['loan_no'],
            'ref_id' => $client_loan_id,
            'status_id' => 1,
            'journal_type_id' => 4
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        //then we prepare the journal transaction lines
        if (!empty($client_loan)) {
            $this->load->model('accounts_model');
            $this->load->model('journal_transaction_line_model');
            $this->load->model('repayment_schedule_model');

            $loan_product_details = $this->loan_product_model->get_accounts($client_loan['loan_product_id']);

            $Loan_account_details = $this->accounts_model->get($loan_product_details['loan_receivable_account_id']);
            $source_fund_ac_details = $this->accounts_model->get($this->input->post('source_fund_account_id'));
            $Interest_receivable_ac_details = $this->accounts_model->get($loan_product_details['interest_receivable_account_id']);
            $Interest_income_ac_details = $this->accounts_model->get($loan_product_details['interest_income_account_id']);

            $debit_or_credit1 = ($Loan_account_details['normal_balance_side'] == 1) ? 'debit_amount' : 'credit_amount';
            $debit_or_credit2 = ($source_fund_ac_details['normal_balance_side'] == 1) ? 'credit_amount' : 'debit_amount'; //Although the normal balancing side is debit side, in this scenario money is being given out so we shall instead credit it.
            $debit_or_credit3 = ($Interest_income_ac_details['normal_balance_side'] == 1) ? 'debit_amount' : 'credit_amount';
            $debit_or_credit4 = ($Interest_receivable_ac_details['normal_balance_side'] == 1) ? 'debit_amount' : 'credit_amount';
            //for Top up loan purpose
            $debit_or_credit5 = ($Interest_receivable_ac_details['normal_balance_side'] == 1) ? 'credit_amount' : 'debit_amount';
            $debit_or_credit6 = ($Interest_income_ac_details['normal_balance_side'] == 1) ? 'credit_amount' : 'debit_amount';

            $index_key = 4;
            $interest_data = $this->repayment_schedule_model->get($client_loan_id);
            $data[0] = [
                'reference_no' =>  $client_loan['loan_no'],
                'reference_id' => $client_loan_id,
                'transaction_date' => $action_date,
                $debit_or_credit1 => $principal_amount,
                'narrative' =>  strtoupper("Loan Disbursement on " . $action_date) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
                'account_id' => $loan_product_details['loan_receivable_account_id'],
                'status_id' => 1
            ];
            $data[1] = [
                'reference_no' =>  $client_loan['loan_no'],
                'reference_id' => $client_loan_id,
                'transaction_date' => $action_date,
                $debit_or_credit2 => $principal_amount,
                'narrative' =>  strtoupper("Loan Disbursement on " . $action_date) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
                'account_id' => $this->input->post('source_fund_account_id'),
                'status_id' => 1
            ];

            if (isset($transaction_data['account_no_id']) && $transaction_data['account_no_id'] != '') { //used if money passes through member savings
                $savings_product_details = $this->savings_account_model->get($transaction_data['account_no_id']);
                $debit_or_credit7 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id']);
                $debit_or_credit8 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);

                $data[3] =  [
                    'reference_no' => $client_loan['loan_no'],
                    'reference_id' => $this->input->post('client_loan_id'),
                    'transaction_date' => $this->input->post('action_date'),
                    $debit_or_credit7 => $principal_amount,
                    'narrative' => strtoupper("Loan Disbursement on " . $this->input->post('action_date')),
                    'account_id' => $savings_product_details['savings_liability_account_id'],
                    'status_id' => 1
                ];
                $data[4] =  [
                    'reference_no' => $client_loan['loan_no'],
                    'reference_id' => $this->input->post('client_loan_id'),
                    'transaction_date' => $this->input->post('action_date'),
                    $debit_or_credit8 => $principal_amount,
                    'narrative' => strtoupper("Loan Disbursement on " . $this->input->post('action_date')),
                    'account_id' => $savings_product_details['savings_liability_account_id'],
                    'status_id' => 1
                ];
            }

            if ($this->input->post('linked_loan_id') != NULL && $this->input->post('linked_loan_id') != '' && $this->input->post('unpaid_interest') != NULL && $this->input->post('unpaid_interest') != '' && $this->input->post('unpaid_interest') != '0') {

                $parent_loan = $this->repayment_schedule_model->get('repayment_schedule.client_loan_id=' . $client_loan_id . ' AND status_id=1 AND payment_status=4');
                $unpaid_installments = '';
                foreach ($parent_loan as $key => $value) {
                    if (empty($unpaid_installments)) {
                        $unpaid_installments = $value['id'];
                    } else {
                        $unpaid_installments .= ',' . $value['id'];
                    }
                }
                $where_clause = "reference_id IN ($unpaid_installments) AND reference_no='" . $client_loan['loan_no'] . "'";
                $line_data['status_id'] = 3;

                $this->journal_transaction_line_model->update_status($line_data, $where_clause);
            }
            foreach ($interest_data as $key => $value) {
                $index_key += 2;
                $transaction_date = date('d-m-Y', strtotime($value['repayment_date']));
                $data[$index_key - 1] = [
                    'reference_no' => $client_loan['loan_no'],
                    'reference_id' => $value['id'],
                    'transaction_date' => $transaction_date,
                    $debit_or_credit3 => $value['interest_amount'],
                    'narrative' => strtoupper("Interest on Loan Disbursed on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
                    'account_id' => $loan_product_details['interest_income_account_id'],
                    'status_id' => 1
                ];

                $data[$index_key] =  [
                    'reference_no' => $client_loan['loan_no'],
                    'reference_id' => $value['id'],
                    'transaction_date' => $transaction_date,
                    $debit_or_credit4 => $value['interest_amount'],
                    'narrative' => strtoupper("Interest on Loan Disbursed on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
                    'account_id' => $loan_product_details['interest_receivable_account_id'],
                    'status_id' => 1
                ];
            }
            $this->journal_transaction_line_model->set($journal_transaction_id, $data);
            /*print_r($income_account_details);die($this->input->post('transaction_channel_id'));*/
        }
    }

    private function do_upload($file_name, $location, $max_size = 10048, $allowed_types = "docx|doc|gif|jpg|jpeg|png|pdf")
    {
        //uploading of the file
        if (!empty($file_name) && !empty($location)) {
            $config['upload_path'] = APPPATH . "../uploads/$location/";
            $document_name = $config['file_name'] = $file_name;
            $config['allowed_types'] = $allowed_types;
            $config['max_size'] = $max_size;
            $config['overwrite'] = true;
            $config['remove_spaces'] = false;
            $config['file_ext_tolower'] = false;
            $this->load->library('upload', $config);
            if (!$this->upload->do_multi_upload('file_name')) {
                return $this->upload->display_errors();
            } else {
                $this->upload->data();
                return $document_name;
            }
        }
    }

    public function interest_rate($loan_data)
    {
        //$repayment_frequency=intval(($loan_data['approved_repayment_frequency'])?$loan_data['approved_repayment_frequency']:$loan_data['repayment_frequency']);
        $repayment_made = ($loan_data['approved_repayment_made_every']) ? $loan_data['approved_repayment_made_every'] : $loan_data['repayment_made_every'];
        $interest_rate = $loan_data['interest_rate'];

        if ($repayment_made == 1) {
            $schedule_date = '% Per day';
            $repayment_made_every = '365';
        } elseif ($repayment_made == 2) {
            $schedule_date = '% Per Week';
            $repayment_made_every = '52';
        } else {
            $schedule_date = '% Per month';
            $repayment_made_every = '12';
        }

        $r = $interest_rate_per_annum = $interest_rate_per_installment = ($interest_rate * 1);

        $l = $length_of_a_period = (1 / $repayment_made_every);

        $i = $interest_rate_per_period = ($r * $l);
        return  round($i, 4) . $schedule_date;
    }

    public function view($loan_id_or_group_loan_id = false, $call_type = false)
    {
        $this->load->model("loan_doc_type_model");
        $this->load->model("loan_product_fee_model");
        $this->load->model("savings_account_model");
        $this->load->model("group_loan_model");

        $this->load->model('transactionChannel_model');
        $this->load->model('user_income_type_model');
        $this->load->model('user_expense_type_model');
        $this->load->model('RolePrivilege_model');
        $this->load->model('loan_installment_payment_model');
        $this->load->library(array("form_validation", "helpers"));
        if ($loan_id_or_group_loan_id == false && $call_type == false) {
            redirect("my404");
        } else if ($loan_id_or_group_loan_id != false && $call_type == false) {
            $this->data['loan_detail'] = $loan_data = $this->client_loan_model->get_client_loan($loan_id_or_group_loan_id);
            //echo json_encode($this->data['loan_detail']); die;
            // print_r($this->data['loan_detail']);die();
            if (empty($this->data['loan_detail'])) {
                redirect("my404");
            }

            $this->data['loan_detail']['calculated_interest_rate'] = $this->interest_rate($loan_data);
            $this->data['title'] = $this->data['loan_detail']['loan_no'] . ' - Loan Details ( <a href="' . base_url() . 'member/member_personal_info/' . $this->data['loan_detail']['member_id'] . '">' . $this->data['loan_detail']['member_name'] . "</a> )";
            $this->data['case2'] = 'client_loan';
            $this->data['modal_title'] = $this->data['loan_detail']['member_name'];
            if ($this->data['loan_detail']['group_loan_id'] != '') {
                $this->data['type'] = $data['sub_type'] = 'client_loan';
                $this->data['loanProducts'] = $this->loan_product_model->get_product("loan_product.status_id=1 AND loan_product.available_to_id=3 OR loan_product.available_to_id=2");
                $this->data['members'] = $this->member_model->get_member_by_user_id("fms_member.id IN (SELECT member_id from fms_group_member WHERE status_id=1 AND group_id = ( SELECT group_id FROM fms_group_loan WHERE id = " . $this->data['loan_detail']['group_loan_id'] . " ) AND status_id=1)");
                $this->data['group_loan_details'] = $this->group_loan_model->get($this->data['loan_detail']['group_loan_id']);
            } else {
                $this->data['type'] = $data['sub_type'] = 'client_loan';
                $this->data['members'] = $this->member_model->get_member_by_user_id("fms_member.status_id=1");
                $this->data['loanProducts'] = $this->loan_product_model->get_product("loan_product.available_to_id=3 OR loan_product.available_to_id=1");
            }

            $member_id = $this->data['loan_detail']['member_id'];
            $loan_id = $loan_id_or_group_loan_id;
            //loan details for payment
            $this->data['installments'] = $this->repayment_schedule_model->get("payment_status <> 1 AND repayment_schedule.status_id=1 AND client_loan_id=" . $loan_id);
            $this->data['active_loans'] = $this->client_loan_model->get_loans("(loan_state.state_id=7 OR loan_state.state_id=13 OR loan_state.state_id=12 ) AND a.id= " . $loan_id);

            //print_r($this->data['active_loans']); die();
            //End of the data

            //savings accounts
            $this->data['savings_accs'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ifnull(charges, 0) + ifnull( amount_locked, 0) ) > 0 and j.state_id = 7 AND a.client_type=1 AND member_id = '" . $member_id . "' ");
            $this->data['savings_accs_member'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ifnull(charges, 0) + ifnull( amount_locked, 0) ) > 0 and j.state_id = 7 AND a.client_type=1 AND member_id = " . $member_id);
        } else {
            $this->load->model("Group_model");
            $this->data['loan_detail'] = $this->client_loan_model->get_client_loan("a.group_loan_id= " . $loan_id_or_group_loan_id);
            if (empty($this->data['loan_detail'])) {
                redirect("my404");
            }
            // print_r($this->data['loan_detail']);die();

            $loan_id = $this->data['loan_detail']['id'];

            //loan details for payment
            $this->data['installments'] = $this->repayment_schedule_model->get("payment_status <> 1 AND repayment_schedule.status_id=1 AND client_loan_id=" . $loan_id);
            $this->data['active_loans'] = $this->client_loan_model->get_loans("(loan_state.state_id=7 OR loan_state.state_id=13 OR loan_state.state_id=12) AND a.group_loan_id= " . $loan_id_or_group_loan_id, 'group_loan'); //die;
            //End of the data

            $this->data['account_list'] = $this->accounts_model->get();
            $this->data['title'] = 'Group loan details';
            $this->data['type'] = $this->data['sub_type'] = 'client_loan';
            $this->data['case2'] = 'group_loan';
            $this->data['client_type'] = 2;
            $this->data['modal_title'] = $this->data['loan_detail']['group_name'];
            $this->data['group_loan_details'] = $this->group_loan_model->get($loan_id_or_group_loan_id);
            $group_id = $this->data['group_loan_details']['group_id'];
            $this->data['groups'] = $this->Group_model->get_group("status_id=1");
            $this->data['loanProducts'] = $this->loan_product_model->get_product("loan_product.status_id=1 AND loan_product.available_to_id=3 OR loan_product.available_to_id=2");
            $this->data['loan_type'] = $this->miscellaneous_model->get_loan_type();
            $this->data['members'] = $this->member_model->get_member_by_user_id("fms_member.id IN (SELECT member_id from fms_group_member WHERE status_id=1 AND group_id =" . $group_id . " AND member_id NOT IN  ( SELECT member_id from fms_client_loan WHERE group_loan_id = " . $this->data['group_loan_details']['id'] . " ) AND status_id=1)");
            /* $this->data['savings_accs'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0) +ifnull(charges, 0)+ ifnull( amount_locked, 0) ) > 0 and j.state_id = 7 AND a.client_type=2 AND member_id = '" . $group_id . "' AND a.id NOT IN "
                . "(SELECT saving_account_id from fms_loan_attached_saving_accounts WHERE loan_id = '" . $loan_id . "' )"); */
            //savings accounts
            $this->data['savings_accs'] = $this->loan_guarantor_model->get_guarantor_savings(" j.state_id = 7 AND a.client_type=2 AND member_id = '" . $group_id . "' ");
            $this->data['savings_accs_member'] = $this->loan_guarantor_model->get_guarantor_savings(" j.state_id = 7 AND a.client_type=2 AND member_id = " . $group_id);
        }
        $this->data['loan_detail']['total_penalty'] = $this->get_total_penalty($this->data['loan_detail']['id']);
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] = array_column($this->data['module_list'], "module_id");

        //data for payment purposes
        $this->data['account_list'] = $this->accounts_model->get();

        //end of the variables
        $this->template->title = $this->data['title'];
        $this->data['loan_doc_types'] = $this->loan_doc_type_model->get();
        $this->data['collateral_types'] = $this->loan_collateral_model->get_collateral_type();
        $this->data['relationship_types'] = $this->miscellaneous_model->get_relationship_type();
        $this->data['available_loan_range_fees'] = $this->loan_fees_model->get_range_fees();
        $this->data['staffs'] = $this->Staff_model->get_registeredby("status_id=1");
        $this->data['penalty_calculation_method'] = $this->penalty_calculation_method_model->get();
        $this->data['repayment_made_every'] = $this->miscellaneous_model->get();
        $this->data['guarantors'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + 
        ifnull( transfer ,0) +ifnull(charges, 0) + ifnull( amount_locked, 0) ) >= 0 and j.state_id = 7 AND a.client_type=1");
        $this->data['available_loan_fees'] = $this->loan_product_fee_model->get(" loanproduct_id = '" . $this->data['loan_detail']['loan_product_id'] . "' and fms_loan_fees.id not in 
        ( SELECT loan_product_fee_id from fms_applied_loan_fee WHERE client_loan_id = '" . $loan_id . "' and status_id = 1 ) ");

        $this->data['unpaid_loan_fees'] = $this->applied_loan_fee_model->get("a.status_id = 1 AND paid_or_not = 0 AND client_loan_id = " . $loan_id);
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 3');
        $this->data['pay_with'] = $this->accounts_model->get_pay_with("10");

        // print_r($this->data['unpaid_loan_fees']); die();
        // member_income and Expeniture
        $this->data['income_items'] = $this->user_income_type_model->get();
        $this->data['expense_items'] = $this->user_expense_type_model->get();
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 3');

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/datepicker/bootstrap-datepicker.js", "plugins/validate/jquery.validate.min.js", "plugins/printjs/print.min.js", "plugins/autoNumeric/autoNumeric.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/datepicker/datepicker3.css");
        $this->data['tchannel'] = $this->transactionChannel_model->get();
        //print_r($this->data['loan_detail']); die();

        // get unpaid loan balance
        $_POST['client_loan_id'] = $loan_id;
        $_POST['status_id'] = 1;
        $loan_payments = $this->loan_installment_payment_model->get();


        if (!empty($loan_payments)) {
            $this->data['loan_balance'] = $loan_payments[0]['end_balance'];
        }
        unset($_POST['client_loan_id']);
        unset($_POST['status_id']);

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->content->view('client_loan/loan_detail', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function change_status()
    {
        if (in_array('7', $this->privileges)) {
            $this->data['success'] = FALSE;
            $this->data['message'] = $this->client_loan_model->change_status_by_id();
            if ($this->data['message'] === true) {
                $this->data['success'] = TRUE;
                $this->data['message'] = "Loan application data successfully deactivated.";
            }
        } else {
            $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        }
        echo json_encode($this->data);
    }

    public function delete()
    {
        $response['success'] = FALSE;
        if ($this->client_loan_model->delete_by_id()) {
            $response['success'] = TRUE;
            $response['message'] = "Data successfully deleted.";
            //activity log
            $this->helpers->activity_logs($_SESSION['id'], 4, "Deleting Client Loan", $response['message'] . " -# ", null, NULL, null);
        }
        echo json_encode($response);
    }

    //checking requirements for approving a loan
    public function get_approval_data()
    {
        //Model for loading

        $this->load->model('approving_staff_model');
        $this->load->model('loan_approval_setting_model');

        $response['success'] = FALSE;
        $loan_id = $this->input->post('id');
        $requested_amount = $this->input->post('requested_amount');
        if (!(empty($loan_id))) {
            $this->data['collateral_sum'] = $this->loan_collateral_model->sum_loan_collateral($loan_id);
            $this->data['guarantor_count'] = $this->loan_guarantor_model->count_loan_guarantor($loan_id);
            $this->data['share_guarantor_count'] = $this->loan_guarantor_model->count_loan_share_guarantor($loan_id);
            $this->data['staff_approval_data'] = $this->approving_staff_model->get(intval($requested_amount), $loan_id);
            $this->data['min_approvals'] = $this->loan_approval_setting_model->min_approvals(intval($requested_amount));
            $this->data['staff_list'] = $this->approving_staff_model->approval_staff_list($requested_amount, $loan_id);


            //share guarantor

            // print_r($this->data['share_guarantor_count']);die();

            $member_id = $this->input->post('member_id');
            $this->data['account_shares'] = $this->shares_model->get_share_guarantor("share_account.member_id=" . $member_id);
            //print_r($this->data['account_shares']);die();

            $this->load->model('loan_product_model');
            if (!(empty($member_id)) && $member_id != '') {
                $this->data['account_savings'] = $this->loan_guarantor_model->get_guarantor_savings("(j.state_id = 7) AND a.client_type=1 AND a.member_id=" . $member_id);
                $this->data['product_details'] = $this->loan_product_model->get_product($this->input->post('loan_product_id'));
            } else {
                $this->load->model('group_loan_model');
                $group_loan_id = $this->input->post('group_loan_id');
                $this->data['group_loan_details'] = $this->group_loan_model->get($group_loan_id);
                $this->data['account_savings'] = $this->loan_guarantor_model->get_guarantor_savings("(j.state_id = 7) AND a.client_type=2 AND a.member_id=" . $this->data['group_loan_details']['group_id']);
                $this->data['product_details'] = $this->loan_product_model->get_product($this->data['group_loan_details']['loan_product_id']);
            }
            $response['success'] = True;
            $response['approval_data']['rank'] = $this->data['staff_approval_data']['rank'];

            $response['approval_data']['approval_status'] = $this->data['staff_approval_data']['approved_or_not'];
            $response['approval_data']['collateral_sum'] = floatval($this->data['collateral_sum']['loan_collateral_value']);
            $response['approval_data']['guarantor_count'] = intval($this->data['guarantor_count']['loan_guarantor'] + $this->data['share_guarantor_count']['loan_share_guarantor']);
            $response['approval_data']['guarantor_amount_locked_sum'] = intval($this->data['guarantor_count']['loan_guarantor_value'] + $this->data['share_guarantor_count']['loan_share_guarantor_value']);
            $response['approval_data']['share_details'] = $this->data['account_shares'];
            $response['approval_data']['savings_details'] = $this->data['account_savings'];
            $response['approval_data']['staff_list'] = $this->data['staff_list'];
            $response['approval_data']['min_approvals'] = $this->data['min_approvals']['required_approvals'];
            $response['selected_product'] = $this->data['product_details'];
            $total = $total2 = 0;
            foreach ($this->data['account_savings'] as $key => $value) {
                $total += $value['real_bal'];
            }
            foreach ($this->data['account_shares'] as $key => $value) {
                $total2 += $value['total_amount'];
            }
            $response['approval_data']['savings_sum'] = $total;
            $response['approval_data']['shares_sum'] = $total2;
            //print_r($response['approval_data']); die();
        }

        echo json_encode($response);
    }

    //End of the get_approval_data function

    # Get requestable min & max amounts
    public function get_requestable_loan_amounts()
    {

        $member_id = $this->input->post('member_id');
        $amount = $this->input->post('amount');
        $this->data['account_shares'] = $this->shares_model->get_share_guarantor("share_account.member_id=" . $member_id);
        $this->data['account_savings'] = $this->loan_guarantor_model->get_guarantor_savings("(j.state_id = 7) AND a.client_type=1 AND a.member_id=" . $member_id);

        $product_details = $this->loan_product_model->get_product($this->input->post('loan_product_id'));
        //echo json_encode($product_details); die;

        $min_collateral_percentage = $product_details['min_collateral'];
        $product_max = $product_details['max_amount'];
        $product_min = $product_details['min_amount'];

        $savings_sum = $shares_sum = 0;
        $col_total = 0;
        $max = 0;

        foreach ($this->data['account_savings'] as $key => $value) {
            $savings_sum += $value['real_bal'];
        }

        foreach ($this->data['account_shares'] as $key => $value) {
            $shares_sum += $value['total_amount'];
        }

        if (intval($product_details['use_savings_as_security']) == 1) {
            $col_total += $savings_sum;
        }

        if (intval($product_details['use_shares_as_security']) == 1) {
            $col_total += $shares_sum;
        }

        if (intval($product_details['mandatory_sv_or_sh']) == 1) {
            $max = $col_total / ($min_collateral_percentage / 100);

            if ($max < $product_max) {
                $product_max = $max;
            }
        }

        $data = [
            'min' => $product_min,
            'max' => $product_max,
            'savings_total' => $savings_sum,
            'shares_total' => $shares_sum
        ];

        if (!empty($amount)) {
            // calculate Needed collateral for Amount
            $needed_col_total = $amount * ($min_collateral_percentage / 100);
            $needed_col = $needed_col_total <= $col_total ? 0 : ($needed_col_total - $col_total);
            if (intval($product_details['mandatory_sv_or_sh']) == 1) {
                $data['needed_col'] = $needed_col;
            }
        }



        echo json_encode($data);
    }


    //disbursement
    public function disbursement()
    {
        $response['success'] = FALSE;
        $loan_id = $this->input->post('id');
        if (!empty($this->input->post('action_date'))) { //in case disbursement happen before today
            $new_date = $this->input->post('action_date');
            $now_date = date('d-m-Y', strtotime($new_date));
        } elseif ($this->input->post('new_repayment_date') !== NULL) { //in the case of reschedule a loan & loan Amortization
            $now_date = $this->input->post('new_repayment_date');
            $new_date = $now_date = date('d-m-Y', strtotime($now_date));
        } else { //in case disbursement is happening today           
            $now_date = date('d-m-Y');
        }

        if ($this->input->post('loan_product_id') !== NULL && empty($loan_id)) { //useful for loan amortization
            $this->data['loan_data'] = $loan_data = $this->loan_product_model->get_product($_POST['loan_product_id']);
        } else { //finding the payment reschedule
            $this->data['loan_data'] = $loan_data = $this->repayment_schedule_model->get_loan_data_penalty($loan_id);
        }
        $response = $this->parameter_construction($now_date);
        $response['available_loan_fees'] = $this->loan_product_fee_model->get(" loanproduct_id = '" . $this->input->post('loan_product_id') . "' and fms_loan_fees.id not in 
        ( SELECT loan_product_fee_id from fms_applied_loan_fee WHERE client_loan_id = '" . $this->input->post('id') . "' and status_id = 1 ) ");

        echo json_encode($response);
    } //End of the disbursement function

    public function disbursement1()
    { //Used when loan is from application to active state
        $response['success'] = FALSE;
        if (!empty($this->input->post('action_date1'))) { //in case disbursement happen before today
            $new_date = $this->input->post('action_date1');
            $now_date = date('d-m-Y', strtotime($new_date));
        } else { //in case disbursement is happening today           
            $now_date = date('d-m-Y');
        }
        $this->data['loan_data']['offset_period'] = $this->input->post('offset_period1');
        $this->data['loan_data']['offset_made_every'] = $this->input->post('offset_made_every1');
        $this->data['loan_data']['amount_approved'] = $this->input->post('amount1');
        $this->data['loan_data']['product_type_id'] = $this->input->post('product_type_id1');
        $this->data['loan_data']['interest_rate'] = $this->input->post('interest_rate1');
        $this->data['loan_data']['approved_installments'] = $this->input->post('installments1');
        $this->data['loan_data']['approved_repayment_made_every'] = $this->input->post('repayment_made_every1');
        $this->data['loan_data']['approved_repayment_frequency'] = $this->input->post('repayment_frequency1');
        $this->data['loan_data']['loan_product_id'] = $this->input->post('loan_product_id1');
        $response = $this->parameter_construction($now_date);
        $response['available_loan_fees'] = $this->loan_product_fee_model->get(" loanproduct_id = '" . $this->input->post('loan_product_id1') . "' and fms_loan_fees.id not in 
        ( SELECT loan_product_fee_id from fms_applied_loan_fee WHERE client_loan_id = '" . $this->input->post('id') . "' and status_id = 1 ) ");

        echo json_encode($response);
    }

    private function parameter_construction($now_date, $loan_data = false)
    {
        //print_r($this->data['loan_data']);die;
        //Repayment Frequency
        if ($loan_data) {
            $this->data['loan_data'] = $loan_data;
        }
        if (!empty($this->input->post('repayment_frequency'))) {
            $repayment_frequency = $this->input->post('repayment_frequency');
        } else {
            $repayment_frequency = intval($this->data['loan_data']['approved_repayment_frequency']);
        }

        //Repayment Period either day, weeks or months
        if (!empty($this->input->post('repayment_made_every'))) { //if new repayment made every has been sent
            $repayment_made = $this->input->post('repayment_made_every');
            //determination of the first payment date
            $payment_date = $now_date = strtotime($now_date);
            $payment_date1 = date('Y-m-d', $payment_date);
        } else { //consideration of offset period incase of disbursement
            $repayment_made = intval($this->data['loan_data']['approved_repayment_made_every']);
            if ($this->data['loan_data']['offset_made_every'] == 1) {
                $off_set = '+' . $this->data['loan_data']['offset_period'] . ' day';
            } elseif ($this->data['loan_data']['offset_made_every'] == 2) {
                $off_set = '+' . $this->data['loan_data']['offset_period'] . ' week';
            } else {
                $off_set = '+' . $this->data['loan_data']['offset_period'] . ' month';
            }
            //determination of the first payment date
            $payment_date = $now_date = strtotime($off_set, strtotime($now_date));
            $payment_date1 = date('Y-m-d', $payment_date);
        }

        $compute_interest_from_disbursement_date = intval($this->input->post('compute_interest_from_disbursement_date'));

        if ($compute_interest_from_disbursement_date == 1) {
            # get loan disbursement date
            $loan_disbursement_date_data = $this->client_loan_model->get_loan_disbursement_date($this->input->post('id'));
            if (!empty($loan_disbursement_date_data)) {
                $loan_disbursement_date = $loan_disbursement_date_data['disbursement_date'];

                //get Date diff as intervals 
                $d1 = new DateTime($loan_disbursement_date . " 00:00:00");
                $d2 = new DateTime($payment_date1 . " 23:59:59");

                $interval = $d1->diff($d2);
                $diffInDays    = $interval->d;
                $diffInMonths  = $interval->m;
            }
        }

        if ($repayment_made == 1) {
            $schedule_date = $repayment_frequency . ' day';
            $repayment_made_every1 = '365';
            $repayment_made_every2 = $compute_interest_from_disbursement_date == 1 ? $diffInDays : '';
        } elseif ($repayment_made == 2) {
            $schedule_date = $repayment_frequency . ' week';
            #$repayment_made_every = '48';
            $repayment_made_every1 = '48';
            $repayment_made_every2 = $compute_interest_from_disbursement_date == 1 ? intdiv($diffInDays, 7) : '';
        } else {
            $schedule_date = $repayment_frequency . ' month';
            #$repayment_made_every = '12';
            $repayment_made_every1 = '12';
            $repayment_made_every2 = $compute_interest_from_disbursement_date == 1 ? $diffInMonths : '';
        }
        //if new interest rate has been sent
        if (!empty($this->input->post('interest_rate'))) {
            $interest_rate = $this->input->post('interest_rate');
        } else {
            $interest_rate = $this->data['loan_data']['interest_rate'];
        }

        # Get Interest Brought Forward from Parent incase of topup
        $interest_brought_forward = $this->client_loan_model->get_interest_brought($this->input->post('id'));

        //if current installment has been sent
        if ((!empty($this->input->post('installments'))) && (!empty($this->input->post('current_installment')))) {

            $installment_number = $this->input->post('current_installment');
            $where = "fms_repayment_schedule.client_loan_id=" . $this->input->post('id') . " AND fms_repayment_schedule.installment_number >= $installment_number AND fms_repayment_schedule.status_id=1";
            $this->data['amount_left'] = $this->repayment_schedule_model->sum_interest_principal($where);
            $n = $number_of_installments = $this->input->post('installments');
            $p = $Original_Loan_Amount = $outstanding_loan_amount = floatval($this->data['amount_left']['principal_sum']);
        } elseif (!empty($this->input->post('amount'))) {
            $n = $number_of_installments = $this->input->post('installments');
            $p = $Original_Loan_Amount = $outstanding_loan_amount = floatval($this->input->post('amount'));
        } else {
            if (isset($this->data['loan_data']['disbursed_amount']) && $this->data['loan_data']['disbursed_amount'] != 0) {
                $p = $Original_Loan_Amount = $outstanding_loan_amount = ( floatval($this->data['loan_data']['amount_approved']) + (floatval($this->data['loan_data']['disbursed_amount']) - floatval($this->data['loan_data']['parent_paid_principal'])) ) + ( floatval($this->data['loan_data']['parent_expected_interest']) - floatval($this->data['loan_data']['parent_paid_interest']) );
            } else {
                $p = $Original_Loan_Amount = $outstanding_loan_amount = floatval($this->data['loan_data']['amount_approved']);
            }
            $n = $number_of_installments = $this->data['loan_data']['approved_installments'];
        }

        if (!empty($_POST['top_up_amount'])) {
            $p = $Original_Loan_Amount = $outstanding_loan_amount = $p + $_POST['top_up_amount'];
        }
        $r = $interest_rate_per_annum = $interest_rate_per_installment = ((($interest_rate) * 1) / 100);
        if ($compute_interest_from_disbursement_date == 1) {
            $l = $length_of_a_period = ($repayment_frequency / $repayment_made_every2);
        } else {
            $l = $length_of_a_period = ($repayment_frequency / $repayment_made_every1);
        }
        $i = $interest_rate_per_period = ($r * $l);
        $number_of_years = $n * $l;
        //if current installment has been sent
        if ($this->input->post('current_installment') != NULL) {
            $installment_counter = $this->input->post('current_installment') - 1;
            $current_installment = $this->input->post('current_installment');
        } else {
            $installment_counter = 0;
            $current_installment = NULL;
        }
        $support_data = array(
            'p' => ($p + $interest_brought_forward), 'n' => $n,
            'r' => $r, 'i' => $i,
            'number_of_years' => $number_of_years,
            'installment_counter' => $installment_counter,
            'current_installment' => $current_installment,
            'product_type_id' => $this->data['loan_data']['product_type_id'],
            'schedule_date' => $schedule_date,
            'payment_date' => $payment_date,
            'payment_date1' => $payment_date1
        );

        $response = $this->schedule_calculation($support_data);
        return $response;
    }

    private function schedule_calculation($required_data)
    {
        $response = [];
        if (!(empty($required_data))) {
            $response['success'] = True;

            $response = $this->loan_schedule_generation->generate($required_data);

            $dStart = new DateTime($response['payment_date1']);
            $dEnd = new DateTime($response['payment_date2']);
            $dDiff = $dStart->diff($dEnd);
            $y = ($dDiff->y) ? ((($dDiff->y) > 1) ? $dDiff->y . ' years ' : $dDiff->y . ' year ') : '';
            $m = ($dDiff->m) ? ((($dDiff->m) > 1) ? $dDiff->m . ' months ' : $dDiff->m . ' month ') : '';
            $d = ($dDiff->d) ? ((($dDiff->d) > 1) ? $dDiff->d . ' days ' : $dDiff->d . ' day ') : '';
            $response['payment_summation']['payment_date'] = $y . $m . $d;
        }
        //$response['balance_message']=$this->helpers->account_balance($this->input->post('fund_source_account_id'),$this->input->post('amount_approved'));

        return $response;
    }
    public function delete_loan_doc()
    {
        $response['message'] = "Data could not be deleted, document support.";
        $response['success'] = FALSE;
        if ($this->client_loan_doc_model->delete_by_id()) {
            $response['success'] = TRUE;
            $response['message'] = "Data successfully deleted.";
        }
        echo json_encode($response);
    }

    //Approving a loan application
    public function approve()
    {
        $this->load->model('loan_approval_model');
        $this->load->model('loan_approval_setting_model');
        $response['message'] = "Loan application could not be approved, contact IT support.";
        $response['success'] = FALSE;


        if ($this->client_loan_model->approve()) {
            $inserted_id = $this->loan_approval_model->set();
            if (is_numeric($inserted_id)) {
                $requested_amount = $this->input->post('requested_amount');
                $loan_id = $this->input->post('client_loan_id');
                //commited approvals
                $this->data['approvals'] = $this->loan_approval_model->sum_approvals($loan_id);
                $this->data['staff_approvals'] = $this->loan_approval_model->get($loan_id);
                $approvals = $this->data['approvals']['approvals'];
                //required approvals
                $this->data['required_approvals'] = $this->loan_approval_setting_model->min_approvals(intval($requested_amount));
                $required_approvals = $this->data['required_approvals']['required_approvals'];
                //checking approvals requirements
                $this->data['chairman_approved'] = false;
                foreach ($this->data['staff_approvals'] as $key => $value) {
                    if ($value['rank'] == 1) {
                        $this->data['chairman_approved'] = true;
                        break;
                    }
                }

                //echo json_encode($this->data['chairman_approved']); die;

                if (($approvals >= $required_approvals) && $this->data['chairman_approved']) {
                    $this->load->model('loan_state_model');
                    if ($this->loan_state_model->set()) {
                        $response['success'] = TRUE;
                        $response['message'] = "Loan application successfully approved.";
                        if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] != '') {
                            $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['client_loan_id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);

                            $this->helpers->activity_logs($_SESSION['id'], 14, "Loan approval", $response['message'] . " -# " . $this->input->post('client_loan_id'), NULL, $this->input->post('client_loan_id'));
                        } else {
                            $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
                            $message = "Your loan with loan number " . $response['client_loan']['loan_no'] . " has been approved today on " . date('d-m-Y') . " your to recieve it soon";
                            $email_response = $this->helpers->send_email($this->input->post('client_loan_id'), $message);
                            if (!empty($result = $this->miscellaneous_model->check_org_module(22))) {
                                $message = $message . "." . $this->organisation . ", Contact " . $this->contact_number;
                                $text_response = $this->helpers->notification($this->input->post('client_loan_id'), $message);
                                $response['message'] = $response['message'] . $text_response;
                            }
                        }
                    } else {
                        $this->loan_approval_model->delete_by_id($inserted_id);

                        $feedback['message'] = "There was a problem approving this loan application, please try again";
                        $this->helpers->activity_logs($_SESSION['id'], 4, "Loan approval", $feedback['message'] . " -# " . $this->input->post('client_loan_id'), NULL, $this->input->post('client_loan_id'));
                    }
                } else {
                    $members = $required_approvals - 1;
                    if (($approvals == $members) && $this->data['chairman_approved'] == false) {
                        $this->email_approving_staffs($this->input->post('client_loan_id'), $this->input->post('amount_approved'), 1);
                    }
                    $response['success'] = TRUE;
                    $response['message'] = "Loan application successfully approved.";
                    $response['state_totals'] = $this->client_loan_model->state_totals();

                    if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] != '') {
                        $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['client_loan_id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);

                        $this->helpers->activity_logs($_SESSION['id'], 14, "Loan approval", $response['message'] . " -# " . $this->input->post('group_loan_id'), NULL, $this->input->post('group_loan_id'));
                    } else {
                        $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);

                        $this->helpers->activity_logs($_SESSION['id'], 4, "Loan approval", $response['message'] . " -# " . $this->input->post('client_loan_id'), NULL, $this->input->post('client_loan_id'));
                    }
                }
            } else {
                $response['message'] = "Loan application could not be approved!";
                $this->helpers->activity_logs($_SESSION['id'], 4, "Loan approval", $response['message'] . " -# " . $this->input->post('group_loan_id'), NULL, $this->input->post('group_loan_id'));
            }
        } else {
            $response['message'] = "Loan application could not be approved!";
            $this->helpers->activity_logs($_SESSION['id'], 14, "Loan approval", $response['message'] . " -# " . $this->input->post('client_loan_id'), NULL, $this->input->post('client_loan_id'));
        }

        echo json_encode($response);
    }

    public function email_approving_staffs($loan_id, $requested_amount, $type)
    {
        $where_clause = round($requested_amount) . " >=loan_approval_setting.min_amount AND " . round($requested_amount) . " <=loan_approval_setting.max_amount AND rank=1";
        $approving_staffs = $this->approving_staff_model->get($where_clause, $loan_id);

        if (is_array($approving_staffs) && $approving_staffs != '') {
            foreach ($approving_staffs as $key => $approving_staff) {
                if ($approving_staff['email']) {
                    if ($approving_staff['email'] != '') {
                        $this->org_site_url = base_url();
                        $message = "Dear " . ucfirst(strtolower($approving_staff['firstname'])) . ",<br><br>" . " Loan no <b>" . $this->input->post('loan_no') . "</b> of amount <b>" . number_format($this->input->post('requested_amount'), 2) . "</b> has been forwarded for your approval.<br/> You can access the loan by clicking this link " . $this->org_site_url;
                        $subject = "Loan approval request - " . $this->input->post('loan_no');

                        $this->helpers->send_multiple_email2(1, $approving_staff['email'], $message, $subject);
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }
    public function get_loan_account_no()
    {
        $this->load->library("num_format_helper");
        $new_loan_acc_no = $this->num_format_helper->new_loan_acc_no();
        $data['data'] = ['new_account_no' => $new_loan_acc_no];
        echo json_encode($data);
    }
    public function generate_loan_ref_no()
    {
        $this->load->library("num_format_helper");
        $new_loan_acc_no = $this->num_format_helper->new_loan_acc_no();
        return $new_loan_acc_no === FALSE ? $this->input->post("loan_no") : $new_loan_acc_no;
    }

    public  function pdf_appraisal($loan_id, $transaction_no = false)
    {
        $this->load->model('address_model');
        $this->load->model('children_model');
        $this->load->model('nextOfKin_model');
        $this->load->model('employment_model');
        $this->load->model('client_loan_monthly_expense_model');
        $this->load->model('client_loan_monthly_income_model');

        $this->load->helper('pdf_helper');

        $data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = "Loan disbursed details";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 8;
        $where = FALSE;
        $where = "a.status_id = 1";
        $where = ($where ? $where . " AND " : "") . " a.client_loan_id = " . $loan_id;
        $data['loan_guarantors'] = $this->loan_guarantor_model->get($where);
        $data['loan_detail'] = $this->client_loan_model->get_client_loan($loan_id);
        $user_id = $data['loan_detail']['user_id'];
        $member_id = $data['loan_detail']['member_id'];
        $data['loan_detail_prev'] = $this->client_loan_model->get_prev_client_loan($member_id);
        $data['addresses'] = $this->address_model->get_addresses("ua.id in (select max(id) from fms_user_address)  
        and ua.user_id=" . $user_id);
        $data['nextofkins'] = $this->nextOfKin_model->get($user_id);
        $data['children'] = $this->children_model->get($member_id);
        $data['employments'] = $this->employment_model->get($user_id);
        $data['repayment_schedules'] = $this->repayment_schedule_model->get($loan_id);
        $data['guarantors'] = $this->loan_guarantor_model->get('a.client_loan_id=' . $loan_id);
        $data['collaterals'] = $this->loan_collateral_model->get("client_loan_id=" . $loan_id);
        $data['users'] = $this->member_model->get_member($member_id);
        $data['applied_fees'] = $this->applied_loan_fee_model->get();
        $data['monthly_incomes'] = $this->client_loan_monthly_income_model->get('a.client_loan_id=' . $loan_id);
        $data['monthly_expenses'] = $this->client_loan_monthly_expense_model->get('a.client_loan_id=' . $loan_id);
        //echo '<pre>'; print_r( $data['monthly_incomes'] ).'</pre>'.die;
        $data['the_page_data'] = $this->load->view('client_loan/states/partial/pdf_appraisal', $data, TRUE);
        $this->load->view('includes/pdf_template', $data);
    }

    public function pdf_loan_fact_sheet($loan_id, $transaction_no = false)
    {

        $this->load->model('Business_model');
        $this->load->helper('pdf_helper');

        $data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = "Loan disbursed details";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 8;
        $where = FALSE;
        $where = "a.status_id = 1";
        $where = ($where ? $where . " AND " : "") . " a.client_loan_id = " . $loan_id;
        $data['loan_guarantors'] = $this->loan_guarantor_model->get($where);
        $data['loan_detail'] = $this->client_loan_model->get_client_loan($loan_id);
        $user_id = $data['loan_detail']['user_id'];
        $member_id = $data['loan_detail']['member_id'];
        $data['repayment_schedules'] = $this->repayment_schedule_model->get($loan_id);
        $data['repayment_schedule'] = isset($data['repayment_schedules'][0]) ? $data['repayment_schedules'][0] : [];
        $data['guarantors'] = $this->loan_guarantor_model->get('a.client_loan_id=' . $loan_id);
        $data['collaterals'] = $this->loan_collateral_model->get("client_loan_id=" . $loan_id);
        $data['business'] = $this->Business_model->get($member_id);
        // echo '<pre>'; print_r( $data['business'] ).'</pre>'.die;
        $data['the_page_data'] = $this->load->view('client_loan/states/approved/pdf_loan_fact_sheet', $data, TRUE);
        $this->load->view('includes/pdf_template', $data);
    }



    public function import()
    {
        $preferred_payment = ["CASH" => 1, "BANK" => 2, "MOBILE MONEY" => 3];
        $periods = ["DAYS" => 1, "WEEKS" => 2, "MONTHS" => 3];
        $topUp = ["N" => 0, "Y" => 1];

        if (isset($_FILES["file"]["name"])) {
            $path = $_FILES["file"]["tmp_name"];
            $object = PHPExcel_IOFactory::load($path);

            $failed = $passed = 0;
            $failed_data = array();
            foreach ($object->getWorksheetIterator() as $worksheet_check) {
                $gethighestRow = $worksheet_check->getHighestRow();
                $getSheetByName = $worksheet_check->getTitle();
                $gethighestColumn = $worksheet_check->getHighestColumn();
                for ($row = 2; $row <= $gethighestRow; $row++) {
                    $client_loan_no = $worksheet_check->getCellByColumnAndRow(0, $row)->getValue();
                    $member_id = $worksheet_check->getCellByColumnAndRow(1, $row)->getValue();
                    $trandate = explode('-',  $worksheet_check->getCellByColumnAndRow(6, $row)->getValue(), 3);
                    $action_date = count($trandate) === 3 ? ($trandate[2] . "-" . $trandate[1] . "-" . $trandate[0] . " " . date("H:i:s")) : '';


                    $member_details = array('' => "");

                    if (!empty($member_id)) {
                        $member_details = $this->member_model->get_member_info($client_loan_no);
                    }
                    if (empty($action_date) || (empty($member_details))) {
                        if (empty($member_id)) {
                            $message = "Member/Customer id is missing. Row Number ( " . $row . " )";
                        } else if (empty($member_details)) {
                            $message = "Client/Customer does not EXIST. Row Number ( " . $row . " )";
                        } else {
                            $message = "Something is wrong with this record. Row Number ( " . $row . " )";
                        }
                        $failed_data[] = array(
                            'row_id'         =>  $row,
                            'loan_no'  =>  $client_loan_no,
                            'customer_id'  =>  $member_id,
                            'application_date'    =>  $action_date,
                            'message'        =>  $message
                        );

                        $failed++;
                    }
                }
            }

            if (empty($failed_data)) {
                foreach ($object->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow();
                    $highestColumn = $worksheet->getHighestColumn();
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $client_loan_no = $worksheet_check->getCellByColumnAndRow(0, $row)->getValue();
                        $amount = $worksheet_check->getCellByColumnAndRow(2, $row)->getValue();
                        $member_id = $worksheet_check->getCellByColumnAndRow(1, $row)->getValue();
                        $trandate = explode('-',  $worksheet_check->getCellByColumnAndRow(6, $row)->getValue(), 3);
                        $action_date = count($trandate) === 3 ? ($trandate[2] . "-" . $trandate[1] . "-" . $trandate[0] . " " . date("H:i:s")) : '';
                        $top_up = ($worksheet_check->getCellByColumnAndRow(21, $row)->getValue()) ? $worksheet_check->getCellByColumnAndRow(21, $row)->getValue() : 'N';
                        $loan_product_id = ($worksheet_check->getCellByColumnAndRow(23, $row)->getValue()) ? $worksheet_check->getCellByColumnAndRow(23, $row)->getValue() : 1;
                        $repayment_frequency = ($worksheet_check->getCellByColumnAndRow(9, $row)->getValue()) ? $worksheet_check->getCellByColumnAndRow(9, $row)->getValue() : $worksheet_check->getCellByColumnAndRow(7, $row)->getValue();
                        $repayment_made_every = ($worksheet_check->getCellByColumnAndRow(10, $row)->getValue()) ? $worksheet_check->getCellByColumnAndRow(10, $row)->getValue() : 3;
                        $data = array(
                            "member_id" =>  $member_id + 1,
                            "loan_no" => $client_loan_no,
                            "credit_officer_id" => 2, //static since he is alone
                            "loan_product_id" => $loan_product_id,
                            "topup_application" => $topUp[$top_up],
                            "requested_amount" => $amount,
                            "application_date" => $action_date,
                            "source_fund_account_id" => $loan_product_data['fund_source_account_id'],
                            "disbursement_date" => $action_date,
                            "suggested_disbursement_date" => $action_date,
                            "interest_rate" => $worksheet_check->getCellByColumnAndRow(4, $row)->getValue(),
                            "offset_period" => 0,
                            "offset_made_every" => 1,
                            "repayment_frequency" => $repayment_frequency,
                            "repayment_made_every" => $repayment_made_every,
                            "installments" => $worksheet_check->getCellByColumnAndRow(8, $row)->getValue(),
                            "link_to_deposit_account" => 1,
                            "comment" => 'Loan imported from the old system/ Excel',
                            "amount_approved" => $amount,
                            "approval_date" => $action_date,
                            "approved_installments" => $worksheet_check->getCellByColumnAndRow(8, $row)->getValue(),
                            "approved_repayment_frequency" => $repayment_frequency,
                            "approved_repayment_made_every" => $repayment_made_every,
                            "approved_by" => 1,
                            "approval_note" => 'Data imported from excel',
                            "loan_purpose" => 'N/L',
                            "preferred_payment_id" => $preferred_payment[isset($loan_data[16]) ? $loan_data[16] : 1],
                            'date_created' => time(),
                            "created_by" => 1
                        );

                        $charges = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $savings_account['deposit_Product_id'], 'sf.chargetrigger_id' => '4', 'sf.status_id' => '1', 's.status_id' => '1'));

                        $transaction_data = $this->Transaction_model->bulk_set($data, $charges);
                        if (is_array($transaction_data)) {
                            $this->deposit_journal_transaction($transaction_data, $charges);
                            if ($charges !== NULL && $charges != '') {
                                $this->de_charges_journal_transaction($transaction_data, $charges);
                            }
                        }
                        $passed++;
                    }
                }
                $response = "Records Imported successfully";
                $feedback['message'] = "( " . $passed . " ) " . $response . " ( " . $failed . " ) Failed , Check error log table";
                $feedback['success'] = true;
            } else {
                $feedback['message'] = "( " . $failed . " ) records with errors , Check the error log. Fix them and Upload again";
                $feedback['success'] = false;
                $feedback['failed'] = $failed_data;
            }
        }
        echo json_encode($feedback);
    }


    public function do_journal_transaction_loan_fees($transaction_date, $loan_id)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('accounts_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('journal_transaction_line_model');
        $update = false;
        $data = [
            'transaction_date' => $transaction_date,
            'description' => "Loan Fees Payment",
            'ref_no' => NULL,
            'ref_id' => $loan_id,
            'status_id' => 1,
            'journal_type_id' => 28
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
                    'reference_no' => NULL,
                    'reference_id' => $loan_id,
                    'narrative' => 'Income received from ' . $fee['feename'] . ' on ' . $transaction_date,
                    'account_id' => $fee['income_account_id'],
                    'status_id' => 1
                ],
                [
                    $debit_or_credit2 => $fee['amount'],
                    'transaction_date' => $transaction_date,
                    'reference_no' => NULL,
                    'reference_id' => $loan_id,
                    'narrative' => 'Income received from ' . $fee['feename'] . ' on ' . $transaction_date,
                    'account_id' => $linked_account_id,
                    'status_id' => 1
                ]
            ];
            if ($this->journal_transaction_line_model->set($journal_transaction_id, $data)) {
                $update = $this->applied_loan_fee_model->mark_charge_paid($fee['id']);
            }
        }
        if ($update == true) {
            return true;
        } else {
            return false;
        }
    }

    public function get_member_guaranteed_active_loans()
    {
        $this->load->model('guarantor_model');
        $shares = $this->loan_guarantor_model->get_member_guaranteed_active_loans_sh($this->input->post('member_id'));
        $savings = $this->loan_guarantor_model->get_member_guaranteed_active_loans_sv($this->input->post('member_id'));
        $guarantors = $this->guarantor_model->get_member_guaranteed_active_loans($this->input->post('member_id'));

        $data = [
            'shares' => $shares,
            'savings' => $savings,
            'guarantors' => $guarantors
        ];

        echo json_encode($data);
    }



    public function get_total_penalty($client_loan_id)
    {
        $loan_details = $this->client_loan_model->get_client_loan($client_loan_id);
        // print_r($loan_details);die();

        $penalty_applicable_after_due_date = $loan_details['penalty_applicable_after_due_date'];
        $fixed_penalty_amount = $loan_details['fixed_penalty_amount'];
        $penalty_calculation_method_id = $loan_details['penalty_calculation_method_id'];
        $last_pay_date = $loan_details['last_pay_date'];
        // $penalty_rate_charged_per = $loan_details['penalty_rate_charged_per'];
        // $next_pay_date = $loan_details['next_pay_date'];

        $total_penalty = 0;
        $data['data'] = $this->repayment_schedule_model->get($client_loan_id);

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

                //echo json_encode($due_installments_data['penalty_rate_charged_per']); die;

                if ($due_installments_data['penalty_rate_charged_per'] == 4) { // One time penalty 
                    $number_of_late_period = 1;
                } elseif ($due_installments_data['penalty_rate_charged_per'] == 3) {
                    $number_of_late_period = intdiv($number_of_late_days, 30);
                } elseif ($due_installments_data['penalty_rate_charged_per'] == 2) {
                    $number_of_late_period = intdiv($number_of_late_days, 7);
                } else {
                    $number_of_late_period = $number_of_late_days;
                }


                if (intval($penalty_calculation_method_id) == 2) { // Fixed amount Penalty

                    $penalty_value = $due_installments_data['penalty_rate_charged_per'] == 4 ? ($due_installments_data['paid_penalty_amount'] > 0 ? 0 : ($fixed_penalty_amount * $number_of_late_period)) : ($fixed_penalty_amount * $number_of_late_period);
                } else {

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
            $total_penalty += $data['data'][$key]['penalty_value'];
        }

        return $total_penalty;
    }

    public function update_loan_installment_payment_date()
    {

        $installment_payment_id = $this->input->post('id');
        $sent_date = explode('-', $this->input->post('payment_date'), 3);
        $new_payment_date = count($sent_date) === 3 ? ($sent_date[2] . "-" . $sent_date[1] . "-" . $sent_date[0]) : null;

        $this->client_loan_model->update_loan_installment_payment_date($new_payment_date, $installment_payment_id);

        $response['message'] = "Payment date updated";
        $response['success'] = TRUE;

        echo json_encode($response);
    }

    public function wipe_out_loan($client_loan_id)
    {
        $this->client_loan_model->wipe_out_loan($client_loan_id);
    }

    public function adjust_penalty()
    {
        $response = array();
        $response['success'] = false;
        $response['message'] = 'Penalty Update failed';

        $post_data = $this->input->post();
        $current_penalty = $this->get_total_penalty($this->input->post('client_loan_id'));
        $result = $this->client_loan_model->adjust_penalty($post_data, $current_penalty);

        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Penalty Updated Successfully';
        }

        // echo json_encode(['data' => $response]);
        echo json_encode($response);
    }

    public function extend_loan_period($client_loan_id)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('journal_transaction_line_model');
        $this->load->model('loan_state_model');

        $feedback = array();
        $feedback['success'] = false;
        $feedback['message'] = "Something went wrong, Please try again";

        $this->db->trans_begin();

        $allowed_states = [7, 11, 12, 13]; // Active, Rescheduled, Locked, In-Arrears

        $extended_installments = $this->input->post('extended_installments') ? $this->input->post('extended_installments') : 1;
        $loan_data = $this->repayment_schedule_model->get_loan_data_penalty($client_loan_id);

        # Loan ID should be Provided
        if ($client_loan_id == NULL || !is_numeric($client_loan_id)) {
            $feedback['message'] = 'Loan Must be Provided';
            $this->output->set_status_header(400);
            return $this->output->set_output(json_encode(($feedback)));
        }

        # Loan state check
        $current_loan_state = $this->loan_state_model->get_max_loan_state_by_id($client_loan_id);
        if (!in_array($current_loan_state, $allowed_states)) {
            $feedback['message'] = 'This Loan can not be extended. Only Active Loans can be extended';
            $this->output->set_status_header(400);
            return $this->output->set_output(json_encode(($feedback)));
        }

        # Loan should have atleast one active installment 
        $current_installments_count = $this->repayment_schedule_model->count_installments($client_loan_id);
        if (!$current_installments_count) {
            $feedback['message'] = 'Loan should have atleast one active installment';
            $this->output->set_status_header(400);
            return $this->output->set_output(json_encode(($feedback)));
        }

        $new_installment_no = $current_installments_count + 1;

        # Get Amortization schedule
        $schedules = $this->get_extended_schedule($client_loan_id, 1, $loan_data);
        $extended_schedule = end($schedules['payment_schedule']);
        # Get last schedule payment_date
        $last_schedule_data = $this->repayment_schedule_model->get_last_schedule_payment_date($client_loan_id);
        # Compute new schedule repayment date
        if ($loan_data['approved_repayment_made_every'] == 1) {
            $schedule_date = $loan_data['approved_repayment_frequency'] . ' day';
        } elseif ($loan_data['approved_repayment_made_every'] == 2) {
            $schedule_date = $loan_data['approved_repayment_frequency'] . ' week';
        } else {
            $schedule_date = $loan_data['approved_repayment_frequency'] . ' month';
        }

        $repayment_date = date('Y-m-d', strtotime($last_schedule_data['repayment_date'] . ' + ' . $schedule_date));
        $new_schedule_data = array(
            'repayment_date' => $repayment_date,
            'interest_amount' => $extended_schedule['interest_amount'],
            'principal_amount' => 0,
            'demanded_penalty' => 0,
            'client_loan_id' => $client_loan_id,
            'grace_period_on' => 0,
            'grace_period_after' => 0,
            'installment_number' => $new_installment_no,
            'interest_rate' => $loan_data['interest_rate'],
            'repayment_frequency' => $loan_data['approved_repayment_frequency'],
            'repayment_made_every' => $loan_data['approved_repayment_made_every'],
            'comment' => 'Extended Loan Schedule',
            'actual_payment_date' => '0000-00-00',
            'payment_status' => 4,
            'status_id' => 1,
            'date_created' => time(),
            'created_by' => $_SESSION['id'],
            'date_modified' => date('Y-m-d H:i:s'),
            'modified_by' => $_SESSION['id']

        );
        //echo json_encode($extended_schedule); die();

        # Repayment date check

        # Add new schedule to repayment schedule table
        $schedule_id = $this->repayment_schedule_model->set3($new_schedule_data);

        # Do the neccesary journal entries
        $journal_data = [
            'transaction_date' =>  date('d-m-Y', strtotime($repayment_date)),
            'description' => 'Extended Loan Schedule',
            'ref_no' => $loan_data['loan_no'],
            'ref_id' => $client_loan_id,
            'status_id' => 1,
            'journal_type_id' => 4,
        ];

        $journal_transaction_id = $this->journal_transaction_model->set($journal_data);
        $loan_product_details = $this->loan_product_model->get_accounts($loan_data['loan_product_id']);

        $Interest_income_ac_details = $this->accounts_model->get($loan_product_details['interest_income_account_id']);
        $Interest_receivable_ac_details = $this->accounts_model->get($loan_product_details['interest_receivable_account_id']);

        $debit_or_credit3 = ($Interest_income_ac_details['normal_balance_side'] == 1) ? 'debit_amount' : 'credit_amount';
        $debit_or_credit4 = ($Interest_receivable_ac_details['normal_balance_side'] == 1) ? 'debit_amount' : 'credit_amount';

        $transaction_date = date('d-m-Y', strtotime($repayment_date));
        $data = array();
        $data[0] = [
            'reference_no' => $loan_data['loan_no'],
            'reference_id' => $schedule_id,
            'transaction_date' => $transaction_date,
            'reference_key' => $loan_data['loan_no'],
            $debit_or_credit3 => $extended_schedule['interest_amount'],
            'narrative' => strtoupper("Interest on Loan Extension done on " . date('Y-m-d H:i:s')),
            'account_id' => $loan_product_details['interest_income_account_id'],
            'status_id' => 1,
        ];

        $data[1] =  [
            'reference_no' => $loan_data['loan_no'],
            'reference_id' => $schedule_id,
            'transaction_date' => $transaction_date,
            'reference_key' => $loan_data['loan_no'],
            $debit_or_credit4 => $extended_schedule['interest_amount'],
            'narrative' => strtoupper("Interest on Loan Extension done on " . date('Y-m-d H:i:s')),
            'account_id' => $loan_product_details['interest_receivable_account_id'],
            'status_id' => 1,
        ];

        $this->journal_transaction_line_model->set($journal_transaction_id, $data);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $feedback['message'] = 'Loan Extension failed, Something went wrong';
            $feedback['success'] = false;
            echo json_encode($feedback);
        } else {
            $this->db->trans_commit();
            $feedback['message'] = 'Loan Extension Successful';
            $feedback['success'] = true;
            echo json_encode($feedback);
        }
    }

    public function get_extended_schedule($client_loan_id, $installments_no, $loan_data)
    {
        $this->load->model('loan_installment_payment_model');
        $payment_data = $this->loan_installment_payment_model->loan_payment_data($client_loan_id);

        //print_r($loan_data); die();
        $_POST['compute_interest_from_disbursement_date'] = 0;
        $_POST['installments'] = $installments_no;
        $_POST['amount'] = ($loan_data['disbursed_amount'] > 0 ? $loan_data['disbursed_amount'] : $loan_data['amount_approved']) - $payment_data['already_principal_amount'];

        // print_r($_POST['amount']); die();
        unset($loan_data['current_loan_paid_principal']);
        //$this->data['loan_data'] = $loan_data;

        $schedules = $this->parameter_construction(date('d-m-Y'), $loan_data);

        return $schedules;
    }

    public function send_sms_remainder()
    {
        $client_loan_id = $this->input->post('loan_id');
        try {
            $amount_due = $this->client_loan_model->get_loan_amount_due($client_loan_id);
        } catch (\Throwable $th) {
            echo json_encode([
                'success' => false,
                'message' => "Sorry, Something Went Wrong",
            ]);
            return;
        }
        
        if (!empty($result = $this->miscellaneous_model->check_org_module(22))) {
            $message = "Dear Customer, You are remainded to clear all your due loan balance of " . number_format($amount_due) . " today. - " . ".
  " . $this->organisation . ", Contact " . $this->contact_number;
            $text_response = $this->helpers->notification($client_loan_id, $message);
          } else {
            echo json_encode([
                'success' => false,
                'message' => "SMS not Enabled, Contact IT support",
            ]);
            return;
          }
        echo json_encode([
            'success' => true,
            'message' => $text_response,
        ]);
    }
}
