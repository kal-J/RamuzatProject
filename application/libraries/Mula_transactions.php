<?php
/**
 * @Author Eric
 */
require_once FCPATH . 'application/libraries/MulaAuthenticator.php';
#required https requests
use GuzzleHttp\Client;

if (!defined('BASEPATH'))
    exit("No direct script access allowed");
class Mula_transactions {
        public $authenticator;

        public function __construct(){
          $this->CI = & get_instance();
          $this->CI->load->model('payment_model', '', TRUE);
          $this->authenticator = new MulaAuthenticator();
        }

        public function request_encryption($request_data){
          $encrypted_data = $this->authenticator->encryptData($request_data);
          return $encrypted_data;
        }
        //a function querying For Checkout Status
        public function queryForStatus(){
           $request = [
               "merchantTransactionID" => $_POST['merchantTransactionID'],
               "checkoutRequestID" => $_POST['checkoutRequestID'],
           ];

           $url = "https://beep2.cellulant.com:9212/checkout/v2/custom/request/query-status";

           $access_token = $this->authenticator->authenticate();

           $client = new Client();
           $response = $client->post(
               $url,
               [
                   'json' => $request,
                   'headers' => [
                       'Accept' => 'application/json',
                       'Authorization' => "Bearer {$access_token}"
                   ]
               ]
           );

           $response = json_decode($response->getBody()->getContents(), true);

           return $response;
        }

        //a function acknowledging Payment
        public function acknowledge_payment(){
               $request = [
                 "merchantTransactionID" => $_POST['merchantTransactionID'], 
                 "checkoutRequestID" => $_POST['checkoutRequestID'],
                 "statusCode" => $_POST['requestStatusCode'], 
                 "statusDescription" => $_POST['requestStatusDescription'],
                 "receiptNumber" => $_POST['payments']['cpgTransactionID']
               ];

               $url = "https://beep2.cellulant.com:9212/checkout/v2/custom/request/acknowlege-payments";

               $access_token = $this->authenticator->authenticate();
               
               $sent_data['token']=$access_token;
               $this->CI->payment_model->mula_test($sent_data['token']);
               $client = new Client();
               $response = $client->post(
                 $url,
                 [
                      'json' => $request,
                      'headers' => [
                      'Accept' => 'application/json',
                      'Authorization' => "Bearer {$access_token}" ]
                 ]
               );

               $response = json_decode($response->getBody()->getContents(), true);

               return $response;
        }

        //a function responsible for refund
        public function refund(){
               $request = [
                 "merchantTransactionID" => $_POST['merchantTransactionID'], 
                 "checkoutRequestID" => $_POST['checkoutRequestID'],
                 "refundType" => $_POST['refundType'],//full or partial
                 "refundAmount" => $_POST['refundAmount'],//optional if refundtype is full
                 "narration" => $_POST['narration'],
                 "extraDetails" => $_POST['extraDetails']
               ];

               $url = "https://beep2.cellulant.com:9212/checkout/v2/custom/request/initiate-refund";

               $access_token = $this->authenticator->authenticate();

               $client = new Client();
               $response = $client->post(
                 $url,
                 [
                      'json' => $request,
                      'headers' => [
                      'Accept' => 'application/json',
                      'Authorization' => "Bearer {$access_token}" ]
                 ]
               );

               $response = json_decode($response->getBody()->getContents(), true);

               return $response;
        }

}