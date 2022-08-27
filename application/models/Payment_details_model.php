<?php
/**
 * @Author Eric
 */
class Payment_details_model extends CI_Model{
	
	public	function __construct(){
		# code...
	}

	public function get($filter = FALSE) {
        $this->db->select("a.*");
        $this->db->from('payment_details a');

        if ($this->input->post('client_loan_id') !== NULL && is_numeric($this->input->post('client_loan_id'))) {
            $this->db->where("a.client_loan_id", $this->input->post('client_loan_id'));
        }
        if ($this->input->post('status_id') !== NULL && is_numeric($this->input->post('status_id'))) {
            $this->db->where("a.status_id", $this->input->post('status_id'));
        }else{
             $this->db->where("a.status_id", 1);
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

	public function set($client_loan_id){
		$data = array(
            'client_loan_id' =>$client_loan_id,
            'ac_name'=> $this->input->post('ac_name'),
            'ac_number'=> $this->input->post('ac_number'),
            'bank_branch'=> $this->input->post('bank_branch'),
            'bank_name'=> $this->input->post('bank_name'),
            'phone_number'=> $this->input->post('phone_number')
        );
		$data['status_id'] = 1;       
        $data['date_created'] = time();       
        $data['created_by'] =$_SESSION['id'];
        $this->db->insert('payment_details', $data);
        return $this->db->insert_id();
	}

    public function set2($sent_data){
        $query = $this->db->insert('payment_details', $sent_data);
        return $this->db->insert_id();
    }
    public function deactivate($client_loan_id){        
            //$data = array('status_id' => '2');
            $this->db->where('client_loan_id', $client_loan_id);
            $query = $this->db->delete('payment_details');
            if ($query) {
                return true;
            } else {
                return false;
            }
    }

}