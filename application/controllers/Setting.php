<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->library("helpers");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('loan_installment_rate_model');
        $this->load->model('Dashboard_model');
        $this->load->model('department_model');
        $this->load->model('DepositProduct_model');
        $this->load->model('Date_application_method_model');
        $this->load->model('loan_product_type_model');
        $this->load->model('penalty_calculation_method_model');
        $this->load->model('miscellaneous_model');
        $this->load->model('loan_fees_model');
        $this->load->model('loan_product_model');
        $this->load->model('ModulePrivilege_model');
        $this->load->model('Modules_model');
        $this->load->model('Fiscal_month_model');
        $this->load->model("Organisation_format_model");
        $this->load->model('accounts_model');
        $this->load->model('payment_engine_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('non_working_days_model');
        $this->load->model('Alert_setting_model');
        $this->load->model("Accounts_model");
        $this->load->model('Staff_model');

        $this->data['settings_list'] = $this->helpers->user_privileges($module_id = 11, $_SESSION['staff_id']);
        $this->data['fiscal_list'] = $this->helpers->user_privileges($module_id = 20, $_SESSION['staff_id']);
        $this->data['share_issuance_list'] = $this->helpers->user_privileges($module_id = 17, $_SESSION['staff_id']);
        $this->data['loan_product_list'] = $this->helpers->user_privileges($module_id = 3, $_SESSION['staff_id']);
        $this->data['deposit_product_list'] = $this->helpers->user_privileges($module_id = 5, $_SESSION['staff_id']);
        $this->data['guarantor_list'] = $this->helpers->user_privileges($module_id = 15, $_SESSION['staff_id']);
        $this->data['rolemodule_list'] = $this->helpers->user_privileges($module_id = 7, $_SESSION['staff_id']);
        $this->data['role_list'] = $this->helpers->user_privileges($module_id = 16, $_SESSION['staff_id']);
        $this->data['format_list'] = $this->helpers->user_privileges($module_id = 18, $_SESSION['staff_id']);
        $this->data['approval_list'] = $this->helpers->user_privileges($module_id = 19, $_SESSION['staff_id']);
        $this->data['subs_list'] = $this->helpers->user_privileges($module_id = 9, $_SESSION['staff_id']);
        $this->data['membership_list'] = $this->helpers->user_privileges($module_id = 21, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 11, $_SESSION['organisation_id']);
        if(empty($this->data['module_access'])){
            redirect('my404');
        } else {
        if (empty($this->data['settings_list'])) {
            redirect('my404');
        } else {
            $this->data['privileges'] = array_column($this->data['settings_list'], "privilege_code");
            $this->data['fiscal_privilege'] = array_column($this->data['fiscal_list'], "privilege_code");
            $this->data['share_issuance_privilege'] = array_column($this->data['share_issuance_list'], "privilege_code");
            $this->data['loan_product_privilege'] = array_column($this->data['loan_product_list'], "privilege_code");
            $this->data['deposit_product_privilege'] = array_column($this->data['deposit_product_list'], "privilege_code");
            $this->data['guarantor_privilege'] = array_column($this->data['guarantor_list'], "privilege_code");
            $this->data['rolemodule_privilege'] = array_column($this->data['rolemodule_list'], "privilege_code");
            $this->data['role_privilege'] = array_column($this->data['role_list'], "privilege_code");
            $this->data['subscription_privilege'] = array_column($this->data['subs_list'], "privilege_code");
            $this->data['membership_privilege'] = array_column($this->data['membership_list'], "privilege_code");
            $this->data['approval_privilege'] = array_column($this->data['approval_list'], "privilege_code");
            $this->data['format_privilege'] = array_column($this->data['format_list'], "privilege_code");

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
    }

    public function index() {
       
        $this->data['account_list'] = $this->accounts_model->get();
        $this->data['organisation'] = $this->organisation_model->get($_SESSION['organisation_id']);
        //$this->data['format_types'] = $this->Organisation_format_model->get_format_types($org_id);
        $this->data['repayment_made_every']= $this->miscellaneous_model->get();
        $this->data['months']= $this->miscellaneous_model->get_months();
        $this->data['repayment_start_options']= $this->miscellaneous_model->get_repayment_start_options();
        $this->data['module_list']=$this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] =array_column($this->data['module_list'],"module_id");
        $this->data['loan_product_type'] = $this->loan_product_type_model->get();
        $this->data['penalty_calculation_method'] = $this->penalty_calculation_method_model->get();
        $this->data['title'] = $this->data['sub_title'] = "Organisation Setup";
       $this->data['payment_engine'] = $this->payment_engine_model->get($_SESSION['organisation_id']);
        
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("fieldset.css","plugins/select2/select2.min.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        
        $this->data['chargeTrigger'] = $this->miscellaneous_model->get_charge_trigger();
        $this->data['dateApplicationMtd'] = $this->Date_application_method_model->get_date_application_mtd();
        $this->data['deposit_product_type'] = $this->miscellaneous_model->get_product_type();
        $this->data['term_list'] = $this->miscellaneous_model->get_term_time_unit();
        $this->data['channels'] = $this->transactionChannel_model->get();
        $this->data['cal_mthd'] = $this->miscellaneous_model->get_interest_cal_mthd();

        $this->data['daysinyear'] = $this->miscellaneous_model->get_daysinyear();
        $this->data['account_balance_interest'] = $this->miscellaneous_model->get_account_balance_interest();

        $this->data['amountcalculatedas'] = $this->miscellaneous_model->get_amountcalculatedas();
        $this->data['loan_charge_trigger'] = $this->miscellaneous_model->get_loan_charge_trigger();
        $this->data['amountcalculatedas_other'] = $this->miscellaneous_model->get_amountcalculatedas_other();
        $this->data['feetypes'] = $this->miscellaneous_model->get_feetype();
        $this->data['available_to'] = $this->miscellaneous_model->get_available_to();
        $this->data['loan_product_data'] = $this->loan_product_model->get_product();
        $this->data['loan_fee'] = $this->loan_fees_model->get_loan_fees();
        $this->data['payment_engine_requirements'] = $this->payment_engine_model->get_requirement();
        $this->data['non_working_days'] = $this->non_working_days_model->get();

        $this->data['holiday_frequency_every']= $this->miscellaneous_model->get_holiday_frequency_every();
        $this->data['holiday_frequency_day']= $this->miscellaneous_model->get_holiday_frequency_day();
        $this->data['holiday_frequency_of']= $this->miscellaneous_model->get_holiday_frequency_of();/**/
        $this->data['alert_types']=$this->Alert_setting_model->get2();
        $this->data['account_from_chart']=$this->Accounts_model->get("acat.id IN(2)");
        $this->data['account_from_chart2']=$this->Accounts_model->get("acat.id IN(1)");
        $this->data['staff'] = $this->Staff_model->get_staff();
        // =====assign privileges to modules ==============
        //$this->data['modules'] = $this->ModulePrivilege_model->get_modules();
        //$this->data['modules'] = $this->Modules_model->get();
        $this->template->title = $this->data['title'];
        $this->template->content->view('setting/index', $this->data);
        // Publish the template
        $this->template->publish();
    }
}
