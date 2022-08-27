<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Investment controller
 @author Eric enhanced by Ambrose:
 */
class Investiment extends CI_controller{
	
 public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->data['privilege_list'] = $this->helpers->user_privileges(8, $this->session->userdata('staff_id'));
        $this->data['fiscal_list'] = $this->helpers->user_privileges(20, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module(8, $_SESSION['organisation_id']);
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['accounts_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
            $this->data['fiscal_privilege'] = array_column($this->data['fiscal_list'], "privilege_code");
        }
        $this->load->model('investiment_model');
        $this->load->model('miscellaneous_model');
        $this->load->model('accounts_model');
        $fiscal_year = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
            if(empty($fiscal_year)){
                redirect('dashboard');
            }else{
            $this->data['fiscal_year'] = array_merge($fiscal_year,['start_date2'=>date("d-m-Y", strtotime($fiscal_year['start_date'])),'end_date2'=>date("d-m-Y", strtotime($fiscal_year['end_date']))]);
            $this->data['lock_month_access'] = $this->helpers->org_access_module($module_id = 23, $_SESSION['organisation_id']);
                if(!empty($this->data['lock_month_access'])){
                    $this->data['active_month'] = $this->Fiscal_month_model->get_active_month();
                    if(empty($this->data['active_month'])){
                       redirect('dashboard');
                    }
                } 
            }
        }
	   
     public function jsonList() {
         $where = FALSE;
        if ($this->input->post('id') !== NULL) {
             $where = "it.status_id !=3";
        }
      
        $this->data['data'] = $this->investiment_model->get($where);
        //print_r($this->data['data']);die();
        echo json_encode($this->data);
    }
     public function jsonList2() {

        $where = FALSE;
        if ($this->input->post('id') !== NULL) {
            $where = "investment_id = " . $this->input->post('id');
             $where = "status_id !=3";
        }
        $this->data['data'] = $this->investiment_model->get2($where);

        echo json_encode($this->data);
    }

    public function index() {
        $this->load->model('accounts_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('staff_model');
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js");

        $neededcss = array("fieldset.css", "plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['account_list'] = $this->accounts_model->get();
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 5');
        $this->data['transaction_channels'] = $this->transactionChannel_model->get();
        $this->data["subcat_list"] = $this->accounts_model->get_subcat_list();
        $this->data['staff_list'] = $this->staff_model->get_registeredby("status_id=1");
        $this->data['title'] = $this->data['sub_title'] = "Investments";
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('inventory/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    
    //investment 
     public function create(){
      $this->load->library('form_validation');
      $this->form_validation->set_rules('type', 'Investment type', 'required');
      $this->form_validation->set_rules('investment_account_id', 'Investment Account', 'required');
      $this->form_validation->set_rules('income_account_id', 'Income Account', 'required');
      $this->form_validation->set_rules('expense_account_id', 'Expense Account Id', 'required');
      $this->form_validation->set_rules('transaction_date', 'Transaction date', 'required');
      $this->form_validation->set_rules('tenure', 'Tenure', 'required');

      $feedback['success'] = false;
      
        if($this->form_validation->run() === FALSE ){
        $feedback['message'] = validation_errors('<li>','</li>');
        
            }else{
                if($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))){ //editing exsting item
    
                  if($this->investiment_model->update()){
                    $feedback['success'] = true;
                    $feedback['message'] = "Investment updated successfully";
                  }else{
                    $feedback['message'] = "Investiment  could not be updated";
                  }
                }else{
                  //adding a new user
                  $return_id = $this->investiment_model->set();
                  if(is_numeric($return_id)){
                    //$this->investiment_member_model->set($return_id);
                    $feedback['success'] = true;
                    $feedback['message'] = "Investment submitted successfully";

                  }else{
                    $feedback['message'] = "There was a problem saving the investiment, please contact IT support";

                  }
                }
            }
        echo json_encode($feedback);
    }
     public function create2(){
      $this->load->library('form_validation');
      $this->form_validation->set_rules('transaction_type_id', 'Transaction Type', 'required');
      $this->form_validation->set_rules('transaction_date', 'Transaction Date', 'required');
      $this->form_validation->set_rules('amount', 'Amount', 'required');
      $this->form_validation->set_rules('payment_mode', 'Payment mode', 'required');
      $this->form_validation->set_rules('account_no_id', 'Account No ID', 'required');
      $this->form_validation->set_rules('description', 'Narative', 'required');
       
      $feedback['success'] = false;
      
        if($this->form_validation->run() === FALSE ){
        $feedback['message'] = validation_errors('<li>','</li>');
        
            } 
             if($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))){
              if($this->investiment_model->update2()){
                    $feedback['success'] = true;
                    $feedback['message'] = "Investment updated successfully";
                  }
                  else{
                    $feedback['message'] = "Investiment  could not be updated";
                  }
                }
                

             else {
                $investment_data = $this->investiment_model->set2();

                //print_r($investment_data );die();

                if ($investment_data!=FALSE) {
                    $this->do_journal_investment_transaction($investment_data);
                    $feedback['success'] = true;
                    $feedback['message'] = "Investment details successfully saved";
                   
                      $this->helpers->activity_logs($_SESSION['id'],6,"Editing investment detail",$feedback['message'],NULL,$this->input->post('transaction_no'));

                } else {
                    $feedback['message'] = "There was a problem saving investment data";

                      $this->helpers->activity_logs($_SESSION['id'],6,"Editing asset detail",$feedback['message'],NULL);
            }

            
        }
        echo json_encode($feedback);
      }
    

  public function transactions($id){

       // $this->load->model('accounts_model');
        $this->load->model('investiment_model'); 
        $this->load->model('transactionChannel_model');
        $this->load->model('user_income_type_model');
        $this->load->model('user_expense_type_model'); 
        
        $neededcss = array("fieldset.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        
        $this->data['investment_data'] = $this->investiment_model->get2();
         
        $this->data['account_list'] = $this->accounts_model->get();
        $this->data["subcat_list"] = $this->accounts_model->get_subcat_list();
        $this->data['income_items'] = $this->user_income_type_model->get();
        $this->data['expense_items'] = $this->user_expense_type_model->get();
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 5');
        $this->data['transaction_channels'] = $this->transactionChannel_model->get();
        $this->data['id']=$id;
        
        $this->data['title'] = $this->data['sub_title'] = 'Transaction';
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('inventory/investment/transactions/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    //investment journal 
     private function do_journal_investment_transaction($transaction_data){

        $this->load->model('journal_transaction_model');
        $this->load->model('accounts_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('journal_transaction_line_model');
        $data = [
            'transaction_date'=> $this->input->post('transaction_date'),
            'description'=> $this->input->post('description'),
            'ref_no'=> $transaction_data['transaction_no'],
            'ref_id'=> $transaction_data['transaction_id'],
            'status_id'=> 1,
            'journal_type_id'=> 29
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        //then we prepare the journal transaction lines
               // Scenario 1: Recording  money deposited to the investment account 
               if($this->input->post('transaction_type_id')==1){
                 $debit_or_credit1 = $this->accounts_model->get_normal_side($this->input->post('fund_source_account_id'), true);
                 $debit_or_credit2 = $this->accounts_model->get_normal_side($this->input->post('investment_account_id'), false);

                 $account_credit=$this->input->post('fund_source_account_id');
                 $account_debit=$this->input->post('investment_account_id');
                 
               }
               //Recording gain realized from the investment
              if($this->input->post('transaction_type_id')==2){
                 $debit_or_credit1 = $this->accounts_model->get_normal_side($this->input->post('investment_account_id'), false);
                 $debit_or_credit2 = $this->accounts_model->get_normal_side($this->input->post('income_account_id'), false);


                 $account_credit=$this->input->post('income_account_id');
                 $account_debit=$this->input->post('investment_account_id');

               }
               //Recording a loss realized from the investment.
              if($this->input->post('transaction_type_id')==3){
                 $debit_or_credit1 = $this->accounts_model->get_normal_side($this->input->post('investment_account_id'), true);
                 $debit_or_credit2 = $this->accounts_model->get_normal_side($this->input->post('expense_account_id'), false);

                  $account_credit=$this->input->post('investment_account_id');
                  $account_debit=$this->input->post('expense_account_id');
                
               }
               // Recording a removal of amount invested
                 if($this->input->post('transaction_type_id')==4){
                  $debit_or_credit1 = $this->accounts_model->get_normal_side($this->input->post('fund_source_account_id'), false);
                  $debit_or_credit2 = $this->accounts_model->get_normal_side($this->input->post('investment_account_id'), true);

                  $account_credit=$this->input->post('investment_account_id');
                  $account_debit=$this->input->post('fund_source_account_id');

                }

             $data = [
                    [
                        $debit_or_credit1=>$this->input->post('transaction_type_id')==4?$this->input->post('amount2'):$this->input->post('amount1'),
                        'narrative'=> $this->input->post('transaction_date')." ".$this->input->post('narrative'),
                        'reference_no'=>$transaction_data['transaction_no'],
                        'reference_id'=>$transaction_data['transaction_id'],
                        'transaction_date'=>$this->input->post('transaction_date'),
                        'account_id'=> $account_credit,
                        'status_id'=> 1
                    ],
                    [
                        $debit_or_credit2=>$this->input->post('transaction_type_id')==4?$this->input->post('amount2'):$this->input->post('amount1'),
                        'narrative'=> $this->input->post('transaction_date')." ".$this->input->post('narrative'),
                        'reference_no'=>$transaction_data['transaction_no'],
                        'reference_id'=>$transaction_data['transaction_id'],
                        'transaction_date'=>$this->input->post('transaction_date'),
                        'account_id'=> $account_debit,
                        'status_id'=> 1
                    ]
                ];
            $this->journal_transaction_line_model->set($journal_transaction_id,$data);
    }

      public function reverse_transaction(){
        $this->load->model('journal_transaction_model');
        $this->load->model('investiment_model');
        
        $this->form_validation->set_rules("reverse_msg", "Reason", array("required"), array("required" => "%s must be entered"));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->investiment_model->reverse()) {
                    $journal_type_id = $this->input->post('journal_type_id');
                    $ref_no = $this->input->post('transaction_no');
                     
                    $this->journal_transaction_model->reverse($_POST['id'],$ref_no,"(29)");
                    
                    $feedback['success'] = true;
                    $feedback['message'] = "Transaction successfully cancled";
            $this->helpers->activity_logs($_SESSION['id'],11,"reversing Transaction",$feedback['message']." -# ". $this->input->post('transaction_no'),NULL,$this->input->post('transaction_no'));
                } else {
                    $feedback['message'] = "There was a problem reversing the transaction";
                 $this->helpers->activity_logs($_SESSION['id'],11,"reversing Transaction",$feedback['message']." -# ". $this->input->post('transaction_no'),NULL,$this->input->post('transaction_no'));
                }
            } 
        }
        echo json_encode($feedback);
      }

}