<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Eric
 */
class Loan_writeoff extends CI_Controller {

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
        $this->load->model("accounts_model");
        $this->load->model("Data_import_model");

    }

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."ubteb".DIRECTORY_SEPARATOR;
        $file_name = "writeoff_data.csv";
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
                    if ($field_names[0] != "Client_loan_id") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key Client_loan_id";
                        fclose($handle);
                        return $feedback;
                    }
                } else {
                    // print("Hello"); die;
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

        $date_created = $this->helpers->extract_date_time($loan_data[4]);
        $action_date = $this->helpers->extract_date_time($loan_data[4],"Y-m-d");
        
        $single_row = [
            "journal_type_id" => 6,
            "ref_id" => $loan_data[1],
            "ref_no" => "LN00".$loan_data[1],
            "description" => "Interest writeoff- Data imported",
            "transaction_date" =>  $action_date,
            "status_id" => 1,
            "date_created" => $date_created,
            "created_by" => 1,
        ];
        $insert_id=$this->Data_import_model->add_journal_tr($single_row);

        if($insert_id){
            $interest_income_account_id=6;
            $interest_receivable_account_id=7;
            $debit_or_credit1 = $this->accounts_model->get_normal_side($interest_receivable_account_id,true); 
            $debit_or_credit2 = $this->accounts_model->get_normal_side($interest_income_account_id,true);

            $data[0] =[
                        $debit_or_credit1=> $loan_data[2],
                        'narrative'=> strtoupper("Loan interest write off due to ".$loan_data[5]." on ".$action_date),
                        'account_id'=> $interest_receivable_account_id,
                        'status_id'=> 1
            ];
            $data[1] =[
                $debit_or_credit2=> $loan_data[2],
                'narrative'=> strtoupper("Loan interest write off due to ".$loan_data[5]." on ".$action_date),
                'account_id'=> $interest_income_account_id,
                'status_id'=> 1
            ];
            $this->Data_import_model->add_journal_tr_line($insert_id, $data);
            return 1;
        }
    return 0;
    }

}