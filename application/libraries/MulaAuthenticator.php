<?php
/**
 * @Author Eric
 */

#required for authentication methods used during payments
use GuzzleHttp\Client;

if (!defined('BASEPATH'))
    exit("No direct script access allowed");
class MulaAuthenticator{
        protected $mm_channel_data;
        protected $CI;
        public function __construct(){
          // Assign the CodeIgniter super-object
          $this->CI = & get_instance();
          $this->CI->load->model('payment_engine_model', '', TRUE);
          $this->mm_channel_data=$this->CI->payment_engine_model->get_requirement(1);
        }
    //for authentication purposes on all requests to the api
    public function authenticate(){
        $client = new Client();
        $auth_url="https://beep2.cellulant.com:9212/checkout/v2/custom/oauth/token";
        $response = $client->post(
                          $auth_url, //config('auth_url'),
                          [
                           'form_params' => [
                               'grant_type' => 'client_credentials',
                               'client_id' => $this->mm_channel_data['client_id'],
                               'client_secret' => $this->mm_channel_data['client_secret']
                              ]
                          ]);
       $access_token = json_decode((string) $response->getBody(),true);
       return $access_token['access_token'];
    }
    
    //for encryption of all requestes
    public function encryptData ($payload = []) {
        $dashIV=$this->mm_channel_data['iv_key'];//"BbP34NWznx2LJMX8"
        $dashkey=$this->mm_channel_data['secret_key'];//"HdzDrLNmnPyJKYvc"
        //The encryption method to be used
        $encrypt_method = "AES-256-CBC" ;
        // Hash the secret key
        $key = hash ( 'sha256' , $dashkey );
        // Hash the iv - encrypt method AES-256-CBC expects 16 bytes
        $iv = substr ( hash ( 'sha256' , $dashIV ), 0 , 16 );
        $encrypted = openssl_encrypt (
        json_encode ($payload , true ),
        $encrypt_method ,
        $key ,
        0 ,
        $iv
        );
        //Base 64 Encode the encrypted payload
        $encrypted = base64_encode ($encrypted);
       $result = array(
            'params' => $encrypted,
            'accessKey' => $payload['accessKey'],
            'countryCode' => $payload['countryCode']
        );

       return $result;
    }

}