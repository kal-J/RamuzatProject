<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Fixed_savings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        }
        $this->load->model("Fixed_savings_model");
        $this->load->model("user_model");
        $this->load->model("Loan_guarantor_model");
    }

    public function jsonList(){
        $data['data'] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7 AND fs.status=1');
        echo json_encode($data);
    }

    public function export_excel() {
        $dataArray = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7 AND fs.status=1');

        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:H1");

           $sheet->setCellValue('A1', 'FIXED SAVINGS ACCOUNTS'); 
        

        $sheet->getStyle("A1:H1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:H1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center

        $sheet->setCellValue('A3', 'ACCOUNT NO');
        $sheet->setCellValue('B3', 'ACCOUNT HOLDER');
        $sheet->setCellValue('C3', 'PRODUCT');
        $sheet->setCellValue('D3', 'QUALIFYING AMOUNT');
        $sheet->setCellValue('E3', 'START DATE');
        $sheet->setCellValue('F3', 'END DATE');

        $sheet->getStyle("A3:F3")->getFont()->setBold(true);

        $rowCount   =   4;
        foreach ($dataArray as $data) {
            $qualifying_bal = $data['type'] == 0 ? $data['qualifying_amount'] : $data['real_bal'];

            $sheet->setCellValue('A' . $rowCount, $data['account_no']);
            $sheet->setCellValue('B' . $rowCount, mb_strtoupper($data['member_name'], 'UTF-8'));
            $sheet->setCellValue('C' . $rowCount, mb_strtoupper($data['productname'], 'UTF-8'));
            $sheet->setCellValue('D' . $rowCount, $qualifying_bal);
            $sheet->setCellValue('E' . $rowCount, date('d-M-Y', strtotime($data['start_date'])));
            $sheet->setCellValue('F' . $rowCount, date('d-M-Y', strtotime($data['end_date'])));

            $rowCount++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('D4:D' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'F' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTAL');
        $sheet->getStyle($total_row)->getFont()->setBold(true);

        // calculate totals
        $sheet->setCellValue('D' . ($highestRow + 2), '=SUM(D4:D' . $highestRow . ')');

        $sheet->getStyle('D' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Fixed Saving Accounts';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function check_data()
    {
        $data['data'] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7 AND fs.savings_account_id=113');
        echo json_encode($data);
    }


    public function jsonList2(){
        $where = FALSE;
        if (isset($_POST['acc_id'])===TRUE) {
            $where = 'fs.savings_account_id='. $this->input->post("acc_id");
        }
        if ($this->input->post('start_date') && $this->input->post('end_date')) {
            $where = 'fs.savings_account_id='. $this->input->post("acc_id").'fs.start_date >='. $this->input->post('start_date').' AND fs.end_date <='.$this->input->post('end_date');
        }

        $data['data'] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7 AND '.$where);
        //$data['data'] = $this->Fixed_savings_model->get($where);
        echo json_encode($data);
    }

    public function set()
    {
        $this->form_validation->set_rules('start_date', 'Start Date', 'required');
        $this->form_validation->set_rules('end_date', 'End Date', 'required');
        //$this->form_validation->set_rules('qualifying_amount', 'Qualifying Amount', 'required');

        $savings_account_id = $this->input->post('savings_account_id');
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
                $this->data['account'] = $this->Fixed_savings_model->get_fixed_account($savings_account_id);
                if (empty($this->data['account'])) {
                    if ($this->Fixed_savings_model->fix()) {
                        $feedback['success'] = true;
                        $feedback['message']='Savings account successfully fixed';
                    }else {
                        $feedback['message'] = "There was a problem Fixing this savings account";
                    }
                }else{
                    $feedback['message'] = "Please deactivate the current running fixed amount, Two or more running fixed amounts are not allowed!";
                }

        }

        echo json_encode($feedback);
    }

    public function delete(){
        $response['message'] = "Account could not be deactivated, contact support.";
        $response['success'] = FALSE;
        if($this->Fixed_savings_model->change_status()){
            $response['success'] = TRUE;
            $response['message'] = "Account successfully deactivated.";
        }
        echo json_encode($response);
    }

}
