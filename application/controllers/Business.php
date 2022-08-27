<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Business extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("Business_model");
    }

    public function jsonList() {
        $data['data'] = $this->Business_model->get();
        echo json_encode($data);
    }

    public function create() {
        $this->form_validation->set_rules('businessname', 'Business Name', array('required', 'min_length[2]', 'max_length[80]'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('natureofbusiness', 'Nature of Business', array('required', 'min_length[2]', 'max_length[80]'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('businesslocation', 'Location', array('min_length[2]', 'max_length[80]'));
        $this->form_validation->set_rules('numberofemployees', 'Number of Employees', array('required'), array('required' => '%s must be entered'));

        //////////////////////////
        $member_id = 'member_id_'.trim($_POST['member_id']);
        $organisation_id = $_SESSION['organisation_id'];
        if (empty($_FILES['certificateofincorporation']['name'])) {
            /*   $this->form_validation->set_rules('certificateofincorporation', 'Certificate of Incorporation', array('required'), array('required' => '%s must be provided')); */
        }
        $config['upload_path']          = APPPATH. '../uploads/organisation_'.$organisation_id.'/user_docs/certificate_of_incorporation/';
        $config['allowed_types'] = 'pdf|doc|docx|xls|png|jpg|jpeg';
        $config['file_name'] = $member_id.$_FILES['certificateofincorporation']['name'];
        $config['max_size'] = 1500;
        $config['max_width'] = 0;
        $config['max_height'] = 0;
        $config['remove_spaces'] = false;
        $config['overwrite'] = TRUE;
        

        $file_name = $config['file_name'];

        $this->load->library('upload', $config);

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                $this->load->library('upload', $config);
                $this->upload->do_upload('certificateofincorporation');
                if ($this->Business_model->update($file_name)) {

                    $feedback['success'] = true;
                    $feedback['message'] = "Business details successfully updated";
                    $feedback['business'] = $this->Business_model->get($_POST['id']);
                } else {
                    $error = array('error' => $this->upload->display_errors());
                    $feedback['message'] = "There was a problem updating the business details";// . $error;
                }
            } else {
                $this->load->library('upload', $config);
                    $this->upload->do_upload('certificateofincorporation');
                    if ($this->Business_model->set($file_name)) {
                        $this->data = array('upload_data' => $this->upload->data());
                        $feedback['success'] = true;
                        $feedback['message'] = "Business details successfully saved";
                    } else {
                    //$error = $this->upload->display_errors();
                    $feedback['message'] = "There was a problem saving the business data";//$error;
                }
            }
        }
        echo json_encode($feedback);
    }

    public function delete(){
      $response['message'] = "Business details could not be deleted, contact IT support.";
      $response['success'] = FALSE;
      if($this->Business_model->change_status_by_id()){
        $response['success'] = TRUE;
        $response['message'] = "Business details successfully deleted.";
      }
      echo json_encode($response);
    }

}
