<?php

/**
 * Description of Loan_product
 *
 * @author Eric
 */
class Loan_product extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('loan_product_model');
        $this->load->library(array("form_validation", "helpers"));
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 11, $_SESSION['staff_id']);
        //$this->data['guarantor_list'] = $this->helpers->user_privileges($module_id = 15, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 11, $_SESSION['organisation_id']);
        if(empty($this->data['module_access'])){
            redirect('my404');
        } else {
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
           // $this->data['guarantor_privilege'] = array_column($this->data['guarantor_list'], "privilege_code");
            $this->data['loan_product_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        }
        $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
        if(empty($this->data['fiscal_active'])){
            redirect('dashboard');
        }
        }
    }

    public function index() {
        $this->data['title'] = $this->data['sub_title'] = 'Loan Products';

        $this->template->title = $this->data['title'];

        // Load a view in the content partial
        $this->data['add_modal'] = $this->load->view('loan/add_modal', $this->data, TRUE);
        $this->template->content->view('loan_product/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function jsonList() {
        $data['data'] = $this->loan_product_model->get();
        echo json_encode($data);
    }

    public function create() {

        if (isset($_POST['id']) && is_numeric($_POST['id']) && isset($_POST['max_tranches'])) {
            $this->form_validation->set_rules('max_tranches', 'Maximum Tranches for the Product', array('required', 'min_length[1]', 'max_length[30]'), array('required' => '%s must be entered'));
        }else{
            $this->form_validation->set_rules('product_name', 'Loan Product Name', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));
            $this->form_validation->set_rules('available_to_id', 'Available To' , array('required'), array('required' => '%s must be selected'));
        }

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->loan_product_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan Product details successfully updated";
                    $feedback['loan_product'] = $this->loan_product_model->get($this->input->post('id'));
                   
                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing Loan Product",$feedback['message']." # ".$this->input->post('id'),NULL,$this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the Loan Product details";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing Loan Product",$feedback['message']." # ".$this->input->post('id'),NULL,$this->input->post('id'));
                }
            } else {
                $loan_product_id = $this->loan_product_model->set();
                if ($loan_product_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan Product details successfully saved";
                    $feedback['loan'] = $loan_product_id;

                     $this->helpers->activity_logs($_SESSION['id'],18,"Creating Loan Product",$feedback['message']." # ".$loan_product_id,NULL,$loan_product_id);
                } else {
                    $feedback['message'] = "There was a problem saving the Loan Product details";
                    
                       $this->helpers->activity_logs($_SESSION['id'],18,"Creating Loan Product",$feedback['message']." # ".$loan_product_id,NULL,$loan_product_id);
                }
            }
        }
        echo json_encode($feedback);
    }

    public function view($loan_product_id) {
        $this->load->model('loan_product_type_model');
        $this->load->model('guarantor_setting_model');
        $this->load->model('loan_product_fee_model');
        $this->load->model('loan_product_guarantor_setting_model');
        $this->load->model('loan_fees_model');
        $this->load->model('penalty_calculation_method_model');
        $this->load->model('miscellaneous_model');
        $this->load->model("accounts_model");
        $this->load->model('RolePrivilege_model');
        $this->load->library(array("form_validation", "helpers"));
        $this->data['loan_product'] = $this->loan_product_model->get($loan_product_id);
        if (empty($this->data['loan_product'])) {
            show_404();
        }
        $this->data['module_list']=$this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] =array_column($this->data['module_list'],"module_id");
        
        $this->data['account_list'] = $this->accounts_model->get();
        $this->data['title'] = $this->data['loan_product']['product_name'];
        $this->data['sub_title'] = $this->data['loan_product']['name'];
        $this->data['loan_product_type'] = $this->loan_product_type_model->get();
        $this->data['penalty_calculation_method'] = $this->penalty_calculation_method_model->get();
        $this->data['available_to']= $this->miscellaneous_model->get_available_to();
        $this->data['repayment_made_every']= $this->miscellaneous_model->get();
        $this->data['guarantor_setting']= $this->guarantor_setting_model->get();
        $this->data['modalTitle'] = "Edit Loan Product Info";
        $this->data['saveButton'] = "Update";
        $this->data['loan_fee'] = $this->loan_fees_model->get_loan_fees();//" fms_loan_fees.id not in  ( SELECT loanfee_id from fms_loan_product_fees WHERE loanproduct_id = '".$loan_product_id."' and status_id = 1) "
        $this->template->title = $this->data['title'];
        $this->data['add_loan_product_modal'] = $this->load->view('setting/loan/loan_product/loan_product-modal',$this->data,true);
        $this->data['add_loan_product_penalty_modal'] = $this->load->view('setting/loan/loan_product/loan_product_penalty-modal',$this->data,true);
        $this->data['add_loan_product_fee_modal'] = $this->load->view('setting/loan/loan_product_fee/loan_product_fee_modal',$this->data,true);
        $this->data['add_loan_product_guarantor_setting_modal'] = $this->load->view('setting/loan/loan_product_guarantor_setting/loan_product_guarantor_setting_modal',$this->data,true);

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("fieldset.css","plugins/select2/select2.min.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->content->view('setting/loan/loan_product/loan_view', $this->data);
        // Publish the template
        $this->template->publish();
    }
    
    public function delete(){
      $response['message'] = "Loan product could not be deleted, contact support.";
      $response['success'] = FALSE;
      if($this->loan_product_model->delete_by_id($this->input->post('id'))){
        $response['success'] = TRUE;
        $response['message'] = "Loan product successfully deleted.";
      }
      echo json_encode($response);
    }
    public function change_status(){
      $response['message'] = "Loan product could not be deactivated, contact support.";
      $response['success'] = FALSE;
        if($this->loan_product_model->change_status_by_id($this->input->post('id'))){
        $response['success'] = TRUE;
        $response['message'] = "Loan product successfully deactivated.";
      }
      echo json_encode($response);
    }
}
