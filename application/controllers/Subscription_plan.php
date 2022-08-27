<?php

/**
 * Description of Subscription_plan
 *
 * @author Diphas
 */
class Subscription_plan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->library("helpers");
       
        $this->load->model('subscription_plan_model');
    }

    public function jsonList() {
        $data['data'] = $this->subscription_plan_model->get();
        echo json_encode($data);
    }
    public function get_user_sub() {
        $data['data'] = $this->subscription_plan_model->get_user_sub();
        echo json_encode($data);
    }
    

    public function create() {

        $this->form_validation->set_rules('plan_name', 'Fee name', array('required', 'min_length[2]'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('amount_payable', 'Amount payable ', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('repayment_frequency', 'Frequency ', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('repayment_made_every', ' Period ', array('required'), array('required' => '%s must be selected'));
        $this->form_validation->set_rules('income_account_id', 'Income Account ', array('required'), array('required' => '%s must be selected'));
        $this->form_validation->set_rules('income_receivable_account_id', 'Income Receivable Account ', array('required'), array('required' => '%s must be selected'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->subscription_plan_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = $this->lang->line('cont_subscription')." details successfully updated";
                    $feedback['subscription_plan'] = $this->subscription_plan_model->get($this->input->post('id'));
                    //activity log 
                      $this->helpers->activity_logs($_SESSION['id'],18, $this->lang->line('cont_subscription')." Adding",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the FEE details";

                      $this->helpers->activity_logs($_SESSION['id'],18, $this->lang->line('cont_subscription')." Editing",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                }
            } else {
                $subscription_plan_id = $this->subscription_plan_model->set();
                if (is_numeric($subscription_plan_id)) {
                    $feedback['success'] = true;
                    $feedback['message'] =  $this->lang->line('cont_subscription')." details successfully saved";
                    $feedback['subsription_plan_id'] = $subscription_plan_id;

                      $this->helpers->activity_logs($_SESSION['id'],18,"Creating subscription plan",$feedback['message']." # ".$subscription_plan_id,NULL,"id #".$subscription_plan_id);
                } else {
                    $feedback['message'] = "There was a problem saving the fee details";

                       $this->helpers->activity_logs($_SESSION['id'],18,"Creating subscription plan",$feedback['message']." # ".$subscription_plan_id,NULL,"id #".$subscription_plan_id);
                }
            }
        }
        echo json_encode($feedback);
    }

    public function view($subscription_plan_id) {
        $this->load->model('miscellaneous_model');
        $this->load->library(array("form_validation", "helpers"));
        $this->data['subscription_plan'] = $this->subscription_plan_model->get($subscription_plan_id);
        if (empty($this->data['subscription_plan'])) {
            redirect('my404');
        }
        $this->data['repayment_made_every'] = $this->miscellaneous_model->get();
        $this->data['repayment_start_options'] = $this->miscellaneous_model->get_repayment_start_options();
        $this->data['sub_title'] = $this->data['title'] = $this->data['subscription_plan']['plan_name'];
        $this->data['modalTitle'] = "Edit Subscription plan Info";
        $this->data['saveButton'] = "Update";
        $this->template->title = $this->data['title'];

        $neededcss = array("fieldset.css");
        $neededjs = array();
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->content->view('setting/subscription_plan/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    /*
      public function delete(){
      $response['message'] = "Loan product could not be deleted, contact support.";
      $response['success'] = FALSE;
      if($this->subscription_plan_model->delete_by_id($this->input->post('id'))){
      $response['success'] = TRUE;
      $response['message'] = "Loan product successfully deleted.";
      }
      echo json_encode($response);
      } */

    public function change_status() {
        $response['message'] =  $this->lang->line('cont_subscription')." could not be deactivated, contact support.";
        $response['success'] = FALSE;
         $this->helpers->activity_logs($_SESSION['id'],18,"Deactivating subscription plan",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        if ($this->subscription_plan_model->change_status_by_id($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] =  $this->lang->line('cont_subscription')." successfully deactivated.";

             $this->helpers->activity_logs($_SESSION['id'],18,"Deactivating subscription plan",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        }
        echo json_encode($response);
    }

}
