<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Reagan
 */
class Data_upgrade extends CI_Controller {

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
        $this->load->model('repayment_schedule_model');
        $this->load->model('loan_state_model');
        $this->load->model("accounts_model");
        $this->load->model("applied_loan_fee_model");
        $this->load->model("Data_import_model");
       
    }

    public function index(){
         $loans=$this->client_loan_model->get('loan_state.state_id IN(7,12)');
        print_r(count($loans));die();
         foreach ($loans as $key => $value) {
             $sent_data['loan']=[
                        'journal_type_id'=> 4,
                        'ref_no' => $value['loan_no'],
                        'ref_id' => $value['id'],
                        'description' => 'LOAN DISBURSED',
                        'action_date' => $value['application_date'],
                        'principal_amount'=>$value['amount_approved'],
                        'source_fund_account_id'=>40,
                        'loan_receivable_account_id'=>1,
                        'interest_income_account_id' =>6,
                        'interest_receivable_account_id' =>7
                    ];
             $schedule=$this->repayment_schedule_model->get2_for_import($value['id']);
             foreach ($schedule as $key => $val) {
                      $index_key+=2;
                        $interest_data[$index_key-1]=[
                            'reference_no' => $value['loan_no'],
                            'reference_id' => $val['id'],
                            'transaction_date' => $val['repayment_date'],
                            'credit_amount'=> $val['interest_amount'],
                            'narrative'=> strtoupper("Interest on Loan Disbursed on ".$val['application_date']),
                            'account_id'=> 6,
                            'status_id'=> 1
                        ];

                        $interest_data[$index_key] =  [
                            'reference_no' => $value['loan_no'],
                            'reference_id' => $val['id'],
                            'transaction_date' => $val['repayment_date'],
                            'debit_amount'=> $val['interest_amount'],
                            'narrative'=> strtoupper("Interest on Loan Disbursed on ".$val['application_date']),
                            'account_id'=> 7,
                            'status_id'=> 1
                        ];
                    
                       
             }

           $this->do_journal($sent_data['loan'],$interest_data);
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

            $data=array_merge($data,$interest_data);
            return $this->Data_import_model->add_journal_tr_line($insert_id, $data);

        }

    }

}