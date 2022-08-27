<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("group_model");
        $this->load->model("Fiscal_month_model");
        $this->load->library("helpers");
        $this->load->library("num_format_helper");
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 13, $_SESSION['staff_id']);
        $this->data['group_loan_list'] = $this->helpers->user_privileges($module_id = 14, $_SESSION['staff_id']);
        $this->data['savings_prev_list'] = $this->helpers->user_privileges($module_id = 6, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 13, $_SESSION['organisation_id']);
        if(empty($this->data['module_access'])){
            redirect('my404');
        } else {
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['group_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
            $this->data['group_loan_privilege'] = array_column($this->data['group_loan_list'], "privilege_code");
            $this->data['savings_privilege'] = array_column($this->data['savings_prev_list'], "privilege_code");
        }
        $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
            if(empty($this->data['fiscal_active'])){
                redirect('dashboard');
            }else{

            $this->data['lock_month_access'] = $this->helpers->org_access_module($module_id = 23, $_SESSION['organisation_id']);
                if(!empty($this->data['lock_month_access'])){
                    $this->data['active_month'] = $this->Fiscal_month_model->get_active_month();
                    if(empty($this->data['active_month'])){
                       redirect('dashboard');
                    }
                } 
            }
        }
    }

    public function jsonList() {
        $this->data['data'] = $this->group_model->get();
        echo json_encode($this->data);
    }

    public function index() {
        $this->load->library("helpers");
        $this->data['title'] = $this->data['sub_title'] = "Client Groups";
        $this->template->title = $this->data['title'];
        $this->template->content->view('group/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function renderCompanyData($group_id) {
          $this->data['company'] = $this->group_model->get($group_id);
         print_r($this->load->view('group/edit_company_modal',$this->data,TRUE));
        
    }

    public function view($group_id) {
        $this->data['group'] = $this->group_model->get($group_id);
         
        
        if (empty($this->data['group'])) {
            show_404();
        }
        $this->data['module_list']=$this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] =array_column($this->data['module_list'],"module_id");



        $this->load->library("helpers");
        $this->load->model("member_model");

        $this->load->model("organisation_format_model");
        $this->load->model("DepositProduct_model");
        $this->load->model("TransactionChannel_model");
        $this->load->model("Savings_account_model");

        $this->load->model('Dashboard_model');
        $this->load->model('Group_model');
        $this->load->model('Staff_model');
        $this->load->model('client_loan_model');
        $this->load->model('Loan_product_model');
        $this->load->model('penalty_calculation_method_model');
        $this->load->model('miscellaneous_model');
        $this->load->model("RolePrivilege_model");
        $this->load->model("Loan_guarantor_model");
        $this->load->model("Share_issuance_model");
        $this->load->model("Share_issuance_category_model");
        $this->load->model("share_call_model");
        $this->load->model("Share_transaction_model");
        $this->load->model("Shares_model");

        //==================== SAVINGS DATA   
        $this->data['tchannel'] = $this->TransactionChannel_model->get();
        $this->data['products'] = $this->DepositProduct_model->get("sp.status_id=1 AND availableto IN (2,3)");
        $this->data['organisation_format'] = $this->organisation_format_model->get_formats();
        $this->data['sorted_clients'] = $this->Savings_account_model->get_clients();
        $this->data['savings_accounts'] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7');
      
        $this->data['new_account_no'] = $this->num_format_helper->new_savings_acc_no();
         //==================== END SAVINGS DATA ===================

         //==================== START SHARES DATA ===================
         $share_privilege_list = $this->helpers->user_privileges(12, $_SESSION['staff_id']);
        $this->data['share_privilege'] = array_column($share_privilege_list, "privilege_code");
        $this->data['share_issuances'] = $this->Share_issuance_model->get(['share_issuance.status_id', 1]);
        $this->data['share_categories'] = $this->Share_issuance_category_model->get("shi.status_id=1");
        $this->data['firstcall'] = $this->share_call_model->get_first_calls(null);
        //$this->data['male_members'] = count($this->Share_transaction_model->get2("u.gender=1 AND m.status_id !=9"));
         //$this->data['female_members'] = count($this->Share_transaction_model->get2("u.gender=0 AND m.status_id !=9"));
         // share accounts 
        //    if(is_numeric($group_id) && isset($group_id)){
        //        $where ="group.id=".$group_id;
        //    }
        //    else{
        //        $where ="state_id IN(7,19) AND share_accounts.status_id=1";
        //    }
          $this->data['share_accounts'] = count($this->Shares_model->get("state_id IN(7,19) AND share_account.status_id=1"));
          $this->data['share_accounts'] = count($this->Shares_model->get("state_id IN(7,19) AND share_account.status_id=1"));
          $this->data['sh_cat'] = $this->Share_issuance_category_model->get_active_share_issuance_price();

        //==================== END SHARES DATA ===================



         //==================== START LOAN DATA ==================== 
        $this->data['staffs'] = $this->Staff_model->get_registeredby("status_id=1");
        $this->data['loanProducts'] = $this->Loan_product_model->get_product("loan_product.status_id=1 AND loan_product.available_to_id=3 OR loan_product.available_to_id=2");
        $this->data['type'] = $this->data['sub_type'] = 'group_loan';
        $this->data['members'] = $this->member_model->get_member_by_user_id();
        //$this->data['new_loan_no'] = $this->client_loan_model->get_id();
        $this->data['new_loan_no'] = $this->generate_group_no();

        $this->data['loan_type'] = $this->miscellaneous_model->get_loan_type();
        $this->data['client_type'] = 2;
        $this->data['penalty_calculation_method'] = $this->penalty_calculation_method_model->get();
        $this->data['repayment_made_every'] = $this->miscellaneous_model->get();
        $this->data['marital_statuses'] = $this->miscellaneous_model->get_marital_status_options();
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 3');
        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);

        
        //==================== END LOAN DATA==================== 

        $this->data['title'] = $this->data['sub_title'] = $this->data['group']['group_name'];
        $this->template->title = $this->data['title'];

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js","plugins/highcharts/code/highcharts.js", "plugins/highcharts/code/highcharts-3d.js", "plugins/highcharts/code/modules/exporting.js", "plugins/highcharts/code/highslide-full.min.js","plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/modules/export-data.js", "plugins/highcharts/code/modules/series-label.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/highcharts/code/css/highslide.css");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->content->view('group/view', $this->data);
        // Publish the template
        $this->template->publish();
    }
    
    public function create() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules("group_name", "Group name", "required|callback__check_group_name", array("required" => "%s must be entered", "_check_group_name" => "%s already exists, choose another one"));
        $this->form_validation->set_rules('description', 'Description', 'required');
        $feedback['success'] = false;

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {
            if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) { //editing exsting item
                if ($this->group_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Group details successfully updated";
                    //activity log

                     $this->helpers->activity_logs($_SESSION['id'],6,"Editing group",$feedback['message']." # ".$this->input->post('id'),NULL,$this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the group details, please try again";

                     $this->helpers->activity_logs($_SESSION['id'],6,"Editing group",$feedback['message']." # ".$this->input->post('id'),NULL,$this->input->post('id'));
                }
            } else {
                $group_no = $this->generate_group_no();  //group_number
                if ($group_no != false) {
                    $returned_id=$this->group_model->add($group_no);
                    if (is_numeric($returned_id)) {
                        $feedback['success'] = true;
                        $feedback['message'] = "Group details have been successfully Added";
                        $feedback['group_id'] = $returned_id;

                         $this->helpers->activity_logs($_SESSION['id'],6,"Creating new group",$feedback['message']." # ".$group_no ,NULL,$group_no );
                    } else {
                        //$this->user_model->delete();
                        $feedback['message'] = "There was a problem saving the group details, please try again";

                         $this->helpers->activity_logs($_SESSION['id'],6,"Creating new group",$feedback['message']." # ".$group_no ,NULL,$group_no );
                    }
                }
            }
        }
        echo json_encode($feedback);
    }
    function _check_group_name($group_name) {
        $existing_name = $this->group_model->validate_group_name($group_name);
        
        return $existing_name;
    }
    function generate_group_no() {
        $this->load->library("num_format_helper");
        $new_group_no = $this->num_format_helper->new_group_no();
        return $new_group_no===FALSE?$this->input->post("group_no"):$new_group_no; 
    }

    public function delete() {
        $response['message'] = "Data could not be deleted, contact support.";
        $response['success'] = FALSE;
        if ($this->group_model->delete()) {
            $response['success'] = TRUE;
            $response['message'] = "Data successfully deleted.";

           
        }
        echo json_encode($response);

          $this->helpers->activity_logs($_SESSION['id'],6,"Deleting group",$response['message']." # ".$this->input->post('id'),NULL,$this->input->post('id'));
    }

    public function deactivate() {
        $response['message'] = "Group details could not be deactivated, please try again or contact IT support.";
        $response['success'] = FALSE;
        if ($this->group_model->deactivate()) {
            $response['success'] = TRUE;
            $response['message'] = "Group successfully deactivated.";
        }
        echo json_encode($response);

         $this->helpers->activity_logs($_SESSION['id'],6,"Deactivating group",$response['message']." # ".$this->input->post('id'),NULL,$this->input->post('id'));
    }
    public function attach_director(){
        $data= $this->input->post(null,true);
        echo json_encode($data);die;
    }

}
