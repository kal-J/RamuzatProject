<?php

class Investiment_member_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->from("investiment_member");

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('`id`', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    
    public function set($investiment_target_id) {
        $partner_list = $this->input->post('partner_list');
        foreach ($partner_list as $key => $value) {//it is a new entry, so we insert afresh
            if (isset($value['user_id']) && is_numeric($value['user_id'])) {
                $value['investiment_target_id'] = $investiment_target_id;
                $value['date_created'] = time();
                $value['created_by'] = $_SESSION['id'];
                $this->db->insert('investiment_member', $value);
            }
        }
        return true;
    }

    //deletes members given a particular where clause
    public function delete() {
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        if ($this->input->post('investiment_target_id') !== NULL && is_numeric($this->input->post('investiment_target_id'))) {
            $this->db->where('investiment_target_id', $this->input->post('investiment_target_id'));
        }
        return $this->db->delete('investiment_member');
    }

    public function change_status() {
        $data = array(
            'status_id' => 0
        );
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        if ($this->input->post('investiment_target_id') !== NULL && is_numeric($this->input->post('investiment_target_id'))) {
            $this->db->where('investiment_target_id', $this->input->post('investiment_target_id'));
        }

        return $this->db->update('investiment_member', $data);
    }


}
