<?php
/**
 * Invoice Line Model
 * @author Allan J. Odeke <allanjodeke@gmtconsults.com>
 *  */
class Invoice_payment_line_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select("ipl.*, ac.account_name,ac.account_code");
        $this->db->from("invoice_payment_line ipl");
        $this->db->join("accounts_chart ac", "ac.id=ipl.account_id");

        if ($this->input->post("invoice_payment_id") != NULL) {
            $this->db->where("invoice_payment_id", $this->input->post("invoice_payment_id"));
        }
        if ($this->input->post("status_id") != NULL) {
            $this->db->where("ipl.status_id", $this->input->post("status_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('ipl.`id`', $filter);
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
        $this->db->select("ipl.*");
        $this->db->from("invoice_payment_line ipl");

        if ($this->input->post("invoice_payment_id") != NULL) {
        $this->db->select("invoice.liability_account_id, invoice_date, account_name, account_code, ref_no");
        $this->db->join("invoice", "invoice.id=ipl.invoice_id");
        $this->db->join("accounts_chart ac", "ac.id=invoice.liability_account_id");
            $this->db->where("invoice_payment_id", $this->input->post("invoice_payment_id"));
        }
        if ($this->input->post("invoice_id") != NULL) {
        $this->db->select("ip.payment_date, ref_no");
            $this->db->join("invoice_payment ip", "ip.id=ipl.invoice_payment_id");
            $this->db->where("invoice_id", $this->input->post("invoice_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('`ipl`.`id`', $filter);
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
            $data = $this->input->post('invoice_payment_line');
        }
        foreach ($data as $key => $value) {//it is a new entry, so we insert afresh
            if (isset($value['invoice_id']) && is_numeric($value['invoice_id']) && ((isset($value['amount']) && is_numeric($value['amount'])))) {
                $value['invoice_payment_id'] = $invoice_id;
                $value['modified_by'] = $_SESSION['id'];
                if (isset($value['id']) && is_numeric($value['id']) && $value['id']!=='') {
                    $ids[] = $value['id'];
                    $this->db->where('id', $value['id']);
                    unset($value['id'],$value['receivable_account_id']);
                    $this->db->update('invoice_payment_line', $value);
                } else {
                    unset($value['id']);
                    $value['created_by'] = $value['modified_by'];
                    $value['date_created'] = time();
                    unset($value['id'],$value['receivable_account_id']);
                    $this->db->insert('invoice_payment_line', $value);
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
            $this->db->where('invoice_payment_id', $this->input->post('id'));
            return $this->db->delete('invoice_payment_line');
        }
        return true;
    }

    //deletes entries given a particular where clause
    public function delete() {
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        if ($this->input->post('invoice_payment_id') !== NULL && is_numeric($this->input->post('invoice_payment_id'))) {
            $this->db->where('invoice_payment_id', $this->input->post('invoice_payment_id'));
        }
        return $this->db->delete('invoice_payment_line');
    }

    public function change_status() {
        $data = array(
            'status_id' => 0
        );
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        if ($this->input->post('invoice_payment_id') !== NULL && is_numeric($this->input->post('invoice_payment_id'))) {
            $this->db->where('invoice_payment_id', $this->input->post('invoice_payment_id'));
        }

        return $this->db->update('invoice_payment_line', $data);
    }

}
