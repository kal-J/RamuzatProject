<?php
/**
 * Description of shares
 *
 * @author Reagan
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Shares extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->library("num_format_helper");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model("shares_model");
        $this->load->model("RolePrivilege_model");
        $this->load->model("Fiscal_month_model");
        $this->load->model("Share_transaction_model");
        $this->load->model('organisation_format_model');
        $this->load->model("Share_issuance_model");
        $this->load->model("Share_state_model");
        $this->load->model("share_call_model");
        $this->load->model("Share_issuance_fees_model");
        $this->load->library(array("form_validation", "helpers"));
        $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
        if(empty($this->data['fiscal_active'])){
            redirect('u/home');
        }

    }

    public function jsonList() {
        $data['data'] = $this->shares_model->get();
       // $data['recordsTotal'] = count($all_data);
        // print_r($data); die;
        echo json_encode($data);

    }

    public function index() {
        $this->load->model("Member_model");
        $this->load->model("Share_issuance_model");
        $this->data['members'] = $this->Member_model->get_member();
        $this->data['share_issuances'] = $this->Share_issuance_model->get(['share_issuance.status_id',1]);
        $this->data['title'] =  $this->data['sub_title'] = 'Shares Accounts';
        $neededjs = array("plugins/validate/jquery.validate.min.js","plugins/select2/select2.full.min.js");
        $neededcss = array("plugins/select2/select2.min.css");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->title = $this->data['title'];
        // Load a view in the content partial
        $this->template->content->view('client/shares/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

  
    public function view($share_id) {
        $this->load->model("share_issuance_fees_model");
        $this->load->model("Share_issuance_model");
        $this->data['share_issuances'] = $this->Share_issuance_model->get(['share_issuance.status_id', 1]);
        $this->load->library(array("form_validation", "helpers"));
        $this->data['title'] = 'Share account details';
        $this->data['modalTitle'] = 'Edit Share';
        $this->template->title = $this->data['title'];
        $neededcss = array("fieldset.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] = array_column($this->data['module_list'], "module_id");

        $this->data['get_share_by_id'] = $this->shares_model->get_by_id($share_id);
        // $this->data['share_price_amount'] = $this->data['get_share_by_id']['share_price'] * $this->data['get_share_by_id']['shares'];
        $this->data['share_price_amount']=20000;
        $this->data['share_detail'] = $this->shares_model->get_share_fee($share_id);
        
        $this->data['share_details'] = $this->shares_model->get(['share_account.id' => $share_id]);
        
        $this->data['available_share_fees'] = $this->share_issuance_fees_model->get("shareproduct_id=" .$share_id);
        //print_r(json_encode($this->data['share_details'])); die;
        
        $this->template->content->view('client/shares/view', $this->data);
        $this->template->publish();
    }


}
