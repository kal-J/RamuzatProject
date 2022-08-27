<?php

class Dashboard_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = false) {
        if ($filter === FALSE) {
            $query = $this->db->get("user_business");
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("business.id=", $filter);
                $query = $this->db->get("user_business");
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get("user_business");
                return $query->result_array();
            }
        }
    }
    
    public function client_savings_sums($filter =false){
        $this->db->select('sum(credit) as total_credit,sum(debit) as total_debit');
        $this->db->from('transaction');
       // $this->db->where("transaction_date >= ", $this->input->post("start_date"));
        $this->db->where("transaction_date <= ",$this->input->post("end_date")." 23:59:59");
        $this->db->where("status_id=", 1);
         if (is_numeric($filter!=false)) {
                $this->db->where($filter);
                $query = $this->db->get();
               return $query->row_array();
         } else {
               $query = $this->db->get();
               return $query->row_array();
         }
        
    }


  
    public function client_share_sums($filter =false){
        $this->db->select('sum(credit) as total_share_credit,sum(debit) as total_share_debit');
        $this->db->from('share_transactions');
       // $this->db->where("transaction_date >= ", $this->input->post("start_date"));
        $this->db->where("transaction_date <= ", $this->input->post("end_date")." 23:59:59");
        $this->db->where("status_id=", 1);
         if (is_numeric($filter!=false)) {
                $this->db->where($filter);
                $query = $this->db->get();
               return $query->row_array();
         } else {
               $query = $this->db->get();
               return $query->row_array();
         }
        
    }
    public function get_current_fiscal_year($org_id, $status_id) {
        if ($status_id === FALSE) {
            $this->db->where("fiscal_year.organisation_id=", $org_id);
            $query = $this->db->get("fiscal_year");
            return $query->result_array();
        } else {
            $this->db->where("fiscal_year.organisation_id=", $org_id);
            $this->db->where("fiscal_year.status_id=", $status_id);
            $query = $this->db->get("fiscal_year");
            return $query->row_array();
        }
    }

     public function get_clients($filter = false) {
        $sub_query= '';
        if ($filter === false) {
           $sub_query= '';
        }else{
            $sub_query= ' WHERE '.$filter.' ';
        }
        $query = $this->db->query('SELECT fms_member.id, concat(salutation," ",firstname," ", lastname," ", othernames) AS client_name,client_no,subscription_plan_id,date_registered,1 as client_type from fms_member JOIN fms_user ON fms_member.user_id=fms_user.id '. $sub_query);
        return $query->result_array();
    }
    public function get_all_system_users($filter = false) {
        $sub_query= '';
        if ($filter === false) {
           $sub_query= '';
        }else{
            $sub_query= ' WHERE '.$filter.' ';
            $sub_query .= ' AND  id !=1';
        }
        $query = $this->db->query('SELECT id,concat(salutation," ",firstname," ", lastname," ", othernames) AS user_name from fms_user'. $sub_query);
        //u LEFT JOIN fms_member m ON m.user_id=u.id

        return $query->result_array();
    }

}