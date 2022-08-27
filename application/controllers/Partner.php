<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Partner extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("contact_model");
        $this->load->model("user_doc_type_model");
        $this->load->model('User_model');
        $this->load->model('partner_model');
        $this->load->model("organisation_model");
        $this->load->model("organisation_format_model");
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
        }
        }
    }



    public function jsonList() {
        $data['data'] = $this->partner_model->get();
        echo json_encode($data);
    }

    public function index() {
        $this->load->model('miscellaneous_model');
        $this->data['title'] = $this->data['sub_title'] = "Partner Management";
        $this->template->title = $this->data['title'];
        $this->data['organisation_format'] = $this->organisation_format_model->get_formats();
        $this->data['staff_no_format'] =$this->organisation_format_model->get_transaction_format();

        $this->data['marital_statuses'] = $this->miscellaneous_model->get_marital_status_options();
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/datepicker/bootstrap-datepicker.js","plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/datepicker/datepicker3.css");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        // Load a view in the content partial
        $this->template->content->view('partner/partner_list', $this->data);
        
        // Publish the template
        $this->template->publish();
    }

    // public function staff_data($id = false) {
    //     if ($id == false) {
    //         redirect("my404");
    //     }else{          
    //         $this->data['user'] = $this->partner_model->get_staff($id);
    //          if (empty($this->data['user'])) {
    //              redirect("my404");
    //             }
    //     }
    //     $this->load->model('District_model');
    //     $this->load->model('Employment_model');
    //     $this->load->model("Address_model");
    //     $this->load->model('miscellaneous_model');
    //     $this->load->model("Signature_model");
    //     $this->load->model('dashboard_model');
        
    //     $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
    //     $this->data['module_list']=$this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
    //     $this->data['modules'] =array_column($this->data['module_list'],"module_id");
    //     $this->data['org_module_list']=$this->organisation_model->get_org_modules($this->session->userdata('organisation_id'));
    //     $this->data['modules_org'] =array_column($this->data['org_module_list'],"module_id");

    //     $this->data['contact_types'] = $this->contact_model->get_contact_type();
    //     $this->data['user_doc_types'] = $this->user_doc_type_model->get_doc_type();


    //     $this->data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
    //     $this->data['type'] = $this->data['sub_type'] = 'staff';
    //     $this->data['staff'] = $this->partner_model->get_registeredby();        
    //     $this->data['districts'] = $this->District_model->get_districts();
    //     $this->data['positions'] = $this->Position_model->get();
    //     $this->data['nature_of_employment'] = $this->Employment_model->get_nature_of_employment();
    //     $this->data['address_types'] = $this->Address_model->get_address_types();
    //     $this->data['marital_statuses'] = $this->miscellaneous_model->get_marital_status_options();
    //     $this->data['relationship_types'] = $this->miscellaneous_model->get_relationship_type();
    //     $this->data['roles'] = $this->Role_model->get_active_roles($status_id=1);
    //     $this->data['modalTitle'] = "Edit Staff Info";
    //     $this->data['saveButton'] = "Update";
    //     $this->data['staff_no_format'] =$this->organisation_format_model->get_staff_format();
    //     $this->data['organisation_format'] = $this->organisation_format_model->get_formats();
    //     $this->data['organisation'] = $this->organisation_format_model->get_account_format();
    //     $this->data['title'] = $this->data['sub_title'] = $this->data['user']['firstname'] . " " . $this->data['user']['lastname'] . " " . $this->data['user']['othernames'];
    //     $this->template->title = $this->data['title'];
    //     $this->data['user_signature'] = $this->Signature_model->get(["fms_user_signatures.user_id"=>$this->data['user']['user_id']]);
      
    //     $neededjs = array("plugins/select2/select2.full.min.js", "plugins/datepicker/bootstrap-datepicker.js", "plugins/cropping/croppie.js","plugins/validate/jquery.validate.min.js");
    //     $neededcss = array("plugins/select2/select2.min.css", "plugins/datepicker/datepicker3.css", "plugins/cropping/croppie.css");

    //     $this->helpers->dynamic_script_tags($neededjs, $neededcss);
    //     $this->data['user_nav'] = $this->load->view('user/user_nav', $this->data, TRUE);
    //     $this->template->content->view('user/index', $this->data);
    //     // Publish the template
    //     $this->template->publish();
    // }

    public function create() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('firstname', 'Staff First Name', 'required');
        $this->form_validation->set_rules('lastname', 'Staff Last Name', 'required');        
        if ($this->input->post('id') == NULL) {
            $this->form_validation->set_rules("mobile_number", "Phone Number", "required|valid_phone_ug", array("required" => "%s must be entered", "valid_phone_ug" => "%s should start with +256 or 0 and minimum of 10(ten) digits"));
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

                    if ($this->partner_model->update()) {
                        $feedback['success'] = true;
                        $feedback['message'] = "Partner Details successfully updated";
                        $feedback['user'] = $this->partner_model->get($this->input->post('id'));
                    } else {
                        $feedback['message'] = "There was a problem updating the partner data, please try again";
                    }
                } else {
                    $feedback['message'] = "Partner Details could not be updated";
                }
        
            } else {
                //adding a new item
                $partner_no=$this->generate_partner_no();
                if ($partner_no != false) {
                $inserted_id = $this->User_model->add_user();
                if ($inserted_id) {
                    $inserted_partner = $this->partner_model->add($inserted_id,$partner_no);
                    if ($inserted_partner) {
                          $inserted_contact_id=$this->contact_model->add_contact($inserted_id);
                            if (is_numeric($inserted_contact_id)) {                                
                                $feedback['message'] = "Partner has been successfully added";
                            }else{
                                $feedback['message'] = "Partner has been successfully added,though contact couldn't be added";
                            }
                        $feedback['success'] = true;
                        $feedback['user'] = $inserted_partner; //$inserted_id;
                    } else {
                        $feedback['message'] = "There was a problem saving the partner data, please try again";
                    }
                } else {
                    $feedback['message'] = "Partner Details failed to submit!";
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
      $response['message'] = "Partner could not be deleted, contact support.";
      $response['success'] = FALSE;
      if($this->partner_model->delete_by_id($this->input->post('id'))){
        $response['success'] = TRUE;
        $response['message'] = "Partner successfully deleted.";
      }
      echo json_encode($response);
    }

    public function change_status(){
        $response['success'] = FALSE;
        $response['message'] = "Partner not deactivated.";
      if($this->partner_model->change_status_by_id($this->input->post('id'))){
        $response['success'] = TRUE;
        $response['message'] = "Partner successfully deactivated.";
      }
      echo json_encode($response);
    }

    function generate_partner_no() {
    $this->data['partner_no_format'] =$this->organisation_format_model->get_partner_format();
    $org_id = $this->data['partner_no_format']['id'];
    $org =  $this->data['partner_no_format']['partner_format'];
    $counter =  $this->data['partner_no_format']['partner_counter'];
    $letter =  $this->data['partner_no_format']['partner_letter'];
   
    $initial =  $this->data['partner_no_format']['org_initial'];
    if ($org == '1') {
        if ($counter == 999) {
            $letter++;
            $counter=0;
        }
        $partner_no = $initial . sprintf("%03d", $counter + 1) . $letter;
    } else if ($org == '2') {
        if ($counter == 999) {
            $letter++;
            $counter=0;
        }
        $partner_no = $letter . sprintf("%03d", $counter + 1) . $initial;
    } else if ($org == '3') {
        $partner_no = $initial . sprintf("%03d", $counter + 1);
    } else {
        $partner_no = false;
    }
    $this->db->where('id',$org_id);
    $upd = $this->db->update('fms_organisation', ["partner_counter"=> $counter+1,"partner_letter"=> $letter]);
    return $partner_no;
    }

}
