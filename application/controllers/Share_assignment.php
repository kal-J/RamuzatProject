<?php

/**
 * Description of Share Assignment
 *
 * @author Melchisedec
 */
class Share_assignment extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->library("helpers");
        $this->load->model('share_assignment_model');
        $this->load->model('Share_issuance_model');
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id=11,$this->session->userdata('staff_id'));
        if(empty($this->data['privilege_list'])){
            redirect('my404');
        } else {
            $this->data['privileges'] =array_column($this->data['privilege_list'],"privilege_code");
        }
    }

    public function jsonList() {
        $where = FALSE;
        if ($this->input->post('organisation_id') !== NULL) {
            $where = "organisation_id = " . $this->input->post('organisation_id');
        }
        $this->data['data'] = $this->share_assignment_model->get();
        echo json_encode($this->data);
    }

    public function create() {

        $this->form_validation->set_rules('sharefee_id', 'Share Fee', array('required', 'min_length[1]', 'max_length[100]'), array('required' => '%s must be entered'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Share_issuance_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Share Issuance Fee details successfully updated";
                    $feedback['share_issuance_fee'] = $this->Share_issuance_model->get($_POST['id']);
                } else {
                    $feedback['message'] = "There was a problem updating the share issuance fee details";
                }
            } else {
                $share_issuance_fee_id = $this->Share_issuance_model->set();
                if ($share_issuance_fee_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "share Product Fee details successfully saved";
                } else {
                    $feedback['message'] = "There was a problem saving the share issuance fee data";
                }
            }
        }
        echo json_encode($feedback);
    }

     public function delete() {
        $response['message'] = "Loan Fee could not be deleted, contact IT support.";
        $response['success'] = FALSE;
        if ($this->share_assignment_model->delete_by_id($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] = "Loan Fee successfully deleted.";
        }
        echo json_encode($response);
    }

    public function view($share_name_id) {
        $this->load->model('share_assignment_model');
        $this->load->library(array("form_validation", "helpers"));
        $this->data['loan_product'] = $this->share_assignment_model->get($share_name_id);
        // if (empty($this->data['loan_product'])) {
        //     show_404();
        // }
        $this->data['title'] = $this->data['loan_product']['product_name'];
        $this->data['sub_title'] = $this->data['loan_product']['name'];

        $this->template->title = $this->data['title'];

        $neededcss = array("fieldset.css");
        $neededjs = array();
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);        
        $this->data['add_share_assignment_modal'] = $this->load->view('setting/shares/share_assignment/add_modal',$this->data,true);
        $this->template->content->view('setting/shares/share_fee/share_detail', $this->data);
        // $this->template->content->view('setting/loan/loan_product/loan_view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function change_status() {
        $response['message'] = "Loan Fee could not be deactivated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->share_assignment_model->change_status_by_id($this->input->post('id'))) {
            $response['message'] = "Loan Fee successfully deactivated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }
}
