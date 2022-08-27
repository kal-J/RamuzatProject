<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Reagan
 */
class Loans extends CI_Controller {

    public function __construct() {
        parent :: __construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('/');
        }
        $this->load->model("Data_import_model");
        $this->load->model("journal_transaction_line_model");
        
        $this->load->library("helpers");
        
         ini_set('memory_limit', '200M');
            ini_set('upload_max_filesize', '200M');
            ini_set('post_max_size', '200M');
            ini_set('max_input_time', 3600);
            ini_set('max_execution_time', 3600);
    }

    public function index() {

        $this->db->trans_start();
        $folder = "data_extract".DIRECTORY_SEPARATOR."jcu".DIRECTORY_SEPARATOR;
        $file_name = "loans.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates($file_path);

        $this->db->trans_complete();

        echo json_encode($feedback);
    }

    private function run_updates($file_path) {
        $handle = fopen($file_path, "r");
        $total_counts = $count = 0;
        $field_names = $batch_data = [];
        $feedback = ["success" => false, "message" => "File Could not be opened"];
        if ($handle) {
            while (($data = fgetcsv($handle, 10240, ",")) !== FALSE) {
                $data1 = $this->security->xss_clean($data);
                if ($count == 0) {//the row with the field_names
                    $field_names = $data1;
                    //echo $field_names[0];die();
                    if ($field_names[0] != "DATE") {
                        $feedback['message'] = "Please ensure that the first cell (ID) contains the key DATE";
                        fclose($handle);
                        return $feedback;
                    }

                } else {
                    
                   $total_counts = $count;
                   //$this->insert_transaction_data($data1);
                   $this->do_journal($data1);
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
    
    public function do_journal($transaction)
    {
        $transaction_date = $this->helpers->extract_date_time($transaction[9],"Y-m-d");
        //print_r($transaction_date); die;
        
        $reference_no = date('yws').mt_rand(1000000,9999999);
        $single_row = [
            "journal_type_id" =>$transaction[10],
            "ref_id" => $transaction[1],
            "ref_no" => $reference_no,
            "description" =>$transaction[2],
            "transaction_date" =>  $transaction_date,
            "status_id" => 1,
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" =>1
        ];

        $insert_id=$this->Data_import_model->add_journal_tr($single_row);

        if(!empty($insert_id)){
                 
            $data[0] = [
                'debit_amount' => NULL,
                'transaction_date' =>$transaction_date,
                'reference_id' => $transaction[1],
                'reference_no' => $reference_no,
                'credit_amount' =>$transaction[8],
                'narrative' => $transaction[3],
                'account_id' =>$transaction[7],
                'status_id' => 1
            ];
            $data[1] = [
                'credit_amount' => NULL,
                'transaction_date' =>$transaction_date,
                'reference_id' => $transaction[1],
                'reference_no' => $reference_no,
                'debit_amount' => $transaction[8],
                'narrative' => $transaction[3],
                'account_id' => $transaction[6],
                'status_id' => 1
            ];

         return $this->Data_import_model->add_journal_tr_line($insert_id, $data);
         }else{
              echo "journal failed";die();
         }

    }
    
 


}
