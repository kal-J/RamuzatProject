<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Journal_trans
 * 2935
 * 2871
 * @author allan_jes modified by reagan
 */
class Journal_trans extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('/');
            /*
              //ini_set("memory_limit","2048M"); */
            ini_set('memory_limit', '200M');
            ini_set('upload_max_filesize', '200M');
            ini_set('post_max_size', '200M');
            ini_set('max_input_time', 3600);
            ini_set('max_execution_time', 3600);
        }

    
    }

    public function auto_insert() {
        $folder = "data_extract" . DIRECTORY_SEPARATOR . "kcca" . DIRECTORY_SEPARATOR;
        $file_name = "journal_transaction.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates($file_path);
        echo json_encode($feedback);
    }

    private function run_updates($file_path) {
         ini_set('memory_limit', '200M');
            ini_set('upload_max_filesize', '200M');
            ini_set('post_max_size', '200M');
            ini_set('max_input_time', 3600);
            ini_set('max_execution_time', 3600);
        $handle = fopen($file_path, "r");
        $total_records = $count = 0;
        $field_names = $multi_data_array = [];
        $feedback = ["success" => false, "message" => "File Could not be opened"];
        if ($handle) {
            $this->load->model("journal_transaction_model");
            while (($data = fgetcsv($handle, 30048576, ",")) !== FALSE) {
                $data1 = $this->security->xss_clean($data);
                if ($count == 0) {//the row with the field_names
                    $field_names = $data1;
                    if ($field_names[0] != "TRANS_ID") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key TRANS_ID";
                        fclose($handle);
                        return $feedback;
                    }
                } else {
                    $multi_data_array[] = $this->get_journal_data($data1);
                }
                $count++;
                if( $count%1000 === 0){
                    //insert a batch of a thousand records
                    $total_records = $total_records + $this->journal_transaction_model->set_auto($multi_data_array);
                    $multi_data_array = [];
                    
                }
            }
            //then the remainder of the data
            if(count($multi_data_array)){
                $total_records = $total_records + $this->journal_transaction_model->set_auto($multi_data_array);
                    $multi_data_array = [];
            }
            fclose($handle);
            if (is_numeric($total_records)) {
                $feedback["success"] = true;
                $feedback["message"] = "Update done\n $total_records records updated";
            }
        }
        return $feedback;
    }
    private function get_journal_data($journal_transaction_data) {
        
        // $journal_types = [
        //     "MULT" => 101, "LOAN" => 102,"CUST SHARE CHARGE"=>103,"DIRECT LEDGER POSTING"=>104,"REVERSAL"=>105,"TRANSFER"=>106,
        //     "BATCHED POSTING"=>107,"CUST ACCOUNT CREDIT"=>108,"CUST ACCOUNT DEBIT"=>109,"CUST ACCOUNT TRANSFER"=>110,
        //     "CUST PREMIUM SHARE CHARGE"=>111,"CUST SHARE CHARGE"=>112,"CUST SUBSCRIPTION CHARGE"=>113,
        //     "CUST TRANS CHARGE"=>114,"INTER-ACC"=>115,"LOAN INTEREST REPAY"=>116,"LOAN PRINCIPLE REPAY"=>117
        //     ];
            
        $journal_types = [
            "MULT" => 1, "LOAN" => 4,"CUST SHARE CHARGE"=>30,"DIRECT LEDGER POSTING"=>1,"REVERSAL"=>31,"TRANSFER"=>1,
            "BATCHED POSTING"=>1,"CUST ACCOUNT CREDIT"=>7,"CUST ACCOUNT DEBIT"=>8,"CUST ACCOUNT TRANSFER"=>7,
            "CUST PREMIUM SHARE CHARGE"=>30,"CUST SHARE CHARGE"=>30,"CUST SUBSCRIPTION CHARGE"=>11,
            "CUST TRANS CHARGE"=>20,"INTER-ACC"=>1,"LOAN INTEREST REPAY"=>6,"LOAN PRINCIPLE REPAY"=>6
            ];
            
        //INSERT INTO fms_journal_type VALUES(101,"MULTI"),(102,"LOAN"),(103,"CUST SHARE CHARGE"),(105,"REVERSAL"),(104,"DIRECT LEDGER POSTING"),(106,"TRANSFER");
        //INSERT INTO fms_journal_type VALUES(107,"BATCHED POSTING"),(108,"CUST ACCOUNT CREDIT"),(109,"CUST ACCOUNT DEBIT"),(110,"CUST ACCOUNT TRANSFER"),(111,"CUST PREMIUM SHARE CHARGE"),(113,"CUST SUBSCRIPTION CHARGE"),(112,"CUST SHARE CHARGE"),(114,"CUST TRANS CHARGE"),(115,"INTER-ACC"),(116,"LOAN INTEREST REPAY"),(117,"LOAN PRINCIPLE REPAY");
        $trans_date = $this->helpers->extract_date_time($journal_transaction_data[5],"Y-m-d");
        $date_created = $this->helpers->extract_date_time($journal_transaction_data[9]);
        if ($journal_transaction_data[0] != "" && $journal_transaction_data[0] != NULL) {
            $single_row = [
                "id" => trim($journal_transaction_data[0]),
                "journal_type_id" => $journal_types[isset($journal_transaction_data[7]) ? $journal_transaction_data[7] : 1],
                "ref_id" => $journal_transaction_data[6],
                //"ref_no" => $journal_transaction_data[2].",".$journal_transaction_data[3],
                "ref_no" => "",
                "description" => $journal_transaction_data[1],
                "transaction_date" => $trans_date,
                "status_id" => 1,
                "date_created" => $date_created,
                "created_by" => $journal_transaction_data[8],
                "modified_by" => $journal_transaction_data[8]
            ];
            //insert into the user table
            //$user_id = $this->journal_transaction_model->set($single_row);
            return $single_row;
        }
        return 0;
    }
    
    public function auto_lines(){
        $folder = "data_extract" . DIRECTORY_SEPARATOR . "kcca" . DIRECTORY_SEPARATOR;
        $file_name = "journal_transaction_line.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_lines_update($file_path);
        echo json_encode($feedback);
    }
    
    private function run_lines_update($file_path) {
         ini_set('memory_limit', '200M');
            ini_set('upload_max_filesize', '200M');
            ini_set('post_max_size', '200M');
            ini_set('max_input_time', 3600);
            ini_set('max_execution_time', 3600);


         $this->load->model('Data_import_model');
        $data_array=$this->Data_import_model->get_journal_ids();
        $journal_ids=array_column($data_array, 'id');


        $handle = fopen($file_path, "r");
        $total_records = $count = 0;
        $field_names = $multi_data_array = [];
        $feedback = ["success" => false, "message" => "File Could not be opened"];
        if ($handle) {
            $this->load->model("journal_transaction_line_model");
            while (($data = fgetcsv($handle, 34048576, ",")) !== FALSE) {
                //$data1 = $this->security->xss_clean($data);
                $data1 = $data;
                if ($count == 0) {//the row with the field_names
                    $field_names = $data1;
                    if ($field_names[0] != "TRANS_ID") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key TRANS_ID";
                        fclose($handle);
                        return $feedback;
                    }
                } else {
                    if(in_array($data1[1], $journal_ids)){
                    $multi_data_array[] = $this->get_line_data($data1);
                   }
                }
                $count++;
                if( $count%10000 === 0){
                    //insert a batch of a thousand records
                    $total_records = $total_records + $this->journal_transaction_line_model->set_auto($multi_data_array);
                    $multi_data_array = [];
                    
                }
            }
            //then the remainder of the data
            if(count($multi_data_array)){
                $total_records = $total_records + $this->journal_transaction_line_model->set_auto($multi_data_array);
            }
            fclose($handle);
            if (is_numeric($total_records)) {
                $feedback["success"] = true;
                $feedback["message"] = "Update done\n $total_records records updated";
            }
        }
        return $feedback;
    }
    
    private function get_line_data($journal_transaction_line_data) {
        //print($journal_transaction_line_data[7]);die();
        $date_created = $this->helpers->extract_date_time($journal_transaction_line_data[9]);
        $trans_date = $this->helpers->extract_date_time($journal_transaction_line_data[7],"Y-m-d");
        //print($date_created);die();
        //die();
        $statuses = ["Y" => 1, "N" => 2,"NULL"];
        if ($journal_transaction_line_data[0] != "" && $journal_transaction_line_data[0] != NULL) {
            if($journal_transaction_line_data[2]==4102){
                $account_id =1105;
            }else{
                $account_id =$journal_transaction_line_data[2];
            }
            $single_row = [
                "id" => $journal_transaction_line_data[0],
                "journal_transaction_id" => trim($journal_transaction_line_data[1]),
                "account_id" => $account_id,
                "debit_amount" => $journal_transaction_line_data[5],
                "credit_amount" => $journal_transaction_line_data[6],
                "transaction_date" => $trans_date,
                "narrative" => $journal_transaction_line_data[4]. (!in_array($journal_transaction_line_data[11],['','NULL'])?(" - ".$journal_transaction_line_data[11]):''),
                "status_id" => $statuses[$journal_transaction_line_data[10]],
                "date_created" => $date_created,
                "created_by" => $journal_transaction_line_data[8],
                "modified_by" => $journal_transaction_line_data[8]
            ];
            //insert into the user table
            return $single_row;
        }
        return 0;
    }

}
