<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }

        $this->load->library("helpers");
        $this->load->model("Dashboard_model");
        $this->load->model('Loan_guarantor_model');
        $this->load->model('organisation_model');
        $this->load->model('User_model');
    }

    public function index()
    {
        $neededcss = array("fieldset.css", "plugins/highcharts/code/css/highslide.css", "plugins/daterangepicker/daterangepicker-bs3.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/highcharts/code/highcharts.js", "plugins/highcharts/code/highcharts-3d.js", "plugins/highcharts/code/modules/exporting.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/modules/export-data.js", "plugins/highcharts/code/modules/series-label.js","plugins/printjs/print.min.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['savings_data'] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7');
        $this->data['title'] = $this->data['sub_title'] = "Dashboard";
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['shares_module'] = $this->organisation_model->get_module_access(12, $this->session->userdata('organisation_id'));
        $this->data['savings_module'] = $this->organisation_model->get_module_access(6, $this->session->userdata('organisation_id'));
        $client_id = $this->session->userdata("id");
        $this->data['has_changed_password'] = $this->User_model->has_already_set_password($client_id);
        $this->template->title = $this->data['title'];
        $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);

        $this->template->content->view('client/index', $this->data,TRUE);

        // Publish the template
        $this->template->publish();
    }

    public function print_client_data(){
        $this->load->model('organisation_model');
        $this->load->model('branch_model');
        $this->load->model('Address_model');
        $this->load->model('contact_model');
        $this->load->model('Employment_model');
        $this->load->model('NextOfKin_model');
        $this->load->model('shares_model');
        $this->load->model('client_loan_model');
        $this->load->model('loan_guarantor_model');
        $this->load->helper('pdf_helper');
        $this->data['font'] = 'helvetica';
        $this->data['fontSize'] = 7;
        $id = (int)$this->input->post('id');
      
        $this->data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = "CLIENT BIO DATA";
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        //===================== PRINT OUT DATA AND VIEW ====================
         $this->data['module_list']=$this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
         $this->data['modules'] =array_column($this->data['module_list'],"module_id");
        
        $this->data['user'] = $this->member_model->get_member($id);
        $this->data['savings_accs'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)  +ifnull(charges, 0) + ifnull( amount_locked, 0) ) > 0 and j.state_id = 7 AND a.client_type=1 AND member_id = $id");
        
        $this->data['address'] = $this->Address_model->get_addresses($id);
        $this->data['contact'] = $this->contact_model->get($id);
        $filename = $this->data['user']['firstname']."".$this->data['user']['lastname']."".$this->data['user']['othernames']."BIO DATA";
        $this->data['filename'] = $filename;
        
        $this->data['employment'] = $this->Employment_model->get($id);
        $this->data['nextofkin'] = $this->NextOfKin_model->get($id);
        $this->data['shares'] = $this->shares_model->get("fms_share_account.member_id=$id");
        $this->data['loans'] = $this->client_loan_model->get_loans("a.member_id=$id");
 
        $data['the_page_data']  = $this->load->view('client/member_print_out_latest', $this->data,TRUE);
        echo json_encode($data);
 
    }
}
