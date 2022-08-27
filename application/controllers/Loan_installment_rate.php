<?php

/**
 * Description of Branch
 *
 * @author Melchisedec
 */
class Loan_installment_rate extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('Loan_installment_rate_model');
    }

    public function index() {
        //error_reporting(0);
        $this->load->library("helpers");

        $this->data['title'] = $this->data['sub_title'] = 'Loan installment rate';

        $this->template->title = $this->data['title'];

        // Load a view in the content partial
        $this->data['add_modal'] = $this->load->view('branch/add_modal', $this->data, TRUE);
        $this->template->content->view('branch/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function jsonList() {
        $this->data['data'] = $this->Loan_installment_rate_model->get();
        echo json_encode($this->data);
    }

    public function create() {

        $this->form_validation->set_rules('loan_installment_rate', 'loan installment rate', array('required',  'max_length[30]'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Loan_installment_rate_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Branch details successfully updated";
                    $feedback['loan_product_fee'] = $this->Loan_installment_rate_model->get($_POST['id']);
                } else {
                    $feedback['message'] = "There was a problem updating the loan product fee details";
                }
            } else {
                $loan_product_fee_id = $this->Loan_installment_rate_model->set();
                if ($loan_product_fee_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan product fee details successfully saved";
                } else {
                    $feedback['message'] = "There was a problem saving the loan product fee data";
                }
            }
        }
        echo json_encode($feedback);
    }

    public function view($branch_id) {
        $this->data['branch'] = $this->Loan_installment_rate_model->get($branch_id);
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
            $this->data['message'] = $this->Loan_installment_rate_model->change_status();
            if ($this->data['message'] === true) {
                $this->data['success'] = TRUE;
            }
        }
        echo json_encode($this->data);
    }
    
    public function delete()
    {
      $response['message'] = "Data could not be deleted, contact support.";
      $response['success'] = FALSE;
      if($this->Loan_installment_rate_model->delete_by_id()){
        $response['success'] = TRUE;
        $response['message'] = "Data successfully deleted.";
      }
      echo json_encode($response);
    }
}
