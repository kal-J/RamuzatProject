<?php
/**
 * @Author Eric
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Beyonic_payment extends CI_Controller {
  
    public function __construct() {
     parent::__construct(); 
      $this->load->library("session");
      $this->load->library("payment_transactions");
      $this->load->library("beyonic_transactions");
      if(empty($this->session->userdata('id'))){
          redirect('/');
      } 
      $this->load->model("payment_model");
    }

    public function deposit(){
       $this->load->library('form_validation');
      $this->form_validation->set_rules("client_contact", "Phone Number", "required|valid_phone_ug", array("required" => "%s must be entered", "valid_phone_ug" => "%s should start with +256 or 0"));
      $this->form_validation->set_rules("amount","Amount for deposit","required");
       $feedback['success'] = false;
       $feedback['message']='Deposit failed due to internal error, Please try again later and if it persists contact the support team';
       if($this->form_validation->run() === FALSE ){
        $feedback['message'] = validation_errors('<li>','</li>');
            }else{
              $client_details=$this->member_model->get_member_contact($_POST['member_id']);
              //start transaction
              $this->db->trans_begin();

              $deposit_data['amount']=$_POST['amount'];
              $deposit_data['phone_number']=$_POST['client_contact'];
              $deposit_data['first_name']=$client_details['firstname'];
              $deposit_data['last_name']=$client_details['lastname'];
              $deposit_data['merchant_transaction_id']=$this->payment_transactions->merchantTransaction();
              $response=$this->beyonic_transactions->new_collection($deposit_data);

              if (is_object($response) && property_exists($response,'currency' )) {
                  $this->payment_model->update_2($response);
                  if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $feedback['success'] = True;
                    $feedback['message']='Deposit successful money will reflect to your account soon';
                  }else {
                      $this->db->trans_rollback();
                      $feedback['message'] = "There was a problem recording your deposit, please contact the support team";
                  }
              }else{
                  $this->db->trans_rollback();
              }
            }
        echo json_encode($feedback);
    }

     public function create(){
        $this->load->model("miscellaneous_model");
        $this->form_validation->set_rules('amount', 'Amount', array('required'), array('required' => '%s must be entered'));
        if ($this->input->post('transaction_type_id') == 3) {
          $this->form_validation->set_rules('savings_account_id', 'savings account', array('required'), array('required' => '%s must be selected'));
        } else {

        //$this->form_validation->set_rules('transaction_channel_id', 'Amount', array('required'), array('required' => '%s must be entered'));
        }
        //$this->form_validation->set_rules('narrative', 'Narrative', array('required'), array('required' => '%s must be entered'));
        //$this->form_validation->set_rules('opening_balance', 'Opening_balance', array('required'), array('required' => '%s must be provided'));
        $this->form_validation->set_rules('transaction_type_id', 'Transaction type', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('account_no_id', 'Account Number', array('required'), array('required' => '%s must be provided'));
        $this->form_validation->set_rules('transaction_date', 'transaction date', array('required'), array('required' => '%s must be provided'));
        $this->form_validation->set_rules('state_id', 'Account state', array('required'), array('required' => '%s must be set'));
         $feedback['success'] = false;
       if( $this->input->post('transaction_type_id')==1){
             $msg_type="Withdraw";
       } else if( $this->input->post('transaction_type_id')==2){
            $msg_type="Deposit";
       }else if($this->input->post('transaction_type_id')==3){
            $msg_type="Transfer";
       }else{ $msg_type=""; }
       
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Transaction_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = $msg_type." update successful";
                    //$feedback['transaction'] = $this->Transaction_model->get($_POST['id']);
                } else {
                    $feedback['message'] = $msg_type." failed";
                }
            } else {
                if ($this->input->post('transaction_type_id') == 3) {
                $transaction_data = $this->Transaction_model->set($this->input->post('account_no_id'),false);
                } else {
                $transaction_data = $this->Transaction_model->set();
                }
                if (is_array($transaction_data)) {
                    $feedback['success'] = true;
                    $feedback['message'] = $msg_type."  successful";
                    if( $this->input->post('transaction_type_id')==1){
                            $this->withdraw_journal_transaction($transaction_data);
                            if ($this->input->post('charges') !==NULL && $this->input->post('charges') !='') {
                                $this->we_charges_journal_transaction($transaction_data);
                            }
                       } else if( $this->input->post('transaction_type_id')==2){
                            $this->deposit_journal_transaction($transaction_data);
                            if ($this->input->post('charges') !==NULL && $this->input->post('charges') !='') {
                                $this->de_charges_journal_transaction($transaction_data);
                            }
                       }else if($this->input->post('transaction_type_id')==3){
                         $this->Transaction_model->set($this->input->post('savings_account_id'),2);
                            if ($this->input->post('charges') !==NULL && $this->input->post('charges') !='') {
                                $this->we_charges_journal_transaction($transaction_data);
                            }

                       }else{ 

                       }

                    $acc_id=$this->input->post('account_no_id');
                    if (!empty($result=$this->miscellaneous_model->check_org_module(22))) {
                        $acc_id2=$this->input->post('savings_account_id');
                        #SMS notification set up
                        $content= (!empty($acc_id2))?' from your account '.$this->input->post('account_no').' to account '.$this->input->post('savings_account_no'):' on account '.$this->input->post('account_no');
                        if (!empty($acc_id)) {                  
                        $message=$msg_type." of amount ".$this->input->post('amount')."/= has been made".$content." today ".date('d-m-Y H:i:s').". Thanks";
                        $text_response=$this->helpers->notification($acc_id,$message,false);
                        $feedback['message']=$feedback['message'].$text_response;
                        }
                        if (!empty($acc_id2)) {
                            $message=$msg_type." of amount ".$this->input->post('amount')."/= has been made to your account ".$this->input->post('savings_account_no')." today ".date('d-m-Y H:i:s')." from account ".$this->input->post('account_no').". Thanks";
                            $text_response=$this->helpers->notification($acc_id2,$message,false);
                        
                        }
                    }//End of the check for the sms module
                    if (isset($_POST['account_details'])) {
                        if($this->input->post('client_type')==2){
                          $feedback['group_members'] = $this->Group_member_model->get_group_member_savings('g.id='.$this->input->post('group_member_id'), $acc_id);  
                        $feedback['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings2("(j.state_id = 5 OR j.state_id = 7)",$acc_id);

                         } else {
                        $feedback['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings("(j.state_id = 5 OR j.state_id = 7)",$acc_id);
                       }
                    }else{
                        $feedback['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings("(j.state_id = 5 OR j.state_id = 7)");
                    }
                    if (isset($_POST['print'])) {
                    $feedback['insert_id']=$transaction_data['transaction_id'];
                    $feedback['client_type'] = $this->input->post('client_type');
                    }
                } else {
                    $feedback['message'] = "There was a problem depositing, try again";
                }
            }
        }
        echo json_encode($feedback);
    }


}
