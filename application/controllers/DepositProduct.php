<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class DepositProduct extends CI_Controller {
     public function __construct() {
         parent::__construct(); 
         $this->load->library("session");
         if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("DepositProduct_model");
        $this->load->model("Saving_fees_model");
        $this->load->model("accounts_model");
        $this->load->library(array("form_validation", "helpers"));
        $this->data['deposit_product_list'] = $this->helpers->user_privileges($module_id = 5, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 5, $_SESSION['organisation_id']);
        if(empty($this->data['module_access'])){
            redirect('my404');
        } else {
        if (empty($this->data['deposit_product_list'])) {
            redirect('my404');
        } else {
            $this->data['deposit_product_privilege'] = array_column($this->data['deposit_product_list'], "privilege_code");
        }
        $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
        if(empty($this->data['fiscal_active'])){
            redirect('dashboard');
        }
        }
     }

     public function jsonList(){
        $all_data = $this->DepositProduct_model->get();
        $new_ranges = [];
        foreach ($all_data as $mydata) {
        $mydata['ranges'] = $this->DepositProduct_model->get_range_rates("product_id=" . $mydata['id']);
            $new_ranges[] = $mydata;
        }
        $data['data'] = $new_ranges;
        echo json_encode($data);
    }

    public function 
    create() {
        if (isset($_POST['interestcalmtd_id']) && is_numeric($_POST['interestcalmtd_id']) && isset($_POST['interestcalmtd_id'])) {
            $this->form_validation->set_rules('interestcalmtd_id', 'Calculation Method', array('required'), array('required' => '%s must be selected'));
            if($_POST['producttype']!=2){ 
             if(empty($_POST['mininterestrate']) && empty($_POST['maxinterestrate'])){
            $this->form_validation->set_rules('defaultinterestrate', 'Interest Rate', array('required'), array('required' => '%s must be entered'));
             }
            }
          }
       
       
            $this->form_validation->set_rules('productname', 'Product Name', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));
            $this->form_validation->set_rules('producttype', 'Product Type', array('required'), array('required' => '%s must be entered'));
            $this->form_validation->set_rules('description', 'Description', array('required'), array('required' => '%s must be entered'));
            //checking product type value for server side validation 
            if ($this->input->post('producttype') == 2) {
              
                $this->form_validation->set_rules('mintermlength', 'Minimum Term Length', array('required'), array('required' => '%s must be entered'));
                $this->form_validation->set_rules('maxtermlength', 'Maximum Term Length', array('required'), array('required' => '%s must be entered'));
            }
        $feedback['success'] = false;

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->DepositProduct_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Deposit Product details successfully updated";
                    $prod= $this->DepositProduct_model->get($_POST['id']);
                    $prod['ranges'] = $this->DepositProduct_model->get_range_rates("product_id=" .$_POST['id']);
                    $feedback['product']= $prod;

                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing deposit product info",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating Deposit Product details";

                    $this->helpers->activity_logs($_SESSION['id'],18,"Editing deposit product info",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                }
            } else {
                $product_id = $this->DepositProduct_model->set();
                if ($product_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Deposit Product details successfully saved";

                    $this->helpers->activity_logs($_SESSION['id'],18,"Creating deposit product info",$feedback['message']." # ".$product_id,NULL,"id #".$product_id);
                } else {
                    $feedback['message'] = "There was a problem saving Deposit Product data";
                         $this->helpers->activity_logs($_SESSION['id'],18,"Creating deposit product info",$feedback['message']." # ".$product_id,NULL,"id #".$product_id);
                }
            }
        }
        echo json_encode($feedback);
     }

    public function create2() {
        $form_rangeFees = $this->input->post('rangeFees');
        $product_id = $this->input->post('id');
        $feedback['success'] = false;
            $feedback['message'] = "failed";
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if(($this->input->post('rangeFees')!=NULL) &&($this->input->post('rangeFees')!="")){
                    $this->db->trans_begin();
                $db_existing_range_rates = $this->DepositProduct_model->get_range_rates("product_id=" . $product_id);
                $range_rates_deleted_array = $this->get_deleted_elements($db_existing_range_rates, $form_rangeFees);
                $this->DepositProduct_model->remove($product_id, $range_rates_deleted_array);
                $saving_rate_ranges = $this->DepositProduct_model->insert_range_rates($product_id, $form_rangeFees);
                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $feedback['success'] = true;
                    $feedback['message'] = "Interest rates successfully updated";
                    $prod= $this->DepositProduct_model->get($_POST['id']);
                    $prod['ranges'] = $this->DepositProduct_model->get_range_rates("product_id=" .$_POST['id']);
                    $feedback['product']= $prod;
                } else {
                    $this->db->trans_rollback();
                    $feedback['message'] = "There was a problem updating the Interest rates";
                }
               }
            }
        echo json_encode($feedback);
    }

    public function view($id) {
        $this->load->library("helpers");
        $this->load->model('miscellaneous_model');
        $this->load->model('organisation_model');
        $prod= $this->DepositProduct_model->get($id);
        $prod['ranges'] = $this->DepositProduct_model->get_range_rates("product_id=" . $id);
        $this->data['product']= $prod;
        $this->data['available_to'] = $this->miscellaneous_model->get_available_to();
        $this->data['title'] = $this->data['sub_title'] = $this->data['product']['productname'];
        $this->template->title = $this->data['title'];
        $this->data['modalTitle'] = "Edit Deposit Product Info";
        $this->data['saveButton'] = "Update";
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/cropping/croppie.js","plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/cropping/croppie.css");
        $this->data['organisation'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        // Load a view in the content partial
        //$this->data['user_nav'] = $this->load->view('setting/savings/deposit_product/deposit_product_setting', $this->data, TRUE);
        $this->data['deposit_product_type'] = $this->miscellaneous_model->get_product_type();
        $this->data['availableTo'] = $this->miscellaneous_model->get_available_to();
        $this->data['term_list'] = $this->miscellaneous_model->get_term_time_unit();
        $this->data['repayment_made_every']= $this->miscellaneous_model->get();
        $this->data['cal_mthd'] = $this->miscellaneous_model->get_interest_cal_mthd();
        
        $this->data['daysinyear'] = $this->miscellaneous_model->get_daysinyear();
        $this->data['account_balance_interest'] = $this->miscellaneous_model->get_account_balance_interest();
        $this->data['savingspdtfees'] = $this->Saving_fees_model->get_saving_fees("sf.status_id = '1'");
        $this->data['amountcalculatedas'] = $this->miscellaneous_model->get_amountcalculatedas();

        
        $this->data['account_list'] = $this->accounts_model->get();

        $this->template->content->view('setting/savings/deposit_product/deposit_product_setting', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function change_status() {
        // print_r($this->input->post('status_id')); die;
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
          $this->helpers->activity_logs($_SESSION['id'],18,"Deactivated deposit product info",$this->data['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        $this->data['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        $this->data['message'] = $this->DepositProduct_model->change_status();
        if ($this->data['message'] === true) {
            $this->data['success'] = TRUE;
            $this->data['message'] = "Deposit Product successfully deactivated";

              $this->helpers->activity_logs($_SESSION['id'],18,"Deactivated deposit product info",$this->data['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        }
        // }
        echo json_encode($this->data);
     }
     
     function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
          $this->helpers->activity_logs($_SESSION['id'],18,"Deleting deposit product info",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        // if (isset($_SESSION['role']) && isset($_SESSION['role']) == 1) {
        if (($response['success'] = $this->DepositProduct_model->delete($this->input->post('id'))) === true) {
            $response['message'] = "Deposit Product Details successfully deleted";

              $this->helpers->activity_logs($_SESSION['id'],18,"Deleting deposit product info",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));

        }
        // }
        echo json_encode($response);
    }


     private function get_deleted_elements($db_array, $form_array) {
            $delete_array = [];
            if (is_array($form_array)) {
                foreach ($db_array as $db_array_el) {
                    $search_result = $this->find_in_array($db_array_el, $form_array);
                    if ($search_result === FALSE) {
                        $delete_array[] = $db_array_el['id'];
                    }
                    //then add it to the $delete_array
                } /* */
            }
            return $delete_array;
        }

        private function find_in_array($elem, $array) {
            foreach ($array as $array_item) {
                if (isset($array_item['id']) && $array_item['id'] === $elem['id']) {
                    return TRUE;
                }
            }
            return FALSE;
        }

}
