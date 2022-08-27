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
    }

    public function auto_insert() {
       // $folder = "MEMBERS".DIRECTORY_SEPARATOR."hrsacco".DIRECTORY_SEPARATOR;
        $folder = "data_extract".DIRECTORY_SEPARATOR;
        $file_name = "MEMBERS.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates($file_path);
        echo json_encode($feedback);
    }

    private function run_updates($file_path) {
        $handle = fopen($file_path, "r");
        $total_clients = $count = 0;
        $client_no="PLC00000";
        $field_names = $data_array = [];
        $feedback = ["success" => false, "message" => "File Could not be opened"];
        if ($handle) {
            ini_set('memory_limit', '200M');
            ini_set('upload_max_filesize', '200M');
            ini_set('post_max_size', '200M');
            ini_set('max_input_time', 3600);
            ini_set('max_execution_time', 3600);
            $this->load->model("user_model");
            $this->load->model("member_model");
            while (($data = fgetcsv($handle, 30048576, ",")) !== FALSE) {
                $data1 = $this->security->xss_clean($data);
                if ($count == 0) {//the row with the field_names
                    $field_names = $data1;
                    if ($field_names[0] != "id") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key ID";
                        fclose($handle);
                        return $feedback;
                    }
                } else {
                    $client_no = ++$client_no;
                    $total_clients = $total_clients + $this->insert_user_data($data1,$client_no);
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

    private function insert_user_data($customer_data,$client_no) {
   
        if ($customer_data[0] != "" && $customer_data[0] != NULL) {
            $single_row = [
              
                "firstname" => $customer_data[2],
                "lastname" => $customer_data[3],
                "othernames" => $customer_data[4],
                "status" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1
            ];
            //insert into the user table
            $user_id = $this->user_model->add_user($single_row);
            //then into the members table
            $member_data = [
                "user_id" => $user_id,
                "client_no" => $client_no,
                "branch_id" => 1, 
                "registered_by" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1
            ];
            $member_id = $this->member_model->add_member($user_id, $client_no, $member_data);
            
            
            return 1;
        }
        return 0;
    }

    private function do_insert_contacts($user_id, $phone_number) {
        $data = [
            "user_id" => $user_id,
            "mobile_number" => $phone_number,
            "contact_type_id" => 1,
            "date_created" => time(),
            "created_by" => 3,
            "modified_by" => 3,
        ];
        $this->load->model("contact_model");
        $this->contact_model->add_contact($user_id, $data);
    }

    private function insert_nextofkin($user_id, $customer_data) {
        
        $nok_names = explode(".", $customer_data[18]);

        if ( !(isset($nok_names[1])) ) {
            $nok_names = explode(" ", $customer_data[18]);
        }
        $relationships = ["BROTHER" => 8, "CHILD" => 13,"DAD" => 5,"DAUGHTER" => 12,"MOTHER" => 6,"SISTER" => 9, "HUSBAND" => 3, "SON" => 13, "WIFE" => 7,"Colleague" => 4];

        if (isset($nok_names[0])) {
            $data = [
                "user_id" => $user_id,
                "firstname" => $nok_names[0],
                "lastname" => isset($nok_names[1]) ? $nok_names[1] : "",
                "othernames" => isset($nok_names[2]) ? $nok_names[2] : "",
                "gender" => 'Male',//Not provided since the template had no provision
                "relationship" => $relationships[ (isset($customer_data[22]) && $customer_data[22] !='') ? $customer_data[22] : 'Colleague'],
                "share_portion" => 0,
                "address" => $customer_data[20],
                "telphone" => (isset($customer_data[19]) && strlen($customer_data[19]) == 9)?'0'.$customer_data[19]:'',
                "active" => 1,
                "date_created" => time(),
                "created_by" => 3,
                "modified_by" => 3,
            ];
            $this->load->model("nextOfKin_model");
            $this->nextOfKin_model->set($data);
        }
    }

    private function insert_child1($member_id, $customer_data) {
        
        $genders = ["M" => 'Male', "F" => 'Female'];
        if (isset($customer_data[27]) && $customer_data[27] !='') {
            $date_of_birth = $this->extract_date_time($customer_data[27],"Y-m-d");
        }else{
            $date_of_birth =date('Y-m-d');
        }

        $data = [
            "member_id" => $member_id,
            "firstname" => $customer_data[23],
            "lastname" => $customer_data[24],
            "othernames" => $customer_data[25],
            "gender" => $genders[(isset($customer_data[26]) && $customer_data[26] !='')? $customer_data[26] : 'F'],
            "date_of_birth" => $date_of_birth,
            "date_created" => time(),
            "created_by" => 3,
            "modified_by" => 3,
        ];
        $this->load->model("Children_model");
        $this->Children_model->set($data);
    }

    private function insert_child2($member_id, $customer_data) {
        
        $genders = ["M" => 'Male', "F" => 'Female'];
        if (isset($customer_data[32]) && $customer_data[32] !='') {
            $date_of_birth = $this->extract_date_time($customer_data[32],"Y-m-d");
        }else{
            $date_of_birth =date('Y-m-d');
        }

        $data = [
            "member_id" => $member_id,
            "firstname" => $customer_data[28],
            "lastname" => $customer_data[29],
            "othernames" => $customer_data[30],
            "date_of_birth" => $date_of_birth,
            "gender" => $genders[(isset($customer_data[31]) && $customer_data[31] !='') ? $customer_data[31] : 'F'],
            "date_created" => time(),
            "created_by" => 3,
            "modified_by" => 3,
        ];
        $this->load->model("Children_model");
        $this->Children_model->set($data);
    }
    private function extract_date_time($date_time_string, $return_format = "U"){
        $date_format = "d/m/Y";
        $date_time_obj = DateTime::createFromFormat($date_format, $date_time_string);
         return $date_time_obj->format($return_format);
    }

}
