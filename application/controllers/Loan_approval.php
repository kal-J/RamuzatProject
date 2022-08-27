<?php
/**
 * Description of Loan_approval
 *
 * @author Eric, Melchisedec
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Loan_approval extends CI_Controller {
	// loan_approval/generate_transaction_no
    public function __construct() {
         parent::__construct(); 
         $this->load->library("session");
         if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("loan_approval_model");
        $this->load->library("helpers");
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 4, $_SESSION['staff_id']);
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['privileges'] = array_column($this->data['privilege_list'], "privilege_code");
        }
    }

    public function jsonList() {
        $this->data['data'] = $this->loan_approval_model->get();
        echo json_encode($this->data);
    }

    
    public  function pdf_approval( $loan_id, $transaction_no=false )
    {
        $this->load->model( 'client_loan_model' );
        $this->load->helper('pdf_helper');
        $data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = "Loan approved details";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 10;
        $data['loan_detail'] = $this->client_loan_model->get_client_loan($loan_id);
        $data['the_page_data'] = $this->load->view('client_loan/approval/pdf', $data, TRUE);
        $this->load->view('includes/pdf_template', $data);
    }

    public  function pdf_disburse(){
        $this->load->model('client_loan_model');
        $this->load->model('applied_loan_fee_model');
        $this->load->model('loan_guarantor_model'); 
        $this->load->model('guarantor_model'); 
        $this->load->model('repayment_schedule_model'); 
        $this->load->model('organisation_model'); 
        $this->load->model('loan_collateral_model'); 
        $this->load->model('branch_model');
        if ($this->input->post('client_loan_id') !=NULL) {
            $loan_id=$this->input->post('client_loan_id');
            $data['loan_detail'] = $this->client_loan_model->get_client_loan($loan_id);
            if($this->input->post('filename')) {
                $filename = $this->input->post('filename');
            }else {
                $filename = $data['loan_detail']['loan_no'] . '_Disbursement_Sheet';

            }
            // $paper = $this->input->post('paper');
            // $orientation = $this->input->post('orientation');
            // $stream = $this->input->post('stream');

            $data['title'] = $_SESSION["org_name"];
            $data['filename'] = $filename;
            $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
            $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);

            $where = "a.status_id = 1 AND a.client_loan_id = ".$loan_id;
        
            $data['loan_guarantors'] = $this->loan_guarantor_model->get_guarantors($where);
            //$data['loan_guarantors'] = $this->guarantor_model->get($where, 2);

            // print_r($data['loan_guarantors']); die();

            $data['loan_collateral'] = $this->loan_collateral_model->get($where);
            $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);        
            $data['repayment_schedules'] = $this->repayment_schedule_model->get('repayment_schedule.status_id = 1 AND client_loan_id='.$loan_id);
            $data['applied_fees'] = $this->applied_loan_fee_model->get('a.client_loan_id='.$loan_id);
            
            $data['pdf_data'] = $this->load->view('client_loan/approval/pdf_disburse', $data, TRUE);
            echo json_encode($data);
            
        }else{
            $response['status']=false;
            $response['message']='Loan not selected';

            echo json_encode($response);
        }
        
    }

    public  function pdf_agreement(){
        $this->load->model('client_loan_model');
        $this->load->model('applied_loan_fee_model');
        $this->load->model('loan_guarantor_model'); 
        $this->load->model('guarantor_model'); 
        $this->load->model('repayment_schedule_model'); 
        $this->load->model('organisation_model'); 
        $this->load->model('loan_collateral_model'); 
        $this->load->model('branch_model');
        if ($this->input->post('client_loan_id') !=NULL) {
            $loan_id=$this->input->post('client_loan_id');
            $data['loan_detail'] = $this->client_loan_model->get_client_loan($loan_id);
            if($this->input->post('filename')) {
                $filename = $this->input->post('filename');
                //print_r($filename);die;
            }else {
                $filename = $data['loan_detail']['loan_no'] . '_Loan_agreement';

            }
            $data['title'] = $_SESSION["org_name"];
            $data['filename'] = $filename;
            $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
            $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);

            $where = "a.status_id = 1 AND a.client_loan_id = ".$loan_id;
        
            $data['loan_guarantors'] = $this->loan_guarantor_model->get_guarantors($where);

            $data['loan_collateral'] = $this->loan_collateral_model->get($where);
            $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);        
            $data['repayment_schedules'] = $this->repayment_schedule_model->get('repayment_schedule.status_id = 1 AND client_loan_id='.$loan_id);
            $data['applied_fees'] = $this->applied_loan_fee_model->get('a.client_loan_id='.$loan_id);
            
            $data['pdf_data'] = $this->load->view('client_loan/approval/pdf_agreement', $data, TRUE);
            echo json_encode($data);
            
        }else{
            $response['status']=false;
            $response['message']='Loan not selected';

            echo json_encode($response);
        }
        
    }
    public  function pdf_application_form(){
        $this->load->model('client_loan_model');
        $this->load->model('applied_loan_fee_model');
        $this->load->model('loan_guarantor_model'); 
        $this->load->model('guarantor_model'); 
        $this->load->model('repayment_schedule_model'); 
        $this->load->model('organisation_model'); 
        $this->load->model('loan_collateral_model'); 
        $this->load->model('branch_model');
        if ($this->input->post('client_loan_id') !=NULL) {
            $loan_id=$this->input->post('client_loan_id');
            $data['loan_detail'] = $this->client_loan_model->get_client_loan($loan_id);
            if($this->input->post('filename')) {
                $filename = $this->input->post('filename');
                //print_r($filename);die;
            }else {
                $filename = $data['loan_detail']['loan_no'] . '_Loan_appliaction';

            }
            $data['title'] = $_SESSION["org_name"];
            $data['filename'] = $filename;
            $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
            $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);

            $where = "a.status_id = 1 AND a.client_loan_id = ".$loan_id;
        
            $data['loan_guarantors'] = $this->loan_guarantor_model->get_guarantors($where);

            $data['loan_collateral'] = $this->loan_collateral_model->get($where);
            $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);        
            $data['repayment_schedules'] = $this->repayment_schedule_model->get('repayment_schedule.status_id = 1 AND client_loan_id='.$loan_id);
            $data['applied_fees'] = $this->applied_loan_fee_model->get('a.client_loan_id='.$loan_id);
            
            $data['pdf_data'] = $this->load->view('client_loan/approval/pdf_loan_application', $data, TRUE);
            echo json_encode($data);
            
        }else{
            $response['status']=false;
            $response['message']='Loan not selected';

            echo json_encode($response);
        }
        
    }

    public  function pdf_schedule(){
        $this->load->model('client_loan_model');
        $this->load->model('applied_loan_fee_model');
        $this->load->model('loan_guarantor_model'); 
        $this->load->model('repayment_schedule_model'); 
        $this->load->model('loan_installment_payment_model'); 
        $this->load->model('organisation_model'); 
        $this->load->model('loan_collateral_model'); 
        $this->load->model('branch_model');
        if ($this->input->post('client_loan_id') !=NULL) {
            $loan_id=$this->input->post('client_loan_id');
            $data['loan_detail'] = $this->client_loan_model->get_client_loan($loan_id);
            if($this->input->post('filename')) {
                $filename = $this->input->post('filename');
            }else {
                $filename = $data['loan_detail']['loan_no'] . '_Schedule';

            }

            $data['title'] = $_SESSION["org_name"];
            $data['filename'] = $filename;
            $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
            $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);

            $where = "a.status_id = 1 AND a.client_loan_id = ".$loan_id;
        
            $data['loan_guarantors'] = $this->loan_guarantor_model->get($where);
            $data['loan_collateral'] = $this->loan_collateral_model->get($where);
            $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);

            $data['repayment_schedules'] = $this->repayment_schedule_model->get('repayment_schedule.status_id = 1 AND client_loan_id='.$loan_id);

            $data['loan_schedule'] = $this->repayment_schedule_model->get();
            $data['loan_payments'] = $this->loan_installment_payment_model->get();

        foreach ($data['loan_schedule'] as $key => $value) {
          $due_installments_data = $this->repayment_schedule_model->due_installments_data($value['id']);
          if (!empty($due_installments_data)) {
              $over_due_principal= $due_installments_data['due_principal'];
              $number_of_late_days= $due_installments_data['due_days']- $due_installments_data['grace_period_after'];
              $penalty_rate=( ($due_installments_data['penalty_rate'])/100);

              if ($due_installments_data['penalty_rate_charged_per'] ==3) {
                $number_of_late_period = intdiv($number_of_late_days, 30);
              }elseif ($due_installments_data['penalty_rate_charged_per'] ==2) {
                $number_of_late_period =intdiv($number_of_late_days,7);
              }else {
                $number_of_late_period =$number_of_late_days;
              }

              $penalty_value= ($over_due_principal * $number_of_late_period * $penalty_rate)-$due_installments_data['paid_penalty_amount'];
              $data['loan_schedule'][$key]['penalty_value']=$penalty_value;
          }else{
            $data['loan_schedule'][$key]['penalty_value']='';
          }
        }
        

            $data['applied_fees'] = $this->applied_loan_fee_model->get('a.client_loan_id='.$loan_id);
            
            $data['pdf_data'] = $this->load->view('client_loan/approval/pdf_schedule', $data, TRUE);
            echo json_encode($data);

        }else{
            $response['status']=false;
            $response['message']='Loan not selected';

            echo json_encode($response);
        }
        
    }

}
