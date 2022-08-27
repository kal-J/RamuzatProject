<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tax_rate extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        
        $this->load->model("tax_rate_model");
    }

    public function jsonList() {
        $this->data['data'] = $this->tax_rate_model->get();
        echo json_encode($this->data);
    }

    public function create() {

        $this->form_validation->set_rules('tax_rate_source_id', 'Tax Rate Source', array('required', 'numeric'), array('required' => '%s must be selected', 'numeric' => '%s must be selected'));
        $this->form_validation->set_rules('rate', 'Rate', array('required', 'greater_than[0]', 'less_than[101]'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('start_date', 'Start Date', array("required", "valid_date[d-m-Y]"), array('required' => '%s must be selected', 'valid_date' => 'Date format must be like so dd-mm-yyyy'));
        $this->input->post('modified_by');

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors("<p>", "</p>");
        } else {
            if (is_numeric($this->input->post('id'))) {
                if ($this->tax_rate_model->set()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Tax rate details successfully updated";
                    //activity log 

                      $this->helpers->activity_logs($_SESSION['id'],18,"Editing tax rate ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the tax rate";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing tax rate ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                }
            } else {
                $tax_rate_id = $this->tax_rate_model->set();
                if (is_numeric($tax_rate_id)) {
                    //now we should get the previous rate, so we make it inactive
                    $old_tax_rates = $this->tax_rate_model->get("tax_rate_source_id=" . $this->input->post('tax_rate_source_id'));
                    if (!empty($old_tax_rates)) {
                        //then lets set the previous rate to inactive
                        $this->tax_rate_model->deactivate($old_tax_rates[count($old_tax_rates) - 1]['id']);
                    }
                    $feedback['success'] = true;
                    $feedback['message'] = "Tax rate details successfully saved";
                    $feedback['loan'] = $tax_rate_id;

                     $this->helpers->activity_logs($_SESSION['id'],18,"Creating tax rate ",$feedback['message']." # ".$tax_rate_id,NULL,"id #".$tax_rate_id);
                } else {
                    $feedback['message'] = "There was a problem saving the tax rate";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Creating tax rate ",$feedback['message']." # ".$tax_rate_id,NULL,"id #".$tax_rate_id);
                }
            }
        }
        echo json_encode($feedback);
    }

    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;

         $this->helpers->activity_logs($_SESSION['id'],18,"Deleting tax rate ",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
         
        // if (isset($_SESSION['role']) && isset($_SESSION['role']) == 1) {
        if (($response['success'] = $this->tax_rate_model->delete()) === true) {
            $response['message'] = "Tax rate details successfully deleted";

         $this->helpers->activity_logs($_SESSION['id'],18,"Deleting tax rate ",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        }
       
        // }
        echo json_encode($response);
    }

}
