<?php

/**
 * Description of Asset_payment
 *
 * @author Allan Jes
 */
class Asset_payment extends CI_Controller {

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

                      $this->helpers->activity_logs($_SESSION['id'],6,"Adding asset payment detail",$feedback['message'],NULL,$this->input->post('asset_id'));

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
        if($this->input->post('disposal_method')!='1'){
        $this->form_validation->set_rules("payment_id", "payment Mode", array("required"), array("required" => "%s must be selected"));
        $this->form_validation->set_rules("fund_source_account_id", "Fund Source Account", array("required"), array("required" => "%s must be selected"));
    }
        if($this->input->post('amount1')=='0'){
        $this->form_validation->set_rules("amount1", "Amount", array("required", "numeric"), array("required" => "%s must be entered"));
            }
        else if($this->input->post('amount2')=='0'){
        $this->form_validation->set_rules("amount2", "Amount", array("required", "numeric"), array("required" => "%s must be entered"));
            }

        else if($this->input->post('amount3')=='0'){
        $this->form_validation->set_rules("amount3", "Amount", array("required", "numeric"), array("required" => "%s must be entered"));
            }

         else if($this->input->post('amount4')=='0'){
        $this->form_validation->set_rules("amount4", "Amount", array("required", "numeric"), array("required" => "%s must be entered"));
            }

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {

                $disposal_data = $this->asset_payment_model->set2();
                
                if ($disposal_data!=FALSE) {
                    $this->update_fixed_asset_status($this->input->post('asset_id'),$this->input->post('status_id'));
                    $disposal_proceeds=$this->asset_disposal_preceeds();
                    $this->do_journal_selling_transaction($disposal_data,$disposal_proceeds);
                     
                    $feedback['success'] = true;
                    $feedback['message'] = "Asset  successfully disposed off";
                   // $feedback['asset_payment'] = $this->asset_payment_model->get("as.status_id =1 ");

                      $this->helpers->activity_logs($_SESSION['id'],8,"Asset disposal ",$feedback['message'],"#".$disposal_data['transaction_id'],"#".$disposal_data['transaction_no']);

                } else {
                    $feedback['message'] = "There was a problem saving asset disposal data";

                      $this->helpers->activity_logs($_SESSION['id'],8,"Editing disposal detail",$feedback['message'],NULL,$this->input->post('asset_id'));

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
    // function 
        public function asset_disposal_preceeds(){
 
              $asset_id=$this->input->post('asset_id');
              $disposal_id  =  $this->input->post('disposal_method');
              $depre_appre_id=(int)$this->input->post('depre_appre_id');

             $fixed_asset=$this->inventory_model->get($asset_id);
             $data['fixed_asset']=$fixed_asset;

             $asset_cost= $data['fixed_asset']['purchase_cost'];
            
            
             if($depre_appre_id==1){
              $book_value= ($asset_cost-$data['fixed_asset']['cumm_dep']);
             }
             else if($depre_appre_id==2){
              $book_value= ($asset_cost+$data['fixed_asset']['cumm_app']);
             }
       //senario :1,disposal at full depreciation;

            if(is_numeric($disposal_id) && $disposal_id==1){
              
            if(is_numeric($depre_appre_id) && $depre_appre_id==1){

                     $disposal_proceeds=array (
                    'purchase_cost'=>$asset_cost,
                    'cumm_dep'=>$data['fixed_asset']['cumm_dep'],
                    'expense_account_id'=>$data['fixed_asset']['expense_account_id'],
                    'depreciation_account_id'=>$data['fixed_asset']['depreciation_account_id'],
                    'depreciation_loss_account_id'=>$data['fixed_asset']['depreciation_loss_account_id'],
                    'depreciation_gain_account_id'=>$data['fixed_asset']['depreciation_gain_account_id'],
                    'depre_appre_id'=>$data['fixed_asset']['depre_appre_id'],
                    'fund_source_account_id'=>$this->input->post('fund_source_account_id'),
                    'asset_account_id'=>$data['fixed_asset']['asset_account_id'],
                    'asset_id'=>$asset_id,
                    'disposal_id'=>$disposal_id
                    );
            }
            else if(is_numeric($depre_appre_id) && $depre_appre_id==2){
                     $disposal_proceeds=array (
                    'purchase_cost'=>$asset_cost,
                    'cumm_app'=>$data['fixed_asset']['cumm_app'],
                    'income_account_id'=>$data['fixed_asset']['income_account_id'],
                    'appreciation_account_id'=>$data['fixed_asset']['appreciation_account_id'],
                    'appreciation_loss_account_id'=>$data['fixed_asset']['appreciation_loss_account_id'],
                    'appreciation_gain_account_id'=>$data['fixed_asset']['appreciation_gain_account_id'],
                    'depre_appre_id'=>$data['fixed_asset']['depre_appre_id'],
                    'fund_source_account_id'=>$this->input->post('fund_source_account_id'),
                    'asset_id'=>$asset_id,
                    'asset_account_id'=>$data['fixed_asset']['asset_account_id'],
                    'disposal_id'=>$disposal_id
                    );
            }
          }
       //disposal with loss either way(Depreciation or Appreciation)
              else if(is_numeric($disposal_id) && $disposal_id==2){
                     if($depre_appre_id==2){
                    $cash  = $this->input->post('amount4');
                         }
                         else{
                    $cash  = $this->input->post('amount2');
                         }

                    if(is_numeric($depre_appre_id) && $depre_appre_id==2){

                       $loss =($book_value-$cash);

                        $disposal_proceeds=array (
                        'purchase_cost'=>$asset_cost,
                        'cumm_app'=>$data['fixed_asset']['cumm_app'],
                        'income_account_id'=>$data['fixed_asset']['income_account_id'],
                        'appreciation_account_id'=>$data['fixed_asset']['appreciation_account_id'],
                        'appreciation_loss_account_id'=>$data['fixed_asset']['appreciation_loss_account_id'],
                        'appreciation_gain_account_id'=>$data['fixed_asset']['appreciation_gain_account_id'],
                        'depre_appre_id'=>$data['fixed_asset']['depre_appre_id'],
                        'fund_source_account_id'=>$this->input->post('fund_source_account_id'),
                        'asset_id'=>$asset_id,
                        'asset_account_id'=>$data['fixed_asset']['asset_account_id'],
                        'amount'=>$cash,
                        'loss'=>$loss,
                        'disposal_id'=>$disposal_id
                        );
                  }
                  elseif(is_numeric($depre_appre_id) && $depre_appre_id==1){
                     $loss =($book_value-$cash); 

                         $disposal_proceeds=array (
                        'purchase_cost'=>$asset_cost,
                        'cumm_dep'=>$data['fixed_asset']['cumm_dep'],
                        'expense_account_id'=>$data['fixed_asset']['expense_account_id'],
                        'depreciation_account_id'=>$data['fixed_asset']['depreciation_account_id'],
                        'depreciation_loss_account_id'=>$data['fixed_asset']['depreciation_loss_account_id'],
                        'depreciation_gain_account_id'=>$data['fixed_asset']['depreciation_gain_account_id'],
                        'fund_source_account_id'=>$this->input->post('fund_source_account_id'),
                        'depre_appre_id'=>$data['fixed_asset']['depre_appre_id'],
                        'asset_id'=>$asset_id,
                        'asset_account_id'=>$data['fixed_asset']['asset_account_id'],
                        'amount'=>$cash,
                        'loss'=>$loss,
                        'disposal_id'=>$disposal_id
                        );
                  }      
              }
                 //disposal with gain (Depre or Appre senario)

                else if(is_numeric($disposal_id) && $disposal_id==3){
                $cash  =  $this->input->post('amount3');

                if(is_numeric($depre_appre_id) && $depre_appre_id==2){
                  $gain =($cash-$book_value);

                      $disposal_proceeds= array (

                      'purchase_cost'=>$asset_cost,
                      'cumm_app'=>$data['fixed_asset']['cumm_app'],
                      'income_account_id'=>$data['fixed_asset']['income_account_id'],
                      'appreciation_account_id'=>$data['fixed_asset']['appreciation_account_id'],
                      'appreciation_loss_account_id'=>$data['fixed_asset']['appreciation_loss_account_id'],
                      'appreciation_gain_account_id'=>$data['fixed_asset']['appreciation_gain_account_id'],
                      'asset_account_id'=>$data['fixed_asset']['asset_account_id'],
                      'depre_appre_id'=>$data['fixed_asset']['depre_appre_id'],
                      'fund_source_account_id'=>$this->input->post('fund_source_account_id'),
                      'asset_id'=>$asset_id,
                      'amount'=>$cash,
                      'gain'=>$gain,
                      'disposal_id'=>$disposal_id
                      );
                }
                  else if($depre_appre_id==1){

                   $gain =($cash-$book_value); 
                       $disposal_proceeds=array (
                      'purchase_cost'=>$asset_cost,
                      'cumm_dep'=>$data['fixed_asset']['cumm_dep'],
                      'expense_account_id'=>$data['fixed_asset']['expense_account_id'],
                      'depreciation_account_id'=>$data['fixed_asset']['depreciation_account_id'],
                      'depreciation_loss_account_id'=>$data['fixed_asset']['depreciation_loss_account_id'],
                      'depreciation_gain_account_id'=>$data['fixed_asset']['depreciation_gain_account_id'],
                      'depre_appre_id'=>$data['fixed_asset']['depre_appre_id'],
                      'fund_source_account_id'=>$this->input->post('fund_source_account_id'),
                      'asset_id'=>$asset_id,
                      'asset_account_id'=>$data['fixed_asset']['asset_account_id'],
                      'amount'=>$cash,
                      'gain'=>$gain,
                      'disposal_id'=>$disposal_id
                      );
                  }
                }

              return $disposal_proceeds;
     }

    //do journal for disposal 
       public function do_journal_selling_transaction($transaction_data,$disposal_proceeds){
      
        $this->load->model('journal_transaction_model');
        $this->load->model('accounts_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('journal_transaction_line_model');
              // Disposal of an asset with a full depreciation
        if($disposal_proceeds['depre_appre_id']==1 && $disposal_proceeds['disposal_id']==1){
          
            $expense_account_id =$disposal_proceeds['expense_account_id'];
            $depreciation_account_id=$disposal_proceeds['depreciation_account_id'];
            $fund_source_account_id=$disposal_proceeds['fund_source_account_id'];
            $asset_account_id=$disposal_proceeds['asset_account_id'];
            $asset_id=$disposal_proceeds['asset_id'];
            $amount1=$disposal_proceeds['purchase_cost'];
            $amount2=$disposal_proceeds['cumm_dep'];

            $debit_or_credit1 = $this->accounts_model->get_normal_side($depreciation_account_id,false);
            $debit_or_credit2 = $this->accounts_model->get_normal_side($asset_account_id,true);

            $account_debit= $disposal_proceeds['asset_account_id'];
            $account_credit=$disposal_proceeds['depreciation_account_id'];
            
         }

             // Disposal with loss for a depreciated asset
          if($disposal_proceeds['depre_appre_id']==1 && $disposal_proceeds['disposal_id']==2){
            $fund_source_account_id=$disposal_proceeds['fund_source_account_id'];
            $expense_account_id =$disposal_proceeds['expense_account_id'];
            $depreciation_account_id=$disposal_proceeds['depreciation_account_id'];
            $depreciation_loss_account_id=$disposal_proceeds['depreciation_loss_account_id'];
            $depreciation_gain_account_id=$disposal_proceeds['depreciation_gain_account_id'];
            $asset_account_id=$disposal_proceeds['asset_account_id'];
            $amount1=$disposal_proceeds['purchase_cost'];
            $amount2=$disposal_proceeds['cumm_dep'];
            $amount_paid=$disposal_proceeds['amount'];
            $amount3=$disposal_proceeds['loss'];
            // clear the depreciation and asset account
            $debit_or_credit1 = $this->accounts_model->get_normal_side($depreciation_account_id, false);
            $debit_or_credit2 = $this->accounts_model->get_normal_side($asset_account_id,true);

            $account_credit=$depreciation_account_id;
            $account_debit= $asset_account_id;

            //Recording a loss 
            $debit_or_credit1_other = $this->accounts_model->get_normal_side($expense_account_id, false);
            $debit_or_credit2_other = $this->accounts_model->get_normal_side($fund_source_account_id, false);

            $account_credit_other=$disposal_proceeds['expense_account_id'];
            $account_debit_other= $disposal_proceeds['fund_source_account_id'];
         
         }
            // Disposal with gain for a depreciated asset
            if($disposal_proceeds['depre_appre_id']==1 && $disposal_proceeds['disposal_id']==3){
            $fund_source_account_id=$disposal_proceeds['fund_source_account_id'];
            $expense_account_id =$disposal_proceeds['expense_account_id'];
            $depreciation_account_id=$disposal_proceeds['depreciation_account_id'];
            $depreciation_loss_account_id=$disposal_proceeds['depreciation_loss_account_id'];
            $depreciation_gain_account_id=$disposal_proceeds['depreciation_gain_account_id'];
            $asset_account_id=$disposal_proceeds['asset_account_id'];
            $amount1=$disposal_proceeds['purchase_cost'];
            $amount2=$disposal_proceeds['cumm_dep'];
            $amount_paid=$disposal_proceeds['amount'];
            $amount3=$disposal_proceeds['gain'];

            $debit_or_credit1 = $this->accounts_model->get_normal_side($asset_account_id,false);
            $debit_or_credit2 = $this->accounts_model->get_normal_side($depreciation_account_id,true);

            $account_debit= $asset_account_id;
            $account_credit=$depreciation_account_id;
           

            $debit_or_credit1_other = $this->accounts_model->get_normal_side($fund_source_account_id,true);
            $debit_or_credit2_other = $this->accounts_model->get_normal_side($depreciation_gain_account_id,true);

            $account_credit_other=$depreciation_gain_account_id;
            $account_debit_other= $fund_source_account_id;
         
         }
             //  Disposal of an appreciated asset with a loss
          if($disposal_proceeds['depre_appre_id']==2 && $disposal_proceeds['disposal_id']==2){
           
            $fund_source_account_id=$disposal_proceeds['fund_source_account_id'];
            $income_account_id =$disposal_proceeds['income_account_id'];
            $appreciation_account_id=$disposal_proceeds['appreciation_account_id'];
            $appreciation_loss_account_id=$disposal_proceeds['appreciation_loss_account_id'];
            $appreciation_gain_account_id=$disposal_proceeds['appreciation_gain_account_id'];
            $asset_account_id=$disposal_proceeds['asset_account_id'];
            $amount1=$disposal_proceeds['purchase_cost'];
            $amount2=$disposal_proceeds['cumm_app'];
            $amount_paid=$disposal_proceeds['amount'];
            $amount3=$disposal_proceeds['loss'];
            
            $debit_or_credit1 = $this->accounts_model->get_normal_side($asset_account_id,true);
            $debit_or_credit2 = $this->accounts_model->get_normal_side($appreciation_account_id,true);

            $account_debit= $asset_account_id;
            $account_credit=$appreciation_account_id;
            

            $debit_or_credit1_other = $this->accounts_model->get_normal_side($appreciation_loss_account_id,false);
            $debit_or_credit2_other = $this->accounts_model->get_normal_side($fund_source_account_id,false);

            $account_credit_other=$appreciation_loss_account_id;
            $account_debit_other= $disposal_proceeds['fund_source_account_id'];
         
         }
             //  Disposing an assets with a gain
           if($disposal_proceeds['depre_appre_id']==2 && $disposal_proceeds['disposal_id']==3){

            $fund_source_account_id=$disposal_proceeds['fund_source_account_id'];
            $income_account_id =$disposal_proceeds['income_account_id'];
            $appreciation_account_id=$disposal_proceeds['appreciation_account_id'];
            $appreciation_loss_account_id=$disposal_proceeds['appreciation_loss_account_id'];
            $appreciation_gain_account_id=$disposal_proceeds['appreciation_gain_account_id'];
            $asset_account_id=$disposal_proceeds['asset_account_id'];
            $amount1=$disposal_proceeds['purchase_cost'];
            $amount2=$disposal_proceeds['cumm_app'];
            $amount_paid=$disposal_proceeds['amount'];
            $amount3=$disposal_proceeds['gain'];

            $debit_or_credit1 = $this->accounts_model->get_normal_side($asset_account_id, true);
            $debit_or_credit2 = $this->accounts_model->get_normal_side($appreciation_account_id, true);

            $account_debit=$asset_account_id;
            $account_credit=$appreciation_account_id;
           

            $debit_or_credit1_other = $this->accounts_model->get_normal_side($fund_source_account_id,true);
            $debit_or_credit2_other = $this->accounts_model->get_normal_side($appreciation_gain_account_id,true);

            $account_debit_other= $disposal_proceeds['fund_source_account_id'];
            $account_credit_other=$disposal_proceeds['appreciation_gain_account_id'];
           

         
         }

        $data2=[
            
            'transaction_date'=> $this->input->post('transaction_date'),
            'description'=> $this->input->post('narrative'),
            'ref_no'=> $transaction_data['transaction_no'],
            'ref_id'=> $transaction_data['transaction_id'],
            'status_id'=> 1,
            'journal_type_id'=> 29
            ];
            
      
        $journal_transaction_id = $this->journal_transaction_model->set($data2);
        unset($data2);
        //then we prepare the journal transaction lines
                if($disposal_proceeds['disposal_id']==1){
           
             $data = [
               
                    [
                        $debit_or_credit1=>$amount2,
                        'narrative'=> $this->input->post('transaction_date')." ".$this->input->post('narrative'),
                        'reference_no'=>$transaction_data['transaction_no'],
                        'reference_id'=>$transaction_data['transaction_id'],
                        'transaction_date'=>$this->input->post('transaction_date'),
                        'account_id'=> $account_credit,
                        'status_id'=> 1
                    ],
                    [
                        $debit_or_credit2=>$amount1,
                        'narrative'=> $this->input->post('transaction_date')." ".$this->input->post('narrative'),
                        'reference_no'=>$transaction_data['transaction_no'],
                        'reference_id'=>$transaction_data['transaction_id'],
                        'transaction_date'=>$this->input->post('transaction_date'),
                        'account_id'=> $account_debit,
                        'status_id'=> 1
                    ],
                
                ];
            }
            else{
                 $data = [
               
                    [
                   $debit_or_credit1=>$disposal_proceeds['disposal_id']==2||$disposal_proceeds['disposal_id']==3?$amount2:'',
                        'narrative'=> $this->input->post('transaction_date')." ".$this->input->post('narrative'),
                        'reference_no'=>$transaction_data['transaction_no'],
                        'reference_id'=>$transaction_data['transaction_id'],
                        'transaction_date'=>$this->input->post('transaction_date'),
                        'account_id'=> $account_credit,
                        'status_id'=> 1
                    ],
                    [
                        $debit_or_credit2=>$amount1,
                        'narrative'=> $this->input->post('transaction_date')." ".$this->input->post('narrative'),
                        'reference_no'=>$transaction_data['transaction_no'],
                        'reference_id'=>$transaction_data['transaction_id'],
                        'transaction_date'=>$this->input->post('transaction_date'),
                        'account_id'=> $account_debit,
                        'status_id'=> 1
                    ],
                     [
                        $debit_or_credit1_other=>$amount3,
                        'narrative'=> $this->input->post('transaction_date')." ".$this->input->post('narrative'),
                        'reference_no'=>$transaction_data['transaction_no'],
                        'reference_id'=>$transaction_data['transaction_id'],
                        'transaction_date'=>$this->input->post('transaction_date'),
                        'account_id'=> $account_credit_other,
                        'status_id'=> 1
                    ],
                    [
                        $debit_or_credit2_other=>$amount_paid,
                        'narrative'=> $this->input->post('transaction_date')." ".$this->input->post('narrative'),
                        'reference_no'=>$transaction_data['transaction_no'],
                        'reference_id'=>$transaction_data['transaction_id'],
                        'transaction_date'=>$this->input->post('transaction_date'),
                        'account_id'=> $account_debit_other,
                        'status_id'=> 1
                    ]

                
                ];

            }
            $this->journal_transaction_line_model->set($journal_transaction_id,$data);
            }
   //updates fixed assets table on selling off
     public function update_fixed_asset_status($asset_id,$status_id){ 
         $this->db->set('status_id',$status_id);
         $this->db->where('id',$asset_id);
         $this->db->update('fms_fixed_assets');
        
      }

   
}
