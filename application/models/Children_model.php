<?php

class Children_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = false) {
        $this->db->select('id,member_id,firstname,lastname,othernames,gender,date_of_birth');
        $this->db->from('member_children');
        if ($filter !== false) {
            if (is_numeric($filter)) {
                $this->db->where('id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
            }
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    public function set($child_data = []) {
        $data = $child_data;
        if ($child_data === []) {
            $data = $this->input->post(NULL, TRUE);
            $dob_date = explode('-', $this->input->post('date_of_birth'), 3);
            $data['date_of_birth'] = count($dob_date) === 3 ? ($dob_date[2] . "-" . $dob_date[1] . "-" . $dob_date[0]) : null;
            unset($data['id'], $data['tbl']);
            $data['date_created'] = time();
            $data['member_id'] = $this->input->post('member_id');
            $data['created_by'] = $_SESSION['id'];
            $data['modified_by'] = $_SESSION['id'];
        }
        $this->db->insert('member_children', $data);
        return $this->db->insert_id();
    }

    public function update() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        $dob_date = explode('-', $this->input->post('date_of_birth'), 3);
        $data['date_of_birth'] = count($dob_date) === 3 ? ($dob_date[2] . "-" . $dob_date[1] . "-" . $dob_date[0]) : null;
        unset($data['member_id'], $data['tbl']);
        $data['date_modified'] = time();
        $data['modified_by'] = $_SESSION['id'];

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('member_children', $data);
        } else {
            return false;
        }
    }

    public function delete() {
        $data = array(
            'status_id' => 0,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('member_children', $data);
    }
}
