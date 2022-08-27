<?php

class DepositReturns_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'loan_guarantor';
        $this->max_state_id = "(SELECT client_loan_id,state_id,comment,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state GROUP BY client_loan_id ) )";
    }


    public function get_savings_account($filter = FALSE, $acc_id = FALSE)
    {
        $this->db->select('account_no_id,sum(debit) withdraw');
        $this->db->from('transaction');
        $this->db->where('transaction_type_id', '1');
        $this->db->where('status_id', '1');
        if ($this->input->post("balance_end_date") !== NULL) {
            $this->db->where("DATE(transaction_date) <= ", $this->helpers->yr_transformer($this->input->post("balance_end_date")));
        }
        if (!empty($this->input->post("end_date")) && !empty($this->input->post("start_date"))) {
            echo "This is the post value date: " . $this->input->post("end_date");
            $this->db->where("DATE(transaction_date) >= ", $this->helpers->yr_transformer($this->input->post("start_date")));
            $this->db->where("DATE(transaction_date) <= ", $this->helpers->yr_transformer($this->input->post("end_date")));
        }
        $this->db->group_by('account_no_id');

        $sub_query_1 = $this->db->get_compiled_select();


        $this->db->select('account_no_id,sum(credit) deposit');
        $this->db->from('transaction');
        $this->db->where('transaction_type_id IN (2,12)');
        $this->db->where('status_id', '1');
        if ($this->input->post("balance_end_date") !== NULL) {
            $this->db->where("DATE(transaction_date) <= ", $this->helpers->yr_transformer($this->input->post("balance_end_date")));
        }

        if (!empty($this->input->post("end_date")) && !empty($this->input->post("start_date"))) {
            $this->db->where("DATE(transaction_date) >= ", $this->helpers->yr_transformer($this->input->post("start_date")));
            $this->db->where("DATE(transaction_date) <= ", $this->helpers->yr_transformer($this->input->post("end_date")));
        }
        $this->db->group_by('account_no_id');
        $sub_query_2 = $this->db->get_compiled_select();


        $this->db->select('account_no_id,sum(debit) transfer');
        $this->db->from('transaction');
        $this->db->where('transaction_type_id', '3');
        $this->db->where('status_id', '1');
        if ($this->input->post("balance_end_date") !== NULL) {
            $this->db->where("DATE(transaction_date) <= ", $this->helpers->yr_transformer($this->input->post("balance_end_date")));
        }
        if (!empty($this->input->post("end_date")) && !empty($this->input->post("start_date"))) {
            $this->db->where("DATE(transaction_date) >= ", $this->helpers->yr_transformer($this->input->post("start_date")));
            $this->db->where("DATE(transaction_date) <= ", $this->helpers->yr_transformer($this->input->post("end_date")));
        }
        $this->db->group_by('account_no_id');
        $sub_query_3 = $this->db->get_compiled_select();


        $this->db->select('account_no_id,sum(debit) payments');
        $this->db->from('transaction');
        $this->db->where('transaction_type_id', '4');
        $this->db->where('status_id', '1');
        if ($this->input->post("balance_end_date") !== NULL) {
            $this->db->where("DATE(transaction_date) <= ", $this->helpers->yr_transformer($this->input->post("balance_end_date")));
        }
        if (!empty($this->input->post("end_date")) && !empty($this->input->post("start_date"))) {
            $this->db->where("DATE(transaction_date) >= ", $this->helpers->yr_transformer($this->input->post("start_date")));
            $this->db->where("DATE(transaction_date) <= ", $this->helpers->yr_transformer($this->input->post("end_date")));
        }
       
        $this->db->group_by('account_no_id');
        $sub_query_4 = $this->db->get_compiled_select();


        $this->db->select('saving_account_id,sum(amount) savings_locked');
        $this->db->from('lock_savings_amount');
        $this->db->where('status_id', '1');
        $this->db->group_by('saving_account_id');
        $sub_query_10 = $this->db->get_compiled_select();


        $this->db->select('account_no_id,sum(debit) charges');
        $this->db->from('transaction');
        $this->db->where('transaction_type_id IN (5,6,7)');
        $this->db->where('status_id', '1');
        if ($this->input->post("balance_end_date") !== NULL) {
            $this->db->where("DATE(transaction_date) <= ", $this->helpers->yr_transformer($this->input->post("balance_end_date")));
        }
        if (!empty($this->input->post("end_date")) && !empty($this->input->post("start_date"))) {
            $this->db->where("DATE(transaction_date) >= ", $this->helpers->yr_transformer($this->input->post("start_date")));
            $this->db->where("DATE(transaction_date) <= ", $this->helpers->yr_transformer($this->input->post("end_date")));
        }
        $this->db->group_by('account_no_id');
        $sub_query_11 = $this->db->get_compiled_select();


        $this->db->select('sum(amount_locked) amount_locked,savings_account_id');
        $this->db->from('loan_guarantor');
        $this->db->where('fms_loan_guarantor.client_loan_id IN', "(SELECT `fms_client_loan`.`id` FROM `fms_client_loan` JOIN $this->max_state_id loan_state ON  loan_state.client_loan_id=`fms_client_loan`.`id` WHERE `state_id` IN (5,6,7,11,12,13,14))", FALSE);
        $this->db->group_by('savings_account_id');
        $sub_query_5 = $this->db->get_compiled_select();


        $this->db->select('sum(amount) locked_amount,saving_account_id');
        $this->db->from('lock_savings_amount');
        $this->db->where('fms_lock_savings_amount.status_id=1');
        $this->db->group_by('saving_account_id');
        $sub_query_12 = $this->db->get_compiled_select();


        $this->db->select('savings_account_id,start_date,end_date,qualifying_amount,type,status');
        $this->db->from('fms_fixed_savings');
        $this->db->where('status', '1');
        $this->db->group_by('savings_account_id');
        $sub_query_13 = $this->db->get_compiled_select();


        $this->sub_query_6 = "(SELECT account_id, state_id FROM fms_account_states
                WHERE id in (
                    SELECT MAX(id) from fms_account_states GROUP BY account_id))";

        $this->db->select("a.id, account_no,a.member_id,b.date_registered, charges, (ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ ifnull( payments ,0) + ifnull(savings_locked,0)+ ifnull( amount_locked, 0) +ifnull(charges, 0) +ifnull( qualifying_amount, 0)+ifnull(min_balance,0)) cash_bal,(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ ifnull( payments ,0)+ifnull(charges, 0)) real_bal, sp.mindepositamount,a.deposit_Product_id, ifnull( transfer ,0) transfers,"
            . "sp.productname,sp.min_balance,a.interest_rate,a.opening_balance,a.client_type,sp.maxwithdrawalamount, j.state_id,concat(c.firstname, ' ',c.lastname,' '"
            . ", c.othernames) member_name,sp.description,sp.producttype,(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ ifnull( payments ,0)+ifnull(charges, 0)) y,concat(c.firstname, ' ',c.lastname,' '"
            . ", c.othernames) name,concat(c.firstname, ' ',c.lastname,' '"
            . ", c.othernames) drilldown,sp.withdraw_cal_method_id,sp.bal_cal_method_id, sp.mandatory_saving, saving_frequency, saving_made_every,date_opened,ifnull( payments ,0) payments,ifnull( withdraw ,0) withdraws,ifnull(deposit,0) deposits,ifnull( locked_amount ,0) locked_amount,fs.start_date,fs.end_date,fs.qualifying_amount,fs.status,fs.type,ifnull(a.child_id, null) child_id, if(a.child_id,concat(ifnull(cd.firstname, ''), ' ',ifnull(cd.lastname, ''),' '"
            . ", ifnull(cd.othernames, '')), null) child_name");
        $this->db->from('savings_account a');
        $this->db->join('fms_member_children cd', 'cd.id=a.child_id', 'LEFT');
        $this->db->join('fms_savings_product sp', 'sp.id=a.deposit_Product_id', 'LEFT');
        $this->db->join('member b', 'a.member_id = b.id');
        $this->db->join('user c', 'b.user_id = c.id');
        $this->db->join('(' . $sub_query_1 . ') d', 'd.account_no_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_2 . ') e', 'e.account_no_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_3 . ') f', 'f.account_no_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_4 . ') g', 'g.account_no_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_10 . ') m', 'm.saving_account_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_11 . ') n', 'n.account_no_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_5 . ') i', 'i.savings_account_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_12 . ') sl', 'sl.saving_account_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_13 . ') fs', 'fs.savings_account_id = a.id', 'LEFT');

        $this->db->join("$this->sub_query_6 j", 'j.account_id=a.id', "LEFT");
     
        
        $subQuery1 = $this->db->get_compiled_select();

        $this->db->reset_query();

        //second query starts here..............

        $this->db->select("a.id ,account_no,a.member_id,g.date_registered, charges,(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ ifnull( payments ,0) + ifnull(savings_locked,0)+ ifnull( amount_locked, 0) +ifnull(charges, 0) +ifnull( qualifying_amount, 0)) cash_bal,(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ ifnull( payments ,0)+ifnull(charges, 0)) real_bal, sp.mindepositamount,a.deposit_Product_id,ifnull( transfer ,0) transfers,"
            . "sp.productname,sp.min_balance,a.interest_rate,a.opening_balance,a.client_type,sp.maxwithdrawalamount, j.state_id, group_name as member_name,sp.description,sp.producttype,(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ ifnull( payments ,0)+ifnull(charges, 0)) y,group_name as name,group_name as drilldown,sp.withdraw_cal_method_id,sp.bal_cal_method_id, sp.mandatory_saving, saving_frequency, saving_made_every,date_opened,ifnull( payments ,0) payments,ifnull( withdraw ,0) withdraws,ifnull(deposit,0) deposits,ifnull( locked_amount ,0) locked_amount, fs.start_date,fs.end_date,fs.qualifying_amount,fs.status,fs.type, ifnull(a.child_id, null) child_id, if(a.child_id,concat(ifnull(cd.firstname, ''), ' ',ifnull(cd.lastname, ''),' '"
            . ", ifnull(cd.othernames, '')), null) child_name");
        $this->db->from('savings_account a');
        $this->db->join('fms_member_children cd', 'cd.id=a.child_id', 'LEFT');
        $this->db->join('fms_savings_product sp', 'sp.id=a.deposit_Product_id', 'LEFT');
        $this->db->join('group g', 'a.member_id = g.id');
        $this->db->join('(' . $sub_query_1 . ') d', 'd.account_no_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_2 . ') e', 'e.account_no_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_3 . ') f', 'f.account_no_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_4 . ') g', 'g.account_no_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_10 . ') m', 'm.saving_account_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_11 . ') n', 'n.account_no_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_5 . ') i', 'i.savings_account_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_12 . ') sl', 'sl.saving_account_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_13 . ') fs', 'fs.savings_account_id = a.id', 'LEFT');

        $this->db->join("$this->sub_query_6 j", 'j.account_id=a.id', "LEFT");
        $subQuery2 = $this->db->get_compiled_select();

        // end of second query..............

        // Start of extra = "";
        $extra_query = "";
        $deposited = "";
        if (isset($_POST['client_id']) === TRUE) {
            $extra_query = "(a.member_id=" . $this->input->post('client_id') . ") AND ";
        } else if (isset($_POST['group_id']) === TRUE) {
            $extra_query = "(group_id=" . $this->input->post('group_id') . ") AND ";
        }


        if ($filter === FALSE) {

            if (!empty($this->input->post("deposit")) && $this->input->post("deposit") !== NULL) {
                if ($this->input->post("deposit") == 1) {
                    $filteredQuery1 = $subQuery1 . " WHERE client_type=1 AND deposit > 0 AND " . $extra_query;
                    $filteredQuery2 = $subQuery2 . " WHERE client_type=2 AND deposit > 0 AND " . $extra_query;
                } else if ($this->input->post("deposit") == 3) {
                    $filteredQuery1 = $subQuery1 . " WHERE client_type=1 AND ifnull(deposit,0) <= 0 AND " . $extra_query;
                    $filteredQuery2 = $subQuery2 . " WHERE client_type=2 AND ifnull(deposit,0) <= 0 AND " . $extra_query;
                } else {
                    $filteredQuery1 = $subQuery1 . " WHERE client_type=1 AND " . $extra_query;
                    $filteredQuery2 = $subQuery2 . " WHERE client_type=2 AND " . $extra_query;
                }
            } else {
                $filteredQuery1 = $subQuery1 . " WHERE client_type=1 AND " . $extra_query;
                $filteredQuery2 = $subQuery2 . " WHERE client_type=2 AND " . $extra_query;
            }
            $query = $this->db->query("select * from ($filteredQuery1 UNION $filteredQuery2) as unionTable ORDER BY id DESC");
            return $query->result_array();
        } else {
            if (is_numeric($acc_id)) {
                if (!empty($this->input->post("deposit")) && $this->input->post("deposit") !== NULL) {
                    if ($this->input->post("deposit") == 1) {
                        $filteredQuery1 = $subQuery1 . " WHERE client_type=1 AND deposit > 0 AND " . $extra_query . " a.id=" . $acc_id . " AND " . $filter;
                        $filteredQuery2 = $subQuery2 . " WHERE client_type=2 AND deposit > 0 AND " . $extra_query . " a.id=" . $acc_id . " AND " . $filter;
                    } else if ($this->input->post("deposit") == 3) {
                        $filteredQuery1 = $subQuery1 . " WHERE client_type=1 AND ifnull(deposit,0) <= 0 AND " . $extra_query . " a.id=" . $acc_id . " AND " . $filter;
                        $filteredQuery2 = $subQuery2 . " WHERE client_type=2 AND ifnull(deposit,0) <= 0 AND " . $extra_query . " a.id=" . $acc_id . " AND " . $filter;
                    } else {
                        $filteredQuery1 = $subQuery1 . " WHERE client_type=1 AND " . $extra_query . " a.id=" . $acc_id . " AND " . $filter;
                        $filteredQuery2 = $subQuery2 . " WHERE client_type=2 AND " . $extra_query . " a.id=" . $acc_id . " AND " . $filter;
                    }
                } else {
                    $filteredQuery1 = $subQuery1 . " WHERE client_type=1 AND " . $extra_query . " a.id=" . $acc_id . " AND " . $filter;
                    $filteredQuery2 = $subQuery2 . " WHERE client_type=2 AND " . $extra_query . " a.id=" . $acc_id . " AND " . $filter;
                }

                $query = $this->db->query("select * from ($filteredQuery1 UNION $filteredQuery2) as unionTable");
                return $query->row_array();
            } else {
                if (!empty($this->input->post("deposit")) && $this->input->post("deposit") !== NULL) {
                    if ($this->input->post("deposit") == 1) {
                        $filteredQuery1 = $subQuery1 . " WHERE client_type=1 AND deposit > 0 AND " . $extra_query . $filter;
                        $filteredQuery2 = $subQuery2 . " WHERE client_type=2 AND deposit > 0 AND " . $extra_query .  $filter;
                    } else if ($this->input->post("deposit") == 3) {
                        $filteredQuery1 = $subQuery1 . " WHERE client_type=1 AND ifnull(deposit,0) <=0 AND " . $extra_query . $filter;
                        $filteredQuery2 = $subQuery2 . " WHERE client_type=2 AND ifnull(deposit,0) <= 0 AND " . $extra_query . $filter;
                    } else {
                        $filteredQuery1 = $subQuery1 . " WHERE client_type=1 AND " . $extra_query .  $filter;
                        $filteredQuery2 = $subQuery2 . " WHERE client_type=2 AND " . $extra_query .  $filter;
                    }
                } else {
                    $filteredQuery1 = $subQuery1 . " WHERE client_type=1 AND " . $extra_query .  $filter;
                    $filteredQuery2 = $subQuery2 . " WHERE client_type=2 AND " . $extra_query .  $filter;
                }
                $query = $this->db->query("select * from ($filteredQuery1 UNION $filteredQuery2) as unionTable ORDER BY id DESC");

                //print_r($this->db->last_query());die;
                return $query->result_array();
            }
        }
    }

   

  // get locked savings
 
  function get_locked_savings ($start, $end)
  {
    $this->db->select('saving_account_id, sum(amount) as locked_amount');
    $this->db->from('lock_savings_amount');
    $this->db->where('status_id =', 1);
    // between start and end 
     $this->db->having("locked_amount >=", $start);
     if(!is_null($end)){
         $this->db->having("locked_amount <", $end);
     }
    $this->db->group_by('saving_account_id');
    $query = $this->db->get();
    $total = 0;
    $response = $query->result_array();
    foreach($response as $t){
        $total += $t['locked_amount'];
    }
    //return $query->result_array();
    return ["count_locked_savings" =>  $query->num_rows(), "total" => $total ];
     
  }

  
  // get fixed savings
  function get_fixed_savings ($start, $end)
  {
    $this->db->where('status =', 1);
    // between start and end 
     $this->db->where("qualifying_amount >=", $start);
     if(!is_null($end)){
         $this->db->where("qualifying_amount <", $end);
     }
    $query = $this->db->get('fixed_savings');
    $total = 0;
    $response = $query->result_array();
    foreach($response as $t){
        $total += $t['qualifying_amount'];
    }
   
    return ["count_fixed_savings" =>  $query->num_rows(), "total" => $total ];
     
  }


  //get savings accounts
  function get_savings_account2($start, $end){
    
    $this->db->select('account_no_id, (sum(credit) - sum(debit)) as account_balance');
    $this->db->from('transaction');
    $this->db->where('status_id', '1');
    $this->db->having("account_balance  >=", $start);
     if(!is_null($end)){
         $this->db->having("account_balance <", $end);
     }
    $this->db->group_by('account_no_id');


    $query = $this->db->get();
    $total = 0;
    $response = $query->result_array();
    foreach($response as $t){
        $total += $t['account_balance'];
    }
    return ["count_savings_account" =>  $query->num_rows(),"total" => $total ];
    //print_r($row); die;
    // return $query->result_array();
  }


    

    // get total amount locked
    function get_total_locked_amount()
    {
        $this->db->select('a.saving_account_id,sum(amount_locked) tot_amount_locked, sum(item_value) tot_item_value, ( sum(amount_locked) + sum(item_value) ) total');
        $this->db->from('lock_savings_amount a');
        $this->db->from('loan_collateral b', 'a.saving_account_id = b.saving_account_id');
        $this->db->group_by('a.saving_account_id');
        $query = $this->db->get();
        return $query->row_array();
    }


   
   
}
