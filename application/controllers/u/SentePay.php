<?php
/**
 * @Author Reagan
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class SentePay extends CI_Controller {
  
    public function __construct() {
     parent::__construct(); 
      $this->load->library("session");
      $this->load->library("payment_transactions");
      $this->load->library("sente_pay");
      if(empty($this->session->userdata('id'))){
          redirect('welcome');
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
          $deposit_data['merchant_transaction_id']=$this->payment_transactions->merchantTransaction();
          $this->db->trans_begin();

          $deposit_data['amount']=$_POST['amount'];
          $deposit_data['narrative']=$_POST['narrative'];
          $deposit_data['phone_number']=$_POST['client_contact'];
          $deposit_data['names']=$client_details['firstname'].' '.$client_details['lastname'];
          $deposit_data['merchant_transaction_id']=$this->payment_transactions->merchantTransaction();
          $response=$this->Sente_pay->new_collection($deposit_data);
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


public function disburseLoan(){
      
    $feedback['success'] = false;
    $feedback['message']='Payment failed due to internal error, Please try again later and if it persists contact the support team';
           $client_details=$this->member_model->get_member_contact($_POST['member_id']);
           $trans=$this->payment_model->get_for_loan($_POST['member_id'],$_POST['client_loan_id']);
           //start transaction
           $this->db->trans_begin();
           $payment_data['amount']=$_POST['amount_approved'];
           $payment_data['phone_number']=$_POST['phone_number'];
           $payment_data['names']=$client_details['firstname']." ".$client_details['lastname'];
           $payment_data['ext_ref']= $trans['ext_ref'];//54545454;//

           $response=$this->sente_pay->new_payment($payment_data);
           //print_r($response[0]);die();
            $result = null;
           if(isset($response[0])) {
               $result = (array) json_decode($response[0]);
           }

           $feedback['message']=isset($result) ? (
               isset($result['message']) ?  isset($result['message']) : $response
           ) : $response;
           

           if (isset($result['refNo'])) {
               $result['phonenumber'] = $payment_data['phone_number'];
               $result['amount'] = $payment_data['amount'];

               $this->payment_model->update_from_sentepay($result, $payment_data['ext_ref']);
               
               if ($this->db->trans_status()) {
                 $this->db->trans_commit();
                 $feedback['success'] = True;
                 $feedback['message']='Payment is being processed (awaiting approval), money will be sent to the client soon';
               }else {
                   $this->db->trans_rollback();
                   $feedback['message'] = "There was a problem recording this payment, please contact the support team";
               }
           }else{
               $this->db->trans_rollback();
           }
     echo json_encode($feedback);
 }

}
