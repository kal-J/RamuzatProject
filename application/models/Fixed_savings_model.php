<?php


class Fixed_savings_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select('fs.id,savings_account_id,fs.start_date,fs.end_date,fs.qualifying_amount,fs.status,sa.account_no');
        $this->db->from('fms_fixed_savings fs');
        $this->db->join('fms_savings_account sa','sa.id=fs.savings_account_id','left');
        if ($filter === FALSE) {
            $query = $this->db->get();
            //Loan_guarantor_model->get_guarantor_savings2()
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('la.id=' . $filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_fixed_account($savings_account_id){
        $this->db->select('*');
        $this->db->from('fms_fixed_savings');
        $this->db->where('savings_account_id', $savings_account_id);
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }


    public function fix() {
        $data = $this->input->post(null, true);
        unset($data['id']);

        $data['start_date']=$this->helpers->yr_transformer($data['start_date']);
        $data['end_date']=$this->helpers->yr_transformer($data['end_date']);

        $insert = $this->db->insert('fms_fixed_savings', $data);
        $last_id = $this->db->insert_id();

        return $last_id;

    }

    public function change_status() {
        $account_id = $this->input->post('id');
        $this->db->where('savings_account_id', $account_id);
        $query = $this->db->update('fms_fixed_savings', ['status' => '0']);
        //print_r($this->db->last_query()); die();
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

}