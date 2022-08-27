<?php

class Supplier extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 8, $this->session->userdata('staff_id'));
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['privileges'] = array_column($this->data['privilege_list'], "privilege_code");
        }
        $this->load->model('supplier_model');
    }

    public function index() {
        //$this->load->model('country_model');

        $this->data['title'] = $this->data['sub_title'] = "Suppliers/Vendors";
        //$data['countries'] = $this->country_model->get_country();
        
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view("accounts/supplier/index", $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function jsonlist() {
        $suppliers['data'] = $this->supplier_model->get();
        echo json_encode($suppliers);
    }

    public function view($supplier_id = NULL) {
        if (!$supplier_id) {
            redirect('supplier');
        }
        $this->data['title'] = 'Supplier';
        $this->data['supplier'] = $this->supplier_model->get($supplier_id);

        if (empty($this->data['supplier'])) {

            $data['sub_title'] = 'No data';
            $data['message'] = 'The supplier record was not found';

            $this->load->view('templates/header', $this->data);
            $this->load->view('templates/404', $this->data);
            $this->load->view('templates/footer');
        } else {
            $this->data['title'] = $this->data['sub_title'] = $this->data['supplier']['supplier_names'];

            // Load a view in the content partial
            $this->template->title = $this->data['title'];
            $this->template->content->view('accounts/supplier/view', $this->data);
            // Publish the template
            $this->template->publish();
        }
    }

    public function create() {
        //$this->form_validation->set_rules('country_id', 'Country', 'required', array('required' => '% was not selected'));
        $this->form_validation->set_rules('tin', 'TIN', 'max_length[30]', array('max_length' => '%s must not exceed 30 characters'));
        $this->form_validation->set_rules('phone1', 'Supplier Phone Number', 'valid_phone_int', array('valid_phone_int' => 'Invalid %s'));
        $this->form_validation->set_rules('phone2', 'Supplier second phone number', 'valid_phone_int', array('valid_phone_int' => 'Invalid %s'));
        $this->form_validation->set_rules('supplier_names', 'Supplier name', 'required', array('required' => '%s missing'));

        $data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $data['success'] = FALSE;
        if (in_array('3', $this->data['privileges'])) {
            if ($this->form_validation->run() === FALSE) {
                $data['message'] = validation_errors('<li>', '</li>');
            } else {
                if ($this->input->post("id")!==NULL && is_numeric($this->input->post("id"))  && in_array('3', $this->data['privileges'])) {
                    if ($this->supplier_model->set()) {
                        $data['success'] = TRUE;
                        $data['message'] = "Supplier details successfully updated";
                    } else {
                        $data['message'] = "There was a problem updating the supplier data";
                    }
                } else {
                    if(in_array('1', $this->data['privileges'])){
                        //inserting a new record 
                        if (($supplier_id = $this->supplier_model->set())) {
                            $data['success'] = TRUE;
                            $data['supplier'] = $this->supplier_model->get($supplier_id);
                            $data['message'] = "Supplier details successfully saved";
                        } else {
                            $data['message'] = "There was a problem saving the supplier data";
                        }
                    }
                }
            }
        }
        echo json_encode($data);
    }
    
    public function change_status() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
        if (in_array('7', $this->data['privileges'])) {
            $this->data['message'] = $this->supplier_model->change_status();
            if ($this->data['message'] === true) {
                $this->data['success'] = TRUE;
            }
        }
        echo json_encode($this->data);
    }

    public function delete($supplier_id = NULL) {
        $data['success'] = FALSE;
        $data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        //if user not admin,deny operation
        if (in_array('4', $this->data['privileges'])) {
            if ($this->input->post('id') != NULL) {
                if ($this->supplier_model->delete($this->input->post('id'))) {
                    $data['success'] = TRUE;
                    $data['message'] = 'Supplier details successfully deleted';
                } else {
                    $data['message'] = 'The supplier data could not be deleted. Please try again or inform administrator';
                }
            } else {
                $data['message'] = 'Invalid request. Please try again';
            }
        }
        echo json_encode($data);
    }

}
