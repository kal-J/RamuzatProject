<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact extends CI_Controller {
  
    public function __construct() {
     parent::__construct(); 
     $this->load->library("session");
     if(empty($this->session->userdata('id'))){
        redirect('welcome');
    } 
        $this->load->model("contact_model");
        $this->load->model("user_model");
    }
    public function jsonList(){
        $data['data'] = $this->contact_model->get( $this->input->post('user_id') );
        echo json_encode($data);
    }
  
#user_id, mobile_number, contact_type_id, date_created, date_modified, created_by, modified_by
    public function create(){
      $this->load->library('form_validation');
      $this->form_validation->set_rules("mobile_number", "Phone Number", "required|valid_phone_int|callback__check_phone_number", array("required" => "%s must be entered", "valid_phone_int" => "%s should start with country code e.g +256 or 0", "_check_phone_number" => "%s already exists"));
      // $this->form_validation->set_rules("mobile_number2", "Phone Number", "required|valid_phone_int|callback__check_phone_number", array("required" => "%s must be entered", "valid_phone_int" => "%s should start with country code e.g +256 or 0", "_check_phone_number" => "%s already exists"));
      
      $this->form_validation->set_rules('contact_type_id', 'Contact type', 'required');
      $feedback['success'] = false;
      
        if($this->form_validation->run() === FALSE ){
        $feedback['message'] = validation_errors('<li>','</li>');
            }else{
                if($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))){ //editing contact
                    
                    if($this->contact_model->update_contact()){
                          $feedback['success'] = true;
                          $feedback['message'] = "Contact Details successfully updated";

                           $this->helpers->activity_logs($_SESSION['id'],1,"Editing contact",$feedback['message']." # ". $this->input->post('id'),NULL,NULL);
                        }else{
                          $feedback['message'] = "There was a problem updating the contact data, please try again";

                              $this->helpers->activity_logs($_SESSION['id'],1,"Editing contact",$feedback['message']." # ". $this->input->post('id'),NULL,null);

                        }
                }else{
                    if($inserted_id=$this->contact_model->add_contact()){
                      $feedback['success'] = true;
                      $feedback['message'] = "contact has been successfully Added";
                      $feedback['contact']=$inserted_id;

                          $this->helpers->activity_logs($_SESSION['id'],1,"Creating contact",$feedback['message']." # ".$inserted_id,NULL,null);
                    }else{
                      $feedback['message'] = "There was a problem saving the contact data, please try again";

                      $this->helpers->activity_logs($_SESSION['id'],1,"Creating contact",$feedback['message']." # ".$inserted_id,NULL,NULL);
                    }
                }
            }
        echo json_encode($feedback);
    }

    function _check_phone_number($phone_number) {
        $existing_number = $this->contact_model->validate_contact($phone_number);
        
        return $existing_number;
    }
    
    public function delete(){
      $response['message'] = "Data could not be deleted, contact support.";
      $response['success'] = FALSE;
      if($this->contact_model->delete_by_id()){
        $response['success'] = TRUE;
        $response['message'] = "Data successfully deleted.";

  $this->helpers->activity_logs($_SESSION['id'],1,"Deleting contact",$response['message']." # ",NULL,NULL);
      }
      echo json_encode($response);
    }

  
}
