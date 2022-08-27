<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("contact_model");
        $this->load->model("member_model");
        $this->load->library('form_validation');
        $this->load->model('user_model');
        $this->load->model("organisation_format_model");
        $this->load->model('District_model');
        $this->load->library("helpers");
    }

    public function jsonList() {
        $data['data'] = $this->member_model->get_member();
        $all_members = $this->member_model->get();
        $data['pagination']['more'] = (count($all_members) > ($this->input->post("page") * 50));
        echo json_encode($data);
    }

    public function jsonList2() {
        $data['draw'] = intval($this->input->post('draw'));
        $data['data'] = $this->member_model->get_dtable_format();
        $filtered_records_cnt = $this->member_model->get_found_rows();
        $all_data = $this->member_model->get();
        //total records
        $data['recordsTotal'] = count($all_data);
        $data['recordsFiltered'] = current($filtered_records_cnt);
        echo json_encode($data);
    }

    public function index() {
           $id=$_SESSION['member_id'];
            $this->data['user'] = $this->member_model->get_member($id);
            if (empty($this->data['user'])) {
                redirect("my404");
            }
        $this->load->model('Staff_model');
        $this->load->model('address_model');
        $this->load->model('employment_model');
        $this->load->model("document_model");
        $this->load->model('miscellaneous_model');
        $this->load->model("user_doc_type_model");
        $this->load->model("Signature_model");
        $this->load->model('user_expense_type_model');
        $this->load->model('dashboard_model');
        
        $this->data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        
        $this->data['user_doc_types'] = $this->user_doc_type_model->get_doc_type();
        $this->data['contact_types'] = $this->contact_model->get_contact_type();
        $this->data['address_types'] = $this->address_model->get_address_types();
        $this->data['relationship_types'] = $this->miscellaneous_model->get_relationship_type();
        $this->data['nature_of_employment'] = $this->employment_model->get_nature_of_employment();
        $this->data['districts'] = $this->District_model->get_districts();
        //$this->data['organisation'] = $this->organisation_format_model->get_account_format();

        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['users'] = $this->member_model->get_member();
      
        $this->data['staffs'] = $this->Staff_model->get_registeredby("status_id=1");
        $this->data['marital_statuses'] = $this->miscellaneous_model->get_marital_status_options();
        
        $this->data['user_signature'] = $this->Signature_model->get(["fms_user_signatures.user_id" => $this->data['user']['user_id']]);

        $this->data['title'] = $this->data['sub_title'] = $this->data['user']['firstname'] . " " . $this->data['user']['lastname'] . " " . $this->data['user']['othernames']. " Profile ";
        $this->data['type'] = $this->data['sub_type'] = 'Member';

        $this->template->title = $this->data['title'];

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/cropping/croppie.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js","plugins/steps/jquery.steps.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/cropping/croppie.css", "plugins/daterangepicker/daterangepicker-bs3.css","plugins/steps/jquery.steps.css");

        $this->data['staff_list'] = $this->Staff_model->get_registeredby();
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        // Load a view in the content partial
        $this->data['profile_nav'] = $this->load->view('client/profile/profile_nav', $this->data, TRUE);

        $this->template->content->view('client/profile/index', $this->data);
        // Publish the template
        $this->template->publish();
    }
   

    function _check_phone_number($phone_number) {
        $existing_number = $this->contact_model->validate_contact($phone_number);
        
        return $existing_number;
    }
    public function delete() {
        $response['success'] = FALSE;
        // if (isset($_SESSION['role']) && isset($_SESSION['role']) == 1) {
        if (($response['success'] = $this->member_model->temporary_delete($this->input->post('id'))) === true) {
            $response['message'] = "Member Details successfully deleted";
        }
        echo json_encode($response);
    }

    public function change_status() {
        $response['success'] = false;
        $response['message'] = "Member not deactivated.";
        if ($this->member_model->change_status_by_id($this->input->post('id'))) {
            $response['success'] = true;
            $response['message'] = "Member successfully deactivated.";
        }
        echo json_encode($response);
    }

    function generate_client_no() {
        $this->data['member_no_format'] = $this->organisation_format_model->get_member_format();
        $org_id = $this->data['member_no_format']['id'];
        $org = $this->data['member_no_format']['client_format'];
        $counter = $this->data['member_no_format']['client_counter'];
        $letter = $this->data['member_no_format']['client_letter'];

        $initial = $this->data['member_no_format']['org_initial'];
        if ($org == '1') {
            if ($counter == 99999999) {
                $letter++;
                $counter = 0;
            }
            $client_number = $initial . sprintf("%08d", $counter + 1) . $letter;
        } else if ($org == '2') {
            if ($counter == 99999999) {
                $letter++;
                $counter = 0;
            }
            $client_number = $letter . sprintf("%08d", $counter + 1) . $initial;
        } else if ($org == '3') {
            $client_number = $initial . sprintf("%08d", $counter + 1);
        } else {
            $client_number = false;
        }
        $this->db->where('id', $org_id);
        $upd = $this->db->update('fms_organisation', ["client_counter" => $counter + 1, "client_letter" => $letter]);
        return $client_number;
    }

}
