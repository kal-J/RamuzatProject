<?php

class Loan_attached_saving_accounts_model extends CI_Model {

    public function __construct() {
        $this->load->database();
        $this->table = 'loan_attached_saving_accounts';
    }

    public function get($filter = FALSE) {
        $this->db->select("a.id, saving_account_id,account_no, loan_id, a.status_id");
        $this->db->from("$this->table a");
        $this->db->join("savings_account b", "a.saving_account_id = b.id");
        if ($this->input->post('status_id') !== NULL && !empty($this->input->post('status_id'))) {
            $this->db->where('a.status_id', $this->input->post('status_id'));
        }else{
            $this->db->where('a.status_id', 1);
        }
        if ($this->input->post('loan_id') !== NULL && !empty($this->input->post('loan_id'))) {
            $this->db->where('a.loan_id', $this->input->post('loan_id'));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.id',$filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set($loan_id = false) {
        $query=false;
         if ($loan_id !== false) {
            $client_loan_id=$loan_id;
        }else{
            $client_loan_id=$this->input->post('loan_id');
        }
        $savingAccs = $this->input->post('savingAccs');

        foreach ($savingAccs as $key => $value) {
            $value['date_created'] = time();
            $value['loan_id'] = $client_loan_id;
            $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
            $query=$this->db->insert('loan_attached_saving_accounts', $value);
        }
        return $query;
    }


     public function set4($loan_id = false,$savingAccs=false) {
        $query=false;
            $client_loan_id=$loan_id;
            $value['date_created'] = time();
            $value['loan_id'] = $client_loan_id;
            $value['saving_account_id'] = $saving_account_id;
            $value['created_by'] = $value['modified_by'] = 1;
            $query=$this->db->insert('loan_attached_saving_accounts', $value);
        return $query;
    }

    public function set2($sent_data){
        $query = $this->db->insert($this->table, $sent_data);
        return $this->db->insert_id();
    }

    public function duplicate_entry($new_loan_id){
        $sql_query="INSERT INTO fms_loan_attached_saving_accounts(loan_id, saving_account_id, status_id, date_created, created_by, modified_by) SELECT ".$new_loan_id.", saving_account_id, status_id, UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP('1970-01-01 03:00:00'),".$_SESSION['id'].",".$_SESSION['id']." FROM fms_loan_attached_saving_accounts WHERE status_id=1 AND loan_id =".$this->input->post('linked_loan_id');
        $query = $this->db->query($sql_query);
        return $this->db->insert_id();
        // print_r($this->db->last_query()); die;
    }


      public function get2($filter = FALSE) {
        $this->db->select("a.id, share_account_id,share_account_no, loan_id, a.status_id");
        $this->db->from("loan_attached_share_accounts a");
        $this->db->join("share_account b", "a.share_account_id = b.id");
        if ($this->input->post('status_id') !== NULL && !empty($this->input->post('status_id'))) {
            $this->db->where('a.status_id', $this->input->post('status_id'));
        }else{
            $this->db->where('a.status_id', 1);
        }
        if ($this->input->post('loan_id') !== NULL && !empty($this->input->post('loan_id'))) {
            $this->db->where('a.loan_id', $this->input->post('loan_id'));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.id',$filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set3($loan_id = false) {
        $query=false;
         if ($loan_id !== false) {
            $client_loan_id=$loan_id;
        }else{
            $client_loan_id=$this->input->post('loan_id');
        }
        $savingAccs = $this->input->post('shareAccs');

        foreach ($savingAccs as $key => $value) {
            $value['date_created'] = time();
            $value['loan_id'] = $client_loan_id;
            $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
            $query=$this->db->insert('loan_attached_share_accounts', $value);
        }
        return $query;
    }


    public function duplicate_entry2($new_loan_id){
        $sql_query="INSERT INTO fms_loan_attached_share_accounts(loan_id, share_account_id, status_id, date_created, created_by, modified_by) SELECT ".$new_loan_id.", share_account_id, status_id, UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP('1970-01-01 03:00:00'),".$_SESSION['id'].",".$_SESSION['id']." FROM fms_loan_attached_share_accounts WHERE status_id=1 AND loan_id =".$this->input->post('linked_loan_id');
        $query = $this->db->query($sql_query);
        return $this->db->insert_id();
        // print_r($this->db->last_query()); die;
    }

    public function change_status_by_id($id = false) {

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

    public function delete() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update($this->table,['status_id'=>"0"]);
    }

    public function attach_savings_accounts_to_loans()
    {
        $this->db->trans_start();

        $this->db->select('cl.id AS client_loan_id, sa.id AS saving_account_id, loan_no');
        $this->db->from('fms_client_loan cl');
        $this->db->join('fms_savings_account sa', "sa.member_id = cl.member_id", "LEFT");
        $query = $this->db->get();
        $loans_savings_accounts = $query->result_array();
        $this->db->trans_complete();

        foreach ($loans_savings_accounts as $key => $loan) {
            $this->db->trans_start();
            $data = [
                'saving_account_id' => $loan['saving_account_id'],
                'loan_id' => $loan['client_loan_id'],
                'created_by' => (isset($_SESSION['id']))?$_SESSION['id']:1,
                'date_created' => time(),
                'amount_locked' => 0,
                'status_id' => 1,
            ];
            $this->db->insert('fms_loan_attached_saving_accounts', $data);
            $this->db->trans_complete();

            print_r("Savings account attached to " . $loan['loan_no'] . "\n");
        }
    }

}
