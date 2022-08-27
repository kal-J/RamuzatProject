<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NextOfKin extends CI_Controller {
	
    public function __construct() {
         parent::__construct(); 
         $this->load->library("session");
         if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("NextOfKin_model");
    }
    public function jsonList(){
        $this->data['data'] = $this->NextOfKin_model->get($this->input->post('user_id'));
        echo json_encode($this->data);
    }
    public function index(){
         $this->helpers->send_email(1,"HELLO");
    }

    public function create(){

        $this->form_validation->set_rules('firstname', 'First Name', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('lastname', 'Last Name', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('othernames', 'Other Name', array('min_length[2]', 'max_length[30]'));

        $this->form_validation->set_rules('gender', 'Gender', array('required'), array('required' => 'Please select %s'));
        //$this->form_validation->set_rules('relationship', 'Relationship', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('telphone', 'Telphone Number', array('valid_phone_ug'), array('valid_phone_ug' => '%s should start with +256 or 0'));   
        $this->form_validation->set_rules('address', 'Address', array('required'), array('required' => '%s must be entered'));
         $this->form_validation->set_rules("share_portion", "Share portion", "required|callback_check_percentage_total", array("required" => "%s must be entered", "check_percentage_total" => "%s total exceeds 100"));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->NextOfKin_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Beneficiary details successfully updated";
                    $feedback['nextofkin'] = $this->NextOfKin_model->get($_POST['id']);
                    //activity members 

                     $this->helpers->activity_logs($_SESSION['id'],1,"Editing next of kin",$feedback['message']." # ". $this->input->post('id'),NULL,null);
                } else {
                    $feedback['message'] = "There was a problem updating the Beneficiary details";

                     $this->helpers->activity_logs($_SESSION['id'],1,"Editing next of kin",$feedback['message']." # ". $this->input->post('id'),NULL,null);
                }
            } else {
                $nextofkin_id = $this->NextOfKin_model->set();
                if ($nextofkin_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Beneficiary details successfully saved";
                    $feedback['nextofkin'] = $this->NextOfKin_model->get();

                     $this->helpers->activity_logs($_SESSION['id'],1,"Creating next of kin",$feedback['message'],NULL,$nextofkin_id);
                } else {
                    $feedback['message'] = "There was a problem saving the Beneficiary data";

                     $this->helpers->activity_logs($_SESSION['id'],1,"Creating next of kin",$feedback['message'],NULL,$nextofkin_id);
                }
            }
        }
        echo json_encode($feedback);
    }

    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
       // if (isset($_SESSION['role']) && isset($_SESSION['role']) == 1) {
            if (($response['success'] = $this->NextOfKin_model->delete($this->input->post('id'))) === true) {
                $response['message'] = "Beneficiary Details successfully deleted";

                 $this->helpers->activity_logs($_SESSION['id'],1,"Deleting next of kin",$feedback['message'],NULL,$this->input->post('id'));
            }
       // }
        echo json_encode($response);
    }
    
    function check_percentage_total($percentage) {
        $data['percentage_total'] = $this->NextOfKin_model->validate_percentage($percentage);
        $total_percentage=$data['percentage_total']['total_percentage']+$percentage;
        if($total_percentage >100){
        return FALSE;
        }else{
        return TRUE;
        }
    }
}
