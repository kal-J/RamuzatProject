<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Savings_transaction
 *
 * @author Reagan
 */
class Dividends_payment extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        
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
        $folder = "data_extract".DIRECTORY_SEPARATOR."mkcc".DIRECTORY_SEPARATOR;
        $file_name = "dividends_2022.csv";
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
                    if ($field_names[0] != "Share A/C NO") {
                        $feedback['message'] = "Please ensure that the first cell (Share A/C NO) contains the key Account No";
                        fclose($handle);
                        return $feedback;
                    }

                } else {
                    
                   $total_counts = $count;
                   if($data[3]>0){
                   $this->insert_transaction_data($data1);
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
        // Get savings account ID
        $share_account_no = $transaction[0];
        $saving_account=$this->Data_import_model->get_savings_account_id_using_share_account_no($share_account_no);
       $transaction_date = "2023-01-01";
       $narration = "Received as Dividends from shares owned in FY 2022 - DEPOSIT for " . "[ " . $transaction[1] . " ]";

            $trans_row = [
                "transaction_no" =>  date('yws').mt_rand(1000000,9999999),
                "account_no_id" => $saving_account['id'],
                "debit" => NULL,
                "credit" => $transaction[3],
                "transaction_type_id" =>2,
                "payment_id" =>2,
                "transaction_date" =>$transaction_date,
                "narrative" => $narration,
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
                "description" =>$narration,
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
                    'credit_amount' =>$transaction[3],
                    'narrative' => $narration,
                    'account_id' =>8 ,
                    'status_id' => 1
                ];
                $data[1] = [
                    'credit_amount' =>NULL,
                    'transaction_date' =>  $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'debit_amount' => $transaction[3],
                    'narrative' => $narration,
                    'account_id' => 31,
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



}
