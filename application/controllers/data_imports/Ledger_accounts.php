<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Ledger accounts
 * @author allan_jes
 */
class Ledger_accounts extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('/');
        }
    }

    public function auto_insert() {
        $folder = "data_extract" . DIRECTORY_SEPARATOR . "members" . DIRECTORY_SEPARATOR;
        $file_name = "ledger_accounts.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates($file_path);
        echo json_encode($feedback);
    }

    private function run_updates($file_path) {
        $handle = fopen($file_path, "r");
        $total_records = $count = 0;
        $field_names = $multi_data_array = [];
        $feedback = ["success" => false, "message" => "File Could not be opened"];
        if ($handle) {
            $this->load->model("accounts_model");
            while (($data = fgetcsv($handle, 1024, ",")) !== FALSE) {
                $data1 = $this->security->xss_clean($data);
                if ($count == 0) {//the row with the field_names
                    $field_names = $data1;
                    if ($field_names[0] != "TRANSITM_ID") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains TRANSITM_ID";
                        fclose($handle);
                        return $feedback;
                    }
                } else {
                    $multi_data_array[] = $this->get_acc_dtl($data1);
                }
                $count++;
            }
            //then the remainder of the data
            if(count($multi_data_array)){
                $total_records = $total_records + $this->accounts_model->set_batch($multi_data_array);
                    $multi_data_array = [];
            }
            fclose($handle);
            if (is_numeric($total_records)) {
                $feedback["success"] = true;
                $feedback["message"] = "Update done\n $total_records accounts created";
            }
        }
        return $feedback;
    }
    private function get_acc_dtl($account_data) {
        $active_statuses = ["Y"=>1,"N"=>0];
        $date_created = $this->helpers->extract_date_time($account_data[13]);
        $date_modified = $this->helpers->extract_date_time($account_data[10],"Y-m-d H:i:s");
        if ($account_data[0] != "" && $account_data[0] != NULL) {
            $single_row = [
                "id" => $account_data[3],
                "account_code" => $account_data[3],
                "sub_category_id" => $account_data[1],
                "account_name" => $account_data[5],
                "description" => "",
                /*"opening_balance" => 0,
                "opening_balance_date" => "2019-01-01",*/
                "manual_entry" => $active_statuses[$account_data[14]],
                "status_id" => $active_statuses[$account_data[11]],
                "date_created" => $date_created,
                "date_modified" => $date_modified,
                "created_by" => $account_data[12],
                "modified_by" => $account_data[9]
            ];
            return $single_row;
        }
        return 0;
    }

}
