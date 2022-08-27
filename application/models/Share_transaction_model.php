<?php
/* 
  Modified by Ambrose
*/
class Share_transaction_model extends CI_Model {

    public function __construct() {
        $this->load->database();

         $start_date = date('Y-m-d',strtotime($this->input->post('start_date')));
         $start_date = str_replace('-','',$start_date);
         $end_date = date('Y-m-d',strtotime($this->input->post('end_date')));
         $end_date = str_replace('-','',$end_date);

         $transaction_status = $this->input->post('transaction_status');
         
        if(isset($transaction_status) && $transaction_status==1){
         $this->acc_sum_credit_debit = "( 
            SELECT DISTINCT
            share_account_id,SUM(shares_bought) as shares_bought,SUM(shares_refund) as shares_refund,SUM(shares_transfer) as shares_transfer,SUM(charges) as charges ,
            SUM(IFNULL(shares_bought,0)-IFNULL(shares_refund,0)-IFNULL(shares_transfer,0)-IFNULL(charges,0)) as total_amount,MAX(DATE(transaction_date)) as latest_transaction_date
            FROM 
            (
            SELECT DISTINCT share_account_id,transaction_date,status_id,
            (CASE WHEN transaction_type_id=9 THEN  IFNULL(credit,0)  else 0 end)as shares_bought,
            (CASE WHEN transaction_type_id=13 THEN IFNULL(debit,0)  else 0 end)as shares_refund,
            (CASE WHEN transaction_type_id=10 THEN IFNULL(debit,0) else 0 end)as shares_transfer,
            (CASE WHEN transaction_type_id=11 THEN IFNULL(debit,0) else 0 end)as charges
            FROM fms_share_transactions
            WHERE status_id=1
                )a
            WHERE DATE(transaction_date) BETWEEN '{$start_date}' AND '{$end_date}'
             GROUP BY  share_account_id
          ) sht";
         }
         if(isset($transaction_status) && $transaction_status==2){
            $this->acc_with_no_transaction="(
                SELECT id as share_account_id,0 as total_amount,0 as shares_bought,0 as shares_transfer,0 as charges,0 as shares_refund, null as latest_transaction_date FROM fms_share_account 
           ) sht";
           }
        
        $this->num_share_filter="(SELECT share_account_no,sit.price_per_share,stt.share_account_id,round((SUM(IFNULL(credit,0))-SUM(IFNULL(debit,0)))/sit.price_per_share,2) as num_of_shares from fms_share_account sat
        LEFT JOIN fms_share_transactions stt on(sat.id=stt.share_account_id)
        LEFT JOIN fms_share_issuance sit ON(sit.id=sat.share_issuance_id) GROUP BY stt.share_account_id) k";
      
       $this->cumm_sum="(SELECT share_issuance_id,sum(IFNULL(credit,0)) as total_share_credit, sum(IFNULL(debit,0)) as total_share_debit,sum(IFNULL(credit,0))- sum(IFNULL(debit,0))as overal_total_share
           FROM fms_share_transactions WHERE DATE(transaction_date) BETWEEN '{$start_date}' AND '{$end_date}' GROUP BY share_issuance_id) y";
          
         
        $this->num_of_shares_ac="(SELECT  COUNT(share_issuance_id) as num_account,share_issuance_id FROM fms_share_account GROUP BY share_issuance_id) sha";

    }

    public function get($filter = FALSE) {
        $this->db->select("tn.id, tn.transaction_no, tn.share_issuance_id, tn.debit, tn.credit, tn.payment_id,u.firstname, u.lastname, u.othernames, sa.member_id, tn.narrative, tn.transaction_date, tn.date_created, tn.created_by, tn.date_modified, tn.ref_no, tn.modified_by, tn.reverse_msg, tp.type_name, tn.transaction_type_id, tn.reversed_date, tn.status_id, sa.share_account_no, tt.payment_mode, group_name");
        $this->db->from("fms_share_transactions tn");
        $this->db->join("fms_share_account sa", "tn.share_account_id=sa.id", "LEFT");
        $this->db->join("fms_group group", "sa.member_id=group.id AND sa.client_type=2", "LEFT");
        $this->db->join("fms_member m", "sa.member_id=m.id", "LEFT");
        $this->db->join("fms_user u", "m.user_id=u.id", "LEFT");
        $this->db->join("fms_payment_mode tt", "tt.id=tn.payment_id", "LEFT");
        $this->db->join("fms_transaction_type tp", "tp.id=tn.transaction_type_id", "LEFT");
        /* $sel_query1="SELECT
        `tn`.`id`,
        `tn`.`transaction_no`, 
        `tn`.`share_issuance_id`,
        `tn`.`debit`,
        `tn`.`credit`,
        `tn`.`payment_id`,
        `u`.`firstname`, 
        `u`.`lastname`,
        `u`.`othernames`,
        `sa`.`member_id`,
        `tn`.`narrative`,
        `tn`.`transaction_date`,
        `tn`.`date_created`,
        `tn`.`created_by`,
        `tn`.`date_modified`,
        `tn`.`ref_no`, 
        `tn`.`modified_by`, 
        `tn`.`reverse_msg`, 
        `tp`.`type_name`, 
        `tn`.`transaction_type_id`, 
        `tn`.`reversed_date`, 
        `tn`.`status_id`, 
        `sa`.`share_account_no`,
        `tt`.`payment_mode` 
        FROM `fms_share_transactions` `tn` 
        LEFT JOIN `fms_share_account` `sa` ON `tn`.`share_account_id`=`sa`.`id`
        LEFT JOIN `fms_member` `m` ON `sa`.`member_id`=`m`.`id`
        LEFT JOIN `fms_user` `u` ON `m`.`user_id`=`u`.`id`
        LEFT JOIN `fms_payment_mode` `tt` ON `tt`.`id`=`tn`.`payment_id`
        LEFT JOIN `fms_transaction_type` `tp` ON `tp`.`id`=`tn`.`transaction_type_id`"; */

        if (!empty($_POST['start_date'])) {
            $start_date = str_replace('-', '', $_POST['start_date']);
            $this->db->where('DATE(tn.transaction_date) >=' . $start_date);
        }

        if (!empty($_POST['end_date'])) {
            $end_date = str_replace('-', '', $_POST['end_date']);
            $this->db->where('DATE(tn.transaction_date) <= ' . $end_date);
        } 
       

        if ($filter === FALSE) { 
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                //$complete_query=$sel_query1."WHERE tn.id=".$filter."WHERE tn.id=".$filter ; 
                //$query = $this->db->query($complete_query);
                $this->db->where('tn.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                //$complete_query=$sel_query1."WHERE ".$filter;
                //$complete_query=$complete_query." ORDER BY tn.transaction_date ASC";
                $this->db->where($filter);
                $this->db->order_by("tn.transaction_date", "asc");
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
     public function get2($filter= FALSE) {

       $this->db->select('client_no,sa.share_account_no,sht.latest_transaction_date,st.date_created,
            st.share_issuance_id,issuance_name,concat(concat(u.salutation,IF(u.salutation!=null, "'.'", ""))," ",u.firstname," ", u.lastname," ", u.othernames) AS member_name,k.num_of_shares,u.gender,0 as amount,si.price_per_share,sht.share_account_id,IFNULL(total_amount,0) as total_amount,shares_bought,shares_refund,shares_transfer,charges');
            $this->db->from("fms_share_account sa");
            $this->db->join('fms_share_transactions st','sa.id=st.share_account_id','LEFT');
       
        $this->db->join("fms_group group", "sa.member_id=group.id AND sa.client_type=2", "LEFT");
        $this->db->join("fms_member m", "sa.member_id=m.id", "LEFT");
        $this->db->join("fms_user u", "m.user_id = u.id");
        $this->db->join("fms_share_issuance si", "sa.share_issuance_id = si.id");

        if($this->input->post('transaction_status')==1){
        $this->db->join("$this->acc_sum_credit_debit", "sa.id=sht.share_account_id", "INNER");
        }
        if($this->input->post('transaction_status') ==2){
        $this->db->join("$this->acc_with_no_transaction", "sa.id=sht.share_account_id", "LEFT");
        $this->db->where('sht.share_account_id NOT IN (SELECT `share_account_id` FROM fms_share_transactions)', NULL, FALSE);
            }
        $this->db->join("$this->num_share_filter","st.id=k.share_account_id","LEFT");
       
         if(!empty($_POST['gender']) && $_POST['gender']!='All'){
          
           $this->db->where('u.gender =',$_POST['gender']);
           
        }

        if(!empty($_POST['issuance_id']) && $_POST['issuance_id']!='All'){
           
           $this->db->where('st.share_issuance_id =' . $_POST['issuance_id']);
           
        }
        if(!empty($_POST['less_more_equal']) &&  !empty($_POST['num_limit'])){

            $less_more_equal= $_POST['less_more_equal'];

              if($less_more_equal==1)
            {
                $this->db->where('num_of_shares <=' . $_POST['num_limit']);
                
            }
            else if($less_more_equal==2)
            {
             
                $this->db->where('num_of_shares >=' .$_POST['num_limit']);
            }
             else if($less_more_equal==3)
            {
             $this->db->where('num_of_shares =' . $_POST['num_limit']);
            }
            
            }

        $this->db->group_by('sa.share_account_no,st.share_account_id');
        $this->db->order_by('DATE(st.transaction_date)','desc');
        //filters
       
        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('st.share_account_id',$filter);

                $query = $this->db->get();
                return $query->row_array();
            }
             else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
}

 
     public function client_share_sums($filter =false){
         $this->db->select('total_share_credit,total_share_debit, stn.share_issuance_id,issuance_name,stn.share_account_id,overal_total_share');
        $this->db->from("fms_share_transactions stn");
        $this->db->join('fms_share_account sa','stn.share_issuance_id=sa.share_issuance_id','left');
        $this->db->join("fms_member m", "sa.member_id = m.id");
        $this->db->join("fms_user u", "m.user_id = u.id");
        $this->db->join("fms_share_issuance si", "si.id = sa.share_issuance_id");
        if($this->input->post('transaction_status')==1){
        $this->db->join("$this->acc_sum_credit_debit", "sa.id=sht.share_account_id", "left");
        }
        $this->db->join("$this->num_share_filter","sa.id=k.share_account_id","left");
        $this->db->join("$this->cumm_sum","stn.share_issuance_id=y.share_issuance_id","left");

         if (!empty($_POST['start_date'])) {

            $start_date=date('Y-m-d',strtotime($_POST['start_date']));
            $start_date = str_replace('-','',$start_date);
            $this->db->where("DATE(stn.transaction_date) >=",$start_date);
        }
         if(!empty($_POST['end_date'])) {

            $end_date = date('Y-m-d',strtotime($_POST['end_date']));
            $end_date = str_replace('-','',$end_date);
           $this->db->where("DATE(stn.transaction_date) <=",$end_date);
           
        }
         if(!empty($_POST['gender']) && $_POST['gender']!='All'){
          
           $this->db->where('u.gender =' . $_POST['gender']);
           
        }

        if(!empty($_POST['issuance_id']) && $_POST['issuance_id']!='All'){
           
           $this->db->where('stn.share_issuance_id =' . $_POST['issuance_id']);
           
        }
        if(!empty($_POST['less_more_equal']) && !empty($_POST['num_limit'])){

            $less_more_equal= $_POST['less_more_equal'];

              if($less_more_equal==1)
            {
                $this->db->where('num_of_shares <=' . $_POST['num_limit']);
                
            }
            else if($less_more_equal==2)
            {
             
                $this->db->where(' num_of_shares >=' .$_POST['num_limit']);
            }
             else if($less_more_equal==3)
            {
             $this->db->where(' num_of_shares =' . $_POST['num_limit']);
            }
            
            }

       $this->db->where("stn.status_id=",1);
        $this->db->group_by("stn.share_issuance_id");
         if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('stn.share_issuance_id',$filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
  
 
    public function set($share_account_id=false,$transaction_type_id=false,$amount=false,$payment_mode=false,$ref_no=false) {   //for both withdraws and deposits
      $data = $this->input->post(NULL, TRUE);
     $charges=isset($data['charges'])?$data['charges']:[];

      unset($data['id'], $data['tbl'],$data['transaction_channel_id'],$data['shares_account_id'],
          $data['transfer_share_issuance_id'],$data['account_no_id'],$data['share_acc_id'],
          $data['transaction_charge_type_id'],$data['charges'],
          $data['total_charges'],$data['dividends_payable_acc_id'],$data['dividend_declaration_id'],
          $data['amount'],$data['dividends_cash_acc_id'],$data['declaration_date'],
          $data['record_date'],$data['total_dividends'],$data['dividend_per_share'],
          $data['payment_type'],$data['transaction_date'],$data['member_id'],$data['transaction_date'],$data['share_account_no']);
      if(is_numeric($share_account_id)){
        $data['share_account_id'] = $share_account_id;
      } else {
        $data['share_account_id'] = $this->input->post('share_acc_id');
      }
      if(is_numeric($transaction_type_id)){
        $data['transaction_type_id'] = $transaction_type_id;
      } else {
        $data['transaction_type_id'] = $this->input->post('transaction_type_id');
      }

        if($transaction_type_id==10){
            $data['debit'] = $this->input->post('amount');
            $data['narrative'] = "[Share Transfer] ".$this->input->post('narrative');
        }elseif($transaction_type_id==13){
            $data['debit'] = $this->input->post('amount');
            $data['narrative'] = "[Share Refund] ".$this->input->post('narrative');
        }elseif($transaction_type_id==11){
            $data['debit'] = $amount;
            $data['narrative'] = "[Share Transaction Charge] ".$this->input->post('narrative');
        }  else {
            if(is_numeric($amount)){
                $data['credit'] = $amount;
            } else {
            $data['credit'] = $this->input->post('amount');
            }
            $data['narrative'] = $this->input->post('narrative');

        }
      if(is_numeric($ref_no)){
        $data['ref_no'] = $ref_no;
      }
        if(is_numeric($payment_mode)){
            $data['payment_id'] = $payment_mode;
        }else{
            $data['payment_id'] = $this->input->post('payment_id');
        }
        $data['transaction_no'] = date('ymdhms').mt_rand(100, 999);

        //$data['transaction_no'] = date('yws').random_int(100, 999);
        $transaction_date = explode('-', $this->input->post('transaction_date'), 3);
        $new_date= count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]): null;
        
        $data['transaction_date'] = $this->helpers->get_date_time($new_date);
        $data['date_created'] = time();
        $data['status_id'] = 1;
        $data['created_by'] = $_SESSION['id'];
        //$data['transaction_type_id'] =2;
        $this->db->insert('fms_share_transactions', $data);
        $last_id = $this->db->insert_id();
   
        if ($last_id) {
            /* if($this->input->post('payment_id')!=5){
                if(is_numeric($transaction_type_id)){

                }else{
                     if(is_numeric($ref_no)){
                        $myref_no = $ref_no;
                      }else{
                        $myref_no = $last_id;
                      }
                    if (($proper_entries = $this->insert_batch_charges($charges,$myref_no,$data)) != false) {
                       $this->db->insert_batch('fms_share_transactions', $proper_entries);
                    } 
                }
          } */
          
            $response['transaction_no']=$data['transaction_no'];
            $response['share_account_id']=$data['share_account_id'];
            $response['last_id']=$last_id;
            return $response;
        } else {
            return false;
        }
        //end the transaction
    }

      public function insert_batch_charges($charges, $last_id,$data) {//prepare the array
        $track = 0;
        $entries = array();
        foreach ($charges as $key=>$value) {
            if ($value['id'] == '' || $value['charge_amount'] == '') {
                $track += 1;
            }else{
                $charge['transaction_no'] =date('ymdhms').mt_rand(100, 999);
                $charge['share_account_id'] =$data['share_account_id'];
                $charge['debit'] =  $value['charge_amount'];
                $charge['transaction_type_id'] =$this->input->post('transaction_charge_type_id');
                $charge['ref_no'] =$last_id;
                $charge['share_issuance_id']=$data['share_issuance_id'];
                $charge['payment_id'] = $this->input->post('payment_id');
                $charge['transaction_date'] = $this->helpers->get_date_time($data['transaction_date']);
                $charge['narrative'] =$data['narrative'];
                $charge['status_id'] =1;
                $charge['date_created'] = time();
                $charge['created_by'] = $_SESSION['id'];
                $entries[] = $charge;
            }
        }
        return ($track === 0)?$entries:false;
    }
 
    public function set2($application_id) {
        $data = [];
        $transaction_date = explode('-', $this->input->post('transaction_date'), 3);
        $transaction_date_actual = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
        $transaction_no=date('yws').mt_rand(1000, 9999);
                $data = array(
                    'application_id' => $application_id,
                    'share_call_id' => 1,
                    'payment_id' => $this->input->post('payment_id'),
                    'transaction_channel_id' => $this->input->post('transaction_channel_id'),
                    'transaction_no' => $transaction_no,
                    'transaction_date'=> $this->helpers->get_date_time($transaction_date_actual),
                    'transaction_type_id'=>$this->input->post('transaction_type_id'),
                    'status_id' =>1,
                    'credit' =>$this->input->post('amount'),
                    'narrative' => $this->input->post('narrative'),
                    'date_created' => time(),
                    'created_by' => $_SESSION['id'],
                    'modified_by' => $_SESSION['id']
                );
         
        if (!empty($data)) {
            $this->db->insert('fms_share_transactions', $data);
            $response['transaction_no']=$transaction_no;
            $response['transaction_id']=$application_id;
            return $response;
        } else {
            return false;
        }
    }
    public function get_transaction($filter = false) {
        $this->db->select('fms_share_transactions.*,staff_no,concat(u.firstname," ", u.lastname," ", u.othernames) AS member_name,concat(gu.firstname," ", gu.lastname," ", gu.othernames) AS gp_member_name,sa.client_type,sa.share_account_no,branch_name,physical_address,office_phone,postal_address,email_address');
        $this->db->from('fms_share_transactions');
        $this->db->join('fms_share_account sa', 'fms_share_transactions.application_id=sa.id','left');
        $this->db->join('member gm', 'group.member_id=gm.id','left');
        $this->db->join('member m', 'sa.member_id=m.id','left');        
        $this->db->join('user gu', 'gm.user_id=gu.id','left');
        $this->db->join('user u', 'm.user_id=u.id','left');
        $this->db->join('staff', 'fms_share_transactions.created_by=staff.id','left');
        $this->db->join('fms_branch br', 'staff.branch_id=br.id','left');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where( $filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function update() {
        $data = $this->input->post(NULL, TRUE);
        $charges=$data['charges'];
        if($this->input->post('client_type')==2){
            $group_member_id = $this->input->post('group_member_id');
        }else{
            $group_member_id = '';
        }
        unset($data['id'],$data['client_type'], $data['tbl'], $data['charges'], $data['state_id'], $data['opening_balance'], $data['cash_bal']);   
          $transaction_date = explode('-', $this->input->post('transaction_date'), 3);
        $data['transaction_date'] = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
        $group_member_id = NULL;
        $data['date_modified'] = time();
        $data['modified_by'] = $_SESSION['id'];
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('fms_share_transactions', $data);
        $last_id = $this->input->post('id');
        $query=$this->db->update_batch('fms_transaction_charges', $this->insert_batch_charges($charge, $last_id), array('transaction_id' => $last_id));

        return $query;
        //end the transaction
    }

    public function total_charges($filter = FALSE) {  //get all tansaction charges per account
        $this->db->select('SUM(charge_amount) as amount', FALSE);
        $this->db->from('fms_transaction_charges tc');
        $this->db->join('fms_share_transactions t', 't.id=tc.transaction_id', 'inner');
        $this->db->where('t.status_id', 1);

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if (is_numeric($filter) && !is_array($filter)) {
                $this->db->where('t.application_id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->row_array();
                //return $query->result_array();
                // echo $this->db->last_query(); die; 
            }
        }
    }

    public function reverse() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['transaction_no'],$data['transaction_type_id']);
        $data['reversed_by'] = $_SESSION['id'];
        $data['reversed_date'] = date("Y-m-d H:i:s");
        $data['reverse_msg'] = $this->input->post('reverse_msg');
        $data['status_id'] = 3;

        if (is_numeric($id)) {
            $this->db->where('ref_no', $id);
            $this->db->update('share_transactions', $data);
            $this->db->where('id', $id);
            return $this->db->update('share_transactions', $data);
        } else {
            return false;
        }
    }

    public function get_account_sums($filter = FALSE) {  //get total transaction amount per account 
        $this->db->select('ABS(SUM(IFNULL(credit,0)-IFNULL(debit,0))) amount, SUM(IFNULL(credit,0)) as credit_sum,SUM(IFNULL(debit,0)) as debit_sum');
        $this->db->from('share_transactions');
        $this->db->where('status_id', 1);

       if ($filter === FALSE) {
           $this->db->where('YEAR(transaction_date)',date('Y'));
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where( $filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

      public function get_account_sums1($filter = FALSE) {  
      //get total transaction amount per account 
        $this->db->select('ABS(SUM(IFNULL(credit,0)-IFNULL(debit,0))) amount, SUM(IFNULL(credit,0)) as credit_sum,SUM(IFNULL(debit,0)) as debit_sum,share_issuance_id,issuance_name,price_per_share,transaction_date');
        $this->db->from('share_transactions');
        $this->db->join('share_issuance', 'share_transactions.share_issuance_id= share_issuance.id', 'left');
        $this->db->where('share_transactions.status_id', 1);
       if ($filter === FALSE) {
            $this->db->where('YEAR(transaction_date)',date('Y'));
            $query = $this->db->get();
             return $query->result_array();
        } else {
            if (is_numeric($filter)) {
               
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where( $filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
    // gender statistics.
       public function get_account_sums2($filter = FALSE) {  //get total transaction amount per account 
        $this->db->select('DISTINCT ABS(SUM(IFNULL(credit,0)-IFNULL(debit,0))) amount, SUM(IFNULL(credit,0)) as credit_sum,SUM(IFNULL(debit,0)) as debit_sum,share_account.share_issuance_id ,num_account,issuance_name,price_per_share,transaction_date,(CASE WHEN u.gender=1 THEN "Male" else "Female" end) as gender');
        $this->db->from('share_transactions');
        $this->db->join('share_issuance', 'share_transactions.share_issuance_id= share_issuance.id', 'left');
        $this->db->join('share_account','share_account.id=share_transactions.share_account_id','left');
        $this->db->join('member', 'member.id =share_account.member_id', 'left');
        $this->db->join('user u', 'u.id= member.user_id', 'left');
        //$this->db->join("fms_user u", "u.id = fms_share_account.id");
        $this->db->join("$this->num_of_shares_ac", "share_account.share_issuance_id= sha.share_issuance_id");
        $this->db->where('share_transactions.status_id', 1);
        $this->db->group_by('gender,issuance_name');
        $this->db->order_by('issuance_name','desc');
       if ($filter === FALSE) {
            $this->db->where('DATE(fms_share_transactions.transaction_date)',$filter);
            $query = $this->db->get();
             return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('DATE(fms_share_transactions.transaction_date)',$filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where( $filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
 
    public function total_transactions($filter = FALSE) {  //get total transaction amount per account 
        $this->db->select('(SUM(IFNULL(credit,0))-SUM(IFNULL(debit,0))) as amount');
        $this->db->from('share_transactions');
        $this->db->where('status_id', 1);

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if (is_numeric($filter) && !is_array($filter)) {
                $this->db->where('application_id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->row_array();
                //return $query->result_array();
                // echo $this->db->last_query(); die; 
            }
        }
    }

  
    public function get_total_shares_transfer($filter = FALSE) { 

        $this->db->select('(COUNT(IFNULL(id,0))) as no_of_shares_transfer, sum(ifnull(debit,0)) as amount_transfered');
        $this->db->from('share_transactions');
        $this->db->where('status_id', 1);
        $this->db->where('transaction_type_id',10);
        if ($filter === FALSE) {
            $this->db->where('DATE(transaction_date)',$filter);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if (is_numeric($filter) && !is_array($filter)) {
                $this->db->where('DATE(transaction_date)',$filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->row_array();
                 
            }
        }
    }

    public function get_total_shares($filter = FALSE) { 

        $this->db->select('(COUNT(IFNULL(id,0))) as no_of_shares_bought, sum(ifnull(credit,0)+ifnull(debit,0)) as amount_bought');
        $this->db->from('share_transactions');
        $this->db->where('status_id', 1);
       // $this->db->where('transaction_performed_id', 3);
        //$this->db->like('narrative','[Share Transfer]');

        if ($filter === FALSE) {
            $this->db->where('YEAR(transaction_date)',date('Y'));
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if (is_numeric($filter) && !is_array($filter)) {
                $this->db->where('MONTH(transaction_date)',$filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->row_array();
                 
            }
        }
    }
     public function get_total_shares_bought($filter = FALSE) { 

        $this->db->select('(COUNT(IFNULL(id,0))) as no_of_shares_bought, sum(ifnull(credit,0)) as amount_bought');
        $this->db->from('share_transactions');
        $this->db->where('status_id', 1);
        $this->db->where('transaction_type_id',9);
        
        if ($filter === FALSE) {
            $this->db->where('YEAR(transaction_date)',date('Y'));
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if (is_numeric($filter) && !is_array($filter)) {
                $this->db->where('MONTH(transaction_date)',$filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->row_array();
                 
            }
        }
    }
     public function get_total_shares_sold($filter = FALSE) { 

        $this->db->select('(COUNT(IFNULL(id,0))) as no_of_shares_bought, sum(ifnull(debit,0)) as amount_sold');
        $this->db->from('share_transactions');
        $this->db->where('status_id', 1);
        $this->db->where('transaction_type_id', 13);
        if ($filter === FALSE) {
            $this->db->where('YEAR(transaction_date)',date('Y'));
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if (is_numeric($filter) && !is_array($filter)) {
                $this->db->where('MONTH(transaction_date)',$filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->row_array();
                 
            }
        }
    }

    public function delete() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fms_share_transactions', ['status_id' => 0]);
    }

    public function get3($filter=false){

        $this->db->select('(SUM(IFNULL(credit,0))-SUM(IFNULL(debit,0))) as total_amount');
        $this->db->from('fms_share_transactions');
        $this->db->where($filter);
        $query = $this->db->get();
        return $query->row_array();

    }

    public function sum_share_amounts($filter)
{
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');

    $this->db->select("SUM(ifnull(credit,0)) as credit_amount, SUM(ifnull(debit,0)) as debit_amount");
    $this->db->from("fms_share_transactions");
    $this->db->where("status_id=1");
    $this->db->where($filter);

    if($start_date) {
        $this->db->where("DATE(transaction_date) >= '{$start_date}'");
    }
    if($end_date) {
        $this->db->where("DATE(transaction_date) <= '{$end_date}'");
    }

    $query = $this->db->get();

    $result = $query->row_array();
    if(!is_numeric($result['credit_amount'])) {
        $result['credit_amount'] = 0;
    }
    if(!is_numeric($result['debit_amount'])) {
        $result['debit_amount'] = 0;
    }
    
    return $result;
}
}

 