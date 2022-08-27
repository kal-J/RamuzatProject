<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_import
 *
 * @author Reagan
 */
class Member_import extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('/');
        }
         $this->load->library("helpers");
        $this->load->model("contact_model");
        $this->load->model("user_model");
        $this->load->model("member_model");
        $this->load->model("nextOfKin_model");
        $this->load->model("address_model");

        $this->account_no="SV00000";
        $this->share_account_no="SH00000";
        $this->member_no="M00000";
    }

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."bana".DIRECTORY_SEPARATOR;
        $file_name = "members.csv";
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
                    if ($field_names[0] != "Client ID") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key Client ID";
                        fclose($handle);
                        return $feedback;
                    }
                } else {
                    $this->account_no = ++$this->account_no;
                    $this->share_account_no = ++$this->share_account_no;
                    $this->member_no = ++$this->member_no;
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
        $date_registered = "2021-01-01";
        //$gender = ["M" => 1, "F" => 0];
        //$marital_status = ["Na" => 1, "Single" => 2,"Married" => 3,"Divorced" =>4, "Widow"=>5];

        if ($customer_data[0] != "" && $customer_data[0] != NULL) {
            $names = explode(",", trim($customer_data[1]));
            $gender = $customer_data[6]=="FEMALE"? 0 : 1;

             if($customer_data[7]=="MARRIED"){
                $marital_status = 3;
             }
               else if($customer_data[7]=="SINGLE"){
                $marital_status = 2;
             }  
             else {
                $marital_status = 1;
             }
              //$date_of_birth = $this->helpers->extract_date_time($customer_data[13],"Y-m-d");
             if($customer_data[13]){
                 $date_of_birth = $customer_data[13];
                $date_of_birth  = $this->helpers->extract_date_time($date_of_birth,"Y-m-d");
             }else{
                 $date_of_birth = "0000-00-00";
             }
             
             

            $single_row = [
                "firstname" => $names[0],
                "lastname" => isset($names[1])?$names[1]:'',
                "othernames" => isset($names[2])?$names[2]:'',
                "gender" =>  $gender,
                "marital_status_id" => $marital_status,
                "children_no" => 0,
                "dependants_no" => 0,
                "status" => 1,
                "salutation" => $customer_data[2],
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1,
                "nid_card_no" => $customer_data[19],
                "spouse_contact" => $customer_data[12],
                "date_of_birth" => $date_of_birth
                //"branch_id"=> $_SESSION['branch_id'],
                //"organisation_id"=>$_SESSION['organisation_id']
            ];
            //echo json_encode($single_row);die;
            $user_id = $this->user_model->add_user($single_row);

            $member_data = [
                "user_id" => $user_id,
                "client_no" => $this->member_no,
                "branch_id" => 1, 
                "registered_by" => 1,
                "date_registered" => $date_registered,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1,
                "occupation" => $customer_data[10]
            ];
            $member_id = $this->member_model->add_member(false, false, $member_data);

            if ($customer_data[9]) {
                $this->do_insert_contacts($user_id,$this->clean($customer_data[9]));
            }
            $this->insert_address($user_id,$customer_data[8]);
            // if ($customer_data[3]) {
            //     $this->do_insert_contacts($user_id,$this->clean($customer_data[6]));
            // }
           /* if ($customer_data[4]) {
                $this->do_insert_contacts($user_id,$this->clean($customer_data[4]));
            }*/
            // if ($customer_data[4]) {
            //     $this->insert_address($user_id,$customer_data[4]);
            // }
            $this->create_savings_accounts($member_id);
            return 1;
        }
        return 0;
    }

    public function clean($string) {
   $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
   $string = str_replace('-', '', $string); // Replaces all spaces with hyphens.
   $string = str_replace('(', '', $string); // Replaces all spaces with hyphens.
   $string = str_replace(')', '', $string); // Replaces all spaces with hyphens.
   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
   }

    private function do_insert_contacts($user_id, $phone_number) {
        $data = [
            "user_id" => $user_id,
            "mobile_number" => $phone_number,
            "contact_type_id" => 1,
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
        ];
        return $this->contact_model->add_contact(false, $data);
    }

    private function insert_nextofkin($user_id, $beneficary) {
        
        $nok_names = explode(" ", trim($beneficary));

        if (isset($nok_names[0])) {
            $data = [
                "user_id" => $user_id,
                "firstname" => $nok_names[0],
                "lastname" => isset($nok_names[1]) ? $nok_names[1] : "",
                "othernames" => isset($nok_names[2]) ? $nok_names[2] : "",
                "gender" => 'Male',
                "relationship" => 4,
                "share_portion" => 0,
                "telphone" => '',
                "active" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1,
               // "branch_id"=> $_SESSION['branch_id'],
               // "organisation_id"=>$_SESSION['organisation_id']
            ];
            return $this->nextOfKin_model->set($data);
        }
    }

    public function create_savings_accounts($member_id) {
        $data = [
            "member_id" => $member_id,
            "status_id" =>7,
            "client_type" =>1,
            "deposit_Product_id" =>3,
            "date_opened" =>"2019-01-01",
            "account_no" => $this->account_no,
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
           // "branch_id"=> $_SESSION['branch_id'],
           // "organisation_id"=>$_SESSION['organisation_id']
        ];
        
        $this->load->model("Data_import_model");
        $savings_account=$this->Data_import_model->add_savings_account($data);
        
        $this->create_share_accounts($member_id,$savings_account);
    }
    
    private function create_share_accounts($member_id,$savings_account) {
        $data = [
            "member_id" => $member_id,
            "status_id" =>1,
            "share_issuance_id"=>1,
            "date_opened"=>"2019-01-01",
            "default_savings_account_id"=>$savings_account,
            "share_account_no" => $this->share_account_no,
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
          //  "branch_id"=> $_SESSION['branch_id'],
           // "organisation_id"=>$_SESSION['organisation_id']
        ];
        
        $this->load->model("Data_import_model");
        $this->Data_import_model->set_share_state($data);
    }
    private function insert_address($user_id,$address){
        $data = [
            'user_id' => $user_id,
            "address1" => $address,
            'address_type_id' => 1,
            'village_id' => 1,
            'start_date' => date("Y-m-d"),
            'date_created' => time(),
            'created_by' => 1
        ];
        return $this->address_model->set2($data);
    }

}
