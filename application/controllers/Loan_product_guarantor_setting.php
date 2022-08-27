<?php

/**
 * Description of Loan Product Guarantor setting
 *
 * @author Eric
 */
class Loan_product_guarantor_setting extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('loan_product_guarantor_setting_model');
    }

    public function jsonList() {
        $this->data['data'] = $this->loan_product_guarantor_setting_model->get($this->input->post('loan_product_id'));
        echo json_encode($this->data);
    }

    public function create() {

        $this->form_validation->set_rules('guarantor_setting_id', 'Guarantor setting', array('required', 'min_length[1]', 'max_length[100]'), array('required' => '%s must be entered'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->loan_product_guarantor_setting_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan Product Guarantor setting details successfully updated";
                    $feedback['loan_product_guarantor_setting'] = $this->loan_product_guarantor_setting_model->get($_POST['id']);
                } else {
                    $feedback['message'] = "There was a problem updating the loan product Guarantor setting details";
                }
            } else {
                $loan_product_guarantor_setting_id = $this->loan_product_guarantor_setting_model->set();
                if ($loan_product_guarantor_setting_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan Product Guarantor setting details successfully saved";
                } else {
                    $feedback['message'] = "There was a problem saving the loan product Guarantor setting data";
                }
            }
        }
        echo json_encode($feedback);
    }
    public function delete() {
        $response['message'] = "Loan product Guarantor setting could not be deleted, contact IT support.";
        $response['success'] = FALSE;
        if ($this->loan_product_guarantor_setting_model->delete_by_id($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] = "Loan product Guarantor setting successfully deleted.";
        }
        echo json_encode($response);
    }

    public function change_status() {
        $response['message'] = "Loan product Guarantor setting could not be deactivated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->loan_product_guarantor_setting_model->change_status_by_id($this->input->post('id'))) {
            $response['message'] = "Loan product Guarantor setting has successfully been deactivated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }
}
