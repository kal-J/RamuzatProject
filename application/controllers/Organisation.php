<?php
/**
 * Description of Branch
 *
 * @author diphas
 */
class Organisation extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('org_modules_model');
        $this->load->model('organisation_model');
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 11, $this->session->userdata('staff_id'));
           
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['privileges'] = array_column($this->data['privilege_list'], "privilege_code");
        }
        $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
        if(empty($this->data['fiscal_active'])){
            redirect('dashboard');
        }
    }

    public function jsonList() {
        $where = FALSE;
        if ($this->input->post('organisation_id') !== NULL) {
            $where = "id = " . $this->input->post('organisation_id');
        }
        $this->data['data'] = $this->organisation_model->get($where);
        echo json_encode($this->data);
    }
    public function index() {
        $this->load->model('department_model');
        $this->load->model('DepositProduct_model');
        $this->load->model('Date_application_method_model');
        $this->load->model('loan_product_type_model');
        $this->load->model('penalty_calculation_method_model');
        $this->load->model('miscellaneous_model');
        $this->load->model('loan_fees_model');
        $this->load->model('loan_product_model');
        $this->load->model('ModulePrivilege_model');
        $this->load->model('Modules_model');
        $this->load->model("Organisation_format_model");
        $this->load->model('accounts_model');

        $this->data['account_list'] = $this->accounts_model->get();
        $this->data['repayment_made_every']= $this->miscellaneous_model->get();
        $this->data['repayment_start_options']= $this->miscellaneous_model->get_repayment_start_options();
     
        $this->data['loan_product_type'] = $this->loan_product_type_model->get();
        $this->data['penalty_calculation_method'] = $this->penalty_calculation_method_model->get();
        $this->data['title'] = $this->data['sub_title'] = "Organisation Setup";
        
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("fieldset.css","plugins/select2/select2.min.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        
        $this->data['chargeTrigger'] = $this->miscellaneous_model->get_charge_trigger();
        $this->data['dateApplicationMtd'] = $this->Date_application_method_model->get_date_application_mtd();
        $this->data['deposit_product_type'] = $this->miscellaneous_model->get_product_type();
        $this->data['term_list'] = $this->miscellaneous_model->get_term_time_unit();

        $this->data['amountcalculatedas'] = $this->miscellaneous_model->get_amountcalculatedas();
        $this->data['feetypes'] = $this->miscellaneous_model->get_feetype();
        $this->data['available_to'] = $this->miscellaneous_model->get_available_to();
        $this->data['loan_product_data'] = $this->loan_product_model->get_product();
        $this->data['loan_fee'] = $this->loan_fees_model->get_loan_fees();

        $this->data['holiday_frequency_every']= $this->miscellaneous_model->get_holiday_frequency_every();
        $this->data['holiday_frequency_day']= $this->miscellaneous_model->get_holiday_frequency_day();
        $this->data['holiday_frequency_of']= $this->miscellaneous_model->get_holiday_frequency_of();/**/
        // =====assign privileges to modules ==============
        //$this->data['modules'] = $this->ModulePrivilege_model->get_modules();
        $this->data['modules'] = $this->Modules_model->get();
        $this->template->title = $this->data['title'];
        $this->template->content->view('organisation_settings/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

   public function create() {
        $this->form_validation->set_rules('name', 'Organisation', array('required', 'min_length[2]'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('description', 'Description', array('required', 'min_length[2]'), array('required' => '%s must be entered'));
      // echo $this->input->post('children_comp'); die;

        $config['upload_path']          = APPPATH. '../uploads/organisation_'.$_POST['id'].'/logo/';
        $config['allowed_types'] = 'png|jpg|jpeg';
        $config['max_size'] = 500;
        $config['max_width'] = 0;
        $config['max_height'] = 0;
        $config['remove_spaces'] = TRUE;
        $config['overwrite'] = TRUE;
        $config['file_name'] = $_FILES['organisation_logo']['name'];
        $file_name = $config['file_name'];
        $this->load->library('upload', $config);


        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $organization_id = $this->input->post('id');
            if (is_numeric($organization_id)) {
                 $this->upload->do_upload('organisation_logo');
                if ($this->organisation_model->update($file_name)) {
                    //uncomment the next two lines if the organization was set up prior to 31st Jan 2019
                    // $this->load->model("accounts_model");
                    // $this->accounts_model->set_organisation_defaults($organization_id);
                    $feedback['success'] = true;
                    $feedback['message'] = "Organisation details successfully updated";
                    $feedback['organisation'] = $this->organisation_model->get($_POST['id']);
                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing Organisation",$feedback['message'],NULL,$this->input->post('name'));
                } else {
                    $feedback['message'] = "There was a problem updating the organisation details";
                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing Organisation",$feedback['message'],NULL,$this->input->post('name'));
                }
            } else {
                $this->upload->do_upload('organisation_logo');
                $organization_id = $this->organisation_model->set($file_name);
                if ($organization_id) {
                    $this->load->model("accounts_model");
                    $this->accounts_model->set_organisation_defaults($organization_id);
                    $this->org_modules_model->set_module_settings($organization_id);
                    $feedback['success'] = true;
                    $feedback['message'] = "Organisation details successfully saved";
                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing Organisation",$feedback['message']." -# ". $this->input->post('name'),NULL,$this->input->post('name'));
                } else {
                    $feedback['message'] = "There was a problem saving the organisation";
                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing Organisation",$feedback['message']." -# ". $this->input->post('name'),NULL,$this->input->post('name'));
                }
            }
        }
        echo json_encode($feedback);
    }

    public function settings($org_id) {
        $this->load->model("Organisation_format_model");
        $this->load->model('miscellaneous_model');

        $this->data['organisation'] = $this->organisation_model->get($org_id);
        $this->data['format_types'] = $this->Organisation_format_model->get_format_types($org_id);
        $this->data['org_modules'] = $this->org_modules_model->get($org_id);
        if (empty($this->data['organisation'])) {
            show_404();
        }
        $this->load->library(array("form_validation", "helpers"));
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("fieldset.css","plugins/select2/select2.min.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['title'] = $this->data['organisation']['name'];
        $this->data['sub_title'] = $this->data['organisation']['description'];
        $this->template->title = $this->data['title'];

        $this->data['payment_engines']= $this->miscellaneous_model->get_payment_engine();
        
        $this->data['add_dept_modal'] = $this->load->view('department/add_modal', $this->data, true);
        $this->data['add_branch_modal'] = $this->load->view('organisation_settings/add_branch_modal', $this->data, TRUE);
        $this->template->content->view('organisation_settings/org_details', $this->data);
        
        //print_r($this->data['org_modules']);die;
        // Publish the template
        $this->template->publish();
    }

    public function create_org_modules() {
        if (count($this->input->post('modules_list[][module_id]')) == 0) {
            $this->form_validation->set_rules('modules_list[0][module_id]', 'Privilege', array('required'), array('required' => ' Select atleast one %s'));

            $this->form_validation->set_rules('organisation_id', 'Organisation', array('required'), array('required' => '  %s organisation can not be identified'));
        } else {
            $this->form_validation->set_rules('organisation_id', 'Role', array('required'), array('required' => '  %s organisation can not be identified'));
        }
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $role_id = $this->org_modules_model->set();
            if ($role_id) {
                $feedback['success'] = true;
                $feedback['org_modules'] = $this->org_modules_model->get($this->input->post('organisation_id'));
                $feedback['message'] = "Modules successfully saved";
            } else {
                $feedback['message'] = "There was a problem saving the Modules ";
            }
        }
        echo json_encode($feedback);
    }
/*     public function loan_process_level() {
        //echo $this->input->post('loan_app_stage'); die;
        $this->form_validation->set_rules('loan_app_stage', 'Loan application stage', array('required'), array('required' => '  %s is required !'));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $role_id = $this->organisation_model->set_module_settings();
            if ($role_id) {
                $feedback['success'] = true;
                $feedback['message'] = "Loan application stage successfully saved";
            } else {
                $feedback['message'] = "There was a problem, try again !";
            }
        }
        echo json_encode($feedback);
    } */

    /* public function client_module_settings() {
        //echo $this->input->post('nextofkin_comp'); die;
        $this->form_validation->set_rules('children_comp', 'Children', array('required'), array('required' => '  %s is required !'));
        $this->form_validation->set_rules('nextofkin_comp', 'Next of Kin', array('required'), array('required' => '  %s is required !'));
        $this->form_validation->set_rules('business_comp', 'Business', array('required'), array('required' => '  %s is required !'));
        $this->form_validation->set_rules('employ_hist_comp', 'Employment History', array('required'), array('required' => '  %s is required !'));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $role_id = $this->organisation_model->set_module_settings();
            if ($role_id) {
                $feedback['success'] = true;
                $feedback['message'] = "Client module settings successfully saved";
            } else {
                $feedback['message'] = "There was a problem, try again !";
            }
        }
        echo json_encode($feedback);
    } */

    public function view($branch_id) {
        $this->data['branch'] = $this->organisation_model->get($branch_id);
        if (empty($this->data['branch'])) {
            show_404();
        }
        $this->load->library(array("form_validation", "helpers"));
        //$this->load->model('staff_model');

        $this->data['title'] = $this->data['branch']['branch_name'];
        $this->data['sub_title'] = $this->data['branch']['branch_number'];

        $this->template->title = $this->data['title'];
        
        $this->data['add_dept_modal'] = $this->load->view('department/add_modal', $this->data, true);
        $this->data['add_branch_modal'] = $this->load->view('branch/add_modal', $this->data, TRUE);
        $this->template->content->view('branch/view', $this->data);
        // Publish the template
        $this->template->publish();
    }
    
    public function change_status() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
     //   if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            $this->data['message'] = $this->organisation_model->change_status();
            if ($this->data['message'] === true) {
                $this->data['success'] = TRUE;
            }
     //   }
        echo json_encode($this->data);
    }
    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        vity_logs($_SESSION['id'],18,"Deleting Organisation",$response['message']." -# ". $this->input->post('organization_id'),NULL,$this->input->post('organization_id'));
        $response['success'] = FALSE;
      //  if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            if (($response['success'] = $this->organisation_model->delete($this->input->post('id'))) === true) {
                $response['message'] = "organisation successfully deleted";
                 $this->helpers->activity_logs($_SESSION['id'],18,"Deleting Organisation",$response['message']." -# ". $this->input->post('organization_id'),NULL,$this->input->post('organization_id'));
            }
      //  }
        echo json_encode($response);
    } 
}
