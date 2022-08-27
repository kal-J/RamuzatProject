<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Eric
 */
class Loan_schedule_import extends CI_Controller {

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

    }

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."ubteb".DIRECTORY_SEPARATOR;
        $file_name = "loan_schedule.csv";
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
                    if ($field_names[0] != "DATE") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key DATE";
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
        $topUp = ["N" => 0, "Y" => 1];

        $date_created = $this->helpers->extract_date_time($loan_data[0]);
        $action_date = $this->helpers->extract_date_time($loan_data[0],"Y-m-d");
        $payment_date=date('Y-m-d',strtotime('+27 day', strtotime($action_date)));

        $payment_status = ["FULL" => 1, "PARTIAL" => 2, "PAID OFF" => 3, "PAID UP" => 3];

        try {
            if ($loan_data[0] != "" && $loan_data[0] != 'NULL') {
                $payment_data = [
                    "client_loan_id" =>  $loan_data[7],
                    "repayment_schedule_id" => $loan_data[8],
                    "paid_interest" => $loan_data[5],
                    "paid_principal" => $loan_data[6],
                    "paid_penalty" => 0,
                    "payment_date" => $payment_date,
                    "transaction_channel_id" => 1,
                    "comment" => 'Loan Payment imported',
                    "status_id" => 1,
                    'date_created' => $date_created,
                    "created_by" => 1
                ];
                $inserted_id=$this->loan_installment_payment_model->set3($payment_data);

                $data = array(
                            'payment_status' => $payment_status[$loan_data[9]],
                            'actual_payment_date' => $payment_date,
                            'modified_by' => 1
                            );
                $this->repayment_schedule_model->update2($data,'repayment_schedule.id ='.$loan_data[8]);

                $sent_data=[
                        'journal_type_id'=> 6,
                        'ref_no' => null,
                        'ref_id' => $inserted_id,
                        'description' => 'Loan Payment imported from Excel',
                        'action_date' => $payment_date,
                        'principal_amount' =>$loan_data[6],
                        'interest_amount' => $loan_data[5],
                        'linked_account_id' =>40,
                        'loan_receivable_account_id' =>1,
                        'interest_income_account_id' =>6,
                        'interest_receivable_account_id' =>7
                    ];
                    $this->do_journal($sent_data);
                return 1;
            }
            return 0;
        } catch (Exception $e) {
            return false;
        }
        return 0;
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
    return false;
    }

}