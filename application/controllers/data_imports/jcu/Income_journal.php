<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Reagan
 */
class Income_journal extends CI_Controller {

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

        $this->db->trans_start();
        $folder = "data_extract".DIRECTORY_SEPARATOR."jcu".DIRECTORY_SEPARATOR;
        $file_name = "income_journal.csv";
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
                    if ($field_names[0] != "CREDIT ACCOUNT ID") {
                        $feedback['message'] = "Please ensure that the first cell (ID) contains the key ACCOUNT ID";
                        fclose($handle);
                        return $feedback;
                    }

                } else {
                    
                   $total_counts = $count;
                   //$this->insert_transaction_data($data1);
                   $this->do_income_journal($data1);
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
    
    public function do_income_journal($transaction)
    {
        $transaction_date = $this->helpers->extract_date_time($transaction[7],"Y-m-d");
        //print_r($transaction_date); die;
        
        $reference_no = date('yws').mt_rand(1000000,9999999);
        $single_row = [
            "journal_type_id" =>14,
            "ref_id" => $transaction[8],
            "ref_no" => $reference_no,
            "description" =>$transaction[3],
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
                'transaction_date' =>$transaction_date,
                'reference_id' => $transaction[8],
                'reference_no' => $reference_no,
                'credit_amount' =>$transaction[5],
                'narrative' => $transaction[3],
                'account_id' =>$transaction[0],
                'status_id' => 1
            ];
            $data[1] = [
                'credit_amount' =>NULL,
                'transaction_date' =>$transaction_date,
                'reference_id' => $transaction[8],
                'reference_no' => $reference_no,
                'debit_amount' => $transaction[5],
                'narrative' => $transaction[3],
                'account_id' => $transaction[6],
                'status_id' => 1
            ];

         return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
         }else{
              echo "journal failed";die();
         }

    }
    
    private function insert_transaction_data($transaction) {  
        $transaction_date = $this->helpers->extract_date_time($transaction[7],"Y-m-d");
         //echo $transaction_date; die();

            $trans_row = [
                "transaction_no" =>  date('yws').mt_rand(1000000,9999999),
                "share_account_id" => $transaction[0],
                "share_issuance_id"=>1,
                "debit" => NULL,
                "credit" => $transaction[4],
                "transaction_type_id" => 9,
                "payment_id" =>2,
                "transaction_date" => $transaction_date,
                "narrative" => $transaction[2],
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1
            ];
            $transaction_data=$this->Data_import_model->add_transaction_shares($trans_row);
            /*  if(!empty($transaction_data)){
            
             $single_row = [
                "journal_type_id" =>22,
                "ref_id" => $transaction_data['transaction_id'],
                "ref_no" => $transaction_data['transaction_no'],
                "description" =>$transaction[2],
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
                    'credit_amount' =>$transaction[4],
                    'narrative' => $transaction[2],
                    'account_id' =>32,
                    'status_id' => 1
                ];
                $data[1] = [
                    'credit_amount' =>NULL,
                    'transaction_date' =>$transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'debit_amount' => $transaction[4],
                    'narrative' => $transaction[2],
                    'account_id' => $transaction[6],
                    'status_id' => 1
                ];

             return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
             }else{
                  echo "journal failed";die();
             }
        }else{
            echo "transaction failed";die();
        } */
            
        
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
