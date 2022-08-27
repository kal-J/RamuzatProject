<?php

class Village_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get_villages($filter = FALSE) {
        $this->db->from('village');
        if ($this->input->post('parish_id') !== NULL && is_numeric($this->input->post('parish_id'))) {
            $this->db->where("parish_id", $this->input->post('parish_id'));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            $query = $this->db->get();
            return $query->result_array();
        }
    }
    public function set($data) {
        return $this->db->insert_batch('village2', $data);
    }
    public function set_json($data) {
        return $this->db->insert('village', $data);
    }

}
