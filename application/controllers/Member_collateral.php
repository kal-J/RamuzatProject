<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Member_collateral extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model("member_collateral_model");
        $this->load->model("user_model");
    }

    public function jsonList() {
        $this->data['data'] = $this->member_collateral_model->get('a.status_id=1');
        echo json_encode($this->data);
    }

    // Get collaterals attached to active loans
    public function active_loan() {
        $this->data['data'] = $this->member_collateral_model->get('a.status_id=1 AND ls.state_id IN(7,12)');
        echo json_encode($this->data);
    }

    // Get collaterals attached to pending loans
    public function pending_loan() {
        $this->data['data'] = $this->member_collateral_model->get('a.status_id=1 AND ls.state_id IN(1,5)');
        echo json_encode($this->data);
    }

    // Get collaterals attached to Approved loans
    public function approved_loan() {
        $this->data['data'] = $this->member_collateral_model->get('a.status_id=1 AND ls.state_id=6');
        echo json_encode($this->data);
    }

    // Get collaterals attached to Approved loans
    public function closed_loan() {
        $this->data['data'] = $this->member_collateral_model->get('a.status_id=1 AND ls.state_id IN(2,3,4,8,9,10,14,15)');
        echo json_encode($this->data);
    }

    // Get reclaimed collaterals
    public function reclaimed() {
        $this->data['data'] = $this->member_collateral_model->get('a.status_id=2');
        echo json_encode($this->data);
    }

    // Get collaterals attached to in-arrears loans
    public function in_arrears_loan() {
        $this->data['data'] = $this->member_collateral_model->get('a.status_id=1 AND ls.state_id=13');
        echo json_encode($this->data);
    }

    // get collateral not attached to active loans
    public function jsonList2() {
        if(!empty($this->input->post('member_id'))) {
            $this->data['data'] = $this->member_collateral_model->get_not_attached_to_active_loan('status_id=1 AND member_id='. $this->input->post('member_id'));

        } else {
            $this->data['data'] = $this->member_collateral_model->get_not_attached_to_active_loan('status_id=1');

        }
        echo json_encode($this->data);
    }

    public function update() {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('item_value', 'Item value', 'required');
        $this->form_validation->set_rules('description', 'Collateral Description', 'required');

        $feedback = array();
        $feedback['success'] = false;
        $feedback['message'] = 'Failed to Upate collateral, Please try again';

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {

            if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
                #updating loan collateral
                $update_data = $this->input->post();

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
                        $update_data['file_name'] = $document_name;
                    }
                }

                if ($this->member_collateral_model->update_document($update_data)) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan collateral document details successfully updated";
                }
            }
        }

        echo json_encode($feedback);
    }

    public function delete() {
        $feedback['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $feedback['success'] = FALSE;
        if ($this->member_collateral_model->check_if_has_attached_active_loan($this->input->post('id'))) {
            $feedback['message'] = "This collateral can not be deleted because it is still attached to a Loan. First Delete the attachement from the Loan before deleting this collateral";
        } else {
            if ($this->member_collateral_model->delete_by_id() === true) {
                $feedback['success'] = TRUE;
                $feedback['message'] = "Member's Collateral successfully deleted.";
            }
        }

        echo json_encode($feedback);
    }
}
