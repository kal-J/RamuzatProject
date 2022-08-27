<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_import
 *
 * @author REAGAN
 */
class Group_import extends CI_Controller {

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
        $this->load->model('group_model');
        
       
    }
    /* 
    @ Ambrose Ogwang 1 May 2022
    Group was initially created using the member data csv filtered by column[3]='G' 
     Later added missing group using this file (rwenzori_group_data)
     */

    public function index() {
        $this->db->trans_start();
        $folder = "data_extract".DIRECTORY_SEPARATOR."rwenzorisacco".DIRECTORY_SEPARATOR;
        $file_name = "rwenzori_group_data.csv";
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
                    if ($field_names[0] != "GROUP ID") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key Account NO";
                        fclose($handle);
                        return $feedback;
                    }
                } else {
                   
                    $total_clients = $total_clients + $this->insert_user_data($data1);
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

    private function insert_user_data($customer_data) {
        $date_registered = '2020-12-31';
      
        if ($customer_data[0] != "" && $customer_data[0]) {
            $single_row = [
                "group_name" => $customer_data[1],
                "group_no" => $customer_data[0],
                "group_client_type" =>2,
                "date_registered" => $date_registered,
                "description" => 'RFGC SACCO GROUP',
                "branch_id" => 1,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1
            ];
            $group_id = $this->group_model->add_through_import($single_row);
 
            return 1;
        }
        return 0;
    }
    public function create_savings_accounts($member_id,$customer_data) {
      
        $data = [
            
            "member_id" => $member_id,
            "status_id" =>7,
            "client_type" =>1,
            "deposit_Product_id" =>1,
            "account_no" =>strtoupper($customer_data[0]),
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1
        ];
        
       $this->load->model("Data_import_model");
       $savings_account=$this->Data_import_model->add_savings_account($data);
       $this->create_share_accounts($member_id,$customer_data);
      // $this->insert_saving_transaction_data($member_id,$customer_data,$savings_account);
       
      }
   
    private function create_share_accounts($member_id,$customer_data) {
        // creating a only selected member a/cs as they few (5)
        
        $data = [
            "member_id" => $member_id,
            "status_id" =>1,
            "share_issuance_id"=>1,
            "date_opened"=>date('Y-m-d'),
            //"default_savings_account_id"=>$savings_account,
            "share_account_no" => strtoupper($customer_data[0]),
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1
         
             
            
        ];
   
    
        
        $this->load->model("Data_import_model");
        $share_accounts= $this->Data_import_model->set_share_state($data);
   
       //$this->insert_share_transaction_data($member_id,$share_accounts,$customer_data);
    
    }

   
    }

