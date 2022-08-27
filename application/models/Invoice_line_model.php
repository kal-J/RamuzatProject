<?php

/**
 * Invoice Line Model
 * @author Allan J. Odeke <allanjodeke@gmtconsults.com>
 *  */
class Invoice_line_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select("il.*, ac.account_name,ac.account_code");
        $this->db->from("invoice_line il");
        $this->db->join("accounts_chart ac", "ac.id=il.account_id");

        if ($this->input->post("invoice_id") != NULL) {
            $this->db->where("invoice_id", $this->input->post("invoice_id"));
        }
        if ($this->input->post("status_id") != NULL) {
            $this->db->where("il.status_id", $this->input->post("status_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('il.`id`', $filter);
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
        $this->db->select("invl.*, invoice.invoice_date, receipt_no");
        $this->db->from("invoice_line invl");
        $this->db->join("invoice", "invoice.id=invl.invoice_id");

        if ($this->input->post("account_id") != NULL) {
            $this->db->where("account_id", $this->input->post("account_id"));
        }
        if ($this->input->post("invoice_id") != NULL) {
            $this->db->where("invoice_id", $this->input->post("invoice_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('`invl`.`id`', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set($invoice_id, $data = []) {
        
        $ids = $ids2 = [];
        if(is_array($data) && empty($data)){
            $data = $this->input->post('invoice_line_item');
        }
        foreach ($data as $key => $value) {//it is a new entry, so we insert afresh
            if (isset($value['account_id']) && is_numeric($value['account_id']) && ((isset($value['amount']) && is_numeric($value['amount'])))) {
                $value['invoice_id'] = $invoice_id;
                $value['modified_by'] = $_SESSION['id'];
                if (isset($value['id']) && is_numeric($value['id']) && $value['id']!=='') {
                    $ids[] = $value['id'];
                    $this->db->where('id', $value['id']);
                    unset($value['id']);
                    $this->db->update('invoice_line', $value);
                } else {
                    unset($value['id']);
                    $value['created_by'] = $value['modified_by'];
                    $value['date_created'] = time();
                    $this->db->insert('invoice_line', $value);
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
            $this->db->where('invoice_id', $this->input->post('id'));
            return $this->db->delete('invoice_line');
        }
        return true;
    }

    //deletes entries given a particular where clause
    public function delete() {
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        if ($this->input->post('invoice_id') !== NULL && is_numeric($this->input->post('invoice_id'))) {
            $this->db->where('invoice_id', $this->input->post('invoice_id'));
        }
        return $this->db->delete('invoice_line');
    }

    public function change_status() {
        $data = array(
            'status_id' => 0
        );
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        if ($this->input->post('invoice_id') !== NULL && is_numeric($this->input->post('invoice_id'))) {
            $this->db->where('invoice_id', $this->input->post('invoice_id'));
        }
        return $this->db->update('invoice_line', $data);
    }

}
