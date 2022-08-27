<?php

/**
 * Description of Branch
 *
 * @author allan
 */
class Branch extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('branch_model');
        $this->load->model('department_model');
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

    public function index() {
        $this->data['title'] = $this->data['sub_title'] = 'Sacco Branches';

        $this->template->title = $this->data['title'];

        // Load a view in the content partial
        $this->data['add_modal'] = $this->load->view('branch/add_modal', $this->data, TRUE);
        $this->template->content->view('branch/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function jsonList() {
        $where = FALSE;
        if ($this->input->post('organisation_id') !== NULL) {
            $where = "organisation_id = " . $this->input->post('organisation_id');
        }
        $branches=$this->branch_model->get($where);
        $this->data['data'] = $this->branch_department($branches);
        echo json_encode($this->data);
    }

    public function branch_department($branches){
        $array = array();
        if (!empty($branches)) {
            foreach ($branches as $key => $branches_value) {
                $branch_id = $branches_value['id'];
                $inner_array = [
                    'id' => $branches_value['id'],
                    'branch_number' => $branches_value['branch_number'],
                    'branch_name' => $branches_value['branch_name'],
                    'office_phone' => $branches_value['office_phone'],
                    'email_address' => $branches_value['email_address'],
                    'physical_address' => $branches_value['physical_address'],
                    'postal_address' => $branches_value['postal_address'],
                    'departments' => $this->department_model->get("branch_id=$branch_id")
                ];
                $array[] = $inner_array;
            }
        }
        return $array; 
    }

    public function create() {

        $this->form_validation->set_rules('branch_number', 'Code', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('branch_name', 'Branch name', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('office_phone', 'Office phone contact', array('required', 'valid_phone_int'), array('required' => '%s must be entered', 'valid_phone_int' => '%s should start with +256 or 0'));
        $this->form_validation->set_rules('email_address', 'office email contact', array('required', 'valid_email'), array('required' => '%s must be entered', 'valid_email' => 'Enter correct %s'));
       //$this->form_validation->set_rules('physical_address', 'Physical address', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('email_address', 'Postal address', array('required'), array('required' => '%s must be entered'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->branch_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Branch details successfully updated";
                    $feedback['branch'] = $this->branch_model->get($_POST['id']);

                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing a branch",$feedback['message']." # ".$_POST['id'],NULL," # ".$_POST['id'],NULL);
                } else {
                    $feedback['message'] = "There was a problem updating the branch details";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing a branch",$feedback['message']." # ".$_POST['id'],NULL," # ".$_POST['id'],NULL);

                }
            } else {
                $branch_id = $this->branch_model->set();
                if ($branch_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Branch details successfully saved";
                      $this->helpers->activity_logs($_SESSION['id'],18,"Creating a branch",$feedback['message']." # ".$_POST['branch_number'],NULL," # ".$_POST['branch_number'],NULL);
                } else {
                    $feedback['message'] = "There was a problem saving the branch data";

                      $this->helpers->activity_logs($_SESSION['branch_number'],18,"Creating a branch",$feedback['message']."#".$_POST['branch_number'],NULL," # ".$_POST['branch_number'],NULL);
                }
            }
        }
        echo json_encode($feedback);
    }

    public function view($branch_id) {
        $this->data['branch'] = $this->branch_model->get($branch_id);
        if (empty($this->data['branch'])) {
            show_404();
        }
        $this->load->library(array("form_validation", "helpers"));
        //$this->load->model('staff_model');

        $this->data['title'] = $this->data['branch']['branch_name'];
        $this->data['sub_title'] = $this->data['branch']['branch_number'];

        $this->template->title = $this->data['title'];
        
        $this->data['add_dept_modal'] = $this->load->view('department/add_modal', $this->data, true);
        $this->data['add_branch_modal'] = $this->load->view('branch/add_modal', $this->data, TRUE);
        $this->template->content->view('branch/view', $this->data);
        // Publish the template
        $this->template->publish();
    }
    public function change_status() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
       

        if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            $this->data['message'] = $this->branch_model->change_status();
            if ($this->data['message'] === true) {
                $this->data['success'] = TRUE;
            }
        }
        echo json_encode($this->data);
    }
    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
           $this->helpers->activity_logs($_SESSION['id'],18,"Deleting record for branch",$response['message']." # ".$this->input->post('id'),NULL,"Record ID:".$this->input->post('id'));

        if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            if (($response['success'] = $this->branch_model->delete($this->input->post('id'))) === true) {
                $response['message'] = "Branch Details successfully deleted";

                 $this->helpers->activity_logs($_SESSION['id'],18,"Deleting record for branch",$response['message']." # ".$this->input->post('id'),NULL,"Record ID:".$this->input->post('id'));
            }
        }
        echo json_encode($response);
    }
}
