<?php
/**
 * Description of ledger
 *
 * @author reagan
 */
class Ledger extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('ledger_model');
        $this->load->model('miscellaneous_model');
        $this->load->model("TransactionChannel_model");
        $this->load->model("Member_model");
        $this->load->model("DepositProduct_model");

        $this->load->library(array("form_validation", "helpers"));
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 7, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 7, $_SESSION['organisation_id']);
        if(empty($this->data['module_access'])){
            redirect('my404');
        } else {
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['privileges'] = array_column($this->data['privilege_list'], "privilege_code");
        }
        $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
        if(empty($this->data['fiscal_active'])){
            redirect('dashboard');
        }
        }
    }
    public function jsonList() {
        $data['data'] = $this->ledger_model->get(); 
         print_r(json_encode($data));
    }
    public function index() {
        $neededcss = array("fieldset.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['accounts_from'] = $this->ledger_model->get_ledger_accounts();
        
        $this->data['title'] = $this->data['sub_title'] = "General Ledger Accounts";
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('accounts/transaction/index', $this->data);
        // Publish the template
        $this->template->publish();
    }
    public function create() {
        $this->form_validation->set_rules('debit_account_id', 'Debit Account', array('required'), array('required' => '%s must be selected'));
        $this->form_validation->set_rules('credit_account_id', 'Credit Account', array('required'), array('required' => '%s must be selected'));
        $this->form_validation->set_rules('amount', 'Amount', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('narrative', 'Narrative', array('required'), array('required' => '%s must be entered'));
    
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->ledger_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Post details successfully updated";
                    $feedback['branch'] = $this->ledger_model->get($_POST['id']);
                } else {
                    $feedback['message'] = "There was a problem  posting";
                }
            } else {
                $branch_id = $this->ledger_model->set();
                if ($branch_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Post Entry successfully saved";
                } else {
                    $feedback['message'] = "There was a problem  posting";
                }
            }
        }
        echo json_encode($feedback);
    }
    public function change_status() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
        if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            $this->data['message'] = $this->ledger_model->change_status();
            if ($this->data['message'] === true) {
                $this->data['success'] = TRUE;
            }
        }
        echo json_encode($this->data);
    }
    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
        if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            if (($response['success'] = $this->ledger_model->delete($this->input->post('id'))) === true) {
                $response['message'] = "Account Details successfully deleted";
            }
        }
        echo json_encode($response);
    }
    public function sub_account_cat(){
        $this->data['sub_account_cat'] = $this->miscellaneous_model->get_account_subcat($this->input->post('category_id'));
        echo json_encode($this->data);
    }
}
