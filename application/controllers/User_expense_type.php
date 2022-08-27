<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_expense_type extends CI_Controller {

    public function __construct() {
		 parent::__construct();
     $this->load->library("session");
        $this->load->model("User_expense_type_model");
        $this->load->model("user_model");
    }
    public function jsonList(){
        $data['data'] = $this->User_expense_type_model->get();
        echo json_encode($data);
    }

public function create(){
  $this->load->library('form_validation');
  $this->form_validation->set_rules('expense_type', 'Expense type', 'required');
  $this->form_validation->set_rules('description', 'Description', 'required');
  $feedback['success'] = false;

    if($this->form_validation->run() === FALSE ){
    $feedback['message'] = validation_errors('<li>','</li>');
        }else{
            if($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))){
                if($this->User_expense_type_model->update()){
                      $feedback['success'] = true;
                      $feedback['message'] = "Expense type Details successfully updated";

                       $this->helpers->activity_logs($_SESSION['id'],18,"Editing expense type ",$data['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
             
                    }else{
                      $feedback['message'] = "There was a problem updating the Expense type data, please try again";

                       $this->helpers->activity_logs($_SESSION['id'],18,"Editing expense type ",$data['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                    }
            }else{
                    if($this->User_expense_type_model->set()){
                      $feedback['success'] = true;
                      $feedback['message'] = "The Expense type list item has successfully been added";

                       $this->helpers->activity_logs($_SESSION['id'],18,"Editing expense type ",$data['message']." # ".$this->input->post('expense_type'),NULL,"Name #".$this->input->post('expense_type'));
                    }else{
                      $this->user_model->delete_by_id();
                      $feedback['message'] = "There was a problem saving the Expense type data, please try again";

                      $this->helpers->activity_logs($_SESSION['id'],18,"Editing expense type ",$data['message']." # ".$this->input->post('expense_type'),NULL,"Name #".$this->input->post('expense_type'));
                    }
            }
        }
    echo json_encode($feedback);
}


    public function change_status() {
        $data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";

        $this->helpers->activity_logs($_SESSION['id'],18,"Editing expense type ",$data['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        $data['success'] = FALSE;
            $data['message'] = $this->User_expense_type_model->change_status_by_id();
            if ($data['message'] === true) {
              $data['success'] = TRUE;
              $data['message'] = "Data successfully DELETED.";
              
                $this->helpers->activity_logs($_SESSION['id'],18,"Editing expense type ",$data['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
            }
        echo json_encode($data);
    }



}
