<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_import
 *
 * @author REAGAN
 */
class Data_import_shares extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('/');
        }
        $this->load->model("contact_model");
        $this->load->model("user_model");
        $this->load->model("member_model");
        $this->load->model("nextOfKin_model");
        $this->load->model("address_model");
        $this->load->model("Data_import_model");
        
        $this->account_no="";
        $this->account_no1="SV000000";
        $this->account_no2="GRP000000";
        $this->account_no3="IST000000";
        $this->account_no4="JNT000000";
        $this->share_account_no="SH00000";
        $this->member_no="NKD000000";
    }

    public function index() {
        $this->db->trans_start();
        $folder = "data_extract".DIRECTORY_SEPARATOR."amaziba".DIRECTORY_SEPARATOR;
        $file_name = "SHARES.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates($file_path);
        $this->db->trans_complete();
        
        echo json_encode($feedback);
    }

    private function run_updates($file_path) {
        $handle = fopen($file_path, "r");
        $total_clients = $count = 0;
        $field_names = $data_array = [];
        $feedback = ["success" => false, "message" => "File Could not be opened"];
        if ($handle) {
            ini_set('memory_limit', '200M');
            ini_set('upload_max_filesize', '200M');
            ini_set('post_max_size', '200M');
            ini_set('max_input_time', 3600);
            ini_set('max_execution_time', 3600);
            while (($data = fgetcsv($handle, 30048576, ",")) !== FALSE) {
                $data1 = $this->security->xss_clean($data);
                if ($count == 0) {//the row with the field_names
                    $field_names = $data1;
                    if ($field_names[0] != "ID") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key Account NO";
                        fclose($handle);
                        return $feedback;
                    }
                } else {
                   
                    $total_clients = $total_clients + $this->insert_transaction_data_shares($data1);
                }
                $count++;
            }
            fclose($handle);

            if (is_numeric($total_clients)) {
                $feedback["success"] = true;
                $feedback["message"] = "Update done\n $total_clients records updated";
            }
        }
        return $feedback;
    }

    private function insert_transaction_data_shares($transaction) {  
        $transaction_date = '2021-12-31';
         //echo $transaction_date; die();

            $trans_row = [
                "transaction_no" =>  date('yws').mt_rand(1000000,9999999),
                "share_account_id" => $transaction[0],
                "share_issuance_id"=>1,
                "debit" => NULL,
                "credit" => $transaction[3],
                "transaction_type_id" => 9,
                "payment_id" =>2,
                "transaction_date" => $transaction_date,
                "narrative" => "SHARES [ " . $transaction[1]." ][ ". $transaction_date." ]",
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1
            ];
            $transaction_data=$this->Data_import_model->add_transaction_shares($trans_row);
             if(!empty($transaction_data)){
            
             $single_row = [
                "journal_type_id" =>22,
                "ref_id" => $transaction_data['transaction_id'],
                "ref_no" =>$transaction_data['transaction_no'],
                "description" =>"SHARES [ " . $transaction[1]." ][ ". $transaction_date." ]",
                "transaction_date" =>  $transaction_date,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" =>1
            ];
            $insert_id=$this->Data_import_model->add_journal_tr($single_row);
            //print_r($insert_id); die;

             if(!empty($insert_id)){
                 
                $data[0] = [
                    'debit_amount' => NULL,
                    'transaction_date' =>$transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'credit_amount' =>$transaction[3],
                    'narrative' => "SHARES [ " . $transaction[1]." ][ ". $transaction_date." ]",
                    'account_id' =>32,
                    'status_id' => 1
                ];
                $data[1] = [
                    'credit_amount' =>NULL,
                    'transaction_date' =>$transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'debit_amount' => $transaction[3],
                    'narrative' => "SHARES [ " . $transaction[1]." ][ ". $transaction_date." ]",
                    'account_id' => 40,
                    'status_id' => 1
                ];

             return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
             }else{
                  echo "journal failed";die();
             }
        }else{
            echo "transaction failed";die();
        }
            
        
    }


}
