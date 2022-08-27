<?php
/**
 * Description of Branch_model
 *
 * @author allan
 */
class Branch_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $organisation_id=(isset($_SESSION['organisation_id']))?$_SESSION['organisation_id']:1;
        $this->db->from('branch');
        if ($filter === FALSE) {
            $this->db->where('organisation_id', $organisation_id);
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)){
                $this->db->where('branch.id=' . $filter);
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

    public function set() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['tbl']);
        $data['date_created'] = time();
        $data['created_by'] =$data['modified_by'] = $_SESSION['id'];
        $this->db->insert('branch', $data);
        return $this->db->insert_id();
    }
	
    public function update() {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['id'];
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('branch', $data);
    }
    public function delete() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->delete('branch');
    }

    // branch dropdown
    public function get_branch($filter = FALSE) {
        $response = array();
        $this->db->select('id,branch_name');
        $q = $this->db->get('branch');
        $response = $q->result_array();
        return $response;
    }

}
