<?php

/**
 * Description of Reports
 *
 * @author reagan
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Reports extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->data['privilege_list'] = $this->helpers->user_privileges(10, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module(10, $_SESSION['organisation_id']);
        if (empty($this->data['module_access'])) {
            redirect('my404');
        } else {
            $this->load->model('reports_model');
            $this->load->model('ledger_model');
            $this->load->model('Fiscal_month_model');
            $this->load->model('fiscal_model');
            $this->load->model('loan_product_model');
            $this->load->model('client_loan_model');
            $this->load->model('accounts_model');
            $this->load->model('shares_model');
            $this->load->model('dashboard_model');
            $this->load->model('transaction_model');
            $this->load->model("Loan_guarantor_model");
            $this->load->model('Interest_payment_points_model');
            $this->load->model('organisation_model');
            $this->load->model('branch_model');
            if (empty($this->data['privilege_list'])) {
                redirect('my404');
            } else {
                $this->data['report_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
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
    }

    // public function income_accs_json() {
    //     $subcategories = $this->accounts_model->get_subcat_list("category_id =4");
    //     $data['data'] = $this->ledgers_display($subcategories, 2);
    //     echo json_encode($data);
    // }

    // public function expense_accs_json() {
    //     $subcategories = $this->accounts_model->get_subcat_list("category_id =5");
    //     $data['data'] = $this->ledgers_display($subcategories, 1);
    //     echo json_encode($data);
    // }

    public function jsonList2()
    {
        $data['data'] = $this->ledger_model->get_single_ledger($this->input->post('account_id'));
        echo json_encode($data);
    }

    public function excel()
    {
        $data['data'] = $this->input->post('excel_data');
        $this->load->view('reports/excel_data', $data);
    }

    public function print_trial_balance_excel($start_date, $end_date)
    {
        $_POST['fisc_date_from'] = $start_date;
        $_POST['fisc_date_to'] = $end_date;
        $_POST['print'] = 1;

        $data = $this->trial_balance();
        $dataArray = $data['data'];
        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:C1");
        $sheet->setCellValue('A1', 'Trial Balance ' . date('d-M-Y', strtotime($start_date)) . ' to ' . date('d-M-Y', strtotime($end_date)));
        $sheet->getStyle("A1:C1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:C1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center

        $sheet->setCellValue('A3', 'ACCOUNT NANME');
        $sheet->setCellValue('B3', 'DEBIT');
        $sheet->setCellValue('C3', 'CREDIT');

        $sheet->getStyle("A3:C3")->getFont()->setBold(true);

        $rowCount   =   4;
        foreach ($dataArray as $account) {
            $debit_sum_amount = 0;
            $credit_sum_amount = 0;
            $debit_sum = $account['debit_sum'] ? $account['debit_sum'] : 0;
            $credit_sum = $account['credit_sum'] ? $account['credit_sum'] : 0;
            if ($account['normal_balance_side'] == 1) {
                $debit_sum_amount = $debit_sum - $credit_sum;
            }
            if ($account['normal_balance_side'] == 2) {
                $credit_sum_amount = $credit_sum - $debit_sum;
            }

            $sheet->setCellValue('A' . $rowCount, $account['account_name']);
            $sheet->setCellValue('B' . $rowCount, $debit_sum_amount);
            $sheet->setCellValue('C' . $rowCount, $credit_sum_amount);

            $rowCount++;
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('B4:B' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('C4:C' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'C' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTAL');
        $sheet->getStyle($total_row)->getFont()->setBold(true);

        // calculate totals
        $sheet->setCellValue('B' . ($highestRow + 2), '=SUM(B4:B' . $highestRow . ')');
        $sheet->setCellValue('C' . ($highestRow + 2), '=SUM(C4:C' . $highestRow . ')');

        $sheet->getStyle('B' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('C' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Trial Balance';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function print_trial_balance_pdf()
    {
        $this->load->model('Staff_model');
        $this->load->model('branch_model');
        $this->load->model('organisation_model');
        $this->load->helper('pdf_helper');

        $data['title'] = $_SESSION["org_name"];
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        //fiscal_year
        $data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $data['title'] = $data['sub_title'] = "Trial Balance";
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);

        $data['trial_bal_data'] = $this->trial_balance();

        $data['start_date'] = $this->input->post('fisc_date_from');
        $data['end_date'] = $this->input->post('fisc_date_to');
        $data['the_page_data'] = $this->load->view('reports/trial_balance_print_out', $data, TRUE);
        $this->load->view('includes/pdf_template', $data);
    }

    public function statement()
    {
        $this->load->model('organisation_model');
        $this->load->model('branch_model');
        $filename = $this->input->post('filename');
        $paper = $this->input->post('paper');
        $orientation = $this->input->post('orientation');
        $stream = $this->input->post('stream');

        $pdf['title'] = $_SESSION["org_name"];
        $pdf['filename'] = $filename;
        $pdf['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $pdf['branch'] = $this->branch_model->get($_SESSION['branch_id']);

        //===================== PRINT OUT DATA AND VIEW ====================



        if ($this->input->post('report_type') == 3) {
            $data = $this->balancesheet();
            $data['end_date'] = $this->input->post('fisc_date_to');
            $pdf['pdf_data'] = $this->load->view('reports/balance_sheet/printout_pdf', $data, true);
        } else if ($this->input->post('report_type') == 2) {
            $data = $this->income_statement();
            $data['end_date'] = $this->input->post('fisc_date_to');

            $pdf['pdf_data'] = $this->load->view('reports/profit_loss/profit_loss_pdf', $data, true);
        } else {
            $data = $this->trial_balance();
            $data['end_date'] = $this->input->post('fisc_date_to');
            $pdf['pdf_data'] = $this->load->view('reports/trial_balance_pdf', $data, true);
        }
        $html = $this->load->view('pdf_template', $pdf, true);

        //===================== END HERE AND GENERATE =======================
        $this->pdfgenerator->generate($html, $filename, $stream, $paper, $orientation);
    }

    public function balancesheet()
    {
        $data_list['sum_income'] = $this->reports_model->get_category_sums(4, FALSE);
        $data_list['sum_expense'] = $this->reports_model->get_category_sums(5, FALSE);

        $net_profloss = ($data_list['sum_income']['credit_sum'] - $data_list['sum_income']['debit_sum']) - ($data_list['sum_expense']['debit_sum'] - $data_list['sum_expense']['credit_sum']);
        $data_list['total_equity'] = $this->reports_model->get_category_sums(3, TRUE);
        $data_list['total_assets'] = $this->reports_model->get_category_sums(1, TRUE);
        $data_list['total_liabilities'] = $this->reports_model->get_category_sums(2, TRUE);
        $equity_side = ($data_list['total_equity']['credit_sum'] - $data_list['total_equity']['debit_sum']) + ($data_list['total_liabilities']['credit_sum'] - $data_list['total_liabilities']['debit_sum']);

        $subcategories_assets = $this->reports_model->get_category_sums(false, "category_id=1");
        $subcategories_lq = $this->reports_model->get_category_sums(false, "category_id IN (2,3)");
        $data['print_sums'] = ['equity_side' => $equity_side, 'total_assets' => $data_list['total_assets']['amount'], 'net_profit_loss' => $net_profloss];
        $data['assets'] = $this->ledgers_display($subcategories_assets, 1);
        $data['liab_equity'] = $this->ledgers_display($subcategories_lq, 2);
        $data['report_type'] = 3;
        if ($_POST['print'] == 1) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }
    public function income_statement()
    {
        $data_list['sum_income'] = $this->reports_model->get_category_sums(4, FALSE);
        $data_list['sum_expense'] = $this->reports_model->get_category_sums(5, FALSE);

        $subcategories_income = $this->reports_model->get_category_sums(false, "category_id=4", "jt.journal_type_id!=26");
        $data['income'] = $this->ledgers_display($subcategories_income, 2);
        $subcategories_expense = $this->reports_model->get_category_sums(false, "category_id=5", "jt.journal_type_id!=26");
        $data['expenses'] = $this->ledgers_display($subcategories_expense, 1);

        $net_profloss = ($data_list['sum_income']['credit_sum'] - $data_list['sum_income']['debit_sum']) - ($data_list['sum_expense']['debit_sum'] - $data_list['sum_expense']['credit_sum']);

        $data['profitloss_sums'] = ['total_income' => ($data_list['sum_income']['credit_sum'] - $data_list['sum_income']['debit_sum']), 'total_expense' => ($data_list['sum_expense']['debit_sum'] - $data_list['sum_expense']['credit_sum']), 'net_profit_loss' => $net_profloss];
        $data['report_type'] = 2;
        if ($_POST['print'] == 1) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }

    public function trial_balance()
    {
        if ($this->input->post('print') == 1) {
            $journal_type_id = " jt.journal_type_id !=26 ";
        } else {
            $journal_type_id = " 1 ";
        }
        $data['data'] = $this->reports_model->get_accounts_sums(FALSE, FALSE, $journal_type_id);
        if ($_POST['print'] == 1) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }


    public function cashflow()
    {
    }
    public function report_savings_accounts()
    { //used for fetching union of members and groups
        $data['data'] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7');
        echo json_encode($data);
    }
    public function report_shares_accounts()
    { 
        $data['data'] = $this->shares_model->get_shares();
        echo json_encode($data);
    }
    public function report_loans_accounts()
    { 
        //$data['draw'] = intval($this->input->post('draw'));
        $data['data'] = $this->client_loan_model->get_dTable();
        $filteredl_records_cnt = $this->client_loan_model->get_found_rows();
        $data['recordsTotal'] = $this->client_loan_model->get2();
        $data['recordsFiltered'] = current($filteredl_records_cnt);
        echo json_encode($data);
    }

    public function export_excel_savings_accounts_report($start_date, $end_date, $deposit_Product_id)
    {


        $_POST['status_id'] = 1;
        $_POST['start_date'] = $start_date != 'null' ? $start_date : '';
        $_POST['end_date'] = $end_date != 'null' ? $end_date : '';
        $_POST['deposit_Product_id'] = $deposit_Product_id != 'null' ? $deposit_Product_id : '';

        $dataArray = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7');

        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:F1");
        $sheet->setCellValue('A1', 'Savings Accounts - Reports ' . date('d-M-Y', strtotime($start_date)) . ' Through to ' . date('d-M-Y', strtotime($end_date)));
        $sheet->getStyle("A1:F1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:F1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center

        $sheet->setCellValue('A3', 'ACCOUNT NO');
        $sheet->setCellValue('B3', 'MEMBER NAME');
        $sheet->setCellValue('C3', 'PRODUCT');
        $sheet->setCellValue('D3', 'MINIMUM BALANCE');
        $sheet->setCellValue('E3', 'LOCKED AMONUT');
        $sheet->setCellValue('F3', 'CASH BALANCE');

        $sheet->getStyle("A3:F3")->getFont()->setBold(true);

        $rowCount   =   4;
        foreach ($dataArray as $saving) {
            $sheet->setCellValue('A' . $rowCount, $saving['account_no']);
            $sheet->setCellValue('B' . $rowCount, $saving['member_name']);
            $sheet->setCellValue('C' . $rowCount, $saving['productname']);
            $sheet->setCellValue('D' . $rowCount, $saving['mindepositamount']);
            $sheet->setCellValue('E' . $rowCount, $saving['locked_amount']);
            $sheet->setCellValue('F' . $rowCount, $saving['real_bal']);


            $rowCount++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('F4:F' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'F' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTALS');
        $sheet->getStyle($total_row)->getFont()->setBold(true);

        // calculate totals
        $sheet->setCellValue('F' . ($highestRow + 2), '=SUM(F4:F' . $highestRow . ')');

        $sheet->getStyle('F' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Savings Accounts - Reports';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function savings_accounts_periodic_reports()
    { //used for fetching union of members and groups
        $data['data'] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7');
        echo json_encode($data);
    }

    public function export_excel_savings_accounts_periodic_reports($start_date, $end_date)
    {
        $_POST['status_id'] = 1;
        $_POST['start_date'] = $start_date != 'null' ? $start_date : '';
        $_POST['end_date'] = $end_date != 'null' ? $end_date : '';

        $dataArray = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7');

        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:H1");

        if ($this->input->post('start_date') && $this->input->post('end_date')) {
            $sheet->setCellValue('A1', 'Savings Accounts - Periodic Reports ' . date('d-M-Y', strtotime($start_date)) . ' Through to ' . date('d-M-Y', strtotime($end_date)));
        } else {
            $sheet->setCellValue('A1', 'Savings Accounts - Periodic Reports Upto Date');
        }

        $sheet->getStyle("A1:H1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:H1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center

        $sheet->setCellValue('A3', 'ACCOUNT NO');
        $sheet->setCellValue('B3', 'MEMBER NAME');
        $sheet->setCellValue('C3', 'DEPOSITS');
        $sheet->setCellValue('D3', 'WITHDRAWS');
        $sheet->setCellValue('E3', 'TRANSFERS');
        $sheet->setCellValue('F3', 'PAYMENTS');
        $sheet->setCellValue('G3', 'CHARGES');
        $sheet->setCellValue('H3', 'CASH BALANCE');

        $sheet->getStyle("A3:H3")->getFont()->setBold(true);

        $rowCount   =   4;
        foreach ($dataArray as $saving) {
            $sheet->setCellValue('A' . $rowCount, $saving['account_no']);
            $sheet->setCellValue('B' . $rowCount, $saving['member_name']);
            $sheet->setCellValue('C' . $rowCount, $saving['deposits']);
            $sheet->setCellValue('D' . $rowCount, $saving['withdraws']);
            $sheet->setCellValue('E' . $rowCount, $saving['transfers']);
            $sheet->setCellValue('F' . $rowCount, $saving['payments']);
            $sheet->setCellValue('G' . $rowCount, $saving['charges']);
            $sheet->setCellValue('H' . $rowCount, $saving['real_bal']);


            $rowCount++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('D4:D' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('E4:E' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F4:F' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G4:G' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H4:H' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'H' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTALS');
        $sheet->getStyle($total_row)->getFont()->setBold(true);

        // calculate totals
        $sheet->setCellValue('D' . ($highestRow + 2), '=SUM(D4:D' . $highestRow . ')');
        $sheet->setCellValue('E' . ($highestRow + 2), '=SUM(E4:E' . $highestRow . ')');
        $sheet->setCellValue('F' . ($highestRow + 2), '=SUM(F4:F' . $highestRow . ')');
        $sheet->setCellValue('G' . ($highestRow + 2), '=SUM(G4:G' . $highestRow . ')');
        $sheet->setCellValue('H' . ($highestRow + 2), '=SUM(H4:H' . $highestRow . ')');

        $sheet->getStyle('D' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('E' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Savings Accounts - Periodic Reports';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }



    public function savings_interest_payments()
    {
        $data['data'] = $this->Interest_payment_points_model->get_payments($this->input->post('fisc_date_from'), $this->input->post('fisc_date_to'));
        echo json_encode($data);
    }

    public function export_excel_savings_interest_payments($start_date, $end_date)
    {
        $_POST['status_id'] = 1;
        $_POST['fisc_date_from'] = $start_date != 'null' ? $start_date : '';
        $_POST['fisc_date_to'] = $end_date != 'null' ? $end_date : '';

        $dataArray = $this->Interest_payment_points_model->get_payments($start_date, $end_date);

        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:H1");
        $sheet->setCellValue('A1', 'Savings Accounts - Interest Payouts ' . date('d-M-Y', strtotime($start_date)) . ' Through to ' . date('d-M-Y', strtotime($end_date)));
        $sheet->getStyle("A1:H1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:H1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center

        $sheet->setCellValue('A3', 'TRANSACTION NO');
        $sheet->setCellValue('B3', 'ACCOUNT NO');
        $sheet->setCellValue('C3', 'MEMEBER NAME');
        $sheet->setCellValue('D3', 'MONTH');
        $sheet->setCellValue('E3', 'QUALIFYING AMOUNT');
        $sheet->setCellValue('F3', 'INTEREST');
        $sheet->setCellValue('G3', 'PAYMENT DATE');
        $sheet->setCellValue('H3', 'STATUS');

        $sheet->getStyle("A3:H3")->getFont()->setBold(true);

        $rowCount   =   4;
        foreach ($dataArray as $interest_payment) {
            $sheet->setCellValue('A' . $rowCount, $interest_payment['transaction_no']);
            $sheet->setCellValue('B' . $rowCount, $interest_payment['account_no']);
            $sheet->setCellValue('C' . $rowCount, $interest_payment['member_name']);
            $sheet->setCellValue('D' . $rowCount, date('M', strtotime($interest_payment['date_calculated'])));
            $sheet->setCellValue('E' . $rowCount, $interest_payment['qualifying_amount']);
            $sheet->setCellValue('F' . $rowCount, $interest_payment['interest_amount']);
            $sheet->setCellValue('G' . $rowCount, date('d-M-Y', strtotime($interest_payment['date_calculated'])));
            $sheet->setCellValue('H' . $rowCount, $interest_payment['status_id']);


            $rowCount++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('E4:E' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F4:F' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'H' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTALS');
        $sheet->getStyle($total_row)->getFont()->setBold(true);

        // calculate totals
        $sheet->setCellValue('E' . ($highestRow + 2), '=SUM(E4:E' . $highestRow . ')');
        $sheet->setCellValue('F' . ($highestRow + 2), '=SUM(F4:F' . $highestRow . ')');

        $sheet->getStyle('E' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Savings Accounts - Interest Payouts';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function savings_per_month()
    {
        $data['data'] = $this->transaction_model->get_per_month($this->input->post('fisc_date_from'), $this->input->post('fisc_date_to'));
        echo json_encode($data);
    }

    public function export_excel_savings_per_month($start_date, $end_date)
    {
        $start_date = $start_date != 'null' ? $start_date : '';
        $end_date = $end_date != 'null' ? $end_date : '';

        $dataArray = $this->transaction_model->get_per_month($start_date, $end_date);

        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:F1");
        $sheet->setCellValue('A1', 'Monthly Savings ' . date('d-M-Y', strtotime($start_date)) . ' Through to ' . date('d-M-Y', strtotime($end_date)));
        $sheet->getStyle("A1:F1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:F1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center

        $sheet->setCellValue('A3', 'MONTH');
        $sheet->setCellValue('B3', 'DEBIT');
        $sheet->setCellValue('C3', 'CREDIT');
        $sheet->setCellValue('D3', 'BALANCE');

        $sheet->getStyle("A3:D3")->getFont()->setBold(true);

        $rowCount   =   4;
        foreach ($dataArray as $saving) {
            $dateObj   = DateTime::createFromFormat('!m', $saving['month']);
            $monthName = $dateObj->format('F'); // March
            $sheet->setCellValue('A' . $rowCount, $monthName);
            $sheet->setCellValue('B' . $rowCount, $saving['debit_sum']);
            $sheet->setCellValue('C' . $rowCount, $saving['credit_sum']);
            $sheet->setCellValue('D' . $rowCount, $saving['balance']);


            $rowCount++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('B4:B' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('C4:C' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('D4:D' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'D' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTALS');
        $sheet->getStyle($total_row)->getFont()->setBold(true);

        // calculate totals
        $sheet->setCellValue('B' . ($highestRow + 2), '=SUM(B4:B' . $highestRow . ')');
        $sheet->setCellValue('C' . ($highestRow + 2), '=SUM(C4:C' . $highestRow . ')');
        $sheet->setCellValue('D' . ($highestRow + 2), '=SUM(D4:D' . $highestRow . ')');

        $sheet->getStyle('B' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('C' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('D' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Monthly Savings';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function profit_loss_tabular()
    {
        $subcategories_income = $this->accounts_model->get_subcat_list("category_id =4");
        $subcategories_expense = $this->accounts_model->get_subcat_list("category_id =5");
        $income = $this->ledgers_display($subcategories_income, 2);
        $expenses = $this->ledgers_display($subcategories_expense, 1);
        $data['data'] = array_merge($income, $expenses);
        //print_r($data['data']);die();
        echo json_encode($data);
    }

    public function profit_loss()
    {
        $this->load->model('Shares_model');
        $this->load->model('Share_issuance_category_model');
        $sum_assets = $this->reports_model->get_category_sums(1, FALSE);
        $sum_liability = $this->reports_model->get_category_sums(2, FALSE);
        $sum_equity = $this->reports_model->get_category_sums(3, FALSE);
        $sum_income = $this->reports_model->get_category_sums(4, FALSE);
        $sum_expense = $this->reports_model->get_category_sums(5, FALSE);
        $sum_credit_debit = $this->reports_model->get_credit_debit_sums();


        $data['profit_loss'] = abs($sum_income['amount']) - abs($sum_expense['amount']);
        $data['total_income'] = abs($sum_income['amount']);
        $data['total_expense'] = abs($sum_expense['amount']);
        $data['total_assets'] = abs($sum_assets['amount']);
        $data['total_credit_tr'] = abs($sum_credit_debit['credit_sum']);
        $data['total_debit_tr'] = abs($sum_credit_debit['debit_sum']);

        $data['total_liability'] = abs($sum_liability['amount']);
        $data['total_equity'] = abs($sum_equity['amount']);

        //compute the total shareholding
        $shares_price = $this->Share_issuance_category_model->get_active_share_issuance_price();
        $sum_shares = $this->Shares_model->get_total_share_capital();
        $data['price_per_share'] = $shares_price['price_per_share'];
        $data['no_shares'] = round(abs($sum_shares['total_amount']) / abs($shares_price['price_per_share']), 2);
        $data['total_shares'] = $sum_shares['total_amount'];
        echo json_encode($data);
    }

    public function index()
    {
        $neededcss = array("fieldset.css", "1_12_1_jquery-ui.css", "plugins/highcharts/code/css/highslide.css", "plugins/daterangepicker/daterangepicker-bs3.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/highcharts/code/highcharts.js", "plugins/highcharts/code/highcharts-3d.js", "plugins/highcharts/code/modules/exporting.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/modules/export-data.js", "plugins/highcharts/code/modules/series-label.js", "plugins/printjs/print.min.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['fiscal_years'] = $this->fiscal_model->get();

        $data['sum_income'] = $this->reports_model->get_category_sums(4, FALSE);
        $data['sum_expense'] = $this->reports_model->get_category_sums(5, FALSE);
        $this->data['net_profit_loss'] = abs($data['sum_income']['amount']) - abs($data['sum_expense']['amount']);
        $this->data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['title'] = $this->data['sub_title'] = "Reporting";
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('reports/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function bs_assets_json()
    {
        $subcategories = $this->accounts_model->get_subcat_list("category_id=1");
        $data['data'] = $this->ledgers_display($subcategories, 1);
        echo json_encode($data);
    }

    public function bs_lc_json()
    {
        $subcategories = $this->accounts_model->get_subcat_list("category_id IN (2,3)");
        $data['data'] = $this->ledgers_display($subcategories, 2);
        echo json_encode($data);
    }

    public function aging_accounts_json()
    {
        if ($this->input->post('type') == 1) {  //1== invoice  2==bills
            $data['data'] = $this->reports_model->get_aging_user_invoices();
        } else {
            $data['data'] = $this->reports_model->get_aging_user_bills();
        }
        echo json_encode($data);
    }

    public function get_indicators_data()
    {
        $start_date = $this->input->post('fisc_date_from');
        $end_date = $this->input->post('fisc_date_to');
        $data_list['sum_income'] = $this->reports_model->get_category_sums(4, FALSE);
        $data_list['sum_expense'] = $this->reports_model->get_category_sums(5, FALSE);
        $net_profloss = abs($data_list['sum_income']['amount']) - abs($data_list['sum_expense']['amount']);
        //$data['net_profit_loss'] =$net_profloss;
        $data_list['total_equity'] = $this->reports_model->get_category_sums(3, TRUE);
        $data_list['total_assets'] = $this->reports_model->get_category_sums(1, TRUE);
        $data_list['total_liabilities'] = $this->reports_model->get_category_sums(2, TRUE);
        $data_list['total_short_term_assets'] = $this->reports_model->get_category_sums(TRUE, 1);
        // $data_list['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $data_list['total_short_term_liabilities'] = $this->reports_model->get_category_sums(TRUE, 8);
        $data_list['total_income'] = $this->reports_model->get_category_sums(4, TRUE);
        $data_list['total_cost_of_goods'] = $this->reports_model->get_category_sums(TRUE, 14);

        $equity_side = (abs($data_list['total_equity']['amount']) + abs($data_list['total_liabilities']['amount']));
        $data['all_totals'] = ['equity_side' => $equity_side, 'net_profit_loss' => $net_profloss];
        /* ========================== Gross Profit Margins ================================================== */
        $data['GrossProfitMargin'] = abs($data_list['total_income']['amount'] - $data_list['total_cost_of_goods']['amount']);

        /* ========================== current ratio totals ================================================== */
        $amount_short_term_assets = abs($data_list['total_short_term_assets']['amount']);
        $amount_short_term_liabilities = abs($data_list['total_short_term_liabilities']['amount']);

        /* ========================== debt ratio totals ====================================================== */
        $amount_liabilities = abs($data_list['total_liabilities']['amount']);
        $amount_equity = abs($data_list['total_equity']['amount']);
        $amount_assets = abs($data_list['total_assets']['amount']);
        $total_amount = $amount_equity + $amount_liabilities;

        /* ========================== Debt calculations ====================================================== */
        if ($amount_liabilities > 0) {
            $liability_percent = ($amount_liabilities / $total_amount) * 100;
            $liability_percent2 = ($amount_liabilities / $amount_assets) * 100;
        } else {
            $liability_percent = $amount_liabilities = $liability_percent2 = 0;
        }
        if ($amount_equity > 0) {
            $equity_percent = ($amount_equity / $total_amount) * 100;
        } else {
            $equity_percent = $amount_equity = 0;
        }

        $total_amount_short_terms = $amount_short_term_assets + $amount_short_term_liabilities;
        if ($amount_short_term_assets > 0) {
            $short_asset_percent = ($amount_short_term_assets / $total_amount_short_terms) * 100;
        } else {
            $short_asset_percent = $amount_short_term_assets = 0;
        }
        if ($amount_short_term_liabilities > 0) {
            $short_liability_percent = ($amount_short_term_liabilities / $total_amount_short_terms) * 100;
        } else {
            $short_liability_percent = $amount_short_term_liabilities = 0;
        }
        /* ========================== Income vs Expenses ================================================== */
        $data['income_expense'] = $this->get_line_graph_data($start_date, $end_date);

        /* ========================== cash flow for investments ================================================== */
        $data['cashflow_investments'] = $this->get_bar_graph_cf_investment($start_date, $end_date);
        /* ========================== cash flow for financing ================================================== */
        $data['cashflow_financing'] = $this->get_bar_graph_cf_financing($start_date, $end_date);

        /* ========================== Chart data arrays ====================================================== */
        $data['debt_equity'] = [
            'slice1' => ['labels' => 'Liabilities', 'amount' => $amount_liabilities, 'percent' => $liability_percent],
            'slice2' => ['labels' => 'Equity', 'amount' => $amount_equity, 'percent' => $equity_percent]
        ];
        $data['debt_assets'] = [
            'slice1' => ['labels' => 'Liabilities', 'amount' => $amount_liabilities, 'percent' => $liability_percent2],
            'slice2' => ['labels' => 'Assets', 'amount' => $amount_assets, 'percent' => (100 - $liability_percent2)]
        ];
        $data['current_ratio'] = [
            'slice1' => ['labels' => 'Current Assets', 'amount' => $amount_short_term_assets, 'percent' => $short_asset_percent],
            'slice2' => ['labels' => 'Current Liabilities', 'amount' => $amount_short_term_liabilities, 'percent' => $short_liability_percent]
        ];

        /* ============================ printable balance sheet ================================================= */

        $data_list['cash'] = $this->reports_model->get_category_sums(TRUE, 3);
        $data_list['mobile_money'] = $this->reports_model->get_category_sums(TRUE, 5);
        $data_list['bank'] = $this->reports_model->get_category_sums(TRUE, 4);
        $data_list['accounts_receivables'] = $this->reports_model->get_category_sums(TRUE, 1);
        $data_list['other_current'] = $this->reports_model->get_category_sums(TRUE, 2);

        $data_list['fixed_assets'] = $this->reports_model->get_category_sums(TRUE, 6);
        $data_list['other_assets'] = $this->reports_model->get_category_sums(TRUE, 7);

        $data_list['current_liabilities'] = $this->reports_model->get_category_sums(TRUE, 8);
        $data_list['other_liabilities'] = $this->reports_model->get_category_sums(TRUE, 9);
        $data_list['long_term_liabilities'] = $this->reports_model->get_category_sums(TRUE, 10);

        $data_list['capital'] = $this->reports_model->get_category_sums(TRUE, 11);

        $data['subaccounts_totals'] = ['cash' => abs($data_list['cash']['amount']), 'mobile_money' => abs($data_list['mobile_money']['amount']), 'bank' => abs($data_list['bank']['amount']), 'accounts_receivables' => abs($data_list['accounts_receivables']['amount']), 'other_current' => abs($data_list['other_current']['amount']), 'fixed_assets' => abs($data_list['fixed_assets']['amount']), 'other_assets' => abs($data_list['other_assets']['amount']), 'current_liabilities' => abs($data_list['current_liabilities']['amount']), 'other_liabilities' => abs($data_list['other_liabilities']['amount']), 'long_term_liabilities' => abs($data_list['long_term_liabilities']['amount']), 'capital' => abs($data_list['capital']['amount'])];

        echo json_encode($data);
    }

    public function aging_receivables()
    {
        $today = date('Y-m-d');
        $createddate = DateTime::createFromFormat('Y-m-d', $today);
        $createddate->modify('-30 day');
        $minus_thirty = $createddate->format('Y-m-d');

        $createddate2 = DateTime::createFromFormat('Y-m-d', $today);
        $createddate2->modify('-60 day');
        $minus_sixty = $createddate2->format('Y-m-d');

        $createddate3 = DateTime::createFromFormat('Y-m-d', $today);
        $createddate3->modify('-90 day');
        $minus_ninty = $createddate3->format('Y-m-d');
        //type 1===invoices
        //type 2=== bills 

        $total_aging1_invoice = $this->get_aging_receivables($minus_thirty, date('Y-m-d'), 1);
        $total_aging2_invoice = $this->get_aging_receivables($minus_sixty, $minus_thirty, 1);
        $total_aging3_invoice = $this->get_aging_receivables($minus_ninty, $minus_sixty, 1);
        $total_aging4_invoice = $this->get_aging_receivables("1900-01-01", $minus_ninty, 1);
        $data['aging_receivables'] = [
            'range1' => ['name' => "0-30 (days)", "amount" => $total_aging1_invoice],
            'range2' => ['name' => "31-60 (days)", "amount" => $total_aging2_invoice],
            'range3' => ['name' => "61-90 (days)", "amount" => $total_aging3_invoice],
            'range4' => ['name' => "91+ (days)", "amount" => $total_aging4_invoice]
        ];
        $data['aging_invoice_receivables'] = ['amount_0_30' => $total_aging1_invoice, "amount_31_60" => $total_aging2_invoice, "amount_61_90" => $total_aging3_invoice, "amount_90_plus" => $total_aging4_invoice];

        echo json_encode($data);
    }

    public function aging_accounts()
    {
        $today = date('Y-m-d');
        $createddate = DateTime::createFromFormat('Y-m-d', $today);
        $createddate->modify('-30 day');
        $minus_thirty = $createddate->format('Y-m-d');

        $createddate2 = DateTime::createFromFormat('Y-m-d', $today);
        $createddate2->modify('-60 day');
        $minus_sixty = $createddate2->format('Y-m-d');

        $createddate3 = DateTime::createFromFormat('Y-m-d', $today);
        $createddate3->modify('-90 day');
        $minus_ninty = $createddate3->format('Y-m-d');
        //type 1===invoices
        //type 2=== bills 

        $total_aging1_invoice = $this->get_aging_accounts($minus_thirty, date('Y-m-d'), 1);
        $total_aging2_invoice = $this->get_aging_accounts($minus_sixty, $minus_thirty, 1);
        $total_aging3_invoice = $this->get_aging_accounts($minus_ninty, $minus_sixty, 1);
        $total_aging4_invoice = $this->get_aging_accounts("1900-01-01", $minus_ninty, 1);
        $data['aging_receivables'] = [
            'range1' => ['name' => "0-30 (days)", "amount" => $total_aging1_invoice],
            'range2' => ['name' => "31-60 (days)", "amount" => $total_aging2_invoice],
            'range3' => ['name' => "61-90 (days)", "amount" => $total_aging3_invoice],
            'range4' => ['name' => "91+ (days)", "amount" => $total_aging4_invoice]
        ];

        $data['aging_invoice_receivables'] = ['amount_0_30' => $total_aging1_invoice, "amount_31_60" => $total_aging2_invoice, "amount_61_90" => $total_aging3_invoice, "amount_90_plus" => $total_aging4_invoice];

        $total_aging1_bill = $this->get_aging_accounts($minus_thirty, date('Y-m-d'), 2);
        $total_aging2_bill = $this->get_aging_accounts($minus_sixty, $minus_thirty, 2);
        $total_aging3_bill = $this->get_aging_accounts($minus_ninty, $minus_sixty, 2);
        $total_aging4_bill = $this->get_aging_accounts("1900-01-01", $minus_ninty, 2);
        $data['aging_payables'] = [
            'range1' => ['name' => "0-30 (days)", "amount" => $total_aging1_bill],
            'range2' => ['name' => "31-60 (days)", "amount" => $total_aging2_bill],
            'range3' => ['name' => "61-90 (days)", "amount" => $total_aging3_bill],
            'range4' => ['name' => "91+ (days)", "amount" => $total_aging4_bill]
        ];
        $data['aging_bill_payables'] = ['amount_0_30' => $total_aging1_bill, "amount_31_60" => $total_aging2_bill, "amount_61_90" => $total_aging3_bill, "amount_90_plus" => $total_aging4_bill];

        echo json_encode($data);
    }

    public function savings()
    {
        $this->load->model("DepositProduct_model");
        $neededcss = array("fieldset.css", "plugins/highcharts/code/css/highslide.css", "plugins/daterangepicker/daterangepicker-bs3.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/highcharts/code/highcharts.js", "plugins/highcharts/code/highcharts-3d.js", "plugins/highcharts/code/modules/exporting.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/modules/export-data.js", "plugins/highcharts/code/modules/series-label.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['title'] = $this->data['sub_title'] = "Savings Reports";
        $this->data['products'] = $this->DepositProduct_model->get_products('mandatory_saving=1');

        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('reports/savings/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function savings_schedule()
    {
        $this->load->model("savings_schedule_model");
        $data['data'] = $this->savings_schedule_model->get();
        echo json_encode($data);
    }

    public function savings_end_balance()
    {
        $startDate = new DateTime($this->input->post('fisc_date_from'));
        $endDate = new DateTime($this->input->post('fisc_date_to'));

        $dateInterval = new DateInterval('P1M');
        $datePeriod   = new DatePeriod($startDate, $dateInterval, $endDate);

        $accounts = $this->savings_account_model->get_savings_account();
        $data1["data"] = array();
        $data1["month_name"] = array("Account No", "Member Name");
        foreach ($accounts as $acc) {
            $counter1 = 1;
            $arr[$counter1] = array($acc['account_no'], $acc['member_name']);
            $counter = 1;
            foreach ($datePeriod as  $date) {
                $ending_balance = $this->transaction_model->get_sums("account_no_id=" . $acc['id'] . " AND status_id=1 AND (transaction_date BETWEEN'" . $date->format('Y-m-d') . "' AND '" . $date->format('Y-m-t') . "')");
                if ($this->input->post('fisc_date_to') <= date('Y-m-d')) {
                    $value = ($ending_balance['cash_bal'] != NULL) ? number_format($ending_balance['cash_bal'], 2) : 0;
                } else {
                    $value = 0;
                }
                array_push($arr[$counter1], $value);
                //$interest=number_format((($acc['interest_rate']>0?$acc['interest_rate']:0)/1200)*($ending_balance['cash_bal']!=NULL?$ending_balance['cash_bal']:0),2);
                // array_push($arr[$counter1],$interest);
                $counter++;
            }
            //array_push($arr[$counter1],$acc['interest_rate']);

            array_push($data1["data"], $arr[$counter1]);
            $counter1++;
        }
        foreach ($datePeriod as  $date) {
            array_push($data1["month_name"], $date->format('F') . " / " . $date->format('Y'));
            //array_push($data1["month_name"],'Interest');

        }
        //array_push($data1["month_name"],'Interest Rate');
        //array_push($data1["month_name"], $month_name);       
        echo json_encode($data1);
    }

    public function export_excel_savings_monthly_ending_bal($start_date, $end_date)
    {
        $_POST['fisc_date_from'] = $start_date != 'null' ? $start_date : '';
        $_POST['fisc_date_to'] = $end_date != 'null' ? $end_date : '';

        $startDate = new DateTime($this->input->post('fisc_date_from'));
        $endDate = new DateTime($this->input->post('fisc_date_to'));

        $dateInterval = new DateInterval('P1M');
        $datePeriod   = new DatePeriod($startDate, $dateInterval, $endDate);

        $accounts = $this->savings_account_model->get_savings_account();
        $data1["data"] = array();
        $data1["month_name"] = array("Account No", "Member Name");
        foreach ($accounts as $acc) {
            $counter1 = 1;
            $arr[$counter1] = array($acc['account_no'], $acc['member_name']);
            $counter = 1;
            foreach ($datePeriod as  $date) {
                $ending_balance = $this->transaction_model->get_sums("account_no_id=" . $acc['id'] . " AND status_id=1 AND (transaction_date BETWEEN'" . $date->format('Y-m-d') . "' AND '" . $date->format('Y-m-t') . "')");
                if ($this->input->post('fisc_date_to') <= date('Y-m-d')) {
                    $value = ($ending_balance['cash_bal'] != NULL) ? number_format($ending_balance['cash_bal'], 2) : 0;
                } else {
                    $value = 0;
                }
                array_push($arr[$counter1], $value);
                //$interest=number_format((($acc['interest_rate']>0?$acc['interest_rate']:0)/1200)*($ending_balance['cash_bal']!=NULL?$ending_balance['cash_bal']:0),2);
                // array_push($arr[$counter1],$interest);
                $counter++;
            }
            //array_push($arr[$counter1],$acc['interest_rate']);

            array_push($data1["data"], $arr[$counter1]);
            $counter1++;
        }
        foreach ($datePeriod as  $date) {
            array_push($data1["month_name"], $date->format('F') . " / " . $date->format('Y'));
            //array_push($data1["month_name"],'Interest');

        }

        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:K1");
        $sheet->setCellValue('A1', 'Savings Accounts - Monthly ending balances ' . date('d-M-Y', strtotime($start_date)) . ' Through to ' . date('d-M-Y', strtotime($end_date)));
        $sheet->getStyle("A1:K1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:K1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center

        $columnCount = 'A';
        foreach ($data1['month_name'] as $column) {
            $sheet->setCellValue($columnCount . '3', $column);

            $columnCount++;
        }
        $highestColumn = $sheet->getHighestColumn();

        $sheet->getStyle("A3:" . $highestColumn . '3')->getFont()->setBold(true);

        $colCount   =   'A';
        foreach ($data1['month_name'] as $index => $value) {
            $row_count = 4;
            if (isset($data1[$index])) {
                foreach ($data1[$index] as $colValue) {
                    $sheet->setCellValue($colCount . $row_count, $colValue);
                    $row_count++;
                }
            }

            $colCount++;
        }

        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Savings Monthly Ending Balances';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
    public function accounts()
    {
        $this->load->model("DepositProduct_model");
        $neededcss = array("fieldset.css", "plugins/highcharts/code/css/highslide.css", "plugins/daterangepicker/daterangepicker-bs3.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/highcharts/code/highcharts.js", "plugins/highcharts/code/highcharts-3d.js", "plugins/highcharts/code/modules/exporting.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/modules/export-data.js", "plugins/highcharts/code/modules/series-label.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['title'] = $this->data['sub_title'] = "accounts Reports";
        $this->data['products'] = $this->DepositProduct_model->get_products('mandatory_saving=1');

        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('reports/accounts/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function receivables()
    {
        $neededcss = array("fieldset.css", "plugins/highcharts/code/css/highslide.css", "plugins/daterangepicker/daterangepicker-bs3.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/highcharts/code/highcharts.js", "plugins/highcharts/code/highcharts-3d.js", "plugins/highcharts/code/modules/exporting.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/modules/export-data.js", "plugins/highcharts/code/modules/series-label.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['title'] = $this->data['sub_title'] = "Aging Receivables";
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('reports/receivables/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function payables()
    {
        $neededcss = array("fieldset.css", "plugins/highcharts/code/css/highslide.css", "plugins/daterangepicker/daterangepicker-bs3.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/highcharts/code/highcharts.js", "plugins/highcharts/code/highcharts-3d.js", "plugins/highcharts/code/modules/exporting.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/modules/export-data.js", "plugins/highcharts/code/modules/series-label.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['title'] = $this->data['sub_title'] = "Aging Payables";
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('reports/payables/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    private function get_aging_receivables($start_date, $end_date, $type)
    {
        $between = "( jtl.transaction_date BETWEEN '" . ($start_date) . "' AND '" . ($end_date) . "')";
        $category_sum = $this->reports_model->get_category_sums(TRUE, 1, $between);
        //print_r($category_sum);die();
        $total_debt = $category_sum['amount'];
        return $total_debt;
    }

    private function get_aging_accounts($start_date, $end_date, $type)
    {
        $between = "( due_date BETWEEN '" . ($start_date) . "' AND '" . ($end_date) . "')";
        if ($type == 1) {
            $category_sum = $this->reports_model->get_aging_invoices($between);
        } else {
            $category_sum = $this->reports_model->get_aging_bills($between);
        }
        if (!empty($category_sum)) {
            $paid_and_discount = (int) $category_sum[0]['total_discount'] + (int) $category_sum[0]['amount_paid'];
        } else {
            $paid_and_discount = 0;
        }
        $total_dabt = empty($category_sum) ? 0 : ((int) $category_sum[0]['total_amount'] - $paid_and_discount);

        return $total_dabt;
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

    private function get_bar_graph_cf_investment($start_date, $end_date)
    {
        $graph_data['yAxis']['title']['text'] = "UGX";
        $period_dates = [];
        if (($graph_periods = $this->helpers->get_graph_periods($end_date, $start_date)) !== TRUE) {
            $graph_data['title']['text'] = "Cash Flow From Investments (" . $graph_periods['date_range'] . ")";
            $graph_data['xAxis'] = $graph_periods['xAxis'];
            $period_dates = $graph_periods['period_dates'];
        }
        if (!empty($period_dates)) {
            //we get the data for the specified periods
            //1. for the income
            $graph_data['datasets'][] = $this->get_period_sub_acc_sums($period_dates, 6);
        }
        if (!empty($graph_data)) {
            return $graph_data;
        } else {
            return false;
        }
    }

    private function get_bar_graph_cf_financing($start_date, $end_date)
    {
        $graph_data['yAxis']['title']['text'] = "UGX";
        $period_dates = [];
        if (($graph_periods = $this->helpers->get_graph_periods($end_date, $start_date)) !== TRUE) {
            $graph_data['title']['text'] = "Cash Flow From Financing (" . $graph_periods['date_range'] . ")";
            $graph_data['xAxis'] = $graph_periods['xAxis'];
            $period_dates = $graph_periods['period_dates'];
        }
        if (!empty($period_dates)) {
            //we get the data for the specified periods
            //1. for the income
            $graph_data['datasets'][] = $this->get_period_sub_acc_sums($period_dates, 10);
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
            if (isset($period_date['start_obj'])) {

                $start_obj = $period_date['start_obj'];
                $end_obj = $period_date['end_obj'];
                $between = "( jtl.transaction_date BETWEEN '" . ($start_obj->format('Y-m-d')) . "' AND '" . ($end_obj->format('Y-m-d')) . "')";
                $category_sum = $this->reports_model->get_account_sums_highcharts($between . " AND  `category_id`=$category_id", TRUE);
                $datasets['data'][] = empty($category_sum) ? 0 : ((int) $category_sum[0]['amount']);
            }
        }
        return $datasets;
    }

    private function get_period_sub_acc_sums($period_dates, $sub_category_id)
    {
        $categories = [
            6 => ['name' => "Fixed Assets", "marker" => ["symbol" => "square"]],
            10 => ['name' => "Long Term Liabilities", "marker" => ["symbol" => "diamond"]]
        ];
        $datasets = $categories[$sub_category_id];
        foreach ($period_dates as $period_date) {
            if (isset($period_date['start_obj'])) {
                $start_obj = $period_date['start_obj'];
                $end_obj = $period_date['end_obj'];
                $between = "( jtl.transaction_date BETWEEN '" . ($start_obj->format('Y-m-d')) . "' AND '" . ($end_obj->format('Y-m-d')) . "')";
                $category_sum = $this->reports_model->get_account_sums_highcharts($between . " AND  acc_sub.id=$sub_category_id", FALSE);
                $datasets['data'][] = empty($category_sum) ? 0 : ((int) $category_sum[0]['amount']);
            }
        }
        return $datasets;
    }

    public function ledgers_display($subcategories, $group)
    {
        if ($this->input->post('print') != 1) {
            $journal_type_id = " jt.journal_type_id !=26 ";
        } else {
            $journal_type_id = " 1 ";
        }
        $array = array();
        if (!empty($subcategories)) {
            foreach ($subcategories as $key => $subcategories_value) {
                $inner_array = [
                    'id' => $subcategories_value['id'],
                    'account_name' => $subcategories_value['sub_cat_name'],
                    'account_code' => $subcategories_value['sub_cat_code'],
                    'debit_sum' => isset($subcategories_value['debit_sum']) ? $subcategories_value['debit_sum'] : 0,
                    'credit_sum' => isset($subcategories_value['credit_sum']) ? $subcategories_value['credit_sum'] : 0,
                    'amount' => isset($subcategories_value['amount']) ? (($subcategories_value['normal_balance_side'] == 2) ? ($subcategories_value['credit_sum'] - $subcategories_value['debit_sum']) : ($subcategories_value['debit_sum'] - $subcategories_value['credit_sum'])) : 0,
                    'cat' => 1,
                ];
                $array[] = $inner_array;
                //$sub_category_accounts = $this->set_sub_category_accounts($subcategories_value['id']);
                $sub_category_id = $subcategories_value['id'];
                $sub_category_accounts = $this->reports_model->get_accounts_sums("ac.sub_category_id=$sub_category_id", FALSE, $journal_type_id);
                if (!empty($sub_category_accounts)) {
                    if ($group == 1) {
                        foreach ($sub_category_accounts as $sub_category_account) {
                            $inner_array = [
                                'id' => $sub_category_account['id'],
                                'cat' => 0,
                                'account_id' => $sub_category_account['id'],
                                'account_name' => $sub_category_account['account_name'],
                                'account_code' => $sub_category_account['account_code'],
                                'debit_sum' => $sub_category_account['debit_sum'] ? $sub_category_account['debit_sum'] : 0,
                                'credit_sum' => $sub_category_account['credit_sum'] ? $sub_category_account['credit_sum'] : 0,
                                'amount' => ((isset($sub_category_account['debit_sum']) ? $sub_category_account['debit_sum'] : 0) - (isset($sub_category_account['credit_sum']) ? $sub_category_account['credit_sum'] : 0))
                            ];
                            $array[] = $inner_array;
                        }
                    } else {
                        foreach ($sub_category_accounts as $sub_category_account) {
                            $inner_array = [
                                'id' => $sub_category_account['id'],
                                'cat' => 0,
                                'account_id' => $sub_category_account['id'],
                                'account_name' => $sub_category_account['account_name'],
                                'account_code' => $sub_category_account['account_code'],
                                'debit_sum' =>  $sub_category_account['debit_sum'] ? $sub_category_account['debit_sum'] : 0,
                                'credit_sum' => $sub_category_account['credit_sum'] ? $sub_category_account['credit_sum'] : 0,
                                'amount' => ((isset($sub_category_account['credit_sum']) ? $sub_category_account['credit_sum'] : 0) - (isset($sub_category_account['debit_sum']) ? $sub_category_account['debit_sum'] : 0))
                            ];
                            $array[] = $inner_array;
                        }
                    }
                }
            }
        }
        return $array;
    }

    private function set_sub_category_accounts($sub_category_id)
    {
        $array = array();
        if (!empty($sub_category_accounts)) {
            foreach ($sub_category_accounts as $sub_category_account) {
                $inner_array = [
                    'id' => $sub_category_account['id'],
                    'account_id' => $sub_category_account['account_name'],
                    'account_name' => $sub_category_account['account_name'],
                    'account_code' => $sub_category_account['account_code'],
                    'debit_sum' => $sub_category_account['debit_sum'],
                    'credit_sum' => $sub_category_account['credit_sum'],
                    'amount' => ((isset($sub_category_account['credit_sum']) ? $sub_category_account['credit_sum'] : 0) - (isset($sub_category_account['debit_sum']) ? $sub_category_account['debit_sum'] : 0)),
                ];
                $array[] = $inner_array;
            }
        }

        return $array;
    }

    public function closed_loans_export_excel(
        $end_date,
        $start_date,
        $min_amount,
        $max_amount,
        $product_id,
        $loan_type,
        $client_id,
        $group_id

    ) {
        //$_POST['end_date'] = $end_date;
        // $_POST['start_date'] = $start_date;
        $_POST['state_ids'] = [8, 9, 10, 14];
        $_POST['min_amount'] = $min_amount != '0' ? $min_amount : '';
        $_POST['max_amount'] = $max_amount != '0' ? $max_amount : '';
        $_POST['product_id'] = $product_id != '0' ? $product_id : '';
        $_POST['loan_type'] = $loan_type != 'null' ? $loan_type : '';
        $_POST['client_id'] = $client_id != '0' ? $client_id : '';
        $_POST['group_id'] = $group_id != '0' ? $group_id : '';
        $_POST['report'] = true;

        if (!is_numeric($_POST['loan_type']) && empty($_POST['loan_type'])) {
            unset($_POST['loan_type']);
        } else {
            $_POST['loan_type'] = intval($_POST['loan_type']) == 0 ? '0' : '1';
        }

        $this->load->model('Staff_model');
        $this->load->model('branch_model');
        $this->load->model('client_loan_model');
        $this->load->model('organisation_model');

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $dataArray = $this->client_loan_model->get_dTable();

        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:I1");

        $sheet->setCellValue('A1', 'Closed Loans ');

        // $sheet->setCellValue('A1', 'Closed Loans ' . date('d-M-Y', strtotime($start_date)) . ' Through to ' . date('d-M-Y', strtotime($end_date)));

        $sheet->getStyle("A1:I1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:I1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center

        $sheet->setCellValue('A3', 'LOAN NUMBER');
        $sheet->setCellValue('B3', 'CLIENT NAME');
        $sheet->setCellValue('C3', 'REQUESTED AMOUNT');
        $sheet->setCellValue('D3', 'DISBURSED AMOUNT');
        $sheet->setCellValue('E3', 'EXPECTED INTEREST');
        $sheet->setCellValue('F3', 'PAID AMOUNT');
        $sheet->setCellValue('G3', 'UNPAID AMOUNT');
        $sheet->setCellValue('H3', 'CLOSING STATE');
        $sheet->setCellValue('I3', 'CLOSING DATE');

        $sheet->getStyle("A3:I3")->getFont()->setBold(true);

        $rowCount   =   4;
        foreach ($dataArray as $loan) {
            $unpaid_amount = $loan['paid_amount'] ? (($loan['expected_principal'] + $loan['expected_interest']) - $loan['paid_amount']) : ($loan['expected_principal'] + $loan['expected_interest']);

            $sheet->setCellValue('A' . $rowCount, $loan['loan_no']);
            $sheet->setCellValue('B' . $rowCount, $loan['member_name'] ? $loan['member_name'] : $loan['group_name']);
            $sheet->setCellValue('C' . $rowCount, $loan['requested_amount']);
            $sheet->setCellValue('D' . $rowCount, $loan['amount_approved']);
            $sheet->setCellValue('E' . $rowCount, $loan['expected_interest']);
            $sheet->setCellValue('F' . $rowCount, $loan['paid_amount']);
            $sheet->setCellValue('G' . $rowCount, $unpaid_amount);
            $sheet->setCellValue('H' . $rowCount, $loan['state_name']);
            $sheet->setCellValue('I' . $rowCount, $loan['action_date'] ? date('d-M-Y', strtotime($loan['action_date'])) : '');

            $rowCount++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('C4:C' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('D4:D' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('E4:E' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F4:F' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G4:G' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'I' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTAL');
        $sheet->getStyle($total_row)->getFont()->setBold(true);

        // calculate totals
        $sheet->setCellValue('C' . ($highestRow + 2), '=SUM(C4:C' . $highestRow . ')');
        $sheet->setCellValue('D' . ($highestRow + 2), '=SUM(D4:D' . $highestRow . ')');
        $sheet->setCellValue('E' . ($highestRow + 2), '=SUM(E4:E' . $highestRow . ')');
        $sheet->setCellValue('F' . ($highestRow + 2), '=SUM(F4:F' . $highestRow . ')');
        $sheet->setCellValue('G' . ($highestRow + 2), '=SUM(G4:G' . $highestRow . ')');

        $sheet->getStyle('C' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('D' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('E' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');


        $writer = new Xlsx($spreadsheet);
        $filename = 'Closed Loans';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function in_arrears_loans_export_excel(
        $min_amount,
        $max_amount,
        $min_days_in_arrears,
        $max_days_in_arrears,
        $product_id,
        $loan_type,
        $client_id,
        $group_id
    ) {
        $_POST['state_id'] = 13;
        $_POST['min_days_in_arrears'] = $min_days_in_arrears != 'null' ? $min_days_in_arrears : '';
        $_POST['max_days_in_arrears'] = $max_days_in_arrears != 'null' ? $max_days_in_arrears : '';
        $_POST['min_amount'] = $min_amount != 'null' ? $min_amount : '';
        $_POST['max_amount'] = $max_amount != 'null' ? $max_amount : '';
        $_POST['product_id'] = $product_id != 'null' ? $product_id : '';
        $_POST['loan_type'] = $loan_type != 'null' ? $loan_type : '';
        $_POST['client_id'] = $client_id != 'null' ? $client_id : '';
        $_POST['group_id'] = $group_id != 'null' ? $group_id : '';
        $_POST['report'] = true;

        if (!is_numeric($_POST['loan_type']) && empty($_POST['loan_type'])) {
            unset($_POST['loan_type']);
        } else {
            $_POST['loan_type'] = intval($_POST['loan_type']) == 0 ? '0' : '1';
        }

        $this->load->model('Staff_model');
        $this->load->model('branch_model');
        $this->load->model('client_loan_model');
        $this->load->model('organisation_model');

        // $start_date = $this->input->post('start_date');
        // $end_date = $this->input->post('end_date');
        $dataArray = $this->client_loan_model->get_dTable();

        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LOAN NUMBER');
        $sheet->setCellValue('B1', 'CLIENT NAME');
        $sheet->setCellValue('C1', 'REQUESTED AMOUNT');
        $sheet->setCellValue('D1', 'DISBURSED AMOUNT');
        $sheet->setCellValue('E1', 'EXPECTED INTEREST');
        $sheet->setCellValue('F1', 'PAID AMOUNT');
        $sheet->setCellValue('G1', 'UNPAID AMOUNT');
        $sheet->setCellValue('H1', 'LOAN DUE DATE');
        $sheet->setCellValue('I1', 'DAYS IN ARREARS');

        $sheet->getStyle("A1:I1")->getFont()->setBold(true);

        $rowCount   =   2;
        foreach ($dataArray as $loan) {
            $unpaid_amount = $loan['paid_amount'] ? (($loan['expected_principal'] + $loan['expected_interest']) - $loan['paid_amount']) : ($loan['expected_principal'] + $loan['expected_interest']);

            $sheet->setCellValue('A' . $rowCount, $loan['loan_no']);
            $sheet->setCellValue('B' . $rowCount, $loan['member_name'] ? $loan['member_name'] : $loan['group_name']);
            $sheet->setCellValue('C' . $rowCount, $loan['requested_amount']);
            $sheet->setCellValue('D' . $rowCount, $loan['amount_approved']);
            $sheet->setCellValue('E' . $rowCount, $loan['expected_interest']);
            $sheet->setCellValue('F' . $rowCount, $loan['paid_amount']);
            $sheet->setCellValue('G' . $rowCount, $unpaid_amount);
            $sheet->setCellValue('H' . $rowCount, date('d-M-Y', strtotime($loan['last_pay_date'])));
            $sheet->setCellValue('I' . $rowCount, $loan['days_in_arrears']);

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
        $filename = 'In-Arrears Loans';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function active_loans_export_excel(
        $min_amount,
        $max_amount,
        $product_id,
        $loan_type,
        $condition,
        $due_days,
        $credit_officer_id,
        $next_due_month,
        $next_due_year,
        $client_id,
        $group_id
    ) {
        $_POST['state_id'] = 7;
        $_POST['condition'] = $condition != 'null' ? $condition : '';
        $_POST['due_days'] = $due_days != 'null' ? $due_days : '';
        $_POST['next_due_year'] = $next_due_year != 'null' ? $next_due_year : '';
        $_POST['next_due_month'] = $next_due_month != 'null' ? $next_due_month : '';
        $_POST['credit_officer_id'] = $credit_officer_id != 'null' ? $credit_officer_id : '';
        $_POST['min_amount'] = $min_amount != 'null' ? $min_amount : '';
        $_POST['max_amount'] = $max_amount != 'null' ? $max_amount : '';
        $_POST['product_id'] = $product_id != 'null' ? $product_id : '';
        $_POST['loan_type'] = $loan_type != 'null' ? $loan_type : '';
        $_POST['client_id'] = $client_id != 'null' ? $client_id : '';
        $_POST['group_id'] = $group_id != 'null' ? $group_id : '';
        $_POST['report'] = true;

        $this->load->model('Staff_model');
        $this->load->model('branch_model');
        $this->load->model('client_loan_model');
        $this->load->model('organisation_model');

        $dataArray = $this->client_loan_model->get_dTable();

        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LOAN ID');
        $sheet->setCellValue('B1', 'LOAN NUMBER');
        $sheet->setCellValue('C1', 'CLIENT NAME');
        $sheet->setCellValue('D1', 'PRODUCT NAME');
        $sheet->setCellValue('E1', 'DISBURSED AMOUNT');
        $sheet->setCellValue('F1', 'EXPECTED INTEREST');
        $sheet->setCellValue('G1', 'PAID AMOUNT');
        $sheet->setCellValue('H1', 'UNPAID AMOUNT');
        $sheet->setCellValue('I1', 'DUE DAYS');
        $sheet->setCellValue('J1', 'DISBURSEMENT DATE');
        $sheet->setCellValue('K1', 'NEXT PAY DATE');
        $sheet->setCellValue('L1', 'LOAN DUE DATE');

        $sheet->getStyle("A1:L1")->getFont()->setBold(true);

        $rowCount   =   2;
        foreach ($dataArray as $loan) {
            $unpaid_amount = $loan['paid_amount'] ? (($loan['expected_principal'] + $loan['expected_interest']) - $loan['paid_amount']) : ($loan['expected_principal'] + $loan['expected_interest']);

            $sheet->setCellValue('A' . $rowCount, $loan['id']);
            $sheet->setCellValue('B' . $rowCount, $loan['loan_no']);
            $sheet->setCellValue('C' . $rowCount, $loan['member_name'] ? $loan['member_name'] : $loan['group_name']);
            $sheet->setCellValue('D' . $rowCount, $loan['product_name']);
            $sheet->setCellValue('E' . $rowCount, $loan['amount_approved']);
            $sheet->setCellValue('F' . $rowCount, $loan['expected_interest']);
            $sheet->setCellValue('G' . $rowCount, $loan['paid_amount']);
            $sheet->setCellValue('H' . $rowCount, $unpaid_amount);
            $sheet->setCellValue('I' . $rowCount, $loan['days_in_demand']);
            $sheet->setCellValue('J' . $rowCount, date('d-M-Y', strtotime($loan['action_date'])));
            $sheet->setCellValue('K' . $rowCount, $loan['next_pay_date'] ? date('d-M-Y', strtotime($loan['next_pay_date'])) : '');
            $sheet->setCellValue('L' . $rowCount, $loan['last_pay_date'] ? date('d-M-Y', strtotime($loan['last_pay_date'])) : '');

            $rowCount++;
        }

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('E2:E' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F2:F' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G2:G' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H2:H' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'L' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTAL');
        $sheet->getStyle($total_row)->getFont()->setBold(true);

        // calculate totals
        $sheet->setCellValue('E' . ($highestRow + 2), '=SUM(E2:E' . $highestRow . ')');
        $sheet->setCellValue('F' . ($highestRow + 2), '=SUM(F2:F' . $highestRow . ')');
        $sheet->setCellValue('G' . ($highestRow + 2), '=SUM(G2:G' . $highestRow . ')');
        $sheet->setCellValue('H' . ($highestRow + 2), '=SUM(H2:H' . $highestRow . ')');

        $sheet->getStyle('E' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');


        $writer = new Xlsx($spreadsheet);
        $filename = 'Active Loans';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function closed_loans_pdf_print_out()
    {
        $this->load->model('Staff_model');
        $this->load->model('branch_model');
        $this->load->model('client_loan_model');
        $this->load->model('organisation_model');
        $this->load->helper('pdf_helper');

        $data['start_date'] = $this->input->post('start_date');
        $data['end_date'] = $this->input->post('end_date');
        $data['title'] = $_SESSION["org_name"];
        $data['loan_product_data'] = $this->loan_product_model->get_product();
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        //fiscal_year
        $data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $data['title'] = $data['sub_title'] = "Closed Loan Reports";
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);

        // ini_set('memory_limit', '200M');
        $data['data'] = $this->client_loan_model->get_dTable();
        $filteredl_records_cnt = $this->client_loan_model->get_found_rows();
        $data['the_page_data'] = $this->load->view('reports/loans/closed_loan_print_out', $data, TRUE);
        $this->load->view('includes/pdf_template', $data);
    }

    public function active_loans_pdf_print_out()
    {
        $this->load->model('Staff_model');
        $this->load->model('branch_model');
        $this->load->model('client_loan_model');
        $this->load->model('organisation_model');
        $this->load->helper('pdf_helper');

        $data['start_date'] = $this->input->post('start_date');
        $data['end_date'] = $this->input->post('end_date');
        $data['title'] = $_SESSION["org_name"];
        $data['loan_product_data'] = $this->loan_product_model->get_product();
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        //fiscal_year
        $data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $data['title'] = $data['sub_title'] = "Active Loan Reports";
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);

        // ini_set('memory_limit', '200M');
        $data['data'] = $this->client_loan_model->get_dTable();
        $filteredl_records_cnt = $this->client_loan_model->get_found_rows();
        $data['the_page_data'] = $this->load->view('reports/loans/active_loan_print_out', $data, TRUE);
        $this->load->view('includes/pdf_template', $data);
    }

    public function in_arrears_loans_pdf_print_out()
    {
        $this->load->model('Staff_model');
        $this->load->model('branch_model');
        $this->load->model('client_loan_model');
        $this->load->model('organisation_model');
        $this->load->helper('pdf_helper');

        $data['start_date'] = $this->input->post('start_date');
        $data['end_date'] = $this->input->post('end_date');
        $data['title'] = $_SESSION["org_name"];
        $data['loan_product_data'] = $this->loan_product_model->get_product();
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        //fiscal_year
        $data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $data['title'] = $data['sub_title'] = "In-arrears Loan Reports";
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);

        // ini_set('memory_limit', '200M');
        $data['data'] = $this->client_loan_model->get_dTable();
        $filteredl_records_cnt = $this->client_loan_model->get_found_rows();
        $data['the_page_data'] = $this->load->view('reports/loans/in_arrears_loan_print_out', $data, TRUE);
        $this->load->view('includes/pdf_template', $data);
    }

    public function member_loan_history()
    {
        $this->load->model('branch_model');
        $this->load->model('loan_installment_payment_model');
        $this->load->model('loan_state_model');
        $this->load->model('loan_approval_model');
        $this->load->model('loan_guarantor_model');
        $this->load->model('applied_loan_fee_model');
        $this->load->model('repayment_schedule_model');
        $this->load->model('loan_collateral_model');
        $this->load->model('client_loan_model');
        $this->load->model('member_model');

        $data['title'] = $_SESSION["org_name"];
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        //fiscal_year
        $data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $data['title'] = $data['sub_title'] = "Loans History";
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);

        $data['data'] = $this->client_loan_model->get_all_client_loans();
        $data['member'] = $this->member_model->get_member_by_user_id(intval($this->input->post('member_id')));

        foreach ($data['data'] as $key => $value) {
            $where = "a.status_id = 1 AND a.client_loan_id = " . $value['id'];
            $data['data'][$key]['loan_guarantors'] = $this->loan_guarantor_model->get($where);
            $data['data'][$key]['loan_collateral'] = $this->loan_collateral_model->get($where);
            $data['data'][$key]['loan_approvals'] = $this->loan_approval_model->get($value['id']);
            $data['data'][$key]['active_state'] = $this->loan_state_model->get('state_id=7 AND client_loan_id=' . $value['id']);
            $data['data'][$key]['applied_fees'] = $this->applied_loan_fee_model->get('a.client_loan_id=' . $value['id']);
            $data['data'][$key]['repayment_schedules'] = $this->repayment_schedule_model->get('repayment_schedule.status_id = 1 AND client_loan_id=' . $value['id']);
            $data['data'][$key]['paid_schedules'] = $this->loan_installment_payment_model->get($value['id']);
        }

        $data['the_page_data'] = $this->load->view('reports/loans/history/view', $data, TRUE);

        echo json_encode($data['the_page_data']);
    }

    public function loans()
    {
        $this->load->model('Staff_model');
        $this->load->model('branch_model');
        $this->load->model('organisation_model');
        $this->load->model('member_model');

        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $this->data['members'] = $this->member_model->get_member_by_user_id("fms_member.status_id=1");

        $neededcss = array("fieldset.css", "1_12_1_jquery-ui.css", "plugins/daterangepicker/daterangepicker-bs3.css", "plugins/select2/select2.min.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/highcharts/code/highcharts.js", "plugins/highcharts/code/highcharts-3d.js", "plugins/highcharts/code/modules/exporting.js", "plugins/highcharts/code/modules/export-data.js", "plugins/highcharts/code/modules/series-label.js", "plugins/printjs/print.min.js", "plugins/select2/select2.full.min.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['loan_product_data'] = $this->loan_product_model->get_product();

        //fiscal_year
        $this->data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['title'] = $this->data['sub_title'] = "Loan Reports";
        //list of credit officers
        $this->data['credit_officers'] = $this->Staff_model->get_registeredby("`fms_staff`.`id` IN (SELECT `credit_officer_id` FROM `fms_client_loan`)");

        $this->template->title = $this->data['title'];
        $this->template->content->view('reports/loans/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function loan_graph_data()
    {
        //pie chart data
        $data['pie_chart']['active'] = $this->client_loan_model->combined_state_totals($state_ids = array(7));
        $data['pie_chart']['locked'] = $this->client_loan_model->combined_state_totals($state_ids = array(12));
        $data['pie_chart']['in_arrears'] = $this->client_loan_model->combined_state_totals($state_ids = array(13));

        $data['pie_chart']['partial'] = $this->client_loan_model->combined_state_totals($state_ids = array(1));
        $data['pie_chart']['pending'] = $this->client_loan_model->combined_state_totals($state_ids = array(5));
        $data['pie_chart']['approved'] = $this->client_loan_model->combined_state_totals($state_ids = array(6));

        $data['pie_chart']['obligation_met'] = $this->client_loan_model->combined_state_totals($state_ids = array(10));
        $data['pie_chart']['paid_off'] = $this->client_loan_model->combined_state_totals($state_ids = array(9));
        $data['pie_chart']['refinanced'] = $this->client_loan_model->combined_state_totals($state_ids = array(14));
        $data['pie_chart']['written_off'] = $this->client_loan_model->combined_state_totals($state_ids = array(8));

        $data['pie_chart']['writhdrawn'] = $this->client_loan_model->combined_state_totals($state_ids = array(4));
        $data['pie_chart']['cancelled'] = $this->client_loan_model->combined_state_totals($state_ids = array(3));
        $data['pie_chart']['rejected'] = $this->client_loan_model->combined_state_totals($state_ids = array(2));

        //column chart data  
        $data['column_chart']['active'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 7)");
        $data['column_chart']['locked'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 12)");
        $data['column_chart']['in_arrears'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 13)");

        $data['column_chart']['partial'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 1)");
        $data['column_chart']['pending'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 5)");
        $data['column_chart']['approved'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 6)");

        $data['column_chart']['paid_off'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 9)");
        $data['column_chart']['refinanced'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 14)");
        $data['column_chart']['written_off'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 8)");
        $data['column_chart']['obligation_met'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 10)");

        $data['column_chart']['writhdrawn'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 4)");
        $data['column_chart']['cancelled'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 3)");
        $data['column_chart']['rejected'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 2)");

        //line graph
        $data['totals']['application'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 1 OR c.state_id= 5 OR  c.state_id= 6)");
        $data['totals']['active'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 7 OR  c.state_id= 12 OR c.state_id= 13)");
        $data['totals']['closed'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 8 OR c.state_id= 9 OR c.state_id= 14 OR c.state_id= 10)");
        $data['totals']['terminated'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 2 OR  c.state_id= 3 OR  c.state_id= 4)");

        echo json_encode($data);
    }

    public function loan_print()
    {
        $this->load->model('organisation_model');
        $this->load->model('branch_model');
        if ($this->input->post('end_date') != NULL) {
            $filename = $this->input->post('filename');
            $paper = $this->input->post('paper');
            $orientation = $this->input->post('orientation');
            $stream = $this->input->post('stream');

            $pdf['title'] = $_SESSION["org_name"];
            $pdf['filename'] = $filename;
            $pdf['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
            $pdf['branch'] = $this->branch_model->get($_SESSION['branch_id']);

            //================PRINT OUT DATA AND VIEW ===============
            $data = $this->loan_print_data();
            $data['totals'] = $this->compute_totals($data);

            $data['loan_statuses'] = $this->input->post('statuses');
            $data['amounts'] = $this->input->post('amounts');
            $data['portfolio'] = $this->input->post('portfolio');
            $data['indicators'] = $this->input->post('indicators');
            $data['end_date'] = $this->input->post('end_date');
            $data['start_date'] = $this->input->post('start_date');
            $pdf['pdf_data'] = $this->load->view('reports/loans/loan_report_printout', $data, true);

            $html = $this->load->view('pdf_template', $pdf, true);

            //================END HERE AND GENERATE =======================
            $this->pdfgenerator->generate($html, $filename, $stream, $paper, $orientation);
        } else {
            $response['status'] = false;
            $response['message'] = 'Provide the end date';

            echo json_encode($response);
        }
    }

    private function compute_totals($array_data)
    {
        $totals = [];
        foreach ($array_data as $data_key => $data_value) {
            if (is_array($data_value)) {
                foreach ($data_value as $key2 => $param_value) {
                    $totals[$data_key][$key2] = 0;
                    foreach ($param_value as $key => $value) {
                        if (isset($value['amount'])) {
                            $totals[$data_key][$key2] += $value['amount'];
                        } else {
                            $totals[$data_key][$key2] += $value['total'];
                        }
                    }
                }
            }
        }
        return $totals;
    }

    public function loan_print_data()
    {
        $this->load->model("Loan_product_model");
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $data['statuses']['application'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 1 OR c.state_id= 5 OR  c.state_id= 6)");
        $data['statuses']['active'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 7 OR  c.state_id= 12 )");
        $data['statuses']['inarrears'] = $this->client_loan_model->product_combined_state_totals("c.state_id= 13");
        $data['statuses']['closed'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 9 OR c.state_id= 14 OR c.state_id= 10)");
        $data['statuses']['written_off'] = $this->client_loan_model->product_combined_state_totals("c.state_id= 8");

        $data['statuses']['terminated'] = $this->client_loan_model->product_combined_state_totals("(c.state_id= 2 OR  c.state_id= 3 OR  c.state_id= 4)");

        $products = $this->Loan_product_model->get_product();
        $data['span_size'] = count($products);
        foreach ($products as $key => $product) {
            $returned_data = $this->loan_amounts(' AND loan_product_id=' . $product['id'], $start_date, $end_date);
            $data['loan_amount']['disbursed'][] = array(
                'product_name' => $product['product_name'],
                'amount' => $returned_data['principal_disbursed']
            );
            $data['loan_amount']['collected'][] = array(
                'product_name' => $product['product_name'],
                'amount' => $returned_data['amount_paid']['already_principal_amount']
            );
            $data['loan_amount']['outstanding'][] = array(
                'product_name' => $product['product_name'],
                'amount' => $returned_data['principal_disbursed'] - $returned_data['amount_paid']['already_principal_amount']
            );
            $data['loan_amount']['interest'][] = array(
                'product_name' => $product['product_name'],
                'amount' => $returned_data['amount_paid']['already_interest_amount']
            );
            $data['loan_amount']['penalty'][] = array(
                'product_name' => $product['product_name'],
                'amount' => $returned_data['amount_paid']['already_paid_penalty']
            );
            $data['loan_amount']['written_off'][] = array(
                'product_name' => $product['product_name'],
                'amount' => $returned_data['extraordinary_writeoff']
            );
            $data['loan_amount']['average_loan_balance'][] = array(
                'product_name' => $product['product_name'],
                'amount' => $returned_data['average_loan_balance']
            );
            $data['loan_amount']['projected_interest_amount'][] = array(
                'product_name' => $product['product_name'],
                'amount' => $returned_data['projected_intrest_earnings']
            );

            //Loan Portfolio
            $data['loan_portfolio']['gross_loan_portfolio'][] = array(
                'product_name' => $product['product_name'],
                'amount' => $returned_data['gross_loan_portfolio']
            );
            $data['loan_portfolio']['portfolio_pending_approval'][] = array(
                'product_name' => $product['product_name'],
                'amount' => $returned_data['loan_count_pend_approval']['requested_amount']
            );

            //Risk Indicators
            $data['risk_indicators']['unpaid_penalty'][] = array(
                'product_name' => $product['product_name'],
                'amount' => $returned_data['penalty_total']['penalty_total']
            );
            $data['risk_indicators']['value_at_risk'][] = array(
                'product_name' => $product['product_name'],
                'amount' => $returned_data['value_at_risk']
            );
            $data['risk_indicators']['portfolio_at_risk'][] = array(
                'product_name' => $product['product_name'],
                'amount' => $returned_data['portfolio_at_risk']
            );
            $data['risk_indicators']['intrest_in_suspense'][] = array(
                'product_name' => $product['product_name'],
                'amount' => $returned_data['intrest_in_suspense']
            );
        }
        if ($this->input->post('print') != NULL && $this->input->post('print') == 1) {
            return $data;
        } else {
            echo json_encode($data);
        }
    }

    public function loan_amounts($added_filter = '', $start_date, $end_date, $end_date2 = '')
    {

        $this->load->model('Repayment_schedule_model');
        $this->load->model('Loan_installment_payment_model');
        $this->load->model('Dashboard_model');
        $where_clauses = $this->whereclause_construction($start_date, $end_date, $end_date2);

        // print_r($where_clauses); die;

        if(!$end_date2) {
            $end_date2 = date('Y-m-d', strtotime('-1 month', strtotime(date($end_date))));
        }

        $end_date2 = implode("", explode("-", $end_date2));
        $end_date = implode("", explode("-", $end_date));

        $journal_loans_disbursed = $this->reports_model->sum_loan_disbursed_credit_debit();
        $data['principal_disbursed'] = $journal_loans_disbursed['total_disbursed'];
        $amount_disbursed['principal_sum'] = $data['principal_disbursed'];

        $data['amount_paid'] = $this->Loan_installment_payment_model->sum_paid_installment_before(" payment_date >= " . $end_date2 . " AND payment_date <= " . $end_date . " " . $added_filter);

        // $amount_disbursed = $this->Repayment_schedule_model->sum_interest_principal_report(false, "state_id IN (7,8,9,10,12,13,14) AND payment_status <> 5" . $added_filter);
        // $data['principal_disbursed'] = $amount_disbursed['principal_sum'];

        //Interest
        $date_filter = " repayment_date >= " . $end_date2 . " AND repayment_date <= " . $end_date . " ";
        $projected_interest_amount = $this->Repayment_schedule_model->sum_interest_principal_report_before($date_filter . $added_filter, "state_id IN (7,13) AND payment_status IN (2,4)"); //Locked loans left out due to the fact that reconveries are stopped
        $total_interest_paid = $this->Loan_installment_payment_model->sum_paid_installment_before("state_id IN (7,13) AND payment_status =2 AND " . $date_filter . $added_filter);
        $data['projected_intrest_earnings'] = abs($projected_interest_amount['interest_sum']) - abs($total_interest_paid['already_interest_amount']);

        //Portfolio
        // $amt_disbursed_active_arrears = $this->Repayment_schedule_model->sum_interest_principal_report(false, "state_id IN (7,12,13)" . $added_filter);
        // $amt_paid_active_arrears = $this->Loan_installment_payment_model->sum_paid_installment("state_id IN (7,12,13)" . $added_filter);

        /* $data['gross_loan_portfolio'] = $amt_disbursed_active_arrears['principal_sum'] - $amt_paid_active_arrears['already_principal_amount']; */

        $data['gross_loan_portfolio'] = $amount_disbursed['principal_sum'] - $journal_loans_disbursed['total_paid_back'];

        //Write off amount
        $amt_disbursed_written_off = $this->Repayment_schedule_model->sum_interest_principal_report(false, "state_id =8" . $added_filter);
        $amt_paid_written_off = $this->Loan_installment_payment_model->sum_paid_installment("state_id =8" . $added_filter);
        $data['extraordinary_writeoff'] = $amt_disbursed_written_off['principal_sum'] - $amt_paid_written_off['already_principal_amount'];

        //penalty               
        $data['penalty_total'] = $this->penalty_calculation($where_clauses['between_interest'] . $added_filter, "'" . $end_date . "'");

        //data for use
        $data['loan_count_active'] = $this->client_loan_model->get_sum_count("loan_state.state_id=7" . $added_filter);
        $data['loan_count_locked'] = $this->client_loan_model->get_sum_count("loan_state.state_id=12" . $added_filter);
        $data['loan_count_arrias'] = $this->client_loan_model->get_sum_count("loan_state.state_id=13" . $added_filter);
        $data['loan_count_pend_approval'] = $this->client_loan_model->get_sum_count("loan_state.state_id=5" . $added_filter);
        $data['loan_count_partial'] = $this->client_loan_model->get_sum_count("loan_state.state_id IN (1,5,6)" . $added_filter);
        //average loan balance
        $total_loan_count = intval($data['loan_count_active']['loan_count']) + intval($data['loan_count_locked']['loan_count']) + intval($data['loan_count_arrias']['loan_count']);
        $data['average_loan_balance'] = ($data['gross_loan_portfolio']) / (($total_loan_count > 0) ? $total_loan_count : 1);

        //interest in suspense
        $amount_in_suspence = $this->Repayment_schedule_model->sum_interest_principal_report(false, "state_id IN (13)" . $added_filter);
        $amount_paid_13 = $this->Loan_installment_payment_model->sum_paid_installment("state_id IN (13)" . $added_filter);

        $data['intrest_in_suspense'] = abs($amount_in_suspence['interest_sum']) - abs($amount_paid_13['already_interest_amount']);

        $total_principal_balance = $amount_in_suspence['principal_sum'] - $amount_paid_13['already_principal_amount'];

        if (($total_principal_balance || $data['gross_loan_portfolio']) != 0) {
            $data['portfolio_at_risk'] = (abs($total_principal_balance) / abs($data['gross_loan_portfolio'])) * 100;
            $data['value_at_risk'] = $total_principal_balance;
        } else {
            $data['portfolio_at_risk'] = 0;
            $data['value_at_risk'] = 0;
        }

        return $data;
    }

    private function whereclause_construction($start_date, $end_date, $end_date2 = '')
    {
        $where_clauses = [];
        if ($start_date != NULL && $start_date != '' && $end_date != NULL && $end_date != '') {
            $where_clauses['between_interest'] = "(repayment_date BETWEEN '" . ($start_date) . "' AND '" . ($end_date) . "')";
            $where_clauses['between_interest1'] = "(repayment_date BETWEEN '" . ($start_date) . "' AND '" . ($end_date2) . "')";

            $where_clauses['between_install'] = "(payment_date BETWEEN '" . ($start_date) . "' AND '" . ($end_date) . "')";
            $where_clauses['between_install_1'] = "(payment_date BETWEEN '" . ($start_date) . "' AND '" . ($end_date2) . "')";
        } elseif ($start_date != NULL && $start_date != '') {
            $where_clauses['between_interest'] = "(repayment_date >='" . ($start_date) . "')";
            $where_clauses['between_interest1'] = "(repayment_date >='" . ($end_date2) . "')";

            $where_clauses['between_install'] = "(payment_date >='" . ($start_date) . "')";
            $where_clauses['between_install_1'] = "(payment_date >='" . ($end_date2) . "')";
        } elseif ($end_date != NULL && $end_date != '') {
            $where_clauses['between_interest'] = "(repayment_date <='" . ($end_date) . "')";
            $where_clauses['between_interest1'] = "(repayment_date <='" . ($end_date2) . "')";

            $where_clauses['between_install'] = "(payment_date <='" . ($end_date) . "')";
            $where_clauses['between_install_1'] = "(payment_date <='" . ($end_date2) . "')";
        } else {
            $where_clauses['between_interest'] = 1;
            $where_clauses['between_install'] = 1;

            $where_clauses['between_interest1'] = "(repayment_date <='" . ($end_date2) . "')";
            $where_clauses['between_install_1'] = "(payment_date <='" . ($end_date2) . "')";
        }
        return $where_clauses;
    }

    public function get_loan_indicators_data($mstart_date = false, $mend_date = false)
    {
        $this->load->model('Repayment_schedule_model');
        $this->load->model('Loan_installment_payment_model');
        $this->load->model('Dashboard_model');
        // ==== Reagan modified this function to cater for other reports ===
        if ($mend_date === false) {
            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
        } else {
            $start_date = $mstart_date;
            $end_date = $mend_date;
        }

        if ($end_date != NULL && $end_date != '') {
            $end_date2 = date('Y-m-d', strtotime('-1 month', strtotime(date($end_date))));
        } else {
            $today = date('d-m-Y');
            $end_date2 = date('Y-m-d', strtotime('-1 month', strtotime(date($today))));
        }

        $end_date2 = implode("", explode("-", $end_date2));
        $end_date = implode("", explode("-", $end_date));
        $start_date = implode("", explode("-", $start_date));

        $where_clauses = $this->whereclause_construction($start_date, $end_date, $end_date2);

        // $sums['total_assets']=$this->reports_model->get_category_sums(1,FALSE); 

        $journal_loans_disbursed = $this->reports_model->sum_loan_disbursed_credit_debit();
        $data['principal_disbursed'] = $journal_loans_disbursed['total_disbursed'];
        $amount_disbursed['principal_sum'] = $data['principal_disbursed'];

        //$amount_disbursed = $this->Repayment_schedule_model->sum_interest_principal_report(false, "state_id IN (7,8,9,10,12,13,14) AND payment_status <> 5 AND repayment_schedule.status_id=1");
        // $data['principal_disbursed'] = $amount_disbursed['principal_sum'];

        // print_r($data['principal_disbursed']); die;

        # Expected Principal 
        $expected_principal_interest = $this->Repayment_schedule_model->get_expected_principal_and_interest();
        $collected_principal_and_interest = $this->Repayment_schedule_model->get_collected_principal_and_interest();
        $disbursed_principal = $this->Repayment_schedule_model->get_disbursed_principal();


        $date_filter = " repayment_date >= " . $end_date2 . " AND repayment_date <= " . $end_date . " ";
        $projected_interest_amount = $this->Repayment_schedule_model->sum_interest_principal_report_before($date_filter, " state_id IN (7,13) AND payment_status IN (2,4) "); //Locked loans left out due to the fact that reconveries are stopped
        $total_interest_paid = $this->Loan_installment_payment_model->sum_paid_installment_before("state_id IN (7,13) AND payment_status =2 AND " . " repayment_date >= " . $start_date . " AND repayment_date <= " . $end_date . " ");
        $data['projected_intrest_earnings'] = abs($projected_interest_amount['interest_sum']) - abs($total_interest_paid['already_interest_amount']);

        $data['expected_principal'] = $expected_principal_interest['principal_sum'];
        $data['expected_interest'] = $expected_principal_interest['interest_sum'];
        $data['collected_principal'] = $collected_principal_and_interest['paid_principal_amount'];
        $data['collected_interest'] = $collected_principal_and_interest['paid_interest_amount'];
        $data['disbursed_principal'] = $disbursed_principal['principal_sum'];

        $data['amount_paid'] = $this->Loan_installment_payment_model->sum_paid_installment_before(" payment_date >= " . $start_date . " AND payment_date <= " . $end_date . " ");
        // $data['change_in_Portfolio'] =  abs($sums['total_assets']['amount']) - $amount_disbursed['principal_sum'] - $data['amount_paid']['already_principal_amount']; 

        // for active (including locked) or in arrears
        // $amt_disbursed_active_arrears = $this->Repayment_schedule_model->sum_interest_principal_report(false, "state_id IN (7,12,13)");
        // $amt_paid_active_arrears = $this->Loan_installment_payment_model->sum_paid_installment("state_id IN (7,12,13)");
        
        /* $data['gross_loan_portfolio'] = $amt_disbursed_active_arrears['principal_sum'] - $amt_paid_active_arrears['already_principal_amount']; */
        $data['gross_loan_portfolio'] = $amount_disbursed['principal_sum'] - $journal_loans_disbursed['total_paid_back'];

        // for extra ordinary writeoff
        $amt_disbursed_written_off = $this->Repayment_schedule_model->sum_interest_principal_report(false, "state_id =8");
        $amt_paid_written_off = $this->Loan_installment_payment_model->sum_paid_installment("state_id =8");
        $data['extraordinary_writeoff'] = $amt_disbursed_written_off['principal_sum'] - $amt_paid_written_off['already_principal_amount'];

        $data['loan_count_active'] = $this->client_loan_model->get_sum_count("loan_state.state_id=7");
        $data['loan_count_locked'] = $this->client_loan_model->get_sum_count("loan_state.state_id=12");
        $data['loan_count_arrias'] = $this->client_loan_model->get_sum_count("loan_state.state_id=13");
        $data['loan_count_pend_approval'] = $this->client_loan_model->get_sum_count("loan_state.state_id=5");
        $data['loan_count_partial'] = $this->client_loan_model->get_sum_count("loan_state.state_id IN (1,5,6)");

        //last row              
        //print_r($where_clauses);die();
        $data['penalty_total'] = $this->penalty_calculation($where_clauses['between_interest'], "'" . $end_date . "'");

        $amount_in_suspence = $this->Repayment_schedule_model->sum_interest_principal_report(false, "state_id IN (13)");
        $amount_paid_13 = $this->Loan_installment_payment_model->sum_paid_installment("state_id IN (13)");

        $data['intrest_in_suspense'] = abs($amount_in_suspence['interest_sum']) - abs($amount_paid_13['already_interest_amount']);

        $data['total_principal_balance'] = $amount_in_suspence['principal_sum'] - $amount_paid_13['already_principal_amount'];

        if (($data['total_principal_balance'] || $data['gross_loan_portfolio']) != 0 && abs($data['gross_loan_portfolio']) != 0) {
            $data['portfolio_at_risk'] = (abs($data['total_principal_balance']) / abs($data['gross_loan_portfolio'])) * 100;
            $data['value_at_risk'] = $data['total_principal_balance'];
        } else {
            $data['portfolio_at_risk'] = 0;
            $data['value_at_risk'] = 0;
        }

        //calculation values for percentage
        $data['active_before'] = $this->client_loan_model->get_sum_count_before("loan_state.state_id=7", $start_date, $end_date2);
        $data['locked_before'] = $this->client_loan_model->get_sum_count_before("loan_state.state_id=12", $start_date, $end_date2);
        $data['arrias_before'] = $this->client_loan_model->get_sum_count_before("loan_state.state_id=13", $start_date, $end_date2);
        $data['pend_approval_before'] = $this->client_loan_model->get_sum_count_before("loan_state.state_id=5", $start_date, $end_date2);
        $data['partial_before'] = $this->client_loan_model->get_sum_count_before("loan_state.state_id IN (1,5,6)", $start_date, $end_date2);

        $journal_loans_disbursed_before = $this->reports_model->sum_loan_disbursed_credit_debit(" IN (92,93,94) ", $end_date2, $end_date);
        $amount_disbursed_before['principal_sum'] = ($journal_loans_disbursed_before['total_disbursed']);

        //$amount_disbursed_before = $this->Repayment_schedule_model->sum_interest_principal_report_before(false, "state_id IN (7,8,9,10,12,13,14) AND payment_status <> 5", $start_date, $end_date2);
        $amount_paid_before = $this->Loan_installment_payment_model->sum_paid_installment_before(" payment_date >= " . $end_date2 . " AND payment_date <= " . $end_date . " ");

        $data['app_percentage'] = round(((abs($data['loan_count_partial']['loan_count']) - abs($data['partial_before']['loan_count'])) / ((abs($data['partial_before']['loan_count']) != 0) ? abs($data['partial_before']['loan_count']) : 1)) * 100, 2);

        $data['active_percentage'] = round(((abs($data['loan_count_active']['loan_count']) - abs($data['active_before']['loan_count'])) / ((abs($data['active_before']['loan_count']) != 0) ? abs($data['active_before']['loan_count']) : 1)) * 100, 2);

        $data['principal_disbursed_percentage'] = round(((abs($amount_disbursed['principal_sum']) - abs($amount_disbursed_before['principal_sum'])) / ((abs($amount_disbursed_before['principal_sum']) != 0) ? abs($amount_disbursed_before['principal_sum']) : 1)) * 100, 2);

        $data['principal_collected_percentage'] = round(((abs($data['amount_paid']['already_principal_amount']) - abs($amount_paid_before['already_principal_amount'])) / ((abs($amount_paid_before['already_principal_amount']) != 0) ? abs($amount_paid_before['already_principal_amount']) : 1)) * 100, 2);

        $data['loan_interest_percentage'] = round(((abs($data['amount_paid']['already_interest_amount']) - abs($amount_paid_before['already_interest_amount'])) / ((abs($amount_paid_before['already_interest_amount']) != 0) ? abs($amount_paid_before['already_interest_amount']) : 1)) * 100, 2);

        $data['paid_penalty_percentage'] = round(((abs($data['amount_paid']['already_paid_penalty']) - abs($amount_paid_before['already_paid_penalty'])) / ((abs($amount_paid_before['already_paid_penalty']) != 0) ? abs($amount_paid_before['already_paid_penalty']) : 1)) * 100, 2);

        $data['portfolio_pending_percentage'] = round(((abs($data['loan_count_pend_approval']['requested_amount']) - abs($data['pend_approval_before']['requested_amount'])) / ((abs($data['pend_approval_before']['requested_amount']) != 0) ? abs($data['pend_approval_before']['requested_amount']) : 1)) * 100, 2);

        $amt_written_off_before = $this->Repayment_schedule_model->sum_interest_principal_report_before(false, "state_id =8", $start_date, $end_date2);
        $amt_paid_written_off_before = $this->Loan_installment_payment_model->sum_paid_installment_before("state_id =8", $start_date, $end_date2);
        $data['extraordinary_writeoff_before'] = $amt_written_off_before['principal_sum'] - $amt_paid_written_off_before['already_principal_amount'];

        $data['writeoff_percentage'] = round(((abs($data['extraordinary_writeoff']) - abs($data['extraordinary_writeoff_before'])) / ((abs($data['extraordinary_writeoff_before']) != 0) ? abs($data['extraordinary_writeoff_before']) : 1)) * 100, 2);

        $total_interest_paid_before = $this->Loan_installment_payment_model->sum_paid_installment_before("state_id IN (7,13) AND payment_status =2 AND " . $where_clauses['between_interest1']);

        $projected_interest_amount_before = $this->Repayment_schedule_model->sum_interest_principal_report_before(" repayment_date >= " . $end_date2 . " AND repayment_date <= " . $end_date . " ", "state_id IN (7,13) AND payment_status IN (2,4)");

        $data['projected_intrest_earnings_before'] = abs($projected_interest_amount_before['interest_sum']) - abs($total_interest_paid_before['already_interest_amount']);

        $data['projected_intrest_earnings_percentage'] = round(((abs($data['projected_intrest_earnings']) - abs($data['projected_intrest_earnings_before'])) / ((abs($data['projected_intrest_earnings_before']) != 0) ? abs($data['projected_intrest_earnings_before']) : 1)) * 100, 2);

        // $amt_disbursed_active_arrears_before = $this->Repayment_schedule_model->sum_interest_principal_report_before(" repayment_date >= " . $end_date2 . " AND repayment_date <= " . $end_date . " ", "state_id IN (7,12,13)");

        // $amt_paid_active_arrears_before = $this->Loan_installment_payment_model->sum_paid_installment_before("state_id IN (7,12,13)", $start_date, $end_date2);

        $data['gross_loan_portfolio_before'] = ($amount_disbursed_before['principal_sum'] - $journal_loans_disbursed_before['total_paid_back']);

        $total_loan_count = intval($data['loan_count_active']['loan_count']) + intval($data['loan_count_locked']['loan_count']) + intval($data['loan_count_arrias']['loan_count']);
        $total_loan_count_before = intval($data['active_before']['loan_count']) + intval($data['locked_before']['loan_count']) + intval($data['arrias_before']['loan_count']);

        $data['average_loan_balance'] = ($data['gross_loan_portfolio']) / (($total_loan_count > 0) ? $total_loan_count : 1);
        $data['average_loan_balance_before'] = ($data['gross_loan_portfolio_before']) / (($total_loan_count_before > 0) ? $total_loan_count_before : 1);

        $data['gross_loan_portfolio_percentage'] = round(((abs($data['gross_loan_portfolio']) - abs($data['gross_loan_portfolio_before'])) / ((abs($data['gross_loan_portfolio_before']) != 0) ? abs($data['gross_loan_portfolio_before']) : 1)) * 100, 2);

        $data['average_loan_balance_percentage'] = round(((abs($data['average_loan_balance']) - abs($data['average_loan_balance_before'])) / ((abs($data['average_loan_balance_before']) != 0) ? abs($data['average_loan_balance_before']) : 1)) * 100, 2);

        $amount_in_suspence_before = $this->Repayment_schedule_model->sum_interest_principal_report_before(" repayment_date >= " . $end_date2 . " AND repayment_date <= " . $end_date . " ", "state_id IN (13)",);
        $amount_paid_13_before = $this->Loan_installment_payment_model->sum_paid_installment_before("state_id IN (13)", $end_date2, $end_date);

        $data['intrest_in_suspense_before'] = abs($amount_in_suspence_before['interest_sum']) - abs($amount_paid_13_before['already_interest_amount']);
        $data['intrest_in_suspense_percentage'] = round(((abs($data['intrest_in_suspense']) - abs($data['intrest_in_suspense_before'])) / ((abs($data['intrest_in_suspense_before']) != 0) ? abs($data['intrest_in_suspense_before']) : 1)) * 100, 2);

        $data['penalty_total_before'] = $this->penalty_calculation($where_clauses['between_interest1'], "'" . $end_date2 . "'");

        $data['penalty_percentage'] = round(((abs($data['penalty_total']) - abs($data['penalty_total_before'])) / ((abs($data['penalty_total_before']) != 0) ? abs($data['penalty_total_before']) : 1)) * 100, 2);

        $data['principal_balance_before'] = $amount_in_suspence_before['principal_sum'] - $amount_paid_13_before['already_principal_amount'];

        if (($data['principal_balance_before'] || $data['gross_loan_portfolio_before']) != 0 && abs($data['gross_loan_portfolio_before']) != 0) {
            $data['portfolio_at_risk_before'] = (abs($data['principal_balance_before']) / abs($data['gross_loan_portfolio_before'])) * 100;
            $data['value_at_risk_before'] = $data['principal_balance_before'];
        } else {
            $data['portfolio_at_risk_before'] = 0;
            $data['value_at_risk_before'] = 0;
        }

        $data['value_at_risk_percentage'] = round(((abs($data['value_at_risk']) - abs($data['value_at_risk_before'])) / ((abs($data['value_at_risk_before']) != 0) ? abs($data['value_at_risk_before']) : 1)) * 100, 2);

        $data['portfolio_at_risk_percentage'] = round(((abs($data['portfolio_at_risk']) - abs($data['portfolio_at_risk_before'])) / ((abs($data['portfolio_at_risk_before']) != 0) ? abs($data['portfolio_at_risk_before']) : 1)) * 100, 2);
        if ($mend_date === false) {
            echo json_encode($data);
        } else {
            return $data;
        }
    }
    public function get_daily_reports_data()
    {
        $this->load->model('Repayment_schedule_model');
        $this->load->model('Loan_installment_payment_model');
        $this->load->model('Dashboard_model');
        $this->load->model('Loan_fees_model');
        $this->load->model('client_loan_model');
        $this->load->model('Transaction_model');
        $this->load->model('Share_transaction_model');

        # Compute Total Principal disbursed
        $amount_disbursed = $this->Repayment_schedule_model->daily_sum_interest_principal();
        # Compute Collected/paid Principal, Interest, penalty sum
        $amount_paid = $this->Repayment_schedule_model->daily_sum_paid_principal_interest_penalty();


        $data['principal_disbursed'] = $amount_disbursed['principal_sum'];
        $data['expected_principal'] = $amount_disbursed['principal_expected'];
        $data['expected_interest'] = $amount_disbursed['interest_sum'];

        $data['collected_principal'] = $amount_paid['paid_principal_sum'];
        $data['collected_interest'] = $amount_paid['paid_interest_sum'];
        $data['collected_penalty'] = $amount_paid['paid_penalty_sum'];
        
        $data['active_loans_count'] = $this->client_loan_model->count_loans_in_state("mls.state_id IN(7,11,12)")['no_of_loans'];
        $data['closed_loans_count'] = $this->client_loan_model->count_loans_in_state("mls.state_id IN(8,9,10,15)")['no_of_loans'];
        $data['penalty_total'] = $this->penalty_calculation4(); // penalty_calculation4 to ignore date filters

        # Savings computation
        $data['savings_deposits'] = $this->Transaction_model->sum_savings_amounts("transaction_type_id >=1")['credit_amount'];
        $data['savings_withdraws'] = $this->Transaction_model->sum_savings_amounts("transaction_type_id >=1")['debit_amount'];

        # Shares computation
        $data['share_deposits'] = $this->Share_transaction_model->sum_share_amounts("transaction_type_id >=1")['credit_amount'];
        $data['share_withdraws'] = $this->Share_transaction_model->sum_share_amounts("transaction_type_id >=1")['debit_amount'];

        echo json_encode($data);
    }
    //penalty calculation
    private function penalty_calculation1($between_interest, $end_date)
    {
        $this->load->model('Repayment_schedule_model');

        $penalty_total = 0;
        $due_installments_data = $this->Repayment_schedule_model->due_installments_report($between_interest, $end_date);
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

    public function penalty_calculation4()
    {
        $this->load->model('Repayment_schedule_model');

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $filter = "";
        if($start_date) {
            $filter .= " DATE(a.repayment_date) >= '{$start_date}' ";
        }
        if($end_date) {
            $filter .= $filter ?  " AND DATE(a.repayment_date) <= '{$end_date}' " : " DATE(a.repayment_date) <= '{$end_date}' ";
        }

        $total_penalty['penalty_total'] = 0;
        $data['data'] = $this->Repayment_schedule_model->due_installments_report_daily_report($filter, $end_date);
        foreach ($data['data'] as $key => $value) {
            $due_installments_data = $this->Repayment_schedule_model->due_installments_data($value['id']);
            if (!empty($due_installments_data)) {
                $over_due_principal = $due_installments_data['due_principal'];
                if ($value['demanded_penalty'] > 0) {
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

                $data['data'][$key]['penalty_value'] = $value['demanded_penalty'] > 0 ? round($penalty_value + $value['demanded_penalty'], 0) : round($penalty_value, 0);
            } else {
                $data['data'][$key]['penalty_value'] = $value['demanded_penalty'];
            }
            $total_penalty['penalty_total'] += $data['data'][$key]['penalty_value'];
        }
        return $total_penalty;
    }
    public function penalty_calculation($between_interest, $end_date)
    {

        $this->load->model('Repayment_schedule_model');
        $total_penalty['penalty_total'] = 0;
        $data['data'] = $this->Repayment_schedule_model->due_installments_report($between_interest, $end_date);
        foreach ($data['data'] as $key => $value) {
            $due_installments_data = $this->Repayment_schedule_model->due_installments_data($value['id']);
            if (!empty($due_installments_data)) {
                $over_due_principal = $due_installments_data['due_principal'];
                if ($value['demanded_penalty'] > 0) {
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

                $data['data'][$key]['penalty_value'] = $value['demanded_penalty'] > 0 ? round($penalty_value + $value['demanded_penalty'], 0) : round($penalty_value, 0);
            } else {
                $data['data'][$key]['penalty_value'] = $value['demanded_penalty'];
            }
            $total_penalty['penalty_total'] += $data['data'][$key]['penalty_value'];
        }
        return $total_penalty;
    }

    public function print()
    {
        $this->load->model('organisation_model');
        $this->load->model('branch_model');
        $filename = $this->input->post('filename');
        $paper = $this->input->post('paper');
        $orientation = $this->input->post('orientation');
        $stream = $this->input->post('stream');

        $pdf['title'] = $_SESSION["org_name"];
        $pdf['filename'] = $filename;
        $pdf['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $pdf['branch'] = $this->branch_model->get($_SESSION['branch_id']);

        //===================== PRINT OUT DATA AND VIEW ====================
        $data = $this->query_reports();
        if ($this->input->post('period') == 3) {
            $pdf['pdf_data'] = $this->load->view('reports/printouts/query_reports_fiscal', $data, true);
        } else {
            $pdf['pdf_data'] = $this->load->view('reports/printouts/query_reports_view', $data, true);
        }
        $html = $this->load->view('pdf_template', $pdf, true);

        //===================== END HERE AND GENERATE =======================
        $this->pdfgenerator->generate($html, $filename, $stream, $paper, $orientation);
    }

    public function query_reports()
    {
        $period = $this->input->post('period');
        if ($period == 2) {
            $start_date = $data['start_date'] = $this->input->post('start_date');
            $end_date = $data['end_date'] = $this->input->post('end_date');
            $data['general_data'] = $this->savings_shares_and_others($start_date, $end_date);
            $data['loan_data'] = $this->get_loan_indicators_data($start_date, $end_date);
        } else if ($period == 1) {
            $end_date = $data['end_date'] = $this->input->post('end_date');
            $data['general_data'] = $this->savings_shares_and_others(false, $end_date);
            $data['loan_data'] = $this->get_loan_indicators_data(false, $end_date);
        } else {
            if (($period == 3) && ($this->input->post('fiscal_1') != "") && ($this->input->post('fiscal_1') != NULL)) {
                $year = $this->fiscal_model->get($this->input->post('fiscal_1'));
                $end_date = $year['end_date'];
                $data['general_data'] = $this->savings_shares_and_others(false, $end_date);
                $data['loan_data'] = $this->get_loan_indicators_data(false, $end_date);
                $data['start_date'] = $year['start_date'];
                $data['end_date'] = $year['end_date'];
            }
            if (($period == 3) && ($this->input->post('fiscal_2') != "") && ($this->input->post('fiscal_2') != NULL)) {
                $year = $this->fiscal_model->get($this->input->post('fiscal_2'));
                $end_date = $year['end_date'];
                $data['general_data1'] = $this->savings_shares_and_others(false, $end_date);
                $data['loan_data1'] = $this->get_loan_indicators_data(false, $end_date);
                $data['start_date1'] = $year['start_date'];
                $data['end_date1'] = $year['end_date'];
            }
            if (($period == 3) && ($this->input->post('fiscal_3') != "") && ($this->input->post('fiscal_3') != NULL)) {
                $year = $this->fiscal_model->get($this->input->post('fiscal_3'));
                $end_date = $year['end_date'];
                $data['general_data2'] = $this->savings_shares_and_others(false, $end_date);
                $data['loan_data2'] = $this->get_loan_indicators_data(false, $end_date);
                $data['start_date2'] = $year['start_date'];
                $data['end_date2'] = $year['end_date'];
            }
        }

        $data['period'] = $period;
        $data['fiscal_1'] = is_numeric($this->input->post('fiscal_1')) ? $this->input->post('fiscal_1') : 0;
        $data['fiscal_2'] = is_numeric($this->input->post('fiscal_2')) ? $this->input->post('fiscal_2') : 0;
        $data['fiscal_3'] = is_numeric($this->input->post('fiscal_3')) ? $this->input->post('fiscal_3') : 0;
        $data['membership'] = $this->input->post('membership');
        $data['period_savings'] = $this->input->post('savings');
        $data['loans'] = $this->input->post('loans');
        $data['shares'] = $this->input->post('shares');
        //print_r($data);die();
        if (is_numeric($this->input->post('print'))) {
            return $data;
        } else {
            $data['success'] = TRUE;
            echo json_encode($data);
        }
    }

    public function savings_shares_and_others($start_date = false, $end_date = false)
    {
        if ($start_date == false) {
            $transaction_range = "( transaction_date <= '" . $end_date . "')";
            $member_range = "( date_registered <= '" . $end_date . "')";
            $savings_range = "( date_registered <= '" . $end_date . "')";
            $shares_range = "( share_account.date_opened <= '" . $end_date . "')";
            $shares_trans_range = "( transaction_date <= '" . $end_date . "') GROUP  BY share_issuance_id";
        } else {
            $transaction_range = "( transaction_date BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
            $member_range = "( date_registered BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
            $savings_range = "( date_registered BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
            $shares_range = "( share_account.date_opened BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
            $shares_trans_range = "( transaction_date BETWEEN '" . $start_date . "' AND '" . $end_date . "') GROUP  BY share_issuance_id";
        }

        $this->load->model('share_transaction_model');
        $this->load->model('Share_issuance_category_model');
        $this->load->model('Shares_model');
        $this->load->model('Transaction_model');
        $this->load->model('Loan_guarantor_model');
        $this->load->model('member_model');
        $data['male_members'] = count($this->member_model->get("u.gender=1 AND m.status_id !=9 AND " . $member_range));

        $data['female_members'] = count($this->member_model->get("u.gender=0 AND m.status_id !=9 AND " . $member_range));

        $sh_cat = $this->Share_issuance_category_model->get_active_share_issuance_price();

        $data['savings'] = $this->Transaction_model->get_sums($transaction_range);

        $data['savings_count'] = count($this->Loan_guarantor_model->get_guarantor_savings2("(j.state_id = 5 OR j.state_id = 7 OR j.state_id = 12 OR j.state_id = 17 OR j.state_id = 18) AND " . $savings_range, false));

        $data['share_accounts'] = count($this->Shares_model->get("state_id IN(7,19) AND share_account.status_id=1 AND " . $shares_range));

        $data['share_report'] = $this->share_transaction_model->get_account_sums1($shares_trans_range);
        $data['rowSpan_value'] = count($data['share_report']) + 3;
        // $data['price_per_share'] = $sh_cat['price_per_share'];
        // $data['total_shares']=$data['share_report']['amount']/$sh_cat['price_per_share'];

        return $data;
    }

    public function member_details_report()
    { //The function needs to be broken down in smaller functions and the arrays to be made global

        $this->load->model('Loan_guarantor_model');
        $this->load->model('shares_model');

        $client_loans = [];
        $client_savings = [];
        $client_shares = [];
        $member_data = [];

        $members = $this->member_model->get_member("m.status_id=1");

        $loans_data = $this->client_loan_model->get_loans("loan_state.state_id=7");

        $savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7');

        $shares_data = $this->shares_model->get("share_state.state_id=7");

        foreach ($loans_data as $key => $value) {
            $client_loans[$value['member_id']][] = [
                "loan_no" => $value['loan_no'],
                "requested_amount" => $value['expected_principal'],
                "paid_principal" => $value['paid_principal']
            ];
        }

        foreach ($savings_data as $key => $value) {
            $client_savings[$value['member_id']][] = [
                "account_no" => $value['account_no'],
                "real_bal" => $value['real_bal']
            ];
        }

        foreach ($shares_data as $key => $value) {
            $client_shares[$value['member_id']][] = [
                "share_account_no" => $value['share_account_no'],
                "total_amount" => $value['total_amount'],
                "price_per_share" => $value['price_per_share']
            ];
        }

        foreach ($members as $key => $value) {
            if (isset($client_loans[$value['id']]) && isset($client_savings[$value['id']]) && isset($client_shares[$value['id']])) {

                if (sizeof($client_loans[$value['id']]) > sizeof($client_savings[$value['id']]) && sizeof($client_loans[$value['id']]) > sizeof($client_shares[$value['id']])) {

                    foreach ($client_loans[$value['id']] as $key1 => $value1) {
                        $member_data[] = [
                            "member_id" => $value['id'],
                            "member_name" => $value['firstname'] . ' ' . $value['lastname'] . ' ' . $value['othernames'],
                            "loan_no" => $value1['loan_no'],
                            "requested_amount" => $value1['requested_amount'],
                            "paid_principal" => $value1['paid_principal'],
                            "account_no" => (isset($client_savings[$value['id']][$key1]['account_no'])) ? $client_savings[$value['id']][$key1]['account_no'] : '',
                            "real_bal" => (isset($client_savings[$value['id']][$key1]['real_bal'])) ? $client_savings[$value['id']][$key1]['real_bal'] : '',
                            "share_account_no" => (isset($client_shares[$value['id']][$key1]['share_account_no'])) ? $client_shares[$value['id']][$key1]['share_account_no'] : '',
                            "total_amount" => (isset($client_shares[$value['id']][$key1]['total_amount'])) ? $client_shares[$value['id']][$key1]['total_amount'] : '',
                            "price_per_share" => (isset($client_shares[$value['id']][$key1]['price_per_share'])) ? $client_shares[$value['id']][$key1]['price_per_share'] : ''
                        ];
                    }
                } elseif (sizeof($client_savings[$value['id']]) > sizeof($client_loans[$value['id']]) && sizeof($client_savings[$value['id']]) > sizeof($client_shares[$value['id']])) {

                    foreach ($client_savings[$value['id']] as $key2 => $value2) {
                        $member_data[] = [
                            "member_id" => $value['id'],
                            "member_name" => $value['firstname'] . ' ' . $value['lastname'] . ' ' . $value['othernames'],
                            "account_no" => $value2['account_no'],
                            "real_bal" => $value2['real_bal'],
                            "loan_no" => (isset($client_loans[$value['id']][$key2]['loan_no'])) ? $client_loans[$value['id']][$key2]['loan_no'] : '',
                            "requested_amount" => (isset($client_loans[$value['id']][$key2]['requested_amount'])) ? $client_loans[$value['id']][$key2]['requested_amount'] : '',
                            "paid_principal" => (isset($client_loans[$value['id']][$key2]['paid_principal'])) ? $client_loans[$value['id']][$key2]['paid_principal'] : '',
                            "share_account_no" => (isset($client_shares[$value['id']][$key2]['share_account_no'])) ? $client_shares[$value['id']][$key2]['share_account_no'] : '',
                            "total_amount" => (isset($client_shares[$value['id']][$key2]['total_amount'])) ? $client_shares[$value['id']][$key2]['total_amount'] : '',
                            "price_per_share" => (isset($client_shares[$value['id']][$key2]['price_per_share'])) ? $client_shares[$value['id']][$key2]['price_per_share'] : ''
                        ];
                    }
                } else {

                    foreach ($client_shares[$value['id']] as $key3 => $value3) {
                        $member_data[] = [
                            "member_id" => $value['id'],
                            "member_name" => $value['firstname'] . ' ' . $value['lastname'] . ' ' . $value['othernames'],
                            "share_account_no" => $value3['share_account_no'],
                            "total_amount" => $value3['total_amount'],
                            "price_per_share" => $value3['price_per_share'],
                            "loan_no" => (isset($client_loans[$value['id']][$key3]['loan_no'])) ? $client_loans[$value['id']][$key3]['loan_no'] : '',
                            "requested_amount" => (isset($client_loans[$value['id']][$key3]['requested_amount'])) ? $client_loans[$value['id']][$key3]['requested_amount'] : '',
                            "paid_principal" => (isset($client_loans[$value['id']][$key3]['paid_principal'])) ? $client_loans[$value['id']][$key3]['paid_principal'] : '',
                            "account_no" => (isset($client_savings[$value['id']][$key3]['account_no'])) ? $client_savings[$value['id']][$key3]['account_no'] : '',
                            "real_bal" => (isset($client_savings[$value['id']][$key3]['real_bal'])) ? $client_savings[$value['id']][$key3]['real_bal'] : ''
                        ];
                    }
                }
            } elseif (isset($client_loans[$value['id']]) && !isset($client_savings[$value['id']]) && !isset($client_shares[$value['id']])) {

                foreach ($client_loans[$value['id']] as $key1 => $value1) {
                    $member_data[] = [
                        "member_id" => $value['id'],
                        "member_name" => $value['firstname'] . ' ' . $value['lastname'] . ' ' . $value['othernames'],
                        "loan_no" => $value1['loan_no'],
                        "requested_amount" => $value1['requested_amount'],
                        "paid_principal" => $value1['paid_principal'],
                        "account_no" => (isset($client_savings[$value['id']][$key1]['account_no'])) ? $client_savings[$value['id']][$key1]['account_no'] : '',
                        "real_bal" => (isset($client_savings[$value['id']][$key1]['real_bal'])) ? $client_savings[$value['id']][$key1]['real_bal'] : '',
                        "share_account_no" => (isset($client_shares[$value['id']][$key1]['share_account_no'])) ? $client_shares[$value['id']][$key1]['share_account_no'] : '',
                        "total_amount" => (isset($client_shares[$value['id']][$key1]['total_amount'])) ? $client_shares[$value['id']][$key1]['total_amount'] : '',
                        "price_per_share" => (isset($client_shares[$value['id']][$key1]['price_per_share'])) ? $client_shares[$value['id']][$key1]['price_per_share'] : ''
                    ];
                }
            } elseif (!isset($client_loans[$value['id']]) && isset($client_savings[$value['id']]) && !isset($client_shares[$value['id']])) {
                foreach ($client_savings[$value['id']] as $key2 => $value2) {
                    $member_data[] = [
                        "member_id" => $value['id'],
                        "member_name" => $value['firstname'] . ' ' . $value['lastname'] . ' ' . $value['othernames'],
                        "account_no" => $value2['account_no'],
                        "real_bal" => $value2['real_bal'],
                        "loan_no" => (isset($client_loans[$value['id']][$key2]['loan_no'])) ? $client_loans[$value['id']][$key2]['loan_no'] : '',
                        "requested_amount" => (isset($client_loans[$value['id']][$key2]['requested_amount'])) ? $client_loans[$value['id']][$key2]['requested_amount'] : '',
                        "paid_principal" => (isset($client_loans[$value['id']][$key2]['paid_principal'])) ? $client_loans[$value['id']][$key2]['paid_principal'] : '',
                        "share_account_no" => (isset($client_shares[$value['id']][$key2]['share_account_no'])) ? $client_shares[$value['id']][$key2]['share_account_no'] : '',
                        "total_amount" => (isset($client_shares[$value['id']][$key2]['total_amount'])) ? $client_shares[$value['id']][$key2]['total_amount'] : '',
                        "price_per_share" => (isset($client_shares[$value['id']][$key2]['price_per_share'])) ? $client_shares[$value['id']][$key2]['price_per_share'] : ''
                    ];
                }
            } elseif (!isset($client_loans[$value['id']]) && !isset($client_savings[$value['id']]) && isset($client_shares[$value['id']])) {
                foreach ($client_shares[$value['id']] as $key3 => $value3) {
                    $member_data[] = [
                        "member_id" => $value['id'],
                        "member_name" => $value['firstname'] . ' ' . $value['lastname'] . ' ' . $value['othernames'],
                        "share_account_no" => $value3['share_account_no'],
                        "total_amount" => $value3['total_amount'],
                        "price_per_share" => $value3['price_per_share'],
                        "loan_no" => (isset($client_loans[$value['id']][$key3]['loan_no'])) ? $client_loans[$value['id']][$key3]['loan_no'] : '',
                        "requested_amount" => (isset($client_loans[$value['id']][$key3]['requested_amount'])) ? $client_loans[$value['id']][$key3]['requested_amount'] : '',
                        "paid_principal" => (isset($client_loans[$value['id']][$key3]['paid_principal'])) ? $client_loans[$value['id']][$key3]['paid_principal'] : '',
                        "account_no" => (isset($client_savings[$value['id']][$key3]['account_no'])) ? $client_savings[$value['id']][$key3]['account_no'] : '',
                        "real_bal" => (isset($client_savings[$value['id']][$key3]['real_bal'])) ? $client_savings[$value['id']][$key3]['real_bal'] : ''
                    ];
                }
            } else {
                $member_data[] = [
                    "member_id" => $value['id'],
                    "member_name" => $value['firstname'] . ' ' . $value['lastname'] . ' ' . $value['othernames'],
                    "share_account_no" => '',
                    "total_amount" => '',
                    "price_per_share" => '',
                    "loan_no" => '',
                    "requested_amount" => '',
                    "paid_principal" => '',
                    "account_no" => '',
                    "real_bal" => ''
                ];
            }
        }

        echo json_encode($member_data);
    }

    //===============================================================================================================================

    /*
 ASSETS REPORT UI borrowed from SAVING REPORT 


*/
    //===============================================================================================================================
    public function asset_purchase_per_month()
    {
        $data['data'] = $this->transaction_model->get_asset_purchase_per_month($this->input->post('fisc_date_from'), $this->input->post('fisc_date_to'));
        echo json_encode($data);
    }

    public function assets_full_list()
    {

        $data['data'] = $this->transaction_model->asset_full_list();
        echo json_encode($data);
    }


    public function assets()
    {

        //$this->load->model("DepositProduct_model");
        $neededcss = array("fieldset.css", "plugins/highcharts/code/css/highslide.css", "plugins/daterangepicker/daterangepicker-bs3.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/highcharts/code/highcharts.js", "plugins/highcharts/code/highcharts-3d.js", "plugins/highcharts/code/modules/exporting.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/modules/export-data.js", "plugins/highcharts/code/modules/series-label.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['title'] = $this->data['sub_title'] = "Assets Reports";
        // $this->data['products'] = $this->DepositProduct_model->get_products('mandatory_saving=1');

        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('reports/assets/index', $this->data);
        // Publish the template
        $this->template->publish();
    }
}
