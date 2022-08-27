<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Journals
 *
 * @author Reagan
 */
class Journals extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('/');
        }
        $this->load->model("Data_import_model");
        $this->load->library("helpers");
        
         ini_set('memory_limit', '200M');
            ini_set('upload_max_filesize', '200M');
            ini_set('post_max_size', '200M');
            ini_set('max_input_time', 3600);
            ini_set('max_execution_time', 3600);
    }

    public function index1() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."plc".DIRECTORY_SEPARATOR;
        $file_name = "JOURNALS1.csv";
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
                    if ($field_names[0] != "date") {
                        $feedback['message'] = "Please ensure that the first cell contains the key Member ID";
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
        $transaction_date = $this->helpers->extract_date_time($transaction[9],"Y-m-d");
            if($transaction[10]=="DEBIT"){
                $debit_amount1=$transaction[4];
                $credit_amount1=NULL;
                $debit_amount2=NULL;
                $credit_amount2=$transaction[4];

                $account_ONE=$transaction[7];
                $account_TWO=$transaction[8];

            }else{
                $debit_amount1=NULL;
                $credit_amount1=$transaction[5];
                $debit_amount2=$transaction[5];
                $credit_amount2=NULL;
                
                $account_ONE=$transaction[8];
                $account_TWO=$transaction[7];
            }
             $single_row = [
                "journal_type_id" =>$transaction[6],
                "ref_id" => NULL,
                "ref_no" => $transaction[3],
                "description" =>$transaction[1]." [ ".$transaction[2]." ] " ,
                "transaction_date" => $transaction_date,
                "status_id" => 1,
                "date_created" =>time(),
                "created_by" => 1,
                "modified_by" =>1
            ];
            $insert_id=$this->Data_import_model->add_journal_tr($single_row);
             if(!empty($insert_id)){
                 
                $data[0] = [
                    'debit_amount' => $debit_amount1,
                    'credit_amount' =>$credit_amount1,
                    'reference_id' => NULL,
                    'reference_no' => $transaction[3],
                    'transaction_date' => $transaction_date,
                    'narrative' => $transaction[1]." [ ".$transaction[2]." ] made on " . $transaction_date,
                    'account_id' => $account_ONE,
                    'status_id' => 1
                ];
                $data[1] = [
                    'credit_amount' =>$credit_amount2,
                    'debit_amount' => $debit_amount2,
                    'reference_id' => NULL,
                    'reference_no' => $transaction[3],
                    'transaction_date' => $transaction_date,
                    'narrative' => $transaction[1]." [ ".$transaction[2]." ] made on " . $transaction_date,
                    'account_id' => $account_TWO,
                    'status_id' => 1
                ];
             return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
             }else{
                  echo "journal failed";die();
             }    
        
    }


    public function do_insert_accounts($account_data) {
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
}


  public function reports() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."plc".DIRECTORY_SEPARATOR;
        $file_name = "REPORTS.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates1($file_path);
        echo json_encode($feedback);
    }

    private function run_updates1($file_path) {
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
                    if ($field_names[0] != "id") {
                        $feedback['message'] = "Please ensure that the first cell contains the key ID";
                        fclose($handle);
                        return $feedback;
                    }

                } else {
                    
                   $total_counts = $count;
                   $this->insert_opening_transactions($data1);
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
    
    public function insert_opening_transactions($data1){
         $this->load->model('transactionChannel_model');
         $this->load->model('journal_transaction_line_model');

         $transaction_date = $this->helpers->extract_date_time($data1[7],"Y-m-d");
         
         if($data1[8]==1){
             $debit_or_credit1="debit_amount";
             $deposit_amount=$data1[4];
         }else{
             $debit_or_credit1="credit_amount";
             $deposit_amount=$data1[5];
         }
        
         $data3 = [
                'transaction_date' => $transaction_date,
                'description' => $data1[3]." [ ".$data1[2]." ]",
                'ref_no' => "#OP",
                'ref_id' => NULL,
                'status_id' => 1,
                'journal_type_id' => 18
            ];
            $journal_transaction_id = $this->journal_transaction_model->set($data3);
            if ($deposit_amount != null && !empty($deposit_amount) && $deposit_amount != '0') {
                $data[0] = [
                    $debit_or_credit1 => $deposit_amount,
                    'reference_no' => "#OP",
                    'reference_id' => NULL,
                    'transaction_date' => $transaction_date,
                    'narrative' => $data1[3]." [ ".$data1[2]." ]",
                    'account_id' => $data1[0],
                    'status_id' => 1
                ];
                $this->journal_transaction_line_model->set($journal_transaction_id, $data);
            }
        
    }
    
    
}
