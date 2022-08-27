<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class client_loan_guarantor extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('loan_guarantor_model');
    }

    public function jsonList() {
        $where = FALSE;
        if($this->input->post('status_id')!==NULL){
            $where = "a.status_id = ".$this->input->post('status_id');
        }
        if($this->input->post('client_loan_id')!==NULL){
            $where = ($where?$where . " AND ":"")." a.client_loan_id = ".$this->input->post('client_loan_id');
        }

        $data['data'] = $this->loan_guarantor_model->get( $where );
        echo json_encode($data);
    }

    public function create() {

        $this->form_validation->set_rules('amount_locked', 'Locked amount', array('required'));
        $this->form_validation->set_rules('relationship_type_id', 'Relatiionship', array('required'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->loan_guarantor_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan guarantor successfully updated";
                    $feedback['guarantors'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + 
                    ifnull( transfer ,0) +ifnull(charges, 0) + ifnull( amount_locked, 0) ) > 0 and j.state_id = 7");
                    // activity log 

                    $this->helpers->activity_logs($_SESSION['id'],4,"Editing guarantor ",$feedback['message']." -# ".NULL,NULL,NULL);
                
                } else {
                    $feedback['message'] = "There was a problem updating the Loan guarantor details";
                }
            } else {
                $client_loan_id = $this->loan_guarantor_model->set();
                if ($client_loan_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan guarantor details successfully saved";
                    $feedback['guarantors'] =  $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + 
                    ifnull( transfer ,0) +ifnull(charges, 0)+ ifnull( amount_locked, 0) ) > 0 and j.state_id = 7");

                      $this->helpers->activity_logs($_SESSION['id'],4,"Creating guarantor ","Added guarantor ",NULL,NULL,NULL);
                } else {
                    $feedback['message'] = "There was a problem saving the Loan guarantor data";

                      $this->helpers->activity_logs($_SESSION['id'],4,"Editing guarantor ","Edited guarantor details",NULL,NULL,NULL);
                }
            }
        }
        echo json_encode($feedback);
    }

    public function change_status() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        $this->data['message'] = $this->loan_guarantor_model->change_status_by_id();
        if ($this->data['message'] === true) {
            $this->data['success'] = TRUE;
            $this->data['message'] = "Data successfully DEACTIVATED.";
        }
        //}
        echo json_encode($this->data);
    }

    public function delete() {
        $response['message'] = "Data could not be deleted, contact support.";
        $response['success'] = FALSE;
        if ($this->loan_guarantor_model->delete_by_id()) {
            $response['success'] = TRUE;
            $response['message'] = "Data successfully deleted.";
        }
        echo json_encode($response);
    }

}
