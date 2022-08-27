<?php
/**
 * Bill Payment Model
 * @author Allan J. Odeke <allanjodeke@gmtconsults.com>
 *  */
class Bill_payment_model extends CI_Model {
    public function __construct() {
        $this->load->database();
    }
    public function get($filter = FALSE) {
        $where = 1;
        if(is_numeric($this->input->post("bill_id"))){
            $where = "bill_id=" .$this->input->post("bill_id");
        }
        $tt_amount_subquery = "(SELECT `bill_payment_id`, SUM(`amount`) `amount_paid` FROM `fms_bill_payment_line` where $where GROUP BY `bill_payment_id`) `payment_lines`";
        $this->db->select("bill_payment.*,amount_paid");
        $this->db->select("ac.account_code, ac.account_name");
        $this->db->from('bill_payment');
        $this->db->join("accounts_chart ac", "ac.id=bill_payment.cash_account_id");
        $this->db->join("$tt_amount_subquery", "`payment_lines`.`bill_payment_id`=`fms_bill_payment`.`id`");
        if(is_numeric($this->input->post("status_id"))){
            $this->db->where("bill_payment.status_id=" .$this->input->post("status_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("bill_payment.id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
     public function set($bill_payment_attachment_url = NULL) {
        $data = $this->input->post(NULL, TRUE);
        
        $data['payment_date'] = $this->helpers->yr_transformer($data['payment_date']);
        $data["attachment_url"] = $bill_payment_attachment_url;
        $data['modified_by'] = $_SESSION['id'];

        if(isset($data['id'])&& is_numeric($data['id'])){
            $this->db->where('id', $data['id']);
            unset($data['id'], $data['tbl'], $data['tbl'],$data["bill_payment_line"]);
            return $this->db->update('fms_bill_payment', $data);
        }else{
            unset($data['id'], $data['tbl'], $data['tbl'],$data["bill_payment_line"]);
            $data['date_created'] = time();
            $data['created_by'] = $data['modified_by'];
            $this->db->insert('fms_bill_payment', $data);
        return $this->db->insert_id();
        }
    }

    public function deactivate() {
        $data = array(
            'status_id' =>$this->input->post('status_id'),
            'modified_by' =>$_SESSION['id']
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('bill_payment', $data);
    }
    public function delete() {
        $data = array(
            'status_id' =>0,
            'modified_by' =>$_SESSION['id']
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('bill_payment',$data);
    }
    public function abs_delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('bill_payment');
    }
}
