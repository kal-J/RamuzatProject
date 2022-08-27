<?php
class Business_model extends CI_Model {
    public function __construct() {
        $this->load->database();
    }

    public function get($filter = false) {
        if ($filter === FALSE) {
            $this->db->where("user_business.status_id=", $this->input->post('status_id'));
            $this->db->where("user_business.member_id=",$this->input->post('member_id'));
            $query = $this->db->get("user_business");
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("user_business.id=", $filter);
                $query = $this->db->get("user_business");
                return $query->row_array();
            } else {
                $this->db->where( $filter);
                $query = $this->db->get("user_business");
                return $query->result_array();
            }
        }
    }

    public function set($file_name) {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['tbl']);
        if (empty($_FILES['certificateofincorporation']['name'])) {
            unset( $data['certificateofincorporation'], $data['tbl']);
        }else{
            $data['certificateofincorporation'] = $file_name;
        }
        $data['date_created'] = time();
        $data['created_by'] =  $_SESSION['id'];
        $data['modified_by'] = $_SESSION['id'];

        $this->db->insert('user_business', $data);
        return $this->db->insert_id();
    }

    public function update($file_name) {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['tbl']);
        unset($data['member_id'], $data['tbl']);
        if (empty($_FILES['certificateofincorporation']['name'])) {
            unset( $data['certificateofincorporation'], $data['tbl']);
        }else{
            $data['certificateofincorporation'] = $file_name;
        }
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] = $_SESSION['id'];

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('user_business', $data);
        } else {
            return false;
        }
    }
    public function change_status_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            $query = $this->db->update('user_business', $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $data = array('status_id' => '0');
            $query = $this->db->update('user_business', $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

}

?>