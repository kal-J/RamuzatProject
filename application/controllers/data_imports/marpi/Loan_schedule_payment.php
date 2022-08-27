<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Eric
 */
class Loan_schedule_payment extends CI_Controller {

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
        $this->load->model('loan_installment_payment_model');
        $this->load->model('repayment_schedule_model');
        $this->load->model("accounts_model");
        $this->load->model("Data_import_model");
        $this->schedules =[];
        $this->schedule_payment_data =[];

    }

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."marpi".DIRECTORY_SEPARATOR;
        $file_name = "loan_payments.csv";
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
                    if ($field_names[0] != "ClientName") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key ClientName";
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

    private function get_payment_data($loan_data,$schedule_no){
        $repayment_data =$this->schedules[$loan_data[4]];
        $repayment_schedule_id = $repayment_data[$schedule_no]['id'];
        if (array_key_exists($loan_data[4], $this->schedule_payment_data)) {//Then there is balance bf
            if($this->schedule_payment_data[$loan_data[4]]['balance_bf']){
                $payment_amount=$this->schedule_payment_data[$loan_data[4]]['balance_bf'];
            }else{
                $payment_amount=$loan_data[3];
            }
        }else{ 
            $payment_amount=$loan_data[3];
        }
        if (($payment_amount >= $repayment_data[$schedule_no]['total_amount'])) {
            $principal_balance = $interest_balance =0;
            $payment_status=1;
            $current_schedule_id = $repayment_data[$schedule_no]['id'];
            if (array_key_exists(($schedule_no+1), $repayment_data)) {
                $principal_amount=$repayment_data[$schedule_no]['principal_amount'];
                $interest_amount=$repayment_data[$schedule_no]['interest_amount'];
                $balance_bf = $payment_amount - $repayment_data[$schedule_no]['total_amount'];
                $schedule_no +=1;
            }else{
                $principal_amount=$repayment_data[$schedule_no]['principal_amount'];
                $interest_amount= $payment_amount - $repayment_data[$schedule_no]['principal_amount'];
                $balance_bf =0;
            }
        }elseif (($payment_amount < $repayment_data[$schedule_no]['total_amount'])) {//schedule number doesn't change
            $current_schedule_id = $repayment_data[$schedule_no]['id'];
            $balance_bf = 0;
            if ($payment_amount < $repayment_data[$schedule_no]['principal_amount']) {
                $principal_balance = $repayment_data[$schedule_no]['principal_amount'] - $payment_amount;
                $interest_balance = $repayment_data[$schedule_no]['interest_amount'];

                $principal_amount = $payment_amount;
                $interest_amount = 0;

            }else{
                $principal_amount=$repayment_data[$schedule_no]['principal_amount'];
                $interest_amount=$payment_amount-$repayment_data[$schedule_no]['principal_amount'];

                $principal_balance = 0;
                $interest_balance = $repayment_data[$schedule_no]['interest_amount'] - $interest_amount;
            }
            $payment_status=2;

        }
        $this->schedule_payment_data[$loan_data[4]]=[
            "current_schedule_id" => $current_schedule_id,
            "principal_balance" => $principal_balance,//if less
            "interest_balance" => $interest_balance,//if less
            "balance_bf" => $balance_bf,
            "schedule_no" => $schedule_no,
        ];
        return $data=[
            'paid_principal'=> $principal_amount,
            'paid_interest'=> $interest_amount,
            'repayment_schedule_id'=> $repayment_schedule_id,
            'client_loan_id' => $loan_data[4],
            'payment_status' => $payment_status
        ];
    }

    private function get_payment_data_1($loan_data,$schedule_no){
        
        $repayment_data =$this->schedules[$loan_data[4]];
        $repayment_schedule_id = $repayment_data[$schedule_no]['id'];
        $payment_amount=$loan_data[3];

        try {
                
            if ($payment_amount < ($this->schedule_payment_data[$loan_data[4]]['interest_balance'] + $this->schedule_payment_data[$loan_data[4]]['principal_balance'])) {
                $available_amount=$payment_amount;
                if ($payment_amount > $this->schedule_payment_data[$loan_data[4]]['principal_balance']) {
                    $principal_amount = $this->schedule_payment_data[$loan_data[4]]['principal_balance'];
                }else{
                    $principal_amount = $available_amount;
                }
                $current_schedule_id=$this->schedule_payment_data[$loan_data[4]]['current_schedule_id'];
                $interest_amount = $available_amount-$principal_amount;
                $balance_bf =0;

                $principal_balance= $this->schedule_payment_data[$loan_data[4]]['principal_balance']-$principal_amount;
                $interest_balance= $this->schedule_payment_data[$loan_data[4]]['interest_balance']-$interest_amount;
                $payment_status=2;
            }elseif ($payment_amount >= ($this->schedule_payment_data[$loan_data[4]]['interest_balance'] + $this->schedule_payment_data[$loan_data[4]]['principal_balance'])){//principal 0 interest 0
                $current_schedule_id=$this->schedule_payment_data[$loan_data[4]]['current_schedule_id']+1;
                if (array_key_exists(($schedule_no+1), $repayment_data)) {
                    $principal_amount = $this->schedule_payment_data[$loan_data[4]]['principal_balance'];
                    $interest_amount = $this->schedule_payment_data[$loan_data[4]]['interest_balance'];
                    $balance_bf =$payment_amount-( $principal_amount+$interest_amount);
                    $schedule_no +=1;
                }else{
                    $principal_amount = $this->schedule_payment_data[$loan_data[4]]['principal_balance'];
                    $interest_amount= $payment_amount - $principal_amount;
                    $balance_bf =0;
                }

                $principal_balance= 0;
                $interest_balance= 0;
                $payment_status=1;
            }

            $this->schedule_payment_data[$loan_data[4]]=[
                "current_schedule_id" => $current_schedule_id,
                "principal_balance" => $principal_balance,//if less
                "interest_balance" => $interest_balance,//if less
                "balance_bf" => $balance_bf,
                "schedule_no" => $schedule_no,
            ];

            return $data=[
                'paid_principal'=> $principal_amount,
                'paid_interest'=> $interest_amount,
                'repayment_schedule_id'=> $repayment_schedule_id,
                'client_loan_id' => $loan_data[4],
                'payment_status' => $payment_status

            ];
        } catch (Exception $e) {
                return false;
        }

       

    }
    private function insert_loan_data($loan_data) {
        $topUp = ["N" => 0, "Y" => 1];
        $payment_status = ["FULL" => 1, "PARTIAL" => 2, "PAID OFF" => 3, "PAID UP" => 3];

        $date_created = $this->helpers->extract_date_time($loan_data[2]);
        $payment_date = $this->helpers->extract_date_time($loan_data[2],"Y-m-d");


        if (!array_key_exists($loan_data[4], $this->schedules)) {
            $this->schedules[$loan_data[4]] = $this->repayment_schedule_model->get($loan_data[4]);
        }

        if (array_key_exists($loan_data[4], $this->schedule_payment_data)) {
            $schedule_no = $this->schedule_payment_data[$loan_data[4]]['schedule_no'];
            
            if ($this->schedule_payment_data[$loan_data[4]]['principal_balance']==0 && $this->schedule_payment_data[$loan_data[4]]['interest_balance']==0 && $this->schedule_payment_data[$loan_data[4]]['balance_bf']==0) {//No balance demanded or bf.
                $payment_data =$this->get_payment_data($loan_data,$schedule_no);

            }elseif (($this->schedule_payment_data[$loan_data[4]]['principal_balance'] !=0 || $this->schedule_payment_data[$loan_data[4]]['interest_balance'] !=0) && $this->schedule_payment_data[$loan_data[4]]['balance_bf']==0) {//demanding
                $payment_data =$this->get_payment_data_1($loan_data,$schedule_no);
            }

        }else{
            $schedule_no =0;
            $payment_data =$this->get_payment_data($loan_data,$schedule_no);
        }
        $this->record_payment($payment_data,$payment_date,$date_created);
        while($this->schedule_payment_data[$loan_data[4]]['balance_bf']){//balance brought fd
            $schedule_no=$this->schedule_payment_data[$loan_data[4]]['schedule_no'];
            $payment_data =$this->get_payment_data($loan_data,$schedule_no);
            $this->record_payment($payment_data,$payment_date,$date_created);
        }
        
        // print_r($payment_data);die;
        return 1;
    }

    private function record_payment($data,$payment_date,$date_created){
        try {
            $payment_data = [
                    "client_loan_id" =>  $data['client_loan_id'],
                    "repayment_schedule_id" => $data['repayment_schedule_id'],
                    "paid_interest" => $data['paid_interest'],
                    "paid_principal" => $data['paid_principal'],
                    "paid_penalty" => 0,
                    "payment_date" => $payment_date,
                    "transaction_channel_id" => 1,
                    "comment" => 'Loan Payment imported',
                    "status_id" => 1,
                    'date_created' => $date_created,
                    "created_by" => 1
                ];
            
            $inserted_id=$this->loan_installment_payment_model->set3($payment_data);

            $update_data = array(
                        'payment_status' => $data['payment_status'],
                        'actual_payment_date' => $payment_date,
                        'modified_by' => 1
                        );
            $this->repayment_schedule_model->update2($update_data,'repayment_schedule.id ='.$data['repayment_schedule_id']);

            $sent_data=[
                    'journal_type_id'=> 6,
                    'ref_no' => null,
                    'ref_id' => $inserted_id,
                    'description' => 'Loan Payment imported from Excel',
                    'action_date' => $payment_date,
                    'principal_amount' =>$data["paid_principal"],
                    'interest_amount' => $data["paid_interest"],
                    'linked_account_id' =>40,
                    'loan_receivable_account_id' =>1,
                    'interest_income_account_id' =>6,
                    'interest_receivable_account_id' =>7
                ];
            $this->do_journal($sent_data);
            return 1;
        } catch (Exception $e) {
            return false;
        }
    }

    private function do_journal($sent_data){
    
        if ($sent_data['principal_amount'] !=null && !empty($sent_data['principal_amount']) && $sent_data['principal_amount'] !='0' && $sent_data['interest_amount'] !=null && !empty($sent_data['interest_amount']) && $sent_data['interest_amount'] !='0') {
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

                $debit_or_credit1 =  $this->accounts_model->get_normal_side($sent_data['loan_receivable_account_id'],true);
                $debit_or_credit3= $this->accounts_model->get_normal_side($sent_data['linked_account_id']);
                $debit_or_credit4 = $this->accounts_model->get_normal_side($sent_data['interest_receivable_account_id'],true);
                // $debit_or_credit5 = $this->accounts_model->get_normal_side($sent_data['interest_income_account_id'],true);

                if ($sent_data['principal_amount'] !=null && !empty($sent_data['principal_amount']) && $sent_data['principal_amount'] !='0') {
                      $data[0] =[
                            'reference_no' => $sent_data['ref_no'],
                            'reference_id' => $sent_data['ref_id'],
                            'transaction_date' => $sent_data['action_date'],
                            $debit_or_credit1=> $sent_data['principal_amount'],
                            'narrative'=> strtoupper("Loan principal payment on ".$sent_data['action_date']),
                            'account_id'=>$sent_data['loan_receivable_account_id'],
                            'status_id'=> 1
                        ];
                        $data[1] =[
                            'reference_no' => $sent_data['ref_no'],
                            'reference_id' => $sent_data['ref_id'],
                            'transaction_date' => $sent_data['action_date'],
                            $debit_or_credit3=> $sent_data['principal_amount'],
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
                        $debit_or_credit4=> $sent_data['interest_amount'],
                        'narrative'=> strtoupper("Loan interest payment on ".$sent_data['action_date']),
                        'account_id'=> $sent_data['interest_receivable_account_id'],
                        'status_id'=> 1
                    ];
                    $data[3] =[
                        'reference_no' => $sent_data['ref_no'],
                        'reference_id' => $sent_data['ref_id'],
                        'transaction_date' => $sent_data['action_date'],
                        $debit_or_credit3=> $sent_data['interest_amount'],
                        'narrative'=> strtoupper("Loan interest payment on ".$sent_data['action_date']),
                        'account_id'=> $sent_data['linked_account_id'],
                        'status_id'=> 1
                    ];
                }
                return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
            }
        }
        return false;
    }

}