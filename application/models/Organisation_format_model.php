<?php
class Organisation_format_model extends CI_Model {

    public function __construct() {
        $this->fields = ["account_format","account_format_initials","loan_format","loan_format_initials", "group_loan_format","group_loan_format_initials","client_format","client_format_initials", "staff_format","staff_format_initials",
        "group_format","group_format_initials","share_format","share_format_initials","partner_format","fixed_dep_format","fixed_dep_format_initials"];
        $this->load->database();
    }

    public function get_formats($filter = FALSE) {
        $this->db->select('*');
        $this->db->from('fms_organisation');
        if ($filter === FALSE) {
            $this->db->where('id', $_SESSION['organisation_id']);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_transaction_format($filter = FALSE) {
       
        $this->db->select('id, name, org_initial,  counter_loan_ref_no,counter_applied_loan_fees,counter_applied_member_fees, counter_applied_share_fees,
        letter_applied_member_fees,letter_applied_loan_fees,letter_applied_share_fees,letter_loan_ref_no');
        $this->db->from('fms_organisation');
        if ($filter === FALSE) {
            $this->db->where('id', $_SESSION['organisation_id']);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    } 

    public function get_format_types($filter = FALSE, $select_fields=[]) {
        $this->db->select(implode(",", empty($select_fields)?$this->fields:$select_fields));
        $this->db->from('fms_organisation');
        if ($filter === FALSE) {
            $this->db->where('id', $_SESSION['organisation_id']);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('id',$_SESSION['organisation_id']);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set_format_type() {
        $format_cat = $this->input->post("format_cat");
        $format_type = $this->input->post("format_type");
        $org_id = $_SESSION['organisation_id'];
        if (empty($org_id)) {
            return false;
        } else {
            $this->db->where('id', $org_id);
            return $this->db->update('fms_organisation', [$this->fields[$format_cat-1] => $format_type]);
        }
    }
    public function set_formats() {
        $update_data = [
            "account_format"=>$this->input->post("account_format"),
            "account_format_initials"=>$this->input->post("account_format_initials"),
            "loan_format"=>$this->input->post("loan_format"),
            "loan_format_initials"=>$this->input->post("loan_format_initials"),
            "client_format"=>$this->input->post("client_format"),
            "client_format_initials"=>$this->input->post("client_format_initials"),
            "staff_format"=>$this->input->post("staff_format"),
            "staff_format_initials"=>$this->input->post("staff_format_initials"),
            "group_format_initials"=>$this->input->post("group_format_initials"),
            "share_format"=>$this->input->post("share_format"),
            "share_format_initials"=>$this->input->post("share_format_initials"),
            "partner_format"=>$this->input->post("partner_format"),
            "group_loan_format_initials"=>$this->input->post("group_loan_format_initials"),
            "group_loan_format"=>$this->input->post("group_loan_format"),
            "fixed_dep_format_initials"=>$this->input->post("fixed_dep_format_initials")
                ];
        $org_id = $_SESSION['organisation_id'];
        if (empty($org_id)) {
            return false;
        } else {
            $this->db->where('id', $org_id);
           return $this->db->update('fms_organisation', $update_data);
             
        }
    }

    public function setdefault_partner_no() {
        if (empty($this->input->post('org_id')) || empty($this->input->post('id'))) {
            return false;
        } else {
            $this->db->where('id', $this->input->post('org_id'));
            return $this->db->update('fms_organisation', ['partner_format' => $this->input->post('id')]);
        }
    }

}
