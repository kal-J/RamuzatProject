<?php

/**

 * Income Line Model
 *  */
class Income_line_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select("inl.*, ac.account_name,ac.account_code");
        $this->db->from("income_line inl");
        $this->db->join("accounts_chart ac", "ac.id=inl.account_id");

        if ($this->input->post("income_id") != NULL) {
            $this->db->where("income_id", $this->input->post("income_id"));
        }
        if ($this->input->post("status_id") != NULL) {
            $this->db->where("inl.status_id", $this->input->post("status_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('inl.`id`', $filter);
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
        $this->db->select("inl.*, inc.receipt_date, receipt_no, ref_id");
        $this->db->from("income_line inl");
        $this->db->join("income inc", "inc.id=inl.income_id");

        if ($this->input->post("account_id") != NULL) {
            $this->db->where("account_id", $this->input->post("account_id"));
        }
        if ($this->input->post("income_id") != NULL) {
            $this->db->where("income_id", $this->input->post("income_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('`inl`.`id`', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set($income_id, $data = []) {
        
        $ids = $ids2 = [];
        if(is_array($data) && empty($data)){
            $data = $this->input->post('income_line_item');
        }
        foreach ($data as $key => $value) {//it is a new entry, so we insert afresh
            if (isset($value['account_id']) && is_numeric($value['account_id']) && ((isset($value['amount']) && is_numeric($value['amount'])))) {
                $value['income_id'] = $income_id;
                $value['modified_by'] = $_SESSION['id'];
                if (isset($value['id']) && is_numeric($value['id']) && $value['id']!=='') {
                    $ids[] = $value['id'];
                    $this->db->where('id', $value['id']);
                    unset($value['id']);
                    $this->db->update('income_line', $value);
                } else {
                    unset($value['id']);
                    $value['created_by'] = $value['modified_by'];
                    $value['date_created'] = time();
                    $this->db->insert('income_line', $value);
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
            $this->db->where('income_id', $this->input->post('id'));
            return $this->db->delete('income_line');
        }
        return true;
    }

    //deletes entries given a particular where clause
    public function delete() {
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        if ($this->input->post('income_id') !== NULL && is_numeric($this->input->post('income_id'))) {
            $this->db->where('income_id', $this->input->post('income_id'));
        }
        return $this->db->delete('income_line');
    }

    public function change_status() {
        $data = array(
            'status_id' => 0
        );
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        if ($this->input->post('income_id') !== NULL && is_numeric($this->input->post('income_id'))) {
            $this->db->where('income_id', $this->input->post('income_id'));
        }

        return $this->db->update('income_line', $data);
    }

}
