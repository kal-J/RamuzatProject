<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_engine extends CI_Controller {
  
    public function __construct() {
      parent::__construct(); 
      $this->load->library("session");
      if(empty($this->session->userdata('id'))){
        redirect('welcome');
      } 

      $this->load->model("payment_engine_model");
    }
    public function jsonList(){
        $data['data'] = $this->payment_engine_model->get();
        echo json_encode($data);
    }
    public function smsjsonList(){
        $data['data'] = $this->payment_engine_model->get_sms();
        echo json_encode($data);
    }

    public function create(){
      $this->load->library('form_validation');
     $this->form_validation->set_rules('organisation_id', 'organisation id', array('required'));
     $this->form_validation->set_rules('payment_id', 'Payment engine', array('required'));
      $feedback['success'] = false;
      
        if($this->form_validation->run() === FALSE ){
          $feedback['message'] = validation_errors('<li>','</li>');
        }else{
            if($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))){ 
                if($this->payment_engine_model->update()){
                  $feedback['success'] = true;
                  $feedback['message'] = "Payment engine Details successfully updated";
                }else{
                  $feedback['message'] = "There was a problem updating the payment engine data, please try again";
                }
            }else{
                //adding a new item
                if($inserted_id=$this->payment_engine_model->set()){
                  $feedback['success'] = true;
                  $feedback['message'] = "Payment engine has been successfully Added";
                }else{
                  $feedback['message'] = "There was a problem saving the payment engine data, please try again";
                }
            }
        }
        echo json_encode($feedback);
    }

    public function create_sms(){
      $this->load->library('form_validation');
     $this->form_validation->set_rules('organisation_id', 'organisation id', array('required'));
     $this->form_validation->set_rules('api_key', 'API_KEY', array('required'));
     $this->form_validation->set_rules('name', 'Gateway NAME', array('required'));
      $feedback['success'] = false;
      
        if($this->form_validation->run() === FALSE ){
          $feedback['message'] = validation_errors('<li>','</li>');
        }else{
            if($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))){ 
                if($this->payment_engine_model->update_sms()){
                  $feedback['success'] = true;
                  $feedback['message'] = "sms engine Details successfully updated";
                }else{
                  $feedback['message'] = "There was a problem updating the sms engine data, please try again";
                }
            }else{
                //adding a new item
                if($inserted_id=$this->payment_engine_model->set_sms()){
                  $feedback['success'] = true;
                  $feedback['message'] = "sms engine has been successfully Added";
                }else{
                  $feedback['message'] = "There was a problem saving the sms engine data, please try again";
                }
            }
        }
        echo json_encode($feedback);
    }

    public function requirements_creation(){
      $this->load->library('form_validation');
     $this->form_validation->set_rules('transaction_channel_id', 'Transaction Channel', array('required'));
      $feedback['success'] = false;
      
        if($this->form_validation->run() === FALSE ){
          $feedback['message'] = validation_errors('<li>','</li>');
        }else{
            if($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))){ 
                if($this->payment_engine_model->update_requirement()){
                  $feedback['success'] = true;
                  $feedback['message'] = "Payment engine requirements Details successfully updated, please reload first";

                    $this->helpers->activity_logs($_SESSION['id'],18,"Editing payment engine ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                }else{
                  $feedback['message'] = "There was a problem updating the payment engine data, please try again";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing payment engine ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                }
            }else{
                //adding a new item
                if($inserted_id=$this->payment_engine_model->set_requirement()){
                  $feedback['success'] = true;
                  $feedback['message'] = "Payment engine requirements have been successfully Added, please reload first";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing payment engine ",$feedback['message']." # ".$inserted_id,NULL,"id #".$inserted_id);
                }else{
                  $feedback['message'] = "There was a problem saving the payment engine requirements, please try again";

                   $this->helpers->activity_logs($_SESSION['id'],18,"Editing payment engine ",$feedback['message']." # ".$inserted_id,NULL,"id #".$inserted_id);
                }
            }
        }
        echo json_encode($feedback);
    }

    public function change_status() {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Payment engine could not be ".$msg."activated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->input->post('status_id') == 1) {
          if ($this->payment_engine_model->check_payment_engine()) {
              $response['message'] = "Payment engine could not be activated because there is an active payment engine.";
               $this->helpers->activity_logs($_SESSION['id'],18,"Editing payment engine ",$response['message']." # ".$msg,NULL,"id #".$msg);

          }else if ($this->payment_engine_model->deactivate()) {
            $response['success'] = TRUE;
            $response['message'] = "Payment engine has successfully been ".$msg."activated.";

             $this->helpers->activity_logs($_SESSION['id'],18,"Managing payment engine ",$response['message']." # ".$msg,NULL,"id #".$msg);
          }
        }else if ($this->payment_engine_model->deactivate()){
            $response['success'] = TRUE;
            $response['message'] = "Payment engine has successfully been ".$msg."activated.";

             $this->helpers->activity_logs($_SESSION['id'],18,"Managing payment engine ",$response['message']." # ".$msg,NULL,"id #".$msg);
        }
        
        echo json_encode($response);
    }
  
}
