<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_import
 *
 * @author REAGAN
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
        
        $this->account_no="SV00000";
        $this->share_account_no="SH00000";
        $this->member_no="MCEE00000";
       
    }

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."agridebt".DIRECTORY_SEPARATOR;
        $file_name = "agridebt_data.csv";
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
                   
                    if($field_names[0] != "NO") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key Client_no";
                        fclose($handle);
                        return $feedback;
                    }
                   
                }  else {
                    $this->account_no = ++$this->account_no;
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
        //echo json_encode($customer_data);die;
        if($customer_data[7]){
            $date_registered = $customer_data[7];
            $date_registered  = $this->helpers->extract_date_time($date_registered,"Y-m-d");
        }else{
            $date_registered = "2021-01-01";
        }
        //$date_registered = $customer_data[7] !=''? $customer_data[7]: '2022-01-01';
        
        if ($customer_data[0] != "" && $customer_data[0] != NULL) {
            $names = explode(" ", trim($customer_data[5]));
            $single_row = [
               
                "firstname" => $names[0],
                "lastname" => isset($names[1])?$names[1]:'',
                "othernames" => isset($names[2])?$names[2]:'',
                "gender" => strtoupper($customer_data[6])=="M" ? 1:0,
                "email" => isset($customer_data[4])?$customer_data[4]:'',
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
                "occupation" => 'N/L',
                "registered_by" => 1,
                "date_registered" => $date_registered,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1
            ];
            $member_id = $this->member_model->add_member(false, false, $member_data);
          
            // if ($customer_data[5]) {
            //     $this->do_insert_contacts($user_id,$customer_data[5]);
            // }
            // if ($customer_data[6]) {
            //     $this->do_insert_contacts($user_id,$customer_data[6]);
            // }
 
           $this->create_savings_accounts($member_id);
           $this->pay_membership_fees($member_id,$customer_data[1]);
           $this->pay_subscription($member_id,$customer_data[1]);
            return 1;
        }
        return 0;
    }

    // private function do_insert_contacts($user_id, $phone_number) {
    //     //echo json_encode($phone_number);die();
    //     $data = [
    //         "user_id" => $user_id,
    //         "mobile_number" => $phone_number,
    //         "contact_type_id" => 1,
    //         "date_created" => time(),
    //         "created_by" => 1,
    //         "modified_by" => 1,
    //     ];
    //     return $this->contact_model->add_contact(false, $data);
    // }

    public function create_savings_accounts($member_id) {
      
        $data = [
            "member_id" => $member_id,
            "status_id" =>7,
            "client_type" =>1,
            "deposit_Product_id" =>1,
            "account_no" => $this->account_no,
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
        ];
        
        $this->load->model("Data_import_model");
       $savings_account=$this->Data_import_model->add_savings_account($data);
       $this->create_share_accounts($member_id,$savings_account);
       $this->insert_saving_transaction_data($data);
       
    }
   
    private function create_share_accounts($member_id,$savings_account) {
        $data = [
            "member_id" => $member_id,
            "status_id" =>1,
            "share_issuance_id"=>1,
            "date_opened"=>date('Y-m-d'),
            "default_savings_account_id"=>$savings_account,
            "share_account_no" => $this->share_account_no,
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
        ];
        
        $this->load->model("Data_import_model");
        $this->Data_import_model->set_share_state($data);
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

    private function insert_saving_transaction_data($transaction,$data) {  
        $transaction_date = "2021-12-31";
      // echo $transaction_date; die();
        $credit = floatval($transaction[8]);
       
            $trans_row = [
                "transaction_no" =>  date('yws').mt_rand(1000000,9999999),
                "member_id" => $transaction[0],
              //  "account_no_id" => $transaction[0],
                "debit" => NULL,
                "credit" => $credit,
                "transaction_type_id" => 2,
                "payment_id" =>2,
                "transaction_date" => $transaction_date,
                "narrative" =>"Imported savings balance ". "transaction for [ ".$transaction[5]." ]",
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1,
                'deposit_product_id' => 1,
            ];
            //echo json_encode(($trans_row)); die;
             unset($trans_row['member_id'],$trans_row['deposit_product_id']);
            $transaction_data=$this->Data_import_model->add_transaction($trans_row);
            //echo json_encode(($transaction_data)); die;
 
             $single_row = [
                "journal_type_id" =>7,
                "ref_id" => $transaction_data['transaction_id'],
                "ref_no" => $transaction_data['transaction_no'],
                "description" => "Imported savings balance ". "transaction for [ ".$transaction[1]." ]",
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
                    'credit_amount' =>$credit,
                    'narrative' => "Imported savings balance "." transaction for [ ".$transaction[1]." ] made on " . $transaction_date,
                    'account_id' =>8 ,
                    'status_id' => 1
                ];
                $data[1] = [
                    'credit_amount' =>NULL,
                    'transaction_date' =>  $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'debit_amount' => $credit,
                    'narrative' => "Imported savings balance "." transaction for [ ".$transaction[1]." ] made on " . $transaction_date,
                    'account_id' => 27,
                    'status_id' => 1
                ];
             return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
             }else{
                  echo "journal failed";die();
             }
       
            

    }

    // share 
    public function insert_share_transaction_data($transaction)
    {
        //$transaction_date = $this->helpers->extract_date_time($transaction[7], "Y-m-d");
        $transaction_date = "2021-12-31";
        $credit =floatval($transaction[7]);
        //echo $transaction_date; die();

        $trans_row = [
            "transaction_no" =>  date('yws') . mt_rand(1000000, 9999999),
            "share_account_id" => $transaction[0],
            "share_issuance_id" => 1,
            "debit" => NULL,
            "credit" =>  $credit ,
            "transaction_type_id" => 9,
            "payment_id" => 2,
            "transaction_date" => $transaction_date,
            "narrative" =>"Imported Shares balance "." transaction for [ ".$transaction[5]." ]",
            "status_id" => 1,
            "date_created" => time(),
            "created_by" => 1
        ];
        $transaction_data = $this->Data_import_model->add_transaction_shares($trans_row);
        if (!empty($transaction_data)) {

            $single_row = [
                "journal_type_id" => 22,
                "ref_id" => $transaction_data['transaction_id'],
                "ref_no" => $transaction_data['transaction_no'],
                "description" => "AGRIDEPT Share as [ ".$transaction_date." ]",
                "transaction_date" =>  $transaction_date,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1
            ];
            $insert_id = $this->Data_import_model->add_journal_tr($single_row);
           
            if (!empty($insert_id)) {

                $data[0] = [
                    'debit_amount' => NULL,
                    'transaction_date' => $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'credit_amount' =>  $credit,
                    'narrative' => "Imported Shares Balance"."transaction for [".$transaction[1]."]",
                    'account_id' => 32,
                    'status_id' => 1
                ];
                $data[1] = [
                    'credit_amount' => NULL,
                    'transaction_date' => $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'debit_amount' => $credit ,
                    'narrative'=> "Imported Shares Balance"."transaction for [ ".$transaction[1]."]",
                    'account_id' => 40,
                    'status_id' => 1
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
}
