<?php
/**
 * Bill Model
 * @author Allan J. Odeke <allanjodeke@gmtconsults.com>
 *  */
class Bill_model extends CI_Model {
    public function __construct() {
        $this->load->database();
    }
    public function get($filter = FALSE) {
        $tt_amount_subquery = "(SELECT `bill_id`, SUM(`amount`) `total_amount` FROM `fms_bill_line` GROUP BY `bill_id`) `lines`";
        $payment_lines_subquery = "(SELECT `bill_id`, SUM(`amount`) `amount_paid` FROM `fms_bill_payment_line` GROUP BY `bill_id`) bpls";

        $this->db->select("bill.*,ac.account_code, ac.account_name,total_amount, amount_paid, supplier_names, supplier_short_name");
        $this->db->select("dac.account_code discount_account_code, dac.account_name discount_account_name");
        //$this->db->select("tax.tax_rate_source_name");
        $this->db->from('bill');
        //$this->db->join("bill_category exc", "exc.id=bill.bill_category_id");
        $this->db->join("accounts_chart ac", "ac.id=bill.liability_account_id");
        $this->db->join("accounts_chart dac", "dac.id=bill.discount_account_id", "LEFT");
        $this->db->join("supplier", "supplier.id=supplier_id");
        $this->db->join("$tt_amount_subquery", "`lines`.`bill_id`=`fms_bill`.`id`");
        $this->db->join("$payment_lines_subquery", "bpls.bill_id=bill.id", "LEFT");
        
        if ($this->input->post("start_date") != NULL && $this->input->post("end_date") != NULL) {
            $start_date = $this->input->post("start_date");
            $end_date = $this->input->post("end_date");
            $date_option = $this->input->post("date_option");
            $where_clause = "(".($date_option?$date_option:"billing_date")." BETWEEN '$start_date' AND '$end_date')";
            $this->db->where($where_clause);
        }
        //$this->db->join("tax tc", "tc.id=bill.applied_tax_id","LEFT");
        if(is_numeric($this->input->post("status_id"))){
            $this->db->where("bill.status_id=" .$this->input->post("status_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("bill.id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
     public function set($bill_attachment_url = NULL) {
        $data = $this->input->post(NULL, TRUE);
        
        $data['billing_date'] = $this->helpers->yr_transformer($data['billing_date']);
        $data['due_date'] = $this->helpers->yr_transformer($data['due_date']);
        $data["attachment_url"] = $bill_attachment_url;
        $data['modified_by'] = $_SESSION['id'];

        if(isset($data['id'])&& is_numeric($data['id'])){
            $this->db->where('id', $data['id']);
            unset($data['id'], $data['tbl'], $data['tbl'],$data["bill_line_item"]);
            return $this->db->update('fms_bill', $data);
        }else{
            unset($data['id'], $data['tbl'], $data['tbl'],$data["bill_line_item"]);
            $data['date_created'] = time();
            $data['created_by'] = $data['modified_by'];
            $this->db->insert('fms_bill', $data);
        return $this->db->insert_id();
        }
    }

    public function deactivate() {
        $data = array(
            'status_id' =>$this->input->post('status_id'),
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('bill', $data);
    }
    public function delete() {
        $data = array(
            'status_id' =>0,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('bill',$data);
    }
    public function abs_delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('bill');
    }
}
