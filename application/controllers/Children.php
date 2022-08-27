<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Children extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model("Children_model");
    }
    public function jsonList() {
        if (!empty($this->input->post('member_id'))) {
            $member_id = $this->input->post('member_id');
            $this->data['data'] = $this->Children_model->get('status_id=1 AND member_id=' . $member_id);
        } else {
            $this->data['data'] = $this->Children_model->get('status_id=1');
        }
        echo json_encode($this->data);
    }
    public function create() {
        $this->form_validation->set_rules('firstname', 'First Name', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('lastname', 'Last Name', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('othernames', 'Other Name', array('min_length[2]', 'max_length[30]'));
        $this->form_validation->set_rules('gender', 'Gender', array('required'), array('required' => 'Please select %s'));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Children_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Guardian details successfully updated";
                    $feedback['children'] = $this->Children_model->get($_POST['id']);
                } else {
                    $feedback['message'] = "There was a problem updating the Guardian details";
                }
            } else {
                $child_id = $this->Children_model->set();
                if ($child_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Guardian details successfully saved";
                    $feedback['children'] = $this->Children_model->get();
                } else {
                    $feedback['message'] = "There was a problem saving the Guardian data";
                }
            }
        }
        echo json_encode($feedback);
    }
    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
        // if (isset($_SESSION['role']) && isset($_SESSION['role']) == 1) {
        if (($response['success'] = $this->Children_model->delete($this->input->post('id'))) === true) {
            $response['message'] = "Guardian Details successfully deleted";
        }
        // }
        echo json_encode($response);
    }
}
