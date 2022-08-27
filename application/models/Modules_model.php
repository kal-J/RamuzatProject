<?php
class Modules_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->from('modules');
        if ($filter === FALSE) {
            $this->db->select('*');
            if($this->input->post('status_id') !== NULL && is_numeric($this->input->post('status_id'))){
                
            }else{
                $this->db->where('status_id', 1);
            }
            
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)){
                $this->db->where('modules.id='. $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    } 
    public  function  get_modules($filter = FALSE){
        $response = array();
        $this->db->select( '*' );
        $this->db->where('status_id='. $filter);
        $q = $this->db->get( 'modules' );
        $response = $q->result_array();
        return $response;
    }
    public  function  get_modules_privileges($filter = FALSE){
        $this->db->select('om.module_id');
        $this->db->from('org_modules om');
        $this->db->where('om.organisation_id', $_SESSION['organisation_id']);
        $sub_query = $this->db->get_compiled_select();

        $response = array();
        $this->db->select( '*' );
        $this->db->from('modules');
        $this->db->where('status_id='. $filter);
        $this->db->where("fms_modules.id IN ($sub_query)");
        $q = $this->db->get();
        $response = $q->result_array();
        return $response;
    }
}
