<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Reagan
 */
class Savings_transaction extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
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
        $this->db->trans_start();
        $folder = "data_extract".DIRECTORY_SEPARATOR."mceesacco".DIRECTORY_SEPARATOR;
        $file_name = "mcee_savings.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates($file_path);
        $this->db->trans_complete();

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
                   $this->insert_transaction_data($data1);
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
        $transaction_date = "2021-12-31";
      // echo $transaction_date; die();
        $credit = floatval($transaction[2]);
       
            $trans_row = [
                "transaction_no" =>  date('yws').mt_rand(1000000,9999999),
                "member_id" => $transaction[0],
                "account_no_id" => $transaction[0],
                "debit" => NULL,
                "credit" => $credit,
                "transaction_type_id" => 2,
                "payment_id" =>2,
                "transaction_date" => $transaction_date,
                "narrative" =>"Imported savings balance ". "transaction for [ ".$transaction[1]." ]",
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1,
                'deposit_product_id' => 1,
            ];
            //echo json_encode(($trans_row)); die;
             unset($trans_row['member_id'],$trans_row['deposit_product_id']);
            $transaction_data=$this->Data_import_model->add_transaction($trans_row);
            //echo json_encode(($transaction_data)); die;
 
             $single_row = [
                "journal_type_id" =>7,
                "ref_id" => $transaction_data['transaction_id'],
                "ref_no" => $transaction_data['transaction_no'],
                "description" => "Imported savings balance ". "transaction for [ ".$transaction[1]." ]",
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
                    'credit_amount' =>$credit,
                    'narrative' => "Imported savings balance "." transaction for [ ".$transaction[1]." ] made on " . $transaction_date,
                    'account_id' =>8 ,
                    'status_id' => 1
                ];
                $data[1] = [
                    'credit_amount' =>NULL,
                    'transaction_date' =>  $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'debit_amount' => $credit,
                    'narrative' => "Imported savings balance "." transaction for [ ".$transaction[1]." ] made on " . $transaction_date,
                    'account_id' => 27,
                    'status_id' => 1
                ];
             return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
             }else{
                  echo "journal failed";die();
             }
       
            

    }

    /*public function do_insert_accounts($account_data) {
            if ($account_data[2] != "" && $account_data[2] != NULL && substr( $account_data[2], 0, 2 )==5) {
            $state =5;
            if($account_data[4]=="Y"){
                $state =7;
            }else{
            $state =17;
            }
            //echo $this->helpers->extract_date_time($account_data[6]); die();
            $data = [
                "member_id" => $account_data[1],
                "account_no" => $account_data[3],
                "deposit_product_id" => 2,
                "client_type" => 1,
                "status_id" => 1,
                "date_created" =>$this->helpers->extract_date_time($account_data[6]),
                "created_by" => $account_data[5]
            ];
            $this->Data_import_model->add_savings_account($data,$state,$this->helpers->extract_date_time($account_data[6]));
        }
    }*/

}
