<?php

class Savings_account_model extends CI_Model {

    public function __construct() {
        $this->load->database();
        $this->maxstate = "
                (SELECT account_id, state_id FROM fms_account_states
                WHERE id in (
                    SELECT MAX(id) from fms_account_states GROUP BY account_id 
                )
            )";
        $this->single_contact = "
                (SELECT `user_id`, `mobile_number` FROM `fms_contact`
                WHERE `id` in (
                    SELECT MAX(`id`) from `fms_contact` WHERE `contact_type_id`=1 GROUP BY `user_id` 
                )
            )";
    }

    public function already_exist($account_no) { //check if exist
        $this->db->select('account_no');
        $this->db->from('fms_savings_account');
        $this->db->where('account_no', $account_no);
        $query = $this->db->get();
        if ($query->result_array()) {
            return true;
        } else {
            return false;
        }
    }
    
    public function check_account_no_exists($account_no) {
        $this->db->select('account_no');
        $this->db->from('fms_savings_account');
        $this->db->where('account_no', $account_no);
        $this->db->where("fms_savings_account.created_by IN (SELECT fms_staff.user_id FROM fms_staff JOIN fms_user ON fms_staff.user_id=fms_user.id WHERE organisation_id=". $_SESSION['organisation_id'].")");
        if (is_numeric($this->input->post('id'))) {
            $this->db->where("fms_savings_account.id <>", $this->input->post('id'));
        }
        $this->db->order_by("account_no", "desc");
        $this->db->limit(1);
        $query = $this->db->get();
        $current_account = $query->row_array();
        return !isset($current_account['account_no']);
    }

    public function last_account_no($filter) { //check if exist
        $this->db->select('id, account_no');
        $this->db->from('fms_savings_account');
        $this->db->order_by('id', "desc");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function state_totals($filter=false,$state_based=true){
        $this->db->select("COUNT(*) AS number, state_id");
        $this->db->from("savings_account a");        
        $this->db->join("$this->maxstate account_state", "account_state.account_id=a.id");
        if ($filter !=false) {
            $this->db->where($filter);
        }
        if ( $this->input->post('date_to') != NULL && $this->input->post('date_to') !=='') {
             $this->db->where("a.date_created <='". strtotime($this->input->post('date_to'))."'");
        }
        if ( $this->input->post('date_from') != NULL && $this->input->post('date_from') !=='') {
            $this->db->where("a.date_created >='". strtotime($this->input->post('date_from')) ."'");
        }
        if ($state_based==true) {
            $this->db->group_by("state_id");
        }
        $query=$this->db->get();
        return $query->result_array();
    }

    public function get_clients($filter = false) {
        $sub_query= '';
        if ($filter === false) {
           $sub_query= '';
        }else{
            $sub_query= ' WHERE '.$filter.' ';
        }
        $query = $this->db->query('SELECT fms_member.id, concat(client_no, " ", salutation," ",firstname," ", lastname," ", othernames) AS client_name,client_no,'
                . '1 as client_type from fms_member JOIN fms_user ON fms_member.user_id=fms_user.id '.    $sub_query.' UNION SELECT fms_group.id as id, '
                . 'group_name as client_name,0 as client_no,2 as client_type from fms_group'.$sub_query);
        return $query->result_array();
    }
    public function get($filter = false) {

        $this->db->select('sa.id,sa.account_no,client_no,sa.client_type,sa.deposit_Product_id,sp.productname,sp.producttype,sp.description,sa.interest_rate,sa.opening_balance,sa.term_length,sa.date_opened,sp.account_balance_for_interest_cal,producttype,sa.term_length,deposit_Product_id,last_interest_cal_date,sa.created_by,sa.date_created,account_state.state_id,sp.interestpaid,sp.defaultinterestrate,sp.daysinyear,sa.member_id,IF(sa.client_type=2, group.group_name , concat( concat(salutation,".")," ",firstname," ", lastname," ", othernames) ) AS member_name,user.email,mobile_number,sp.min_balance,sp.mindepositamount,sp.maxwithdrawalamount,sp.savings_liability_account_id,interest_paid_expense_account_id,interest_earned_payable_account_id,wheninterestispaid');
        $this->db->from('fms_savings_account sa');
        $this->db->join('fms_savings_product sp', 'sp.id=sa.deposit_Product_id', 'left');
        $this->db->join('fms_group group', 'group.id=sa.member_id', 'LEFT');
        $this->db->join("member", "member.id = sa.member_id");
        $this->db->join("user", "member.user_id = user.id");        
        $this->db->join($this->single_contact . " c", "c.user_id = user.id", "left");
        $this->db->join("$this->maxstate account_state", "account_state.account_id = sa.id");
        $this->db->order_by('sa.id', 'DESC');
        
        if ($this->input->post('state_id') != "" && !empty($this->input->post('state_id'))) {
            $this->db->where('account_state.state_id', $this->input->post('state_id'));
        }

        if ($this->input->post('client_type') != "" && !empty($this->input->post('client_type'))) {
            $this->db->where('sa.client_type', $this->input->post('client_type'));
        }

        if ($this->input->post('client_id') != "" && !empty($this->input->post('client_id'))) {
            $this->db->where('sa.member_id', $this->input->post('client_id'));
        }
        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('sa.id', $filter);
                $query = $this->db->get();
                //print_r($this->db->last_query());
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }


     public function get_for_payments($filter = false) {

        $this->db->select('sa.id,sa.account_no,client_no,sa.client_type,sa.deposit_Product_id,sp.productname,sp.producttype,sp.description,sa.interest_rate,sa.opening_balance,sa.term_length,sa.date_opened,sp.account_balance_for_interest_cal,producttype,sa.term_length,deposit_Product_id,last_interest_cal_date,sa.created_by,sa.date_created,account_state.state_id,sp.interestpaid,sp.defaultinterestrate,sp.daysinyear,sa.member_id,concat( concat(salutation,".")," ",firstname," ", lastname," ", othernames) AS member_name,user.email,mobile_number,sp.min_balance,sp.mindepositamount,sp.maxwithdrawalamount,sp.savings_liability_account_id,interest_paid_expense_account_id,interest_earned_payable_account_id,wheninterestispaid');
        $this->db->from('fms_savings_account sa');
        $this->db->join('fms_savings_product sp', 'sp.id=sa.deposit_Product_id', 'left');
        $this->db->join("member", "member.id = sa.member_id");
        $this->db->join("user", "member.user_id = user.id");        
        $this->db->join($this->single_contact . " c", "c.user_id = user.id", "left");
        $this->db->join("$this->maxstate account_state", "account_state.account_id = sa.id");
        $this->db->order_by('sa.id', 'DESC');
      
        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('sa.id', $filter);
                $query = $this->db->get();
                //print_r($this->db->last_query());
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

     public function get_min_date($filter = false) {
        $this->db->select('min(date_opened) as min_open_date,min(last_interest_cal_date) as min_last_date');
        $this->db->from('fms_savings_account sa');
        $this->db->join('fms_savings_product sp', 'sp.id=sa.deposit_Product_id', 'left');
        $this->db->join("$this->maxstate account_state", "account_state.account_id = sa.id");
        $this->db->where($filter);
        $query = $this->db->get();
        return $query->row_array();
            
        }


    public function get_count($filter = 1) {
        $this->db->select("count(`id`) `total_accounts`")
                ->join("$this->maxstate account_state", "account_state.account_id = savings_account.id")
                ->where($filter);
        $q = $this->db->get("savings_account");
        return $q->row_array();
    }


    public function set($account_no) {
        if (($this->already_exist($account_no)) == false) {
            $data = $this->input->post(null, true);
            unset($data['id'], $data['tbl'], $data['organisation_id'], $data['format'], $data['mandatory_saving']);
            $data['date_created'] = time();
            $data['status_id'] = 1;
            $data['date_opened'] = $this->helpers->yr_transformer($this->input->post('date_opened'));
            $data['account_no'] = $account_no;
            $data['created_by'] = $_SESSION['staff_id'];
            $inser = $this->db->insert('fms_savings_account', $data);
            $last_id = $this->db->insert_id();
            if ($inser === true && $last_id != '') {
                    $inserter = $this->db->insert('fms_account_states', ['account_id' => $last_id, 
                        'comment' => 'New account created',
                        'state_id' => 7,
                        'created_by' => $_SESSION['staff_id'],
                        'date_created' => time()
                    ]);
                    $this->db->insert('fms_auto_savings', ['product_id' => $data['deposit_Product_id'], 
                        'savings_account_id' => $last_id,
                        'last_payment_date' => $data['date_opened']
                    ]);
                    if ($inserter == true) {
                        return $last_id;
                    } else {
                        $this->db->delete('fms_savings_account', array('id' =>  $last_id));
                        return false;
                    }
            } else {
                return false;
            }
        } else {
            // 'Already exist, refresh this page first';
            return false;
        }
    }

    public function update() {
        $data = $this->input->post(null, true);
        unset($data['id'], $data['tbl'], $data['account_no'], $data['organisation_id'], $data['format'], $data['status_id'], $data['mandatory_saving']);
        $data['modified_by'] = $_SESSION['id'];
        $data['date_opened'] = $this->helpers->yr_transformer($this->input->post('date_opened'));
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fms_savings_account', $data);
    }
    public function update_last_pay_date($date,$account_id) {
        $data = array('last_interest_cal_date' =>$date);
        $this->db->where('id', $account_id);
        return $this->db->update('fms_savings_account', $data);
    }

    public function change_status() {
      
        if($this->input->post('id')){
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fms_savings_account', ['status_id' => '0']);
        }
        
    }

    public function change_state($data =FALSE) {
     
        if($data==FALSE){
        return $this->db->insert(
                    'fms_account_states', [
                    'account_id' => $this->input->post('account_id'),
                    'state_id' => $this->input->post('state_id'),
                    'comment' => $this->input->post('comment'),
                    'created_by' => $_SESSION['id'],
                    'date_created' => time()
                        ]
        );
    }
    else {
        $state_id = $data['state_id']== 7 ? 17:7;
        $this->db->where('account_id', $data['account_id']);
        return $this->db->update('fms_account_states', ['state_id' => $state_id]);
    }
    }

     public function get_excel_data($filter = false) {
        $this->db->select('sa.id,sa.account_no,client_no,sa.client_type,sa.deposit_Product_id,sp.productname,sp.producttype,sp.description,sa.interest_rate,sa.opening_balance,sa.term_length,sa.created_by,sa.date_created,account_state.state_id,sa.member_id,concat( concat(salutation,".")," ",firstname," ", lastname," ", othernames) AS member_name,sp.min_balance,sp.mindepositamount,sp.maxwithdrawalamount');
        $this->db->from('fms_savings_account sa');
        $this->db->join('fms_savings_product sp', 'sp.id=sa.deposit_Product_id', 'left');
        $this->db->join("member", "member.id = sa.member_id");
        $this->db->join("user", "member.user_id = user.id");
        $this->db->join("$this->maxstate account_state", "account_state.account_id = sa.id");
         $this->db->where('account_state.state_id', 7);
        $this->db->order_by('sa.id', 'ASC');

        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('sa.id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
    // Savings acc details dropdown
    public function get_savings_acc_details($filter = false) {
        $this->db->select('a.*');
        $this->db->from('savings_account a');
        if ( is_numeric($filter) && $filter !== false) {
            $this->db->where('a.id',$filter);

            $query = $this->db->get();
            return $query->row_array();
        }elseif ( !(is_numeric($filter)) && $filter !== false){
            $this->db->where('a.account_no',$filter);

            $query = $this->db->get();
            return $query->result_array();
        }else{
            $query = $this->db->get();
            return $query->result_array();            
        }
    }

    public function get2($id = false) {
        $this->db->select('sa.*');
        $this->db->from('fms_savings_account sa');
        $this->db->where('sa.id', $id);
        $query = $this->db->get();
        return $query->row_array();
           
    }

     public function get_savings_account($filter = false) {
        $this->db->select('sa.id,sa.account_no,client_no,sa.client_type,sa.deposit_Product_id,sp.productname,sp.producttype,sp.description,sa.interest_rate,sa.opening_balance,sa.term_length,sa.created_by,sa.date_created,account_state.state_id,sa.member_id,concat( concat(salutation,".")," ",firstname," ", lastname," ", othernames) AS member_name,sp.min_balance,sp.mindepositamount,sp.maxwithdrawalamount');
        $this->db->from('fms_savings_account sa');
        $this->db->join('fms_savings_product sp', 'sp.id=sa.deposit_Product_id', 'left');
        $this->db->join("member", "member.id = sa.member_id");
        $this->db->join("user", "member.user_id = user.id");
        $this->db->join("$this->maxstate account_state", "account_state.account_id = sa.id");
        $this->db->where('user.status!=9');
        $this->db->order_by('sa.id', 'DESC');

        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('sa.id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_payouts($filter = false) {
        $this->db->select('pi.*,sa.account_no,client_no,sa.client_type,sa.deposit_Product_id,sa.interest_rate,sa.term_length,account_state.state_id,sa.member_id,concat( concat(salutation,".")," ",firstname," ", lastname," ", othernames) AS member_name,savings_liability_account_id,interest_earned_payable_account_id');
        $this->db->from('fms_savings_interest_payment pi');
        $this->db->join('fms_savings_account sa', 'sa.id=pi.savings_account_id', 'left');
        $this->db->join('fms_savings_product sp', 'sp.id=sa.deposit_Product_id', 'left');
        $this->db->join("member", "member.id = sa.member_id");
        $this->db->join("user", "member.user_id = user.id");
        $this->db->join("$this->maxstate account_state", "account_state.account_id = sa.id");
         $this->db->where('account_state.state_id', 7);

        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('sa.id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

}
