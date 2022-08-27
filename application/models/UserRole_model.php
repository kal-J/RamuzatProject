<?php
class UserRole_model extends CI_Model {

    Public function __construct()
    {
      parent :: __construct();
    }
    public function get($filter = FALSE) {
        $query = $this->db->from('user_role ur');
        $this->db->select('ur.id,ur.role_id,r.role,ur.status_id');
        $this->db->join('role r', 'ur.role_id=r.id', 'left');
        $this->db->where_not_in('ur.status_id',0);
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('ur.staff_id=' . $filter);
                //$this->db->where_not_in('ur.status_id',0);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
    public function set($filter =FALSE) {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['position_id'],$data['user_id'],$data['comment'],$data['id'],$data['client_no'],$data['tbl']);
        if (is_numeric($filter)) {
        $data['staff_id'] = $filter;
        }
        $data['date_created'] = time();
        $data['created_by'] = $this->session->userdata('id');
        $data['modified_by'] = $this->session->userdata('id');

        $this->db->insert('user_role', $data);
        return $this->db->insert_id();
    }

    public function delete() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->delete('user_role');
    }


    public function deactivate() {
        $data = array(
            'status_id' =>$this->input->post('status_id'),
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('user_role', $data);
    }
}
