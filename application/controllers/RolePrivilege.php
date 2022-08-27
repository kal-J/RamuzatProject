<?php

class RolePrivilege extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('RolePrivilege_model');
        $this->load->model('Modules_model');
        $this->load->model('member_model');
        $this->load->model('ModulePrivilege_model');
        $this->load->model('role_model');
        $this->load->library(array("form_validation", "helpers"));
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 7, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 7, $_SESSION['organisation_id']);
        if(empty($this->data['module_access'])){
            redirect('my404');
        } else {
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['rolemodule_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        }
        $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
        if(empty($this->data['fiscal_active'])){
            redirect('dashboard');
        }
        }
    }

    public function index() {
        //error_reporting(0);
        $this->data['title'] = $this->data['sub_title'] = 'Sacco roles';

        $this->template->title = $this->data['title'];

        // Load a view in the content partial
        $this->data['add_modal'] = $this->load->view('role/add_modal', $this->data, TRUE);
        $this->template->content->view('role/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function jsonList() {
        $this->data['data'] = $this->RolePrivilege_model->get();
        echo json_encode($this->data);
    }

    public function view($id) {
        //error_reporting(0);
        $this->data['title'] = $this->data['sub_title'] = 'Roles and Privileges';
        $this->template->title = $this->data['title'];
        $this->data['modules'] = $this->Modules_model->get_modules_privileges($status_id = 1);

        $this->data['role'] = $this->role_model->get($id);
        // Load a view in the content partial
        $neededcss = array("plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css");
        $neededjs = array();
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->data['all_privileges'] = $this->RolePrivilege_model->get($id);
        $this->template->content->view('setting/role/view_details', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function create() {
        if (count($this->input->post('role_privilege[][privilege_id]')) == 0) {
            $this->form_validation->set_rules('role_privilege[0_0][privilege_id]', 'Privilege', array('required'), array('required' => ' Select atleast one %s'));

            $this->form_validation->set_rules('role_id', 'Role', array('required'), array('required' => '  %s Role can not be identified'));
        } else {
            $this->form_validation->set_rules('role_id', 'Role', array('required'), array('required' => '  %s Role can not be identified'));
        }
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $role_id = $this->RolePrivilege_model->set();
            if ($role_id) {
                $feedback['success'] = true;
                $feedback['all_privileges'] = $this->RolePrivilege_model->get($this->input->post('role_id'));
                $feedback['message'] = "Privilege successfully saved";
            } else {
                $feedback['message'] = "There was a problem saving the Privilege ";
            }
        }
        echo json_encode($feedback);
    }

    public function delete() {
        $response['message'] = "Role could not be deleted, contact IT support.";
        $response['success'] = FALSE;
        if ($this->RolePrivilege_model->delete_by_id($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] = "Role successfully deleted.";
        }
        echo json_encode($response);
    }

    public function change_status() {
        $response['message'] = "Role could not be deactivated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->RolePrivilege_model->change_status_by_id($this->input->post('id'))) {
            $response['message'] = "Role has successfully been deactivated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }

  
}
