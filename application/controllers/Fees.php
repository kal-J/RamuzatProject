<?php

/**
 * Description of FEES
 *
 * @author reagan
 */
class Fees extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->data['privilege_list'] = $this->helpers->user_privileges(2, $this->session->userdata('staff_id'));
        $this->data['subs_list'] = $this->helpers->user_privileges(9, $_SESSION['staff_id']);
        $this->data['membership_list'] = $this->helpers->user_privileges(21, $_SESSION['staff_id']);

        $this->data['module_access'] = $this->helpers->org_access_module(2, $_SESSION['organisation_id']);
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
             $this->data['member_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
            $this->data['subscription_privilege'] = array_column($this->data['subs_list'], "privilege_code");
            $this->data['membership_privilege'] = array_column($this->data['membership_list'], "privilege_code");
        }
        $this->load->model('accounts_model');
        $this->load->model('ledger_model');
        $this->load->model('Fiscal_month_model');
        $this->load->model('miscellaneous_model');
        $fiscal_year = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
            if(empty($fiscal_year)){
                redirect('dashboard');
            }else{
            $this->data['fiscal_active'] = array_merge($fiscal_year,['start_date2'=>date("d-m-Y", strtotime($fiscal_year['start_date'])),'end_date2'=>date("d-m-Y", strtotime($fiscal_year['end_date']))]);
            $this->data['lock_month_access'] = $this->helpers->org_access_module($module_id = 23, $_SESSION['organisation_id']);
                if(!empty($this->data['lock_month_access'])){
                    $this->data['active_month'] = $this->Fiscal_month_model->get_active_month();
                    if(empty($this->data['active_month'])){
                       redirect('dashboard');
                    }
                } 
            }
        }

        public function index() {
        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] = array_column($this->data['module_list'], "module_id");
        $this->load->library("num_format_helper");
        $this->load->model('miscellaneous_model');
        $this->load->model("organisation_format_model");
        $this->load->model("TransactionChannel_model");
        $this->load->model("RolePrivilege_model");
        $this->load->model("Savings_account_model");
        $this->load->model("Member_fees_model");
        $this->load->model("Member_fees_model");
        $this->load->model("loan_guarantor_model");
        $this->load->model("loan_product_fee_model");
        $this->load->model("subscription_plan_model");
        $this->load->model('Applied_member_fees_model');

        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['savings_accs'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)  +ifnull(charges, 0) + ifnull( amount_locked, 0) ) > 0 and j.state_id = 7 AND a.client_type=1");

        $this->data['available_loan_fees'] = $this->loan_product_fee_model->get();

        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 3');
//==================== SAVINGS DATA====================================
        $this->data['tchannel'] = $this->TransactionChannel_model->get();
        $this->data['organisation'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['sorted_clients'] = $this->Dashboard_model->get_clients('status_id=1');

//==================== END MEMBERSHIP  DATA ====================
        $this->data['title'] = $this->data['sub_title'] = "Fees";
       
        $this->data['available_member_fees'] = $this->Applied_member_fees_model->get();

        $this->template->title = $this->data['title'];
      
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css","custom.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->template->content->view('fees/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

 
}
