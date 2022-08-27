<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Reagan
 */
class Loan_payments_import extends CI_Controller {

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
        $this->client_loan_no='LN00000';

        $this->debit_or_credit1 = $this->accounts_model->get_normal_side(1);//loan_receivable_account_id
        $this->debit_or_credit2 = $this->accounts_model->get_normal_side(40, true);//source_fund_account_id
        $this->debit_or_credit3 = $this->accounts_model->get_normal_side(6);//interest_income_account_id
        $this->debit_or_credit4 = $this->accounts_model->get_normal_side(7);//interest_receivable_account_id
    }

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."mkcc".DIRECTORY_SEPARATOR;
        $file_name = "LOAN_PAYMENTS.csv";
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
                    if ($field_names[0] != "ID") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key LOAN_ID";
                        fclose($handle);
                        return $feedback;
                    }
                } else {
                    if($total_loans<=640){
                    $total_loans = $total_loans + $this->insert_loan_data($data1);
                    }
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
        $loan_detail = $this->client_loan_model->get_client_data($loan_data[0]);
        $date_created = time();
        //print($loan_detail['loan_no']);die;
        $repayment_date = $this->helpers->extract_date_time($loan_data[6],"Y-m-d");
      
            // installment payment 
            if ($loan_data[0] != "" && $loan_data[0] != 'NULL') {
             $payment_data = [
                    "client_loan_id" => $loan_data[0],
                    "paid_principal" => $loan_data[2],
                    "paid_interest" => $loan_data[3],
                    "repayment_schedule_id" =>$loan_data[0],
                    "paid_penalty" => $loan_data[4],
                    "payment_date" => $repayment_date,
                    "transaction_channel_id" => 1,
                    "comment" => 'Loan Payment imported',
                    "status_id" => 1,
                    'date_created' => $date_created,
                    "created_by" => 1
                ];
                $inserted_id=$this->loan_installment_payment_model->set3($payment_data);

                $data = array(
                            'payment_status' => $loan_data[7],//All payments will be partial
                            'actual_payment_date' => $repayment_date,
                            'modified_by' => 1
                            );
                $this->repayment_schedule_model->update2($data,'repayment_schedule.id ='.$loan_data[0]);//loan_id is the same and the repayment_schedule_id

                $sent_data3=[
                        'journal_type_id'=> 6,
                        'ref_no' => $loan_detail['loan_no'],
                        'ref_id' => $inserted_id,
                        'description' => 'Loan Payment imported from Excel [ '.$loan_data[1].' ]',
                        'action_date' => $repayment_date,
                        'member_name' => $loan_data[1],
                        'principal_amount' => $loan_data[2],
                        'interest_amount' => $loan_data[3],
                        'penalty_amount' => $loan_data[4],
                        'linked_account_id' =>40,
                        'loan_receivable_account_id' =>1,
                        'interest_income_account_id' =>6,
                        'interest_receivable_account_id' =>7,
                        'penalty_account_id'=>4
                    ];
                    $this->do_journal2($sent_data3);
            return 1;
            }
            return 0;
    }
    
     private function do_journal2($sent_data){
         
        $this->debit_or_credit5 =  $this->accounts_model->get_normal_side(1,true);//loan_receivable_account_id
        $this->debit_or_credit6= $this->accounts_model->get_normal_side(40);//linked_account_id
        $this->debit_or_credit7 = $this->accounts_model->get_normal_side(7,true);//interest_receivable_account_id
        $this->debit_or_credit8 = $this->accounts_model->get_normal_side(4);

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
                        'narrative'=> strtoupper("Loan principal payment on [ ".$sent_data['member_name']." ] ".$sent_data['action_date']),
                        'account_id'=>$sent_data['loan_receivable_account_id'],
                        'status_id'=> 1
                    ];
                    $data[1] =[
                        'reference_no' => $sent_data['ref_no'],
                        'reference_id' => $sent_data['ref_id'],
                        'transaction_date' => $sent_data['action_date'],
                        $this->debit_or_credit6=> $sent_data['principal_amount'],
                        'narrative'=> strtoupper("Loan principal payment on [ ".$sent_data['member_name']." ] ".$sent_data['action_date']),
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
                    'narrative'=> strtoupper("Loan interest payment on  [ ".$sent_data['member_name']." ] ".$sent_data['action_date']),
                    'account_id'=> $sent_data['interest_receivable_account_id'],
                    'status_id'=> 1
                ];
                $data[3] =[
                    'reference_no' => $sent_data['ref_no'],
                    'reference_id' => $sent_data['ref_id'],
                    'transaction_date' => $sent_data['action_date'],
                    $this->debit_or_credit6 => $sent_data['interest_amount'],
                    'narrative'=> strtoupper("Loan interest payment on [ ".$sent_data['member_name']." ] ".$sent_data['action_date']),
                    'account_id'=> $sent_data['linked_account_id'],
                    'status_id'=> 1
                ];
            }

            if ($sent_data['penalty_amount'] !=null && !empty($sent_data['penalty_amount']) && $sent_data['penalty_amount'] !='0') {
              $data[4] =[
                    'reference_no' => $sent_data['ref_no'],
                    'reference_id' => $sent_data['ref_id'],
                    'transaction_date' => $sent_data['action_date'],
                    $this->debit_or_credit8 => $sent_data['penalty_amount'],
                    'narrative'=> strtoupper("Loan Penalty payment on [ ".$sent_data['member_name']." ] ".$sent_data['action_date']),
                    'account_id'=> $sent_data['penalty_account_id'],
                    'status_id'=> 1
                ];
                $data[5] =[
                    'reference_no' => $sent_data['ref_no'],
                    'reference_id' => $sent_data['ref_id'],
                    'transaction_date' => $sent_data['action_date'],
                    $this->debit_or_credit6 => $sent_data['penalty_amount'],
                    'narrative'=> strtoupper("Loan Penalty payment on [ ".$sent_data['member_name']." ] ".$sent_data['action_date']),
                    'account_id'=> $sent_data['linked_account_id'],
                    'status_id'=> 1
                ];
            }
            return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
        }
    return false;
    }

}
