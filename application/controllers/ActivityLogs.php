<?php
class ActivityLogs extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->data['privilege_list'] = $this->helpers->user_privileges(12, $this->session->userdata('staff_id'));

        $this->data['module_access'] = $this->helpers->org_access_module(12, $_SESSION['organisation_id']);
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['billing_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        }

        $this->load->model("logs_model");
        
        $fiscal_year = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        if (empty($fiscal_year)) {
            redirect('dashboard');
        } else {
            $this->data['fiscal_active'] = array_merge($fiscal_year, ['start_date2' => date("d-m-Y", strtotime($fiscal_year['start_date'])), 'end_date2' => date("d-m-Y", strtotime($fiscal_year['end_date']))]);
            $this->data['lock_month_access'] = $this->helpers->org_access_module($module_id = 23, $_SESSION['organisation_id']);
            if (!empty($this->data['lock_month_access'])) {
                $this->data['active_month'] = $this->Fiscal_month_model->get_active_month();
                if (empty($this->data['active_month'])) {
                    redirect('dashboard');
                }
            }
        }
    }

    public function jsonList()
    {
        $data['data'] = $this->logs_model->get();
        echo json_encode($data);
    }

    public function get_login_log_list()
    {
        $data['data'] = $this->logs_model->get_login_log_list();
        echo json_encode($data);
    }

    public function index()
    {
        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] = array_column($this->data['module_list'], "module_id");
        $this->load->library("num_format_helper");
        $this->load->model('miscellaneous_model');
        $this->load->model("organisation_format_model");
        $this->load->model("RolePrivilege_model");
        $this->load->model("logs_model");

        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);

        //$this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 3');

        $this->data['title'] = $this->data['sub_title'] = "Activity Log";

        $this->template->title = $this->data['title'];

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css", "custom.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->template->content->view('logs/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    
    public function login_log_details($member_id)
    {
       if ($member_id == false) {
           redirect("my404");
        } else {
            $this->data['user'] = $this->logs_model->get_login_log_list();
            if (empty($this->data['user'])) {
               redirect("my404");
            }
        }

        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] = array_column($this->data['module_list'], "module_id");
        $this->load->library("num_format_helper");
        $this->load->model('miscellaneous_model');
        $this->load->model("organisation_format_model");
        $this->load->model("RolePrivilege_model");

        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);

        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 3');

        $this->data['title'] = "Billing";
        $this->data['sub_title'] = $this->data['user']['firstname'] . " " . $this->data['user']['lastname'];
        $this->data['member_id'] = $member_id;
        $this->template->title = $this->data['title'];

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css", "custom.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->template->content->view('logs/', $this->data);
        // Publish the template
        $this->template->publish();

    }

}