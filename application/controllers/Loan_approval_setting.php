<?php

/**
 * Description of Loan_approval_setting
 *
 * @author Eric
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Loan_approval_setting extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("loan_approval_setting_model");
        $this->data['privilege_list'] = $this->rolePrivilege_model->get_user_privileges($module_id = 11, $this->session->userdata('staff_id'));
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['approval_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        }
    }

    public function jsonList() {
        $this->data['data'] = $this->loan_approval_setting_model->get();
        echo json_encode($this->data);
    }

    public function create() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules("min_amount", "Minimum amount", "required|callback__check_amount_set", array("required" => "%s must be entered", "_check_amount_set" => "%s overlaps with an already existing range of amounts"));
        $this->form_validation->set_rules("max_amount", "Maximum amount", "required|callback__check_amount_set", array("required" => "%s must be entered", "_check_amount_set" => "%s overlaps with an already existing range of amounts"));

        $feedback['success'] = false;

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {
            if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) { //editing exsting item
                if ($this->loan_approval_setting_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan Approval setting updated";
                    //activity log 
                       $this->helpers->activity_logs($_SESSION['id'],18,"Editing loan approval ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                } else {
                    $feedback['message'] = "Loan Approval setting could not be updated";

                    $this->helpers->activity_logs($_SESSION['id'],18,"Editing loan approval ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                }
            } else {
                //adding a new setting
                $return_id = $this->loan_approval_setting_model->set();
                if (is_numeric($return_id)) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan Approval setting submitted";

                    $this->helpers->activity_logs($_SESSION['id'],18,"Creating loan approval ",$feedback['message']." # ". $return_id,NULL,"id #". $return_id);
                } else {
                    $feedback['message'] = "There was a problem saving the Loan Approval setting, please contact IT support";
                    
                      $this->helpers->activity_logs($_SESSION['id'],18,"Creating loan approval ",$feedback['message']." # ". $return_id,NULL,"id #". $return_id);

                     
                }
            }
        }
        echo json_encode($feedback);
    }

    function _check_amount_set($amount) {
        $existing_range = $this->loan_approval_setting_model->validate_range_value($amount);
        
        return $existing_range;
    }

    public function view($approval_setting_id) {
        $this->load->model('approving_staff_model');
        $this->load->model('Staff_model');
        $this->load->library(array("form_validation", "helpers"));
        $this->data['registered_staffs'] = $this->Staff_model->get_registeredby("status_id=1");
        $this->data['approval_setting'] = $this->loan_approval_setting_model->get($approval_setting_id);
        //print_r($this->data['approval_setting']); die();
        if (empty($this->data['approval_setting'])) {
            show_404();
        }
        $this->data['staffs'] = $this->approving_staff_model->get_staffs($approval_setting_id);
        $this->data['title'] = 'Amount Range ' . number_format(round($this->data['approval_setting']['min_amount'])) . '-' . number_format(round($this->data['approval_setting']['max_amount']));
        $this->template->title = $this->data['title'];
        $this->data['add_approving_staff_modal'] = $this->load->view('setting/loan/approval_setting/approving_staff_modal', $this->data, true);
        $this->template->content->view('setting/loan/approval_setting/view_approving_staffs', $this->data);
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        // Publish the template
        $this->template->publish();
    }

}
