<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Collateral_docs_setup extends CI_Controller {

    public function __construct() {
         parent::__construct();
         $this->load->library("session");
         if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("Collateral_docs_setup_model");
        $this->load->model("user_model");
    }
    public function jsonList(){
        $data['data'] = $this->Collateral_docs_setup_model->get();
        echo json_encode($data);
    }

    public function create(){
      $this->load->library('form_validation');
      $this->form_validation->set_rules('collateral_type_name', 'Loan doc type name', 'required');
      $this->form_validation->set_rules('description', 'Description', 'required');
      $feedback['success'] = false;

        if($this->form_validation->run() === FALSE ){
        $feedback['message'] = validation_errors('<li>','</li>');
            }else{
                if($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))){
                    if($this->Collateral_docs_setup_model->update()){
                          $feedback['success'] = true;
                          $feedback['message'] = "Document list successfully updated";
                          //activity log 
                           $this->helpers->activity_logs($_SESSION['id'],18,"Editing collateral type ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                        }else{
                          $feedback['message'] = "There was a problem updating the document list, please try again";
                           $this->helpers->activity_logs($_SESSION['id'],18,"Editing collateral type ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                        }
                }else{
                        if($this->Collateral_docs_setup_model->set()){
                          $feedback['success'] = true;
                          $feedback['message'] = "The document list item has successfully been added";
                           $this->helpers->activity_logs($_SESSION['id'],18,"Creating collateral type ",$feedback['message']." # ".$this->input->post('collateral_type_name'),NULL,$this->input->post('collateral_type_name'));

                        }else{
                          $feedback['message'] = "There was a problem saving the document item, please try again";

                            $this->helpers->activity_logs($_SESSION['id'],18,"Creating collateral type ",$feedback['message']." # ".$this->input->post('collateral_type_name'),NULL,$this->input->post('collateral_type_name'));
                        }
                }
            }
        echo json_encode($feedback);
    }


    public function change_status() {
        $data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
         $this->helpers->activity_logs($_SESSION['id'],18,"Deleting collateral type ",$data['message']." # ".$this->input->post('id'),NULL,$this->input->post('id'));
        $data['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            $data['message'] = $this->Collateral_docs_setup_model->change_status_by_id();
            if ($data['message'] === true) {
              $data['success'] = TRUE;
              $data['message'] = "Data successfully DELETED.";

                $this->helpers->activity_logs($_SESSION['id'],18,"Deleting collateral type ",$data['message']." # ".$this->input->post('id'),NULL,$this->input->post('id'));
            }
        //}
        echo json_encode($data);
    }



}
