<?php

/**
 * Description of Loan Product Fee
 *
 * @author Eric
 */
class Loan_product_fee extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('loan_product_fee_model');
    }

    public function jsonList() {
        $where = FALSE;
        if($this->input->post('loanproduct_id')!==NULL){
            $where = "loanproduct_id = ".$this->input->post('loanproduct_id');
        }
        if($this->input->post('status_id')!==NULL){
            $where = ($where?$where . " AND ":"")." loan_product_fees.status_id = ".$this->input->post('status_id');
            $where .= " AND loan_fees.status_id=".$this->input->post('status_id');
        }
        
        $this->data['data'] = $this->loan_product_fee_model->get($where);
        echo json_encode($this->data);
    }

    public function create() {
        $this->load->model('loan_fees_model');

        $this->form_validation->set_rules('loanfee_id', 'Loan Fee', array('required', 'min_length[1]', 'max_length[100]'), array('required' => '%s must be entered'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->loan_product_fee_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan Product Fee details successfully updated";
                    $feedback['loan_product_fee'] = $this->loan_fees_model->get_loan_fees(" fms_loan_fees.id not in  ( SELECT loanfee_id from fms_loan_product_fees WHERE loanproduct_id = '".$_POST['loanproduct_id']."' and status_id = 1) ");
                        //activity log 
                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing Loan Product fee",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the loan product fee details";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing Loan Product fee",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                }
            } else {
                $loan_product_fee_id = $this->loan_product_fee_model->set();
                if (is_numeric($loan_product_fee_id)) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan Product Fee details successfully saved";
                    $feedback['loan_product_fee'] = $this->loan_fees_model->get_loan_fees(" fms_loan_fees.id not in  ( SELECT loanfee_id from fms_loan_product_fees WHERE loanproduct_id = '".$_POST['loanproduct_id']."' and status_id = 1) ");

                     $this->helpers->activity_logs($_SESSION['id'],18,"Creating Loan Product fee",$feedback['message']." # ". $loan_product_fee_id,NULL,"id #". $loan_product_fee_id);
                } else {
                    $feedback['message'] = "There was a problem saving the loan product fee data";

                      $this->helpers->activity_logs($_SESSION['id'],18,"Creating Loan Product fee",$feedback['message']." # ". $loan_product_fee_id,NULL,"id #". $loan_product_fee_id);
                }
            }
        }
        echo json_encode($feedback);
    }

    public function delete() {
        $response['message'] = "Loan product Fee could not be deleted, contact IT support.";
        $response['success'] = FALSE;

        $this->helpers->activity_logs($_SESSION['id'],18,"Deleting Loan Product fee",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));

        if ($this->loan_product_fee_model->delete_by_id($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] = "Loan product Fee successfully deleted.";

            $this->helpers->activity_logs($_SESSION['id'],18,"Deleting Loan Product fee",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        }
        echo json_encode($response);
    }

    public function change_status() {
        $response['message'] = "Loan product Fee could not be deactivated, contact IT support.";
        $response['success'] = FALSE;

          $this->helpers->activity_logs($_SESSION['id'],18,"Deleting Loan Product fee",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        
        if ($this->loan_product_fee_model->change_status_by_id($this->input->post('id'))) {
            $response['message'] = "Loan product Fee has successfully been deactivated.";
            $response['success'] = TRUE;
            echo json_encode($response);

              $this->helpers->activity_logs($_SESSION['id'],18,"Deleting Loan Product fee",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        
        }
    }

}
