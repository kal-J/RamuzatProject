<?php
class Available_to_model extends CI_Model {

    Public function __construct()
    {
      parent :: __construct();
    }

    public function get($filter = FALSE) {
        $this->db->select('available_to.id,name');
        $query = $this->db->from('available_to');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('available_to.id=' . $filter);
                $query = $this->db->get();
                //print_r($query->row_array());die();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

}
