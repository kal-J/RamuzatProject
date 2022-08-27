<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loans extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
       
        $this->load->model('client_loan_model');
        $this->load->model('member_model');
        $this->load->model('Staff_model');
        $this->load->model('loan_product_model');
        $this->load->model('penalty_calculation_method_model');
        $this->load->model('accounts_model');
        $this->load->model('repayment_schedule_model');
        $this->load->model('miscellaneous_model');
        $this->load->model('dashboard_model');
        $this->load->model('organisation_model');
        $this->load->model('branch_model');
        $this->load->model('loan_collateral_model');
        $this->data['fiscal_active'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
        if(empty($this->data['fiscal_active'])){
            redirect('u/home');
        }
        
    }

    public function index($group_loan_id = false) {
        $this->load->model('transactionChannel_model');
        $this->load->model("loan_doc_type_model");
        $this->load->model('loan_guarantor_model');
        $this->load->model('user_income_type_model');
        $this->load->model('user_expense_type_model');
        $this->load->model('loan_product_fee_model');
        $this->load->model('RolePrivilege_model');
        $this->load->model('dashboard_model');
        $this->load->model('loan_fees_model');
        $this->load->library("num_format_helper");

        $this->data['case2'] = 'My Loans';        
        $this->data['memberloan'] = 'member_loan';
        $this->data['user'] = $this->member_model->get_member($_SESSION['member_id']);
        $this->data['fiscal_active'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        // $this->data['approval_info']=$this->loan_approval_setting_model->get();
        $this->data['savings_accs'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)  +ifnull(charges, 0)+ ifnull( amount_locked, 0) ) > 0 and j.state_id = 7 AND a.client_type=1 AND member_id =".$_SESSION['member_id']);
        if ($group_loan_id == false) {
            $this->data['type'] = $this->data['sub_type'] = 'My Loans';
            $this->data['title'] = $this->data['sub_title'] = 'My Loans';
            $this->data['modal_title'] = 'Individual Loan';
            $this->data['members'] = $this->member_model->get_member_by_user_id("fms_member.status_id=1");
            $this->data['loanProducts'] = $this->loan_product_model->get_product("loan_product.status_id=1 AND loan_product.available_to_id=3 OR loan_product.available_to_id=1");
        } else {
            $this->load->model('group_loan_model');
            $this->load->model('Group_model');
            $this->data['group_id'] = $group_loan_id;
            $this->data['type'] = $this->data['sub_type'] = 'group_loan';
            $this->data['modal_title'] = 'Group Loan';
            $this->data['title'] = $this->data['sub_title'] = 'Group Loans';
            $this->data['group_loan_details'] = $this->group_loan_model->get($group_loan_id);
            $this->data['groups'] = $this->Group_model->get_group($this->data['group_loan_details']['group_id']);
            $this->data['loanProducts'] = $this->loan_product_model->get_product($this->data['group_loan_details']['loan_product_id']);
            $this->data['loan_type'] = $this->miscellaneous_model->get_loan_type();
            $this->data['members'] = $this->member_model->get_member_by_user_id("fms_member.id IN (SELECT member_id from fms_group_member WHERE status_id=1 AND group_id =" . $this->data['group_loan_details']['group_id'] . " AND member_id NOT IN  ( SELECT member_id from fms_client_loan WHERE group_loan_id = " . $group_loan_id . " ) AND status_id=1)");
        }  
        $this->data['modules']=$this->organisation_model->get_module_access(6,$this->session->userdata('organisation_id'));
        $this->data['loan_doc_types'] = $this->loan_doc_type_model->get();
        $this->data['relationship_types'] = $this->miscellaneous_model->get_relationship_type();
        $this->data['collateral_types'] = $this->loan_collateral_model->get_collateral_type();
        $this->data['guarantors'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + 
        ifnull( transfer ,0)  +ifnull(charges, 0) + ifnull( amount_locked, 0) ) >= 0 and j.state_id = 7 AND a.client_type=1 AND a.member_id <>".$_SESSION['member_id']);
         $this->data['income_items'] = $this->user_income_type_model->get();
        $this->data['expense_items'] = $this->user_expense_type_model->get();
        $this->data['available_loan_fees'] = $this->loan_product_fee_model->get();
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 3');
        
        //$this->data['installments'] = $this->repayment_schedule_model->get('payment_status <> 1 AND repayment_schedule.status_id=1');
        $this->data['active_loans'] = $this->client_loan_model->get_loans('loan_state.state_id=7');
        $this->data['account_list'] = $this->accounts_model->get();


        $this->data['available_loan_range_fees'] = $this->loan_fees_model->get_range_fees();
        $this->data['staffs'] = $this->Staff_model->get_registeredby("status_id=1");
        $this->data['penalty_calculation_method'] = $this->penalty_calculation_method_model->get();
        $this->data['repayment_made_every'] = $this->miscellaneous_model->get();
        $this->data['new_loan_acc_no'] = $this->num_format_helper->new_loan_acc_no();
        $this->template->title = $this->data['title'];
        $this->data['tchannel'] = $this->transactionChannel_model->get();
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['branch'] = $this->branch_model->get($_SESSION['branch_id']);

        $rand_no = mt_rand(1000, 1200);
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js","plugins/steps/jquery.steps.min.js?v=$rand_no", "plugins/printjs/print.min.js");//,"plugins/steps/jquery.steps.fix.js"
        $neededcss = array("plugins/select2/select2.min.css","plugins/daterangepicker/daterangepicker-bs3.css","plugins/steps/jquery.steps.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->content->view('client/loans/index', $this->data);
        // Publish the template
        $this->template->publish();
    }
    public function jsonList() {
        $data['draw'] = intval($this->input->post('draw'));
        $all_data = $this->client_loan_model->get();
        $data['data'] = $this->client_loan_model->get_dTable();
        $filteredl_records_cnt = $this->client_loan_model->get_found_rows();
        //total records
        $data['recordsTotal'] = count($all_data);
        $data['recordsFiltered'] = current($filteredl_records_cnt);
        print_r(json_encode($data)); die;
    }

    public function create() {

        $this->form_validation->set_rules('requested_amount', 'Requested amount', array('required'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
               if (empty($_POST['loan_type_id']) || (isset($_POST['loan_type_id']) && $_POST['loan_type_id']==1)) {//updating a pure group loan or an Individual loan
                    if ($this->client_loan_model->update()) {//updating the loan in the client loan table
                        
                        if (isset($_POST['group_loan_id']) && is_numeric($_POST['group_loan_id'])) {//checking if this loan was a group loan
                            if (isset($_POST['loan_type_id']) && $_POST['loan_type_id']==1) {//Do this if this is a pure group loan
                                $this->load->model('group_loan_model');
                                if ($this->group_loan_model->update()) {
                                    $feedback['success'] = true;
                                    $feedback['message'] = "Loan application successfully updated";
                                    $feedback['client_loan'] = $this->client_loan_model->get_client_loan("a.id=".$_POST['id']." AND a.group_loan_id=" . $_POST['group_loan_id']);
                                }

                            }else{//Do this if this is a solidarity group loan
                                $feedback['success'] = true;
                                $feedback['message'] = "Loan application successfully updated";
                                $feedback['client_loan'] = $this->client_loan_model->get_client_loan("a.id=".$_POST['id']." AND a.group_loan_id=" . $_POST['group_loan_id']);
                            }
                        }else {//Do this if this is an individual
                            $feedback['success'] = true;
                            $feedback['message'] = "Loan application successfully updated";
                            $feedback['client_loan'] = $this->client_loan_model->get_client_loan($_POST['id']);
                        }
                    } else {//failure to update the client loan table do this
                        $feedback['message'] = "There was a problem updating the Loan application, please try again or get in touch with the admin";
                    }
               }else{//updating aloan that has been pure and your taking it to solidarity  type

                    if (isset($_POST['group_loan_id']) && is_numeric($_POST['group_loan_id']) && $_POST['loan_type_id']==2) {//checking if it's true it is set to solidarity
                        if ($this->client_loan_model->delete_by_id()) {//Delete this pure loan from a client table before changing to solidarity
                            $this->load->model('group_loan_model');
                            if ($this->group_loan_model->update()) {//update the group loan table
                                $feedback['success'] = true;
                                $feedback['message'] = "Loan application successfully updated";
                                $feedback['group_loan']=1;//redirect the user to the group loan
                            }
                        }
                } else {
                    $feedback['message'] = "There was a problem updating the Loan application, please try again or get in touch with the admin";
                }

               }
            } else {
                $loan_ref_no = $this->generate_loan_ref_no();
                if ($client_loan_id = $this->client_loan_model->set( $loan_ref_no )) {
                    $this->load->model('loan_state_model');
                    if ($this->loan_state_model->set($client_loan_id)) {
                        $feedback['success'] = true;
                        $feedback['loan_ref_no'] = ++$loan_ref_no;
                        $feedback['message'] = "Loan application details successfully saved";
                        if (isset($_POST['group_loan_id']) && is_numeric($_POST['group_loan_id'])) {
                            $this->load->model('group_loan_model');
                            $feedback['group_loan_details'] = $this->group_loan_model->get($_POST['group_loan_id']);
                            $feedback['members'] = $this->member_model->get_member_by_user_id("fms_member.id IN (SELECT member_id from fms_group_member WHERE status_id=1 AND group_id = ( SELECT group_id FROM fms_group_loan WHERE id = " . $_POST['group_loan_id'] . " ) AND member_id NOT IN  ( SELECT member_id from fms_client_loan WHERE group_loan_id = " . $_POST['group_loan_id'] . " ) AND status_id=1)");
                        }
                    } else {
                        $this->client_loan_model->delete_by_id($client_loan_id);
                        $feedback['message'] = "There was a problem saving the Loan application , please try again";
                    }
                } else {
                    $feedback['message'] = "There was a problem saving the Loan application";
                }
            }
        }
        echo json_encode($feedback);
    }
    public function create2() {
        //print_r($this->input->post('application_date'));
        //$client_loan_id = $this->client_loan_model->set(); die();
        $this->form_validation->set_rules('requested_amount', 'Requested amount', array('required'));
        $this->form_validation->set_rules('loan_product_id', 'Loan Product', array('required'));
        // $this->form_validation->set_rules('credit_officer_id', 'Credit officer', array('required'));
        $this->form_validation->set_rules('interest_rate', 'Interest rate', array('required'));
        $this->form_validation->set_rules('grace_period', 'Grace period', array('required'));
        $this->form_validation->set_rules('offset_period', 'Offset period', array('required'));
        $this->form_validation->set_rules('offset_made_every', 'Offset made every', array('required'));
        $this->form_validation->set_rules('installments', 'Number of installments', array('required'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
                #loan application submision
                $loan_ref_no = $this->generate_loan_ref_no();
                $this->db->trans_begin();
                $client_loan_id = $this->client_loan_model->set($loan_ref_no);
                if (is_numeric($client_loan_id)) {

                    #Loan state update
                    $this->load->model('loan_state_model');
                    $this->loan_state_model->set($client_loan_id);
                    
                    #adding client's income sources for a month
                    if ($this->input->post('incomes') !=NULL && $this->input->post('incomes') !='') {
                        $this->load->model('client_loan_monthly_income_model');
                        $this->client_loan_monthly_income_model->set2($client_loan_id); 
                    }
                    #adding client's expende details
                    if ($this->input->post('expenses') !=NULL && $this->input->post('expenses') !='' ) {
                        $this->load->model('client_loan_monthly_expense_model');
                        $this->client_loan_monthly_expense_model->set2($client_loan_id);
                    }
                    #Adding guarantors
                    if ($this->input->post('guarantors') !=NULL && $this->input->post('guarantors') !='' ) {
                        $this->load->model('loan_guarantor_model');
                        $this->loan_guarantor_model->set2($client_loan_id);
                    }

                      #Adding Members as guarantors i.e without savings or shares attached
                    if ($this->input->post('member_guarantors') !=NULL && !empty($this->input->post('member_guarantors')) && $this->input->post('member_guarantors') !='' ) {
                        $this->load->model('guarantor_model');
                        $this->guarantor_model->add($client_loan_id);

                    }

                    #adding the client's savings account
                    if ($this->input->post('savingAccs') !=NULL && $this->input->post('savingAccs') !='' ) {
                        $this->load->model('loan_attached_saving_accounts_model');
                        $this->loan_attached_saving_accounts_model->set($client_loan_id);
                    }

                    #recording the approved loan details
                    if ($this->input->post('loan_app_stage') == 1 || $this->input->post('loan_app_stage') == 2) {
                        $this->load->model('loan_approval_model');
                        $this->client_loan_model->approve($client_loan_id);
                        $this->loan_approval_model->set($client_loan_id);
                    }
                     #adding loan fees for this loan application
                    if ($this->input->post('repayment_schedule') !=NULL && $this->input->post('repayment_schedule') !='' && $this->input->post('loan_app_stage') == 2) {
                        $this->load->model('repayment_schedule_model');
                        $this->repayment_schedule_model->set($client_loan_id);
                        $this->client_loan_model->update_source_fund($client_loan_id);
                        $this->do_journal_transaction($client_loan_id);
                    }
                    #adding loan collateral
                    if ($this->input->post('collaterals') !=NULL && $this->input->post('collaterals') !='' ) {//need to upload
                        $organisation_id = isset($_SESSION['organisation_id']) ? $_SESSION['organisation_id'] :'unknown';
                        $location = 'organisation_'.$organisation_id.'/loan_docs/collateral/';
                        $collaterals=$this->input->post('collaterals');
                        #multiple uploading
                        foreach ($collaterals as $key => $value) {
                             $file[$key]='';
                            if (array_key_exists($key, $_FILES['file_name']['name'])) {
                                $file_name=$_FILES['file_name']['name'][$key];
                                if (!empty($file_name)) {                                   
                                 $file[$key]=$this->do_upload($file_name,$location); 
                                }                              
                            }
                            
                        }
                        #insertion in the db
                        $this->load->model('loan_collateral_model');
                        $this->loan_collateral_model->set($client_loan_id,$file);
                    }
                    #attaching clients loan documents
                    if ($this->input->post('loan_docs') !=NULL && $this->input->post('loan_docs') !='' ) {//need to upload
                        $organisation_id = isset($_SESSION[ 'organisation_id' ]) ? $_SESSION[ 'organisation_id' ] : 0;
                        $location = 'organisation_'. $organisation_id .'/loan_docs/other_docs/';
                        $loan_docs=$this->input->post('loan_docs');
                        #uploading the files
                        foreach ($loan_docs as $key => $value) {
                            $file[$key]='';
                            if (array_key_exists($key, $_FILES['file_name']['name'])) {
                                $file_name=$_FILES['file_name']['name'][$key];
                                if (!empty($file_name)) {
                                 $file[$key]=$this->do_upload($file_name,$location);
                                }                                
                            }
                        }
                        #db insertion
                        $this->load->model('client_loan_doc_model');
                        $this->client_loan_doc_model->set($client_loan_id, $file);
                    }
                    #adding loan fees for this loan application
                    if ($this->input->post('loanFees') !=NULL && $this->input->post('loanFees') !='' ) {
                        $this->load->model('applied_loan_fee_model');
                        $this->applied_loan_fee_model->set($client_loan_id);
                    }
                    if ($this->input->post('preferred_payment_id') !=NULL && $this->input->post('preferred_payment_id') !='1' ) {
                        $this->load->model('payment_details_model');
                        $this->payment_details_model->set($client_loan_id);
                    }
                    if ($this->db->trans_status()) {
                        $this->db->trans_commit();
                        $feedback['success'] = true;
                        $feedback['loan_ref_no'] = ++$loan_ref_no;
                        $feedback['message'] = "Loan application details successfully saved";
                        if (isset($_POST['group_loan_id']) && is_numeric($_POST['group_loan_id'])) {
                            $this->load->model('group_loan_model');
                            $feedback['group_loan_details'] = $this->group_loan_model->get($_POST['group_loan_id']);
                            $feedback['members'] = $this->member_model->get_member_by_user_id("fms_member.id IN (SELECT member_id from fms_group_member WHERE status_id=1 AND group_id = ( SELECT group_id FROM fms_group_loan WHERE id = " . $_POST['group_loan_id'] . " ) AND member_id NOT IN  ( SELECT member_id from fms_client_loan WHERE group_loan_id = " . $_POST['group_loan_id'] . " ) AND status_id=1)");
                        }
                    }else {
                        $this->db->trans_rollback();
                        $feedback['message'] = "There was a problem saving the Loan application, please try again";
                    }
                    
                }else {
                    $feedback['message'] = "There was a problem saving the Loan application";
                }
        }
        echo json_encode($feedback);
    }
    private function do_journal_transaction($client_loan_id){
        $this->load->model('journal_transaction_model');
        $this->load->model('loan_product_model');
        $action_date= date('d-m-Y');
        if ($this->input->post('action_date') !== NULL) {
            $action_date=$this->input->post('action_date');
        }
        $client_loan = $this->client_loan_model->get_client_data($client_loan_id);
        $principal_amount=round($this->input->post('principal_value'));
        $interest_amount=round($this->input->post('interest_value'));
        $data = [
            'transaction_date'=>  $action_date,
            'description'=> $this->input->post('comment'),
            'ref_no'=> $client_loan['loan_no'],
            'ref_id'=> $client_loan_id,
            'status_id'=> 1,
            'journal_type_id'=> 4
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        //then we prepare the journal transaction lines
        if(!empty($client_loan)){
            $this->load->model('accounts_model');
            $this->load->model('journal_transaction_line_model');

            $loan_product_details = $this->loan_product_model->get_accounts($client_loan['loan_product_id']);

            $Loan_account_details = $this->accounts_model->get($loan_product_details['loan_receivable_account_id']);
            $source_fund_ac_details = $this->accounts_model->get($this->input->post('source_fund_account_id'));
            $Interest_receivable_ac_details = $this->accounts_model->get($loan_product_details['interest_receivable_account_id']);
            $Interest_income_ac_details = $this->accounts_model->get($loan_product_details['interest_income_account_id']);

            $debit_or_credit1 = ($Loan_account_details['normal_balance_side']==1)?'debit_amount':'credit_amount';
            $debit_or_credit2 = ($source_fund_ac_details['normal_balance_side'] ==1)?'credit_amount':'debit_amount';//Although the normal balancing side is debit side, in this scenario money is being given out so we shall instead credit it.
            $debit_or_credit3= ($Interest_income_ac_details['normal_balance_side']==1)?'debit_amount':'credit_amount';
            $debit_or_credit4 = ($Interest_receivable_ac_details['normal_balance_side']==1)?'debit_amount':'credit_amount';
            $data = [
                [
                    $debit_or_credit1=> $principal_amount,
                    'narrative'=> "Loan Disbursement on ". $action_date,
                    'account_id'=>$loan_product_details['loan_receivable_account_id'],
                    'status_id'=> 1
                ],
                [
                    $debit_or_credit2=> $principal_amount,
                    'narrative'=> "Loan Disbursement on ". $action_date,
                    'account_id'=> $this->input->post('source_fund_account_id'),
                    'status_id'=> 1
                ],
                [
                    $debit_or_credit3=> $interest_amount,
                    'narrative'=> "Loan Disbursement on ". $action_date,
                    'account_id'=> $loan_product_details['interest_income_account_id'],
                    'status_id'=> 1
                ],
                [
                    $debit_or_credit4=> $interest_amount,
                    'narrative'=> "Loan Disbursement on ". $action_date,
                    'account_id'=> $loan_product_details['interest_receivable_account_id'],
                    'status_id'=> 1
                ]
            ];
            $this->journal_transaction_line_model->set($journal_transaction_id,$data);
            /*print_r($income_account_details);die($this->input->post('transaction_channel_id'));*/
        }
    }
    private function do_upload($file_name,$location, $max_size = 2048, $allowed_types = "gif|jpg|jpeg|png|pdf"){
        //uploading of the file
            if (!empty($file_name) && !empty($location)) {
                $config['upload_path'] = APPPATH . "../uploads/$location/";
                $document_name =$config['file_name']= $file_name;
                $config['allowed_types'] = $allowed_types;
                $config['max_size'] = $max_size;
                $config['max_filename'] = 120;
                $config['overwrite'] = true;
                $config['remove_spaces'] = false;
                $config['file_ext_tolower'] = true;
                $this->load->library('upload', $config);
                if(!$this->upload->do_multi_upload('file_name')){
                    return $this->upload->display_errors();                
                }else{
                    $this->upload->data();
                    return $document_name;
                }
            }
    }
    public function view($loan_id_or_group_loan_id = false, $call_type = false) {
        $this->load->model("loan_doc_type_model");
        $this->load->model("loan_product_fee_model");
        $this->load->model("savings_account_model");
        $this->load->model("group_loan_model");
        $this->load->model('loan_guarantor_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('user_income_type_model');
        $this->load->model('user_expense_type_model'); 
        $this->load->model('RolePrivilege_model');    
        $this->load->library(array("form_validation", "helpers"));
        if ($loan_id_or_group_loan_id == false && $call_type == false) {
            redirect("my404");
        } else if ($loan_id_or_group_loan_id != false && $call_type == false) {
            $this->data['loan_detail'] = $this->client_loan_model->get_client_loan($loan_id_or_group_loan_id);
            if (empty($this->data['loan_detail'])) {
                redirect("my404");
            }

            $this->data['loan_detail']['total_penalty'] = $this->get_total_penalty($this->data['loan_detail']['id']);
            
            $this->data['module_list']=$this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
            $this->data['modules'] =array_column($this->data['module_list'],"module_id");
            $this->data['title'] = 'Client loan details';
            $this->data['case2'] = 'client_loan';
            $this->data['modal_title'] = $this->data['loan_detail']['member_name'];
            if ($this->data['loan_detail']['group_loan_id'] !='') {
                $this->data['type'] = $data['sub_type'] = 'client_loan';
                $this->data['loanProducts'] = $this->loan_product_model->get_product("loan_product.status_id=1 AND loan_product.available_to_id=3 OR loan_product.available_to_id=2");
                $this->data['members'] = $this->member_model->get_member_by_user_id("fms_member.id IN (SELECT member_id from fms_group_member WHERE status_id=1 AND group_id = ( SELECT group_id FROM fms_group_loan WHERE id = " . $this->data['loan_detail']['group_loan_id'] . " ) AND status_id=1)"); 
                $this->data['group_loan_details'] = $this->group_loan_model->get($this->data['loan_detail']['group_loan_id']);           
            }else{
                $this->data['type'] = $data['sub_type'] = 'client_loan';
                $this->data['members'] = $this->member_model->get_member_by_user_id("fms_member.status_id=1");
                 $this->data['loanProducts'] = $this->loan_product_model->get_product("loan_product.available_to_id=3 OR loan_product.available_to_id=1");
            }

            $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
            $member_id = $this->data['loan_detail'] ['member_id'];
            $loan_id = $loan_id_or_group_loan_id;
            //loan details for payment
            $this->data['installments'] = $this->repayment_schedule_model->get("payment_status <> 1 AND repayment_schedule.status_id=1 AND client_loan_id=". $loan_id);
            $this->data['active_loans'] = $this->client_loan_model->get_loans("loan_state.state_id=7 AND a.id= ".$loan_id);
            //End of the data
            //savings accounts
            $this->data['savings_accs'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0) +ifnull(charges, 0) + ifnull( amount_locked, 0) ) > 0 and j.state_id = 7 AND a.client_type=1 AND member_id = '" . $member_id . "' AND a.id NOT IN "
                    . "(SELECT saving_account_id from fms_loan_attached_saving_accounts WHERE loan_id = '" . $loan_id . "' and status_id = 1 )");
        } else {
            $this->load->model("Group_model");
            $this->data['loan_detail'] = $this->client_loan_model->get_client_loan("a.group_loan_id= ".$loan_id_or_group_loan_id);
            if (empty($this->data['loan_detail'])) {
                redirect("my404");
            }
            $loan_id = $this->data['loan_detail'] ['id'];

            $this->data['loan_detail']['total_penalty'] = $this->get_total_penalty($this->data['loan_detail']['id']);

            //loan details for payment
            $this->data['installments'] = $this->repayment_schedule_model->get("payment_status <> 1 AND repayment_schedule.status_id=1 AND client_loan_id=". $loan_id);
            $this->data['active_loans'] = $this->client_loan_model->get_loans("loan_state.state_id=7 AND a.group_loan_id= ".$loan_id_or_group_loan_id,'group_loan');
            //End of the data

            $this->data['account_list'] = $this->accounts_model->get();
            $this->data['title'] = 'Group loan details';
            $this->data['type'] = $this->data['sub_type'] = 'client_loan';
            $this->data['case2'] = 'group_loan';
            $this->data['modal_title'] = $this->data['loan_detail']['group_name'];
            $this->data['group_loan_details'] = $this->group_loan_model->get($loan_id_or_group_loan_id);
            $group_id = $this->data['group_loan_details'] ['group_id'];
            $this->data['groups'] = $this->Group_model->get_group("status_id=1");
            $this->data['loanProducts'] = $this->loan_product_model->get_product("loan_product.status_id=1 AND loan_product.available_to_id=3 OR loan_product.available_to_id=2");
            $this->data['loan_type'] = $this->miscellaneous_model->get_loan_type();
            $this->data['members'] = $this->member_model->get_member_by_user_id("fms_member.id IN (SELECT member_id from fms_group_member WHERE status_id=1 AND group_id =" . $group_id . " AND member_id NOT IN  ( SELECT member_id from fms_client_loan WHERE group_loan_id = " . $this->data['group_loan_details']['id'] . " ) AND status_id=1)");
            $this->data['savings_accs'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0) +ifnull(charges, 0)+ ifnull( amount_locked, 0) ) > 0 and j.state_id = 7 AND a.client_type=2 AND member_id = '" . $group_id . "' AND a.id NOT IN "
                    . "(SELECT saving_account_id from fms_loan_attached_saving_accounts WHERE loan_id = '" . $loan_id . "' )");
        }

        //data for payment purposes
         $this->data['account_list'] = $this->accounts_model->get();
        //end of the variables
        $this->template->title = $this->data['title'];
        $this->data['loan_doc_types'] = $this->loan_doc_type_model->get();
        $this->data['collateral_types'] = $this->loan_collateral_model->get_collateral_type();
        $this->data['relationship_types'] = $this->miscellaneous_model->get_relationship_type();
        $this->data['staffs'] = $this->Staff_model->get_registeredby("status_id=1");
        $this->data['penalty_calculation_method'] = $this->penalty_calculation_method_model->get();
        $this->data['repayment_made_every'] = $this->miscellaneous_model->get();
        $this->data['guarantors'] = $this->loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + 
        ifnull( transfer ,0)  +ifnull(charges, 0) + ifnull( amount_locked, 0) ) >= 0 and j.state_id = 7 AND a.client_type=1");
        $this->data['available_loan_fees'] = $this->loan_product_fee_model->get(" loanproduct_id = '" . $this->data['loan_detail']['loan_product_id'] . "' and fms_loan_fees.id not in 
        ( SELECT loan_product_fee_id from fms_applied_loan_fee WHERE client_loan_id = '" . $loan_id . "' and status_id = 1 ) ");
        // member_income and Expeniture
        $this->data['income_items'] = $this->user_income_type_model->get();
        $this->data['expense_items'] = $this->user_expense_type_model->get();
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 3');
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/datepicker/bootstrap-datepicker.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/datepicker/datepicker3.css");
        $this->data['tchannel'] = $this->transactionChannel_model->get();
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->content->view('client/loans/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function get_total_penalty($client_loan_id)
    {
        $loan_details = $this->client_loan_model->get_client_loan($client_loan_id);
        // print_r($loan_details);die();

        $penalty_applicable_after_due_date = $loan_details['penalty_applicable_after_due_date'];
        $fixed_penalty_amount = $loan_details['fixed_penalty_amount'];
        $penalty_calculation_method_id = $loan_details['penalty_calculation_method_id'];
        $last_pay_date = $loan_details['last_pay_date'];
        // $penalty_rate_charged_per = $loan_details['penalty_rate_charged_per'];
        // $next_pay_date = $loan_details['next_pay_date'];

        $total_penalty = 0;
        $data['data'] = $this->repayment_schedule_model->get($client_loan_id);

        foreach ($data['data'] as $key => $value) {
            $due_installments_data = $this->repayment_schedule_model->due_installments_data($value['id']);

            if (!empty($due_installments_data)) {
                $over_due_principal = $due_installments_data['due_principal'];
                if ($value['demanded_penalty'] > 0) {
                    $number_of_late_days = $due_installments_data['due_days2'];
                } else {
                    $number_of_late_days = $due_installments_data['due_days'] - $due_installments_data['grace_period_after'];
                }

                ##
                if (intval($penalty_calculation_method_id) == 1) {
                    $penalty_rate = (($due_installments_data['penalty_rate']) / 100);
                } else {
                    $penalty_rate = 1;
                }

                //echo json_encode($due_installments_data['penalty_rate_charged_per']); die;

                if ($due_installments_data['penalty_rate_charged_per'] == 4) { // One time penalty 
                    $number_of_late_period = 1;
                } elseif ($due_installments_data['penalty_rate_charged_per'] == 3) {
                    $number_of_late_period = intdiv($number_of_late_days, 30);
                } elseif ($due_installments_data['penalty_rate_charged_per'] == 2) {
                    $number_of_late_period = intdiv($number_of_late_days, 7);
                } else {
                    $number_of_late_period = $number_of_late_days;
                }


                if (intval($penalty_calculation_method_id) == 2) { // Fixed amount Penalty

                    $penalty_value = $due_installments_data['penalty_rate_charged_per'] == 4 ? ($due_installments_data['paid_penalty_amount'] > 0 ? 0 : ($fixed_penalty_amount * $number_of_late_period)) : ($fixed_penalty_amount * $number_of_late_period);
                } else {

                    $penalty_value = $due_installments_data['penalty_rate_charged_per'] == 4 ? ($due_installments_data['paid_penalty_amount'] > 0 ? 0 : ($over_due_principal * $number_of_late_period * $penalty_rate)) : ($over_due_principal * $number_of_late_period * $penalty_rate);
                }


                if ((intval($penalty_applicable_after_due_date) == 1)) {

                    if ($last_pay_date >= date('Y-m-d')) {
                        $penalty_value = 0;
                    }
                }


                $data['data'][$key]['penalty_value'] = $value['demanded_penalty'] > 0 ? round($penalty_value + $value['demanded_penalty'], 0) : round($penalty_value, 0);
            } else {
                $data['data'][$key]['penalty_value'] = $value['demanded_penalty'];
            }
            $total_penalty += $data['data'][$key]['penalty_value'];
        }

        return $total_penalty;
    }

    public function change_status() {
        if (in_array('7', $this->privileges)) {
            $this->data['success'] = FALSE;
            $this->data['message'] = $this->client_loan_model->change_status_by_id();
            if ($this->data['message'] === true) {
                $this->data['success'] = TRUE;
                $this->data['message'] = "Loan application data successfully deactivated.";
            }
        } else {
            $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        }
        echo json_encode($this->data);
    }

    public function delete() {
            $response['success'] = FALSE;
            if ($this->client_loan_model->delete_by_id()) {
                $response['success'] = TRUE;
                $response['message'] = "Data successfully deleted.";
            }
        echo json_encode($response);
    }

    //checking requirements for approving a loan
    public function get_approval_data() {
        //Model for loading
        $this->load->model('loan_guarantor_model');
        $this->load->model('approving_staff_model');
        $this->load->model('loan_approval_setting_model');
        $this->load->model('Loan_guarantor_model');
        $response['success'] = FALSE;
        $loan_id = $this->input->post('id');
        $requested_amount = $this->input->post('requested_amount');
        if (!(empty($loan_id))) {
            $this->data['collateral_sum'] = $this->loan_collateral_model->sum_loan_collateral($loan_id);
            $this->data['guarantor_count'] = $this->loan_guarantor_model->count_loan_guarantor($loan_id);
            $this->data['staff_approval_data'] = $this->approving_staff_model->get(intval($requested_amount),$loan_id);
            $this->data['min_approvals'] = $this->loan_approval_setting_model->min_approvals(intval($requested_amount));
            $this->data['staff_list'] = $this->approving_staff_model->approval_staff_list($requested_amount,$loan_id);

            $member_id = $this->input->post('member_id');
            $this->load->model('loan_product_model');
            if (!(empty($member_id)) && $member_id != '') {
                $this->data['account_savings'] = $this->Loan_guarantor_model->get_guarantor_savings("(j.state_id = 7) AND a.client_type=1 AND a.member_id=" . $member_id);
                $this->data['product_details'] = $this->loan_product_model->get_product($this->input->post('loan_product_id'));
            } else {
                $this->load->model('group_loan_model');
                $group_loan_id = $this->input->post('group_loan_id');
                $this->data['group_loan_details'] = $this->group_loan_model->get($group_loan_id);
                $this->data['account_savings'] = $this->Loan_guarantor_model->get_guarantor_savings("(j.state_id = 7) AND a.client_type=2 AND a.member_id=" . $this->data['group_loan_details']['group_id']);
                $this->data['product_details'] = $this->loan_product_model->get_product($this->data['group_loan_details']['loan_product_id']);
            }
            $response['success'] = True;
            $response['approval_data']['rank'] = $this->data['staff_approval_data']['rank'];
            $response['approval_data']['approval_status'] = $this->data['staff_approval_data']['approved_or_not'];
            $response['approval_data']['collateral_sum'] = floatval($this->data['collateral_sum']['loan_collateral_value']);
            $response['approval_data']['guarantor_count'] = intval($this->data['guarantor_count']['loan_guarantor']);
            $response['approval_data']['guarantor_amount_locked_sum'] = intval($this->data['guarantor_count']['loan_guarantor_value']);
            $response['approval_data']['savings_details'] = $this->data['account_savings'];
            $response['approval_data']['staff_list'] = $this->data['staff_list'];
            $response['approval_data']['min_approvals'] = $this->data['min_approvals']['required_approvals'];
            $response['selected_product'] = $this->data['product_details'];
            //print_r($this->data['product_details']); die();
        }

        echo json_encode($response);
    }

//End of the get_approval_data function
    //checking whether the date is a weekend;
    function isWeekend($date) {
        $received_date = date('Y-m-d', $date);
        $weekDay = date('w', strtotime($received_date));
        if ($weekDay == 0 || $weekDay == 6) {
            return true;
        } else {
            if ($this->isHoliday($date)) {
                return true;
            } else {
                return false;
            }
        }
    }

     //checking whether the date is a public holiday;
    function isHoliday($date) {
        //Model for loading
        $this->load->model('holiday_model');
        //setting default timezone
        date_default_timezone_set('Africa/Kampala');
        $holiday_data = $this->holiday_model->get();
        if (empty($holiday_data)) {
            return false;
        } else {
            foreach ($holiday_data as $key => $value) {
                if ($value['every'] == 'Constant') {
                    $holidays[] = $value['holiday'];
                } else if ($value['every'] == 'Good_Friday') {
                    $the_year = date('Y', $date);
                    $the_easter_sunday = date('Y-m-d', easter_date($the_year));
                    $holidays[] = date("m-d", strtotime("-2 day", strtotime($the_easter_sunday)));
                } else if ($value['every'] == 'Easter_Sunday') {
                    $the_year = date('Y', $date);
                    $the_easter_sunday = date('Y-m-d', easter_date($the_year));
                    $holidays[] = date("m-d", strtotime($the_easter_sunday));
                } else if ($value['every'] == 'Easter_Monday') {
                    $the_year = date('Y', $date);
                    $the_easter_sunday = date('Y-m-d', easter_date($the_year));
                    $holidays[] = date("m-d", strtotime("+1 day", strtotime($the_easter_sunday)));
                } else {
                    $year = date('Y', $date);
                    $month = strtolower($value['month']);
                    $day = strtolower($value['day']);
                    $every = strtolower($value['every']);
                    $holidays[] = date('m-d', strtotime("$every $day of $month $year"));
                }
            }
            $date = date('m-d', $date);
            if (in_array($date, $holidays)) {
                return true;
            } else {
                return false;
            }
        }
    }

    //disbursement
    public function disbursement(){
        $response['success'] = FALSE;
        $loan_id = $this->input->post('id');
        if (!empty($this->input->post('action_date'))) {//in case disbursement happen before today
            $new_date=$this->input->post('action_date');
            $now_date=date('d-m-Y',strtotime($new_date));
        }elseif($this->input->post('new_repayment_date') !==NULL){ //in the case of reschedule a loan & loan Amortization
            $now_date=$this->input->post('new_repayment_date');
            $new_date=$now_date=date('d-m-Y',strtotime($now_date));
        }else{ //in case disbursement is happening today           
            $now_date=date('d-m-Y');   
        }

        if ($this->input->post('loan_product_id')!==NULL && empty($loan_id)) { //useful for loan amortization
            $this->data['loan_data']=$loan_data=$this->loan_product_model->get_product($_POST['loan_product_id']);
        }else{ //finding the payment reschedule
            $this->data['loan_data']=$loan_data=$this->client_loan_model->get_client_data($loan_id);
        }
          
        $response=$this->schedule_calculation($this->data['loan_data'], $now_date);

        echo json_encode($response);
    }//End of the disbursement function

    public function disbursement1(){//Used when loan is from application to active state
        $response['success'] = FALSE;
        if (!empty($this->input->post('action_date1'))) {//in case disbursement happen before today
            $new_date=$this->input->post('action_date1');
            $now_date=date('d-m-Y',strtotime($new_date));
        }else{ //in case disbursement is happening today           
            $now_date=date('d-m-Y');   
        }
        $this->data['loan_data']['offset_period']=$this->input->post('offset_period1');
        $this->data['loan_data']['offset_made_every']=$this->input->post('offset_made_every1');
        $this->data['loan_data']['amount_approved']=$this->input->post('amount1');
        $this->data['loan_data']['product_type_id']=$this->input->post('product_type_id1');
        $this->data['loan_data']['interest_rate']=$this->input->post('interest_rate1');
        $this->data['loan_data']['approved_installments']=$this->input->post('installments1');
        $this->data['loan_data']['approved_repayment_made_every']=$this->input->post('repayment_made_every1');
        $this->data['loan_data']['approved_repayment_frequency']=$this->input->post('repayment_frequency1');
        $this->data['loan_data']['loan_product_id']=$this->input->post('loan_product_id1');
        $response=$this->schedule_calculation($this->data['loan_data'], $now_date);
        echo json_encode($response);
    }

    private function schedule_calculation($required_data, $now_date){
        $response=[];
        if (!(empty($required_data))) {
                    $response['success'] = True;
                    //if new repayment frequency has been sent
                    if (!empty($_POST['repayment_frequency'])) {
                        $repayment_frequency=$this->input->post('repayment_frequency');
                    }else{
                        $repayment_frequency=intval($this->data['loan_data']['approved_repayment_frequency']);
                    }

                    if (!empty($_POST['repayment_made_every'])) {//if new repayment made every has been sent
                        $repayment_made=$this->input->post('repayment_made_every');
                        //determination of the first payment date
                        $payment_date=$now_date = strtotime($now_date);
                        $payment_date1=date('Y-m-d',$payment_date);
                    }else{//
                        $repayment_made=intval($this->data['loan_data']['approved_repayment_made_every']);
                        //consideration of offset period incase of disbursement
                        if ($this->data['loan_data']['offset_made_every']==1) {
                            $off_set='+'.$this->data['loan_data']['offset_period'].' day';
                        }elseif ($this->data['loan_data']['offset_made_every']==2) {
                            $off_set='+'.$this->data['loan_data']['offset_period'].' week';
                        }else{
                        $off_set='+'.$this->data['loan_data']['offset_period'].' month';
                        }

                        //determination of the first payment date
                        $payment_date=$now_date = strtotime($off_set, strtotime($now_date));
                        $payment_date1=date('Y-m-d',$payment_date);
                    }

                    if ($repayment_made==1) {
                        $schedule_date=$repayment_frequency.' day';
                        $repayment_made_every= '365';
                   }elseif ($repayment_made==2) {
                        $schedule_date=$repayment_frequency.' week';
                        $repayment_made_every= '52';
                   }else{
                    $schedule_date=$repayment_frequency.' month';
                    $repayment_made_every= '12';
                   }
                    $product_type=$this->data['loan_data']['product_type_id'];
                    //if new interest rate has been sent
                    if (!empty($_POST['interest_rate'])) {
                        $interest_rate=$this->input->post('interest_rate');
                    }else{
                        $interest_rate=$this->data['loan_data']['interest_rate'];                    
                    }
                    
                    $r=$interest_rate_per_annum=$interest_rate_per_installment=((($interest_rate)*1)/100); 

                    $l=$length_of_a_period=($repayment_frequency/$repayment_made_every);

                    $i=$interest_rate_per_period=($r*$l);
                    //Intialize
                    $paid_principal=0;
                    //if current installment has been sent
                    if ((!empty($_POST['installments'])) &&(!empty($_POST['current_installment'])) ) {
            
                        $installment_number=$this->input->post('current_installment');
                        $where="fms_repayment_schedule.client_loan_id=".$this->input->post('id')." AND fms_repayment_schedule.installment_number < $installment_number AND fms_repayment_schedule.status_id=1";
                        $this->data['paid']=$this->repayment_schedule_model->sum_interest_principal($where);
                        $n=$number_of_installments=$this->input->post('installments');
                        $paid_amount=$this->data['paid']['principal_sum'];
                        $p=$Original_Loan_Amount=$outstanding_loan_amount=floatval($this->data['loan_data']['amount_approved'])-$paid_amount;
                    }elseif(!empty($_POST['amount'])){
                        $n=$number_of_installments=$this->input->post('installments');
                        $p=$Original_Loan_Amount=$outstanding_loan_amount=floatval($this->input->post('amount'));
                    }else{ 
                    $p=$Original_Loan_Amount=$outstanding_loan_amount=floatval($this->data['loan_data']['amount_approved']);
                    $n=$number_of_installments=$this->data['loan_data']['approved_installments'];                   
                    }

                    if (!empty($_POST['top_up_amount'])) {
                            $p=$Original_Loan_Amount=$outstanding_loan_amount=$p+$_POST['top_up_amount'];
                    }
                    $number_of_years=$n*$l;
                    
                    if(!empty($_POST['amount'])){
                        $amount_approved=floatval($this->input->post('amount')); 
                    }else{
                        $amount_approved=floatval($this->data['loan_data']['amount_approved']);                         
                    }
                    //if current installment has been sent
                    if (!empty($_POST['current_installment'])) {
                        $current_installment=$this->input->post('current_installment');
                        $y= $current_installment-1;
                    }else{
                        $y= 0;                    
                    }
                     $x=0;
                    $response['payment_summation']['interest_amount']=0;
                    $response['payment_summation']['principal_amount']=0;
                    $response['payment_summation']['paid_principal']=0;
                    $response['payment_summation']['payment_date']=0;
                    
                    if ($product_type==2) {//for dynamic Term loan, interest amount calculated on reducing balance 
                            //paid installment, this includes both the interest+paid principal
                            $EMI =($i*$amount_approved)/ (1- pow((1+$i),-$n));
                       
                        for ($y; $y <$n; $y++) { 
                            if ( (!empty($_POST['current_installment'])) && ($y ==($current_installment-1)) && $y != ($n-1) ) {
                                $payment_date2 = $payment_date1;
                                $response['payment_schedule'][$x]['payment_date']=$payment_date;
                            }elseif ( (!empty($_POST['current_installment'])) && ($y ==($current_installment-1)) && $y == ($n-1) ) {
                                $payment_date2=$payment_date1;
                                $response['payment_schedule'][$x]['payment_date']=$payment_date;
                            }elseif ((!empty($_POST['amount'])) && ($y ==0) ) {
                                $payment_date2 = $payment_date1;
                                $response['payment_schedule'][$x]['payment_date']=$payment_date;
                            }else{
                                $payment_date = strtotime($schedule_date, strtotime(date('Y-m-d', $payment_date)));
                               if ($this->isWeekend($payment_date)) {
                                    $payment_date=date('Y-m-d',$payment_date);
                                    $payment_date= strtotime('+1 Weekday', strtotime($payment_date));
                                    while($this->isHoliday($payment_date)) {
                                        $payment_date=date('Y-m-d',$payment_date);
                                        $payment_date= strtotime('+1 Weekday', strtotime($payment_date));
                                    }
                                    $response['payment_schedule'][$x]['payment_date']=$payment_date;
                                    $payment_date2 = date('Y-m-d', $payment_date);
                                }else{
                                    $response['payment_schedule'][$x]['payment_date']=$payment_date;
                                    $payment_date2  = date('Y-m-d', $payment_date);
                                }
                            }
                            //installment number
                            $response['payment_schedule'][$x]['installment_number']=$y+1;
                            //interest amount paid per installment alone
                            $response['payment_schedule'][$x]['interest_amount']=$interest_amount_per_installment= round(($i*$p),2);
                            $response['payment_summation']['interest_amount']+=round($interest_amount_per_installment);
                            //principal amount payable
                            $response['payment_schedule'][$x]['principal_amount']=$principal_amount=round(($EMI-$interest_amount_per_installment),2);
                            $response['payment_summation']['principal_amount']+=round($principal_amount);
                            //total principal amount paid 
                            $response['payment_schedule'][$x]['paid_principal']=$paid_principal=round($EMI,2);
                            $response['payment_summation']['paid_principal']+=$paid_principal;
                            //outstanding_balance
                            $p=$p-$principal_amount;                            
                           $x++;
                        }
                    }else{
                        //for Fixed Term loan, interest amount calculated flat rate
                        for ($y; $y <$n; $y++) {
                             if ( (!empty($_POST['current_installment'])) && ($y ==($current_installment-1)) && $y != ($n-1) ) {
                                $payment_date2 = $payment_date1;
                                $response['payment_schedule'][$x]['payment_date']=$payment_date;
                            }elseif ( (!empty($_POST['current_installment'])) && ($y ==($current_installment-1)) && $y == ($n-1) ) {
                                $payment_date2=$payment_date1;
                                $response['payment_schedule'][$x]['payment_date']=$payment_date;
                            }elseif ((!empty($_POST['amount'])) && ($y ==0) ) {
                                $payment_date2 = $payment_date1;
                                $response['payment_schedule'][$x]['payment_date']=$payment_date;
                            }else{
                                $payment_date = strtotime($schedule_date, strtotime(date('Y-m-d', $payment_date)));
                                if ($this->isWeekend($payment_date)) {
                                    $payment_date = date('Y-m-d', $payment_date);
                                    $payment_date = strtotime('+1 Weekday', strtotime($payment_date));
                                    while ($this->isHoliday($payment_date)) {
                                        $payment_date = date('Y-m-d', $payment_date);
                                        $payment_date = strtotime('+1 Weekday', strtotime($payment_date));
                                    }
                                    $response['payment_schedule'][$x]['payment_date'] = $payment_date;
                                    $payment_date2 = date('Y-m-d', $payment_date);
                                } else {
                                    $response['payment_schedule'][$x]['payment_date'] = $payment_date;
                                    $payment_date2 = date('Y-m-d', $payment_date);
                                }
                            }
                            //installment number
                            $response['payment_schedule'][$x]['installment_number']=$y+1;
                            $response['payment_schedule'][$x]['interest_amount']=$interest_amount_per_installment=round((($amount_approved*$number_of_years*$r)/$n),2);
                            $response['payment_summation']['interest_amount']+=round($interest_amount_per_installment);;
                            $response['payment_schedule'][$x]['principal_amount']=$principal_amount=round(($amount_approved/$n),2);
                            $response['payment_summation']['principal_amount']+=round($principal_amount);;
                            $response['payment_schedule'][$x]['paid_principal']=$paid_principal=round(($interest_amount_per_installment+$principal_amount),2);
                             $response['payment_summation']['paid_principal']+=$paid_principal;
                             $x++;
                        }
                    }
            $dStart = new DateTime($payment_date1);
            $dEnd = new DateTime($payment_date2);
            $dDiff = $dStart->diff($dEnd);
            $y=($dDiff->y)?((($dDiff->y)>1)?$dDiff->y.' years ':$dDiff->y.' year '):'';
            $m=($dDiff->m)?((($dDiff->m)>1)?$dDiff->m.' months ':$dDiff->m.' month '):'';
            $d=($dDiff->d)?((($dDiff->d)>1)?$dDiff->d.' days ':$dDiff->d.' day '):'';
            $response['payment_summation']['payment_date']=$y.$m.$d;
        }
         //$response['balance_message']=$this->helpers->account_balance($this->input->post('fund_source_account_id'),$this->input->post('amount_approved'));

        return $response;
    }
 
    public function delete_loan_doc() {
        $response['message'] = "Data could not be deleted, document support.";
        $response['success'] = FALSE;
        if ($this->client_loan_doc_model->delete_by_id()) {
            $response['success'] = TRUE;
            $response['message'] = "Data successfully deleted.";
        }
        echo json_encode($response);
    }

    //Approving a loan application
    public function approve() {
        $this->load->model('loan_approval_model');
        $this->load->model('loan_approval_setting_model');
        $response['message'] = "Loan application could not be approved, contact IT support.";
        $response['success'] = FALSE;
        if (!empty($_POST['amount_approved']) && $_POST['rank']==1) {
            if ($this->client_loan_model->approve()) {
                $inserted_id = $this->loan_approval_model->set();
                if (is_numeric($inserted_id)) {
                    $requested_amount = $this->input->post('requested_amount');
                    $loan_id = $this->input->post('client_loan_id');
                    //commited approvals
                    $this->data['approvals'] = $this->loan_approval_model->sum_approvals($loan_id);
                    $approvals = $this->data['approvals']['approvals'];
                    //required approvals
                    $this->data['required_approvals'] = $this->loan_approval_setting_model->min_approvals(intval($requested_amount));
                    $required_approvals = $this->data['required_approvals']['required_approvals'];
                    //checking approvals requirements
                    if ($approvals == $required_approvals) {
                        $this->load->model('loan_state_model');
                        if ($this->loan_state_model->set()) {
                            $response['success'] = TRUE;
                            $response['message'] = "Loan application successfully approved.";
                            if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] !='') {
                                $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=".$_POST['client_loan_id']." AND a.group_loan_id=".$_POST['group_loan_id']);
                            }else{
                                $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
                            }
                        } else {
                            $this->loan_approval_model->delete_by_id($inserted_id);
                            $feedback['message'] = "There was a problem approving this loan application, please try again";
                        }
                    } else {
                        $response['success'] = TRUE;
                        $response['message'] = "Loan application successfully approved.";
                        if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] !='') {
                            $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=".$_POST['client_loan_id']." AND a.group_loan_id=".$_POST['group_loan_id']);
                        }else{
                            $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
                        }
                    }
                } else {
                    $feedback['message'] = "Loan application could not be approved!";
                }
            } else {
                $feedback['message'] = "Loan application could not be approved!";
            }
        } else {   
            $inserted_id = $this->loan_approval_model->set();
            if (is_numeric($inserted_id)) {
                $requested_amount = $this->input->post('requested_amount');
                $loan_id = $this->input->post('client_loan_id');
                //commited approvals
                $this->data['approvals'] = $this->loan_approval_model->sum_approvals($loan_id);
                $approvals = $this->data['approvals']['approvals'];
                //required approvals
                $this->data['required_approvals'] = $this->loan_approval_setting_model->min_approvals(intval($requested_amount));
                $required_approvals = $this->data['required_approvals']['required_approvals'];
                //checking approvals requirements
                if ($approvals == $required_approvals) {
                    $this->load->model('loan_state_model');
                    if ($this->loan_state_model->set()) {
                        $response['success'] = TRUE;
                        $response['message'] = "Loan application successfully approved.";
                        if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] !='') {
                            $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=".$_POST['client_loan_id']." AND a.group_loan_id=".$_POST['group_loan_id']);
                        }else{
                            $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
                        }
                    } else {
                        $this->loan_approval_model->delete_by_id($inserted_id);
                        $feedback['message'] = "There was a problem approving this loan application, please try again";
                    }
                } else {
                    $response['success'] = TRUE;
                    $response['message'] = "You have successfully approved the loan application .";
                    if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] !='') {
                        $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=".$_POST['client_loan_id']." AND a.group_loan_id=".$_POST['group_loan_id']);
                    }else{
                        $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
                    }
                }
            } else {
                $feedback['message'] = "There was a problem approving this loan application, please try again";
            }
        }
        echo json_encode($response);
    }

    public function generate_loan_ref_no() {
        $this->load->library("num_format_helper");
        $new_loan_acc_no = $this->num_format_helper->new_loan_acc_no();
        return $new_loan_acc_no===FALSE?$this->input->post("loan_no"):$new_loan_acc_no;  
    }

    public  function pdf_appraisal( $loan_id, $transaction_no=false ) {
        $this->load->model( 'client_loan_model' );
        $this->load->model( 'applied_loan_fee_model' );
        $this->load->model( 'loan_guarantor_model' ); 
        $this->load->model( 'address_model' );
        $this->load->model( 'children_model' );
        $this->load->model( 'member_model' );
        $this->load->model( 'nextOfKin_model' );
        $this->load->model( 'employment_model' );        
        $this->load->model('client_loan_monthly_expense_model');
        $this->load->model('client_loan_monthly_income_model');

        $this->load->helper('pdf_helper');

        $data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = "Loan disbursed details";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 8;
        $where = FALSE;
        $where = "a.status_id = 1";
        $where = ($where?$where . " AND ":"")." a.client_loan_id = ".$loan_id;        
        $data['loan_guarantors'] = $this->loan_guarantor_model->get( $where );
        $data['loan_detail'] = $this->client_loan_model->get_client_loan( $loan_id );
        $user_id = $data['loan_detail']['user_id'];
        $member_id = $data['loan_detail']['member_id'];
        $data['loan_detail_prev'] = $this->client_loan_model->get_prev_client_loan( $member_id );
        $data['addresses'] = $this->address_model->get_addresses("ua.id in (select max(id) from fms_user_address)  
        and ua.user_id=". $user_id);
        $data[ 'nextofkins' ] = $this->nextOfKin_model->get( $user_id );
        $data[ 'children' ] = $this->children_model->get( $member_id );
        $data[ 'employments' ] = $this->employment_model->get( $user_id );
        $data['repayment_schedules'] = $this->repayment_schedule_model->get( $loan_id );
        $data['guarantors'] = $this->loan_guarantor_model->get('a.client_loan_id='.$loan_id);
        $data['collaterals'] = $this->loan_collateral_model->get( "client_loan_id=" . $loan_id );
        $data['users'] = $this->member_model->get_member( $member_id );
        $data['applied_fees'] = $this->applied_loan_fee_model->get();        
        $data['monthly_incomes'] = $this->client_loan_monthly_income_model->get( 'a.client_loan_id='.$loan_id );
        $data['monthly_expenses'] = $this->client_loan_monthly_expense_model->get( 'a.client_loan_id='.$loan_id );
        //echo '<pre>'; print_r( $data['monthly_incomes'] ).'</pre>'.die;
        $data['the_page_data'] = $this->load->view('client_loan/states/partial/pdf_appraisal', $data, TRUE);
        $this->load->view('includes/pdf_template', $data);
    }

    public function pdf_loan_fact_sheet( $loan_id, $transaction_no=false ){
        $this->load->model('client_loan_model');
        $this->load->model('loan_guarantor_model'); 
        $this->load->model('Business_model' );               
        $this->load->helper('pdf_helper');

        $data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = "Loan disbursed details";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 8;
        $where = FALSE;
        $where = "a.status_id = 1";
        $where = ($where?$where . " AND ":"")." a.client_loan_id = ".$loan_id;        
        $data['loan_guarantors'] = $this->loan_guarantor_model->get( $where );
        $data['loan_detail'] = $this->client_loan_model->get_client_loan( $loan_id );
        $user_id = $data['loan_detail']['user_id'];
        $member_id = $data['loan_detail']['member_id'];        
        $data['repayment_schedules'] = $this->repayment_schedule_model->get( $loan_id );
        $data['repayment_schedule'] = isset($data['repayment_schedules'][0])?$data['repayment_schedules'][0]:[];
        $data['guarantors'] = $this->loan_guarantor_model->get('a.client_loan_id='.$loan_id);
        $data['collaterals'] = $this->loan_collateral_model->get( "client_loan_id=" . $loan_id );
        $data['business'] = $this->Business_model->get( $member_id );
        // echo '<pre>'; print_r( $data['business'] ).'</pre>'.die;
        $data['the_page_data'] = $this->load->view('client_loan/states/approved/pdf_loan_fact_sheet', $data, TRUE);
        $this->load->view('includes/pdf_template', $data);
    }


    # Get requestable min & max amounts
    public function get_requestable_loan_amounts()
    {
        $this->load->model('shares_model');
        $this->load->model('loan_guarantor_model');
        $this->load->model('loan_product_model');

        $member_id = $this->input->post('member_id');
        $amount = $this->input->post('amount');
        $this->data['account_shares'] = $this->shares_model->get_share_guarantor("share_account.member_id=" . $member_id);
        $this->data['account_savings'] = $this->loan_guarantor_model->get_guarantor_savings("(j.state_id = 7) AND a.client_type=1 AND a.member_id=" . $member_id);

        $product_details = $this->loan_product_model->get_product($this->input->post('loan_product_id'));
        //echo json_encode($product_details); die;

        $min_collateral_percentage = $product_details['min_collateral'];
        $product_max = $product_details['max_amount'];
        $product_min = $product_details['min_amount'];

        $savings_sum = $shares_sum = 0;
        $col_total = 0;
        $max = 0;

        foreach ($this->data['account_savings'] as $key => $value) {
            $savings_sum += $value['real_bal'];
        }

        foreach ($this->data['account_shares'] as $key => $value) {
            $shares_sum += $value['total_amount'];
        }

        if (intval($product_details['use_savings_as_security']) == 1) {
            $col_total += $savings_sum;
        }
        if (intval($product_details['use_shares_as_security']) == 1) {
            $col_total += $shares_sum;
        }

        if (intval($product_details['mandatory_sv_or_sh']) == 1) {
            $max = $col_total / ($min_collateral_percentage / 100);

            if ($max < $product_max) {
                $product_max = $max;
            }
        }

        $data = [
            'min' => $product_min, 
            'max' => $product_max, 
            'savings_total' => $savings_sum, 
            'shares_total' => $shares_sum
        ];

        if(!empty($amount)) {
            // calculate Needed collateral for Amount
            $needed_col_total = $amount * ($min_collateral_percentage / 100);
            $needed_col = $needed_col_total <= $col_total ? 0 : ($needed_col_total - $col_total);
            if (intval($product_details['mandatory_sv_or_sh']) == 1) {
               $data['needed_col'] = $needed_col; 
            }
            
        }



        echo json_encode($data);
    }


}
