<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member_referrals extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        $this->load->library("helpers");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        }
        
        $this->load->model('Staff_model');
        $this->load->model('User_model');
        $this->load->model("Fiscal_month_model");
        $this->load->model("dashboard_model");
        $this->load->model("organisation_model");
        $this->load->model("organisation_format_model");
        $this->load->model('Role_model');
        $this->load->model('member_model');
        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);

        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 26, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 26, $_SESSION['organisation_id']);
        if(empty($this->data['module_access'])){
            redirect('my404');
        } else {
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['till_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
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

    public function index() {
        $this->load->library("num_format_helper");
        $this->load->model('miscellaneous_model');
        $this->load->model('TransactionChannel_model');
        $this->data['title'] = $this->data['sub_title'] = "Member Referral";
        $this->template->title = $this->data['title'];
         $this->data['module_list']=$this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] =array_column($this->data['module_list'],"module_id");
        $this->data['tchannel'] = $this->TransactionChannel_model->get();
        $this->data['staff_list'] = $this->Staff_model->get_registeredby("status_id=1");
        $this->data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
       
        $this->data['member_referral_info'] = $this->member_model->get_member_referral_list1();
       

        $neededjs = ["plugins/select2/select2.full.min.js", "plugins/datepicker/bootstrap-datepicker.js","plugins/validate/jquery.validate.min.js"];
        $neededcss = ["plugins/select2/select2.min.css", "plugins/datepicker/datepicker3.css"];
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        // Load a view in the content partial
        $this->template->content->view('member_referral/index', $this->data);
        
        // Publish the template
        $this->template->publish();
    }

    public function memberReferrals(){
        $this->load->model('member_model');
        if(isset($_SESSION['member_referral']) && $_SESSION['member_referral']==1){
           $introduced_by_id=$this->input->post("introduced_by_id");
        
        $data['data'] = $this->member_model->get_member_referral_list($introduced_by_id);
            
        echo json_encode($data);
        }
        }
    
}
