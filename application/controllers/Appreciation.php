<?php

/**
 * Description of Asset_depreciation
 *
 * @author Allan Jes modified by ajuna reagan
 */
class Appreciation extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('appreciation_model');
        $this->load->model('ledger_model');
        $this->load->library(array("form_validation", "helpers"));
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 8, $this->session->userdata('staff_id'));
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['privileges'] = array_column($this->data['privilege_list'], "privilege_code");
        }
    }

    public function jsonList() {
        $data['data'] = $this->appreciation_model->get();
        echo json_encode($data);
    }
    
    public function create() {
        $this->form_validation->set_rules("appre_transaction_date", "Date recorded", array("required"), array("required" => "%s must be entered/selected"));
        $this->form_validation->set_rules("narrative", "Narrative", array("required"), array("required" => "%s must be entered"));
        $this->form_validation->set_rules("fixed_asset_id", "Fixed Asset", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("income_account_id", "Income Account", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("appreciation_account_id", "Appreciation Account", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("amount", "Amount", array("required","numeric"), array("required" => "%s must be entered"));
        // $this->form_validation->set_rules("financial_year_id", "Financial year", array("required","valid_app_year[".$this->input->post('fixed_asset_id')."]"), array("required" => "Select financial year","valid_app_year"=>"Appreciation for this year already submitted"));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } 
        else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->appreciation_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Fixed asset appreciation details successfully updated";
                    $feedback['assets'] = $this->appreciation_model->get($this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the fixed asset appreciation details";
                }
            } 
            else {
                $transaction_data = $this->appreciation_model->set();
                if (!empty($transaction_data)) {
                   $this->do_journal_transaction($transaction_data);
                    $feedback['success'] = true;
                    $feedback['message'] = "Fixed asset appreciation details successfully saved";
                } else {
                    $feedback['message'] = "There was a problem saving the fixed asset appreciation data";
                }
            }
        }
        echo json_encode($feedback);
    }

    public function delete() {
        $response['message'] = "Fixed asset appreciation details could not be deleted, contact support.";
        $response['success'] = FALSE;
        if ($this->appreciation_model->delete($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] = "Fixed asset appreciation details successfully deleted.";
        }
        echo json_encode($response);
    }

    public function change_status() {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Fixed asset appreciation details could not be ".$msg."activated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->depreciation_model->deactivate($this->input->post('id'))) {
            $response['message'] = "Fixed asset appreciation details successfully ".$msg."activated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }
   
     private function do_journal_transaction($transaction_data){

        $this->load->model('journal_transaction_model');
        $this->load->model('accounts_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('journal_transaction_line_model');
        $data = [
            'transaction_date'=> $this->input->post('transaction_date'),
            'description'=> $this->input->post('narrative'),
            'ref_no'=> $transaction_data['transaction_no'],
            'ref_id'=> $transaction_data['transaction_id'],
            'status_id'=> 1,
            'journal_type_id'=> 29
        ];

        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        //then we prepare the journal transaction lines
               $debit_or_credit2 = $this->accounts_model->get_normal_side($this->input->post('income_account_id'), false);
               $debit_or_credit1 = $this->accounts_model->get_normal_side($this->input->post('appreciation_account_id'), false);
                $data = [
                    [
                        $debit_or_credit1=>$this->input->post('amount'),
                        'narrative'=> $this->input->post('transaction_date')." ".$this->input->post('narrative'),
                        'reference_no'=>$transaction_data['transaction_no'],
                        'reference_id'=>$transaction_data['transaction_id'],
                        'transaction_date'=>$this->input->post('transaction_date'),
                        'account_id'=> $this->input->post('appreciation_account_id'),
                        'status_id'=> 1
                    ],
                    [
                        $debit_or_credit2=> $this->input->post('amount'),
                        'narrative'=> $this->input->post('transaction_date')." ".$this->input->post('narrative'),
                        'reference_no'=>$transaction_data['transaction_no'],
                        'reference_id'=>$transaction_data['transaction_id'],
                        'transaction_date'=>$this->input->post('transaction_date'),
                        'account_id'=> $this->input->post('income_account_id'),
                        'status_id'=> 1
                    ]
                ];
            $this->journal_transaction_line_model->set($journal_transaction_id,$data);
    }
    

}
