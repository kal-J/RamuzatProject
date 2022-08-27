<?php
/**
 * Description of custom emails model
 *
 * @author Ambrose
 */
class Emails_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select("");
        $this->db->from('');
        $this->db->join();
        if ($filter === FALSE) {
            $this->db->where('share_issuance.status_id IN (1,2)');
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)){ 
                $this->db->where('' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set() {
        $data = $this->input->post(NULL, TRUE);
        $data['date_created'] = time();
        $data['status_id'] = 1;
        $data['created_by'] =$_SESSION['id'];

        $this->db->insert('fms_emails', $data);
        $insert_id= $this->db->insert_id();
        
        return $insert_id;
    }
	
     
}
