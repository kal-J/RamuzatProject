<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Group_loan extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('client_loan_model');
        $this->load->model('group_loan_model');
        $this->load->model('Group_model');
        $this->load->model('Member_model');
        $this->load->model('Staff_model');
        $this->load->model('Fiscal_month_model');
        $this->load->model('Loan_product_model');
        $this->load->model('penalty_calculation_method_model');
        $this->load->library(array("form_validation", "helpers"));
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 14, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 14, $_SESSION['organisation_id']);
        if(empty($this->data['module_access'])){
            redirect('my404');
        } else {
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['group_loan_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
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
        $this->load->model('miscellaneous_model');
        $this->load->library("num_format_helper");
        $this->data['type'] = $this->data['sub_type'] = 'group_loan';
        $this->data['modal_title']='Group Loan';
        $this->data['groups'] = $this->Group_model->get_group("status_id=1");
        $this->data['loan_type']= $this->miscellaneous_model->get_loan_type();
        $this->data['staffs'] = $this->Staff_model->get_registeredby("status_id=1");
        $this->data['loanProducts'] = $this->Loan_product_model->get_product("loan_product.status_id=1 AND loan_product.available_to_id=3 OR loan_product.available_to_id=2");
        $this->data['penalty_calculation_method'] = $this->penalty_calculation_method_model->get();
        $this->data['repayment_made_every']= $this->miscellaneous_model->get();
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 3');
        //$this->data['new_group_loan_no'] = $this->num_format_helper->new_group_loan_no();
        $this->data['new_loan_no'] = $this->generate_client_loan_ref_no();
        $this->data['title'] = $this->data['sub_title'] = 'Group Loans';
        $this->template->title = $this->data['title'];
        $this->data['add_loan_modal'] = $this->load->view('client_loan/group_loan/types/solidarity/add_group_loan',$this->data,true);

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->content->view('client_loan/group_loan/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function jsonList() {
        $data['data'] = $this->group_loan_model->get();
        echo json_encode($data);
    }
    public function jsonList_per_group($filter) {
        if($filter==false){
            redirect('my404');
        }
        $data['data'] = $this->group_loan_model->get('group.id='.$filter.' AND group_loan.loan_type_id='. $this->input->post('loan_type_id'));
        echo json_encode($data);
    }


    public function create() {
        $this->load->model('payment_details_model');
        
        $this->form_validation->set_rules('group_id', 'Group Name', array('required'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {

                if ($this->group_loan_model->update()) {
                     if (isset($_POST['loan_type_id']) && $_POST['loan_type_id']==1) {
                        //This means we are updating a group loan and changing its loan type to pure
                        if ($client_loan_id=$this->client_loan_model->set($_POST['id'])) {
                            $this->load->model('loan_state_model');
                            if ($this->loan_state_model->set($client_loan_id)) {                                
                                $this->payment_details_model->deactivate($_POST['id']);
                                $this->payment_details_model->set($_POST['id']);
                                $feedback['success'] = true;
                                $feedback['message'] = "Group Loan application details successfully updated";
                                //loging this action in activity log.
                                $this->helpers->activity_logs($_SESSION['id'],14,"Editing group loan application",$feedback['message']." -# ". $this->input->post('group_id'),NULL,$this->input->post('group_id'));
                            }else{
                                $this->client_loan_model->delete_by_id($client_loan_id);
                                $feedback['message'] = "There was a problem updating the Group Loan application , please try again";
                                
                                  $this->helpers->activity_logs($_SESSION['id'],14,"Editing group loan application",$feedback['message']." -# ". $this->input->post('group_id'),NULL,$this->input->post('group_id'));
                            }
                        }else{//second entry in the client loan was not successfull
                           $this->group_loan_model->delete_by_id($group_loan_id);
                            $feedback['message'] = "There was a problem saving the Group Loan application , please try again"; 

                              $this->helpers->activity_logs($_SESSION['id'],14,"Editing group loan application",$feedback['message']." -# ". $this->input->post('group_id'),NULL,$this->input->post('group_id'));
                        }
                    }else{
                        $feedback['success'] = true;
                        $feedback['message'] = "Group Loan application successfully updated";

                          $this->helpers->activity_logs($_SESSION['id'],14,"Editing group loan application",$feedback['message']." -# ". $this->input->post('group_id'),NULL,$this->input->post('group_id'));
                    }
                } else {
                    $feedback['message'] = "There was a problem updating the Group Loan application, please try again or get in touch with the admin";

                      $this->helpers->activity_logs($_SESSION['id'],14,"Editing group loan application",$feedback['message']." -# ". $this->input->post('group_id'),NULL,$this->input->post('group_id'));
                }
            } else {

                $group_loan_no = $this->generate_pure_loan_ref_no();
                $loan_ref_no = $this->generate_client_loan_ref_no();
                $group_loan_id = $this->group_loan_model->set( $group_loan_no );
                
                if (is_numeric($group_loan_id)) {
                    if (isset($_POST['loan_type_id']) && $_POST['loan_type_id']==1 ) {
                        //This means we are inserting a group loan of type pure
                        if ($client_loan_id=$this->client_loan_model->set( $loan_ref_no, $group_loan_id)) {
                            $this->load->model('loan_state_model');
                            if ($this->loan_state_model->set($client_loan_id)) {                                
                                $this->payment_details_model->set($client_loan_id);
                                $feedback['success'] = true;
                                $feedback['loan_type_id'] = $this->input->post('loan_type_id');
                                $feedback['new_group_loan_no'] = ++$group_loan_no;
                                $feedback['message'] = "Group Loan application details successfully saved";
                                 //activity log
                                  $this->helpers->activity_logs($_SESSION['id'],14,"Creating group loan application",$feedback['message']." -# ". $this->input->post('group_id'),NULL,$this->input->post('group_id'));
                            }else{
                                $this->group_loan_model->delete_by_id($group_loan_id);
                                $this->client_loan_model->delete_by_id($client_loan_id);
                                $feedback['message'] = "There was a problem saving the Group Loan application , please try again";
                                 //activity log
                                 $this->helpers->activity_logs($_SESSION['id'],14,"Creating group loan application",$feedback['message']." -# ". $this->input->post('group_id'),NULL,$this->input->post('group_id'));
                            }
                        }else{//second entry in the client loan was not successfull
                           $this->group_loan_model->delete_by_id($group_loan_id);
                            $feedback['message'] = "There was a problem saving the Group Loan application , please try again"; 
                             //activity log
                             $this->helpers->activity_logs($_SESSION['id'],14,"Creating group loan application",$feedback['message']." -# ". $this->input->post('group_id'),NULL,$this->input->post('group_id'));
                        }
                    }else{
                        $feedback['success'] = true;
                        $feedback['loan_type_id'] = $this->input->post('loan_type_id');
                        $feedback['new_group_loan_no'] = ++$group_loan_no;
                        $feedback['message'] = "Group Loan application details successfully saved";
                         //activity log
                         $this->helpers->activity_logs($_SESSION['id'],14,"Creating group loan application",$feedback['message']." -# ". $this->input->post('group_id'),NULL,$this->input->post('group_id'));
                    }
                    
                } else {
                    $feedback['message'] = "There was a problem saving the Group Loan application";
                    //activity log
                     $this->helpers->activity_logs($_SESSION['id'],14,"Creating group loan application",$feedback['message']." -# ". $this->input->post('group_id'),NULL,$this->input->post('group_id'));
                }
            }
        }
        echo json_encode($feedback);
    }

    public function generate_pure_loan_ref_no() {
        $this->load->library("num_format_helper");
        $new_pure_group_loan_no = $this->num_format_helper->new_group_loan_no();
        return $new_pure_group_loan_no===FALSE?$this->input->post("group_loan_no"):$new_pure_group_loan_no;
    }

    public function generate_client_loan_ref_no() {
        $this->load->library("num_format_helper");
        $new_loan_acc_no = $this->num_format_helper->new_loan_acc_no();
        return $new_loan_acc_no===FALSE?$this->input->post("loan_no"):$new_loan_acc_no;
    }

}
