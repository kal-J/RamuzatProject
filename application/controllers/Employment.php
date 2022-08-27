<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employment extends CI_Controller {
	
    public function __construct() {
     parent::__construct(); 
     $this->load->library("session");
     if(empty($this->session->userdata('id'))){
      redirect('welcome');
     } 
        $this->load->model("Employment_model");
    }
    public function jsonList(){
        $this->data['data'] = $this->Employment_model->get($this->input->post('user_id'));
        echo json_encode($this->data);
    }

    public function nature_of_employment(){
        $this->data['data'] = $this->Employment_model->get_nature_of_employment();
    }

     public function create(){
      $this->load->library('form_validation');
      $this->form_validation->set_rules('start_date', 'Start Date', 'required');
      $this->form_validation->set_rules('user_id', 'User ID', 'required');
     
      $feedback['success'] = false;
      
        if($this->form_validation->run() === FALSE ){
        $feedback['message'] = validation_errors('<li>','</li>');
        
            }else{
            	if($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))){ //editing exsting item
    
			      if($this->Employment_model->update_employment()){
			        $feedback['success'] = true;
			        $feedback['message'] = "Employment history updated";
			      }else{
			        $feedback['message'] = "Employment history could not be updated";
			      }
			    }else{
			      //adding a new user
			      $return_id = $this->Employment_model->add_employment();
			      if(is_numeric($return_id)){
			        $feedback['success'] = true;
			        $feedback['message'] = "Employment history submitted";

			      }else{
			        $feedback['message'] = "There was a problem saving the Employment history, please contact IT support";

			      }
			    }
            }
        echo json_encode($feedback);
    }

    public function delete(){
      $response['message'] = "Employment history could not be deleted, contact IT support.";
      $response['success'] = FALSE;
      if($this->Employment_model->delete_by_id($this->input->post('id'))){
        $response['success'] = TRUE;
        $response['message'] = "Employment history successfully deleted.";
      }
      echo json_encode($response);
    }
    public function change_status(){
      $response['message'] = "Employment history could not be deactivated, contact IT support.";
      $response['success'] = FALSE;
      if($this->Employment_model->change_status_by_id($this->input->post('id'))){
        $response['success'] = TRUE;
        $response['message'] = "Employment history successfully deactivated.";
      }
      echo json_encode($response);
    }

}
