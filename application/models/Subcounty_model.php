<?php

class Subcounty_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get_subcounties($filter = FALSE) {
        $this->db->from('subcounty');
        if ($this->input->post('district_id') !== NULL && is_numeric($this->input->post('district_id'))) {
            $this->db->where("district_id", $this->input->post('district_id'));
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
        return $this->db->insert_batch('subcounty', $data);
    }

    public function update($data) {
        $this->db->where('county_id=' . $data['id']);
        return $this->db->update('subcounty', array("district_id"=>$data['district_id']));
    }

}
