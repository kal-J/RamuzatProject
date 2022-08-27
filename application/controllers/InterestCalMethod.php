<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class InterestCalMethod extends CI_Controller {
	
    public function __construct() {
		 parent::__construct(); 
         $this->load->library("session");
        $this->load->model("InterestCalMethod_model");
    }
    public function jsonList(){
        $this->data['data'] = $this->InterestCalMethod_model->get();
        echo json_encode($this->data);
    }
    public function create(){

        $this->form_validation->set_rules('interest_method', 'Interest Calculation Method', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->InterestCalMethod_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Interest Calculation Method successfully updated";
                    $feedback['channel'] = $this->InterestCalMethod_model->get($_POST['id']);
                } else {
                    $feedback['message'] = "There was a problem updating Interest Calculation Method details";
                }
            } else {
                $interestcal_id = $this->InterestCalMethod_model->set();
                if ($interestcal_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Interest Calculation Method details successfully saved";
                } else {
                    $feedback['message'] = "There was a problem saving Interest Calculation Method data";
                }
            }
        }
        echo json_encode($feedback);
    }

    public function change_status() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            $this->data['message'] = $this->InterestCalMethod_model->deactivate();
            if ($this->data['message'] === true) {
                $this->data['success']= TRUE;
                $this->data['message'] =  "Interest Calculation Method successfully deactivated";
            }
         // }
        echo json_encode($this->data);
    }

    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
       // if (isset($_SESSION['role']) && isset($_SESSION['role']) == 1) {
            if (($response['success'] = $this->InterestCalMethod_model->delete($this->input->post('id'))) === true) {
                $response['message'] = "Interest Calculation Method successfully deleted";
            }
       // }
        echo json_encode($response);
    }
}
