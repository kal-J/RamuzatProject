<?php
class ModulePrivilege_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->from('module_privilege md');
        if ($filter === FALSE) {
            $this->db->select('m.module_name,md.module_id,md.id,md.status_id as status_id,md.privilege_code,md.description');
            $this->db->join('modules m', 'md.module_id=m.id', 'left');
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)){
                $this->db->select('m.module_name,md.module_id,md.id,md.status_id as status_id,md.privilege_code,md.description');
                $this->db->join('modules m', 'md.module_id=m.id', 'left');
                $this->db->where('md.module_id',$filter);
                $query = $this->db->get();
                return $query->result_array();
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
        $q = $this->db->get( 'modules' );
        $response = $q->result_array();
        return $response;
    }
    public function set() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['tbl']);
        $data['date_created'] = time();
        $data['status_id'] = '1';
        $data['created_by'] = $_SESSION['id'];

        $this->db->insert('module_privilege', $data);
        return $this->db->insert_id();
    }
	
    public function update() {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['id'];
        $data['status_id'] = '1';
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('module_privilege', $data);
    }
    public function deactivate() {
        $data = array(
            'status_id' =>$this->input->post('status_id'),
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('module_privilege', $data);
    }
    public function delete() {
        $data = array(
            'status_id' =>0,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('module_privilege',$data);
    }

}
