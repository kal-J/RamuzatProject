<?php

class NextOfKin_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($id = false) {
        $this->db->from('nextofkin s');
        $this->db->join('relationship_type r','s.relationship=r.id','left');
        if ($id !== false) {
            $this->db->where('s.user_id', $id);
            $this->db->where('s.status_id', 1);
            $this->db->select('s.id,s.user_id,s.firstname,s.lastname,s.othernames, s.gender gender,s.relationship,r.relationship_type,s.address,s.telphone,share_portion');
        } else {
            $this->db->select('s.id,s.user_id,s.firstname,s.lastname,s.othernames, s.gender gender,s.relationship,r.relationship_type,s.address,s.telphone,share_portion');
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    public function set($nok_data = []) {
        $data = $nok_data;
        if($nok_data === []){
            $data = $this->input->post(NULL, TRUE);
            unset($data['id'], $data['tbl']);
            $data['date_created'] = time();
            $data['created_by'] = $_SESSION['id'];
            $data['modified_by'] = $_SESSION['id'];
        }

        $this->db->insert('nextofkin', $data);
        return $this->db->insert_id();
    }

    public function update() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['tbl']);
        unset($data['user_id'], $data['tbl']);
        $data['date_modified'] = time();
        $data['modified_by'] = $_SESSION['id'];

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('nextofkin', $data);
        } else {
            return false;
        }
    }

    public function delete() {
        $data = array(
            'active' => 0
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('nextofkin', $data);
    }
    public function validate_percentage($pecentage) {
            $this->db->select('SUM(IFNULL(share_portion,0)) as total_percentage');
            $this->db->from('nextofkin');
            $this->db->where('nextofkin.status_id', 1);
            $this->db->where('nextofkin.user_id', $this->input->post('user_id'));

            if(!empty($this->input->post('id'))){
            $this->db->where_not_in('nextofkin.id', $this->input->post('id'));
            }
            $query = $this->db->get();
            return $query->row_array();
        }


}

?>
