<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Non_working_days extends CI_Controller {
  
    public function __construct() {
     parent::__construct(); 
     $this->load->library("session");
     if(empty($this->session->userdata('id'))){
        redirect('welcome');
    } 
        $this->load->model("non_working_days_model");
    }
    public function create(){
      $this->load->library('form_validation');
      // print_r($this->input->post('monday')); die;
      $feedback['success'] = false;
        if($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))){//editing Non working days
            if($this->non_working_days_model->update()){
                  $feedback['success'] = true;
                  $feedback['message'] = "Non working days  successfully updated";
                  $feedback['non_working_days']=$this->non_working_days_model->get();
                }else{
                  $feedback['message'] = "There was a problem updating the Non working days data, please try again";
                }
        }else{
            if($inserted_id=$this->non_working_days_model->set()){
              $feedback['success'] = true;
              $feedback['message'] = "Non working days have been successfully Added";
              $feedback['non_working_days']=$this->non_working_days_model->get();
            }else{
              $feedback['message'] = "There was a problem saving the Non working days data, please try again";
            }
        }
        echo json_encode($feedback);
    }
  
}
