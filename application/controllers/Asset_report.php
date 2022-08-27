<?php

/**
 * Description of Asset_payment
 *
 * @author Allan Jes
 */
class Asset_report extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('asset_payment_model');
        $this->load->model('inventory_model');
        $this->load->library(array("form_validation", "helpers"));
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 8, $this->session->userdata('staff_id'));
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['privileges'] = array_column($this->data['privilege_list'], "privilege_code");
        }
    }

    public function jsonList() {
        $data['data'] = $this->asset_payment_model->get();
        echo json_encode($data);
    }

    public function create() {
        $this->form_validation->set_rules("transaction_date", "Date recorded", array("required"), array("required" => "%s must be entered/selected"));
        $this->form_validation->set_rules("narrative", "Narrative", array("required"), array("required" => "%s must be entered"));
        $this->form_validation->set_rules("asset_id", "Asset", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("asset_account_id", "Asset account", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("payment_id", "payment Mode", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("fund_source_account_id", "Fund Source Account", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("amount", "Amount", array("required", "numeric"), array("required" => "%s must be entered"));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
                $payment_data = $this->asset_payment_model->set();
                if ($payment_data!=FALSE) {
                    $this->do_journal_transaction($payment_data);
                    $feedback['success'] = true;
                    $feedback['message'] = "Asset payment details successfully saved";
                    $feedback['asset_payment'] = $this->asset_payment_model->get("as.status_id =1 AND as.transaction_type_id = 2");

                      $this->helpers->activity_logs($_SESSION['id'],6,"Editing asset payment detail",$feedback['message'],NULL,$this->input->post('asset_id'));

                } else {
                    $feedback['message'] = "There was a problem saving asset payment data";

                      $this->helpers->activity_logs($_SESSION['id'],6,"Editing asset detail",$feedback['message'],NULL,$this->input->post('asset_id'));

                }
        }
        echo json_encode($feedback);
    }
    //creat 2 for buying 
     public function create2() {
        $this->form_validation->set_rules("transaction_date", "Date recorded", array("required"), array("required" => "%s must be entered/selected"));
        $this->form_validation->set_rules("narrative", "Narrative", array("required"), array("required" => "%s must be entered"));
        $this->form_validation->set_rules("asset_id", "Asset", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("asset_account_id", "Asset account", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("payment_id", "payment Mode", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("fund_source_account_id", "Fund Source Account", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("amount", "Amount", array("required", "numeric"), array("required" => "%s must be entered"));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
                $selling_data = $this->asset_payment_model->set2();
                if ($selling_data!=FALSE) {
                    $this->do_journal_selling_transaction($selling_data);
                    $feedback['success'] = true;
                    $feedback['message'] = "Asset selling details successfully saved";
                   // $feedback['asset_payment'] = $this->asset_payment_model->get("as.status_id =1 ");

                      $this->helpers->activity_logs($_SESSION['id'],6,"Asset selling ",$feedback['message'],NULL,$this->input->post('asset_id'));

                } else {
                    $feedback['message'] = "There was a problem saving asset selling data";

                      $this->helpers->activity_logs($_SESSION['id'],6,"Editing selling detail",$feedback['message'],NULL,$this->input->post('asset_id'));

                }
        }
        echo json_encode($feedback);
    }


    public function edit_transaction() {
        $this->form_validation->set_rules("transaction_date", "Date recorded", array("required"), array("required" => "%s must be entered/selected"));
        $this->form_validation->set_rules("narrative", "Narrative", array("required"), array("required" => "%s must be entered"));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->asset_payment_model->update()) {
                    // CALL FUNCTIONS FROM INVENTORY MODEL
                    $journal =$this->inventory_model->get_journal_transaction();
                    $this->inventory_model->edit_journal_transaction($journal['id']);
                    
                    $feedback['success'] = true;
                    $feedback['message'] = "Payment details successfully updated"; 

                   $this->helpers->activity_logs($_SESSION['id'],8,"Updating asset payment",$feedback['message']." # ". $this->input->post('transaction_no'),NULL,$this->input->post('transaction_no'));

                } else {
                    $feedback['message'] = "There was a problem updating Payment transaction";

                     $this->helpers->activity_logs($_SESSION['id'],8,"Payment asset detail",$feedback['message'],NULL, "");
                }
            } 
        }
        echo json_encode($feedback);
    }
    public function reverse_transaction() {
        $this->load->model('journal_transaction_model');
        $this->form_validation->set_rules("reverse_msg", "Reason", array("required"), array("required" => "%s must be entered"));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                $ref_no = $this->input->post('transaction_no');

                if ($this->asset_payment_model->reverse()) {
                    // CALL FUNCTIONS FROM INVENTORY MODEL
                    $journal_type_id = $this->input->post('journal_type_id');
                    $this->journal_transaction_model->reverse(false,$ref_no,"(29)");
                    
                    $feedback['success'] = true;
                    $feedback['message'] = "Payment transaction successfully cancled";

                     $this->helpers->activity_logs($_SESSION['id'],8,"Payment  income detail",$feedback['message'],NULL, $ref_no);

                } else {
                    $feedback['message'] = "There was a problem reversing the transaction";
                    
                      $this->helpers->activity_logs($_SESSION['id'],8,"Payment  income detail",$feedback['message'],NULL, $ref_no);
                }
            } 
        }
        echo json_encode($feedback);
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
               if($this->input->post('payment_id')==3){
               $debit_or_credit2 = $this->accounts_model->get_normal_side($this->input->post('fund_source_account_id'), false);
               } else {
                $debit_or_credit2 = $this->accounts_model->get_normal_side($this->input->post('fund_source_account_id'), true);
               }
                $debit_or_credit1 = $this->accounts_model->get_normal_side($this->input->post('asset_account_id'), false);
                $data = [
                    [
                        $debit_or_credit1=>$this->input->post('amount'),
                        'narrative'=> $this->input->post('transaction_date')." ".$this->input->post('narrative'),
                        'reference_no'=>$transaction_data['transaction_no'],
                        'reference_id'=>$transaction_data['transaction_id'],
                        'transaction_date'=>$this->input->post('transaction_date'),
                        'account_id'=> $this->input->post('asset_account_id'),
                        'status_id'=> 1
                    ],
                    [
                        $debit_or_credit2=> $this->input->post('amount'),
                        'narrative'=> $this->input->post('transaction_date')." ".$this->input->post('narrative'),
                        'reference_no'=>$transaction_data['transaction_no'],
                        'reference_id'=>$transaction_data['transaction_id'],
                        'transaction_date'=>$this->input->post('transaction_date'),
                        'account_id'=> $this->input->post('fund_source_account_id'),
                        'status_id'=> 1
                    ]
                ];
            $this->journal_transaction_line_model->set($journal_transaction_id,$data);
    }

    //do journal after selling 
     private function do_journal_selling_transaction($transaction_data){
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
               if($this->input->post('payment_id')==3){
               $debit_or_credit2 = $this->accounts_model->get_normal_side($this->input->post('fund_source_account_id'), false);
               } else {
                $debit_or_credit2 = $this->accounts_model->get_normal_side($this->input->post('fund_source_account_id'), false);
               }
                $debit_or_credit1 = $this->accounts_model->get_normal_side($this->input->post('asset_account_id'), true);
                $data = [
                    [
                        $debit_or_credit1=>$this->input->post('amount'),
                        'narrative'=> $this->input->post('transaction_date')." ".$this->input->post('narrative'),
                        'reference_no'=>$transaction_data['transaction_no'],
                        'reference_id'=>$transaction_data['transaction_id'],
                        'transaction_date'=>$this->input->post('transaction_date'),
                        'account_id'=> $this->input->post('asset_account_id'),
                        'status_id'=> 1
                    ],
                    [
                        $debit_or_credit2=> $this->input->post('amount'),
                        'narrative'=> $this->input->post('transaction_date')." ".$this->input->post('narrative'),
                        'reference_no'=>$transaction_data['transaction_no'],
                        'reference_id'=>$transaction_data['transaction_id'],
                        'transaction_date'=>$this->input->post('transaction_date'),
                        'account_id'=> $this->input->post('fund_source_account_id'),
                        'status_id'=> 1
                    ]
                ];
            $this->journal_transaction_line_model->set($journal_transaction_id,$data);
    }



}
