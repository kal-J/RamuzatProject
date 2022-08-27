<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tax_application extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model("tax_application_model");
    }

    public function jsonList() {
        $this->data['data'] = $this->tax_application_model->get();
        echo json_encode($this->data);
    }

    public function create() {
        $this->load->library('form_validation');
        $feedback['success'] = false;

        $this->form_validation->set_rules('tax_applied_to', 'Tax applied to', 'required');
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {
            if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
                if ($this->tax_application_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Data successfully updated";
                    //activaty log
                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing tax rate ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the data, please try again";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing tax rate ",$feedback['message']." # ".$tax_applied_to,NULL,"id #".$tax_applied_to);
                }
            } else {
                if ($this->tax_application_model->set()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Data successfully submitted";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Creating tax rate ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                } else {
                    $this->user_model->delete_by_id();
                    $feedback['message'] = "There was a problem saving the data, please try again";

                      $this->helpers->activity_logs($_SESSION['id'],18,"Creating tax rate ",$feedback['message']." # ".$tax_applied_to,NULL,"id #".$tax_applied_to);
                }
            }
        }
        echo json_encode($feedback);
    }

    public function change_status() {
        $message = $this->input->post("status_id")==2?"de":"";
        $response['message'] = "Access denied. You do not have the permission to ".$message."activate, contact the admin for further assistance.";
        $response['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        if (($response['success'] = $this->tax_application_model->change_status_by_id()) === true) {
            $response['message'] = "Record successfully ".$message."activate.";

              $this->helpers->activity_logs($_SESSION['id'],18,"Deleting tax rate ",$feedback['message']." # ".$tax_applied_to,NULL,"id #".$tax_applied_to);
        } else {
            $response['message'] = "You do not have access to ".$message."activate this record";
        }
        //}
        echo json_encode($response);
    }

    public function delete() {
        if (in_array('4', $this->privileges)) {
            $response['success'] = FALSE;
            if ($this->tax_application_model->delete_by_id()) {
                $response['success'] = TRUE;
                $response['message'] = "Data successfully deleted.";

                  $this->helpers->activity_logs($_SESSION['id'],18,"Deleting tax rate ",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
            }
        } else {
            $response['message'] = "You do not have access to delete this record";

             $this->helpers->activity_logs($_SESSION['id'],18,"Deleting tax rate ",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        }
        echo json_encode($response);
    }

}
