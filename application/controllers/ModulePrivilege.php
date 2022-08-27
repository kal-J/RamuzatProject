<?php
class ModulePrivilege extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('Modules_model');
        $this->load->model('ModulePrivilege_model');
        $this->load->library(array("form_validation", "helpers"));
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 11, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 11, $_SESSION['organisation_id']);
        if(empty($this->data['module_access'])){
            redirect('my404');
        } else {
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['privileges'] = array_column($this->data['privilege_list'], "privilege_code");
        }
        $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
        if(empty($this->data['fiscal_active'])){
            redirect('dashboard');
        }
        }
      
    }

    public function view($id) {
        //error_reporting(0);
        $this->load->library("helpers");

        $this->data['title'] = $this->data['sub_title'] = 'Modules and Privileges';
        $this->template->title = $this->data['title'];
        $this->data['module'] = $this->Modules_model->get($id);
        // Load a view in the content partial

        $this->template->content->view('setting/privilege/module_privilege/view_details', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function jsonList() {
        $where = FALSE;
        if ($this->input->post('organisation_id') !== NULL) {
            $where = "organisation_id = " . $this->input->post('organisation_id');
        }
        $jsonlist['data'] = $this->Modules_model->get($where);
        echo json_encode($jsonlist);
    }

    public function jsonList2() {
        $where = FALSE;
        if ($this->input->post('organisation_id') !== NULL) {
            $where = "organisation_id = " . $this->input->post('organisation_id');
        }
        $data['data'] = $this->ModulePrivilege_model->get($this->input->post("module_id"));
        echo json_encode($data);
    }

    public function create() {
        $this->form_validation->set_rules('privilege_code', 'Privilege', array('required'), array('required' => 'Please assign atleast one %s'));
        $this->form_validation->set_rules('module_id', 'Module', array('required'), array('required' => 'Please select a %s'));
        $this->form_validation->set_rules('description', 'Narative', array('required'), array('required' => 'Please provide a short  %s'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->ModulePrivilege_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "privileges successfully updated";
                    $feedback['privilege'] = $this->ModulePrivilege_model->get($_POST['id']);
                } else {
                    $feedback['message'] = "There was a problem updating the assigning privileges";
                }
            } else {
                $privilege_id = $this->ModulePrivilege_model->set();
                if ($privilege_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "privilege successfully assigned";
                } else {
                    $feedback['message'] = "There was a problem assigning privileges";
                }
            }
        }
        echo json_encode($feedback);
    }

    public function delete() {
        $response['message'] = "Module privilege could not be deleted, contact support.";
        $response['success'] = FALSE;
        if ($this->ModulePrivilege_model->delete($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] = "Module privilege successfully deleted.";
        }
        echo json_encode($response);
    }
    
    public function change_status() {
        $msg = $this->input->post('status_id')==1?"":"de";
        $response['message'] = "Module privilege could not be $msg activated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->ModulePrivilege_model->deactivate($this->input->post('id'))) {
            $response['message'] = "Module privilege has successfully been $msg activated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }
}
