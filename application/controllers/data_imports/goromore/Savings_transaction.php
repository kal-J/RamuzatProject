<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Reagan
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Savings_transaction extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('/');
        }
        $this->load->model("Data_import_model");
        $this->load->model("journal_transaction_line_model");
        
        $this->load->library("helpers");
        
         ini_set('memory_limit', '200M');
            ini_set('upload_max_filesize', '200M');
            ini_set('post_max_size', '200M');
            ini_set('max_input_time', 3600);
            ini_set('max_execution_time', 3600);
    }


  public function import()
    {
        $feedback['success']=false;
       $folder = "data_extract".DIRECTORY_SEPARATOR."goromore".DIRECTORY_SEPARATOR;
        $file_name = "savings_transactions.csv";
        $file_path = FCPATH . $folder . $file_name;
        
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file_path);
            $spreadsheet = $reader->load($file_path);

            $failed =$passed =0;
            $failed_data = array();
          
            foreach($spreadsheet->getWorksheetIterator() as $worksheet)
            {
                
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
                 
                for($row=2; $row<=$highestRow; $row++) {
                    $counter_amount=4;$counter_date=5;
                    for ($col = $counter_date; $col <= $highestColumnIndex; $col=$col+2) {
                        
                    $savings_account_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $name = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    $amount = $worksheet->getCellByColumnAndRow($counter_amount, $row)->getValue();
                    $trans_date = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                  
                    //$savings_account = $this->member_model->get_member_info($client_no);
                    if (!empty($amount) && ($amount > 0)) {
            $transaction_date = $this->helpers->extract_date_time($trans_date,"Y-m-d");
            $trans_row = [
                "transaction_no" =>  date('yws').mt_rand(10000000,99999999),
                "account_no_id" => $savings_account_id,
                "debit" => NULL,
                "credit" => $amount,
                "transaction_type_id" => 2,
                "payment_id" =>2,
                "transaction_date" => $transaction_date,
                "narrative" => strtoupper($name." - Monthly Savings -[ ".$transaction_date." ]"),
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1
            ];
            $transaction_data=$this->Data_import_model->add_transaction($trans_row);
             $single_row = [
                "journal_type_id" =>7,
                "ref_id" => $savings_account_id,
                "ref_no" => $transaction_data['transaction_no'],
                "description" => strtoupper($name." - Monthly Savings -[ ".$transaction_date." ]"),
                "transaction_date" =>  $transaction_date,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" =>1
            ];
            $insert_id=$this->Data_import_model->add_journal_tr($single_row);
            
                 
                $data[0] = [
                    'debit_amount' => NULL,
                    'transaction_date' =>  $transaction_date,
                    'reference_id' => $savings_account_id,
                    'reference_no' => $transaction_data['transaction_no'],
                    'credit_amount' =>$amount,
                    'narrative' => strtoupper($name." - Monthly Savings  -[ ".$transaction_date." ]"),
                    'account_id' =>8 ,
                    'status_id' => 1
                ];
                $data[1] = [
                    'credit_amount' =>NULL,
                    'transaction_date' =>$transaction_date,
                    'reference_id' => $savings_account_id,
                    'reference_no' => $transaction_data['transaction_no'],
                    'debit_amount' => $amount,
                    'narrative' =>  strtoupper($name." - Monthly Savings  -[ ".$transaction_date." ]"),
                    'account_id' => 40,
                    'status_id' => 1
                ];
              $this->Data_import_model->add_journal_tr_line($insert_id, $data);
            
                        $passed++;

                  }else{
                     $failed++;
                  }
                     $counter_amount=$counter_amount+2;
                        $counter_date=$counter_date+2;
                    }
                }
            }
            $response ="Records Imported successfully";
            $feedback['message'] = "( ".$passed." ) ".$response." ( ".$failed." ) Failed , Check error log table";
            
            $feedback['success'] = true;
      
        
        echo json_encode($feedback);
    }
}
