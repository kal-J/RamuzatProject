<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_accounts_import
 *
 * @author Eric
 */
class Data_accounts_import extends CI_Controller {

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
    }

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."kcca".DIRECTORY_SEPARATOR;
        $file_name = "savings_accounts.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates($file_path);
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
                    if ($field_names[0] != "CUST_ACC_ID") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key CUST_ACC_ID";
                        fclose($handle);
                        return $feedback;
                    }
                } else {
                    if ($data1[3] != "" && $data1[3] != NULL && substr($data1[3], 0, 2 )=="SH") {
                    $this->create_share_accounts($data1);
                    }else{
                        if ($data1[3] != "" && $data1[3] != NULL && substr( $data1[3], 0, 2 )=="SV") {
                           $deposit_Product_id=1;
                        }else{
                            $deposit_Product_id=2;
                        }
                    $this->create_savings_accounts($data1,$deposit_Product_id);    
                    }
                    $total_clients = $total_clients + 1;
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


    public function create_savings_accounts($data,$id) {
        $data = [
            "member_id" => $data[1],
            "status_id" =>7,
            "client_type" =>1,
            "deposit_Product_id" =>$id,
            "account_no" => $data[3],
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
        ];
        
        $this->load->model("Data_import_model");
        $this->Data_import_model->add_savings_account($data);
        
        //$this->create_share_accounts($member_id,$savings_account);
    }
    
    private function create_share_accounts($data) {
        $data = [
            "member_id" => $data[1],
            "status_id" =>1,
            "share_issuance_id"=>1,
            "date_opened"=>date('Y-m-d'),
            "default_savings_account_id"=>NULL,
            "share_account_no" => $data[3],
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
        ];
        
        $this->load->model("Data_import_model");
        $this->Data_import_model->set_share_state($data);
    }
    private function insert_address($user_id,$address){
        $data = [
            'user_id' => $user_id,
            'address1' => $address,
            'address_type_id' => 1,
            'village_id' => 1,
            'start_date' => date("Y-m-d"),
            'date_created' => time(),
            'created_by' => 1
        ];
        return $this->address_model->set2($data);
    }

}
