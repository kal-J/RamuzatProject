<?php

/**

 * Expense Line Model
 *  */
class Expense_line_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select("exl.*, ac.account_name,ac.account_code");
        $this->db->from("expense_line exl");
        $this->db->join("accounts_chart ac", "ac.id=exl.account_id");

        if ($this->input->post("expense_id") != NULL) {
            $this->db->where("expense_id", $this->input->post("expense_id"));
        }
        if ($this->input->post("status_id") != NULL) {
            $this->db->where("exl.status_id", $this->input->post("status_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('exl.`id`', $filter);
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
        $this->db->select("exl.*, ex.payment_date, receipt_no, ref_id");
        $this->db->from("expense_line exl");
        $this->db->join("expense ex", "ex.id=exl.expense_id");

        if ($this->input->post("account_id") != NULL) {
            $this->db->where("account_id", $this->input->post("account_id"));
        }
        if ($this->input->post("expense_id") != NULL) {
            $this->db->where("expense_id", $this->input->post("expense_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('`exl`.`id`', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set($expense_id, $data = []) {
        
        $ids = $ids2 = [];
        if(is_array($data) && empty($data)){
            $data = $this->input->post('expense_line_item');
        }
        foreach ($data as $key => $value) {//it is a new entry, so we insert afresh
            if (isset($value['account_id']) && is_numeric($value['account_id']) && ((isset($value['amount']) && is_numeric($value['amount'])))) {
                $value['expense_id'] = $expense_id;
                $value['modified_by'] = $_SESSION['id'];
                if (isset($value['id']) && is_numeric($value['id']) && $value['id']!=='') {
                    $ids[] = $value['id'];
                    $this->db->where('id', $value['id']);
                    unset($value['id']);
                    $this->db->update('expense_line', $value);
                } else {
                    unset($value['id']);
                    $value['created_by'] = $value['modified_by'];
                    $value['date_created'] = time();
                    $this->db->insert('expense_line', $value);
                    $ids2[] = $this->db->insert_id();
                }
            }
        }
        return $this->update_delete($ids,$ids2);
    }

    //deletes entries given a particular where clause
    private function update_delete($ids = false, $ids2 = false) {
        if ($ids !== false && !empty($ids) && is_numeric($this->input->post('id'))) {
            $this->db->where_not_in('id', $ids);
            if($ids2 !== false && !empty($ids2)){
                $this->db->where_not_in('id', $ids2);
            }
            $this->db->where('expense_id', $this->input->post('id'));
            return $this->db->delete('expense_line');
        }
        return true;
    }

    //deletes entries given a particular where clause
    public function delete() {
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        if ($this->input->post('expense_id') !== NULL && is_numeric($this->input->post('expense_id'))) {
            $this->db->where('expense_id', $this->input->post('expense_id'));
        }
        return $this->db->delete('expense_line');
    }

    public function change_status() {
        $data = array(
            'status_id' => 0
        );
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        if ($this->input->post('expense_id') !== NULL && is_numeric($this->input->post('expense_id'))) {
            $this->db->where('expense_id', $this->input->post('expense_id'));
        }

        return $this->db->update('expense_line', $data);
    }

}
