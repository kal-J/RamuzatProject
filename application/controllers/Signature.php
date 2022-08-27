<?php

defined('BASEPATH') OR exit('No direct script access allowed');
class Signature extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        $this->load->model('Signature_model');
    }

    public function jsonList() {
        $this->data['data'] = $this->Signature_model->get();
        echo json_encode($this->data);
    }

    public function add_signature() {
        // print_r($this->session->userdata());
        $this->load->helper('file');
        $this->load->model('organisation_model');
        $userid = $this->input->post('i_d');
        $user_name = $this->input->post('user_name');
        $org_id = $this->session->userdata('organisation_id');

        $imagery = $this->input->post('image');
        if (!empty($imagery) && $userid != "") {
            $data2 = $imagery;
            list($type, $data2) = explode(';', $data2);
            list(, $data2) = explode(',', $data2);
            $data2 = base64_decode($data2);
            $mypath = 'uploads/organisation_' . $org_id . '/user_docs/signatures';
            if (!is_dir($mypath)) {
                mkdir('./' . $mypath, 0755, true);
            }
            $imageName = $user_name . time() . rand(10, 256) . '.jpg';
            $db_img_link = $imageName;
            $path_folder = './' . $mypath . '/' . $imageName;
            if (file_put_contents($path_folder, $data2)) {
                $photo = array(
                    'user_id' => $userid,
                    'signature' => $db_img_link,
                    'date_created' => time(),
                    'created_by' => $_SESSION['staff_id']
                );
              /* if ($path_old_folder != "") {
                    if (file_exists($path_old_folder)) {
                        unlink($path_old_folder);
                    }
                }  */
                $this->db;
                $insertt=$this->db->insert('fms_user_signatures', $photo);
                if ($insertt===true) {
                      $feedback['response'] = base_url().$mypath.'/'. $imageName;
                      $feedback['message'] = "Data submitted";
                } else {
                    $feedback['message'] = "Database Error";
                }
            }
        } else {
            $feedback['message'] = "Failed, Invalid Photo";
        }
        echo json_encode($feedback);
    }

}
