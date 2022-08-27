<?php 
Class UserCourses extends CI_Model{
    public function __construct(){
        $this->load->library("Server", "server");
    }
    public function findUserCourses(){
        $this->server->select("tbl_user_courses.*");
        $this->server->from('tbl_user_courses');
        if ($filter === FALSE) {
            $query = $this->db->get();
            print_r($query->result_array());
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('tbl_user_courses.id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                !empty($filter) ? $this->db->where($filter) : "";
                $query = $this->db->get();
                return $query->result_array();
            }
        }
      
    }
}