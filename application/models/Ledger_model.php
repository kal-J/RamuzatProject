<?php
/**
 * Description of ledger model
 *
 * @author reagan
 */
class Ledger_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }
    public function get($filter = FALSE) {
        $this->db->select('at.id,at.debit_account_id,at.credit_account_id,debit_ac.id as account_id,at.amount,at.narrative,at.ref_number,at.transaction_date,at.status_id,account_name,1 side');
        $this->db->from('fms_account_transaction at');
        $this->db->join('accounts_chart debit_ac','debit_ac.id=at.debit_account_id');
        if ($this->input->post('organisation_id') !== NULL) {
            $this->db->where("organisation_id = " . $this->input->post('organisation_id'));
        }
        if (is_numeric($this->input->post('account_id'))) {
            $this->db->where("at.debit_account_id = " . $this->input->post('account_id'));
        }
        $query_debits = $this->db->get_compiled_select();
        $this->db->select('at.id as trans_id,at.debit_account_id,at.credit_account_id, credit_ac.id as account_id,at.amount,at.narrative,at.ref_number,at.transaction_date,at.status_id,account_name,2 side');
        $this->db->from('fms_account_transaction at');
        $this->db->join('accounts_chart credit_ac','credit_ac.id=at.credit_account_id');
        if (is_numeric($this->input->post('account_id'))) {
            $this->db->where("at.credit_account_id = " . $this->input->post('account_id'));
        }
        $query_credits = $this->db->get_compiled_select();
      
        if ($filter === FALSE) {
            $query = $this->db->query($query_debits . ' UNION ' . $query_credits . '  order by `id` desc');
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('at.debit_account_id='.$filter);
                $this->db->or_where('at.credit_account_id='.$filter);
                $query = $this->db->query($query_debits . ' UNION ' . $query_credits . '  order by `id` desc');
                //print_r($this->db->get_compiled_select());die();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                return $query->result_array();
            }
        }
    }
    public function get_single_ledger($filter = FALSE) {
        $query = $this->db->query('SELECT t.id,t.narrative,t.ref_number,t.transaction_date,ac.account_name,t.amount as debit_amount,null as credit_amount '
                . 'FROM fms_accounts_chart ac JOIN fms_account_transaction t ON t.debit_account_id=ac.id '
                . 'WHERE ac.id ='.$filter.''
                . ' UNION '
                . 'SELECT t.id,t.narrative,t.ref_number,t.transaction_date,ac.account_name,null as debit_amount, t.amount as credit_amount '
                . 'FROM fms_accounts_chart ac JOIN fms_account_transaction t ON t.credit_account_id=ac.id '
                . 'WHERE ac.id ='.$filter);
        return $query->result_array();
    }
    public function get_ledger_accounts($filter = FALSE) {
        $this->db->select('*');
        $this->db->from('accounts_chart');
        $this->db->order_by('accounts_chart.account_code','asc');
        if (is_numeric($filter)) {
            $this->db->where('accounts_chart.id='.$filter);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $query = $this->db->get();
            return $query->result_array();
        }
    }

     public function set() {
        $data = $this->input->post(NULL, TRUE);
        $transaction_date = explode('-', $this->input->post('transaction_date'), 3);
        $data['transaction_date'] = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
        
        unset($data['id'], $data['tbl']);
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] = $_SESSION['id'];

        $this->db->insert('fms_account_transaction', $data);
        return $this->db->insert_id();
    }

    public function update() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        $transaction_date = explode('-', $this->input->post('transaction_date'), 3);
        $data['transaction_date'] = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
        unset($data['id'], $data['tbl']);
        $data['date_modified'] = time();
        $data['modified_by'] = $_SESSION['id'];

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('fms_account_transaction', $data);
        } else {
            return false;
        }
    }

    public function auto_transaction($incoming_data) {
        $transaction_date = explode('-', $this->input->post($incoming_data["transaction_date"]), 3);
        $data = array(
            //"debit_account_id" => $this->input->post($incoming_data["debit_account_id"]),
            "credit_account_id" => $this->input->post($incoming_data["credit_account_id"]),
            "amount" => $this->input->post($incoming_data["amount"]),
            "transaction_date" => count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null,
            "narrative" => $this->input->post($incoming_data["narrative"]),
            "date_created" => time(),
            "created_by" => $_SESSION["id"],
            "modified_by" =>$_SESSION["id"]
        );
        $this->db->insert("account_transaction", $data);
        return $this->db->insert_id();
    }
   
}
