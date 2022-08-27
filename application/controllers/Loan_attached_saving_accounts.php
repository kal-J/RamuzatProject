<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Loan_attached_saving_accounts extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model("loan_attached_saving_accounts_model");
    }

    public function jsonList() {
        $this->data['data'] = $this->loan_attached_saving_accounts_model->get();
        echo json_encode($this->data);
    }

    public function create() {
        $savingAccs = $this->input->post('savingAccs');
        if (empty($savingAccs)) {
            $feedback['success'] = false;
            $feedback['message'] = "All fields are required";
        } else {
            if ($this->loan_attached_saving_accounts_model->set()) {
                $this->load->model('loan_guarantor_model');
                $feedback['success'] = true;
                $feedback['message'] = "Account successfully created";
                $feedback['savings_accs'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + 
                ifnull( transfer ,0)  +ifnull(charges, 0)+ ifnull( amount_locked, 0) ) > 0 and j.state_id = 7 
                AND member_id =(SELECT member_id FROM fms_client_loan WHERE id='".$_POST['loan_id']."')  AND a.id NOT IN (SELECT saving_account_id from 
                fms_loan_attached_saving_accounts WHERE loan_id = '".$_POST['loan_id']."' )");
            } else {
                $feedback['success'] = false;
                $feedback['message'] = "There was a problem while saving";
            }
        }
        echo json_encode($feedback);
    }

    public function change_status() {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Account data could not be $msg activated, contact IT support.";
        $response['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            if ($this->loan_attached_saving_accounts_model->change_status_by_id($this->input->post('id'))) {
                $response['message'] = "Account data has successfully been $msg Deactivated.";
                $response['success'] = TRUE;
                echo json_encode($response);
            }
        //}
    }

    public function delete() {
            $response['success'] = FALSE;
            if ($this->loan_attached_saving_accounts_model->delete()) {
                $response['success'] = TRUE;
                $response['message'] = "Data successfully deleted.";
            }
        echo json_encode($response);
    }

}

