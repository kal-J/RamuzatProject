<?php

class Parish_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get_parishes($filter = FALSE) {
        if ($this->input->post('subcounty_id') !== NULL && is_numeric($this->input->post('subcounty_id'))) {
            $this->db->where("subcounty_id", $this->input->post('subcounty_id'));
        }
        /* $i = 29;
        $this->db->limit(250, $i*250);
        $this->db->where("id BETWEEN", " (250*28+68) AND (250*28+250)", FALSE);*/
        if ($filter === FALSE) {
            $query = $this->db->get('parish');
            return $query->result_array();
        } else {
            $query = $this->db->get('parish');
            return $query->result_array();
        }
    }

    public function set($data) {
        return $this->db->insert_batch('parish', $data);
    }

}
