<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("contact_model");
        $this->load->model("member_model");
        $this->load->model('Staff_model');
        $this->load->model('UserRole_model');
        $this->load->library('form_validation');
        $this->load->model('user_model');
        $this->load->model('Position_model');
        $this->load->model('Fiscal_month_model');
        $this->load->model("organisation_format_model");
        $this->load->model('District_model');
         $this->load->model('Branch_model');
         $this->load->model('Transaction_date_control_model');
        $this->load->library("helpers");

        $this->data['privilege_list'] = $this->helpers->user_privileges(2, $_SESSION['staff_id']);
        $this->data['staff_priv_list'] = $this->helpers->user_privileges(1, $_SESSION['staff_id']);
        $this->data['client_loan_list'] = $this->helpers->user_privileges(4, $_SESSION['staff_id']);
        $this->data['subs_list'] = $this->helpers->user_privileges(9, $_SESSION['staff_id']);
        $this->data['membership_list'] = $this->helpers->user_privileges(21, $_SESSION['staff_id']);
        $this->data['savings_prev_list'] = $this->helpers->user_privileges(6, $_SESSION['staff_id']);
        $this->data['shares_prev_list'] = $this->helpers->user_privileges(12, $_SESSION['staff_id']);
        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 2, $_SESSION['organisation_id']);
        $this->data['allowed_transaction_dates'] = $this->Transaction_date_control_model->generate_allowed_dates();
        if (empty($this->data['module_access'])) {
            redirect('my404');
        } else {
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['member_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
            $this->data['staff_privilege'] = array_column($this->data['staff_priv_list'], "privilege_code");
            $this->data['subscription_privilege'] = array_column($this->data['subs_list'], "privilege_code");
            $this->data['membership_privilege'] = array_column($this->data['membership_list'], "privilege_code");
            $this->data['client_loan_privilege'] = array_column($this->data['client_loan_list'], "privilege_code");
            $this->data['savings_privilege'] = array_column($this->data['savings_prev_list'], "privilege_code");
            $this->data['share_privilege'] = array_column($this->data['shares_prev_list'], "privilege_code");
            $this->data['member_staff_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
            $this->data['till_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
          
            $this->data['member_referral_info'] = $this->member_model->get_member_referral_list1();
       
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
        $this->load->model('Role_model');
        $this->load->model('subscription_plan_model');
        $this->load->library("num_format_helper");
        $this->data['title'] = $this->data['sub_title'] = $this->lang->line('cont_client_name_p');
        $this->template->title = $this->data['title'];
        $this->load->model('miscellaneous_model');
        $this->data['organisation_format'] = $this->organisation_format_model->get_formats();
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        //member referral check on or off
        $this->data['member_referral'] = $this->data['org']['member_referral'];
       
        $this->data['new_client_no'] = $this->num_format_helper->new_client_no();
        $this->data['subscription_plans'] = $this->subscription_plan_model->get("subscription_plan.organisation_id = " . $_SESSION['organisation_id']);
        $this->data['marital_statuses'] = $this->miscellaneous_model->get_marital_status_options();
        $this->data['roles'] = $this->Role_model->get_active_roles($status_id=1);
        $this->data['positions'] = $this->Position_model->get();
        $this->data['staff_list'] = $this->Staff_model->get_registeredby("status_id=1");
        $this->data['sorted_users'] = $this->Dashboard_model->get_all_system_users('status=1');
       
        //branch list:
        $this->data['branch_list'] = $this->Branch_model->get();

        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 3');
        
        $this->data['module_list']=$this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] =array_column($this->data['module_list'],"module_id");
        $neededjs = array("plugins/validate/jquery.validate.min.js","plugins/select2/select2.full.min.js");
        $neededcss = array("plugins/select2/select2.min.css");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        // Load a view in the content partial
        $this->template->content->view('user/member/members_list', $this->data);
       
        // Publish the template
        $this->template->publish();
    }

    public function jsonList() {
        $data['data'] = $this->member_model->get_member();
        $all_members = $this->member_model->get();
        $data['pagination']['more'] = (count($all_members) > ($this->input->post("page") * 50));
        echo json_encode($data);
    }

    public function get_members_data() {
        $data['data'] = $this->member_model->get_member_data($this->input->post('search_term'));
        echo json_encode($data);
    }


    public function members_birthdays(){
        $this->member_model->member_birthday();
    }
    public function jsonList2() {
        $data['draw'] = intval($this->input->post('draw'));
        $data['data'] = $this->member_model->get_dtable_format();
        $filtered_records_cnt = $this->member_model->get_found_rows();
        $where ="u.gender =".$this->input->post('gender');
        $all_data = $this->member_model->get1($where);
        //total records
        $data['recordsTotal'] = $all_data;
        $data['recordsFiltered'] = count($data['data']);
        echo json_encode($data);
    }

    public function member_personal_info($id = false){
        if ($id == false) {
            redirect("my404");
        } else {
            $this->data['user'] = $this->member_model->get_member($id);
            if (empty($this->data['user'])) {
                redirect("my404");
            }
        }
        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] = array_column($this->data['module_list'], "module_id");
        $this->load->library("num_format_helper");
        $this->data['sorted_users'] = $this->Dashboard_model->get_all_system_users('status=1');
       
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        //member referral check on or off
        $this->data['member_referral'] = $this->data['org']['member_referral'];
       

        /*  $this->data['org_module_list']=$this->organisation_model->get_org_modules($this->session->userdata('organisation_id'));
          $this->data['modules_org'] =array_column($this->data['org_module_list'],"module_id"); */

        $this->load->model('address_model');
        $this->load->model('employment_model');
        $this->load->model("document_model");
        $this->load->model('client_loan_model');
        $this->load->model('Loan_product_model');
        $this->load->model('penalty_calculation_method_model');
        $this->load->model('miscellaneous_model');
        $this->load->model("organisation_format_model");
        $this->load->model("DepositProduct_model");
        $this->load->model("TransactionChannel_model");
        $this->load->model("RolePrivilege_model");
        $this->load->model("Savings_account_model");
        $this->load->model("user_doc_type_model");
        $this->load->model("Member_fees_model");
        $this->load->model("Signature_model");
        $this->load->model('transactionChannel_model');
        $this->load->model('user_income_type_model');
        $this->load->model("subscription_plan_model");
        $this->load->model('accounts_model');
        $this->load->model('repayment_schedule_model');
        $this->load->model("loan_doc_type_model");
        $this->load->model('loan_guarantor_model');
        $this->load->model('user_expense_type_model');
        $this->load->model('loan_product_fee_model');
        $this->load->model('loan_collateral_model');
        $this->load->model('dashboard_model');
        $this->load->model('loan_fees_model');
        $this->load->model('Applied_member_fees_model');
        $this->load->model('shares_model');
        $this->load->model('Share_issuance_model');
        $this->load->model('share_call_model');
        $this->load->model('Children_model');
        $this->load->model('member_collateral_model');

        $this->data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
         $this->data['loan_doc_types'] = $this->loan_doc_type_model->get();
        $this->data['collateral_types'] = $this->loan_collateral_model->get_collateral_type();
        $this->data['all_collaterals'] = $this->member_collateral_model->get_not_attached_to_active_loan('member_id='. $id);
        $this->data['share_issuances'] = $this->Share_issuance_model->get(['share_issuance.status_id', 1]);
        $this->data['firstcall'] = $this->share_call_model->get_first_calls(null);

        // print_r(json_encode($this->data['all_collaterals']));
        // die;

        $this->data['members'] = $this->member_model->get_member_by_user_id();
        // returns single member in an array
        $member_arr = $this->member_model->get_member_by_user_id("fms_member.id=".$id);
        $this->data['member'] = $member_arr[0]; 

        $this->data['guarantors'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + 
        ifnull( transfer ,0)  +ifnull(charges, 0) + ifnull( amount_locked, 0) ) >= 0 and j.state_id = 7 AND a.client_type=1 AND a.member_id <>".$id);
        $this->data['savings_accs'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)  +ifnull(charges, 0) + ifnull( amount_locked, 0) ) > 0 and j.state_id = 7 AND a.client_type=1 AND member_id = $id");
        $this->data['share_guarantors'] = $this->shares_model->get("share_state.state_id = 7");

        $this->data['share_accs'] = $this->shares_model->get("share_state.state_id = 7");

        $this->data['income_items'] = $this->user_income_type_model->get();
        $this->data['expense_items'] = $this->user_expense_type_model->get();
        $this->data['available_loan_fees'] = $this->loan_product_fee_model->get();
        $this->data['case2'] = '';

        $this->data['tchannel'] = $this->transactionChannel_model->get();
        //added ambrose.
         $this->data['branch_list'] = $this->Branch_model->get_branch();

        $this->data['memberloan'] = 'member_loan';
        
        $this->data['user_doc_types'] = $this->user_doc_type_model->get_doc_type();
        $this->data['contact_types'] = $this->contact_model->get_contact_type();
        $this->data['address_types'] = $this->address_model->get_address_types();
        $this->data['relationship_types'] = $this->miscellaneous_model->get_relationship_type();
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 3');

        // $this->data['payment_modes_other'] = $this->miscellaneous_model->get_payment_mode('id <> 3 ');

        $this->data['nature_of_employment'] = $this->employment_model->get_nature_of_employment();
        $this->data['new_client_no'] = $this->num_format_helper->new_client_no();
        $this->data['districts'] = $this->District_model->get_districts();
//==================== SAVINGS DATA
        $this->data['tchannel'] = $this->TransactionChannel_model->get();
        $this->data['products'] = $this->DepositProduct_model->get("sp.status_id=1 AND availableto IN (1,3)");
        //$this->data['format_types'] = $this->organisation_format_model->get_format_types(false);
        $this->data['organisation'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['new_account_no'] = $this->num_format_helper->new_savings_acc_no();
        $this->data['users'] = $this->member_model->get_member();
        $this->data['sorted_clients'] = $this->Savings_account_model->get_clients();
        $this->data['pay_with'] = $this->accounts_model->get_pay_with("10");
        
        //==================== END SAVINGS DATA ===================
//==================== START LOAN DATA ==================== 
        $this->data['staffs'] = $this->Staff_model->get_registeredby("status_id=1");
        $this->data['loanProducts'] = $this->Loan_product_model->get_product("loan_product.status_id=1 AND loan_product.available_to_id=3 OR loan_product.available_to_id=1");
        //$this->data['members'] = $this->member_model->get_member_by_user_id("fms_member.status_id=1");
        $this->data['penalty_calculation_method'] = $this->penalty_calculation_method_model->get();
        $this->data['repayment_made_every'] = $this->miscellaneous_model->get();
        $this->data['marital_statuses'] = $this->miscellaneous_model->get_marital_status_options();
        $this->data['new_loan_acc_no'] = $this->num_format_helper->new_loan_acc_no();
       // $this->data['new_loan_no'] = $this->client_loan_model->get_id();
       // $this->data['installments'] = $this->repayment_schedule_model->get('payment_status <> 1 AND repayment_schedule.status_id=1');
        $this->data['ac_state_totals'] = $this->Savings_account_model->state_totals("a.member_id=".$id);
        $this->data['state_totals'] = $this->client_loan_model->state_totals("a.member_id=".$id);
        $this->data['active_loans'] = $this->client_loan_model->get_loans('(loan_state.state_id=7 OR loan_state.state_id=13) AND member_id='.$id);
        $this->data['account_list'] = $this->accounts_model->get();        
        $this->data['available_loan_range_fees'] = $this->loan_fees_model->get_range_fees();
//==================== END LOAN DATA ==================== 
        $this->data['user_signature'] = $this->Signature_model->get(["fms_user_signatures.user_id" => $this->data['user']['user_id']]);

//==================== START SUBSCRIPTION PLAN DATA ==================== 
        $this->data['subscription_plans'] = $this->subscription_plan_model->get("subscription_plan.organisation_id = " . $_SESSION['organisation_id']);
        if ($this->data['user']['subscription_plan_id']) {
            $this->data['subscription_plan'] = $this->subscription_plan_model->get($this->data['user']['subscription_plan_id']);
        }
//==================== END SUBSCRIPTION PLAN DATA ====================

//==================== START CHILDREN DATA ====================
        $this->data['children'] = $this->Children_model->get('status_id=1 AND member_id='. $id);
//==================== END CHILDREN DATA ====================

        $this->data['title'] = $this->data['sub_title'] = $this->data['user']['firstname'] . " " . $this->data['user']['lastname'] . " " . $this->data['user']['othernames']. " Profile ";
        $this->data['type'] = $this->data['sub_type'] = "member";
        $this->data['attach_member_fees'] = $this->Member_fees_model->get("fms_member_fees.`id` NOT IN ( SELECT member_fee_id 
            FROM `fms_applied_member_fees` WHERE member_id = '" . $id . "' AND status_id=1)");

        $this->data['available_member_fees'] = $this->Applied_member_fees_model->get("member_id =". $id);

        $this->template->title = $this->data['title'];
        $this->data['modalTitle'] = "Edit ".$this->lang->line('cont_client_name')." Info";

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/cropping/croppie.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js","plugins/steps/jquery.steps.min.js", "plugins/validate/jquery.validate.min.js","plugins/highcharts/code/highcharts.js", "plugins/highcharts/code/highcharts-3d.js", "plugins/highcharts/code/modules/exporting.js", "plugins/highcharts/code/highslide-full.min.js","plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/modules/export-data.js", "plugins/highcharts/code/modules/series-label.js", "plugins/autoNumeric/autoNumeric.min.js","plugins/printjs/print.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/cropping/croppie.css", "plugins/daterangepicker/daterangepicker-bs3.css","plugins/steps/jquery.steps.css","plugins/highcharts/code/css/highslide.css","custom.css");

        $this->data['staff_list'] = $this->Staff_model->get_registeredby();
        $this->data['member_id'] = $id;
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        // Load a view in the content partial
        $this->data['user_nav'] = $this->load->view('user/user_nav', $this->data, TRUE);

        $this->template->content->view('user/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    // updated script for printing 
 
    public function print(){
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
        $id = $this->input->post('id');
        $this->data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = "MEMBER BIO DATA";
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
        $this->data['shares'] = $this->shares_model->get();
        $this->data['loans'] = $this->client_loan_model->get_loans("a.member_id=$id AND state_id=7");
 
        $data['the_page_data']  = $this->load->view('user/member/member_print_out_latest', $this->data, TRUE);
        echo json_encode($data);

        //===================== END HERE AND GENERATE =======================
        //$this->pdfgenerator->generate($html, $filename,$stream,$paper,$orientation);
    }
    public function create() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('firstname', 'Member First Name', 'required');
        $this->form_validation->set_rules('lastname', 'Member Last Name', 'required');
        $this->form_validation->set_rules('registered_by', 'Registered By', 'required');
        $this->form_validation->set_rules('date_registered', 'Date Registered', 'required');
        if ($this->input->post('email') != NULL && $this->input->post('email') !='') {
            $this->form_validation->set_rules("email", "Email", "callback__check_email", array("_check_email" => "%s already exists and possibly the client too"));
        }
         if ($this->input->post('client_no') != NULL && $this->input->post('client_no') !='') {
            $this->form_validation->set_rules("client_no", $this->lang->line('cont_client_no'), "callback__check_client_no", array("_check_client_no" => "Someone with the same %s already exists"));
        }
        if ($this->input->post('id') == NULL) {
            $this->form_validation->set_rules("mobile_number", "Phone Number", "required|valid_phone_int|callback__check_phone_number", array("required" => "%s must be entered", "valid_phone_int" => "%s should start with country code e.g +256 or 0 with a minimum of 10(ten) digits", "_check_phone_number" => "%s already exists and possibly the client too"));
        }
        
        $feedback['success'] = false;

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {
            if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
                //editing exsting item
                if ($this->user_model->update_user()) {
                    if ($this->member_model->update_member()) {
                        $feedback['success'] = true;
                        $feedback['message'] = "Member Details successfully updated";
                        $feedback['user'] = $this->member_model->get_member($this->input->post('id'));
                     $this->helpers->activity_logs($_SESSION['id'],1,"Editing Member",$feedback['message']." # ". $this->input->post('firstname'),NULL,$this->input->post('registered_by'));
                    } else {
                        $feedback['message'] = "There was a problem updating the member this->data, please try again";

                         $this->helpers->activity_logs($_SESSION['id'],1,"Editing Member",$feedback['message']." # ". $this->input->post('firstname'),NULL,$this->input->post('registered_by'));

                    }
                } else {
                    $feedback['message'] = "Member Details could not be updated";

                     $this->helpers->activity_logs($_SESSION['id'],1,"Editing Member",$feedback['message']." # ". $this->input->post('firstname'),NULL,$this->input->post('registered_by'));
                }
            } else {
                //adding a new member
                $client_no = $this->generate_client_no();
                if ($client_no != false) {
                    $inserted_id = $this->user_model->add_user();
                    if ($inserted_id) {
                        $member_id = $this->member_model->add_member($inserted_id, $client_no);
                        if (is_numeric($member_id)) {
                            $inserted_contact_id=$this->contact_model->add_contact($inserted_id);
                            if (is_numeric($inserted_contact_id)) { 

                                $feedback['message'] = "Member has been successfully added";
                                 $this->helpers->activity_logs($_SESSION['id'],1,"Creating Member",$feedback['message']." # ". $this->input->post('firstname'),NULL,$this->input->post('registered_by'));
                            }else{
                                $feedback['message'] = "Member has been successfully added,though contact couldn't be added";
                                   $this->helpers->activity_logs($_SESSION['id'],1,"Creating Member",$feedback['message']." # ". $this->input->post('firstname'),NULL,$this->input->post('registered_by'));
                            }
                            $feedback['success'] = true;
                            $feedback['user'] = $member_id;
                            $feedback['client_no'] = ++$client_no;
                        } else {
                            $this->user_model->delete_by_id($inserted_id);
                            $feedback['message'] = "There was a problem saving the member data, please try again";
                              $this->helpers->activity_logs($_SESSION['id'],1,"Creating Member",$feedback['message']." # ". $this->input->post('firstname'),NULL,$this->input->post('registered_by'));
                        }
                    } else {
                        $feedback['message'] = "Member Details failed to submit!";
                           $this->helpers->activity_logs($_SESSION['id'],1,"Creating Member",$feedback['message']." -# ". $this->input->post('firstname'),NULL,$this->input->post('registered_by'));
                    }
                }
            }
        }

        echo json_encode($feedback);
    }
 public function make_staff(){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user_id', 'User Id', 'required');
        $this->form_validation->set_rules('position_id', 'Position', 'required');
        $this->form_validation->set_rules('role_id', 'System Role', 'required');
        $feedback['success'] = false;

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {
         //adding a new staff
                $staff_no = $this->get_staff_no();
                if ($staff_no != false) {
                    if ($this->input->post('user_id')) {
                        $staff_id = $this->Staff_model->add_staff($this->input->post('user_id'), $staff_no);
                        if (is_numeric($staff_id)) {
                            $this->UserRole_model->set($staff_id);
                            $feedback['message'] = "Staff has been successfully added";
                            $feedback['success'] = true;
                        } else {
                            $feedback['message'] = "There was a problem registering Staff, please try again";
                        }
                    } 
                }
        }
         echo json_encode($feedback);
    }

    function _check_phone_number($phone_number) {
        $existing_number = $this->contact_model->validate_contact($phone_number);
        
        return $existing_number;
    }
    function _check_email($email) {
        $existing_email = $this->user_model->validate_email($email);
        return $existing_email;
    }
     function _check_client_no($client_no) {
        $existing_client_no = $this->user_model->validate_client_no($client_no);
        return $existing_client_no;
    }
    public function delete() {
        $response['success'] = FALSE;
        // if (isset($_SESSION['role']) && isset($_SESSION['role']) == 1) {
        if (($response['success'] = $this->member_model->temporary_delete($this->input->post('id'))) === true) {
            $response['message'] = "Member Details successfully deleted";
        }
        echo json_encode($response);
    }
    public function memberReferrals(){
        $this->load->model('member_model');
        $introduced_by_id= $this->input->post("introduced_by_id");
        $data['data'] = $this->member_model->get_member_referral_list($introduced_by_id);
            
        echo json_encode($data);
       
    }

    public function change_status() {
        $this->load->model('Savings_account_model');
        $this->load->model('Shares_model');
        if(is_numeric($this->input->post('id'))){
        
        $memberId = $this->input->post('id'); 
         $data = array();
        $member_shares_data = $this->Shares_model->get('share_account.member_id='.$memberId);
        $member_savings_data = $this->Savings_account_model->get('member_id='.$memberId);

        if(!empty($member_shares_data ||$member_savings_data)){

         $share_account_id = $member_shares_data[0]['id'];
          
         $data['share_account_id'] = $share_account_id;
         $data['saving_state_id'] = $member_savings_data[0]['state_id'];
         $data['action']= 'update';
         $this->Shares_model->change_state($data);
        }
       
        if(!empty($member_savings_data)){
        $savings_id = $member_savings_data[0]['id'];
        $state_id   =   $member_savings_data[0]['state_id'];

        $data['action']='update';$data['account_id']=$savings_id;$data['state_id']=$state_id;

        $this->Savings_account_model->change_state($data);
        }
        
        }
        $response['success'] = false;
        $msg = $this->input->post('status_id')==1?"":"de";
        $response['message'] = "Member could not be $msg"."activated.";
        if ($this->member_model->change_status_by_id($this->input->post('id'))) {
            $response['success'] = true;
            $response['message'] = "Member successfully $msg"."activated.";
        }
        echo json_encode($response);
          $this->helpers->activity_logs($_SESSION['id'],1,"Changing member status",$response['message']." # ".$this->input->post('id'),NULL,$this->input->post('registered_by'));
    }

    function generate_client_no() {
        $this->load->library("num_format_helper");
        $new_client_no = $this->num_format_helper->new_client_no();
        return $new_client_no===FALSE?$this->input->post("client_no"):$new_client_no; 
    }
    private function get_staff_no() {
        $this->load->library("num_format_helper");
        $new_staff_no = $this->num_format_helper->new_staff_no();
        return $new_staff_no===FALSE?$this->input->post("staff_no"):$new_staff_no; 
    }

}
