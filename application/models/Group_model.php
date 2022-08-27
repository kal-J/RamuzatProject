<?php

class Group_model extends CI_Model {

    public function __construct() {
        $this->load->database();
        $this->table='group';
    }

    public function get($filter = false) {
        if (isset($_SESSION) && isset($_SESSION['branch_id']) && is_numeric($_SESSION['branch_id'])) {
            $this->db->where("branch_id =", (int) $_SESSION['branch_id']);
        }
        if(isset($_POST['group_client_type']) && !empty($this->input->post('group_client_type'))) {
            $this->db->where('group_client_type', $this->input->post('group_client_type'));
        }

        $this->db->select("*");
        $this->db->from("group");
        $this->db->join("(SELECT COUNT(`id`) member_count, group_id FROM fms_group_member GROUP BY `group_id`) gm","group.id=gm.group_id", "LEFT");
        $this->db->order_by("id","DESC");
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("group.id=", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function add($group_no){
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['tbl']);
        $data['date_created'] = time();
        $data['status_id'] = 1;
        $data['group_no'] = $group_no;
        $data['branch_id'] =$_SESSION['branch_id'];
        $data['created_by'] =$data['modified_by'] =$_SESSION['id'];

        $this->db->insert('group', $data);
        return $this->db->insert_id();
    }
    // Added for importation script applies to client import with group : Ambrose Ogwang
    public function add_through_import($data){
        $this->db->insert('group', $data);
        return $this->db->insert_id();
    }

    public function update() {

        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['tbl']);
        $data['modified_by'] =$_SESSION['id'];
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update($this->table, $data);
    }

    public function delete() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->delete('group');
    }
    public function deactivate() {
        $data = array(
            'status' => 0
        );

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('group', $data);
    }

    public function validate_group_name($group_name) {
        $id=$this->input->post('id'); 
        if ($id === NULL || empty($id)) {
            $query_result = $this->db
                    ->limit(1)
                    ->where("group_name=",$group_name)
                    ->get('group');
            return ($query_result->num_rows() === 0);
        }else{
            $query_result = $this->db
                ->limit(1)
                ->where('id=', $id)
                ->where("group_name=",$group_name)
                ->get('group');
            if ($query_result->num_rows() === 1) {
                return TRUE;
            }else{                
                $query_result = $this->db
                        ->limit(1)
                        ->where("group_name=",$group_name)
                        ->get('group');
                return ($query_result->num_rows() === 0);
            }
        }
    }
    // branch dropdown
    public function get_group($filter = FALSE) {
        $response = array();
        $this->db->select('*');
       
        if ($filter === FALSE) {
            $q = $this->db->get('group');
            $response = $q->result_array(); 
            return $response;
        } else {
            if (is_numeric($filter)) {
                $this->db->where("id=", $filter);
                $q = $this->db->get('group');
                $response = $q->row_array();
                return $response;
            } else {
                $this->db->where($filter);
                $q = $this->db->get('group');
                $response = $q ->result_array();  
                return $response;
            }
        }
    }


}

