<?php

/**
 * Description of Department
 *
 * @author allan
 */
class Department extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('department_model');
    }

    public function index() {
        //error_reporting(0);
        $this->load->library("helpers");
        $this->data['title'] = $this->data['sub_title'] = 'Branch Departments';
        $this->template->title = $this->data['title'];

        $neededjs = array('myassets/js/plugins/dataTables/datatables.min.js');
        $neededcss = array('myassets/css/plugins/dataTables/datatables.min.css');

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        // Load a view in the content partial
        $this->data['add_modal'] = $this->load->view('branch/add_modal', $this->data, TRUE);
        $this->template->content->view('branch/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function jsonList() {
        $where = FALSE;
        if ($this->input->post('branch_id') !== NULL) {
            $where = "branch_id = " . $this->input->post('branch_id');
        }
        $this->data['data'] = $this->department_model->get($where);
        echo json_encode($this->data);
    }

    public function create() {

        $this->form_validation->set_rules('department_number', 'Department Code', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('department_name', 'Department name', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->department_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Department details successfully updated";
                    //activity log
                     $this->helpers->activity_logs($_SESSION['id'],1,"Editing department",$feedback['message']." # ". $this->input->post('id'),NULL,NULL);
                } else {
                    $feedback['message'] = "There was a problem updating the department details";

                      $this->helpers->activity_logs($_SESSION['id'],1,"Editing department",$feedback['message']." # ". $this->input->post('id'),NULL,NULL);
                }
            } else {
                $dept_id = $this->department_model->set();
                if ($dept_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Department details successfully saved";

                      $this->helpers->activity_logs($_SESSION['id'],1,"Creating department",$feedback['message']." # ".$dept_id,NULL,$dept_id);
                } else {
                    $feedback['message'] = "There was a problem saving the department data";

                     $this->helpers->activity_logs($_SESSION['id'],1,"Creating department",$feedback['message']." # ".$department_number,NULL,$department_number);
                }
            }
        }
        echo json_encode($feedback);
    }

    public function view($department_id) {
        $this->data['department'] = $this->department_model->get($department_id);
        if (empty($this->data['branch'])) {
            show_404();
        }
        $this->load->model('department_model');

        $this->data['title'] = $this->data['branch']['branch_name'];
        $this->data['sub_title'] = $this->data['branch']['branch_number'];

        $this->data['departments'] = $this->department_model->get("branch_id=$branch_id");
        $this->data['staff'] = $this->staff_model->get("department_id IN (SELECT id FROM `fms_department` WHERE branch_id=$branch_id)");

        $this->template->title = $this->data['title'];

        $neededjs = array('myassets/js/plugins/dataTables/datatables.min.js');
        $neededcss = array('myassets/css/plugins/dataTables/datatables.min.css');

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        
        $this->data['add_department_modal'] = $this->load->view('department/add_modal', $this->data, true);
        $this->data['add_branch_modal'] = $this->load->view('branch/add_modal', $this->data, TRUE);
        $this->template->content->view('branch/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
        if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            if (($response['success'] = $this->department_model->delete($this->input->post('id'))) === true) {
                $response['message'] = "Pap Details successfully deleted";

                 $this->helpers->activity_logs($_SESSION['id'],1,"Deleting department",$response['message']." # ".$_POST['id'],NULL,$_POST['id']);
            }
        }
        echo json_encode($response);
        
          $this->helpers->activity_logs($_SESSION['id'],1,"Deleting department",$response['message']." # ".$_POST['id'],NULL,$_POST['id']);
    }

}
