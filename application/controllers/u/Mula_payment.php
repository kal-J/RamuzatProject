<?php
/**
 * @Author Eric
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Mula_payment extends CI_Controller {
  
    public function __construct() {
     parent::__construct();
     $this->request = !empty($_POST)? $_POST:json_decode(file_get_contents('php://input'), true); 

     $this->load->library("session");
     $this->load->library("mula_transactions");
     $this->load->library("payment_transactions");
     $this->load->model("payment_model");
     // if(empty($this->session->userdata('id'))){
     //        redirect('welcome');
     //    } 
    }

    //encryption requests are directed  here
    public function encryption(){
      $response['success']=true;
       $response= $this->mula_transactions->request_encryption($this->request);
       echo json_encode($response);
    }
    //acknowledgement requests are directed  here
    public function acknowledge_payment(){
      $response=false;
      if (isset($_POST['merchantTransactionID'])) {
        $response= $this->mula_transactions->acknowledge_payment();
        $sent_data['response']= $response;
        $this->payment_model->mula_test($sent_data);
      }
      return $response;
    }
    //merchant_transaction_id requests are directed  here
    public function get_transactionID(){
      $response['success']=true;
      $response= $this->payment_transactions->merchantTransaction();
      //print_r($response);
      echo json_encode($response);
    }

    public function jsonList(){
      $response['data']= $this->payment_model->get();
      echo json_encode($response);
    }

}