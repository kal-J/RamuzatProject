<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Data_loan_import
 *
 * @author Eric
 */
class Data_loan_import extends CI_Controller {

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
        $this->load->model('loan_product_model');
        $this->load->model('savings_account_model');
        $this->load->model('client_loan_monthly_income_model');
        $this->load->model('client_loan_monthly_expense_model');
        $this->load->model('loan_attached_saving_accounts_model');
        $this->load->model('payment_details_model');
        $this->load->model('loan_state_model');
    }

    public function index() {
        $folder = "data_extract".DIRECTORY_SEPARATOR."members".DIRECTORY_SEPARATOR;
        $file_name = "member_loans.csv";
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
                    if ($field_names[0] != "LOAN_ID") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key LOAN_ID";
                        fclose($handle);
                        return $feedback;
                    }
                } else {
                    if (array_key_exists($data1[2], $client_loans)) {
                        $client_loans[$data1[2]]=($client_loans[$data1[2]]+1);
                    }else{
                        $client_loans[$data1[2]]=1;
                    }
                    $total_loans = $total_loans + $this->insert_loan_data($data1,$client_loans[$data1[2]]);
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

    private function insert_loan_data($loan_data,$loan_num_) {
        $preferred_payment = ["CSH" => 1, "BNK" => 2];
        $topUp = ["N" => 0, "Y" => 1];
        $loan_product_data = $this->loan_product_model->get_product($loan_data[29]);
        //print_r($loan_product_data); die;
        $date_created = $this->helpers->extract_date_time($loan_data[25]);
        $action_date = $this->helpers->extract_date_time($loan_data[8],"Y-m-d");
        if ($loan_data[0] != "" && $loan_data[0] != 'NULL') {
            $single_row = [
                "id" => $loan_data[0],
                "member_id" =>  $loan_data[1],
                "loan_no" => $loan_data[2].'-'.$loan_num_,
                "credit_officer_id" => 11,//static since he is alone
                "loan_product_id" => $loan_data[29],
                "topup_application" => $topUp[isset($loan_data[26]) ? $loan_data[26] : 'N'],
                "requested_amount" => $loan_data[3],
                "application_date" => $action_date,
                "source_fund_account_id" => $loan_product_data['fund_source_account_id'],
                "disbursement_date" => $action_date,
                "suggested_disbursement_date" => $action_date,
                "interest_rate" => $loan_data[10],
                "offset_period" => 0,
                "offset_made_every" => 1,
                "repayment_frequency" => 1,
                "repayment_made_every" => 3,
                "installments" => $loan_data[9],
                "link_to_deposit_account" => 1,
                "comment" => 'Loan imported from the old system',
                "amount_approved" => $loan_data[3],
                "approval_date" => $action_date,
                "approved_installments" => $loan_data[9],
                "approved_repayment_frequency" => 1,
                "approved_repayment_made_every" => 3,
                "approved_by" => $loan_data[24],
                "approval_note" => 'Old system didn\'t have approval level so the one who entered the loan in the system becames the one who approved',
                "loan_purpose" => 'N/L',
                "preferred_payment_id" => $preferred_payment[isset($loan_data[16]) ? $loan_data[16] : 1],
                "date_created" => $date_created,
                "created_by" => $loan_data[24],
                "modified_by" => $loan_data[24]
            ];
            //insert into the client table
            $this->client_loan_model->set2($single_row);
            
            //add the client's expense
            if ($loan_data[19]>0) {

                $client_expense_data = [
                    "client_loan_id" => $loan_data[0],
                    "expense_id" => 1,
                    "amount" => $loan_data[19],
                    "status_id" => 1,
                    "description" => 'Client\'s monthly expense',
                    "date_created" => $date_created,
                    "created_by" => $loan_data[24],
                    "modified_by" => $loan_data[24]
                ];
                $this->client_loan_monthly_expense_model->set($client_expense_data);
            }

            //add the client incomes
            if ($loan_data[19]>0) {
                $client_income_data = [
                    "client_loan_id" => $loan_data[0],
                    "income_id" => 1,
                    "amount" => $loan_data[18],
                    "status_id" => 1,
                    "description" => 'Client\'s monthly income',
                    "date_created" => $date_created,
                    "created_by" => $loan_data[24],
                    "modified_by" => $loan_data[24]
                ];
            $this->client_loan_monthly_income_model->set($client_income_data);
            }

            //attach the savings a/c
            $savings_ac_data=$this->savings_account_model->get_savings_acc_details($loan_data[11]);
            $client_savings_ac = [
                "loan_id" => $loan_data[0],
                "saving_account_id" => isset($savings_ac_data[0]['id'])?$savings_ac_data[0]['id']:0,
                "status_id" => 1,
                "date_created" => $date_created,
                "created_by" => $loan_data[24],
                "modified_by" => $loan_data[24]
            ];
            $this->loan_attached_saving_accounts_model->set2($client_savings_ac);

            //Record the state of the loan
            $loan_state=["CLS"=>10, "CNL"=>3, "REJ"=>2, "STP"=>12, "APR"=>7,"OTHER"=>4];
            $loan_state_comment=["CLS"=>'Obligations met Loan closed - Data imported', "CNL"=>'LOAN REVERSAL or CANCELLED ON TRANS REVERAL-Data imported', "REJ"=>'Loan application Declined - Data imported', "STP"=>'Loan Termination/ Locked - Data imported', "APR"=>'Loan Dispersed - Data imported',"OTHER"=>'Loan state unknown so considered withrawn-Data imported from Old system'];
            $loan_state_details = [
                "client_loan_id" => $loan_data[0],
                "state_id" => $loan_state[isset($loan_data[23])?$loan_data[23]:"OTHER"],
                "comment" => $loan_state_comment[isset($loan_data[23])?$loan_data[23]:"OTHER"],
                "action_date" => $action_date,
                "date_created" => $date_created,
                "created_by" => $loan_data[24],
                "modified_by" => $loan_data[24]
            ];
            $this->loan_state_model->set2($loan_state_details);


            //attach the payment details
            $client_payment_details = [
                "client_loan_id" => $loan_data[0],
                "ac_name" => $loan_data[13],
                "ac_number" => $loan_data[12],
                "bank_branch" => $loan_data[15],
                "bank_name" => $loan_data[14],
                "status_id" => 1,
                "date_created" => $date_created,
                "created_by" => $loan_data[24],
                "modified_by" => $loan_data[24]
            ];
            $this->payment_details_model->set2($client_payment_details);
            return 1;
        }
        return 0;
    }

}
