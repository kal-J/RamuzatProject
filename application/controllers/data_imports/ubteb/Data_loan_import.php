<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Eric
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
        $this->load->model('loan_product_model');
        $this->load->model('savings_account_model');
        $this->load->model('client_loan_monthly_income_model');
        $this->load->model('client_loan_monthly_expense_model');
        $this->load->model('loan_attached_saving_accounts_model');
        $this->load->model('payment_details_model');
        $this->load->model('repayment_schedule_model');
        $this->load->model('loan_state_model');
        $this->load->model("applied_loan_fee_model");
        $this->load->model("accounts_model");
        $this->load->model("Data_import_model");
        $this->schedule_id=0;

    }

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."ubteb".DIRECTORY_SEPARATOR;
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
                    if ($field_names[0] != "LOAN_ACCOUNT") {
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
        $preferred_payment = ["CASH" => 1, "BANK" => 2, "MOBILE MONEY" => 3];
        $periods = ["DAYS" => 1, "WEEKS" => 2, "MONTHS" => 3];
        $topUp = ["N" => 0, "Y" => 1];
        $source_fund_id = [27, 40];

        // $date_created = time();
        $date_created = $this->helpers->extract_date_time($loan_data[6]);
        $action_date = $this->helpers->extract_date_time($loan_data[6],"Y-m-d");

        $client_loan_no = "LN00".$loan_data[0];
        $amount=($loan_data[21]=='N')?$loan_data[2]:$loan_data[25];
        $member_id = $loan_data[1];
        $top_up=($loan_data[21])?$loan_data[21]:'N';
        $loan_product_id =($loan_data[23])?$loan_data[23]:1;
        $repayment_frequency = ($loan_data[9])?$loan_data[9]:$loan_data[7];
        $repayment_made_every = ($loan_data[10])?$loan_data[10]:3;
        $installments=($loan_data[8] !='' && $loan_data[8]!= NULL)?$loan_data[8]:1;

        try {
            if ($loan_data[0] != "" && $loan_data[0] != 'NULL') {
                $single_row = [
                    "member_id" =>  $member_id+1,
                    "loan_no" => $client_loan_no,
                    "credit_officer_id" => 1, //To be changed after insertion
                    "loan_product_id" => $loan_product_id,
                    "topup_application" => $topUp[$top_up],
                    "linked_loan_id" => $loan_data[22],
                    "requested_amount" => $amount,
                    "application_date" => $action_date,
                    "source_fund_account_id" => 40, //$source_fund_id[$loan_product_id-1],
                    "disbursement_date" => $action_date,
                    "suggested_disbursement_date" => $action_date,
                    "interest_rate" => $loan_data[4],
                    "offset_period" => 0,
                    "offset_made_every" => 1,
                    "repayment_frequency" => $repayment_frequency,
                    "repayment_made_every" => $repayment_made_every,
                    "installments" => $installments,
                    "link_to_deposit_account" => 1,
                    "comment" => 'Loan imported from the old system/ Excel',
                    "amount_approved" => $amount,
                    "approval_date" => $action_date,
                    "approved_installments" => $loan_data[8],
                    "approved_repayment_frequency" => $repayment_frequency,
                    "approved_repayment_made_every" => $repayment_made_every,
                    "approved_by" => 1,
                    "approval_note" => 'Data imported from excel',
                    "loan_purpose" => 'N/L',
                    "preferred_payment_id" => $preferred_payment[(isset($loan_data[11]) && $loan_data[11] !='' && $loan_data[11] != NULL) ? $loan_data[11] : 'CASH'],
                    'date_created' => time(),
                    "created_by" => 1
                ];
                //insert into the client table
                $inserted_loan_id=$this->client_loan_model->set2($single_row);

                if ($inserted_loan_id) {//add the client incomes
                    if ($loan_data[16]>0) {
                        $client_income_data = [
                            "client_loan_id" => $inserted_loan_id,
                            "income_id" => 1,
                            "amount" => $loan_data[16],
                            "status_id" => 1,
                            "description" => 'Client\'s monthly income',
                            "date_created" => $date_created,
                            "created_by" => 1
                        ];
                    $this->client_loan_monthly_income_model->set($client_income_data);
                    }

                    //add the client's expense
                    if ($loan_data[17]>0) {
                        $client_expense_data = [
                            "client_loan_id" => $inserted_loan_id,
                            "expense_id" => 1,
                            "amount" => $loan_data[17],
                            "status_id" => 1,
                            "description" => 'Client\'s monthly expense',
                            "date_created" => $date_created,
                            "created_by" => 1
                        ];
                        $this->client_loan_monthly_expense_model->set($client_expense_data);
                    }

                    //Record the state of the loan
                    $loan_state=["CLEARED"=>10, "ACTIVE"=>7,"PAID UP"=> 9,"PAID OFF"=> 14,"RESTRUCTURED"=> 14,"OTHER"=>4];
                    $loan_state_comment=["CLEARED"=>'Obligations met Loan closed - Data imported', "ACTIVE"=>'Loan Given out - Data imported',"PAID UP" => 'Loan Paid off - Data imported', "PAID OFF" => 'Loan Refinanced - Data imported',"RESTRUCTURED" => 'Loan Restructred - Data imported',"OTHER"=>'Loan state unknown so considered withrawn-Data imported'];
                    $loan_state_details = [
                        "client_loan_id" => $inserted_loan_id,
                        "state_id" => $loan_state[isset($loan_data[20])?$loan_data[20]:"OTHER"],
                        "comment" => $loan_state_comment[isset($loan_data[20])?$loan_data[20]:"OTHER"],
                        "action_date" => $action_date,
                        "date_created" => $date_created,
                        "created_by" => 1
                    ];
                    $this->loan_state_model->set2($loan_state_details);

                    //attach the payment details
                    if ($loan_data[12] != "" && $loan_data[12] != 'NULL') {
                        $client_payment_details = [
                            "client_loan_id" => $inserted_loan_id,
                            "ac_number" => $loan_data[12],
                            "ac_name" => $loan_data[13],
                            "bank_name" => $loan_data[14],
                            "bank_branch" => $loan_data[15],
                            "status_id" => 1,
                            "date_created" => $date_created,
                            "created_by" => 1
                        ];
                        $this->payment_details_model->set2($client_payment_details);
                    }

                    //Loan fee addition
                    if (!($loan_data[21]=='N' && $loan_data[22] !='')) {
                        $loan_fee_data = [
                                "client_loan_id" => $inserted_loan_id,
                                "loan_product_fee_id" => $loan_product_id,
                                "amount" => (0.01 * $loan_data[2]),
                                "paid_or_not" => 1,
                                "date_paid" => $action_date,
                                "status_id" => 1,
                                "date_created" => $date_created,
                                "created_by" => 1
                            ];
                        $this->applied_loan_fee_model->set2($loan_fee_data);
                    }
                    //Schedule genration
                    $repayment_frequency = ($loan_data[9] !='' && $loan_data[9] != NULL)?$loan_data[9]:$loan_data[7];
                    $repayment_made_every = 12;

                    $r=$interest_rate_per_annum=$interest_rate_per_installment=( $loan_data[4]/100 ); 
                    $l=$length_of_a_period=($repayment_frequency/$repayment_made_every);
                    $i=$interest_rate_per_period=($r*$l);
                    $n=$loan_data[8]; 
                    $p=$loan_data[2];
                    $EMI =($i*$p)/ (1- pow((1+$i),-$n));
                    $repayment_date=$action_date; $interest_sum=0; $principal_amount=0;

                    $index_key=2;
                    $repayment_schedule_array =[];
                    $interest_data =[];
                    $debit_or_credit3 = $this->accounts_model->get_normal_side(6);
                    $debit_or_credit4 = $this->accounts_model->get_normal_side(7);

                    for ($y=1; $y <= $loan_data[8]; $y++) { 
                        $index_key+=2;
                        $this->schedule_id = ++$this->schedule_id;
                        $interest_amount= ($i*$p);
                        $principal_amount= ($EMI-$interest_amount);
                        $repayment_schedule_array[] = [
                            "repayment_date" => $repayment_date,
                            "interest_amount" => $interest_amount,
                            "principal_amount" => $principal_amount,
                            "client_loan_id" => $inserted_loan_id,
                            "grace_period_on" => 0,
                            "grace_period_after" => 0,
                            "installment_number" => $y,
                            "interest_rate" => $loan_data[4],
                            "repayment_frequency" => 1,
                            "repayment_made_every" => 3,
                            "comment" => 'Loan schedule imported',
                            "payment_status" => 4,
                            "status_id" => 1,
                            "date_created" => $date_created,
                            "created_by" => 1
                        ];
                        $interest_data[$index_key-1]=[
                            'reference_no' => $client_loan_no,
                            'reference_id' => $this->schedule_id,
                            'transaction_date' => $repayment_date,
                            $debit_or_credit3=> $interest_amount,
                            'narrative'=> strtoupper("Interest on Loan Disbursed on ".$action_date),
                            'account_id'=> 6,
                            'status_id'=> 1
                        ];

                        $interest_data[$index_key] =  [
                            'reference_no' => $client_loan_no,
                            'reference_id' => $this->schedule_id,
                            'transaction_date' => $repayment_date,
                            $debit_or_credit4=> $interest_amount,
                            'narrative'=> strtoupper("Interest on Loan Disbursed on ".$action_date),
                            'account_id'=> 7,
                            'status_id'=> 1
                        ];
                        $repayment_date = date('Y-m-d',strtotime('+'.$repayment_frequency.' month', strtotime($repayment_date)));
                        $p = $p-$principal_amount;
                        // $interest_sum +=$interest_amount;
                        
                    }
                    $this->repayment_schedule_model->set2($repayment_schedule_array);
                    $loan_status=($loan_data[21]=='N' && $loan_data[22] !='')?'RESTRUCTURED':'NORMAL';
                    $sent_data['loan']=[
                        'journal_type_id'=> 4,
                        'ref_no' => $client_loan_no,
                        'ref_id' => $inserted_loan_id,
                        'description' => 'Loan imported from Excel',
                        'action_date' => $action_date,
                        'principal_amount'=>$amount,
                        'interest_amount' => $interest_sum,
                        'source_fund_account_id'=>40,
                        'loan_receivable_account_id'=>1,
                        'interest_income_account_id' =>6,
                        'status' =>$loan_status,
                        'interest_receivable_account_id' =>7
                    ];
                    $this->do_journal($sent_data['loan'],true,$interest_data);

                    $sent_data['loan_fee']=[
                        'journal_type_id'=> 28,
                        'ref_no' => $client_loan_no,
                        'ref_id' => $inserted_loan_id,
                        'description' => 'Loan imported from Excel',
                        'feename' => 'Loan Processing Fee',
                        'action_date' => $action_date,
                        'charge_amount' => (0.01 * $loan_data[2]),
                        'income_account_id' =>40,
                        'fee_income_account_id' =>2
                    ];
                    $this->do_journal($sent_data['loan_fee'],false);
                }
                return 1;
            }
        } catch (Exception $e) {
            return false;
        }
       
        return 0;
    }

    private function do_journal($sent_data,$loan=True,$interest_data=false){
        
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
    if(!empty($insert_id) && $loan){
        $data=[];
        if ($sent_data['status'] != "RESTRUCTURED") {
            $debit_or_credit1 = $this->accounts_model->get_normal_side($sent_data['loan_receivable_account_id']);
            $debit_or_credit2 = $this->accounts_model->get_normal_side($sent_data['source_fund_account_id'], true);
            $data[0] = [
                'reference_no' => $sent_data['ref_no'],
                'reference_id' => $sent_data['ref_id'],
                'transaction_date' => $sent_data['action_date'],
                $debit_or_credit2=> $sent_data['principal_amount'],
                'narrative'=> strtoupper("Loan Disbursement on ".$sent_data['action_date']),
                'account_id'=> $sent_data['source_fund_account_id'],
                'status_id'=> 1
            ];
            $data[1] = [
                'reference_no' => $sent_data['ref_no'],
                'reference_id' => $sent_data['ref_id'],
                'transaction_date' => $sent_data['action_date'],
                $debit_or_credit1=> $sent_data['principal_amount'],
                'narrative'=> strtoupper("Loan Disbursement on ".$sent_data['action_date']),
                'account_id'=>$sent_data['loan_receivable_account_id'],
                'status_id'=> 1
            ];
        }

        $data=array_merge($data,$interest_data);
        return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
    }else{
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

}