<?php
class Penalty_calculation_method_model extends CI_Model {

    Public function __construct()
    {
      parent :: __construct();
    }

    public function get($filter = FALSE) {
        $this->db->select('penalty_calculation_method.id,method_description');
        $query = $this->db->from('penalty_calculation_method');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('penalty_calculation_method.id=' . $filter);
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
