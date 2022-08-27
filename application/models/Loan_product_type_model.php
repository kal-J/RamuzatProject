<?php
class Loan_product_type_model extends CI_Model {

    Public function __construct()
    {
      parent :: __construct();
    }

    public function get($filter = FALSE) {
        $this->db->select('loan_product_type.id,type_name, description');
        $query = $this->db->from('loan_product_type');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('loan_product_type.id=' . $filter);
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
