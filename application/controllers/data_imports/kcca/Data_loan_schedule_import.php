<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_schedule_import
 *
 * @author Eric
 */
class Data_loan_schedule_import extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        // $this->load->library("session");
        // if (empty($this->session->userdata('id'))) {
        //     redirect('/');
        // }
        ini_set('memory_limit', '200M');
        ini_set('upload_max_filesize', '200M');
        ini_set('post_max_size', '200M');
        ini_set('max_input_time', 3600);
        ini_set('max_execution_time', 3600);
        $this->load->model('client_loan_model');
        $this->load->model('repayment_schedule_model');
        $this->load->model('loan_installment_payment_model');
        $this->load->model('loan_state_model');
        $this->load->model('accounts_model');
        $this->load->model('Data_import_model');

        $this->debit_or_credit3 = $this->accounts_model->get_normal_side(4102);
        $this->debit_or_credit4 = $this->accounts_model->get_normal_side(1105);
        $this->client_loan_details=[];
        $this->loan_journals=[];
        $this->journal_line_data=[];
        $client_loan_data=$this->client_loan_model->get_loan_interest(); //To be changed
        foreach ($client_loan_data as $key => $value) {
           $this->client_loan_details[$value['id']]=[
                'interest_rate' => $value['interest_rate'],
                'client_loan_no' => $value['loan_no'],
                'disbursement_date' => $value['disbursement_date']
           ];
        }
        unset($client_loan_data);
    }

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."kcca".DIRECTORY_SEPARATOR;
        $file_name = "loan_schedules.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates($file_path);
        echo json_encode($feedback);
    }

    private function run_updates($file_path) {
        $handle = fopen($file_path, "r");
        $total_loan_schedule = $count = 0;
        $schedule_id=1;
        $repayment_schedule_array = $installment_payment_array =[];
        $ending_balances = []; $installment_details = []; $loans = [];
        $feedback = ["success" => false, "message" => "File Could not be opened"];
        if ($handle) {
            $data = fgetcsv($handle, 10240, ",");
            if ($data[1] != "LOAN_ID") {
                $feedback['message'] = "Please ensure that the first cell (B1) contains the key LOAN_ID";
                fclose($handle);
                return $feedback;
            }
            $ending_balances['previous']=0;
            ini_set('memory_limit', '200M');
            ini_set('upload_max_filesize', '200M');
            ini_set('post_max_size', '200M');
            ini_set('max_input_time', 3600);
            ini_set('max_execution_time', 3600);
            while (($data = fgetcsv($handle, 10240, ",")) !== FALSE) {
                $ending_balances['current']=$data[8];
                if ($data[9] !='R') {//Installemnt renumbering
                    if (array_key_exists($data[1], $installment_details)) {
                        $installment_details[$data[1]]['number']=($installment_details[$data[1]]['number']+1);
                        if ($data[2] == 1) {
                           $installment_details[$data[1]]['date']=$data[3];
                           if ($installment_details[$data[1]]['number'] !=1) {//checking if loan was rescheduled
                               $installment_details[$data[1]]['number1']=1;
                           }
                        }else{
                            if (isset($installment_details[$data[1]]['number1']) && $installment_details[$data[1]]['number1'] >=1 ) {//checking if installment is after rescheduling
                                $installment_details[$data[1]]['number1']=($installment_details[$data[1]]['number1']+1);
                            }
                        }
                    }else{
                        $installment_details[$data[1]]['number']=1;
                        $installment_details[$data[1]]['date']=$data[3];
                    }
                }
                if (!array_key_exists($data[1], $installment_details)){//Checking if the first installment was rescheduled
                   $installment_details[$data[1]]['number']=1;
                   $installment_details[$data[1]]['date']=$data[3];
                }
                $returned_array = $this->insert_loan_schedule_data($data,$schedule_id,$ending_balances,$installment_details[$data[1]]);

                if (!array_key_exists($data[1], $loans)){//Updating the loan state
                    if ($data[9] =='C') {                    
                        $loans[$data[1]]=1;
                        $loan_state_details = [
                            "state_id" => 14,
                            "comment" => 'Loan Refinanced - Data imported',
                        ];
                        // $this->loan_state_model->update2($loan_state_details,$data[1]);
                    }elseif ($data[9] =='Y' && $data[7] =='0') {
                        $loans[$data[1]]=1;
                        $loan_state_details = [
                            "state_id" => 9,
                            "comment" => 'Loan Paid off - Data imported',
                        ];
                        // $this->loan_state_model->update2($loan_state_details,$data[1]);
                    }

                }

                if (isset($returned_array["schedule"])) {//Checking if the loan installment was paid
                    $schedule_id++;
                    $repayment_schedule_array[] = $returned_array["schedule"];
                    if (isset($returned_array["payment"])) {
                        $installment_payment_array[] = $returned_array["payment"];
                    }
                }
                unset($data);$data=null;
                if ($schedule_id%1000 === 0) {
                	// $this->repayment_schedule_model->set2($repayment_schedule_array);
                    // $this->loan_installment_payment_model->set2($installment_payment_array);
                    unset($repayment_schedule_array,$installment_payment_array);
                    $repayment_schedule_array=$installment_payment_array=NULL;
                }
                $ending_balances['previous']=$ending_balances['current'];
            }
            fclose($handle);
            //then the remainder of the data
            // if(count($repayment_schedule_array)){
            //     $this->repayment_schedule_model->set2($repayment_schedule_array);
            //     $this->loan_installment_payment_model->set2($installment_payment_array);
            //     unset($repayment_schedule_array,$installment_payment_array);
            //     $repayment_schedule_array=$installment_payment_array=NULL;
            // }
            unset($client_loan_details);
            unset($repayment_schedule_array,$installment_payment_array);

            //post the journal lines
            ini_set('memory_limit', '200M');
            ini_set('upload_max_filesize', '200M');
            ini_set('post_max_size', '200M');
            ini_set('max_input_time', 3600);
            ini_set('max_execution_time', 3600);
            foreach ($this->loan_journals as $key => $loan_journal) {
                $this->do_journal($loan_journal);
            }
            

            if (is_numeric($schedule_id)) {
                $feedback["success"] = true;
                $feedback["message"] = "Update done\n $schedule_id records updated";
            }
        }
        return $feedback;
    }

    private function insert_loan_schedule_data($loan_data,$schedule_id,$ending_balances,$installment_data) {
        if ($loan_data[0] != "" && $loan_data[0] != 'NULL') { 
                $data_array=[];

                $payment_status = ["Y" => 1, "C" => 5, "N" => 4, "R" => 4, "E" => 6];
                $schedule_status = ["Y" => 1, "C" => 1, "N" => 1, "R" => 2, "E" => 1];
                $date_created = $this->helpers->extract_date_time($loan_data[12]);
                if (isset($loan_data[10]) && $loan_data[10] !='' && $loan_data[10] !='NULL') {
                    $actual_payment_date = $this->helpers->extract_date_time($loan_data[10],"Y-m-d");
                }else{
                   $actual_payment_date =''; 
                }

                if ($loan_data[9]=='R' || $loan_data[2]==1) {
                    $repayment_date = $this->helpers->extract_date_time($loan_data[3],"Y-m-d");
                }else{
                    $number=(isset($installment_data['number1']))?$installment_data['number1']-1:$installment_data['number']-1;
                    $repayment_date = date('Y-m-d',strtotime('+'.$number.' month', strtotime($this->helpers->extract_date_time($installment_data['date'],"Y-m-d"))));
                }
                
                if ($loan_data[6] !='0' && $loan_data[7] =='0') {
                    $principal_amount=$loan_data[6];
                    $interest_amount=$loan_data[5]-$loan_data[6];
                }elseif ($loan_data[6] =='0' && $loan_data[7] =='0') {
                    $principal_amount=$ending_balances['previous']-$ending_balances['current'];
                    $interest_amount=$loan_data[5]-$principal_amount;
                }else{
                    $principal_amount=$loan_data[6];
                    $interest_amount=$loan_data[7];
                }
                if ($loan_data[9] =='C') {
                    $payment_status_id=5;
                    $journal_status_id=3;
                }elseif ($loan_data[9] =='Y' && $loan_data[7] =='0') {
                    $payment_status_id=3;
                    $journal_status_id=3;
                }elseif ($loan_data[9] =='R') {
                    $payment_status_id=$payment_status[isset($loan_data[9]) ? $loan_data[9] : "E"];
                    $journal_status_id=3;
                }else{
                    $payment_status_id=$payment_status[isset($loan_data[9]) ? $loan_data[9] : "E"];
                    $journal_status_id=1;
                }

                $data_array['schedule'] = [
                    "repayment_date" => $repayment_date,
                    "interest_amount" => $interest_amount,
                    "principal_amount" => $principal_amount,
                    "client_loan_id" => trim($loan_data[1]),
                    "grace_period_on" => 0,
                    "grace_period_after" => 0,
                    "installment_number" => ($loan_data[9]=='R')?$loan_data[2]:$installment_data['number'],
                    "interest_rate" => $this->client_loan_details[trim($loan_data[1])]['interest_rate'],
                    "repayment_frequency" => 1,
                    "repayment_made_every" => 3,
                    "comment" => 'Loan schedule imported from an old system, payment status for a paid off,closed due to Top up and reamortized scheduled will all be give a paid off status, Old system didn\'t differentiate the three',
                    "actual_payment_date" => ($loan_data[9] =='Y')?$actual_payment_date:'',
                    "payment_status" => $payment_status_id,
                    "status_id" => $schedule_status[isset($loan_data[9]) ? $loan_data[9] : "E"],
                    "date_created" => $date_created,
                    "created_by" => $loan_data[11],
                    "modified_by" => $loan_data[11]
                ];
                //insert into the repayment schedule table
                
                //add the installment_payment table
                if (isset($loan_data[10]) && $loan_data[10] !='' && $loan_data[10] !='NULL' && $loan_data[9] =='Y') {

                    if (($loan_data[7] !='0' && $loan_data[6] !='0') || $loan_data[7] !='0' || $loan_data[6] !='0') {
                        
                        $data_array['payment'] = [
                            "client_loan_id" => trim($loan_data[1]),
                            "repayment_schedule_id" => $schedule_id,
                            "paid_interest" => $loan_data[7],
                            "paid_principal" => $loan_data[6],
                            "comment" => 'Loan payment imported from an old system',
                            "payment_date" => $actual_payment_date,
                            "status_id" => 1,
                            "date_created" => $date_created,
                            "created_by" => $loan_data[11],
                            "modified_by" => $loan_data[11]
                        ];
                    }
                }

                $this->journal_line_data[$loan_data[1]][]=[
                    'reference_no' => $this->client_loan_details[trim($loan_data[1])]['client_loan_no'],
                    'reference_id' => $schedule_id,
                    'transaction_date' => $repayment_date,
                    $this->debit_or_credit3=> $interest_amount,
                    'narrative'=> strtoupper("Interest on Loan Disbursed on ".$this->client_loan_details[trim($loan_data[1])]['disbursement_date']),
                    'account_id'=> 4102,
                    'status_id'=> $journal_status_id
                ];

                $this->journal_line_data[$loan_data[1]][] =  [
                    'reference_no' => $this->client_loan_details[trim($loan_data[1])]['client_loan_no'],
                    'reference_id' => $schedule_id,
                    'transaction_date' => $repayment_date,
                    $this->debit_or_credit4=> $interest_amount,
                    'narrative'=> strtoupper("Interest on Loan Disbursed on ".$this->client_loan_details[trim($loan_data[1])]['disbursement_date']),
                    'account_id'=> 1105,
                    'status_id'=> $journal_status_id
                ];
                $this->loan_journals[$loan_data[1]]=[
                    'journal_type_id'=> 4,
                    'ref_no' => $this->client_loan_details[trim($loan_data[1])]['client_loan_no'],
                    'ref_id' => $loan_data[1],
                    'description' => 'Loan imported from Excel',
                    'action_date' => $this->client_loan_details[trim($loan_data[1])]['disbursement_date']
                ];
                

            return $data_array;
        }
        return [];
    }

    private function do_journal($sent_data){ 
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
            return $this->Data_import_model->add_journal_tr_line($insert_id,$this->journal_line_data[$sent_data['ref_id']]);
        }

    }
}
