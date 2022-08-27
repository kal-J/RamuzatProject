<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_doc_type extends CI_Controller {

    public function __construct() {
		 parent::__construct();
     $this->load->library("session");
        $this->load->model("User_doc_type_model");
        $this->load->model("user_model");
    }
    public function jsonList(){
        $data['data'] = $this->User_doc_type_model->get();
        echo json_encode($data);
    }

public function create(){
  $this->load->library('form_validation');
  $this->form_validation->set_rules('user_doc_type', 'User doc type name', 'required');
  $this->form_validation->set_rules('description', 'Description', 'required');
  $feedback['success'] = false;

    if($this->form_validation->run() === FALSE ){
    $feedback['message'] = validation_errors('<li>','</li>');
        }else{
            if($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))){
                if($this->User_doc_type_model->update()){
                      $feedback['success'] = true;
                      $feedback['message'] = "Document Details successfully updated";

                      //activity log 
                       $this->helpers->activity_logs($_SESSION['id'],18,"Editing user document ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                    }else{
                      $feedback['message'] = "There was a problem updating the document data, please try again";

                        $this->helpers->activity_logs($_SESSION['id'],18,"Editing user document ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                    }
            }else{
                    if($this->User_doc_type_model->set()){
                      $feedback['success'] = true;
                      $feedback['message'] = "The document list item has successfully been added";

                       $this->helpers->activity_logs($_SESSION['id'],18,"Creating user document ",$feedback['message']." # ".$this->input->post('user_doc_type'),NULL,"id #".$this->input->post('user_doc_type'));
                    }else{
                      $feedback['message'] = "There was a problem saving the document data, please try again";

                       $this->helpers->activity_logs($_SESSION['id'],18,"Creating user document ",$feedback['message']." # ".$this->input->post('user_doc_type'),NULL,"id #".$this->input->post('user_doc_type'));
                    }
            }
        }
    echo json_encode($feedback);
}


    public function change_status() {
        $data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $data['success'] = FALSE;

         $this->helpers->activity_logs($_SESSION['id'],18,"Deleting user document ",$data['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
             
            $data['message'] = $this->User_doc_type_model->change_status_by_id();
            if ($data['message'] === true) {
              $data['success'] = TRUE;
              $data['message'] = "Data successfully DELETED.";

               $this->helpers->activity_logs($_SESSION['id'],18,"Deleting user document ",$data['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
            }
        echo json_encode($data);
    }



}
