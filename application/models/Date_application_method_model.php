<?php

class Date_application_method_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get_date_application_mtd($filter = FALSE) {

        $this->db->select('*');

        $this->db->from('fms_date_application_methods');
		//$this->db->from('fms_saving_fees');
       
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('id=' . $filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
	
	  
}