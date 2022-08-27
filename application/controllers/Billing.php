<?php

/**
 * Description of Billings
 *
 * @author kalujja
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Billing extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->data['privilege_list'] = $this->helpers->user_privileges(25, $this->session->userdata('staff_id'));

        $this->data['module_access'] = $this->helpers->org_access_module(25, $_SESSION['organisation_id']);
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['billing_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        }

        $this->load->model("member_model");
        $this->load->model('billing_model');
        $this->load->model("user_model");

        $this->load->model('accounts_model');
        $this->load->model('ledger_model');
        $this->load->model('Fiscal_month_model');
        $this->load->model('miscellaneous_model');
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

    public function jsonList()
    {
        $data['data'] = $this->billing_model->get();
        echo json_encode($data);
    }

    public function pdf_print_out() {
        $this->load->model('branch_model');
        $this->load->model('organisation_model');
        $this->load->helper('pdf_helper');

        $data['billing'] = $this->billing_model->get();
        if(!empty($_POST['month'])) {
            $data['sub_title'] = date('F-Y', strtotime($_POST['month'] . '-01')) . ' SMS BILLING';
        } else {
            $data['sub_title'] = 'SMS BILLING';
        }
        
        $data['title'] = $_SESSION["org_name"];
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $data['the_page_data'] = $this->load->view('billing/pdf_print_out', $data, TRUE);

        $this->helpers->activity_logs($_SESSION['id'], 6, "Exporting PDF data", "SMS BILLING", NULL, NULL);

        echo json_encode($data);
    }

    public function export_excel($month = false) {
        if ($month) {
            $_POST['month'] = $month;
            $month = date('M-Y', strtotime($month . '-01'));
        }
        $dataArray = $this->billing_model->get();

       
        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:H1");
        if (!empty($month)) {
            $sheet->setCellValue('A1', ' SMS BILLING FOR ' . $month);
        } else {
            $sheet->setCellValue('A1', 'SMS BILLING');
        }


        $sheet->getStyle("A1:H1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:H1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center

        $sheet->setCellValue('A3', 'CLIENT NO');
        $sheet->setCellValue('B3', 'NAME');
        $sheet->setCellValue('C3', 'MOBILE NUMBER');
        $sheet->setCellValue('D3', 'No. of Messages');
        $sheet->setCellValue('E3', 'TOTAL COST');

        $sheet->getStyle("A3:E3")->getFont()->setBold(true);

        $rowCount   =   4;
        foreach ($dataArray as $data) {
            
            $sheet->setCellValue('A' . $rowCount, $data['client_no']);
            $sheet->setCellValue('B' . $rowCount, mb_strtoupper($data['member_name'], 'UTF-8'));
            $sheet->setCellValue('C' . $rowCount, mb_strtoupper($data['mobile_number'], 'UTF-8'));
            $sheet->setCellValue('D' . $rowCount, $data['no_of_msgs']);
            $sheet->setCellValue('E' . $rowCount, $data['cost'] * $data['no_of_msgs']);

            $rowCount++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('D4:D' . $highestRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('E4:E' . $highestRow)->getNumberFormat()->setFormatCode('#,##0');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'G' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTAL');
        $sheet->getStyle($total_row)->getFont()->setBold(true);

        // calculate totals
        $sheet->setCellValue('D' . ($highestRow + 2), '=SUM(D4:D' . $highestRow . ')');
        $sheet->setCellValue('E' . ($highestRow + 2), '=SUM(E4:E' . $highestRow . ')');

        $sheet->getStyle('D' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('E' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0');

        $writer = new Xlsx($spreadsheet);
        $filename = !empty($month) ? $month . '-SMS-BILLING' :  'SMS-BILLING';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        $this->helpers->activity_logs($_SESSION['id'], 6, "Exporting data", "Exported data" . $filename, NULL, NULL);
    }

    public function member_sms_jsonList()
    {
        $member_id = $this->input->post('member_id');

        $data['data'] = $this->billing_model->get_member_sms_list($member_id);
        echo json_encode($data);
    }

    public function index()
    {
        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] = array_column($this->data['module_list'], "module_id");
        $this->load->library("num_format_helper");
        $this->load->model('miscellaneous_model');
        $this->load->model("organisation_format_model");
        $this->load->model("RolePrivilege_model");

        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);

        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 3');

        $this->data['title'] = $this->data['sub_title'] = "Billing";

        $this->template->title = $this->data['title'];

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js", "plugins/printjs/print.min.js");

        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css", "custom.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->template->content->view('billing/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function sms_billing_details($member_id)
    {
        if ($member_id == false) {
            redirect("my404");
        } else {
            $this->data['user'] = $this->member_model->get_member($member_id);
            if (empty($this->data['user'])) {
                redirect("my404");
            }
        }

        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] = array_column($this->data['module_list'], "module_id");
        $this->load->library("num_format_helper");
        $this->load->model('miscellaneous_model');
        $this->load->model("organisation_format_model");
        $this->load->model("RolePrivilege_model");

        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);

        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 3');

        $this->data['title'] = "Billing";
        $this->data['sub_title'] = $this->data['user']['firstname'] . " " . $this->data['user']['lastname'];
        $this->data['member_id'] = $member_id;
        $this->template->title = $this->data['title'];

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css", "custom.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->template->content->view('billing/sms/details', $this->data);
        // Publish the template
        $this->template->publish();

    }

}
