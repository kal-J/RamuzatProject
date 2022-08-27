<?php
class Share_issuance_category extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Share_issuance_category_model');
        $this->load->library("helpers");

    }

    public function jsonList() {
        $where = FALSE;
        if ($this->input->post('organisation_id') !== NULL) {
            $where = "organisation_id = " . $this->input->post('organisation_id');
        }
        $this->data['data'] = $this->Share_issuance_category_model->get($where);
        echo json_encode($this->data);
    }

    public function create() {
        $this->form_validation->set_rules('price_per_share', 'price_per_share ', array('required'), array('required' => '%s must be selected'));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Share_issuance_category_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Share Category details successfully updated";
                    
                } else {
                    $feedback['message'] = "There was a problem updating the Share Category details";
              }
            } else {
                $role_id = $this->Share_issuance_category_model->set();
                if ($role_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Share Category details successfully saved";
                } else {
                    $feedback['message'] = "There was a problem saving the Share Category data";
                }
            }
        }
        echo json_encode($feedback);
    }

    public function delete() {
        $response['message'] = "Share Category could not be deleted, IT support.";
        $response['success'] = FALSE;
        if ($this->Share_issuance_category_model->delete($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] = "Share Category successfully deleted.";
        }
        echo json_encode($response);
    }

    public function change_status() {
        $msg = $this->input->post('status_id')==1?"":"de";
        $response['message'] = "Share Category could not be $msg activated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->Share_issuance_category_model->deactivate($this->input->post('id'))) {
            $response['message'] = "Share Category has successfully been $msg activated.";
            $response['success'] = TRUE;
        }
      echo json_encode($response);
    }

}
