<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Staff extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        $this->load->library("helpers");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        }
        
        $this->load->model("contact_model");
        $this->load->model("user_doc_type_model");
        $this->load->model('Staff_model');
        $this->load->model('User_model');
        $this->load->model('Position_model');
        $this->load->model("Fiscal_month_model");
        $this->load->model("organisation_model");
        $this->load->model("organisation_format_model");
        $this->load->model("subscription_plan_model");
        $this->load->model('Role_model');

        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 1, $_SESSION['staff_id']);
        $this->data['member_priv_list'] = $this->helpers->user_privileges($module_id = 1, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 1, $_SESSION['organisation_id']);
        if(empty($this->data['module_access'])){
            redirect('my404');
        } else {
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['staff_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
            $this->data['member_privilege'] = array_column($this->data['member_priv_list'], "privilege_code");
            $this->data['member_staff_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
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
        $this->data['title'] = $this->data['sub_title'] = "Staff Management";
        $this->template->title = $this->data['title'];
        //$this->data['organisation_format'] = $this->organisation_format_model->get_formats();
        // $this->data['staff_no_format'] =$this->organisation_format_model->get_staff_format();
        $this->data['new_staff_no'] =$this->num_format_helper->new_staff_no();
        //$this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['subscription_plans'] = $this->subscription_plan_model->get("subscription_plan.organisation_id = " . $_SESSION['organisation_id']);
        $this->data['positions'] = $this->Position_model->get();
        $this->data['marital_statuses'] = $this->miscellaneous_model->get_marital_status_options();
         $this->data['module_list']=$this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] =array_column($this->data['module_list'],"module_id");
        $this->data['staff_list'] = $this->Staff_model->get_registeredby("status_id=1");
        
        $neededjs = ["plugins/select2/select2.full.min.js", "plugins/datepicker/bootstrap-datepicker.js","plugins/validate/jquery.validate.min.js"];
        $neededcss = ["plugins/select2/select2.min.css", "plugins/datepicker/datepicker3.css"];
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        // Load a view in the content partial
        $this->template->content->view('user/staff/staff_view', $this->data);
        
        // Publish the template
        $this->template->publish();
    }

    public function staff_data($id = false) {
        if ($id == false) {
            redirect("my404");
        } else {          
            $this->data['user'] = $this->Staff_model->get_staff($id);
            //print_r($this->data['user']);die();
             if (empty($this->data['user'])) {
                 redirect("my404");
                }
        }
        $this->load->model('District_model');
        $this->load->model('Employment_model');
        $this->load->model("Address_model");
        $this->load->model('miscellaneous_model');
        $this->load->model("Signature_model");
        $this->load->model('dashboard_model');
        
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['module_list']=$this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] =array_column($this->data['module_list'],"module_id");
        $this->data['org_module_list']=$this->organisation_model->get_org_modules($this->session->userdata('organisation_id'));
        $this->data['modules_org'] =array_column($this->data['org_module_list'],"module_id");

        $this->data['contact_types'] = $this->contact_model->get_contact_type();
        $this->data['user_doc_types'] = $this->user_doc_type_model->get_doc_type();


        $this->data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['staff_id'] = $id;
        $this->data['type'] = $this->data['sub_type'] = 'staff';
        $this->data['staff'] = $this->Staff_model->get_registeredby();        
        $this->data['districts'] = $this->District_model->get_districts();
        $this->data['positions'] = $this->Position_model->get();
        $this->data['nature_of_employment'] = $this->Employment_model->get_nature_of_employment();
        $this->data['address_types'] = $this->Address_model->get_address_types();
        $this->data['marital_statuses'] = $this->miscellaneous_model->get_marital_status_options();
        $this->data['relationship_types'] = $this->miscellaneous_model->get_relationship_type();
        $this->data['roles'] = $this->Role_model->get_active_roles($status_id=1);
        $this->data['modalTitle'] = "Staff Details";
        $this->data['saveButton'] = "Update";
        $this->data['new_staff_no'] =$this->data['user']['staff_no'];
        $this->data['title'] = $this->data['sub_title'] = $this->data['user']['firstname'] . " " . $this->data['user']['lastname'] . " " . $this->data['user']['othernames'];
        $this->template->title = $this->data['title'];
        $this->data['user_signature'] = $this->Signature_model->get(["fms_user_signatures.user_id"=>$this->data['user']['user_id']]);
      
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/datepicker/bootstrap-datepicker.js", "plugins/cropping/croppie.js","plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/datepicker/datepicker3.css", "plugins/cropping/croppie.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['user_nav'] = $this->load->view('user/user_nav', $this->data, TRUE);
        $this->template->content->view('user/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function jsonList() {
        $data['data'] = $this->Staff_model->get_staff();
        echo json_encode($data);
    }

    public function create() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('firstname', 'Staff First Name', 'required');
        $this->form_validation->set_rules('lastname', 'Staff Last Name', 'required');        
        if ($this->input->post('id') == NULL) {
            $this->form_validation->set_rules("mobile_number", "Phone Number", "required|valid_phone_ug|callback__check_phone_number", array("required" => "%s must be entered", "valid_phone_ug" => "%s should start with +256 or 0 and minimum of 10(ten) digits", "_check_phone_number" => "%s already exists and possibly the staff too"));
        }
        if(isset($_POST['password'])){ 
        $this->form_validation->set_rules('password', 'New Password', 'required|min_length[8]', array('required' => 'Please set  %s.','min_length[8]'=>'%s must be alteast 8 Characters'));
       $this->form_validation->set_rules('confirmpassword', 'Confirm Password', 'required|matches[password]', array('required' => 'Please confirm password.',' matches[pass]'=>'%s does not match'));
        }
        $feedback['success'] = false;

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {
            if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) { 
                //editing exsting item
                if ($this->User_model->update_user()) {

                    if ($this->Staff_model->update_staff()) {
                        $feedback['success'] = true;
                        $feedback['message'] = "Staff Details successfully updated";
                        $feedback['user'] = $this->Staff_model->get_staff($this->input->post('id'));
                         $this->helpers->activity_logs($_SESSION['id'],1,"Updating Staff",$feedback['message']." #ID:".$this->input->post('id'),NULL,$this->input->post('firstname')." ".$this->input->post('lastname')." ".$this->input->post('othernames'));

                    } else {
                        $feedback['message'] = "There was a problem updating the staff data, please try again";
                          $this->helpers->activity_logs($_SESSION['id'],1,"Updating Staff",$feedback['message']." -# ". $this->input->post('firstname'),NULL,$this->input->post('lastname'));
                    }
                } else {
                    $feedback['message'] = "Staff Details could not be updated";
                      $this->helpers->activity_logs($_SESSION['id'],1,"Updating Staff",$feedback['message']." -# ". $this->input->post('firstname'),NULL,$this->input->post('lastname'));
                }
        
            } else {
                //adding a new item
                $staff_no = $this->get_staff_no();
                if ($staff_no != false) {
                $inserted_id = $this->User_model->add_user();
                if ($inserted_id) {
                    $inserted_staff = $this->Staff_model->add_staff($inserted_id, $staff_no);
                    if ($inserted_staff) {
                          $inserted_contact_id=$this->contact_model->add_contact($inserted_id);
                            if (is_numeric($inserted_contact_id)) {                                
                                $feedback['message'] = "Staff has been successfully added";
                                  $this->helpers->activity_logs($_SESSION['id'],1,"Creating Staff",$feedback['message'],NULL,$this->input->post('firstname')." ".$this->input->post('lastname')." ".$this->input->post('othernames'));
                            }else{
                                $feedback['message'] = "Staff has been successfully added,though contact couldn't be added";
                                   $this->helpers->activity_logs($_SESSION['id'],1,"Creating Staff",$feedback['message'],NULL,$this->input->post('firstname')." ".$this->input->post('lastname')." ".$this->input->post('othernames'));
                            }
                        $feedback['success'] = true;
                        $feedback['user'] = $inserted_staff; //$inserted_id;
                    } else {
                        $this->User_model->delete_by_id($inserted_id);
                        $feedback['message'] = "There was a problem saving the staff data, please try again";
                          $this->helpers->activity_logs($_SESSION['id'],1,"Creating Staff",$feedback['message'],NULL,$this->input->post('firstname')." ".$this->input->post('lastname')." ".$this->input->post('othernames'));
                    }
                } else {
                    $feedback['message'] = "Staff Details failed to submit!";
                       $this->helpers->activity_logs($_SESSION['id'],1,"Creating Staff",$feedback['message'],NULL,$this->input->post('firstname')." ".$this->input->post('lastname')." ".$this->input->post('othernames'));
                }
            } 
          } 
        }
        
        echo json_encode($feedback);
    }
    public function make_member(){
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user_id', 'User Id', 'required');
        // $this->form_validation->set_rules('subscription_plan_id', 'Subscription Plan', 'required');
        $this->form_validation->set_rules('registered_by', 'Registered By', 'required');
        $this->form_validation->set_rules('date_registered', 'Date Registered', 'required');
       
        $feedback['success'] = false;

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {
         //adding a new member
                $client_no = $this->generate_client_no();
                if ($client_no != false) {
                    if ($this->input->post('user_id')) {
                        $member_id = $this->member_model->add_member($this->input->post('user_id'), $client_no);
                        if (is_numeric($member_id)) {
                            $feedback['message'] = "Member has been successfully added";
                            $feedback['success'] = true;

                            $this->helpers->activity_logs($_SESSION['id'],1,"Creating staff as member",$feedback['message']."#".$member_id,NULL," # ".$member_id,NULL);
                        } else {
                            $feedback['message'] = "There was a problem registering member, please try again";

                             $this->helpers->activity_logs($_SESSION['id'],1,"Creating staff as member",$feedback['message']."#".$member_id,NULL," # ".$member_id,NULL);
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
    public function delete(){
      $response['message'] = "Staff could not be deleted, contact support.";
      $response['success'] = FALSE;
      if($this->Staff_model->delete_by_id($this->input->post('id'))){
        $response['success'] = TRUE;
        $response['message'] = "Staff successfully deleted.";
      }
      echo json_encode($response);
    }

    public function change_status(){
        $response['success'] = FALSE;
        $msg = $this->input->post('status_id')==1?"":"de";
        $response['message'] = "Staff could not be $msg"."activated.";
 
      if($this->Staff_model->change_status_by_id($this->input->post('id'))){
        $response['success'] = TRUE;
        $response['message'] = "Staff successfully $msg"."activated.";

        
      }
      echo json_encode($response);

       $this->helpers->activity_logs($_SESSION['id'],1,"Deactivating staff",$response['message']." -# ". $this->input->post('id'),NULL,NULL);
    }

     

    private function get_staff_no() {
        $this->load->library("num_format_helper");
        $new_staff_no = $this->num_format_helper->new_staff_no();
        return $new_staff_no===FALSE?$this->input->post("staff_no"):$new_staff_no; 
    }
    
    private function get_client_no() {
        $this->load->library("num_format_helper");
        $new_staff_no = $this->num_format_helper->new_staff_no();
        return $new_staff_no===FALSE?$this->input->post("staff_no"):$new_staff_no; 
    }

    function generate_client_no() {
        $this->load->library("num_format_helper");
        $new_client_no = $this->num_format_helper->new_client_no();
        return $new_client_no===FALSE?$this->input->post("client_no"):$new_client_no; 
    }

      public function unblock_account(){
        
           $response['success'] = FALSE;
           $response['message'] = "User Account could not be unblocked.";
          if($this->User_model->update_login_attempt($this->input->post('id'),4)){
            $response['success'] = TRUE;
            $response['message'] = "User Account successfully unblocked.";
          }
          echo json_encode($response);
    }


}
