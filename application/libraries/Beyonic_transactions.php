<?php
/**
 * @Author Eric modified by reagan
 */
if (!defined('BASEPATH'))
    exit("No direct script access allowed");
class Beyonic_transactions {
        protected $mm_channel_data;
        protected $CI;
        public function __construct(){
          // Assign the CodeIgniter super-object
          $this->CI = & get_instance();
          $this->CI->load->model('payment_engine_model', '', TRUE);
          $this->mm_channel_data=$this->CI->payment_engine_model->get_requirement(1);
          //Setting the api key
          //Beyonic::setApiKey($this->mm_channel_data['api_key']);//"ab594c14986612f6167a975e1c369e71edab6900"
          Beyonic::setApiKey("aa4ad6fd1cb26fcbac18d8140a2cd25a206115fd");//"ab594c14986612f6167a975e1c369e71edab6900"
          //version of the api
          define("BEYONIC_CLIENT_VERSION", "0.0.15");
        }

        //responsible for formatting contacts into international format
        public function format_contact($contact){

            if (preg_match("/^[\+]+[0-9]{12,12}$/", $contact)) {
                $mobile_number=$contact;
            }elseif (preg_match("/^[07]+[0-9]{9,10}$/", $contact)){
                $mobile_number='+256'.substr($contact,-9);
            }
            return $mobile_number;
        }

        //responsible for creating a collection request
        public function new_collection($col_instructions){
          $phonenumber=$this->format_contact($col_instructions['phone_number']);
          
          try {
            $collection_request = Beyonic_Collection_Request::create(array(
                "phonenumber" =>$phonenumber,// $phonenumber
                "amount" => $col_instructions['amount'],
                "first_name" => $col_instructions['first_name'],
                "last_name" => $col_instructions['last_name'],
                "currency" => "UGX",// UGX
                "success_message" => "{customer} you have Deposited {amount} /= on your savings account .Thank you!",
                "metadata" => array("mnt_trx_id"=>"1256S2334", "name"=>"Raj"),
                "send_instructions" => True
              ));
            return $collection_request;
          } catch (Beyonic_Exception $e) {
           return print_r($e->getMessage().' '.$e->responseBody);
          }
        }

        //Retreiving all collection requests from the system
        public function get_collection_request($filter=[]){
          try {
            $collection_requests = Beyonic_Collection_Request::getAll($filter);
            return $collection_requests;
          } catch (Beyonic_Exception $e) {
            return $e->getMessage().' '.$e->responseBody;
          }
        }

        //This function can be used to send mobile money or airtime to any number
        public function new_payment($pay_instructions){
          $phonenumber=$this->format_contact($pay_instructions['phone_number']);
          $payment_data = array(
                "phonenumber" => $phonenumber,
                "first_name" => $pay_instructions['first_name'],
                "last_name" => $pay_instructions['last_name'],
                "amount" => $pay_instructions['amount'],
                "currency" => "UGX",
                //"account" => "2951262",//From beyonic dashboard. The id of the account money will be deduct
                "description" => "DISBURSE LOAN TO ".$pay_instructions['first_name']." ".$pay_instructions['last_name'],//$pay_instructions['description'],//should be limited to 140 characters
                "payment_type" => "money",//$pay_instructions['payment_type'],//money for mobile money or airtime if you want to send airtime
                "metadata" => array("mnt_trx_id"=>$pay_instructions['merchant_transaction_id'])
              );
          try {
              $payment = Beyonic_Payment::create($payment_data);
              //print_r($payment);
              return $payment;
            } catch (Beyonic_Exception $e) {
             return $e->getMessage().' '.$e->responseBody;
            }
        }
///base_url('System_automations/callback')
        //This function can be used to send mobile money or airtime to many clinets or numbers
        public function bulk_payment($client_payment,$pay_instructions){
           try {
                $payment = Beyonic_Payment::create(array(
                  "currency" => "UGX",
                  "account" => "1",//From beyonic dashboard. The id of the account money will be deduct
                  "payment_type" => $pay_instructions['payment_type'],//money for mobile money or airtime if you want to send airtime
                  "metadata" => array("merchant_transaction_id"=>$pay_instructions['merchant_transaction_id']),
                  "recipient_data" => array($client_payment),
                  "callback_url" => "https://my.website/payments/callback"
                ));
                return $payment;
            } catch (Beyonic_Exception $e) {
             return $e->getMessage().' '.$e->responseBody;
            }
        }

        //Retreiving all payments from the system
        public function get_payment_list($filter=[]){
          try{
              $payments = Beyonic_Payment::getAll($filter);
              return $payments;
            } catch (Beyonic_Exception $e) {
              return $e->getMessage().' '.$e->responseBody;
            }
        }

        //Retreiving single payment from beyonic
        public function get_payment_id($id){
          try{
              $payments = Beyonic_Payment::get($id);
              return $payments;
            } catch (Beyonic_Exception $e) {
              return $e->getMessage().' '.$e->responseBody;
            }
        }


        //claiming un matched deposit
        public function claim_collection($claim_data){
            $phonenumber=$this->format_contact($claim_data['phone_number']);
            try {
              $collections = Beyonic_Collection::getAll(array(
                "phonenumber" => $phonenumber,
                "remote_transaction_id" => $claim_data['remote_transaction_id'],
                "claim" => "True",
                "amount" =>  $claim_data['amount'],
              ));
              return $collections;
            } catch (Beyonic_Exception $e) {
               return $e->getMessage().' '.$e->responseBody;
            }
        }

}