<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Description of Savings_transaction
 *
 * @author Reagan
 */
class Savings_bulk_withdrawal_transactions extends CI_Controller
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
        $folder = "data_extract" . DIRECTORY_SEPARATOR . "compassion" . DIRECTORY_SEPARATOR;
        $file_name = "Bulk_Savings_Withdraw.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates($file_path);
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
                    if ($field_names[0] != "Account Number") {
                        $feedback['message'] = "Please ensure that the first cell (Account ID) contains the key Account No";
                        fclose($handle);
                        return $feedback;
                    }
                } else {

                    $total_counts = $count;
                    $this->insert_transaction_data($data1);
                    //$this->insert_share_transaction_data($data1);
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
        /* getting respective account_no_id for the CSV account no.
          @Ambrose 
        */
        $ac_ids[] = $transaction[0];
        foreach ($ac_ids as $accountNumber) {
            $acc_id = $accountNumber;
            $where = "sa.account_no='" . $acc_id . "'";
            $accountID3 = $this->Savings_account_model->get($where);
            foreach ($accountID3 as $accountHolderAccountID) {
                $sysAccountID = $accountHolderAccountID['id'];

                $accountNoID = $sysAccountID;
                $transactionDateCsv =  $transaction[3];
                // Transposing  date in d/m/y from the CSV as passing it to the date transformer function.
                $transactionDateCsvExploded = explode("/", $transactionDateCsv);
                $transactionDate = $transactionDateCsvExploded[1] . "/" . $transactionDateCsvExploded[0] . "/" . $transactionDateCsvExploded[2];
                $transactionDate2 = $this->helpers->extract_date_time($transactionDate, "Y-m-d");

                $trans_row = [
                    "transaction_no" =>  date('yws') . mt_rand(1000000, 9999999),
                    "account_no_id" => $accountNoID,
                    "debit" => $transaction[2],
                    "credit" => NULL,
                    "transaction_type_id" => 1,
                    "payment_id" => 2,
                    "transaction_date" => $transactionDate2,
                    "narrative" => "Bulk withdrawal transaction of [" . $transaction[2] . "] from account no " . $accountNoID,
                    "status_id" => 1,
                    "date_created" => time(),
                    "created_by" => 1,
                    //"branch_id" => 1,
                    //"organisation_id" => 1
                ];
                $transaction_data = $this->Data_import_model->add_transaction($trans_row);
                if (!empty($transaction_data)) {

                    $single_row = [
                        "journal_type_id" => 8,
                        "ref_id" => $transaction_data['transaction_id'],
                        "ref_no" => $transaction_data['transaction_no'],
                        'description' => "Bulk withdrawal transaction of [" . $transaction[2] . "] from account no " . $accountNoID,
                        "transaction_date" =>  $transactionDate2,
                        "status_id" => 1,
                        "date_created" => time(),
                        "created_by" => 1,
                        "modified_by" => 1,
                        //"branch_id" => 1,
                        //"organisation_id" => 1
                    ];
                    $insert_id = $this->Data_import_model->add_journal_tr($single_row);
                    if (!empty($insert_id)) {

                        $data[0] = [
                            'debit_amount' => NULL,
                            'transaction_date' =>  $transactionDate2,
                            'reference_id' => $transaction_data['transaction_id'],
                            'reference_no' => $transaction_data['transaction_no'],
                            'credit_amount' => $transaction[2],
                            'narrative' => "Bulk withdrawal transaction of [" . $transaction[2] . "] from account no " . $accountNoID,
                            'account_id' => 40,
                            'status_id' => 1,
                            //"branch_id" => 1,
                            //"organisation_id" => 1
                        ];
                        $data[1] = [
                            'credit_amount' => NULL,
                            'transaction_date' =>  $transactionDate2,
                            'reference_id' => $transaction_data['transaction_id'],
                            'reference_no' => $transaction_data['transaction_no'],
                            'debit_amount' => $transaction[2],
                            'narrative' => "Bulk withdrawal transaction of [" . $transaction[2] . "] from account no " . $accountNoID,
                            'account_id' => 8,
                            'status_id' => 1,
                            //"branch_id" => 1,
                            //"organisation_id" => 1
                        ];
                        return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
                    } else {
                        echo "Journal Failed";
                        die();
                    }
                } else {
                    echo "Transaction Failed";
                    die();
                }
            }
        }
    }
}
