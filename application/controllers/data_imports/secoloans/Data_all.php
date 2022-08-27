<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_import
 *
 * @author REAGAN enhanced and imported by Ambrose Ogwang 2022/03/23.
 */
class Data_all extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('/');
        }
        $this->load->model("contact_model");
        $this->load->model("user_model");
        $this->load->model("member_model");
        $this->load->model("nextOfKin_model");
        $this->load->model("address_model");
        $this->load->model("Data_import_model");
        
        //$this->account_no="SV00000";
        //$this->share_account_no="SH00000";
        $this->member_no="SL00000";
       
    }

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."secoloans".DIRECTORY_SEPARATOR;
        $file_name = "seco_data.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates($file_path);
        echo json_encode($feedback);
    }

    private function run_updates($file_path) {
        $handle = fopen($file_path, "r");
        $total_clients = $count = 0;
        $field_names = $data_array = [];
        $feedback = ["success" => false, "message" => "File Could not be opened"];
        if ($handle) {
            ini_set('memory_limit', '200M');
            ini_set('upload_max_filesize', '200M');
            ini_set('post_max_size', '200M');
            ini_set('max_input_time', 3600);
            ini_set('max_execution_time', 3600);
            while (($data = fgetcsv($handle, 30048576, ",")) !== FALSE) {
                $data1 = $this->security->xss_clean($data);
                
                if ($count == 0) {//the row with the field_names
                    $field_names = $data1;
                   
                    if($field_names[0] != "CLIENT NO") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key CLIENT ID";
                        fclose($handle);
                        return $feedback;
                    }
                   
                }  else {
                    //$this->account_no = ++$this->account_no;
                   // $this->share_account_no = ++$this->share_account_no;
                    $this->member_no = ++$this->member_no;
                    $total_clients = $total_clients + $this->insert_user_data($data1);
                }
              
                $count++;
            }
            fclose($handle);

            if (is_numeric($total_clients)) {
                $feedback["success"] = true;
                $feedback["message"] = "Update done\n $total_clients records updated";
            }
        }
        return $feedback;
    }

    private function insert_user_data($customer_data){
     
        
        $date_registered = "2021-12-31";
           
        if ($customer_data[1] != "" && $customer_data[1] != NULL) {
            // Maritial status should be 1.
            $names = explode(" ", trim($customer_data[1]));
            $single_row = [
               
                "firstname" => $names[0],
                "lastname" => isset($names[1])?$names[1]:'',
                "othernames" => isset($names[2])?$names[2]:'',
                "gender" => (int)$customer_data[2]==1 ? 1:0,
               // "email" => isset($customer_data[5])?strtolower($customer_data[5]):'',
               "marital_status_id" => 1,
               // "date_of_birth" => $date_registered,
               // "nid_card_no" => isset($customer_data[8]) ? $customer_data[8]:'',
                "children_no" => 0,
                "dependants_no" => 0,
                "status" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1
            ];
            $user_id = $this->user_model->add_user($single_row);
          
            $member_data = [
                "user_id" => $user_id,
                "client_no" => $this->member_no,
                "branch_id" => 1, 
                "subscription_plan_id" => 1, 
                "occupation" =>'N/L',
                "registered_by" => 1,
               // "introduced_by_id" => isset($customer_data[16]) ? (int)$customer_data[16]:'',
                "date_registered" => $date_registered,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1,
                "status_id"=>1
            ];
          $member_id = $this->member_model->add_member(false, false, $member_data);
          
           //$this->create_savings_accounts($member_id,$customer_data);
           //$this->insert_next_of_kin($user_id,$customer_data);
           //$this->insert_user_addres($user_id,$customer_data);
           //$this->insert_contacts($user_id,$customer_data);
           //$this->do_member_referral($customer_data);

        
            
           //$this->pay_membership_fees($member_id,$customer_data[1]);
           //$this->pay_subscription($member_id,$customer_data[1]);
            return 1;
        }
        return 0;
    }



    private function insert_contacts($user_id, $customer_data) {
        if (isset($customer_data[3]) && $customer_data[3] !== "") {
            $phone_number = $customer_data[3];
            $this->do_insert_contacts($user_id, $phone_number);
        }
        if (isset($customer_data[4]) && $customer_data[4] !== "") {
            $phone_number = $customer_data[4];
            $this->do_insert_contacts($user_id, $phone_number);
        }
    }

    private function do_insert_contacts($user_id, $phone_number) {

        $data = [
            "user_id" => $user_id,
             "mobile_number" =>isset($phone_number)?$phone_number:'',
            "contact_type_id" => 1,
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
        ];
        return $this->contact_model->add_contact(false, $data);
    }

     private function insert_next_of_kin($user_id,$customer_data){
        if($customer_data[10] != "" && $customer_data[10] != NULL) {
            $nextOfKinName = explode(" ",$customer_data[10]);
        }
         $data = [
            "user_id" => $user_id,
            "firstname" => isset($nextOfKinName[0])? $nextOfKinName[0]:'',
            "lastname" =>  isset($nextOfKinName[1]) ? $nextOfKinName[1]:'',
            "othernames" => isset($nextOfKinName[2])?$nextOfKinName[2]:'',
            "gender" => isset($customer_data[11]) && $customer_data[11]==1 ? "M":(isset($customer_data[11]) && $customer_data[11]==0 ? "F":""),
            "telphone"=>isset($customer_data[12]) ? $customer_data[12]:'',
            "created_by"=> 1,
            "date_created" =>time(),
            "status_id"=>1
         ];
         $data = $this->nextOfKin_model->set($data);
     }
     // User Address 
    private function insert_user_addres($user_id,$customer_data){
         $data = [
           "user_id" => $user_id,
           "address1"=> isset($customer_data[14]) ? $customer_data[14]:'',
           "address_type_id" =>1,
           "date_created" => time(),
           'start_date' => "2022-01-01",
           'end_date' => "2022-12-31",
         ];
         $data = $this->address_model->set3($data);
    }
    // do member referral if supplied 
    private function do_member_referral($customer_data){
        if($customer_data[16] !="" && $customer_data[16]!=NULL){
        $data = [
            "member_id" => isset($customer_data[0]) ? (int) $customer_data[0]+1:'',
            "introduced_by_id" => isset($customer_data[16]) ? $customer_data[16]+1:''
        ];
         $data=$this->member_model->do_member_referral_via_data_import($data);
        }
    }
    public function create_savings_accounts($member_id,$customer_data) {
      
        $data = [
            
            "member_id" => $member_id,
            "status_id" =>7,
            "client_type" =>1,
            "deposit_Product_id" =>1,
            "account_no" =>strtoupper($customer_data[1]),
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1
        ];
        
       $this->load->model("Data_import_model");
       $savings_account=$this->Data_import_model->add_savings_account($data);
       $this->create_share_accounts($member_id,$customer_data);
      // $this->insert_saving_transaction_data($member_id,$customer_data,$savings_account);
       
      }
   
    private function create_share_accounts($member_id,$customer_data) {
        // creating a only selected member a/cs as they few (5)
        
        $data = [
            "member_id" => $member_id,
            "status_id" =>1,
            "share_issuance_id"=>1,
            "date_opened"=>date('Y-m-d'),
            //"default_savings_account_id"=>$savings_account,
            "share_account_no" => strtoupper($customer_data[1]),
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1
         
             
            
        ];
   
    
        
        $this->load->model("Data_import_model");
        $share_accounts= $this->Data_import_model->set_share_state($data);
   
       //$this->insert_share_transaction_data($member_id,$share_accounts,$customer_data);
    
    }

    public function pay_membership_fees($member_id,$member_name) {
            $trans_row = [
                "transaction_no" => date('yws').mt_rand(100000,999999),
                "member_id" => $member_id,
                "amount" => 20000,
                "member_fee_id" => 1,
                "payment_id" =>1,
                "requiredfee"=>1,
                "payment_date" =>'2022-01-01',
                "narrative" => "Membership Fees [ ".$member_name." ]",
                "status_id" => 1,
                "fee_paid" => 1,
                "date_created" => time(),
                "created_by" => 1
            ];
        $this->Data_import_model->add_membership_fees($trans_row);
    }

    public function pay_subscription($member_id,$member_name){
         $trans_row = [
                "transaction_no" => date('yws').mt_rand(10000,99999),
                "client_id" => $member_id,
                "amount" => 10000,
                "transaction_channel_id" => 1,
                "payment_id" =>1,
                "subscription_date" =>'2022-01-01',
                "payment_date" => '2021-01-01',
                "narrative" => "ANNUAL SUBSCRIPTION [ ".$member_name." ]",
                "sub_fee_paid" => 1,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1
            ];

        $this->Data_import_model->add_subscription_fees($trans_row);
    }
/*
    private function insert_saving_transaction_data($member_id,$customer_data,$savings_account) {  

       
        $transaction_date = "2021-12-31";
      
        $credit = $customer_data[8]>0?floatval($customer_data[8]):0;
       
            $trans_row = [
                "transaction_no" =>  date('yws').mt_rand(1000000,9999999),
                "member_id" => $member_id,
                "account_no_id" => $savings_account,
                "debit" => NULL,
                "credit" => $credit,
                "transaction_type_id" => 2,
                "payment_id" =>2,
                "transaction_date" => $transaction_date,
                "narrative" =>"Imported savings balance ". "transaction for [ ".$customer_data[5]." ]",
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1,
                'deposit_product_id' => 1,
                "branch_id" => intval($customer_data[2]),
                "organisation_id"=> 1
            ];
          
             unset($trans_row['member_id'],$trans_row['deposit_product_id']);
            $transaction_data=$this->Data_import_model->add_transaction($trans_row);
           
             $single_row = [
                "journal_type_id" =>7,
                "ref_id" => $transaction_data['transaction_id'],
                "ref_no" => $transaction_data['transaction_no'],
                "description" => "Imported savings balance ". "transaction for [ ".$customer_data[5]." ]",
                "transaction_date" =>  $transaction_date,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" =>1,
                "branch_id" => intval($customer_data[2]),
                "organisation_id"=> 1
            ];
            $insert_id=$this->Data_import_model->add_journal_tr($single_row);
             if(!empty($insert_id)){
                 
                $data[0] = [
                    'debit_amount' => NULL,
                    'transaction_date' =>  $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'credit_amount' =>$credit,
                    'narrative' => "Imported savings balance "." transaction for [ ".$customer_data[1]." ] made on " . $transaction_date,
                    'account_id' =>8 ,
                    'status_id' => 1,
                    "organisation_id"=> 1,
                    "branch_id"=>$customer_data[2]
                ];
                $data[1] = [
                    'credit_amount' =>NULL,
                    'transaction_date' =>  $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'debit_amount' => $credit,
                    'narrative' => "Imported savings balance "." transaction for [ ".$customer_data[1]." ] made on " . $transaction_date,
                    'account_id' => 27,
                    'status_id' => 1,
                    
                ];
             return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
             }else{
                  echo "journal failed";die();
             }
       
            

    }*/

    // share transaction
    public function insert_share_transaction_data($member_id,$share_accounts,$customer_data)
    {
      // 5 shares hold transaction for account ids [3,71,75,78,58]
        $transaction_date = "2021-12-31";
        $credit = 60000000;
       
        $trans_row = [
            "transaction_no" =>  date('yws') . mt_rand(1000000, 9999999),
            "share_account_id" => $share_accounts,
            "share_issuance_id" => 1,
            "debit" => NULL,
            "credit" =>  $credit ,
            "transaction_type_id" => 9,
            "payment_id" => 2,
            "transaction_date" => $transaction_date,
            "narrative" =>"Imported Shares balance "." transaction for [ ".$customer_data[1]." ]",
            "status_id" => 1,
            "date_created" => time(),
            "created_by" => 1,
             
        ];
       
       
        $transaction_data = $this->Data_import_model->add_transaction_shares($trans_row);
       
        if (!empty($transaction_data)) {

            $single_row = [
                "journal_type_id" => 22,
                "ref_id" => $transaction_data['transaction_id'],
                "ref_no" => $transaction_data['transaction_no'],
                "description" => "Nanam Financial Service Share as [ ".$transaction_date." ]",
                "transaction_date" =>  $transaction_date,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1,
                 
            ];
            $insert_id = $this->Data_import_model->add_journal_tr($single_row);
           
            if (!empty($insert_id)) {

                $data[0] = [
                    'debit_amount' => NULL,
                    'transaction_date' => $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'credit_amount' =>  $credit,
                    'narrative' => "Imported Shares Balance"."transaction for [".$customer_data[1]."]",
                    'account_id' => 32,
                    'status_id' => 1,
                    
                ];
                $data[1] = [
                    'credit_amount' => NULL,
                    'transaction_date' => $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'debit_amount' => $credit ,
                    'narrative'=> "Imported Shares Balance"."transaction for [ ".$customer_data[1]."]",
                    'account_id' => 40,
                    'status_id' => 1,
                     
                ];

               
                return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
            } else {
                echo "journal failed";
                die();
            }
        } else {
            echo "transaction failed";
            die();
        }
    }

    // update to new number format for NKD client.
    public function updateToNewNumberFormat($data){
        $this->member_model->updateToNewNumberFormat($data);
    }

}
