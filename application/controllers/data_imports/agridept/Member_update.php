<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_import
 *
 * @author Reagan
 */
class Member_update extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('/');
        }
        $this->load->model("Data_import_model");
       
    }

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."agridept".DIRECTORY_SEPARATOR;
        $file_name = "members_update.csv";
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
                    if ($field_names[0] != "ID") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key ID";
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
       
            return $this->Data_import_model->update_members($customer_data[0],$customer_data[3]);
    }


}
