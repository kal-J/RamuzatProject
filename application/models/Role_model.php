<?php
/**
 * Description of Role_model
 *
 * @author Eric
 */
class Role_model extends CI_Model {

    public function __construct() {
        $this->load->database();
        $this->load->library("session");
    }

    public function get($filter = FALSE) {
        $this->db->from('role');
        if ($filter === FALSE) {
            $this->db->where_not_in('status_id',$this->input->post('status_id'));
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)){
                $this->db->where('role.id=' . $filter);
                $this->db->where_not_in('status_id',0);
                $query = $this->db->get();
                //echo $this->db->last_query();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public  function  get_active_roles($filter = FALSE){
        $response = array();
        $this->db->select( '*' );
        $this->db->where('status_id='. $filter);
        $q = $this->db->get( 'role' );
        $response = $q->result_array();
        return $response;
    }

    public function set() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['tbl']);
        $data['status_id'] = '1';
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];

        $this->db->insert('role', $data);
        return $this->db->insert_id();
    }
	
    public function update() {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['id'];
        $data['status_id'] = '1';
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('role', $data);
    }
    
    public function deactivate() {
        $data = array(
            'status_id' =>$this->input->post('status_id'),            
            'modified_by' =>$_SESSION['id']
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('role', $data);
    }

    public function delete() {
        $data = array(
            'status_id' =>0,            
            'modified_by' =>$_SESSION['id']
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('role', $data);
    }
}
