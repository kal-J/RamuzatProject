<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Savings extends CI_Controller {

    protected $mm_channel_data;
    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("Savings_account_model");
        $this->load->model("DepositProduct_model");
        $this->load->model("Savings_product_fee_model");
        $this->load->model("Staff_model");
        $this->load->model("Organisation_format_model");
        $this->load->model("Member_model");
        $this->load->model("Transaction_model");
        $this->load->model("TransactionChannel_model");
        $this->load->model("Loan_guarantor_model");
        $this->load->model("payment_model");
        $this->load->model("Group_member_model");
        $this->load->model("payment_engine_model");
        $this->load->library(array("form_validation", "helpers"));

        $this->mm_channel_data=$this->payment_engine_model->get_requirement(1);
       $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
       $data['module']=$this->organisation_model->get_module_access(6,$this->session->userdata('organisation_id'));
       if(empty($data['module'])){
            redirect('my404');
       }
    }
    public function index() {
        $this->data['acc_id'] = "";    //used to hide transaction tab
        $this->data['switch_deposit_modal'] = 'oustide';
        $this->data['tchannel'] = $this->TransactionChannel_model->get();
        $this->data['products'] = $this->DepositProduct_model->get("sp.status_id=1");
        $this->data['organisation_format'] = $this->Organisation_format_model->get_formats();
        //$this->data['organisation'] = $this->Organisation_format_model->get_account_format();
        $this->data['sorted_clients'] = $this->Savings_account_model->get_clients('status_id=1');

        if (isset($_POST['requestStatusCode'])) {
           $this->payment_model->update();

           if ($_POST['requestStatusCode'] ==178) {    
                $deposit_data['account_no_id']=$_POST['accountNumber'];           
                $deposit_data['transaction_channel_id']=$this->mm_channel_data['transaction_channel_id'];
                $deposit_data['amount']=$this->input->post('amountPaid');
                $deposit_data['narrative']='Client deposit using mobile money platform';
                $deposit_data['transaction_type_id']=2;
                $deposit_data['group_member_id']=NULL;
                //product_id
                $savings_account = $this->Savings_account_model->get($_POST['accountNumber']);
                $charges= $this->Savings_product_fee_model->get(array('s.saving_product_id' => $savings_account['deposit_Product_id'], 'sf.chargetrigger_id' => '6', 'sf.status_id' => '1', 's.status_id' => '1'));


                $response=$this->Transaction_model->mm_set($deposit_data,$charges);

               if (is_array($response)) {
                  $this->deposit_journal_transaction($response);
               }
           }
        }

        $this->data['title'] = $this->data['sub_title'] = 'Savings Accounts';
        $this->data['payment_engine'] = $this->payment_engine_model->get($_SESSION['organisation_id']);
        $this->data['payment_engine_requirements'] = $this->mm_channel_data;
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css","plugins/daterangepicker/daterangepicker-bs3.css");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->title = $this->data['title'];
        // Load a view in the content partial
        $this->template->content->view('client/savings/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function jsonList() {//used for fetching union of members and groups
        $data['data'] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id IN(5,7,12,17,18)');
        echo json_encode($data);
    }

    public function jsonList2() {
        $data['accounts_data'] = $this->Savings_account_model->get();
        echo json_encode($data);
    }
    public function jsonList_member() {  //used for member in case you access through clients->member
        $data['data'] = $this->Loan_guarantor_model->get_guarantor_savings('client_type='.$this->input->post('client_type').' AND j.state_id=' . $this->input->post('state_id'));
        echo json_encode($data);
    }
    public function jsonList_group() {   //used for groups in case you access through clients->group
        $data['data'] = $this->Loan_guarantor_model->get_guarantor_savings_group('client_type='.$this->input->post('client_type').' AND j.state_id=' . $this->input->post('state_id'));
        echo json_encode($data);
    }

    public function view($acc_id) {
        $this->data['acc_id'] = $acc_id;    //used for the share tab

        $this->data['organisation_format'] = $this->Organisation_format_model->get_formats();
        // $this->data['organisation'] = $this->Organisation_format_model->get_account_format();
        $this->data['tchannel'] = $this->TransactionChannel_model->get();
        $this->data['selected_account'] = $this->Loan_guarantor_model->get_guarantor_savings2("(j.state_id = 5 OR j.state_id = 7 OR j.state_id = 12 OR j.state_id = 17 OR j.state_id = 18)", $acc_id);

        //fetch withdraw fees   =================================================
        $this->data['withdraw_fees'] = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $this->data['selected_account']['deposit_Product_id'], 'sf.chargetrigger_id' => '3')); //withdraw
        $this->data['deposit_fees'] = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $this->data['selected_account']['deposit_Product_id'], 'sf.chargetrigger_id' => '4'));
        if (empty($this->data['selected_account'])) {
            show_404();
        }
        if(intval($this->data['selected_account']['client_type'])==2){
            $this->data['group_members'] = $this->Group_member_model->get_group_member_savings('g.id='.$this->data['selected_account']['member_id'], $acc_id);  
       }
      
        $available_to_filter = "availableto IN (" . ($this->data['selected_account']['client_type']) . ",3)";
        $this->data['products'] = $this->DepositProduct_model->get($available_to_filter);
        //fetch withdraw fees   =================================================
        $this->data['withdraw_fees'] = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $this->data['selected_account']['deposit_Product_id'], 'sf.chargetrigger_id' => '3')); //withdraw
        $this->data['deposit_fees'] = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $this->data['selected_account']['deposit_Product_id'], 'sf.chargetrigger_id' => '4'));
        //$this->load->model('staff_model');

        $this->data['title'] =  "Account No: (  ".$this->data['selected_account']['account_no']."  )";
        $this->data['sub_title'] = $this->data['selected_account']['account_no'];
        $this->data['modalTitle'] = "Edit Savings Form";

        $neededjs = array("plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js","plugins/select2/select2.full.min.js");
        $neededcss = array("plugins/select2/select2.min.css","plugins/daterangepicker/daterangepicker-bs3.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->title = $this->data['title'];

        $this->template->content->view('client/savings/view', $this->data);
        // Publish the template
        $this->template->publish();
    }
    
    function change_status() {

        $response['success'] = FALSE;
        if (($response['success'] = $this->Savings_account_model->change_status()) === true) {
            $response['message'] = " Savings account deleted";
        }


        echo json_encode($response);
    }

    function change_state() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to modify this record";
        $response['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        $this->form_validation->set_rules('account_id', 'Account', 'required');
        $this->form_validation->set_rules('state_id', 'State', 'required');
        $this->form_validation->set_rules('comment', 'Comment', 'required|trim|min_length[5]');
        if ($this->form_validation->run() === true) {
            if (($response['success'] = $this->Savings_account_model->change_state()) === true) {
                $response['message'] = " Status Changed";
                //$response['accounts'] = $this->Savings_account_model->get($_POST['account_id']);
                $response['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings2('1=1', $_POST['account_id']);
            }
        } else {
            $response['message'] = validation_errors();
        }

        // }
        echo json_encode($response);
    }

    public function check_channel_balance(){
        $account_id = $this->TransactionChannel_model->get($this->input->post('transaction_channel_id'));
        $response = ['success'=>$this->channel_balance($account_id['linked_account_id'])];
        if (!$response['success']) {
            echo json_encode($response['success']);
        }else{
            echo json_encode($response['success']);  
        }
    }
    public function channel_balance($account_id = false){
        $amount =$this->input->post('amount');
        $message =$this->helpers->account_balance($account_id,$amount);
        return $message;
    }
    public function deposit_fees() {//Deposit feeses for mobile money deposits
        $this->data['deposit_fees'] = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $this->input->post('new_product_id'), 'sf.chargetrigger_id' => '6', 'sf.status_id' => '1', 's.status_id' => '1'));
        $response['deposit_fees'] = $this->data['deposit_fees'];
        echo json_encode($response);
    }

    public function withdraw_fees() {
        //fetch withdraw fees  
        $this->data['withdraw_fees'] = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $this->input->post('new_product_id'), 'sf.chargetrigger_id' => '3'));
        $response['withdraw_fees'] = $this->data['withdraw_fees'];
        echo json_encode($response);
    }
    public function transfer_fees() {
        //fetch transfer_fees  
        $this->data['transfer_fees'] = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $this->input->post('new_product_id'), 'sf.chargetrigger_id' => '5'));
        $response['transfer_fees'] = $this->data['transfer_fees'];
        echo json_encode($response);
    }

    public function get_savings_accounts() {
        //fetch savings_accounts  
        $this->data['savings_accounts'] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7 AND a.id!='.$this->input->post('account_no_id'));
        $response['savings_accounts'] = $this->data['savings_accounts'];
        echo json_encode($response);
    }

    public  function AcStatement( $acc_id, $start_date,$end_date){
        $this->load->model('branch_model');
        $where = "  account_no_id = " . $acc_id ." AND transaction_date BETWEEN '" . ($start_date) . "' AND '" . ($end_date) . "'";
        $this->load->helper('pdf_helper');
        $data['start_date']=$start_date;
        $data['end_date']=$end_date;
        $data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = "Loan disbursed details";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 10;
        $data['selected_account'] = $this->Loan_guarantor_model->get_guarantor_savings2("(j.state_id = 5 OR j.state_id = 7 OR j.state_id = 12 OR j.state_id = 17 OR j.state_id = 18)", $acc_id);

        $data['transactions'] = $this->Transaction_model->get($where);
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $data['the_page_data'] = $this->load->view('savings_account/pdf_account_statement', $data, TRUE);
        $this->load->view('includes/pdf_template', $data);
    }

    private function deposit_journal_transaction($transaction_data){
        $this->load->model('journal_transaction_model');
        $date=date('d-m-Y');
        $savings_account = $this->Savings_account_model->get($this->input->post('accountNumber'));
        $total_charges=round($this->compute_charge($savings_account['deposit_Product_id'],$this->input->post('amountPaid')));
        $deposited_amount=round($this->input->post('amountPaid'),2);
        $deposit_amount=$deposited_amount-$total_charges;

        if (!empty($total_charges) && $total_charges >0) {
            $this->de_charges_journal_transaction($transaction_data,$savings_account['deposit_Product_id'],$this->input->post('amountPaid'));
        }
        //then we prepare the journal transaction lines
        if(!empty($savings_account)){
            $this->load->model('accounts_model');
            $this->load->model('journal_transaction_line_model');

        $data = [
            'transaction_date'=> $date,
            'description'=> 'Mobile money deposit using the system',
            'ref_no'=> $transaction_data['transaction_no'],
            'ref_id'=>  $transaction_data['transaction_id'],
            'status_id'=> 1,
            'journal_type_id'=> 7
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
            
        $transaction_channel = $this->TransactionChannel_model->get($this->mm_channel_data['transaction_channel_id']);
        $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);

        $debit_or_credit1 =  $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id']);
        $debit_or_credit2= $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);

            //if deposit amount has been received
            if ($deposit_amount !=null && !empty($deposit_amount) && $deposit_amount !='0') {
              $data[0] =[
                    $debit_or_credit1=> $deposit_amount,
                    'narrative'=> "Deposit transaction made on ".$date,
                    'account_id'=>$savings_product_details['savings_liability_account_id'],
                    'status_id'=> 1
                ];
                $data[1] =[
                    $debit_or_credit2=> $deposit_amount,
                    'narrative'=> "Deposit transaction made on ".$date,
                    'account_id'=> $transaction_channel['linked_account_id'],
                    'status_id'=> 1
                ];
            $this->journal_transaction_line_model->set($journal_transaction_id,$data);
            }//end of the if
        }
    }

    private function compute_charge($amount,$product_id){
        $mm_deposit_fees= $this->Savings_product_fee_model->get(array('s.saving_product_id' => $product_id, 'sf.chargetrigger_id' => '6', 'sf.status_id' => '1', 's.status_id' => '1'));

        $total_charge=0;
        foreach ($mm_deposit_fees as $key => $value) {
            if ($value['cal_method_id']==1) {
               $charge=(($value['amount'] * $amount)/100);
               $total_charge +=$charge;
            }else{//if ($value['cal_method_id']==2) 
                $charge=$value['amount'];
                $total_charge +=$charge;
            }
        }

        return $total_charge;
    }

    private function de_charges_journal_transaction($transaction_data,$product_id,$amount){
        $this->load->model('journal_transaction_model');
        $date=date('d-m-Y');
        $charges= $this->Savings_product_fee_model->get(array('s.saving_product_id' => $product_id, 'sf.chargetrigger_id' => '6', 'sf.status_id' => '1', 's.status_id' => '1'));
        //then we prepare the journal transaction lines
        if(!empty($charges)){
            $this->load->model('accounts_model');
            $this->load->model('journal_transaction_line_model');

        $data = [
            'transaction_date'=> $date,
            'description'=> 'Charge deducted from client deposit deposited using Mobile money',
            'ref_no'=> $transaction_data['transaction_no'],
            'ref_id'=>  $transaction_data['transaction_id'],
            'status_id'=> 1,
            'journal_type_id'=> 10
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
            
        $transaction_channel = $this->TransactionChannel_model->get($this->mm_channel_data['transaction_channel_id']);
        $debit_or_credit2= $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);

            //if charges have been received
            foreach ($charges as $key => $value) {
                $savings_product_fee_details = $this->Savings_product_fee_model->get_accounts($value['id']);

                $debit_or_credit1 =  $this->accounts_model->get_normal_side($savings_product_fee_details['savings_fees_income_account_id']);
              $data[0] =[
                    $debit_or_credit1=> ($value['cal_method_id']==1)?(($value['amount'] * $amount)/100):$value['amount'],
                    'narrative'=> "Charge on mobile money deposit transaction made on ".$date,
                    'account_id'=>$savings_product_fee_details['savings_fees_income_account_id'],
                    'status_id'=> 1
                ];
                $data[1] =[
                    $debit_or_credit2=> ($value['cal_method_id']==1)?(($value['amount'] * $amount)/100):$value['amount'],
                    'narrative'=> "Charge on mobile money deposit transaction made on ".$date,
                    'account_id'=> $transaction_channel['linked_account_id'],
                    'status_id'=> 1
                ];
                $this->journal_transaction_line_model->set($journal_transaction_id,$data);
            
            }//end of foreach
        }
    }

    public function get_withdraw_requestsToJson() {
        $this->load->model('Withdraw_requests_model');
        $data['data'] = $this->Withdraw_requests_model->get_requests();
        echo json_encode($data);
    }

    public function withdraw_request() {
        $this->load->model('Withdraw_requests_model');
        $response['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        $this->form_validation->set_rules('account_no_id', 'Account Number id', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        $this->form_validation->set_rules('reason', 'Reason', 'required|trim|min_length[5]');
        $this->form_validation->set_rules('member_id', 'Member id', 'required');
        if ($this->form_validation->run() === true) {
            $this->Withdraw_requests_model->save_request();
        } else {
            $response['message'] = validation_errors();
            echo json_encode($response);
        }
    }
}
