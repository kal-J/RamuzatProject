<?php

/**
 * Invoice Model
 * @author Allan J. Odeke <allanjodeke@gmtconsults.com>
 *  */
class Invoice_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $tt_amount_subquery = "(SELECT `invoice_id`, SUM(`amount`) `total_amount` FROM `fms_invoice_line` GROUP BY `invoice_id`) `lines`";
        $payment_lines_subquery = "(SELECT `invoice_id`, SUM(`amount`) `amount_paid` FROM `fms_invoice_payment_line` GROUP BY `invoice_id`) bpls";

        $this->db->select("invoice.*,ac.account_code, ac.account_name,total_amount, amount_paid");
        $this->db->select("dac.account_code discount_account_code, dac.account_name discount_account_name");
        $this->db->select("concat(fms_user.firstname, ' ', fms_user.lastname, ' ', fms_user.othernames) client_names");
        //$this->db->select("tax.tax_rate_source_name");
        $this->db->from('invoice');
        $this->db->join("accounts_chart ac", "ac.id=invoice.receivable_account_id");
        $this->db->join("accounts_chart dac", "dac.id=invoice.discount_account_id", "LEFT");
        $this->db->join('member', 'member.id = invoice.client_id', 'LEFT');
        $this->db->join('user', 'user.id=member.user_id', "LEFT");
        $this->db->join("$tt_amount_subquery", "`lines`.`invoice_id`=`fms_invoice`.`id`");
        $this->db->join("$payment_lines_subquery", "bpls.invoice_id=invoice.id", "LEFT");
        //$this->db->join("tax tc", "tc.id=invoice.applied_tax_id","LEFT");
        
        if ($this->input->post("start_date") != NULL && $this->input->post("end_date") != NULL) {
            $start_date = $this->input->post("start_date");
            $end_date = $this->input->post("end_date");
            $date_option = $this->input->post("date_option");
            $where_clause = "(".($date_option?$date_option:"invoice_date")." BETWEEN '$start_date' AND '$end_date')";
            $this->db->where($where_clause);
        }
        if (is_numeric($this->input->post("status_id"))) {
            $this->db->where("invoice.status_id=" . $this->input->post("status_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("invoice.id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set($invoice_attachment_url = NULL) {
        $data = $this->input->post(NULL, TRUE);

        $data['invoice_date'] = $this->helpers->yr_transformer($data['invoice_date']);
        $data['due_date'] = $this->helpers->yr_transformer($data['due_date']);
        $data["attachment_url"] = $invoice_attachment_url;
        $data['modified_by'] = $_SESSION['id'];

        if (isset($data['id']) && is_numeric($data['id'])) {
            $this->db->where('id', $data['id']);
            unset($data['id'], $data['tbl'], $data['tbl'], $data["invoice_line_item"]);
            return $this->db->update('fms_invoice', $data);
        } else {
            unset($data['id'], $data['tbl'], $data['tbl'], $data["invoice_line_item"]);
            $data['date_created'] = time();
            $data['created_by'] = $data['modified_by'];
            $this->db->insert('fms_invoice', $data);
            return $this->db->insert_id();
        }
    }

    public function deactivate() {
        $data = array(
            'status_id' => $this->input->post('status_id'),
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('invoice', $data);
    }

    public function delete() {
        $data = array(
            'status_id' => 0,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('invoice', $data);
    }

    public function abs_delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('invoice');
    }

}
