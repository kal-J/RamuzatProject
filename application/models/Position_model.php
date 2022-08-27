<?php
class Position_model extends CI_Model {

    Public function __construct()
    {
      parent :: __construct();
      $this->table = 'position';
    }

    public function get($filter = FALSE) {
        $query = $this->db->from('position');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('position.id=' . $filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id']);
        $data['status_id'] = '1';
        $data['organisation_id'] = isset($_SESSION['organisation_id'])?$_SESSION['organisation_id']:1;
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update() {
        $id=$this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id']);
        $data['modified_by'] = $_SESSION['id'];
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update($this->table, $data);
    }

}
