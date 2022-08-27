<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('User_model');
    }

    public function jsonList(){
        $this->data['data'] = $this->User_model->get_staff();
        echo json_encode($this->data);
    }

    public function create() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('firstame', 'Firstame', 'required');
        $this->form_validation->set_rules('lastname', 'Lastname', 'required');
        $feedback['error'] = true;

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {
            if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) { //editing exsting item
                if ($this->User_model->update_user()) {
                    $feedback['error'] = false;
                    $feedback['message'] = "Data updated";
                } else {
                    $feedback['message'] = "Data could not be updated";
                }
            } else {
                //adding a new user
                $return_id = $this->User_model->add_user();

                if (is_numeric($return_id)) {
                    $feedback['error'] = false;
                    $feedback['message'] = "Data submitted";

                    $this->data['title'] = $this->data['sub_title'] = "Staff Management";
                    $this->load->view("includes/header", $this->data);
                    $this->load->view('staff/individual_staff_view');
                    $this->load->view('includes/footer');
                } else {
                    $feedback['message'] = "There was a problem saving the data, please contact IT support";
                }
            }
        }
        echo json_encode($feedback);
    }

    public function add_profile_pic() {
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
            $mypath = 'uploads/organisation_' . $org_id . '/user_docs/profile_pics';
            if (!is_dir($mypath)) {
                mkdir('./' . $mypath, 0755, true);
            }
            $imageName = $user_name . time() . rand(10, 256) . '.jpg';
            $db_img_link = $imageName;
            $path_folder = './' . $mypath . '/' . $imageName;
            if (file_put_contents($path_folder, $data2)) {
                $photo = array(
                    'photograph' => $db_img_link
                );
              /* if ($path_old_folder != "") {
                    if (file_exists($path_old_folder)) {
                        unlink($path_old_folder);
                    }
                }  */
                $this->db;
                $this->db->where('id', $userid);
                $updte=$this->db->update('fms_user', $photo);
                if ($updte===true) {
                    if($_SESSION['id']===$userid){
                         echo  $this->session->set_userdata('photograph',$imageName);
                     }
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
