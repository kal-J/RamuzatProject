<?php

/**
 * Description of approving_staff
 *
 * @author Eric
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Approving_staff extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("approving_staff_model");
        $this->load->model("loan_approval_setting_model");
        $this->load->model("Staff_model");
    }

    public function create() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('rank', 'Staff Rank', 'required');
        $this->form_validation->set_rules('staff_id', 'Staff Name', 'required');

        $feedback['success'] = false;
        $approval_setting_id = $this->input->post('approval_setting_id');
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {
            if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) { //editing existing item
                if ($this->approving_staff_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Approving staff updated";
                    $feedback['approval_setting'] = $this->loan_approval_setting_model->get($approval_setting_id);
                    $feedback['staffs'] = $this->approving_staff_model->get_staffs($approval_setting_id);

                    $this->helpers->activity_logs($_SESSION['id'],1,"Approving staff",$feedback['message'],NULL,$this->input->post('id'));
            
        
                } else {
                    $feedback['message'] = "Approving staff could not be updated";

                      $this->helpers->activity_logs($_SESSION['id'],1,"Approving staff",$feedback['message'],NULL,$this->input->post('id'));
                }
            } else {
                //adding a new staff
                $rows= $this->approving_staff_model->already_added_staff($approval_setting_id, $_POST['staff_id']);
                if ($rows>0) {
                    $feedback['message'] = "This staff is already added to this setting";
                      $this->helpers->activity_logs($_SESSION['id'],1,"Approving staff",$feedback['message'],NULL,$this->input->post('id'));
                }else{
                    $return_id = $this->approving_staff_model->set();
                    if (is_numeric($return_id)) {
                        $feedback['success'] = true;
                        $feedback['message'] = "Approving staff submitted";
                        $feedback['approval_setting'] = $this->loan_approval_setting_model->get($approval_setting_id);
                        $feedback['staffs'] = $this->approving_staff_model->get_staffs($approval_setting_id);

                          $this->helpers->activity_logs($_SESSION['id'],1,"Approving staff",$feedback['message'],NULL,$this->input->post('id'));
                    } else {
                        $feedback['message'] = "There was a problem saving the Approving staff, please contact IT support";
                          $this->helpers->activity_logs($_SESSION['id'],1,"Approving staff",$feedback['message'],NULL,$this->input->post('id'));
                    }
                }
            }
        }
        echo json_encode($feedback);
    }

    public function change_status() {
        $approval_setting_id=$this->input->post('approval_setting_id');
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Approving staff could not be ".$msg."activated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->approving_staff_model->deactivate($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] = "Approving staff  has successfully been ".$msg."activated. Please reload the page";
            if (!empty($approval_setting_id)) {
                $response['approval_setting'] = $this->loan_approval_setting_model->get($approval_setting_id);
                $response['staffs'] = $this->approving_staff_model->get_staffs($approval_setting_id);
            }
            echo json_encode($response);
        }
    }

}
