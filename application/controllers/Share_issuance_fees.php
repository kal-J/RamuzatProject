<?php

/**
 * Description of share Product Fee
 *
 * @author Eric
 */
class Share_issuance_fees extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('share_issuance_fees_model');
    }

    public function jsonList() {
        $this->data['data'] = $this->share_issuance_fees_model->get($this->input->post('shareproduct_id'));
        echo json_encode($this->data);
    }

    public function create() {

        $this->form_validation->set_rules('sharefee_id', 'share Fee', array('required', 'min_length[1]', 'max_length[100]'), array('required' => '%s must be entered'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->share_issuance_fees_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Share Issuance Fee details successfully updated";
                    $feedback['share_issuance_fee'] = $this->share_issuance_fees_model->get($_POST['id']);
                } else {
                    $feedback['message'] = "There was a problem updating the share issuance fee details";
                }
            } else {
                $share_issuance_fee_id = $this->share_issuance_fees_model->set();
                if ($share_issuance_fee_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Share Issuance Fee details successfully saved";
                } else {
                    $feedback['message'] = "There was a problem saving the share issuance fee data";
                }
            }
        }
        echo json_encode($feedback);
    }

    public function delete() {
        $response['message'] = "Share issuance Fee could not be deleted, contact IT support.";
        $response['success'] = FALSE;
        if ($this->share_issuance_fees_model->delete_by_id()) {
            $response['success'] = TRUE;
            $response['message'] = "Share issuance Fee successfully Removed.";
        }
        echo json_encode($response);
    }

    public function change_status() {
        $response['message'] = "Share issuance Fee could not be deactivated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->share_issuance_fees_model->change_status_by_id()) {
            $response['message'] = "Share issuance Fee status been updated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }

}
