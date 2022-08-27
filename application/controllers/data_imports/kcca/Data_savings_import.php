<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Reagan
 */
class Data_savings_import extends CI_Controller {

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

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."kcca".DIRECTORY_SEPARATOR;
        $file_name = "savings_transactions.csv";
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
        $total_counts = $count = 0;
        $field_names = $batch_data = [];
        $feedback = ["success" => false, "message" => "File Could not be opened"];
        if ($handle) {
            while (($data = fgetcsv($handle, 10240, ",")) !== FALSE) {
                $data1 = $this->security->xss_clean($data);
                if ($count == 0) {//the row with the field_names
                    $field_names = $data1;
                   
                    if ($field_names[0] != "TRANS_ID") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key TRANS_ID";
                        fclose($handle);
                        return $feedback;
                    }

                } else {
                    $dataadded=$this->insert_share_transaction_data($data1);
                    if (!empty($dataadded)) {
                   $batch_data[] = $dataadded;
                    }
                   //$total_counts = $total_counts + $this->do_insert_accounts($data1);
                    
                }
                
                $count++;
               
                if( $count%5000 === 0){
                    //insert a batch of a thousand records
                    $total_counts = $total_counts + $this->Data_import_model->add_share_transaction_batch($batch_data);
                   // $total_counts = $total_counts + $this->Data_import_model->add_transaction_batch($batch_data);
                    $batch_data = [];
                }

            }
            //then the remainder of the data
            if(count($batch_data)){
                    $total_counts = $total_counts + $this->Data_import_model->add_share_transaction_batch($batch_data);
                   // $total_counts = $total_counts + $this->Data_import_model->add_transaction_batch($batch_data);
                    $batch_data = [];
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

    public function insert_transaction_data($transaction) {  
        
        if ($transaction[2] != "" && $transaction[2] != NULL && (substr( $transaction[2], 0, 2 )=="SV" || substr( $transaction[2], 0, 2 )=="FD")) {
            
        $account_id =$this->Data_import_model->get_savings_accounts($transaction[2]);
        $date_created = $this->helpers->extract_date_time2($transaction[13]);
        $transaction_date = $this->helpers->extract_date_time2($transaction[11],"Y-m-d");
        
        if($transaction[3]=="CREDIT"){
        $debit_amount =NULL;
        $credit_amount =$transaction[9];
        } else {
        $debit_amount =$transaction[8];
        $credit_amount =NULL;
        }
        if($transaction[3]=="DEBIT"){
        $transaction_type_id =1;
        $payment_id =2;
        } else if($transaction[3]=="CREDIT"){
        $transaction_type_id =2;
        $payment_id =2;
        } else {
        $transaction_type_id =4;
        $payment_id =5;
        }

        if(is_numeric($account_id['id'])){
            $account_no_id  =$account_id['id'];
        } else{
            $account_no_id =0;
        }
            $trans_row = [
                "transaction_no" =>  date('yws').mt_rand(100000,9999999),
                "account_no_id" => $account_no_id,
                "debit" => $debit_amount,
                "credit" => $credit_amount,
                "transaction_type_id" => $transaction_type_id,
                "payment_id" =>$payment_id,
                "transaction_date" => $transaction_date,
                "narrative" => $transaction[5],
                "status_id" => 1,
                "date_created" => $date_created,
                "created_by" => $transaction[12]
            ];
            return $trans_row;
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



    public function insert_share_transaction_data($transaction) {  
        
        if ($transaction[2] != "" && $transaction[2] != NULL && (substr( $transaction[2], 0, 2 )=="SH")) {
            
        $account_id =$this->Data_import_model->get_shares_accounts($transaction[2]);
        $date_created = $this->helpers->extract_date_time2($transaction[13]);
        $transaction_date = $this->helpers->extract_date_time2($transaction[11],"Y-m-d");
        
        if($transaction[3]=="CREDIT"){
        $debit_amount =NULL;
        $credit_amount =$transaction[9];
        } else {
        $debit_amount =$transaction[8];
        $credit_amount =NULL;
        }
        if($transaction[3]=="DEBIT"){
        $transaction_type_id =1;
        $payment_id =2;
        } else if($transaction[3]=="CREDIT"){
        $transaction_type_id =9;
        $payment_id =2;
        } else {
        $transaction_type_id =4;
        $payment_id =5;
        }

        if(is_numeric($account_id['id'])){
            $account_no_id  =$account_id['id'];
        } else{
            $account_no_id =0;
        }
            $trans_row = [
                "transaction_no" =>  date('yws').mt_rand(100000,9999999),
                "share_account_id" => $account_no_id,
                "debit" => $debit_amount,
                "credit" => $credit_amount,
                "transaction_type_id" => $transaction_type_id,
                "payment_id" =>$payment_id,
                "transaction_date" => $transaction_date,
                "narrative" => $transaction[5],
                "status_id" => 1,
                "date_created" => $date_created,
                "created_by" => $transaction[12]
            ];
            return $trans_row;
        }
        
    }


}
