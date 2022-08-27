<?php

/**
 * This class helps to create the mode for the database operations
 *@Eric modified by @mbrose
 */
class Investiment_model extends CI_Model {

    public function __construct() {
        parent :: __construct();
        $this->load->database();

        // total deposit 
        $this->total_deposit="(SELECT sum(credit) as amount,it.investment_id FROM fms_investment_transaction it LEFT JOIN fms_investment i ON(i.id=it.investment_id) WHERE it.transaction_type_id=1 AND it.status_id !=3 GROUP BY type) d";
        //loss
         $this->loss="(SELECT sum(credit) as loss,it.investment_id FROM fms_investment_transaction it LEFT JOIN fms_investment i ON(i.id=it.investment_id) WHERE it.transaction_type_id=3 AND it.status_id !=3 GROUP BY type) cumm_loss";

          $this->gain="(SELECT sum(credit) as gain,it.investment_id FROM fms_investment_transaction it LEFT JOIN fms_investment i ON(i.id=it.investment_id) WHERE it.transaction_type_id=2 AND it.status_id !=3 GROUP BY type) cumm_gain";

          $this->withdrawals="(SELECT SUM(IFNULL(debit,0)) as withdraw,it.investment_id FROM fms_investment_transaction it LEFT JOIN fms_investment i ON(i.id=it.investment_id) WHERE it.transaction_type_id=4 AND it.status_id !=3 GROUP BY type) cumm_withdrawal";
    }

    /**
     * This method displays investiment data from the database
     */
   
    // get all my investments 
     public function get($filter = FALSE) {
        $this->db->select("i.id,((ifnull(d.amount,0))-(ifnull(cumm_withdrawal.withdraw,0)))as amount,cumm_gain.gain,cumm_loss.loss,cumm_withdrawal.withdraw,
        i.date_created,tenure,i.status_id,expense_account_id,account_no_id,investment_account_id,income_account_id,type,i.description,inpac.account_code income_account_code,"
         . "at.account_name, at.account_code,"
                . "expac.account_name expense_account_name, expac.account_code expense_account_code,"
                . "appac.account_name appreciation_account_name, appac.account_code appreciation_account_code,inpac.account_name income_account_name, inpac.account_code income_account_code"
    );
         $this->db->distinct('type');
         $this->db->from('fms_investment i');
         $this->db->group_by('type');
         $this->db->join("fms_investment_transaction it", "i.id=it.investment_id","LEFT");
         $this->db->join("accounts_chart at", "at.id=i.investment_account_id","LEFT");
         $this->db->join("accounts_chart expac", "`expac`.`id`=i.expense_account_id","LEFT");
         $this->db->join("accounts_chart appac", "`appac`.`id`=i.investment_account_id","LEFT");
         $this->db->join("accounts_chart inpac", "`inpac`.`id`=i.income_account_id","LEFT");
         $this->db->join("$this->total_deposit","`i`.`id`=d.investment_id","LEFT");
         $this->db->join("$this->loss","`i`.`id`=cumm_loss.investment_id","LEFT");
         $this->db->join("$this->gain","`i`.`id`=cumm_gain.investment_id","LEFT");
         $this->db->join("$this->withdrawals","`i`.`id`=cumm_withdrawal.investment_id","LEFT");

        if ($this->input->post('status_id') !='') {
            $this->db->where('it.status_id !=',3);
        } 
        if ($this->input->post('id') !='') {
            $this->db->where('i.status_id',$this->input->post('status_id'));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('fms_investment.status_id!=',$filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
    //transactions
    public function get2($filter = FALSE) {

         $this->db->select("*");
         $this->db->from('fms_investment_transaction');
        if ($this->input->post('id') !=''){
            $this->db->where('investment_id',$this->input->post('id'));
        } 
        if ($this->input->post('id') !='') {
            $this->db->where('investment_id',$this->input->post('id'));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('investment_id=' . $filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
    /**
     * This method helps to add investiment target into the sacco
     */
     
    public function set() {
        $data = array(
            'type' => $this->input->post('type'),
            'tenure'=>$this->input->post('tenure'),
            'expense_account_id' => $this->input->post('expense_account_id'),
            'investment_account_id' => $this->input->post('investment_account_id'),
            'income_account_id' => $this->input->post('income_account_id'),
            'description' => $this->input->post('description'),
            'status_id' => '1',
            'date_created' => time(),
            'created_by' => $_SESSION['id']
        );
        $this->db->insert('fms_investment', $data);
        return $this->db->insert_id();
    }

     public function set2() {
          if (empty($data)) {
            $data = $this->input->post(NULL, TRUE);
            unset($data['tbl'],$data['fund_source_account_id'],$data['investment_account_id'],$data['expense_account_id'],$data['income_account_id']);
        }
          $transaction_date = explode('-', $data['transaction_date'], 3);
          $data['transaction_date'] = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
           $data['transaction_date'] =$this->helpers->get_date_time($data['transaction_date']);
 
           if(is_numeric($_POST['transaction_type_id']) && $_POST['transaction_type_id']==1){
              $data['credit']=$data['amount1'];
              unset($data['debit'],$data['amount1'],$data['amount2']);
           }
             elseif(is_numeric($_POST['transaction_type_id']) && $_POST['transaction_type_id']==2){
              $data['credit']=$data['amount1'];
               unset($data['debit'],$data['amount1'],$data['amount2']);
           }
             elseif(is_numeric($_POST['transaction_type_id']) && $_POST['transaction_type_id']==3){
              $data['credit']=$data['amount1'];
               unset($data['debit'],$data['amount1'],$data['amount2']);
           }
             elseif(is_numeric($_POST['transaction_type_id']) && $_POST['transaction_type_id']==4){
              $data['debit']=$data['amount2'];
               unset($data['credit'],$data['amount1'],$data['amount2']);
           }
            $data['investment_id']=$this->input->post('investment_id');
            $data['transaction_type_id']=$this->input->post('transaction_type_id');
            $data['transaction_no']=date('ymdhms').mt_rand(100, 999);
            $data['account_no_id']=$this->input->post('fund_source_account_id');
            $data['payment_mode'] =$this->input->post('payment_mode');
            $data['transaction_date'] =$data['transaction_date'];
            $data['description'] =$this->input->post('description');
            $data['status_id']= '1';
            $data['date_created'] =time();
            $data['created_by'] = $_SESSION['id'];
            unset($data['amount']);
        
        $this->db->insert('fms_investment_transaction', $data);
        $last_id = $this->db->insert_id();
        if(is_numeric($last_id)){
            $response['transaction_no']=$data['transaction_no'];
            $response['transaction_id']=$last_id;
            return $response;
        }else{
            return false;
        }
    }

 
    public function update() {
         $id = $this->input->post('id');
         $data = array(
            'type' => $this->input->post('type'),
            'tenure'=>$this->input->post('tenure'),
            'expense_account_id' => $this->input->post('expense_account_id'),
            'expense_account_id' => $this->input->post('expense_account_id'),
            'investment_account_id' => $this->input->post('investment_account_id'),
            'income_account_id' => $this->input->post('income_account_id'),
            'description' => $this->input->post('description'),
            'status_id' => '1',
            'date_created' => time(),
            'created_by' => $_SESSION['id']
        );
         $this->db->where('id', $id);

        $query = $this->db->update('fms_investment', $data);
        if ($query) {
            return 1;
        } else {
            return false;
        }
    }
  
    //investment transaction 
    public function update2() {
          if (empty($data)) {
            $data = $this->input->post(NULL, TRUE);
            unset($data['id'], $data['tbl'],$data['account_no_id'],$data['investment_account_id'],$data['expense_account_id'],$data['income_account_id']);
        }
       
           $transaction_date = explode('-', $data['transaction_date'], 3);
           $data['transaction_date'] = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
           $data['transaction_date'] =$this->helpers->get_date_time($data['transaction_date']);
  
         $id = $this->input->post('id');
           $data = array(
            'transaction_date' => $data['transaction_date'] ,
            'description' => $this->input->post('description'),
        );
         $this->db->where('id', $id);

        $query = $this->db->update('fms_investment_transaction', $data);
        if ($query) {
            return 1;
        } else {
            return false;
        }
    }

     public function reverse($external_ref_no=false) {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['transaction_no'],$data['transaction_type_id']);
        $data['reversed_by'] = $_SESSION['id'];
        $data['reversed_date'] = date("Y-m-d H:i:s");
        $data['reverse_msg'] = $this->input->post('reverse_msg');
        $data['status_id'] = 3;

        if (is_numeric($id)) {
            if ($external_ref_no!=false) {
            $this->db->where('ref_no', $external_ref_no);
            return $this->db->update('fms_investment_transaction', $data);
            }else{
            $this->db->where('ref_no', $id);
            $this->db->update('transaction', $data);
            $this->db->where('id', $id);
            return $this->db->update('fms_investment_transaction', $data);
            }
           
        } else {
            return false;
        }
    }

   

}
