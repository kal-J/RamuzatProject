<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Applied_loan_fee extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("applied_loan_fee_model");
        $this->load->model("miscellaneous_model");
        $this->load->model("loan_product_fee_model");
        $this->load->model('Loan_guarantor_model');
        $this->load->library("helpers");
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id=2,$_SESSION['staff_id']);
        if(empty($this->data['privilege_list'])){
            redirect('my404');
        } else {
            $this->data['privileges'] =array_column($this->data['privilege_list'],"privilege_code");
            
        }
    }


    public function jsonList() {
        $data['data'] = $this->applied_loan_fee_model->get();
        echo json_encode($data);
    }

    public function apply() {
        $this->data['module_list']=$this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] =array_column($this->data['module_list'],"module_id");
        $loanFees = $this->input->post('loanFees');
        if (empty($loanFees)) {
            $feedback['success'] = false;
            $feedback['message'] = "All fields are required";
        } else {
            //$transaction_no=$this->generate_transaction_no();  
            $this->db->trans_begin(); 
            if ($this->applied_loan_fee_model->set()) {
                $this->load->model( 'loan_product_fee_model' );
                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan fee(s) successfully applied";
                    $loan_id = $this->input->post('loan_id');
                    $feedback['available_loan_fees'] = $this->loan_product_fee_model->get(" loanproduct_id = '" .$this->input->post('loan_product_id')."' and fms_loan_fees.id not in ( SELECT loan_product_fee_id from fms_applied_loan_fee WHERE client_loan_id = '".$loan_id."' and status_id = 1   ) ");
                }else{
                    $this->db->trans_rollback();
                    $feedback['success'] = false;
                    $feedback['message'] = "There was a problem applying the loan fees";
                }
            } else {
                $feedback['success'] = false;
                $feedback['message'] = "There was a problem applying the loan fees";
            }
        }
        echo json_encode($feedback);
    }

     public function create() {
        $loan_id = $this->input->post('loan_id');
        $loanFees = $this->input->post('loanFees');
        $amount=0;
        foreach($loanFees as $feetotal){
            $amount=$feetotal['amount']+$amount;
        }
        if (empty($loanFees)) {
            $feedback['success'] = false;
            $feedback['message'] = "All fields are required";
        } else {
           
            if($this->input->post('payment_id')==5){
                 $savings_data=$this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $this->input->post('savings_account_id'));
                 $current_balance=$savings_data['cash_bal'];
              if ($current_balance >= $amount) {
              if ($this->deduct_charges($this->input->post('loan_id'),$savings_data['account_no'])) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan fee(s) has  successfully been received";
                    $feedback['unpaid_loan_fees'] = $this->loan_product_fee_model->get(" loanproduct_id = '" .$this->input->post('loan_product_id')."' and fms_loan_fees.id not in ( SELECT loan_product_fee_id from fms_applied_loan_fee WHERE client_loan_id = '".$loan_id."' and status_id = 1   ) ");

                    // $feedback['unpaid_loan_fees'] = $this->applied_loan_fee_model->get("a.status_id = 1 AND paid_or_not = 0 AND client_loan_id = " . $loan_id);
                } else {
                    $feedback['message'] = "There was a problem applying the Loan fee, please contact IT support";
                } 
                } else{
                $feedback['message'] = "Insufficient balance to complete the payment";
             }
            } else {
                  if ($this->do_journal_transaction()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan fee(s) has  successfully been received";
                    $feedback['unpaid_loan_fees'] = $this->loan_product_fee_model->get(" loanproduct_id = '" .$this->input->post('loan_product_id')."' and fms_loan_fees.id not in ( SELECT loan_product_fee_id from fms_applied_loan_fee WHERE client_loan_id = '".$loan_id."' and status_id = 1   ) ");

                    // $feedback['unpaid_loan_fees'] = $this->applied_loan_fee_model->get("a.status_id = 1 AND paid_or_not = 0 AND client_loan_id = " . $loan_id);

                } else {
                    $feedback['message'] = "There was a problem applying the Loan fees";
                } 

            }
            //adding a new item
        }
        echo json_encode($feedback);
    }
    public function deduct_charges($client_loan_id,$account_no){
        $update=false;
        $this->load->model('transaction_model');
        #$charge_trigger_id = array('1', '3', '4', '1');
        $loanFees = $this->input->post('loanFees');
        foreach ($loanFees as $fee) {//loop for attached fees
             $value=$this->applied_loan_fee_model->get($fee['loan_product_fee_id']);
                    #deduct the money
                    $deduction_data['amount']=$fee['amount'];
                    $deduction_data['account_no_id']=$this->input->post('savings_account_id');
                    $deduction_data['narrative']='Payment deduction  made to clear '.ucfirst($value['feename'])." for your loan";
                    $transaction_data=$this->transaction_model->deduct_savings($deduction_data);
                    if (is_array($transaction_data)) {
                        $charge_payment_data['account_no_id']=$this->input->post('savings_account_id');
                        $charge_payment_data['comment']='Payment  for '.$value['feename'];
                        $charge_payment_data['transaction_no']=$transaction_data['transaction_no'];
                        $charge_payment_data['transaction_id']=$transaction_data['transaction_id'];
                        $charge_payment_data['client_loan_id']=$client_loan_id;
                        $charge_payment_data['charge_amount']=$fee['amount'];
                        $charge_payment_data['feename']=$value['feename'];
                        $charge_payment_data['income_account_id']=$value['income_account_id']; 
                         $this->helpers->charges_journal_transaction($charge_payment_data);
                        if($this->applied_loan_fee_model->mark_charge_paid($fee['loan_product_fee_id'])){
                            $update = true;
                        $message="Payment of amount ".round($value['amount'],2)."/= has been made from your account ".$account_no." today ".date('d-m-Y H:i:s');
                        $this->helpers->send_email($this->input->post('savings_account_id'),$message,false);
                        #check for the sms module
                        if (!empty($result=$this->miscellaneous_model->check_org_module(22,1))) {
                          $this->helpers->notification($this->input->post('savings_account_id'),$message,false);
                        }
                      }
                    }else{

                    }
    

        }//end of attached fees loop
        if($update==true){
          return "true";
        } else {
          return "false";
        }
    }

     public function do_journal_transaction(){
        $this->load->model('journal_transaction_model');
        $this->load->model('accounts_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('journal_transaction_line_model');
        $update=false;
        $data = [
            'transaction_date'=> $this->input->post('action_date'),
            'description'=> "Loan Fees Payment",
            'ref_no'=> NULL,
            'ref_id'=> $this->input->post('loan_id'),
            'status_id'=> 1,
            'journal_type_id'=> 28
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        //then we prepare the journal transaction lines
            $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
            $linked_account_id=$transaction_channel['linked_account_id'];

            $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id'], false);
            $loanFees = $this->input->post('loanFees');
            foreach ($loanFees as $fee) {
             $attached_fees=$this->applied_loan_fee_model->get($fee['loan_product_fee_id']);
                $debit_or_credit1 = $this->accounts_model->get_normal_side($attached_fees['income_account_id'], false);
                $data = [
                    [
                        $debit_or_credit1=>$fee['amount'],
                        'transaction_date'=> $this->input->post('action_date'),
                        'reference_no'=> NULL,
                        'reference_id'=> $this->input->post('loan_id'),
                        'narrative'=> 'Income received from '.$attached_fees['feename']. ' on '.$this->input->post('action_date'),
                        'account_id'=> $attached_fees['income_account_id'],
                        'status_id'=> 1
                    ],
                    [
                        $debit_or_credit2=> $fee['amount'],
                        'transaction_date'=> $this->input->post('action_date'),
                        'reference_no'=> NULL,
                        'reference_id'=> $this->input->post('loan_id'),
                        'narrative'=> 'Income received from '.$attached_fees['feename']. ' on '.$this->input->post('action_date'),
                        'account_id'=> $linked_account_id,
                        'status_id'=> 1
                    ]
                ];
            if($this->journal_transaction_line_model->set($journal_transaction_id,$data)){
              $update = $this->applied_loan_fee_model->mark_charge_paid($attached_fees['id']);
            }
            
        }
        if($update==true){
          return true;
        } else {
          return false;
        }
    }


    private function generate_transaction_no() {
        $this->load->model( 'organisation_format_model' );
        $this->data['transaction_no_format'] =$this->organisation_format_model->get_transaction_format();
        $org_id = $this->data['transaction_no_format']['id'];
        $counter =  $this->data['transaction_no_format']['counter_applied_loan_fees'];
        $letter =  $this->data['transaction_no_format']['letter_applied_loan_fees'];
        $initial =  $this->data['transaction_no_format']['org_initial'];
        if ($counter == 999999999999) {
                $letter++;
                $counter=0;
            }
        $transaction = 'LF'.sprintf("%012d", $counter + 1) . $letter;
        $this->db->where('id',$org_id);
        $this->db->update('fms_transaction_no_format', ["counter_applied_loan_fees"=> $counter+1,"letter_applied_loan_fees"=> $letter]);
        return $transaction;
    }

    public function change_status() {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Applied share fee data could not be $msg activated, contact IT support.";
        $response['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            if ($this->applied_loan_fee_model->change_status_by_id($this->input->post('id'))) {
                $response['message'] = "Applied loan fee data has successfully been ". $msg. "activated.";
                $response['success'] = TRUE;
                echo json_encode($response);
            }
        //}
    }

    public function delete() {
        $response['success'] = FALSE;
        if ($this->applied_loan_fee_model->delete()) {
            $response['success'] = TRUE;
            $response['message'] = "Data successfully deleted.";
            $response['available_loan_fees'] = $this->loan_product_fee_model->get(" loanproduct_id = '" .$this->input->post('loan_product_id')."' and fms_loan_fees.id not in ( SELECT loan_product_fee_id from fms_applied_loan_fee WHERE client_loan_id = '".$this->input->post('client_loan_id')."' and status_id = 1   ) ");
            $feedback['unpaid_loan_fees'] = $this->loan_product_fee_model->get(" loanproduct_id = '" .$this->input->post('loan_product_id')."' and fms_loan_fees.id not in ( SELECT loan_product_fee_id from fms_applied_loan_fee WHERE client_loan_id = '".$this->input->post('client_loan_id')."' and status_id = 1   ) ");
        }
        echo json_encode($response);
    }

    public  function pdf( $client_loan_id, $transaction_no=false )
    {
        $this->load->helper('pdf_helper');
        $this->load->model('client_loan_model');
        $this->load->model('applied_loan_fee_model');
        $data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = "Loan Fees";        
        $data['font'] = 'helvetica';
        $data['fontSize'] = 8;
        $data['single_receipt_items'] = $this->applied_loan_fee_model->get( "transaction_no = '".$transaction_no."'"  );
        $data['loan_detail'] = $this->client_loan_model->get_client_loan($client_loan_id);
        $data['receipt_item_sum'] = $this->applied_loan_fee_model->get_sum( "transaction_no = '".$transaction_no."'" );
        $data['the_page_data'] = $this->load->view('client_loan/fees/pdf', $data, TRUE);
        $this->load->view('includes/pdf_template', $data);
    }
}

