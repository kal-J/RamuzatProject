<?php

class TransactionChannel_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = false) {
        $this->db->select("c.*, ac.account_code,concat(s.staff_no,' | ',u.lastname,' ',u.firstname,' ', u.othernames) staff_name, ac.account_name,linked_account_id,u.id as user_id");
        $this->db->from('transaction_channel c');
        $this->db->join("accounts_chart ac", "ac.id=c.linked_account_id","LEFT");
        $this->db->join('staff s', 's.id=c.staff_id', 'left');
        $this->db->join('user u', 'u.id=s.user_id', 'left');
        if(is_numeric($this->input->post('status_id'))){
            $this->db->where('c.status_id', $this->input->post('status_id'));
        }else{
            $this->db->where('c.status_id', 1);
        }
        if(isset($_SESSION['staff_id'])) {
            $this->db->where('c.staff_id', $_SESSION['staff_id']);
        }
        
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('c.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
    public function get2($filter = false) {
        $this->db->select("c.*, ac.account_code,concat(s.staff_no,' | ',u.lastname,' ',u.firstname,' ', u.othernames) staff_name, ac.account_name,linked_account_id,u.id as user_id");
        $this->db->from('transaction_channel c');
        $this->db->join("accounts_chart ac", "ac.id=c.linked_account_id","LEFT");
        $this->db->join('staff s', 's.id=c.staff_id', 'left');
        $this->db->join('user u', 'u.id=s.user_id', 'left');
        if(is_numeric($this->input->post('status_id'))){
            $this->db->where('c.status_id', $this->input->post('status_id'));
        }else{
            $this->db->where('c.status_id', 1);
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('c.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_tchanel($id = false) {
        $this->db->from('transaction_channel c');
        $this->db->select('*');
        $this->db->where('c.status_id', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function set() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['tbl']);
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by']= $_SESSION['id'];

        $this->db->insert('transaction_channel', $data);
        return $this->db->insert_id();
    }

    public function update() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['tbl']);
        $data['date_modified'] = time();
        $data['modified_by'] = $_SESSION['id'];

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('transaction_channel', $data);
        } else {
            return false;
        }
    }

    public function delete() {
        $data = array(
            'status_id' => 0,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('transaction_channel', $data);
    }

    public function deactivate() {
        $data = array(
            'status_id' => 2,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('transaction_channel', $data);
    }

}

?>