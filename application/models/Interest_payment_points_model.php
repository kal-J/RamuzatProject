<?php

class Interest_payment_points_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get_payment_points() {

        $this->db->select('*');
        $this->db->from('savings_interest_payment');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function set($data) {
        return $this->db->insert_batch('savings_interest_payment', $data);
    }


    public function change_status($id,$status_id,$date_paid) {
        $this->db->where('id', $id);
        return $this->db->update('savings_interest_payment',['status_id'=>$status_id,'date_paid'=>$date_paid]);
    }

	public function delete() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->delete('savings_interest_payment');
    }

    public function get_payments($start,$end){
        $this->db->select('i.*,concat(u.firstname," ", u.lastname," ", u.othernames) AS member_name,account_no,sa.member_id');
        $this->db->from('savings_interest_payment i');
        $this->db->join('fms_savings_account sa', 'i.savings_account_id=sa.id','left');
        $this->db->join('member m', 'sa.member_id=m.id','left');        
        $this->db->join('user u', 'm.user_id=u.id','left');
        $this->db->where('date_calculated>=', $start);
        $this->db->where('date_calculated<=', $end);
        $query = $this->db->get();
        return $query->result_array();
    }

}
