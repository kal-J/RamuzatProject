<?php
class Guarantor_setting_model extends CI_Model {

    Public function __construct()
    {
      parent :: __construct();
    }

    public function get($filter = FALSE) {
        //$this->db->select('guarantor_setting.id,setting,description');
        $query = $this->db->from('guarantor_setting');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('guarantor_setting.id=' . $filter);
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
