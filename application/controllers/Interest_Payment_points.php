<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Interest_Payment_points extends CI_Controller {
	
    public function __construct() {
		 parent::__construct(); 
         $this->load->library("session");
        $this->load->model("Interest_payment_points_model");
    }
	 
	 public function jsonList(){
        $this->data['data'] = $this->Interest_payment_points_model->get_payment_points();
		//print_r($this->data);
        echo json_encode($this->data);
    }
	
	 public function create() {

        $this->form_validation->set_rules('interest_point_name', ' Interest saving point name', array('required', 'min_length[2]'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('interest_point_description', 'Interest saving point', array('required', 'min_length[2]'), array('required' => '%s must be entered'));
       
		$feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Interest_payment_points_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Interest payment point successfully updated";
                    
                } else {
                    $feedback['message'] = "There was a problem updating";
                }
            } else {
                $checker =$this->Interest_payment_points_model->set();
                if ($checker) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Interest payment point saved";
                } else {
                    $feedback['message'] = "There was a problem saving the payment point";
                }
            }
        }
        echo json_encode($feedback);
    }
		public function change_status() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            if (($response['success'] = $this->Interest_payment_points_model->change_status($this->input->post('id'))) === true) {
                $response['message'] = "Interest payment point successfully deactivated";
            }
       // }
        echo json_encode($response);
    }
	
	function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            if (($response['success'] = $this->Interest_payment_points_model->delete($this->input->post('id'))) === true) {
                $response['message'] = "Interest payment point successfully deleted";
            }
       // }
        echo json_encode($response);
    }
	
}