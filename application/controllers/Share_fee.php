<?php

/**
 * Description of Share Fee
 *
 * @author Melchisedec
 */
class Share_fee extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('share_fees_model');
        $this->load->library("helpers");        
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id=11,$this->session->userdata('staff_id'));
        if(empty($this->data['privilege_list'])){
            redirect('my404');
        } else {
            $this->data['privileges'] =array_column($this->data['privilege_list'],"privilege_code");
        }
    }

    public function jsonList() {
        $where = FALSE;
        if ($this->input->post('organisation_id') !== NULL) {
            $where = "organisation_id = " . $this->input->post('organisation_id');
        }
        $this->data['data'] = $this->share_fees_model->get();
        echo json_encode($this->data);
    }

    public function create() {

        $this->form_validation->set_rules('feename', 'Fee name', array('required', 'min_length[3]', 'max_length[300]'), array('required' => '%s must be entered'));
        if (($this->input->post('amountcalculatedas_id')==1)&&($this->input->post('amount')>100)) {
        $this->form_validation->set_rules('amount', 'amount','numeric|less_than_equal_to[100]');
        }else{
         $this->form_validation->set_rules('amount', 'amount', array('required', 'min_length[1]', 'max_length[30]'), array('required' => '%s must be entered'));
        }

        

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->share_fees_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan Fee details successfully updated";
                    $feedback['loan_fee'] = $this->share_fees_model->get($_POST['id']);
                    //activity log 
                      $this->helpers->activity_logs($_SESSION['id'],18,"Editing share fee",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the loan fee details";

                    $this->helpers->activity_logs($_SESSION['id'],18,"Editing share fee",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                }
            } else {
                $loan_fee_id = $this->share_fees_model->set();
                if ($loan_fee_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan fee details successfully saved";

                      $this->helpers->activity_logs($_SESSION['id'],18,"Creating share fee",$feedback['message']." # ".$loan_fee_id,NULL,"id #".$loan_fee_id);
                } else {
                    $feedback['message'] = "There was a problem saving the loan fee data";

                      $this->helpers->activity_logs($_SESSION['id'],18,"Creating share fee",$feedback['message']." # ".$loan_fee_id,NULL,"id #".$loan_fee_id);
                }
            }
        }
        echo json_encode($feedback);
    }
     public function delete() {
        $response['message'] = "Loan Fee could not be deleted, contact IT support.";
        $response['success'] = FALSE;

          $this->helpers->activity_logs($_SESSION['id'],18,"Deleting share fee",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        if ($this->share_fees_model->delete_by_id($this->input->post('id'))) {
            $response['success'] = TRUE;

              $this->helpers->activity_logs($_SESSION['id'],18,"Deleting share fee",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
            $response['message'] = "Loan Fee successfully deleted.";
        }
        echo json_encode($response);
    }

    public function view($share_name_id) {
        $this->load->model('share_fees_model');
        $this->load->library(array("form_validation", "helpers"));
        $this->data['share'] = $this->share_fees_model->get( $share_name_id );
        // if (empty($this->data['share'])) {
        //     show_404();
        // }
        $this->data['title'] = $this->data['share']['fee_name'];
        $this->data['sub_title'] = $this->data['share']['amount'];

        $this->template->title = $this->data['title'];

        $neededcss = array("fieldset.css");
        $neededjs = array();
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);        
        $this->data[ 'add_share_assignment_modal' ] = $this->load->view('setting/shares/share_assignment/add_modal',$this->data,true);
        $this->data[ 'add_share_application_modal' ] = $this->load->view('setting/shares/share_application/add_modal',$this->data,true);
        $this->template->content->view('setting/shares/share_fee/share_detail', $this->data);
        // $this->template->content->view('setting/loan/loan_product/loan_view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function change_status() {
         
        $response['message'] = "Loan Fee could not be deactivated, contact IT support.";
        $response['success'] = FALSE;

          $this->helpers->activity_logs($_SESSION['id'],18,"Deactivating share fee",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));

        if ($this->share_fees_model->change_status_by_id($this->input->post('id'))) {
            $response['message'] = "Loan Fee successfully deactivated.";
            $response['success'] = TRUE;

              $this->helpers->activity_logs($_SESSION['id'],18,"Deactivating share fee",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
            echo json_encode($response);
        }
    }
}
