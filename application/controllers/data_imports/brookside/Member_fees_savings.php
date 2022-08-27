<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Reagan
 */
class Member_fees_savings extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('/');
        }
        $this->load->model("Data_import_model");
        $this->load->model("journal_transaction_line_model");
        $this->load->model("Savings_account_model");

        $this->load->library("helpers");

        ini_set('memory_limit', '200M');
        ini_set('upload_max_filesize', '200M');
        ini_set('post_max_size', '200M');
        ini_set('max_input_time', 3600);
        ini_set('max_execution_time', 3600);
    }

    public function index()
    {
        $this->db->trans_start();
        $folder = "data_extract" . DIRECTORY_SEPARATOR . "brookside" . DIRECTORY_SEPARATOR;
        $file_name = "Member_fees_sv.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates($file_path);
        $this->db->trans_complete();
        echo json_encode($feedback);
    }

    private function run_updates($file_path)
    {
        $handle = fopen($file_path, "r");
        $total_counts = $count = 0;
        $field_names = $batch_data = [];
        $feedback = ["success" => false, "message" => "File Could not be opened"];
        if ($handle) {
            while (($data = fgetcsv($handle, 10240, ",")) !== FALSE) {
                $data1 = $this->security->xss_clean($data);
                if ($count == 0) { //the row with the field_names
                    $field_names = $data1;
                    //echo $field_names[0];die();
                    if ($field_names[0] != "Member_id") {
                        $feedback['message'] = "Please ensure that the first cell (Member_id) contains the key Account No";
                        fclose($handle);
                        return $feedback;
                    }
                } else {

                    $total_counts = $count;
                    $this->insert_transaction_data($data1);
                }

                $count++;
            }

            //$batch_data = [];
            fclose($handle);

            if (is_numeric($total_counts)) {
                $feedback["success"] = true;
                $feedback["message"] = "Update done\n $total_counts records updated";
            }
        }
        return $feedback;
    }


    private function insert_transaction_data($transaction)
    {
        $transaction_date = date('Y-m-d', strtotime($transaction[5]));

        $amount = 10000;
        $journal_type_id = 12;
        // Get savings account id
        $acc = $this->Savings_account_model->get_savings_account("member.id='{$transaction[0]}'")[0];
        //echo $transaction[1]; die;


        $trans_row = [
            "transaction_no" =>  date('yws').mt_rand(1000000,9999999),
            "account_no_id" => $acc['id'],
            "debit" => $amount,
            "transaction_type_id" => 4,
            "payment_id" => 5,
            "transaction_date" => $transaction_date,
            "narrative" => $transaction[3] . " [ " . $transaction[2] . " ] " . "[ ". $transaction[1] . " ]",
            "status_id" => 1,
            "date_created" => time(),
            "created_by" => 1
        ];
        //for transfers to shares
        //$this->insert_share_transaction_data($transaction);
        $transaction_data = $this->Data_import_model->add_transaction($trans_row);
        //echo json_encode($transaction_data); die;
        if (!empty($transaction_data)) {

            $single_row = [
                "journal_type_id" => $journal_type_id,
                "ref_id" => $transaction_data['transaction_id'],
                "ref_no" => $transaction_data['transaction_no'],
                "description" => $transaction[3] . " [ " . $transaction[2] . " ]",
                "transaction_date" =>  $transaction_date,
                "status_id" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1
            ];
            $insert_id = $this->Data_import_model->add_journal_tr($single_row);
            if (!empty($insert_id)) {

                $data[0] = [
                    'debit_amount' => $amount,
                    'transaction_date' =>  $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'credit_amount' => NULL,
                    'narrative' => $transaction[3] . " [ " . $transaction[2] . " ] made on " . $transaction_date,
                    'account_id' => 8,
                    'status_id' => 1
                ];
                $data[1] = [
                    'credit_amount' => $amount,
                    'transaction_date' => $transaction_date,
                    'reference_id' => $transaction_data['transaction_id'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'debit_amount' => NULL,
                    'narrative' => "Reverse transaction " . $transaction[3] . " [ " . $transaction[2] . " ] made on " . $transaction_date,
                    'account_id' => 40,
                    'status_id' => 1
                ];
                return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
            } else {
                echo "journal failed";
                die();
            }
        } else {
            echo "trasaction failed";
            die();
        }
    }
}


//UPDATE `fms_applied_member_fees` SET payment_id=5 WHERE id >= 130