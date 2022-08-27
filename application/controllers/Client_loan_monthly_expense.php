<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Client_loan_monthly_expense extends CI_Controller {

    public function __construct() {
         parent::__construct();
         $this->load->library("session");
         if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("client_loan_monthly_expense_model");
        $this->load->model("user_model");
    }
    public function jsonList(){
        $data['data'] = $this->client_loan_monthly_expense_model->get();
        echo json_encode($data);
    }

   
   
   

public function create(){
  $this->load->library('form_validation');
  $this->form_validation->set_rules('expense_id', 'expense item', 'required');
  $this->form_validation->set_rules('amount', 'Amount', 'required');
  $this->form_validation->set_rules('description', 'Description', 'required');
  $feedback['success'] = false;

    if($this->form_validation->run() === FALSE ){
    $feedback['message'] = validation_errors('<li>','</li>');
        }else{
            if($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))){
                if($this->client_loan_monthly_expense_model->update()){
                      $feedback['success'] = true;
                      $feedback['message'] = "user expense Details successfully updated";
                    }else{
                      $feedback['message'] = "There was a problem updating the user expense data, please try again";
                    }
            }else{
                    if($this->client_loan_monthly_expense_model->set()){
                      $feedback['success'] = true;
                      $feedback['message'] = "The user expense list item has successfully been added";
                    }else{
                      $feedback['message'] = "There was a problem saving the user expense data, please try again";
                    }
            }
        }
    echo json_encode($feedback);
}


    public function change_status() {
        $data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $data['success'] = FALSE;
            $data['message'] = $this->client_loan_monthly_expense_model->change_status_by_id();
            if ($data['message'] === true) {
              $data['success'] = TRUE;
              $data['message'] = "Data successfully DELETED.";
            }
        echo json_encode($data);
    }



}
