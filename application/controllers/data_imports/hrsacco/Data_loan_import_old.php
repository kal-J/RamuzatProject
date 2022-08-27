<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Eric
 */
class Data_loan_import_old extends CI_Controller {

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
        // $this->load->model('client_loan_model');
        // $this->load->model('loan_product_model');
        // $this->load->model('savings_account_model');
        // $this->load->model('client_loan_monthly_income_model');
        // $this->load->model('client_loan_monthly_expense_model');
        // $this->load->model('loan_attached_saving_accounts_model');
        // $this->load->model('payment_details_model');
        // $this->load->model('loan_state_model');
        // $this->load->model("applied_loan_fee_model");
        $this->load->model('repayment_schedule_model');
        $this->load->model("accounts_model");
        $this->load->model("Data_import_model");

    }

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."hdrsacco".DIRECTORY_SEPARATOR;
        $file_name = "old_member_loans.csv";
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
                    if ($field_names[0] != "id") {
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
        // $loan_receivable_account_id = [6, 93];
        // $interest_income_account_id = [11, 97];
        // $interest_receivable_account_id = [12, 99];

        $loan_receivable_account_id = [92, 93];
        $interest_income_account_id = [78, 97];
        $interest_receivable_account_id = [98, 99];

        $action_date = $this->helpers->extract_date_time($loan_data[5],"Y-m-d");
        $inserted_loan_id=$loan_data[0];
        $client_loan_no=$loan_data[1];
        $amount=$loan_data[4];

        try {
            if ($loan_data[0] != "" && $loan_data[0] != 'NULL') {
                if ($inserted_loan_id) {

                    $index_key=2; $interest_data =[];
                    $debit_or_credit3 = $this->accounts_model->get_normal_side($interest_income_account_id[trim($loan_data[3])-1]);
                    $debit_or_credit4 = $this->accounts_model->get_normal_side($interest_receivable_account_id[trim($loan_data[3])-1]);
                    $schedule_data = $this->repayment_schedule_model->get($inserted_loan_id);
                    foreach ($schedule_data as $key => $value) {
                        $index_key+=2;
                        $interest_data[$index_key-1]=[
                            'reference_no' => $client_loan_no,
                            'reference_id' => $value['id'],
                            'transaction_date' => $value['repayment_date'],
                            $debit_or_credit3=> $value['interest_amount'],
                            'narrative'=> strtoupper("Interest on Loan Disbursed on ".$action_date),
                            'account_id'=> $interest_income_account_id[trim($loan_data[3])-1],
                            'status_id'=> 1
                        ];

                        $interest_data[$index_key] =  [
                            'reference_no' => $client_loan_no,
                            'reference_id' => $value['id'],
                            'transaction_date' => $value['repayment_date'],
                            $debit_or_credit4=> $value['interest_amount'],
                            'narrative'=> strtoupper("Interest on Loan Disbursed on ".$action_date),
                            'account_id'=> $interest_receivable_account_id[trim($loan_data[3])-1],
                            'status_id'=> 1
                        ];
                        
                    }
                    $sent_data=[
                        'journal_type_id'=> 4,
                        'ref_no' => $client_loan_no,
                        'ref_id' => $inserted_loan_id,
                        'description' => 'Loan imported from Excel',
                        'action_date' => $action_date,
                        'principal_amount'=>$amount,
                        'interest_amount' => '',
                        'source_fund_account_id'=>28,
                        'loan_receivable_account_id'=>$loan_receivable_account_id[trim($loan_data[3])-1],
                        'interest_income_account_id' =>$interest_income_account_id[trim($loan_data[3])-1],
                        'interest_receivable_account_id' =>$interest_receivable_account_id[trim($loan_data[3])-1]
                    ];
                    $this->do_journal($sent_data,true,$interest_data);
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
        if(!empty($insert_id)){
            $data=[];
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