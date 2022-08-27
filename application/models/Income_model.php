<?php

class Income_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $tt_amount_subquery = "(SELECT `income_id`, SUM(`amount`) `total_amount` FROM `fms_income_line` GROUP BY `income_id`) totals";

        $this->db->select("income.*,ac.account_code, ac.account_name,total_amount");
        $this->db->select("concat(fms_user.firstname, ' ', fms_user.lastname, ' ', fms_user.othernames) client_names");
        $this->db->from('income');
        $this->db->join("accounts_chart ac", "ac.id=income.cash_account_id");
        $this->db->join('member', 'member.id = income.client_id', 'LEFT');
        $this->db->join('user', 'user.id=member.user_id', "LEFT");
        $this->db->join("$tt_amount_subquery", "income_id=income.id");
        
        if ($this->input->post("start_date") != NULL && $this->input->post("end_date") != NULL) {
            $start_date = $this->input->post("start_date");
            $end_date = $this->input->post("end_date");
            $where_clause = "(receipt_date BETWEEN '$start_date' AND '$end_date')";
            $this->db->where($where_clause);
        }
        
        if (is_numeric($this->input->post("status_id"))) {
            $this->db->where("income.status_id=" . $this->input->post("status_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->select("concat(d.salutation, ' ', d.firstname, ' ', d.lastname, ' ', d.othernames) receiver_names");
                $this->db->join('staff', 'staff.id = income.receiver_id','LEFT');
                $this->db->join('user d', 'd.id=staff.user_id');
                $this->db->where("income.id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

     public function set($income_attachment_url = NULL) {
        $data = $this->input->post(NULL, TRUE);
        
        $data['receipt_date'] = $this->helpers->yr_transformer($data['receipt_date']);
        $data["attachment_url"] = $income_attachment_url;
        $data['modified_by'] = $_SESSION['id'];

        if(isset($data['id'])&& is_numeric($data['id'])){
            $this->db->where('id', $data['id']);
            unset($data['id'], $data['tbl'], $data['tbl'],$data["income_line_item"],$data["tax_id"]);
            return $this->db->update('income', $data);
        }else{
            unset($data['id'], $data['tbl'], $data['tbl'],$data["income_line_item"],$data["tax_id"]);
            $data['date_created'] = time();
            $data['created_by'] = $data['modified_by'];
            $this->db->insert('income', $data);
        return $this->db->insert_id();
        }
    }

    public function deactivate() {
        $data = array(
            "status_id" => $this->input->post("status_id"),
        );
        $this->db->where("id", $this->input->post("id"));
        return $this->db->update("income", $data);
    }

    public function delete() {
        $data = array(
            "status_id" => 0,
        );
        $this->db->where("id", $this->input->post("id"));
        return $this->db->update("income", $data);
    }
    
    public function abs_delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('income');
    }

}
