<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_import
 *
 * @author allan_jes
 */
class Data_import extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('/');
        }
    }

    public function auto_insert() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."members".DIRECTORY_SEPARATOR;
        $file_name = "member_list.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates($file_path);
        echo json_encode($feedback);
    }

    private function run_updates($file_path) {
        $handle = fopen($file_path, "r");
        $total_clients = $count = 0;
        $field_names = $data_array = [];
        $feedback = ["success" => false, "message" => "File could not be opened"];
        if ($handle) {
            /*
              //ini_set("memory_limit","2048M"); */
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
                    if ($field_names[0] != "CUSTOMER_ID") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key CUSTOMER_ID";
                        fclose($handle);
                        return $feedback;
                    }

                    /* if (in_array($field_names[0], $db_column_names)) { //if the field name is amnong those in the pap table column names
                      $feedback['message'] = "The 'column header names' are incorrect. Please ensure you have the most recent 'file template'";
                      fclose($handle);
                      return $feedback;
                      } */
                } else {
                    $total_clients = $total_clients + $this->insert_user_data($data1);
                }
                $count++;
            }
            fclose($handle);
            /*$this->load->helper("file");
            delete_files($file_path);*/

            if (is_numeric($total_clients)) {
                $feedback["success"] = true;
                $feedback["message"] = "Update done\n $total_clients records updated";
            }
        }
        return $feedback;
    }

    private function insert_user_data($customer_data) {
        $saluations = ["M" => "Mister", "F" => "Miss"];
        $genders = ["M" => 1, "F" => 0];
        $statuses = ["N" => 0, "Y" => 1];
        $date_registered = $this->extract_date_time($customer_data[26],"Y-m-d");
        $lastnames = explode(" ", $customer_data[3]);
        $date_created = $this->extract_date_time($customer_data[28]);
        if ($customer_data[0] != "" && $customer_data[0] != NULL) {
            $single_row = [
                "salutation" => $saluations[isset($customer_data[14]) ? $customer_data[14] : ""],
                "firstname" => $customer_data[2],
                "marital_status_id" => 1,
                "lastname" => $lastnames[0],
                "othernames" => isset($lastnames[1])?$lastnames[1]:"",
                "email" => ($customer_data[10]!=="other@kcca.go.ug"||$this->helpers->valid_email($customer_data[10]))?$customer_data[10]:"",
                "gender" => $genders[isset($customer_data[14]) ? $customer_data[14] : 1],
                "disability" => 0,
                "status" => $statuses[isset($customer_data[24]) ? $customer_data[24] : 0],
                "date_of_birth" => date('Y-m-d'),
                "date_registered" => $date_registered,
                "date_created" => $date_created,
                "created_by" => $customer_data[27],
                "modified_by" => $customer_data[27]
            ];
            //insert into the user table
            $user_id = $this->user_model->add_user($single_row);
            //then into the members table
            $member_data = [
                "id" => $customer_data[0],
                "user_id" => $user_id,
                "client_no" => $customer_data[1],
                "branch_id" => $customer_data[7], //taking the department id as the branch id
                "occupation" => 'NA', //taking the designation as the occupation
                //"occupation" => $customer_data[9]?$customer_data[9]:'', //taking the designation as the occupation
                "subscription_plan_id" => 1,
                "registered_by" => $customer_data[27],
                "date_created" => $date_created,
                "date_registered" => $date_registered,
                "created_by" => $customer_data[27],
                "modified_by" => $customer_data[27]
            ];
            $member_id = $this->member_model->add_member($user_id, $customer_data[1], $member_data);
            //then we insert the contacts
            $this->insert_contacts($user_id, $customer_data);
            //and the next of kin
            $this->insert_nextofkin($user_id, $customer_data);
            return 1;
        }
        return 0;
    }

    private function insert_contacts($user_id, $customer_data) {
        $staff_id = $customer_data[27];
        $created_at = $customer_data[28];
        if (isset($customer_data[11]) && $customer_data[11] !== "") {
            $this->do_insert_contacts($user_id, $customer_data[11], $staff_id,$created_at);
        }
        if (isset($customer_data[12]) && $customer_data[12] !== "") {
            $this->do_insert_contacts($user_id, $customer_data[12], $staff_id, $created_at);
        }
    }

    private function do_insert_contacts($user_id, $phone_number, $staff_id, $created_at) {
        $data = [
            "user_id" => $user_id,
            "mobile_number" => $this->pure_phone_no($phone_number),
            "contact_type_id" => 1,
            "date_created" => $this->extract_date_time($created_at),
            "created_by" => $staff_id,
            "modified_by" => $staff_id,
        ];
        $this->load->model("contact_model");
        $this->contact_model->add_contact($user_id, $data);
    }

    private function insert_nextofkin($user_id, $customer_data) {
        $nok_names = explode(" ", $customer_data[15]);
        if (isset($nok_names[0])) {
            $data = [
                "user_id" => $user_id,
                "firstname" => $nok_names[0],
                "lastname" => isset($nok_names[1]) ? $nok_names[1] : "",
                "othernames" => isset($nok_names[2]) ? $nok_names[2] : "",
                "gender" => $customer_data[9],
                "relationship" => $customer_data[19],
                "share_portion" => 0,
                "address" => $customer_data[17],
                "telphone" => $this->pure_phone_no($customer_data[16]),
                "active" => 1,
                "date_created" => $this->extract_date_time($customer_data[28]),
                "created_by" => $customer_data[27],
                "modified_by" => $customer_data[27],
            ];
            $this->load->model("nextOfKin_model");
            $this->nextOfKin_model->set($data);
        }
    }

    private function pure_phone_no($messy_phone_no = "") {
        $first_pass = preg_replace(["/\s/", "/-/"], "", $messy_phone_no);
        if(preg_match("/^((^0)|(^\+256))/", $first_pass)){
            $second_pass = "+256".$first_pass;
            return $second_pass;
        }
        return $first_pass;
    }
    private function extract_date_time($date_time_string, $return_format = "U"){
        $date_format = "m/d/Y H:i:s";
        $date_time_obj = DateTime::createFromFormat($date_format, $date_time_string);
         return $date_time_obj->format($return_format);
    }

}
