<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Organisation_format extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model("organisation_format_model");
        $this->load->library("helpers");
    }

    public function set_num_format() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to change this record";
        $response['success'] = FALSE;
        $this->form_validation->set_rules('format_cat', 'Format category', ['required'], ['required' => "Incorrect selection, please click the immediate 'Save' button"]);
        $this->form_validation->set_rules('format_type', 'manual or dynamic option', ['required'], ['required' => 'Select either %s']);
        $this->form_validation->set_rules('formats', 'Formats', ['callback__required_options']);

        if ($this->form_validation->run() === FALSE) {
            $response['message'] = validation_errors();
        } else {
            $this->data['format_types'] = $this->Organisation_format_model->get_format_types($org_id);
            
            $response['success'] = $this->organisation_format_model->set_format_type();
            if ($response['success'] === true) {
                //then insert the line formats if the dynamic option was selected
                if($this->input->post("format_type") == 2){
                    $this->load->model("num_format_model");
                    $this->num_format_model->set($_SESSION['organisation_id']);
                }
                
                $response['message'] = "Format successfully set";
            }else{
                $response['message'] = "Format could not be set";
            }
        }
        echo json_encode($response);
    }
    public function set_formats() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to change this record";
        $response['success'] = FALSE;
        // if($this->input->post('loan_format')=='1'){
        $this->form_validation->set_rules('account_format', 'Savings Account No. Format', ['trim'], ['required' => "%s Must Be Entered"]);
 
      //}


        $this->form_validation->set_rules('fixed_dep_format', 'Fixed Deposit Account No. Format', ['trim'], ['required' => "%s Must Be Entered"]);
        $this->form_validation->set_rules('loan_format', 'Loan Account No. Format', ['trim'], ['required' => "%s Must Be Entered"]);
        $this->form_validation->set_rules('client_format', 'Client Number Format', ['trim'], ['required' => "%s Must Be Entered"]);
        $this->form_validation->set_rules('staff_format', 'Staff Number Format', ['trim'], ['required' => "%s Must Be Entered"]);
        $this->form_validation->set_rules('group_format', 'Group Number Format', ['trim'], ['required' => "%s Must Be Entered"]);
        $this->form_validation->set_rules('share_format', 'Share Account No. Format', ['trim'], ['required' => "%s Must Be Entered"]);
        $this->form_validation->set_rules('partner_format', 'Partner Number Format', ['trim'], ['required' => "%s Must Be Entered"]);

        if ($this->form_validation->run() === FALSE) {
            $response['message'] = validation_errors();
        } else {
            $response['success'] = $this->organisation_format_model->set_formats();
            if ($response['success'] === true) {
                $response['message'] = "Formats successfully saved";
            }else{
                $response['message'] = "Formats could not be saved, please contact the administrator";
            }
        }
        echo json_encode($response);
    }

    public function _required_options($formats = []) {
        $format_options = $this->input->post("formats");
        $format_type = $this->input->post("format_type");
        $incrementing_nums = 0; //incrementing numbers
        if ($format_type == 2) {
            foreach ($format_options as $key => $format_option) {
                $idx = $key+1;
                if (!isset($format_option['section_format']) || (isset($format_option['section_format']) && $format_option['section_format'] == '')) {
                    $this->form_validation->set_message("_required_options", "You must select one option on line $idx");
                    return false;
                }
                if (in_array($format_option['section_format'], [1, 2]) && isset($format_option['section_start']) && trim($format_option['section_start']) == '') {
                    $this->form_validation->set_message("_required_options", "You must set the start characters for option No. $idx");
                    return false;
                }
                if ($format_option['section_format'] == 3) {
                    $incrementing_nums++;
                    if (!isset($format_option['section_start']) || (isset($format_option['section_start']) && !is_numeric($format_option['section_start']))) {
                        $this->form_validation->set_message("_required_options", "You must set the starting number for option No. $idx");
                        return false;
                    }
                    if (!isset($format_option['section_length']) || (isset($format_option['section_length']) && trim($format_option['section_length']) == '')) {
                        $this->form_validation->set_message("_required_options", "You must set the number of digits for option No. $idx");
                        return false;
                    }
                }
            }
            if ($incrementing_nums == 0) {
                $this->form_validation->set_message("_required_options", "You must select one option of incrementing numbers");
                return false;
            }
        }
        return true;
    }

    function get_num_format() {
        $this->load->model("num_format_model");
        $response['num_formats'] = $this->num_format_model->get("org_id=".$_SESSION['organisation_id']);
        echo json_encode($response);
    }

}
