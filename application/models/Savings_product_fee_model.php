<?php

class Savings_product_fee_model extends CI_Model {

    public function __construct(){
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select('s.id, s.id as charge_id,s.saving_product_id,ac_inc.account_name as account_name_ac,ac_rec.account_name as account_name_rec,s.savings_fees_id,sf.feename,sf.repayment_made_every,sf.repayment_frequency,sf.amount,sf.amount as charge_amount,sf.chargetrigger_id,sf.dateapplicationmethod_id,sf.cal_method_id,sf.fee_type,sp.id AS savings_product_id,s.status_id,s.savings_fees_income_receivable_account_id,s.savings_fees_income_account_id');
        $this->db->from('fms_savings_product_fees s');
        $this->db->join('fms_saving_fees sf', 'sf.id=s.savings_fees_id', 'left');
        $this->db->join('fms_savings_product sp', 'sp.id=s.saving_product_id', 'left');
        $this->db->join('fms_accounts_chart ac_inc', 'ac_inc.id=s.savings_fees_income_account_id', 'left');
        $this->db->join('fms_accounts_chart ac_rec', 'ac_rec.id=s.savings_fees_income_receivable_account_id', 'left');
        //$this->db->join('fms_savings_product sp', 'sp.id=s.saving_product_id', 'left');
        
        $this->db->order_by('s.id', 'DESC');
        if ($this->input->post('status_id') != "" || !empty($this->input->post('status_id'))) {
            $this->db->where('s.status_id!=', 0);
        } 
       
        if ($filter === FALSE) {
            $this->db->where('s.saving_product_id=', $this->input->post('id'));
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('s.id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                // print_r($this->db->last_query());die();
                return $query->result_array();
            }
        }
    }
       
    
    public function set() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id']);
        $data['date_created'] = time();
        $data['status_id'] = 1;
        $data['created_by'] = $_SESSION['id'];
        $this->db->insert('savings_product_fees', $data);
        return $this->db->insert_id();
    }
    public function update() {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['id'];
        $data['status_id'] = '1';
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('savings_product_fees', $data);
    }

    public function delete_by_id() {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['id'];
        $data['status_id'] = 0;
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('savings_product_fees', $data);
    }

    public function change_status() {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['id'];
        $data['status_id'] =  $this->input->post('status_id');
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('savings_product_fees', $data);
    }

    public function get_accounts($filter = FALSE){
        $this->db->select('*');
        $this->db->from('savings_product_fees');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("savings_product_fees.id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
}
