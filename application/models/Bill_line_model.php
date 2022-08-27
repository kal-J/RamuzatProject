<?php

/**
 * Bill Line Model
 * @author Allan J. Odeke <allanjodeke@gmtconsults.com>
 *  */
class Bill_line_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select("bl.*, ac.account_name,ac.account_code");
        $this->db->from("bill_line bl");
        $this->db->join("accounts_chart ac", "ac.id=bl.account_id");

        if ($this->input->post("bill_id") != NULL) {
            $this->db->where("bill_id", $this->input->post("bill_id"));
        }
        if ($this->input->post("status_id") != NULL) {
            $this->db->where("bl.status_id", $this->input->post("status_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('bl.`id`', $filter);
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
        $this->db->select("bl.*, bill.billing_date, ref_no");
        $this->db->from("bill_line bl");
        $this->db->join("bill", "bill.id=bl.bill_id");

        if ($this->input->post("account_id") != NULL) {
            $this->db->where("account_id", $this->input->post("account_id"));
        }
        if ($this->input->post("bill_id") != NULL) {
            $this->db->where("bill_id", $this->input->post("bill_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('`bl`.`id`', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set($bill_id, $data = []) {
        
        $ids = $ids2 = [];
        if(is_array($data) && empty($data)){
            $data = $this->input->post('bill_line_item');
        }
        foreach ($data as $key => $value) {//it is a new entry, so we insert afresh
            if (isset($value['account_id']) && is_numeric($value['account_id']) && ((isset($value['amount']) && is_numeric($value['amount'])))) {
                $value['bill_id'] = $bill_id;
                $value['modified_by'] = $_SESSION['id'];
                if (isset($value['id']) && is_numeric($value['id']) && $value['id']!=='') {
                    $ids[] = $value['id'];
                    $this->db->where('id', $value['id']);
                    unset($value['id']);
                    $this->db->update('bill_line', $value);
                } else {
                    unset($value['id']);
                    $value['created_by'] = $value['modified_by'];
                    $value['date_created'] = time();
                    $this->db->insert('bill_line', $value);
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
            $this->db->where('bill_id', $this->input->post('id'));
            return $this->db->delete('bill_line');
        }
        return true;
    }

    //deletes entries given a particular where clause
    public function delete() {
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        if ($this->input->post('bill_id') !== NULL && is_numeric($this->input->post('bill_id'))) {
            $this->db->where('bill_id', $this->input->post('bill_id'));
        }
        return $this->db->delete('bill_line');
    }

    public function change_status() {
        $data = array(
            'status_id' => 0
        );
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        if ($this->input->post('bill_id') !== NULL && is_numeric($this->input->post('bill_id'))) {
            $this->db->where('bill_id', $this->input->post('bill_id'));
        }

        return $this->db->update('bill_line', $data);
    }

}
