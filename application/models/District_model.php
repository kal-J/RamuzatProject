<?php

class District_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get_districts($filter = FALSE) {
        if ($filter === FALSE) {
            $query = $this->db->get('district');
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('district.id=' . $filter);
                $query = $this->db->get('district');
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get('district');
                return $query->result_array();
            }
        }
    }

    public function get_county() {
        $query = $this->db->get('county');
        return $query->result_array();
    }

    public function set($data) {
        $this->db->insert_batch('district', $data);
        return $this->db->insert_id();
    }

    public function set_json() {
        $parishes = $this->input->post('parishes');
        $success = false;
        foreach ($parishes as $parish) {
            $parishId = explode(",", $parish['parishId']);
            $parish['subcountyId'] = $parishId[2];
            $this->db->insert('parish2', $parish);
            $success = $this->db->insert_id();
        }
        /* if ($this->db->insert_batch('subcounty2', $this->input->post('subcounties'))) {
          return true;
          } */
        return $success;
    }

}
