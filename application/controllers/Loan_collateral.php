<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Loan_collateral extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model("loan_collateral_model");
        $this->load->model("member_collateral_model");
        $this->load->model("user_model");
    }

    public function jsonList() {
        $data['data'] = $this->loan_collateral_model->get('a.status_id=1');
        echo json_encode($data);
    }

    public function index() {
        $this->load->library("num_format_helper");
        $this->load->model("organisation_format_model");
        $this->load->model("RolePrivilege_model");

        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);

        $this->data['title'] = $this->data['sub_title'] = "Loan Collateral";

        $this->template->title = $this->data['title'];

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css", "custom.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->template->content->view('client_loan/loan_collateral/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    #id, user_id, name, type, description, date_created, created_by, date_modified, modified_by

    public function create() {
        $this->load->library('form_validation');

        if ($this->input->post('collaterals')) {
            $this->form_validation->set_rules('client_loan_id', 'Loan ID', 'required');
        } else {
            $this->form_validation->set_rules('description', 'Collateral description', 'required');
            $this->form_validation->set_rules('item_value', 'Item Value', 'required');
            $this->form_validation->set_rules('collateral_type_id', 'Collateral Type', 'required');
            $this->form_validation->set_rules('client_loan_id', 'Loan ID', 'required');
        }

        $feedback = array();
        $feedback['success'] = false;
        $feedback['message'] = "Failed to add collateral, Contact system admin";

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {
            $data = $this->input->post();

            if (isset($data['collaterals'])) {
                // attach collaterals to loan
                $feedback['success'] = array();
                $feedback['message'] = array();

                foreach ($data['collaterals'] as $key => $value) {
                    $value['client_loan_id'] = $data['client_loan_id'];
                    $inserted_id = $this->loan_collateral_model->add($value);

                    if (is_numeric($inserted_id)) {
                        $feedback['success'][$key] = true;
                        $feedback['message'][$key] = "Loan collateral document details successfully Attached";
                        $feedback['document'][$key] = $inserted_id;
                    }
                }
            } else {
                // save new collateral
                if (isset($_FILES['file_name']['name'])) {
                    //need to upload
                    $organisation_id = isset($_SESSION['organisation_id']) ? $_SESSION['organisation_id'] : 'unknown';
                    $location = 'organisation_' . $organisation_id . '/loan_docs/collateral/';

                    //set file upload settings 
                    $config['upload_path']  = APPPATH . "../uploads/$location/";
                    $config['file_name']    = $_FILES['file_name']['name'];
                    $config['allowed_types'] = 'docx|doc|gif|jpg|png|pdf';
                    $config['max_size'] = 2000;
                    $config['max_width'] = 0;
                    $config['max_height'] = 0;
                    $config['remove_spaces'] = false;
                    $config['overwrite'] = true;
                    $config['encrypt_name'] = false;

                    $document_name = $config['file_name'];
                    $this->load->library('upload', $config);

                    if (!$this->upload->do_upload('file_name')) {
                        $error =  $this->upload->display_errors();
                        $feedback['message'] = $error;
                    } else {
                        $data['file_name'] = $document_name;
                    }
                }

                unset($data['client_loan_id']);

                $inserted_id = $this->member_collateral_model->add(false, $data);

                if (is_numeric($inserted_id)) {
                    $insert_data['member_collateral_id'] = $inserted_id;
                    $insert_data['item_value'] = $data['item_value'];
                    $insert_data['client_loan_id'] = $this->input->post('client_loan_id');

                    $inserted_id = $this->loan_collateral_model->add($insert_data);
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan collateral document details successfully added";
                    $feedback['document'] = $inserted_id;
                }
            }
        }
        echo json_encode($feedback);
    }

    public function delete() {
        $feedback['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $feedback['success'] = FALSE;
        if ($this->loan_collateral_model->delete_by_id() === true) {
            $feedback['success'] = TRUE;
            $feedback['message'] = "Collateral attachment successfully deleted.";
        }
        echo json_encode($feedback);
    }
}
