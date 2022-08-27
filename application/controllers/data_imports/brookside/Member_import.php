<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_import
 *
 * @author Eric
 */
class Member_import extends CI_Controller {

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

        $this->relationships = ['Member' => 1, 'Staff' => 2, 'Husband'=> 3,'Colleague' => 4, 'Father' => 5, 'Mother' => 6, 'Wife' => 7, 'Brother'  => 8, 'Sister' => 9, 'Cousin brother' => 10,'Cousin' => 10, 'Cousin sister' => 11, 'Daughter' => 12, 'Son' => 13, 'Father in law' => 14, 'In-law' => 14, 'Mother in law' => 15, 'Sister in law' => 16, 'Brother in law' => 17, 'Grand father' => 18, 'Grand mother' => 19, 'Uncle' => 20, 'Aunt' => 21, 'Niece' => 22, 'Nephew' => 23, 'Grand daughter' => 24, 'Grand son' => 25, 'Friend' => 26,'Boss' => 26,'Spouse' => 4];

        $this->gender_data = ['Husband'=> 1,'Father' => 1, 'Son' => 1, 'Brother'  => 1, 'Cousin brother' => 1, 'In-law' => 1, 'Grand father' => 1, 'Uncle' => 1, 'Grand son' => 1, 'Boss' => 1];

        $this->client_no="B0000";
        $this->account_no="SV0000";
        $this->share_account_no="SH0000";
    }

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."brookside".DIRECTORY_SEPARATOR;
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
                    if ($field_names[0] != "MEMBER_NAME") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key MEMBER_NAME";
                        fclose($handle);
                        return $feedback;
                    }
                } else {
                    $this->client_no = ++$this->client_no;
                    $this->account_no = ++$this->account_no;
                    $this->share_account_no = ++$this->share_account_no;
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
        $date_of_birth = date('Y-m-d');
        $this->date_registered = $this->helpers->extract_date_time($customer_data[1],"Y-m-d");
        $gender = ["M" => 1, "F" => 0];
        $marital_status = ["Na" => 1, "Single" => 1,"Married" => 2,"Divorced" =>4, "Widow"=>5];

        if ($customer_data[0] != "" && $customer_data[0] != NULL) {
            $names = explode(" ", trim($customer_data[0]));
            $single_row = [
              
                "firstname" => $names[0],
                "lastname" => isset($names[1])?$names[1]:'',
                "othernames" => isset($names[2])?$names[2]:'',
                "gender" => 1,
                "email" => $customer_data[5],
                "marital_status_id" => 1,
                "date_of_birth" => $date_of_birth,
                "children_no" => 0,
                "dependants_no" => 0,
                "status" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1
            ];
            $user_id = $this->user_model->add_user($single_row);

            $member_data = [
                "user_id" => $user_id,
                "client_no" => $this->client_no,
                "branch_id" => 1, 
                "occupation" => $customer_data[2],
                "registered_by" => 1,
                "date_registered" => $this->date_registered,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1
            ];
            $member_id = $this->member_model->add_member(false, false, $member_data);

            if ($customer_data[3]) {
                $this->do_insert_contacts($user_id,$customer_data[3]);
            }
            if ($customer_data[4]) {
                $this->do_insert_contacts($user_id,$customer_data[4]);
            }
            if ($customer_data[7]) {
                $this->insert_nextofkin($user_id,$customer_data);
            }
            if ($customer_data[6]) {
                $this->insert_address($user_id,$customer_data[6]);
            }
            $this->create_savings_accounts($member_id);
            return 1;
        }
        return 0;
    }

    private function do_insert_contacts($user_id, $phone_number) {
        $data = [
            "user_id" => $user_id,
            "mobile_number" => '0'.$phone_number,
            "contact_type_id" => 1,
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
        ];
        return $this->contact_model->add_contact(false, $data);
    }

    private function insert_nextofkin($user_id, $customer_data) {
        
        $nok_names = explode(" ", trim($customer_data[7]));

        if (isset($nok_names[0])) {
            $data = [
                "user_id" => $user_id,
                "firstname" => $nok_names[0],
                "lastname" => isset($nok_names[1]) ? $nok_names[1] : "",
                "othernames" => isset($nok_names[2]) ? $nok_names[2] : "",
                "gender" => $this->gender((isset($customer_data[8]) && $customer_data[8] !='') ? ucfirst(strtolower(trim($customer_data[8]," "))) : 'Colleague'),
                "relationship" => $this->relationships[ (isset($customer_data[8]) && $customer_data[8] !='') ? ucfirst(strtolower(trim($customer_data[8]," "))) : 'Colleague'],
                "share_portion" => 100,
                "address" => (isset($customer_data[10]))?$customer_data[10]:'NIL',
                "telphone" => (isset($customer_data[9]) && strlen($customer_data[9]) == 9)?'0'.$customer_data[9]:'',
                "active" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1,
            ];
            return $this->nextOfKin_model->set($data);
        }
    }

    public function create_savings_accounts($member_id) {
        $data = [
            "member_id" => $member_id,
            "status_id" =>7,
            "client_type" =>1,
            "deposit_Product_id" =>1,
            "account_no" => $this->account_no,
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
        ];
        $savings_account=$this->Data_import_model->add_savings_account($data);
        
        $this->create_share_accounts($member_id,$savings_account);
    }
    
    private function create_share_accounts($member_id,$savings_account) {
        $data = [
            "member_id" => $member_id,
            "status_id" =>1,
            "share_issuance_id"=>1,
            "date_opened"=> $this->date_registered,
            "default_savings_account_id"=>$savings_account,
            "share_account_no" => $this->share_account_no,
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
        ];
        $this->Data_import_model->set_share_state($data);
    }

    private function gender($array_key){
        if (array_key_exists($array_key, $this->gender_data)) {
           return 1;
        }
        return 0;
    }

    private function insert_address($user_id,$address){
        $data = [
            'user_id' => $user_id,
            'address1' => $address,
            'address_type_id' => 1,
            'village_id' => 1,
            'start_date' => $this->date_registered,
            'date_created' => time(),
            'created_by' => 1
        ];
        return $this->address_model->set2($data);
    }

}
