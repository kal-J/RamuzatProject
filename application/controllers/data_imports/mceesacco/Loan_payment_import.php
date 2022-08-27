<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Eric
 */
class Loan_payment_import extends CI_Controller {

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

        $this->debit_or_credit1 =  $this->accounts_model->get_normal_side(1,true);//loan_receivable_account_id
        $this->debit_or_credit3= $this->accounts_model->get_normal_side(40);//linked_account_id
        $this->debit_or_credit4 = $this->accounts_model->get_normal_side(7,true);//interest_receivable_account_id

    }

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."mceesacco".DIRECTORY_SEPARATOR;
        $file_name = "loan_payments.csv";
        $file_path = FCPATH .$folder.$file_name;
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
                   
                    if ($field_names[1] != "Client Loan ID") {
                        $feedback['message'] = "Please ensure that the first cell (A2) contains the key Client_loan_id";
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
       // $datePartsFromExcel = explode('-',$loan_data[7]);
       
        // $date_created= $datePartsFromExcel[2]."-".$datePartsFromExcel[1]."-".$datePartsFromExcel[0];
        $date_created = date("Y-m-d", strtotime($loan_data[7]));
        //print_r($date_created);die;

        // $date_created = $this->helpers->extract_date_time($loan_data[2]);
        $payment_date=   $date_created;//$this->helpers->extract_date_time($loan_data[7],"Y-m-d");
        //print_r($payment_date);die;

        $payment_status = ["FULL" => 1, "PARTIAL" => 2, "PAID OFF" => 3, "PAID UP" => 3];
     
        $penaltyPaid =intval($loan_data[6]);
       
        // $loan_no='LN0000'+$loan_data[0];
        // $loan_details = $this->Data_import_model->get_loan("loan_no='".$loan_no."'");

        try {
            if ($loan_data[0] != "" && $loan_data[0] != 'NULL') {
                $payment_data = [
                    "client_loan_id" =>  $loan_data[1],
                    "paid_principal" => $loan_data[4],
                    "paid_interest" => $loan_data[5],
                    "repayment_schedule_id" => $loan_data[2],
                    "paid_penalty" =>  $penaltyPaid,
                    "payment_date" => $payment_date,
                    "transaction_channel_id" => 1,
                    "comment" => 'Loan Payment imported',
                    "status_id" => 1,
                    'date_created' => $date_created,
                    "created_by" => 1
                ];
                $inserted_id=$this->loan_installment_payment_model->set3($payment_data);

                $data = array(
                            'payment_status' => intval($loan_data[8]),//All payments will be partial
                            'actual_payment_date' => $payment_date,
                            'modified_by' => 1
                            );
                $this->repayment_schedule_model->update2($data,'repayment_schedule.id ='.$loan_data[0]);//loan_id is the same and the repayment_schedule_id

                $sent_data=[
                        'journal_type_id'=> 6,
                        'ref_no' => null,
                        'ref_id' => $inserted_id,
                        'description' => 'Loan Payment imported from Excel',
                        'action_date' => $payment_date,
                        'principal_amount' => $loan_data[5],
                        'interest_amount' => $loan_data[6],
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

            if ($sent_data['principal_amount'] !=null && !empty($sent_data['principal_amount']) && $sent_data['principal_amount'] !='0') {
                  $data[0] =[
                        'reference_no' => $sent_data['ref_no'],
                        'reference_id' => $sent_data['ref_id'],
                        'transaction_date' => $sent_data['action_date'],
                        $this->debit_or_credit1 => $sent_data['principal_amount'],
                        'narrative'=> strtoupper("Loan principal payment on ".$sent_data['action_date']),
                        'account_id'=>$sent_data['loan_receivable_account_id'],
                        'status_id'=> 1
                    ];
                    $data[1] =[
                        'reference_no' => $sent_data['ref_no'],
                        'reference_id' => $sent_data['ref_id'],
                        'transaction_date' => $sent_data['action_date'],
                        $this->debit_or_credit3=> $sent_data['principal_amount'],
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
                    $this->debit_or_credit4 => $sent_data['interest_amount'],
                    'narrative'=> strtoupper("Loan interest payment on ".$sent_data['action_date']),
                    'account_id'=> $sent_data['interest_receivable_account_id'],
                    'status_id'=> 1
                ];
                $data[3] =[
                    'reference_no' => $sent_data['ref_no'],
                    'reference_id' => $sent_data['ref_id'],
                    'transaction_date' => $sent_data['action_date'],
                    $this->debit_or_credit3 => $sent_data['interest_amount'],
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