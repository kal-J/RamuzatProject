<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Savings_transaction
 *
 * @author Reagan
 */
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

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."mutaka".DIRECTORY_SEPARATOR;
        $file_name = "member_savings.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates($file_path);
        echo json_encode($feedback);
    }

    private function run_updates($file_path) {
        $handle = fopen($file_path, "r");
        $total_counts = $count = 0;
        $field_names = $batch_data = [];
        $feedback = ["success" => false, "message" => "File Could not be opened"];
        if ($handle) {
            while (($data = fgetcsv($handle, 10240, ",")) !== FALSE) {
                $data1 = $this->security->xss_clean($data);
                if ($count == 0) {//the row with the field_names
                    $field_names = $data1;
                    //echo $field_names[0];die();
                    if ($field_names[0] != "ID") {
                        $feedback['message'] = "Please ensure that the first cell (Account ID) contains the key Account No";
                        fclose($handle);
                        return $feedback;
                    }

                } else {
                    
                   $total_counts = $count;
                   if($data1[7]==2){
                   $this->insert_transaction_data($data1);
                   }
                   if($data1[7]==1){
                   $this->insert_transaction_data_W($data1);
                   }
                    if($data1[7]==4){
                   $this->insert_transaction_data_F($data1);
                   }
                   if($data1[7]==6){
                   $this->insert_transaction_data_L($data1);
                   }
                }
                            
                $count++;
            }

                //$batch_data = [];
            fclose($handle);

            if (is_numeric($total_counts)) {
                $feedback["success"] = true;
                $feedback["message"] = "Update done\n $total_counts records updated";
            }
        }
        return $feedback;
    }
    
    
    private function insert_transaction_data($transaction) {  
        $account=$this->Data_import_model->get_savings_accounts($transaction[0]);
       $transaction_date = $this->extract_date_time_dot($transaction[1],"Y-m-d");
            $trans_row = [
                "transaction_no" =>  date('yws').mt_rand(1000000,9999999),
                "account_no_id" => $account['id'],
                "debit" => NULL,
                "credit" => abs($transaction[4]),
                "transaction_type_id" =>2,
                "payment_id" =>1,
                "transaction_date" =>$transaction_date,
                "narrative" => "DEPOSIT [ ".$transaction[5]." ][ ".$transaction[0]." ]",
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1
            ];
            $transaction_data=$this->Data_import_model->add_transaction($trans_row);
             if(!empty($transaction_data)){
            
             $single_row = [
                "journal_type_id" =>7,
                "ref_id" => $transaction_data['transaction_id'],
                "ref_no" => $transaction_data['transaction_no'],
                "description" =>"DEPOSIT [ ".$transaction[5]." ][ ".$transaction[0]." ]",
                "transaction_date" =>  $transaction_date,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" =>1
            ];
            $insert_id=$this->Data_import_model->add_journal_tr($single_row);
             if(!empty($insert_id)){
                 
                $data[0] = [
                    'debit_amount' => NULL,
                    'transaction_date' =>  $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'credit_amount' =>abs($transaction[4]),
                    'narrative' => "DEPOSIT [ ".$transaction[5]." ][ ".$transaction[0]." ]",
                    'account_id' =>8 ,
                    'status_id' => 1
                ];
                $data[1] = [
                    'credit_amount' =>NULL,
                    'transaction_date' =>  $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'debit_amount' => abs($transaction[4]),
                    'narrative' => "DEPOSIT [ ".$transaction[5]." ][ ".$transaction[0]." ]",
                    'account_id' => 40,
                    'status_id' => 1
                ];
             return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
             }else{
                  echo "journal failed";die();
             }
        }else{
            echo "trasaction failed";die();
        }
            
        
    }

     private function insert_transaction_data_W($transaction) {  
       $account=$this->Data_import_model->get_savings_accounts($transaction[0]);
       $transaction_date = $this->extract_date_time_dot($transaction[1],"Y-m-d");
            $trans_row = [
                "transaction_no" =>  date('yws').mt_rand(1000000,9999999),
                "account_no_id" => $account['id'],
                "debit" => abs($transaction[4]),
                "credit" => NULL,
                "transaction_type_id" =>1,
                "payment_id" =>1,
                "transaction_date" =>$transaction_date,
                "narrative" => "SAVINGS WITHDRAW [ ".$transaction[5]." ][ ".$transaction[0]." ]",
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1
            ];
            $transaction_data=$this->Data_import_model->add_transaction($trans_row);
             if(!empty($transaction_data)){
            
             $single_row = [
                "journal_type_id" =>8,
                "ref_id" => $transaction_data['transaction_id'],
                "ref_no" => $transaction_data['transaction_no'],
                "description" =>"SAVINGS WITHDRAW [ ".$transaction[5]." ][ ".$transaction[0]." ]",
                "transaction_date" =>  $transaction_date,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" =>1
            ];
            $insert_id=$this->Data_import_model->add_journal_tr($single_row);
             if(!empty($insert_id)){
                 
                $data[0] = [
                    'debit_amount' => abs($transaction[4]),
                    'transaction_date' =>  $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'credit_amount' =>NULL,
                    'narrative' => "SAVINGS WITHDRAW [ ".$transaction[5]." ][ ".$transaction[0]." ]",
                    'account_id' =>8 ,
                    'status_id' => 1
                ];
                $data[1] = [
                    'credit_amount' =>abs($transaction[4]),
                    'transaction_date' =>  $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'debit_amount' => NULL,
                    'narrative' => "SAVINGS WITHDRAW [ ".$transaction[5]." ][ ".$transaction[0]." ]",
                    'account_id' => 40,
                    'status_id' => 1
                ];
             return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
             }else{
                  echo "journal failed";die();
             }
        }else{
            echo "trasaction failed";die();
        }
    }

     private function insert_transaction_data_F($transaction) {  
       $account=$this->Data_import_model->get_savings_accounts($transaction[0]);
       $transaction_date = $this->extract_date_time_dot($transaction[1],"Y-m-d");
            $trans_row = [
                "transaction_no" =>  date('yws').mt_rand(1000000,9999999),
                "account_no_id" => $account['id'],
                "debit" => abs($transaction[4]),
                "credit" => NULL,
                "transaction_type_id" =>4,
                "payment_id" =>5,
                "transaction_date" =>$transaction_date,
                "narrative" => "LOAN DEFAULT FINES [ ".$transaction[5]." ][ ".$transaction[0]." ]",
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1
            ];
            $transaction_data=$this->Data_import_model->add_transaction($trans_row);
             if(!empty($transaction_data)){
            
             $single_row = [
                "journal_type_id" =>28,
                "ref_id" => $transaction_data['transaction_id'],
                "ref_no" => $transaction_data['transaction_no'],
                "description" =>"LOAN DEFAULT FINES [ ".$transaction[5]." ][ ".$transaction[0]." ]",
                "transaction_date" =>  $transaction_date,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" =>1
            ];
            $insert_id=$this->Data_import_model->add_journal_tr($single_row);
             if(!empty($insert_id)){
                 
                $data[0] = [
                    'debit_amount' => abs($transaction[4]),
                    'transaction_date' =>  $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'credit_amount' =>NULL,
                    'narrative' => "LOAN DEFAULT FINES [ ".$transaction[5]." ][ ".$transaction[0]." ]",
                    'account_id' =>8 ,
                    'status_id' => 1
                ];
                $data[1] = [
                    'credit_amount' =>abs($transaction[4]),
                    'transaction_date' =>  $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'debit_amount' => NULL,
                    'narrative' => "LOAN DEFAULT FINES [ ".$transaction[5]." ][ ".$transaction[0]." ]",
                    'account_id' => 46,
                    'status_id' => 1
                ];
             return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
             }else{
                  echo "journal failed";die();
             }
        }else{
            echo "trasaction failed";die();
        }
    }

         private function insert_transaction_data_L($transaction) {  
       $account=$this->Data_import_model->get_savings_accounts($transaction[0]);
       $transaction_date = $this->extract_date_time_dot($transaction[1],"Y-m-d");
            $trans_row = [
                "transaction_no" =>  date('yws').mt_rand(1000000,9999999),
                "account_no_id" => $account['id'],
                "debit" => abs($transaction[4]),
                "credit" => NULL,
                "transaction_type_id" =>4,
                "payment_id" =>5,
                "transaction_date" =>$transaction_date,
                "narrative" => "LOAN REPAYMENT [ ".$transaction[5]." ][ ".$transaction[0]." ]",
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1
            ];
            $transaction_data=$this->Data_import_model->add_transaction($trans_row);
           
    }

public function extract_date_time_dot($date_time_string, $return_format = "U") {
        $date_format = "d.m.Y" . (strlen($date_time_string) > 10 ? " H:i:s" : "");
        $date_time_obj = DateTime::createFromFormat($date_format, $date_time_string);
        return $date_time_obj->format($return_format);
    }
}
