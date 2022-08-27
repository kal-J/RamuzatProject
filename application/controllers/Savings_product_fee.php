<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Savings_product_fee extends CI_Controller {
	
    public function __construct() {
		 parent::__construct(); 
        $this->load->library("session");
        $this->load->model("Savings_product_fee_model");
    
    }
	 
	 public function jsonList(){
        $data['data'] = $this->Savings_product_fee_model->get();
	//	print_r($data); die;
        echo json_encode($data);
    }
	
	
    public function create() {

        $this->form_validation->set_rules('saving_product_id', 'saving product', array('required', 'min_length[1]', 'max_length[100]'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('savings_fees_id', 'Savings fee', array('required', 'min_length[1]', 'max_length[100]'), array('required' => '%s must be entered'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Savings_product_fee_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Savings fee details successfully updated";
                    $feedback['loan_product_fee'] = $this->Savings_product_fee_model->get($_POST['id']);
                } else {
                    $feedback['message'] = "There was a problem updating the saving product fee details";
                }
            } else {
                $sett = $this->Savings_product_fee_model->set();
                if ($sett) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Savings fee details successfully saved";
                } else {
                    $feedback['message'] = "There was a problem saving the loan product fee data";
                }
            }
        }
        echo json_encode($feedback);
    }

    function change_status() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
          //  $this->form_validation->set_rules('status_id', 'Status','required|trim|max_length[5]');
           // if ($this->form_validation->run() === true){ 
                if (($response['success'] = $this->Savings_product_fee_model->change_status($this->input->post('id'))) === true) {
                    $response['message'] = "Saving fees status successfully updated";
                }
          //  }else{
         // $response['message'] = validation_errors();
        //   }
       // }
        echo json_encode($response);
    }

    
		function delete_me() {
            //if user not logged in, take them to the login page
            $response['message'] = "You do not have access to delete this record";
            $response['success'] = FALSE;
            //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
              //  $this->form_validation->set_rules('status_id', 'Status','required|trim|max_length[5]');
               // if ($this->form_validation->run() === true){ 
                    if (($response['success'] = $this->Savings_product_fee_model->delete_by_id($this->input->post('id'))) === true) {
                        $response['message'] = "Saving fees details successfully removed";
                    }
              //  }else{
             // $response['message'] = validation_errors();
            //   }
           // }
            echo json_encode($response);
        }
	
}