<?php

/**
 * @Author Eric
 */
class Sms_model extends CI_Model{
	
	public	function __construct(){
		# code...
	}

	public function set($sent_data){

		$data['member_id'] =$sent_data['member_id'];
		$data['message_type'] =$sent_data['message_type'];
		$data['message'] =$sent_data['message'];
		$data['ref_no'] =$sent_data['ref_no'];
		$data['date_created'] = time();
        $data['created_by'] = isset($_SESSION['staff_id'])?$_SESSION['staff_id']:1;
        $data['modified_by'] = isset($_SESSION['staff_id'])?$_SESSION['staff_id']:1;
        $this->db->insert('sms', $data);
        return $this->db->insert_id();
	}

	public function get($filter = FALSE) {
        $this->db->select('*');
        $this->db->from('sms');
        if (is_numeric($this->input->post("member_id"))) {
            $this->db->where('sms.member_id=', $this->input->post('member_id'));
        }
        if ( $this->input->post('date_to') != NULL && $this->input->post('date_from') !=='' ) {
             $this->db->where("sms.date_created <=". strtotime($this->input->post('date_to')));
            $this->db->where("sms.date_created >=". strtotime($this->input->post('date_from')));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('sms.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_totals($filter = FALSE){
    	$this->db->select('COUNT(*) total_sms');
        $this->db->from('sms');
        if (is_numeric($this->input->post("member_id"))) {
            $this->db->where('sms.member_id=', $this->input->post('member_id'));
        }
        if ( $this->input->post('date_to') != NULL && $this->input->post('date_from') !=='' ) {
             $this->db->where("sms.date_created <=". strtotime($this->input->post('date_to')));
            $this->db->where("sms.date_created >=". strtotime($this->input->post('date_from')));
        }
        if ( $this->input->post('end_date') != NULL && $this->input->post('start_date') !=='' ) {
             $this->db->where("sms.date_created <=". strtotime($this->input->post('end_date')));
            $this->db->where("sms.date_created >=". strtotime($this->input->post('start_date')));
        }

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('sms.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->row_array();
            }
        }
    }
}