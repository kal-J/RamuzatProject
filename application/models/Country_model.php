<?php

class Country_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function set() {
        $data = array(
            'country_name' => $this->input->post('country_name'),
        );
        if(is_numeric($this->input->post('id'))){
            $this->db->where('id', $this->input->post('id'));
            return $this->db->update('country', $data);
        }else{
            $this->db->insert('country', $data);
            return $this->db->insert_id();
        }
        
    }

    public function get($filter = false) {
        $this->db->select("country.*");
        $this->db->from('country');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('country.id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                !empty($filter) ? $this->db->where($filter) : "";
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function delete() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->delete('country');
    }

}
