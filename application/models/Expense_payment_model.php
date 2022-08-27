<?php

class Expense_payment_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select("ep.*, exp.description, invoice_no, invoice_date, tc.channel_name");
        $this->db->from('expense_payment ep');
        $this->db->join("transaction_channel tc", "tc.id=ep.transaction_channel_id","LEFT");
        $this->db->join("expenses exp", "exp.id=ep.expense_id");
        if (is_numeric($this->input->post("status_id"))) {
            $this->db->where("ep.status_id=" . $this->input->post("status_id"));
        }
        if (is_numeric($this->input->post("expense_id"))) {
            $this->db->where("ep.expense_id=" . $this->input->post("expense_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("ep.id=" . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set($data = array()) {
        if (empty($data)) {
            $data = $this->input->post(NULL, TRUE);
            unset($data['id'], $data['creditor_account_id'],  $data['tbl']);
        }
        //Transaction date
        $transaction_date = explode('-', $data['transaction_date'], 3);
        $data['transaction_date'] = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] =$_SESSION['id'];

        $this->db->insert("expense_payment", $data);
        return $this->db->insert_id();
    }

    public function update() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['tbl']);
        $data['modified_by'] =$_SESSION['id'];

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('expense_payment', $data);
        } else {
            return false;
        }
    }

    public function deactivate() {
        $data = array(
            'status_id' => $this->input->post('status_id'),
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('expense_payment', $data);
    }

    public function delete() {
        $data = array(
            'status_id' => 0,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('expense_payment', $data);
    }

}
