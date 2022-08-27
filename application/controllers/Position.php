<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Position extends CI_Controller {
	
    public function __construct() {
		 parent::__construct(); 
     $this->load->library("session");
        $this->load->model("Position_model");
        $this->load->model('Staff_model');
        $this->load->model('User_model');
    }
    public function jsonList(){
        $this->data['data'] = $this->Position_model->get();
        echo json_encode($this->data);
    }

     public function create(){
      $this->load->library('form_validation');
      $this->form_validation->set_rules('position', 'Position Name', 'required');
      $feedback['success'] = false;
      
        if($this->form_validation->run() === FALSE ){
        $feedback['message'] = validation_errors('<li>','</li>');
        
            }else{
                if($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))){ //editing exsting item
    
                  if($this->Position_model->update()){
                    $feedback['success'] = true;
                    $feedback['message'] = "Position details successfully updated";
                    //activity log 
                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing position ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                  }else{
                    $feedback['message'] = "Position details could not be updated";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing position ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                  }
                }else{
                  //adding a new user
                  $return_id = $this->Position_model->set();
                  if(is_numeric($return_id)){
                    $feedback['success'] = true;
                    $feedback['message'] = "Position details submitted";

                    $this->helpers->activity_logs($_SESSION['id'],18,"Creating position ",$feedback['message']." # ".$return_id,NULL,"id #".$return_id);

                  }else{
                    $feedback['message'] = "There was a problem saving the position details, please contact IT support";
                    
                     $this->helpers->activity_logs($_SESSION['id'],18,"Creating position ",$feedback['message']." # ".$return_id,NULL,"id #".$return_id);

                  }
                }
            }
        echo json_encode($feedback);
    }
    
}
