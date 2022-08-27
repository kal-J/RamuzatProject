<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Import_opening
 *
 * @author Reagan
 */
class Import_opening extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model("Data_import_model");
        $this->load->library("helpers");
        
         ini_set('memory_limit', '200M');
            ini_set('upload_max_filesize', '200M');
            ini_set('post_max_size', '200M');
            ini_set('max_input_time', 3600);
            ini_set('max_execution_time', 3600);
    }

    public function auto_insert() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."members".DIRECTORY_SEPARATOR;
        $file_name = "user_passwords.csv";
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
                    if ($field_names[1] != "CUSTOMER_ID") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key CUSTOMER_ID";
                        fclose($handle);
                        return $feedback;
                    }

                } else {
                   //$total_counts = $total_counts + $this->do_insert_opening($data1);
                   $total_counts = $total_counts + $this->update_passwords($data1);
                    
                }        
                $count++;

            }
           
            fclose($handle);

            if (is_numeric($total_counts)) {
                $feedback["success"] = true;
                $feedback["message"] = "Update done\n $total_counts records updated";
            }
        }
        return $feedback;
    }

    public function update_passwords($passwords){
        $data = [
            "password2" => $passwords[2]
        ];
        if($this->Data_import_model->update_user_password($passwords[1],$data)){
            return 1;
        } else {
            return 0;
        }
    }


    public function do_insert_opening($account_data) {
        if ($account_data[2] == "DEBIT" || $account_data[2] == "CREDIT") {
        
        $data1 = [
            "description" => $account_data[1]." ".$account_data[3],
            "journal_type_id" => $account_data[4],
            "transaction_date" => "2019-01-01",
            "date_created" => time(),
            "status_id" => 1,
            "created_by" => $account_data[7]
        ];
        $id =$this->Data_import_model->add_journal_tr($data1);
        if(is_numeric($id)){
            if($account_data[2]=="DEBIT"){
                $debit_amount=$account_data[5];
                $credit_amount=NULL;
            }else if($account_data[2]=="CREDIT"){
                $debit_amount=NULL;
                $credit_amount=$account_data[6];
            }
        $data2 = [
            "narrative" => $account_data[1]." ".$account_data[3],
            "journal_transaction_id" => $id,
            "account_id" => $account_data[0],
            "debit_amount" =>$debit_amount,
            "credit_amount" =>$credit_amount,
            "date_created" => time(),
            "status_id" => 1,
            "created_by" => $account_data[7]
        ];
         $this->Data_import_model->add_journal_tr_line($data2);
        }
    }
}

}
