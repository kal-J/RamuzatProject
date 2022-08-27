<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') OR exit('No direct script access allowed');

class Till extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        $this->load->library("helpers");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        }
        
        $this->load->model('Staff_model');
        $this->load->model('User_model');
        $this->load->model("Fiscal_month_model");
        $this->load->model("dashboard_model");
        $this->load->model("organisation_model");
        $this->load->model("organisation_format_model");
        $this->load->model('Role_model');

        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 26, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 26, $_SESSION['organisation_id']);
        if(empty($this->data['module_access'])){
            redirect('my404');
        } else {
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['till_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        }
         $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
            if(empty($this->data['fiscal_active'])){
                redirect('dashboard');
            }else{

            $this->data['lock_month_access'] = $this->helpers->org_access_module($module_id = 23, $_SESSION['organisation_id']);
                if(!empty($this->data['lock_month_access'])){
                    $this->data['active_month'] = $this->Fiscal_month_model->get_active_month();
                    if(empty($this->data['active_month'])){
                       redirect('dashboard');
                    }
                } 
            }
        }
    }

    public function index() {
        $this->load->library("num_format_helper");
        $this->load->model('miscellaneous_model');
        $this->load->model('TransactionChannel_model');
        $this->data['title'] = $this->data['sub_title'] = "Cash Register";
        $this->template->title = $this->data['title'];
         $this->data['module_list']=$this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] =array_column($this->data['module_list'],"module_id");
        $this->data['tchannel'] = $this->TransactionChannel_model->get2();
        $this->data['staff_list'] = $this->Staff_model->get_registeredby("status_id=1");
        $this->data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);

          $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js", "plugins/printjs/print.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css", "custom.css");
        
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        // Load a view in the content partial
        $this->template->content->view('till/index', $this->data);
        
        // Publish the template
        $this->template->publish();
    }

    public function cashRegister(){
         $accoun_id=$this->input->post("account_id");
        $start_date=$this->input->post("start_date");
        $end_date=$this->input->post("end_date");
        $end_date2=date('Y-m-d', strtotime($end_date));

        $fiscal_year = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        if($fiscal_year['start_date']==$start_date){
           $data['balance']=0;
           $cb=$this->reports_model->get_accounts_sums_one($accoun_id,$fiscal_year['start_date'],$end_date2);
           $data['closing']=$cb['debit_sum']-$cb['credit_sum'];
        }else{
            $start_date1=$fiscal_year['start_date'];
            $end_date1=date('Y-m-d', strtotime($start_date . ' -1 days'));

            $bf=$this->reports_model->get_accounts_sums_one($accoun_id,$start_date1,$end_date1);
            $data['balance']=$bf['debit_sum']-$bf['credit_sum'];

            $cb=$this->reports_model->get_accounts_sums_one($accoun_id,$start_date1,$end_date2);
            $data['closing']=$cb['debit_sum']-$cb['credit_sum'];
        }

        echo json_encode($data);
       
    }

    public function export_to_excel() {
        $this->load->model("Journal_transaction_line_model");
        $data = $this->Journal_transaction_line_model->get_dTable();

        // echo json_encode($data); die;

          // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:J1");
        $sheet->setCellValue('A1', "Cash Register");
        $sheet->getStyle("A1:J1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:J1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center

            // header
        $sheet->setCellValue('A2', 'Date');
        $sheet->setCellValue('B2', "Ref. ID");
        $sheet->setCellValue('C2', "Ref. No");
        $sheet->setCellValue('D2', "Ref. Name");
        $sheet->setCellValue('E2', "Account");
        $sheet->setCellValue('F2', "Journal Type");
        $sheet->setCellValue('G2', "Debit");
        $sheet->setCellValue('H2', "Credit");
        $sheet->setCellValue('I2', "Narrative");
        $sheet->setCellValue('J2', "Staff");
        $sheet->getStyle("A2:J2")->getFont()->setBold(true);

        // body
        $rowCount = 3;
        $credit_sum = 0;
        $debit_sum = 0;

        foreach($data as $single){
            $sheet->setCellValue('A'.$rowCount, $single['transaction_date']);
            $sheet->setCellValue('B'.$rowCount, $single['ref_id']);
            $sheet->setCellValue('C'.$rowCount, $single['ref_no']);
            $sheet->setCellValue('D'.$rowCount, $single['member_name']);
            $sheet->setCellValue('E'.$rowCount, '['.$single['account_code'].']'.$single['account_name']);
            $sheet->setCellValue('F'.$rowCount, $single['type_name']);
            $sheet->setCellValue('G'.$rowCount, number_format($single['debit_amount']));
            $sheet->setCellValue('H'.$rowCount, number_format($single['credit_amount']));
            $sheet->setCellValue('I'.$rowCount, $single['narrative']);
            $sheet->setCellValue('J'.$rowCount, $single['staff_name']);
            $credit_sum += $single['credit_amount'];
            $debit_sum += $single['debit_amount'];
            // print_r($single); die;
            $rowCount++;
        }

        //totals

        $sheet->mergeCells("A".$rowCount.":F".$rowCount);
        $sheet->setCellValue("A".$rowCount, "Totals");
        $sheet->setCellValue("G".$rowCount, number_format($debit_sum));
        $sheet->setCellValue("H".$rowCount, number_format($credit_sum));
        $sheet->getStyle("A".$rowCount.":J".$rowCount)->getFont()->setBold(true)->setSize(12);
         $rowCount++;
        // auto size
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "cash register";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }


}
