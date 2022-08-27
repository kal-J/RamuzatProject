<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Description of System_automations
 *
 * @author Reagan
 */
class Notification extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('helpers');
        $this->load->model("organisation_model");
    }

     public function sendEmail($account_id,$account_no,$amount,$msg_type,$account_no2=0)
    {    
       if($msg_type==1){
             $msg="Withdraw";
       } else if($msg_type==2){
            $msg="Deposit";
       }else {
            $msg="Transfer";
       }
        
        if($msg_type==3 && $account_no2==0){
            $message=$msg." of amount ".number_format($amount,2)."/= has been made to your account ".$account_no." today ".date('d-m-Y H:i:s')." from account ".$account_no2.". Thanks";
        }else{
            $content= $msg_type==3?' from your account '.$account_no.' to account '.$account_no2:' on account '.$account_no;         
            $message=$msg." of amount ".number_format($amount,2)."/= has been made".$content." today ".date('d-m-Y H:i:s').". Thanks";
        }

        $this->helpers->send_email($account_id,$message,false);
       
    }

    public function sendEmailLoan(){
        $message = "Your loan with loan number " . $response['client_loan']['loan_no'] . " has been approved today on " . date('d-m-Y') . " your to recieve it soon";
    }



    public function sendSMS($acc_id){
         $orgdata['org'] = $this->organisation_model->get(1);
        $orgdata['branch'] = $this->organisation_model->get_org(1);
        $this->organisation=$orgdata['org']['name'];        
        $this->contact_number=$orgdata['branch']['office_phone'];

        $message=$message.". ".$this->organisation.", Contact ".$this->contact_number;
        $this->helpers->notification($acc_id,$message,false);
    }
}