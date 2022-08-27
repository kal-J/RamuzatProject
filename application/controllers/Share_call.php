<?php

/**
 * Description of Share Fee
 *
 * @author Melchisedec
 */
class Share_call extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('share_call_model');
        $this->load->library("helpers"); 
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id=11,$this->session->userdata('staff_id'));
        if(empty($this->data['privilege_list'])){
            redirect('my404');
        } else {
            $this->data['privileges'] =array_column($this->data['privilege_list'],"privilege_code");
        }       
    }

    public function jsonList() {
        $where = FALSE;
        if ($this->input->post('organisation_id') !== NULL) {
            $where = "organisation_id = " . $this->input->post('organisation_id');
        }
        $this->data['data'] = $this->share_call_model->get();
        echo json_encode($this->data);
    }
    public function active_share_calls() {
        $data['sharecall'] = $this->share_call_model->get_share_calls($this->input->post('new_application_id'));
        echo json_encode($data);
    }

    public function create() {

        $this->form_validation->set_rules('call_name', 'Call name', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));
      $this->form_validation->set_rules("percentage", "Percentage", "required|callback_check_percentage_total", array("required" => "%s must be entered", "check_percentage_total" => "%s total exceeds 100"));


        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->share_call_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Share call details successfully updated";
                    $feedback['loan_fee'] = $this->share_call_model->get($_POST['id']);
                } else {
                    $feedback['message'] = "There was a problem updating Share call  details";
                }
            } else {
                $loan_fee_id = $this->share_call_model->set();
                if ($loan_fee_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Share call details successfully saved";
                } else {
                    $feedback['message'] = "There was a problem saving the Share call data";
                }
            }
        }
        echo json_encode($feedback);
    }
    function check_percentage_total($percentage) {
        $data['percentage_total'] = $this->share_call_model->validate_percentage($percentage);
        $total_percentage=$data['percentage_total']['total_percentage']+$percentage;
        if($total_percentage >100){
        return FALSE;
        }else{
        return TRUE;
        }
    }
    
    public function activate() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
     //   if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
       
        $this->data['first_call'] = $this->share_call_model->get_first_calls();
        if(empty($this->data['first_call'])){
            $this->data['message'] = $this->share_call_model->change_first_call();
            if ($this->data['message'] === true) {
                $this->data['success'] = TRUE;
                $this->data['message'] = "Successfully activated as First Call!";
            }
        } else {
            $this->data['message'] = "Please Deactivate the current First Call, there can only be one First Call!";
        }
     //   }
        echo json_encode($this->data);
    }
    public function inactivate() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
            $this->data['message'] = $this->share_call_model->change_first_call();
            if ($this->data['message'] === true) {
                $this->data['success'] = TRUE;
                $this->data['message'] = "First Call now Deactivated!";
            }
        echo json_encode($this->data);
    }

    public function change_status() {
        $response['message'] = "Share call could not be deactivated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->share_call_model->change_status()) {
            $response['message'] = "Share call successfully deactivated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }
}
