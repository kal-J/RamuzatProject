<?php
/**
 * Description of Share_issuance
 *
 * @author Diphas modified by Reagan
 */
class Share_issuance extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->library("helpers");
        $this->load->model('Share_issuance_model');
        $this->load->model('share_call_model');
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 17, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 17, $_SESSION['organisation_id']);
        if(empty($this->data['module_access'])){
            redirect('my404');
        } else {
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['share_issuance_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        }
        $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
        if(empty($this->data['fiscal_active'])){
            redirect('dashboard');
        }
        }
    }

    public function jsonList() {
        $where = FALSE;
        if ($this->input->post('organisation_id') !== NULL) {
            $where = "organisation_id = " . $this->input->post('organisation_id');
        }
        $data['data'] = $this->Share_issuance_model->get($where);
        echo json_encode($data);
    }

    public function create() {
        $this->form_validation->set_rules('share_to_issue', 'Number of Shares to Issue', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('date_of_issue', ' Date of Issue ', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('issuance_name', ' Share Issuance Name or Category ', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('price_per_share', ' Price per share  ', array('required'), array('required' => '%s must be entered'));

        $this->form_validation->set_rules('share_capital_account_id', 'Share capital account ', array('required'), array('required' => '%s must be selected'));

        $this->form_validation->set_rules('default_shares', 'Default Shares per application ', array('required'), array('required' => '%s must be entered'));

        $this->form_validation->set_rules('min_shares', 'Minimum Shares per application ', array('required'), array('required' => '%s must be entered'));

       $this->form_validation->set_rules('max_shares', 'Maximum Shares per application ', array('required'), array('required' => '%s must be entered'));

        $this->form_validation->set_rules('closing_date', ' Closing Date ', array('required'), array('required' => '%s must be entered'));
       
        $this->form_validation->set_rules('default_lock_in_period', 'Default Lock In Period', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('min_lock_in_period', 'Min Lock In Period', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('max_lock_in_period', 'Max Lock In Period', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('allow_inactive_clients_dividends', 'Allow Inactive Clients Dividends', array('required'), array('required' => '%s must be entered'));
        //$this->form_validation->set_rules('active_period_id', 'Active Period', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('lock_in_period_id', 'Lock in period', array('required'), array('required' => '%s must be entered'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Share_issuance_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Share Issuance details successfully updated";
                    $feedback['share_issuance'] = $this->Share_issuance_model->get($this->input->post('id'));
                    // activity log
                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing share insuance",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the Share Issuance details";
                     
                       $this->helpers->activity_logs($_SESSION['id'],18,"Editing share insuance",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));

                }
            } else {
                $share_issuance_id = $this->Share_issuance_model->set();
                if (is_numeric($share_issuance_id)) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Share Issuance details successfully saved";
                    $feedback['share'] = $share_issuance_id;

                       $this->helpers->activity_logs($_SESSION['id'],18,"Creating share insuance",$feedback['message']." # ".$share_issuance_id,NULL,"id #".$share_issuance_id);
                } else {
                    $feedback['message'] = "There was a problem saving the Share Issuance details";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Creating share insuance",$feedback['message']." # ".$share_issuance_id,NULL,"id #".$share_issuance_id);
                }
            }
        }
        echo json_encode($feedback);
    }

 function check_percentage_total($first_call_percent) {
        $data['percentage_total'] = $this->Share_issuance_model->validate_percentage($first_call_percent);
        $total_percentage=$data['percentage_total']['total_percentage']+$first_call_percent;
        if($total_percentage >100){
        return FALSE;
        }else{
        return TRUE;
        }
    }
    public function view($share_issuance_id) {
        $this->load->model('accounts_model');
        $this->load->model('Share_category_model');
        $this->load->model('miscellaneous_model');
        $this->load->model('share_fees_model');
        $this->load->library(array("form_validation", "helpers"));
        $this->data['share_issuance'] = $this->Share_issuance_model->get($share_issuance_id);
        if (empty($this->data['share_issuance'])) {
            show_404();
        }
        $this->data['account_list'] = $this->accounts_model->get();
        $this->data['share_categories'] = $this->Share_category_model->get();
        $this->data['repayment_made_every'] = $this->miscellaneous_model->get();
        $this->data['title'] = "Share Issuance";
        $this->data['sub_title'] = "Share Issuance";
        $this->data['modalTitle'] = "Edit Share Info";
        $this->data['saveButton'] = "Update";
        $this->data['share_fee'] = $this->share_fees_model->get_share_fees();
        $this->template->title = $this->data['title'];

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("fieldset.css","plugins/select2/select2.min.css");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['add_share_issuance_fees_modal'] = $this->load->view('setting/shares/share_issuance_fees/add_modal', $this->data, true);
        $this->template->content->view('setting/shares/share_issuance/share_view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    
      public function delete(){
      $response['message'] = "Share record could not be deleted, contact support.";
      $response['success'] = FALSE;
       $this->helpers->activity_logs($_SESSION['id'],18,"Deleting share insuance",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));

      if($this->Share_issuance_model->delete_by_id($this->input->post('id'))){
      $response['success'] = TRUE;
      $response['message'] = "Share record successfully deleted.";

       $this->helpers->activity_logs($_SESSION['id'],18,"Deleting share insuance",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
      }
      echo json_encode($response);
      } 

    public function change_status() {
        $response['message'] = "Share record could not be deactivated, contact support.";
        $response['success'] = FALSE;
         $this->helpers->activity_logs($_SESSION['id'],18,"Deactivated share insuance",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));

        if ($this->Share_issuance_model->change_status_by_id()) {
            $response['success'] = TRUE;
            $response['message'] = "Share record successfully deactivated.";
            
              $this->helpers->activity_logs($_SESSION['id'],18,"Deactivated share insuance",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        }
        echo json_encode($response);
    }

}
