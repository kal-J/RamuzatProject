<?php

class Group_member_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->from("group_member");

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('`id`', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get2($filter = FALSE) {
        $this->db->select("gm.*, client_no");
        $this->db->from("group_member gm");
        $this->db->join("member m", "m.id=gm.member_id");

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('`id`', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    
    public function set() {
        $group_member = array_unique($this->input->post('group_member'), SORT_REGULAR);
        foreach ($group_member as $key => $value) {//it is a new entry, so we insert afresh
            if (isset($value['member_id']) && is_numeric($value['member_id']) && isset($value['group_id']) && is_numeric($value['group_id'])) {
                $value['date_created'] = time();
                $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
                $this->db->insert('group_member', $value);
                $group_membership_id = $this->db->insert_id();
            }
        }
        return true;
    }

    //deletes members given a particular where clause
    public function delete() {
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        if ($this->input->post('group_id') !== NULL && is_numeric($this->input->post('group_id'))) {
            $this->db->where('group_id', $this->input->post('group_id'));
        }
        return $this->db->delete('group_member');
    }

    public function change_status() {
        $data = array(
            'status_id' => 0
        );
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        if ($this->input->post('group_id') !== NULL && is_numeric($this->input->post('group_id'))) {
            $this->db->where('group_id', $this->input->post('group_id'));
        }

        return $this->db->update('group_member', $data);
    }

    public function mark_group_leader() {
        $data = array(
            'group_leader' =>$this->input->post('status')
        );
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
       

        return $this->db->update('group_member', $data);
    }

    public function get_group_member_savings($filter = FALSE, $acc_id = FALSE) {
        $this->db->select('group_member_id,t.transaction_date, sum(t.credit) deposit, SUM(debit) charge_amount');
        $this->db->from('transaction t');
        if ($this->input->post('account_no_id') !== NULL || is_numeric($acc_id)) {
            $account_no_id = is_numeric($acc_id)?$acc_id:$this->input->post('account_no_id');
            $this->db->where('account_no_id', $account_no_id);
        }
        $this->db->where('transaction_type_id', 2);
        $this->db->group_by('group_member_id');
        $sub_query_2 = $this->db->get_compiled_select();
        
        $this->db->select("gm.id,gm.member_id,group_id,((ifnull( deposit ,0) ) - ifnull(charge_amount, 0)) real_bal,transaction_date, group_name, group_leader,"
                . "concat( ifnull(salutation,''),' ',firstname,' ', lastname,' ', othernames) AS member_name");

        $this->db->from('group_member gm');
        $this->db->join("group g", "g.id = gm.group_id");
        $this->db->join("member m", "m.id = gm.member_id");
        $this->db->join("user u", "u.id= m.user_id ");
        $this->db->join('(' . $sub_query_2 . ') tdeposit', "tdeposit.group_member_id = gm.id", "LEFT");

        if ($this->input->post('group_id') !== NULL) {
            $this->db->where('g.id=', $this->input->post('group_id'));
        }
        if (is_numeric($this->input->post('status_id'))) {
            $this->db->where('group_member.status_id=', $this->input->post('status_id'));
        }

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('gm.id', $filter);
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
