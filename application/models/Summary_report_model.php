<?php

class Summary_report_model extends CI_Model
{

    private $columns = ["jtr.id", "jtr.ref_no", "jtr.ref_id", "transaction_date", "jty.type_name", "jtr.description", "jtr.date_created", "jtr.status_id", "jtr.reverse_msg", "jtr.reversed_date"];
    private $alias_only_pattern = '/(\s+(as[\s]+)?)((`)?[a-zA-Z0-9_]+(`)?)$/';


    public function __construct()
    {
        $this->load->database();
    }




    public function get_dTable($filter = false)
    {

        $this->db->select('jt.ref_id,jtl.created_by, ABS(SUM(IFNULL(credit_amount,0)-IFNULL(debit_amount,0))) amount, ABS(SUM(IFNULL(credit_amount,0))) as credit_sum,ABS(SUM(IFNULL(debit_amount,0))) as debit_sum');
        $this->db->from("fms_journal_transaction_line jtl");
        $this->db->join("fms_journal_transaction jt", "jt.id=jtl.journal_transaction_id");
        $this->db->where("jtl.status_id =", 1);
        $this->db->where("jt.status_id =", 1);
        if (($this->input->post("member_id")) != NULL) {
            $this->db->where("jtl.created_by = ", $this->input->post("member_id"));
        }
        
        if (($this->input->post("start_date")) != NULL) {
            $this->db->where("jtl.transaction_date >= ", $this->input->post("start_date"));
        }
        if (($this->input->post("end_date")) != NULL) {
            $this->db->where("jtl.transaction_date <= ", $this->input->post("end_date"));
        }

        $this->db->where($filter);

        $query = $this->db->get();
        //print_r($this->db->last_query()); die;
        return $query->result_array();
    }

    public function get_trans($journal_type_id)
    {
        $tt_amount_subquery = "(SELECT `journal_transaction_id` `jt_id`, SUM(`debit_amount`) `tt_amount`, SUM(`credit_amount`) `c_amount` FROM `fms_journal_transaction_line` GROUP BY `journal_transaction_id`) tt_as";
        $this->db->select('jtl.id,jt.id as jt_id,jtl.created_by, jtl.transaction_date,jt.journal_type_id, reference_no,jt.description,u.firstname,u.lastname, reference_id,narrative,jt.status_id, jty.type_name,tt_as.tt_amount,tt_as.c_amount');
        $this->db->from("fms_journal_transaction_line jtl");
        $this->db->join("fms_journal_transaction jt", "jt.id=jtl.journal_transaction_id", 'left');
        $this->db->join("journal_type jty", "jt.journal_type_id=jty.id");
        $this->db->join("user u", "jt.created_by=u.id");
        $this->db->join("$tt_amount_subquery", "jt_id=jt.id");
        $this->db->where("jtl.status_id =", 1);
        $this->db->where("jt.status_id =", 1);
        $this->db->group_by('jt_id');
        if (($this->input->post("member_id")) != NULL) {
            $this->db->where("jtl.created_by = ", $this->input->post("member_id"));
        }

        if (($this->input->post("start_date")) != NULL) {
            $this->db->where("jtl.transaction_date >=", $this->input->post("start_date"));
        }

        if (($this->input->post("end_date")) != NULL) {
            $this->db->where("jtl.transaction_date <=", $this->input->post("end_date"));
        }

        $this->db->where("jt.journal_type_id IN($journal_type_id)");

        $query = $this->db->get();
        //print_r($this->db->last_query());
        return $query->result_array();
    }
}
