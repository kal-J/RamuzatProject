<?php

class Lock_savings_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {

        $this->db->select('la.id,saving_account_id,la.amount as locked_savings_amount,la.status_id,account_no,locked_date,amountcalculatedas_id,amountcalculatedas,percentage,narrative');
        $this->db->from('fms_lock_savings_amount la');
        $this->db->join('fms_savings_account sa','sa.id=la.saving_account_id','left');
	   $this->db->join('fms_amountcalculatedas ca','ca.id=la.amountcalculatedas_id','left');
	    //$this->db->join('fms_client_loan cl','cl.id=la.loan_id','left');
        $this->db->where('la.status_id=1');
        if ($filter === FALSE) {
            $query = $this->db->get(); 
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

     public function set() {
        if($this->input->post('amountcalculatedas_id')==1){
        $amount =round(($this->input->post('percentage')/100)*$this->input->post('available_balance'), 2);
        //print_r($amount);die;
        } else{
            $amount =$this->input->post('amount');
            
        }
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['available_balance'], $data['tbl']);
        $locked_date = explode('-', $this->input->post('locked_date'), 3);
        $data['locked_date'] = count($locked_date) === 3 ? ($locked_date[2] . "-" . $locked_date[1] . "-" . $locked_date[0]) : null;
        $data['amount'] = $amount;
        //print_r($data['amount']);die;
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] = $_SESSION['id'];

        $this->db->insert('fms_lock_savings_amount', $data);
        return $this->db->insert_id();
    }

    public function update() {
        $id = $this->input->post('id');
        if($this->input->post('amountcalculatedas_id')==1){
            $amount =round(($this->input->post('percentage')/100)*$this->input->post('available_balance'), 2);
            
        } else{
            $amount =$this->input->post('amount');
        }
        $data = $this->input->post(NULL, TRUE);
        unset($data['id']);
        unset( $data['available_balance'],$data['tbl']);
        $locked_date = explode('-', $this->input->post('locked_date'), 3);
        $data['locked_date'] = count($locked_date) === 3 ? ($locked_date[2] . "-" . $locked_date[1] . "-" . $locked_date[0]) : null;
        $data['amount'] = $amount;
        $data['date_modified'] = time();
        $data['modified_by'] = $_SESSION['id'];

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('fms_lock_savings_amount', $data);
        } else {
            return false;
        }
    }
     public function delete() {
        $data = array(
            'status_id' => 2,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fms_lock_savings_amount', $data);
    }
}