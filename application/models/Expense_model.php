<?php
class Expense_model extends CI_Model {
    public function __construct() {
        $this->load->database();
    }
    public function get($filter = FALSE) {
        $tt_amount_subquery = "(SELECT `expense_id`, SUM(`amount`) `total_amount` FROM `fms_expense_line` GROUP BY `expense_id`) totals";

        $this->db->select("ex.*,ac.account_code, ac.account_name,total_amount, supplier_names");
        //$this->db->select("tax.tax_rate_source_name");
        $this->db->from('expense ex');
        //$this->db->join("expense_category exc", "exc.id=ex.expense_category_id");
        $this->db->join("accounts_chart ac", "ac.id=ex.cash_account_id");
        $this->db->join("supplier", "supplier.id=supplier_id");
        $this->db->join("$tt_amount_subquery", "expense_id=ex.id");
        //$this->db->join("tax tc", "tc.id=ex.transaction_channel_id","LEFT");
        
        if ($this->input->post("start_date") != NULL && $this->input->post("end_date") != NULL) {
            $start_date = $this->input->post("start_date");
            $end_date = $this->input->post("end_date");
            $where_clause = "(payment_date BETWEEN '$start_date' AND '$end_date')";
            $this->db->where($where_clause);
        }
        if(is_numeric($this->input->post("status_id"))){
            $this->db->where("ex.status_id=" .$this->input->post("status_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->select("concat(d.salutation, ' ', d.firstname, ' ', d.lastname, ' ', d.othernames) authorizer_names");
                $this->db->join('staff', 'staff.id = ex.authorizer_id','LEFT');
                $this->db->join('user d', 'd.id=staff.user_id');
                $this->db->where("ex.id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
     public function set($expense_attachment_url = NULL) {
        $data = $this->input->post(NULL, TRUE);
        
        $data['payment_date'] = $this->helpers->yr_transformer($data['payment_date']);
        $data["expense_attachment_url"] = $expense_attachment_url;
        $data['modified_by'] = $_SESSION['id'];

        if(isset($data['id'])&& is_numeric($data['id'])){
            $this->db->where('id', $data['id']);
            unset($data['id'], $data['tbl'], $data['tbl'],$data["expense_line_item"],$data["tax_id"]);
            return $this->db->update('fms_expense', $data);
        }else{
            unset($data['id'], $data['tbl'], $data['tbl'],$data["expense_line_item"],$data["tax_id"]);
            $data['date_created'] = time();
            $data['created_by'] = $data['modified_by'];
            $this->db->insert('fms_expense', $data);
        return $this->db->insert_id();
        }
    }

    public function deactivate() {
        $data = array(
            'status_id' =>$this->input->post('status_id'),
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('expense', $data);
    }
    public function delete() {
        $data = array(
            'status_id' =>0,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('expense',$data);
    }
    public function abs_delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('expense');
    }
}
