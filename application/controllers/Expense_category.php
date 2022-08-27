<?php

/**
 * Description of Expense Category controller
 *
 * @author Allan Jes
 */
class Expense_category extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('expense_category_model');
        $this->load->library(array("form_validation", "helpers"));
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 8, $this->session->userdata('staff_id'));
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['privileges'] = array_column($this->data['privilege_list'], "privilege_code");
        }
    }

    public function jsonList() {
        $data['data'] = $this->expense_category_model->get();
        echo json_encode($data);
    }

    public function view($id) {
        $neededcss = array("fieldset.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->data['expense_category'] = $this->expense_category_model->get($id);
        if (empty($this->data['expense_category'])) {
            redirect("my404");
        }

        $this->data['title'] = $this->data['sub_title'] = $this->data['expense_category']['expense_category_name'];
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('accounts/expense_category/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function create() {
        $this->load->model('ledger_model');
        $this->form_validation->set_rules('description', 'description', array('required'), array('required' => '%s must be entered'));
        //$this->form_validation->set_rules('expense_category_code', 'Expense category code', array('max_length[10]'), array('max_length' => '%s length cannot exceed 10 characters'));
        $this->form_validation->set_rules('expense_category_name', 'expense Category Name', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('linked_account_id', 'Linked Account', array('required'), array('required' => '%s must be selected'));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->expense_category_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Expense category details successfully updated";
                    $feedback['expense_category'] = $this->expense_category_model->get($_POST['id']);
                } else {
                    $feedback['message'] = "There was a problem updating the assets details";
                }
            } else {
                $expense_category_id = $this->expense_category_model->set();
                if (is_numeric($expense_category_id)) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Expense category details successfully saved";
                } else {
                    $feedback['message'] = "There was a problem saving the assets data";
                }
            }
        }
        echo json_encode($feedback);
    }

    public function delete() {
        $response['message'] = "Expense category details could not be deleted, contact support.";
        $response['success'] = FALSE;
        if ($this->expense_category_model->delete($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] = "Expense category details successfully deleted.";
        }
        echo json_encode($response);
    }

    public function change_status() {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Expense category details could not be $msg activated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->expense_category_model->deactivate($this->input->post('id'))) {
            $response['message'] = "Expense category details successfully been $msg activated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }

}
