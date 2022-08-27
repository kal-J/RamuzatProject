<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Reagan
 */
class Data_loan_import extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('/');
        }
        ini_set('memory_limit', '200M');
        ini_set('upload_max_filesize', '200M');
        ini_set('post_max_size', '200M');
        ini_set('max_input_time', 3600);
        ini_set('max_execution_time', 3600);

        $this->load->model('client_loan_model');
        // $this->load->model('loan_product_model');
        $this->load->model('client_loan_monthly_income_model');
        $this->load->model('client_loan_monthly_expense_model');
        $this->load->model('payment_details_model');
        $this->load->model('repayment_schedule_model');
        $this->load->model('loan_state_model');
        $this->load->model('loan_installment_payment_model');
        
        $this->load->model('applied_loan_fee_model');
        $this->load->model('Data_import_model');
        $this->load->model('accounts_model');
        $this->schedule_id=0;
        $this->debit_or_credit1 = $this->accounts_model->get_normal_side(1);//loan_receivable_account_id
        $this->debit_or_credit2 = $this->accounts_model->get_normal_side(40, true);//source_fund_account_id
        $this->debit_or_credit3 = $this->accounts_model->get_normal_side(6);//interest_income_account_id
        $this->debit_or_credit4 = $this->accounts_model->get_normal_side(7);//interest_receivable_account_id
    }

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."mutaka".DIRECTORY_SEPARATOR;
        $file_name = "member_loans.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates($file_path);
        echo json_encode($feedback);
    }
    

    private function run_updates($file_path) {
        $handle = fopen($file_path, "r");
        $total_loans = $count = 0;
        $field_names = $data_array = [];
        $client_loans= [];
        $feedback = ["success" => false, "message" => "File Could not be opened"];
        if ($handle) {
            while (($data = fgetcsv($handle, 30048576, ",")) !== FALSE) {
                $data1 = $this->security->xss_clean($data);
                if ($count == 0) {//the row with the field_names
                    $field_names = $data1;
                    if ($field_names[0] != "Loan No") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key LOAN_ID";
                        fclose($handle);
                        return $feedback;
                    }
                } else {
                    
                    $total_loans = $total_loans + $this->insert_loan_data($data1);
                }
                $count++;
            }
            fclose($handle);

            if (is_numeric($total_loans)) {
                $feedback["success"] = true;
                $feedback["message"] = "Update done\n $total_loans records updated";
            }
        }
        return $feedback;
    }

    private function insert_loan_data($loan_data) {
        
        $date_created = time();
        //print($loan_data[2]);die;
        $action_date =$this->extract_date_time_dot($loan_data[8],"Y-m-d");

        $amount_req=$loan_data[5];
        $amount_disb=$loan_data[5];
        $member_id = $loan_data[1];
        $top_up='N';
        $loan_product_id =1;
        $repayment_frequency = $loan_data[4];
        $repayment_made_every = 3;
        $interest_rate = 60;
        // $loan_product_data = $this->loan_product_model->get_product($loan_product_id);
        $installments=1;
        if ($loan_data[0] != "" && $loan_data[0] != 'NULL') {
            $single_row = [
                "member_id" =>  $member_id,
                "loan_no" => $loan_data[0],
                "credit_officer_id" => 2,//static since he is alone
                "loan_product_id" => $loan_product_id,
                "topup_application" => $top_up,
                "requested_amount" => $amount_req,
                "application_date" => $action_date,
                "source_fund_account_id" => 40,//fund_source_account_id
                "disbursement_date" => $action_date,
                "suggested_disbursement_date" => $action_date,
                "interest_rate" => $interest_rate,
                "offset_period" => 0,
                "offset_made_every" => 1,
                "repayment_frequency" => $repayment_frequency,
                "repayment_made_every" => $repayment_made_every,
                "installments" => $installments,
                "link_to_deposit_account" => 1,
                "comment" => 'Loan imported from Excel',
                "amount_approved" => $amount_disb,
                "approval_date" => $action_date,
                "approved_installments" => 1,
                "approved_repayment_frequency" => $repayment_frequency,
                "approved_repayment_made_every" => $repayment_made_every,
                "approved_by" => 1,
                "approval_note" => 'Data imported from excel',
                "loan_purpose" => 'N/L',
                "preferred_payment_id" => 2,
                'date_created' => time(),
                "created_by" => 1
            ];
            //insert into the client table
            $inserted_loan_id=$this->client_loan_model->set2($single_row);

            if ($inserted_loan_id) {
                //add the client incomes
                // if ($loan_data[16]>0) {
                //     $client_income_data = [
                //         "client_loan_id" => $inserted_loan_id,
                //         "income_id" => 1,
                //         "amount" => $loan_data[16],
                //         "status_id" => 1,
                //         "description" => 'Client\'s monthly income',
                //         "date_created" => $date_created,
                //         "created_by" => 1
                //     ];
                // $this->client_loan_monthly_income_model->set($client_income_data);
                // }

               // add the client's expense
                // if ($loan_data[7]>0) {

                //     $member_collaterals = [
                //         "member_id" => $member_id,
                //         "collateral_type_id" => $loan_data[7],
                //         "description" => $loan_data[6],
                //         "item_value" => 0,
                //         "status_id" => 1,
                //         "date_created" => $date_created,
                //         "modified_by"=>$date_created,
                //         "created_by" => 1
                //     ];
                //     $member_collateral_id=$this->Data_import_model->member_collateral($member_collaterals);
                //       if (is_numeric($member_collateral_id)) {

                //     $collaterals = [
                //         "member_collateral_id" => $member_collateral_id,
                //         "client_loan_id" => $inserted_loan_id,
                //         "item_value" => 0,
                //         "status_id" => 1,
                //         "date_created" => $date_created,
                //         "modified_by"=>$date_created,
                //         "created_by" => 1
                //     ];
                //     $this->Data_import_model->user_collateral($collaterals);
                // }

                // }
              
                //Record the state of the loan
                
                $loan_state_details = [
                    "client_loan_id" => $inserted_loan_id,
                    "state_id" => $loan_data[3]=="Approved"?7:10,
                    "comment" => $loan_data[3]=="Closed"?'Obligations met Loan closed - Data imported':'Loan Disbursed - Data imported',
                    "action_date" => $action_date,
                    "date_created" => $date_created,
                    "created_by" => 1
                ];
                $this->loan_state_model->set2($loan_state_details);

                //attach the payment details
                // if ($loan_data[12] != "" && $loan_data[12] != 'NULL') {
                //     $client_payment_details = [
                //         "client_loan_id" => $inserted_loan_id,
                //         "ac_number" => $loan_data[12],
                //         "ac_name" => $loan_data[13],
                //         "bank_name" => $loan_data[14],
                //         "bank_branch" => $loan_data[15],
                //         "status_id" => 1,
                //         "date_created" => $date_created,
                //         "created_by" => 1
                //     ];
                //     $this->payment_details_model->set2($client_payment_details);
                // }

                      //Loan fee addition

                // if ($loan_data[17]>0) {
                //         $loan_fee_data = [
                //                 "client_loan_id" => $inserted_loan_id,
                //                 "loan_product_fee_id" => 1,
                //                 "amount" => $loan_data[17],
                //                 "paid_or_not" => 1,
                //                 "date_paid" => $action_date,
                //                 "status_id" => 1,
                //                 "date_created" =>$date_created,
                //                 "created_by" => 1
                //             ];

                //         $this->applied_loan_fee_model->set2($loan_fee_data);
                // }
                        // $loan_fee_data1 = [
                        //         "client_loan_id" => $inserted_loan_id,
                        //         "loan_product_fee_id" => 2,
                        //         "amount" => $loan_data[13],
                        //         "paid_or_not" => 1,
                        //         "date_paid" =>$action_date,
                        //         "status_id" => 1,
                        //         "date_created" => $date_created,
                        //         "created_by" => 1
                        //     ];
                        // $this->applied_loan_fee_model->set2($loan_fee_data1);

                $repayment_frequency = $loan_data[4];
                $repayment_date = date('Y-m-d',strtotime('+'.$repayment_frequency.' month', strtotime($action_date)));

                $principal_amount= $amount_disb;
                $interest_amount =$loan_data[6];

                $repayment_schedule_array = [
                        "repayment_date" => $repayment_date,
                        "interest_amount" => $interest_amount,
                        "principal_amount" => $principal_amount,
                        "client_loan_id" => $inserted_loan_id,
                        "grace_period_on" => 0,
                        "grace_period_after" => 0,
                        "installment_number" => 1,
                        "interest_rate" => $interest_rate,
                        "repayment_frequency" => $repayment_frequency,
                        "repayment_made_every" => 3,
                        "comment" => 'Loan schedule imported from excel',
                        "payment_status" => $loan_data[3]=="Approved"?2:4,
                        "status_id" => 1,
                        "date_created" => $date_created,
                        "created_by" => 1
                    ];
                $repayment_schedule_id=$this->repayment_schedule_model->set3($repayment_schedule_array);

                $sent_data=[
                        'journal_type_id'=> 4,
                        'ref_no' => $loan_data[0],
                        'ref_id' => $inserted_loan_id,
                        'description' => 'Loan imported from Excel',
                        'action_date' => $action_date,
                        'principal_amount'=>$amount_disb
                    ];

                $interest_data[2]=[
                    'reference_no' => $loan_data[0],
                    'reference_id' => $inserted_loan_id,
                    'transaction_date' => $repayment_date,
                    $this->debit_or_credit3=> $interest_amount,
                    'narrative'=> strtoupper("Interest on Loan Disbursed on ".$action_date),
                    'account_id'=> 6,
                    'status_id'=> 1
                ];

                $interest_data[3] =  [
                    'reference_no' => $loan_data[0],
                    'reference_id' => $inserted_loan_id,//Loan id will be equal to scheduled id since it's one installment
                    'transaction_date' => $repayment_date,
                    $this->debit_or_credit4=> $interest_amount,
                    'narrative'=> strtoupper("Interest on Loan Disbursed on ".$action_date),
                    'account_id'=> 7,
                    'status_id'=> 1
                ];
                $this->do_journal($sent_data,$interest_data);

            // if ($loan_data[17]>0) {
                
            //         $sent_data2['loan_fee']=[
            //             'journal_type_id'=> 28,
            //             'ref_no' =>  $this->client_loan_no,
            //             'ref_id' => $inserted_loan_id,
            //             'description' => 'Loan fees imported from Excel',
            //             'feename' => 'Loan Application Fee',
            //             'action_date' => $action_date,
            //             'charge_amount' => $loan_data[17],
            //             'income_account_id' =>40,
            //             'fee_income_account_id' =>2
            //         ];

            //         $this->do_journal_fees($sent_data2['loan_fee'],false);
            // }

            // installment payment 

             // $payment_data = [
             //        "client_loan_id" => $inserted_loan_id,
             //        "paid_principal" => $principal_amount,
             //        "paid_interest" => $interest_amount,
             //        "repayment_schedule_id" => $repayment_schedule_id,
             //        "paid_penalty" => 0,
             //        "payment_date" => $repayment_date,
             //        "transaction_channel_id" => 1,
             //        "comment" => 'Loan Payment imported',
             //        "status_id" => 1,
             //        'date_created' => $date_created,
             //        "created_by" => 1
             //    ];
             //    $inserted_id=$this->loan_installment_payment_model->set3($payment_data);

             //    $data = array(
             //                'payment_status' => 1,//All payments will be partial
             //                'actual_payment_date' => $repayment_date,
             //                'modified_by' => 1
             //                );
             //    $this->repayment_schedule_model->update2($data,'repayment_schedule.id ='.$repayment_schedule_id);//loan_id is the same and the repayment_schedule_id

             //    $sent_data3=[
             //            'journal_type_id'=> 6,
             //            'ref_no' => null,
             //            'ref_id' => $inserted_id,
             //            'description' => 'Loan Payment imported from Excel',
             //            'action_date' => $repayment_date,
             //            'principal_amount' => $principal_amount,
             //            'interest_amount' => $interest_amount,
             //            'linked_account_id' =>40,
             //            'loan_receivable_account_id' =>1,
             //            'interest_income_account_id' =>6,
             //            'interest_receivable_account_id' =>7
             //        ];
             //        $this->do_journal2($sent_data3);
            
            }
            return 1;
        }
        return 0;
    }
    
    public function extract_date_time_dot($date_time_string, $return_format = "U") {
        $date_format = "d.m.Y" . (strlen($date_time_string) > 10 ? " H:i:s" : "");
        $date_time_obj = DateTime::createFromFormat($date_format, $date_time_string);
        return $date_time_obj->format($return_format);
    }
    
     private function do_journal_fees($sent_data,$interest_data){
        
        $single_row = [
            "journal_type_id" => $sent_data['journal_type_id'],
            "ref_id" => $sent_data['ref_id'],
            "ref_no" => $sent_data['ref_no'],
            "description" => $sent_data['description'],
            "transaction_date" =>  $sent_data['action_date'],
            "status_id" => 1,
            "date_created" => time(),
            "created_by" => 1,
        ];
        $insert_id=$this->Data_import_model->add_journal_tr($single_row);

        if(!empty($insert_id)){

           $debit_or_credit1 = $this->accounts_model->get_normal_side($sent_data['income_account_id']);
        $debit_or_credit2 = $this->accounts_model->get_normal_side($sent_data['fee_income_account_id']);

        $data[0] = [
            'reference_no' => $sent_data['ref_no'],
            'reference_id' => $sent_data['ref_id'],
            'transaction_date' => $sent_data['action_date'],
            $debit_or_credit1 => $sent_data['charge_amount'],
            'narrative' => ucfirst($sent_data['feename']) . " charge on loan at " . $sent_data['action_date'],
            'account_id' => $sent_data['income_account_id'],
            'status_id' => 1
        ];
        $data[1] = [
            'reference_no' => $sent_data['ref_no'],
            'reference_id' => $sent_data['ref_id'],
            'transaction_date' => $sent_data['action_date'],
            $debit_or_credit2 => $sent_data['charge_amount'],
            'narrative' => ucfirst($sent_data['feename']) . " charge on loan at " . $sent_data['action_date'],
            'account_id' => $sent_data['fee_income_account_id'],
            'status_id' => 1
        ];

     return $this->Data_import_model->add_journal_tr_line($insert_id, $data);


        }

    }


    private function do_journal($sent_data,$interest_data){
        
        $single_row = [
            "journal_type_id" => $sent_data['journal_type_id'],
            "ref_id" => $sent_data['ref_id'],
            "ref_no" => $sent_data['ref_no'],
            "description" => $sent_data['description'],
            "transaction_date" =>  $sent_data['action_date'],
            "status_id" => 1,
            "date_created" => time(),
            "created_by" => 1,
        ];
        $insert_id=$this->Data_import_model->add_journal_tr($single_row);

        if(!empty($insert_id)){

            $data[0] = [
                'reference_no' => $sent_data['ref_no'],
                'reference_id' => $sent_data['ref_id'],
                'transaction_date' => $sent_data['action_date'],
                $this->debit_or_credit2=> $sent_data['principal_amount'],
                'narrative'=> strtoupper("Loan Disbursement on ".$sent_data['action_date']),
                'account_id'=> 40,//source_fund_account_id
                'status_id'=> 1
            ];
            $data[1] = [
                'reference_no' => $sent_data['ref_no'],
                'reference_id' => $sent_data['ref_id'],
                'transaction_date' => $sent_data['action_date'],
                $this->debit_or_credit1=> $sent_data['principal_amount'],
                'narrative'=> strtoupper("Loan Disbursement on ".$sent_data['action_date']),
                'account_id'=> 1,//loan_receivable_account_id
                'status_id'=> 1
            ];

            $data=array_merge($data,$interest_data);
            return $this->Data_import_model->add_journal_tr_line($insert_id, $data);

        }

    }


     private function do_journal2($sent_data){
         
          $this->debit_or_credit5 =  $this->accounts_model->get_normal_side(1,true);//loan_receivable_account_id
        $this->debit_or_credit6= $this->accounts_model->get_normal_side(40);//linked_account_id
        $this->debit_or_credit7 = $this->accounts_model->get_normal_side(7,true);//interest_receivable_account_id

        $single_row = [
            "journal_type_id" => $sent_data['journal_type_id'],
            "ref_id" => $sent_data['ref_id'],
            "ref_no" => $sent_data['ref_no'],
            "description" => $sent_data['description'],
            "transaction_date" =>  $sent_data['action_date'],
            "status_id" => 1,
            "date_created" => time(),
            "created_by" => 1,
        ];
        $insert_id=$this->Data_import_model->add_journal_tr($single_row);
        if(!empty($insert_id)){

            if ($sent_data['principal_amount'] !=null && !empty($sent_data['principal_amount']) && $sent_data['principal_amount'] !='0') {
                  $data[0] =[
                        'reference_no' => $sent_data['ref_no'],
                        'reference_id' => $sent_data['ref_id'],
                        'transaction_date' => $sent_data['action_date'],
                        $this->debit_or_credit5 => $sent_data['principal_amount'],
                        'narrative'=> strtoupper("Loan principal payment on ".$sent_data['action_date']),
                        'account_id'=>$sent_data['loan_receivable_account_id'],
                        'status_id'=> 1
                    ];
                    $data[1] =[
                        'reference_no' => $sent_data['ref_no'],
                        'reference_id' => $sent_data['ref_id'],
                        'transaction_date' => $sent_data['action_date'],
                        $this->debit_or_credit6=> $sent_data['principal_amount'],
                        'narrative'=> strtoupper("Loan principal payment on ".$sent_data['action_date']),
                        'account_id'=> $sent_data['linked_account_id'],
                        'status_id'=> 1
                    ];
                }

            //if interest has been received

            if ($sent_data['interest_amount'] !=null && !empty($sent_data['interest_amount']) && $sent_data['interest_amount'] !='0') {
              $data[2] =[
                    'reference_no' => $sent_data['ref_no'],
                    'reference_id' => $sent_data['ref_id'],
                    'transaction_date' => $sent_data['action_date'],
                    $this->debit_or_credit7 => $sent_data['interest_amount'],
                    'narrative'=> strtoupper("Loan interest payment on ".$sent_data['action_date']),
                    'account_id'=> $sent_data['interest_receivable_account_id'],
                    'status_id'=> 1
                ];
                $data[3] =[
                    'reference_no' => $sent_data['ref_no'],
                    'reference_id' => $sent_data['ref_id'],
                    'transaction_date' => $sent_data['action_date'],
                    $this->debit_or_credit6 => $sent_data['interest_amount'],
                    'narrative'=> strtoupper("Loan interest payment on ".$sent_data['action_date']),
                    'account_id'=> $sent_data['linked_account_id'],
                    'status_id'=> 1
                ];
            }
            return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
        }
    return false;
    }

}

