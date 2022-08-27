<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Reagan
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Fees extends CI_Controller {

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
       $folder = "data_extract".DIRECTORY_SEPARATOR;
        $file_name = "fees.csv";
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
                    for ($col = 3; $col <= $highestColumnIndex; $col++) {
                    $member_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $amount = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $trans_date = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
                   echo $this->helpers->extract_date_time($trans_date,"Y-m-d")."<BR>";
                }
            }

                   die();
                    //$savings_account = $this->member_model->get_member_info($client_no);
                    if (!empty($amount) && ($amount > 0)) {
                      $transaction_date = $this->helpers->extract_date_time($trans_date,"Y-m-d");
               $trans_row = [
                "transaction_no" =>  "SF".intval(0).date('yws').mt_rand(10000,99999),
                "client_id" => $member_id,
                "amount" => $amount,
                "transaction_channel_id" => 1,
                "payment_id" =>1,
                "subscription_date" => $transaction_date,
                "payment_date" => $transaction_date,
                "narrative" => "SOCIAL FUNDS-".$name,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1
            ];

        $transaction_data=$this->Data_import_model->add_subscription_fees($trans_row);
             if(!empty($transaction_data)){
            
             $single_row = [
                "journal_type_id" =>11,
                "ref_id" => $transaction_data['transaction_id'],
                "ref_no" => $transaction_data['transaction_no'],
                "description" =>"SOCIAL FUNDS-".$name,
                "transaction_date" => $transaction_date,
                "status_id" => 1,
                "date_created" =>time(),
                "created_by" => 1,
                "modified_by" =>1
            ];
            $insert_id=$this->Data_import_model->add_journal_tr($single_row);
           
                 
                $data[0] = [
                    'debit_amount' => NULL,
                    'credit_amount' =>$amount,
                    "transaction_date" => $transaction_date,
                    'reference_no'=>$transaction_data['transaction_no'],
                    'reference_id'=>  $transaction_data['transaction_id'],
                    'narrative' => "SOCIAL FUNDS-".$name ." made on " . $transaction_date,
                    'account_id' => 20,
                    'status_id' => 1
                ];
                $data[1] = [
                    'credit_amount' =>NULL,
                    'debit_amount' => $amount,
                    "transaction_date" => $transaction_date,
                    'reference_no'=>$transaction_data['transaction_no'],
                    'reference_id'=>  $transaction_data['transaction_id'],
                    'narrative' => "SOCIAL FUNDS-".$name." made on " . $transaction_date,
                    'account_id' => 40,
                    'status_id' => 1
                ];
              $this->Data_import_model->add_journal_tr_line($insert_id, $data);
                        $passed++;

                  }else{
                     $failed++;
                  }
                    
                    }
            $response ="Records Imported successfully";
            $feedback['message'] = "( ".$passed." ) ".$response." ( ".$failed." ) Failed , Check error log table";
            
            $feedback['success'] = true;
      
        
        echo json_encode($feedback);
    }
}
}