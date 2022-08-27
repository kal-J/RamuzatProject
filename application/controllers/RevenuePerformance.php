<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RevenuePerformance extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        $this->load->model("Revenue_performance_model");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->data['privilege_list'] = $this->helpers->user_privileges(12, $this->session->userdata('staff_id'));

        $this->data['module_access'] = $this->helpers->org_access_module(12, $_SESSION['organisation_id']);
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['billing_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        }

        
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
        $this->load->library("num_format_helper");
        $this->load->model('miscellaneous_model');
        $this->load->model("organisation_format_model");
        $this->load->model("RolePrivilege_model");
        $this->load->model("logs_model");

        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['revenue_data'] = $this->Revenue_performance_model->get_category_sums_data(4);

        //$this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 3');

        $this->data['title'] = $this->data['sub_title'] = "Revenue Performance";

        $this->template->title = $this->data['title'];

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css", "custom.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->template->content->view('reports/revenue_performance/index', $this->data);
        // Publish the template
        $this->template->publish();
    }


    public function jsonList() {
        $income = $this->Revenue_performance_model->get_category_sums_data(4);
        $fiscal_years = $this->Revenue_performance_model->get_years_gone_on_fiscal_year();
        $current_year = date("Y");
        
        $expenses = $this->Revenue_performance_model->get_category_sums_data(5);

        echo json_encode(['income'=>$income,'expenses' => $expenses, "fiscal_years" => $fiscal_years, "current_year" => $current_year]);
    }

    public function print_revenue_performance($year, $month)
    {
        $_POST['year'] = $year;
        $_POST['month'] = $month;
        $_POST['print'] = 1;

        if($month != "All") {
            $this->print_single_month_revenue_performance($year, $month);
        }else {
            $this->print_all_month_revenue_performance($year);
        }
    }

    private function print_single_month_revenue_performance($year, $month) {
        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:C1");
        $sheet->setCellValue('A1', 'Revenue Performance for ' .$month. " (".$year.")");
        $sheet->getStyle("A1:C1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:C1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center

        $sheet->mergeCells("A2:B2");
        $sheet->setCellValue('A2', 'ITEM');
        $sheet->setCellValue('C2', $month);
        $sheet->getStyle("A2:C2")->getFont()->setBold(true);

        $sheet->mergeCells("A3:C3");
        $sheet->setCellValue('A3', 'INCOME');
        $sheet->getStyle("A3:C3")->getFont()->setBold(true)->setSize(14);

        // data
        $rowCount = 4;
        $income = $this->Revenue_performance_model->get_category_sums_data(4);
        $expenses = $this->Revenue_performance_model->get_category_sums_data(5);
        foreach ($income['data'] as $value) {
            $sheet->mergeCells("A".$rowCount.":B".$rowCount);
            $sheet->setCellValue('A'.$rowCount, $value['name']);
            $sheet->setCellValue('C'.$rowCount, $value['income']);
            $rowCount++;
        }
        // total
        $sheet->mergeCells("A".$rowCount.":B".$rowCount);
        $sheet->setCellValue('A'.$rowCount, "TOTAL");
        $sheet->setCellValue('C'.$rowCount, $income['total']);
        $sheet->getStyle("A".$rowCount.":C".$rowCount)->getFont()->setBold(true)->setSize(11);
        $rowCount++; //increment the row count again

        // expenses
        $sheet->mergeCells("A".$rowCount.":C".$rowCount);
        $sheet->setCellValue('A'.$rowCount, "EXPENSES");
        $sheet->getStyle("A".$rowCount.":C".$rowCount)->getFont()->setBold(true)->setSize(14);
        $rowCount++; //increment the row count again
        foreach ($expenses['data'] as $value) {
            $sheet->mergeCells("A".$rowCount.":B".$rowCount);
            $sheet->setCellValue('A'.$rowCount, $value['name']);
            $sheet->setCellValue('C'.$rowCount, $value['income']);
            $rowCount++;
        }
        // total
        $sheet->mergeCells("A".$rowCount.":B".$rowCount);
        $sheet->setCellValue('A'.$rowCount, "TOTAL");
        $sheet->setCellValue('C'.$rowCount, $expenses['total']);
        $sheet->getStyle("A".$rowCount.":C".$rowCount)->getFont()->setBold(true)->setSize(11);
        $rowCount++; //increment the row count again

        //net profit
        $sheet->mergeCells("A".$rowCount.":B".$rowCount);
        $sheet->setCellValue('A'.$rowCount, "Net Profit");
        $sheet->setCellValue('C'.$rowCount, $income['total'] - $expenses['total']);
        $sheet->getStyle("A".$rowCount.":C".$rowCount)->getFont()->setBold(true)->setSize(11);
        $rowCount++; //increment the row count again

        // auto size
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }


        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('B4:B' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('C4:C' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $writer = new Xlsx($spreadsheet);
        $filename = "revenue performance ". $month. " (".$year.")";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

    }

    private function print_all_month_revenue_performance($year){
        $income = $this->Revenue_performance_model->get_category_sums_data(4);
        $expenses = $this->Revenue_performance_model->get_category_sums_data(5);
        $months = $income['table_months'];
        $cell_columns_count = count($income['table_months']);
        $cell_numbers = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P'];
        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:".$cell_numbers[$cell_columns_count+1]."1");
        $sheet->setCellValue('A1', 'Revenue Performance for ' .$year);
        $sheet->getStyle("A1:".$cell_numbers[$cell_columns_count+1]."1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A1:".$cell_numbers[$cell_columns_count+1]."1")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center


        $sheet->mergeCells("A2:B2");
        $sheet->setCellValue('A2', 'ITEM');
        $x = 2;
        for($i = 0; $i<$cell_columns_count; $i++){
            $sheet->setCellValue($cell_numbers[$x].'2', $months[$i]);
            $x++;
        }
        $sheet->getStyle("A2:".$cell_numbers[$cell_columns_count + 1]."2")->getFont()->setBold(true);

        $sheet->mergeCells("A3:".$cell_numbers[$cell_columns_count+1]."3");
        $sheet->setCellValue('A3', 'INCOME');
        $sheet->getStyle("A3:".$cell_numbers[$cell_columns_count + 1]."3")->getFont()->setBold(true)->setSize(14);

        // income
        $rowCount = 4;
        foreach ($income['data'] as $value) {
            $sheet->mergeCells("A".$rowCount.":B".$rowCount);
            $sheet->setCellValue('A'.$rowCount, $value['name']);
            $x = 2;
            $total = 0;
            foreach ($value['data'] as $key => $val) {
                $sheet->setCellValue($cell_numbers[$x].$rowCount,  $value['unformated_data'][$key]);
                $sheet->getStyle($cell_numbers[$x].$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $total += $value['unformated_data'][$key];
                $x++;
            }
            //total
            $sheet->setCellValue($cell_numbers[$x].$rowCount, ($total));
            $sheet->getStyle($cell_numbers[$x].$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle($cell_numbers[$x].$rowCount)->getFont()->setBold(true)->setSize(10);
            $rowCount++;
        }
        // income total
        $sheet->mergeCells("A".$rowCount.":B".$rowCount);
        $sheet->setCellValue('A'.$rowCount, "TOTAL");
        $sheet->getStyle('A'.$rowCount)->getFont()->setBold(true)->setSize(10);
        $x = 2;
        foreach ($income['total'] as $value) {
            $sheet->setCellValue($cell_numbers[$x].$rowCount, $value);
            $sheet->getStyle($cell_numbers[$x].$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle($cell_numbers[$x].$rowCount)->getFont()->setBold(true)->setSize(10);
            $x++;
        }
        $rowCount++;

        // expenses
        $sheet->mergeCells("A".$rowCount.":".$cell_numbers[$cell_columns_count+1].$rowCount);
        $sheet->setCellValue("A".$rowCount, 'EXPENSES');
        $sheet->getStyle("A".$rowCount.":".$cell_numbers[$cell_columns_count+1].$rowCount)->getFont()->setBold(true)->setSize(14);
        $rowCount++;

        foreach ($expenses['data'] as $value) {
            $sheet->mergeCells("A".$rowCount.":B".$rowCount);
            $sheet->setCellValue('A'.$rowCount, $value['name']);
            $x = 2;
            $total = 0;
            foreach ($value['data'] as $key => $val) {
                $sheet->setCellValue($cell_numbers[$x].$rowCount, $value['unformated_data'][$key]);
                $sheet->getStyle($cell_numbers[$x].$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $total += $value['unformated_data'][$key];
                $x++;
            }
            //total
            $sheet->setCellValue($cell_numbers[$x].$rowCount, $total);
            $sheet->getStyle($cell_numbers[$x].$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle($cell_numbers[$x].$rowCount)->getFont()->setBold(true)->setSize(10);
            $rowCount++;
        }
        // expense total
        $sheet->mergeCells("A".$rowCount.":B".$rowCount);
        $sheet->setCellValue('A'.$rowCount, "TOTAL");
        $sheet->getStyle('A'.$rowCount)->getFont()->setBold(true)->setSize(10);
        $x = 2;
        foreach ($expenses['total'] as $value) {
            $sheet->setCellValue($cell_numbers[$x].$rowCount, $value);
            $sheet->getStyle($cell_numbers[$x].$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle($cell_numbers[$x].$rowCount)->getFont()->setBold(true)->setSize(10);
            $x++;
        }
        $rowCount++;

        // net profit
        $sheet->mergeCells("A".$rowCount.":B".$rowCount);
        $sheet->setCellValue('A'.$rowCount, "Net Profit");
        $sheet->getStyle('A'.$rowCount)->getFont()->setBold(true)->setSize(10);
        $x = 2;
        foreach ($expenses['total'] as $key => $value) {
            $sheet->setCellValue($cell_numbers[$x].$rowCount, ($income['total'][$key]- $expenses['total'][$key]));
            $sheet->getStyle($cell_numbers[$x].$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle($cell_numbers[$x].$rowCount)->getFont()->setBold(true)->setSize(10);
            $x++;
        }
        $rowCount++;
        // accm profit
        $sheet->mergeCells("A".$rowCount.":B".$rowCount);
        $sheet->setCellValue('A'.$rowCount, "Accm Profit");
        $sheet->getStyle('A'.$rowCount)->getFont()->setBold(true)->setSize(10);
        $x = 2;
        $prev_total = 0;
        foreach ($expenses['total'] as $key => $value) {
            $prev_total += $income['total'][$key]- $expenses['total'][$key];
            $sheet->setCellValue($cell_numbers[$x].$rowCount, $prev_total);
            $sheet->getStyle($cell_numbers[$x].$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle($cell_numbers[$x].$rowCount)->getFont()->setBold(true)->setSize(10);
            $x++;
        }
        $rowCount++;

        // auto size
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }



        $writer = new Xlsx($spreadsheet);
        $filename = "revenue performance for ". $year;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

}