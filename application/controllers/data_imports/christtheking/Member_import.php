<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_import
 *
 * @author REAGAN
 */
class Member_import extends CI_Controller {

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
        
        $this->share_account_no="SH000000";
        $this->member_no="CTK000000";
    }

    public function index() {
        $this->db->trans_start();
        $folder = "data_extract".DIRECTORY_SEPARATOR."christtheking".DIRECTORY_SEPARATOR;
        $file_name = "MEMBER_AND_SAVINGS.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates($file_path);
        $this->db->trans_complete();
        
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
                    if ($field_names[0] != "ID") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key Account NO";
                        fclose($handle);
                        return $feedback;
                    }
                } else {
                   
                    $this->share_account_no = ++$this->share_account_no;
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

    private function insert_user_data($customer_data) {
        $date_registered = '2022-01-01';
      
        if ($customer_data[0] != "" && $customer_data[0] != NULL) {
            $names = explode(" ", trim($customer_data[2]));
            $name2=isset($names[2])?$names[2]:'';
            $name3=isset($names[3])?$names[3]:'';
            $name4=isset($names[4])?$names[4]:'';
            $name5=isset($names[5])?$names[5]:'';
            $name6=isset($names[6])?$names[6]:'';
            $single_row = [
                "firstname" => $names[0],
                "lastname" => isset($names[1])?$names[1]:'',
                "othernames" => $name2." ".$name3." ".$name4." ".$name5." ".$name6." ",
                "gender" => 0,
                "email" => '',
                "marital_status_id" => 1,
                "date_of_birth" => $date_registered,
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
                "occupation" => '',
                "registered_by" => 1,
                "date_registered" => $date_registered,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1,
                "status_id" => 1
            ];
            $member_id = $this->member_model->add_member(false, false, $member_data);

            // if ($customer_data[2]) {
            //     $this->do_insert_contacts($user_id,"0".$customer_data[5]);
            // }
            /* if ($customer_data[4]) {
                $this->do_insert_contacts($user_id,"0".$customer_data[6]);
            } */
        
            $this->create_savings_accounts($member_id,$customer_data);
        
            //$this->pay_membership_fees($member_id,$customer_data[1]);
            //$this->pay_subscription($member_id,$customer_data[1]);
            return 1;
        }
        return 0;
    }

    private function do_insert_contacts($user_id, $phone_number) {
        $data = [
            "user_id" => $user_id,
            "mobile_number" => $phone_number,
            "contact_type_id" => 1,
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
        ];
        return $this->contact_model->add_contact(false, $data);
    }

    public function create_savings_accounts($member_id,$customer_data) {
       if ($customer_data[1]>0) {
        $data = [
            "member_id" => $member_id,
            "status_id" =>7,
            "client_type" =>1,
            "deposit_Product_id" =>1,
            "account_no" => $customer_data[1],
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
        ];
        
        $this->load->model("Data_import_model");

        $savings_account=$this->Data_import_model->add_savings_account($data);
        }else{
          $savings_account=0;  
        }
        if ($customer_data[3]>0) {
            $this->insert_transaction_data_savings($savings_account,$customer_data);
        }
       $this->create_share_accounts($member_id,$savings_account,$customer_data);

    }
    
    private function create_share_accounts($member_id,$savings_account,$customer_data) {
        $data = [
            "member_id" => $member_id,
            "status_id" =>1,
            "share_issuance_id"=>1,
            "date_opened"=>'2022-01-01',
            "default_savings_account_id"=>$savings_account,
            "share_account_no" => $this->share_account_no,
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
        ];
        
        $this->load->model("Data_import_model");
         $share_account =$this->Data_import_model->set_share_state($data);
        $this->insert_transaction_data_shares($share_account,$customer_data);

    }

    public function pay_membership_fees($member_id,$member_name) {
            $trans_row = [
                "transaction_no" => date('yws').mt_rand(100000,999999),
                "member_id" => $member_id,
                "amount" => 20000,
                "member_fee_id" => 1,
                "payment_id" =>1,
                "requiredfee"=>1,
                "payment_date" =>'2021-01-01',
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
                "subscription_date" =>'2021-01-01',
                "payment_date" => '2021-01-01',
                "narrative" => "ANNUAL SUBSCRIPTION [ ".$member_name." ]",
                "sub_fee_paid" => 1,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1
            ];

        $this->Data_import_model->add_subscription_fees($trans_row);
    }


    private function insert_transaction_data_shares($acc,$transaction) {  
        $transaction_date = '2020-12-31';
         //echo $transaction_date; die();

            $trans_row = [
                "transaction_no" =>  date('yws').mt_rand(1000000,9999999),
                "share_account_id" => $acc,
                "share_issuance_id"=>1,
                "debit" => NULL,
                "credit" => $transaction[4],
                "transaction_type_id" => 9,
                "payment_id" =>2,
                "transaction_date" => $transaction_date,
                "narrative" => "SHARES [ " . $transaction[2]." ][ ". $transaction[1]." ]",
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1
            ];
            $transaction_data=$this->Data_import_model->add_transaction_shares($trans_row);
             if(!empty($transaction_data)){
            
             $single_row = [
                "journal_type_id" =>22,
                "ref_id" => $transaction_data['transaction_id'],
                "ref_no" =>$transaction_data['transaction_no'],
                "description" =>"SHARES [ " . $transaction[2]." ][ ". $transaction[1]." ]",
                "transaction_date" =>  $transaction_date,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" =>1
            ];
            $insert_id=$this->Data_import_model->add_journal_tr($single_row);
            //print_r($insert_id); die;

             if(!empty($insert_id)){
                 
                $data[0] = [
                    'debit_amount' => NULL,
                    'transaction_date' =>$transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'credit_amount' =>$transaction[4],
                    'narrative' => "SHARES [ " . $transaction[2]." ][ ". $transaction[1]." ]",
                    'account_id' =>32,
                    'status_id' => 1
                ];
                $data[1] = [
                    'credit_amount' =>NULL,
                    'transaction_date' =>$transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'debit_amount' => $transaction[4],
                    'narrative' => "SHARES [ " . $transaction[2]." ][ ". $transaction[1]." ]",
                    'account_id' => 40,
                    'status_id' => 1
                ];

             return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
             }else{
                  echo "journal failed";die();
             }
        }else{
            echo "transaction failed";die();
        }
            
        
    }


    private function insert_transaction_data_savings($acc,$transaction) {  
        $transaction_date = '2022-01-19';
        //$transaction_date = $this->helpers->extract_date_time($transaction_date,"Y-m-d");
        // echo $transaction_date; die();
            $amount=abs($transaction[3]);
           
            $credit_account_id=8;
            $debit_account_id=40;

   
       
            $trans_row = [
                "transaction_no" =>  date('yws').mt_rand(1000000,9999999),
                "account_no_id" => $acc,
                "debit" => NULL,
                "credit" => $amount,
                "transaction_type_id" => 2,
                "payment_id" =>2,
                "transaction_date" => $transaction_date,
                "narrative" => "SAVINGS BALANCES AS OF ". $transaction_date,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1
            ];
            //for transfers to shares
            //$this->insert_share_transaction_data($transaction);
            $transaction_data=$this->Data_import_model->add_transaction($trans_row);
             if(!empty($transaction_data)){
            
             $single_row = [
                "journal_type_id" =>7,
                "ref_id" => $transaction_data['transaction_id'],
                "ref_no" => $transaction_data['transaction_no'],
                "description" =>"SAVINGS BALANCES AS OF ". $transaction_date,
                "transaction_date" =>  $transaction_date,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" =>1
            ];
            $insert_id=$this->Data_import_model->add_journal_tr($single_row);
             if(!empty($insert_id)){
                 
                $data[0] = [
                    'debit_amount' => NULL,
                    'transaction_date' =>  $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'credit_amount' =>$amount,
                    'narrative' => "SAVINGS BALANCES AS OF ". $transaction_date,
                    'account_id' =>$credit_account_id,
                    'status_id' => 1
                ];
                $data[1] = [
                    'credit_amount' =>NULL,
                    'transaction_date' =>$transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'debit_amount' => $amount,
                    'narrative' => "SAVINGS BALANCES AS OF ". $transaction_date,
                    'account_id' => $debit_account_id,
                    'status_id' => 1
                ];
             return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
             }else{
                  echo "journal failed";die();
             }
        }else{
            echo "trasaction failed";die();
        }
            
        
    }

    private function insert_transaction_data_savings_w($acc,$transaction) {  
        $transaction_date = '2020-12-31';
        //$transaction_date = $this->helpers->extract_date_time($transaction_date,"Y-m-d");
        // echo $transaction_date; die();
            $amount=abs($transaction[3]);
        
            $credit_account_id=8;
            $debit_account_id=40;

       
            $trans_row = [
                "transaction_no" =>  date('yws').mt_rand(1000000,9999999),
                "account_no_id" => $acc,
                "debit" => $amount,
                "credit" => NULL,
                "transaction_type_id" => 1,
                "payment_id" =>2,
                "transaction_date" => $transaction_date,
                "narrative" => "SAVINGS BALANCES AS OF ". $transaction_date,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1
            ];
            //for transfers to shares
            //$this->insert_share_transaction_data($transaction);
            $transaction_data=$this->Data_import_model->add_transaction($trans_row);
             if(!empty($transaction_data)){
            
             $single_row = [
                "journal_type_id" =>8,
                "ref_id" => $transaction_data['transaction_id'],
                "ref_no" => $transaction_data['transaction_no'],
                "description" =>"SAVINGS BALANCES AS OF ". $transaction_date,
                "transaction_date" =>  $transaction_date,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" =>1
            ];
            $insert_id=$this->Data_import_model->add_journal_tr($single_row);
             if(!empty($insert_id)){
                 
                $data[0] = [
                    'debit_amount' => $amount,
                    'transaction_date' =>  $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'credit_amount' =>NULL,
                    'narrative' => "SAVINGS BALANCES AS OF ". $transaction_date,
                    'account_id' =>$credit_account_id,
                    'status_id' => 1
                ];
                $data[1] = [
                    'credit_amount' =>$amount,
                    'transaction_date' =>$transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'debit_amount' => NULL,
                    'narrative' => "SAVINGS BALANCES AS OF ". $transaction_date,
                    'account_id' => $debit_account_id,
                    'status_id' => 1
                ];
             return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
             }else{
                  echo "journal failed";die();
             }
        }else{
            echo "trasaction failed";die();
        }
            
        
    }
}
