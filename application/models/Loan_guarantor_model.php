<?php

class Loan_guarantor_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'loan_guarantor';
        $this->max_state_id = "(SELECT client_loan_id,state_id,comment,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state GROUP BY client_loan_id ) )";
    }

    public function set()
    { //adding one by one guarantor
        $data = $this->input->post(NULL, TRUE);
        unset($data['id']);
        $data['status_id'] = '1';
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function set2($loan_id = false)
    { //For a set by step form
        $query = false;
        if ($loan_id !== false) {
            $client_loan_id = $loan_id;
        } else {
            $client_loan_id = $this->input->post('client_loan_id');
        }
        $guarantors = $this->input->post('guarantors');
        foreach ($guarantors as $key => $value) { //it is a new entry, so we insert afresh
            if ($value['savings_account_id'] != 0) {
                $value['date_created'] = time();
                $value['client_loan_id'] = $client_loan_id;
                $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
                $query = $this->db->insert($this->table, $value);
            }
        }
        return $query;
    }

    public function set3($sent_data)
    {
        $query = $this->db->insert($this->table, $sent_data);
        return $this->db->insert_id();
    }

    public function duplicate_entry($new_loan_id)
    {
        $sql_query = "INSERT INTO fms_loan_guarantor(client_loan_id, amount_locked, savings_account_id,relationship_type_id,status_id,date_created,created_by,modified_by) SELECT " . $new_loan_id . ", amount_locked, savings_account_id, relationship_type_id, status_id, UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP('1970-01-01 03:00:00')," . $_SESSION['id'] . "," . $_SESSION['id'] . " FROM fms_loan_guarantor WHERE status_id=1 AND client_loan_id =" . $this->input->post('linked_loan_id');
        $query = $this->db->query($sql_query);
        return $this->db->insert_id();
    }

    public function update()
    {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['id'];
        $data['status_id'] = '1';
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update($this->table, $data);
    }


    public function set_share_guarantor($loan_id = false)
    { //For a set by step form
        $query = false;
        if ($loan_id !== false) {
            $client_loan_id = $loan_id;
        } else {
            $client_loan_id = $this->input->post('client_loan_id');
        }
        $guarantors = $this->input->post('share_guarantors');
        foreach ($guarantors as $key => $value) { //it is a new entry, so we insert afresh
            if ($value['share_account_id'] != 0) {
                $value['date_created'] = time();
                $value['client_loan_id'] = $client_loan_id;
                $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
                $query = $this->db->insert("share_loan_guarantor", $value);
            }
        }
        return $query;
    }

    public function duplicate_entry2($new_loan_id)
    {
        $sql_query = "INSERT INTO fms_share_loan_guarantor(client_loan_id, amount_locked, share_account_id,relationship_type_id,status_id,date_created,created_by,modified_by) SELECT " . $new_loan_id . ", amount_locked, share_account_id, relationship_type_id, status_id, UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP('1970-01-01 03:00:00')," . $_SESSION['id'] . "," . $_SESSION['id'] . " FROM fms_share_loan_guarantor WHERE status_id=1 AND client_loan_id =" . $this->input->post('linked_loan_id');
        $query = $this->db->query($sql_query);
        return $this->db->insert_id();
    }

    /**
     * This method Deactivates client_loan data from the database
     */
    public function delete_by_id($id = false)
    {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->delete($this->table);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete($this->table);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    //get a single client loan guarantor
    public function get($filter = FALSE)
    {
        $this->db->select("a.id id,account_no,b.client_no,ct.mobile_number, ua.address1, ua.address2, e.member_id, client_loan_id, amount_locked, savings_account_id, relationship_type_id, a.status_id, "
            . "a.date_created, a.created_by, a.date_modified, a.modified_by,concat(c.salutation, ' ', c.firstname, ' ', c.lastname, ' ', c.othernames) full_name,
                ,concat(g.salutation, ' ', g.firstname, ' ', g.lastname, ' ', g.othernames) guarantor_name,d.relationship_type");
        $this->sub_query_1 = "(select * from fms_user_address where id in ( select max(id) from fms_user_address group by user_id ))";
        $this->sub_query_2 = "( select * from fms_contact where id in ( select max(id) from fms_contact group by user_id ) )";

        $this->db->from($this->table . ' a');
        $this->db->join('client_loan', 'a.client_loan_id = client_loan.id');
        $this->db->join('member b', 'client_loan.member_id = b.id');
        $this->db->join('user c', 'b.user_id = c.id');
        $this->db->join('savings_account e', 'a.savings_account_id = e.id');
        $this->db->join('member f', 'f.id = e.member_id');
        $this->db->join('user g', 'g.id=f.user_id');
        $this->db->join("$this->sub_query_1 ua", 'ua.user_id=g.id', "LEFT");
        $this->db->join("$this->sub_query_2 ct", 'ct.user_id=g.id', "LEFT");

        $this->db->join('relationship_type d', 'a.relationship_type_id = d.id');

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.client_loan_id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                // print_r($this->db->last_query()) . die;;
                return $query->result_array();
            }
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_guarantors($filter = FALSE)
    {
        $this->db->select("a.id id,account_no,b.client_no,ct.mobile_number, ua.address1, ua.address2, e.member_id, a.client_loan_id,  lg.amount_locked, lg.savings_account_id, a.relationship_type_id, a.status_id, "
            . "a.date_created, a.created_by, a.date_modified, a.modified_by,concat(c.salutation, ' ', c.firstname, ' ', c.lastname, ' ', c.othernames) full_name,
                ,concat(g.salutation, ' ', g.firstname, ' ', g.lastname, ' ', g.othernames) guarantor_name,d.relationship_type");
        $this->sub_query_1 = "(select * from fms_user_address where id in ( select max(id) from fms_user_address group by user_id ))";
        $this->sub_query_2 = "( select * from fms_contact where id in ( select max(id) from fms_contact group by user_id ) )";

        $this->db->from('fms_guarantor a');
        $this->db->join('client_loan', 'a.client_loan_id = client_loan.id', 'left');
        $this->db->join('member b', 'client_loan.member_id = b.id', 'left');
        $this->db->join('user c', 'b.user_id = c.id', 'left');
        $this->db->join('savings_account e', 'e.member_id = a.member_id', 'left');
        $this->db->join('fms_loan_guarantor lg', 'lg.savings_account_id = e.id', 'left');
        $this->db->join('member f', 'f.id = e.member_id', 'LEFT');
        $this->db->join('user g', 'g.id=f.user_id', 'LEFT');
        $this->db->join("$this->sub_query_1 ua", 'ua.user_id=g.id', "LEFT");
        $this->db->join("$this->sub_query_2 ct", 'ct.user_id=g.id', "LEFT");

        $this->db->join('relationship_type d', 'a.relationship_type_id = d.id', 'left');

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.client_loan_id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                // print_r($this->db->last_query()) . die;;
                return $query->result_array();
            }
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    public function change_status_by_id($id = false)
    {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            $query = $this->db->update($this->table, $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            $query = $this->db->update($this->table, $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function get_guarantor_savings($filter = FALSE, $acc_id = FALSE)
    {

        $this->db->select('account_no_id,sum(debit) withdraw');
        $this->db->from('transaction');
        $this->db->where('transaction_type_id', '1');
        $this->db->where('status_id=1');
        $this->db->group_by('account_no_id');
        $sub_query_1 = $this->db->get_compiled_select();

        $this->db->select('account_no_id,sum(credit) deposit');
        $this->db->from('transaction');
        $this->db->where('transaction_type_id IN (2,12)');
        $this->db->where('status_id=1');
        $this->db->group_by('account_no_id');
        $sub_query_2 = $this->db->get_compiled_select();

        $this->db->select('account_no_id,sum(debit) transfer');
        $this->db->from('transaction');
        $this->db->where('transaction_type_id', '3');
        $this->db->where('status_id=1');
        $this->db->group_by('account_no_id');
        $sub_query_3 = $this->db->get_compiled_select();

        $this->db->select('account_no_id,sum(debit) payments');
        $this->db->from('transaction');
        $this->db->where('transaction_type_id', '4');
        $this->db->where('status_id=1');
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
        $this->db->where('status_id=1');
        $this->db->group_by('account_no_id');
        $sub_query_11 = $this->db->get_compiled_select();


        $this->db->select('sum(amount_locked) amount_locked,savings_account_id');
        $this->db->from('loan_guarantor');
        $this->db->where('fms_loan_guarantor.client_loan_id IN', "(SELECT `fms_client_loan`.`id` FROM `fms_client_loan` JOIN $this->max_state_id loan_state ON  loan_state.client_loan_id=`fms_client_loan`.`id` WHERE `state_id` IN (5,6,7,11,12,13,14))", FALSE);
        $this->db->group_by('savings_account_id');
        $sub_query_5 = $this->db->get_compiled_select();

        $this->sub_query_6 = "(SELECT account_id, state_id FROM fms_account_states
                WHERE id in (
                    SELECT MAX(id) from fms_account_states GROUP BY account_id))";

        $this->db->select("a.id, account_no,a.member_id, (ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ ifnull( payments ,0)  + "
            . "ifnull(savings_locked,0)+ ifnull( amount_locked, 0) +ifnull(charges, 0) ) cash_bal,(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ ifnull( payments ,0)+ifnull(charges, 0)) real_bal, sp.mindepositamount,a.deposit_Product_id,"
            . "sp.productname,sp.min_balance,a.interest_rate,a.opening_balance,a.client_type,sp.maxwithdrawalamount, j.state_id,concat(salutation,' ', firstname, ' ',lastname,' '"
            . ", othernames) member_name,sp.description,sp.withdraw_cal_method_id,sp.bal_cal_method_id, sp.mandatory_saving, saving_frequency, saving_made_every,date_opened");
        $this->db->from('savings_account a');
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
        $this->db->join("$this->sub_query_6 j", 'j.account_id=a.id', "LEFT");

        if (isset($_POST['client_id']) && !empty($this->input->post('client_id'))) {
            $this->db->where('a.member_id', $this->input->post('client_id'));
        }
        if (isset($_POST['group_id']) && !empty($this->input->post('group_id'))) {
            $this->db->where('a.member_id', $this->input->post('group_id'));
        }

        if ($filter === FALSE) {

            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($acc_id)) {
                $this->db->where('a.id=' . $acc_id);
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                // print_r( $this->db->last_query()); die;
                return $query->result_array();
            }
        }
    }

    public function get_guarantor_savings_group($filter = FALSE, $acc_id = FALSE)
    {

        $this->db->select('account_no_id,sum(debit) withdraw');
        $this->db->from('transaction');
        $this->db->where('transaction_type_id', '1');
        $this->db->where('status_id', '1');
        $this->db->group_by('account_no_id');
        $sub_query_1 = $this->db->get_compiled_select();

        $this->db->select('account_no_id,sum(credit) deposit');
        $this->db->from('transaction');
        $this->db->where('transaction_type_id IN (2,12)');
        $this->db->where('status_id', '1');

        $this->db->group_by('account_no_id');
        $sub_query_2 = $this->db->get_compiled_select();

        $this->db->select('account_no_id,sum(debit) transfer');
        $this->db->from('transaction');
        $this->db->where('transaction_type_id', '3');
        $this->db->where('status_id', '1');

        $this->db->group_by('account_no_id');
        $sub_query_3 = $this->db->get_compiled_select();

        $this->db->select('account_no_id,sum(debit) payments');
        $this->db->from('transaction');
        $this->db->where('transaction_type_id', '4');
        $this->db->where('status_id', '1');

        $this->db->group_by('account_no_id');
        $sub_query_4 = $this->db->get_compiled_select();

        $this->db->select('saving_account_id,sum(amount) savings_locked');
        $this->db->from('lock_savings_amount');
        $this->db->where('status_id', '1');
        $this->db->group_by('saving_account_id');
        $sub_query_10 = $this->db->get_compiled_select();

        $this->db->select('account_no_id,sum(debit) charges');
        $this->db->from('transaction');
        $this->db->where('status_id', '1');

        $this->db->where('transaction_type_id IN (5,6,7)');
        $this->db->group_by('account_no_id');
        $sub_query_11 = $this->db->get_compiled_select();


        $this->db->select('sum(amount_locked) amount_locked,savings_account_id');
        $this->db->from('loan_guarantor');
        $this->db->where('fms_loan_guarantor.client_loan_id IN', "(SELECT `fms_client_loan`.`id` FROM `fms_client_loan` JOIN $this->max_state_id loan_state ON  loan_state.client_loan_id=`fms_client_loan`.`id` WHERE `state_id` IN (5,6,7,11,12,13,14))", FALSE);
        $this->db->group_by('savings_account_id');
        $sub_query_5 = $this->db->get_compiled_select();

        $this->sub_query_6 = "(SELECT account_id, state_id FROM fms_account_states
                WHERE id in (
                    SELECT MAX(id) from fms_account_states GROUP BY account_id))";

        $this->db->select("a.id, account_no,a.member_id, (ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ ifnull( payments ,0)  + ifnull(savings_locked,0)+ ifnull( amount_locked, 0) +ifnull(charges, 0)) cash_bal,(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ ifnull( payments ,0) +ifnull(charges, 0)) real_bal, sp.mindepositamount,a.deposit_Product_id,"
            . "sp.productname,sp.min_balance,a.interest_rate,a.opening_balance,a.client_type,sp.maxwithdrawalamount, j.state_id,group_name as member_name,sp.description,sp.withdraw_cal_method_id,sp.bal_cal_method_id , sp.mandatory_saving, saving_frequency, saving_made_every,date_opened");
        $this->db->from('savings_account a');
        $this->db->join('fms_savings_product sp', 'sp.id=a.deposit_Product_id', 'LEFT');
        $this->db->join('group g', 'a.member_id = g.id');
        $this->db->join('(' . $sub_query_1 . ') d', 'd.account_no_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_2 . ') e', 'e.account_no_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_3 . ') f', 'f.account_no_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_4 . ') g', 'g.account_no_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_10 . ') m', 'm.saving_account_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_11 . ') n', 'n.account_no_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_5 . ') i', 'i.savings_account_id = a.id', 'LEFT');
        $this->db->join("$this->sub_query_6 j", 'j.account_id=a.id', "LEFT");

        if (isset($_POST['client_id']) && !empty($this->input->post('client_id'))) {
            $this->db->where('a.member_id', $this->input->post('client_id'));
        }
        if (isset($_POST['group_id']) && !empty($this->input->post('group_id'))) {
            $this->db->where('a.member_id', $this->input->post('group_id'));
        }

        if ($filter === FALSE) {

            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($acc_id)) {
                $this->db->where('a.id=' . $acc_id);
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_guarantor_savings2($filter = FALSE, $acc_id = FALSE)
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

        $this->db->select("a.id, c.gender, account_no,a.member_id,b.date_registered, charges, (ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ ifnull( payments ,0) + ifnull(savings_locked,0)+ ifnull( amount_locked, 0) +ifnull(charges, 0) +ifnull( qualifying_amount, 0)+ifnull(min_balance,0)) cash_bal,(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ ifnull( payments ,0)+ifnull(charges, 0)) real_bal, sp.mindepositamount,a.deposit_Product_id, ifnull( transfer ,0) transfers,"
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
        // $this->db->where("c.gender", 1);

        $this->db->join("$this->sub_query_6 j", 'j.account_id=a.id', "LEFT");
        // $this->db->join("$this->sub_query_6 j", 'b.user_id = c.id');
     
        
        $subQuery1 = $this->db->get_compiled_select();

        $this->db->reset_query();

        //second query starts here..............

        $this->db->select("a.id, a.term_length ,account_no,a.member_id,g.date_registered, charges,(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ ifnull( payments ,0) + ifnull(savings_locked,0)+ ifnull( amount_locked, 0) +ifnull(charges, 0) +ifnull( qualifying_amount, 0)) cash_bal,(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ ifnull( payments ,0)+ifnull(charges, 0)) real_bal, sp.mindepositamount,a.deposit_Product_id,ifnull( transfer ,0) transfers,"
            . "sp.productname,sp.min_balance,a.interest_rate,a.opening_balance,a.client_type,sp.maxwithdrawalamount, j.state_id, group_name as member_name,sp.description,sp.producttype,(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)+ ifnull( payments ,0)+ifnull(charges, 0)) y,group_name as name,group_name as drilldown,sp.withdraw_cal_method_id,sp.bal_cal_method_id, sp.mandatory_saving, saving_frequency, saving_made_every,date_opened,ifnull( payments ,0) payments,ifnull( withdraw ,0) withdraws,ifnull(deposit,0) deposits,ifnull( locked_amount ,0) locked_amount, fs.start_date,fs.end_date,fs.qualifying_amount,fs.status,fs.type, ifnull(a.child_id, null) child_id, if(a.child_id,concat(ifnull(cd.firstname, ''), ' ',ifnull(cd.lastname, ''),' '"
            . ", ifnull(cd.othernames, '')), null) child_name");
        $this->db->from('savings_account a');
        $this->db->join('member b', 'a.member_id = b.id');
        $this->db->join('user c', 'b.user_id = c.id');
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

    public function count_loan_guarantor($filter = FALSE)
    {
        $this->db->select('COUNT(client_loan_id) AS loan_guarantor,coalesce(SUM(amount_locked),0)  AS loan_guarantor_value');
        $query = $this->db->from('loan_guarantor')->limit(1, 0);
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('loan_guarantor.client_loan_id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function count_loan_share_guarantor($filter = FALSE)
    {
        $this->db->select('COUNT(client_loan_id) AS loan_share_guarantor,coalesce(SUM(amount_locked),0) AS loan_share_guarantor_value');
        $query = $this->db->from('share_loan_guarantor')->limit(1, 0);
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('share_loan_guarantor.client_loan_id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    // get client loan's guarantor and collateral total
    function get_total_locked_amount()
    {
        $this->db->select('a.client_loan_id,sum(amount_locked) tot_amount_locked, sum(item_value) tot_item_value, ( sum(amount_locked) + sum(item_value) ) total');
        $this->db->from('loan_guarantor a');
        $this->db->from('loan_collateral b', 'a.client_loan_id = b.client_loan_id');
        $this->db->group_by('a.client_loan_id');
        $query = $this->db->get();
        return $query->row_array();
    }


    // Savings
    public function get_member_guaranteed_active_loans_sv($member_id)
    {
        $this->db->select('g.client_loan_id, cl.member_id');
        $this->db->where("l.state_id IN(1,5,6,7,11,12,13,14)");
        $this->db->where('cl.member_id', $member_id);
        $this->db->where('g.status_id', 1);
        $this->db->from('fms_loan_guarantor g');

        $this->db->join('fms_savings_account cl', 'cl.id=g.savings_account_id', 'LEFT');

        $this->db->join($this->max_state_id . ' l', 'l.client_loan_id=g.client_loan_id', 'LEFT');

        $query = $this->db->get();
        return $query->result_array();
    }

    //Shares
    public function get_member_guaranteed_active_loans_sh($member_id)
    {
        $this->db->select('g.client_loan_id, cl.member_id');
        $this->db->where("l.state_id IN(1,5,6,7,11,12,13,14)");
        $this->db->where('cl.member_id', $member_id);
        $this->db->where('g.status_id', 1);
        $this->db->from('fms_share_loan_guarantor g');

        $this->db->join('fms_share_account cl', 'cl.id=g.share_account_id', 'LEFT');

        $this->db->join($this->max_state_id . ' l', 'l.client_loan_id=g.client_loan_id', 'LEFT');

        $query = $this->db->get();
        return $query->result_array();
    }
}
