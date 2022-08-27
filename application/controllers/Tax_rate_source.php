<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tax_rate_source extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        
        $this->load->model("tax_rate_source_model");
        $this->load->library(array("form_validation", "helpers"));
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 11, $this->session->userdata('staff_id'));
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['privileges'] = array_column($this->data['privilege_list'], "privilege_code");
        }
    }

    public function jsonList() {
        $this->data['data'] = $this->tax_rate_source_model->get();
        echo json_encode($this->data);
    }

    public function view($tax_rate_source_id) {
        $this->load->model("tax_application_model");
        $this->load->model("miscellaneous_model");

        $this->load->library(array("helpers"));
        $this->data['tax_rate_source'] = $this->tax_rate_source_model->get($tax_rate_source_id);
        if (empty($this->data['tax_rate_source'])) {
            redirect('my404');
        }
        $this->data['title'] = $this->data['tax_rate_source']['source'];
        $this->data['modalTitle'] = "Add/Edit Tax Rates";
        $this->data['saveButton'] = "Update";

        $this->data['available_income_sources'] = $this->miscellaneous_model->get_income_source();
        
        $neededjs = array("plugins/validate/jquery.validate.min.js");
        $neededcss = array();

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->title = $this->data['title'];

        $this->template->content->view('setting/tax_rate_source/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

}
