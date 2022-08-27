<?php

class Contact_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    var $table = 'contact';

    public function get($user_id = false) {
         
        $this->db->where('user_id', $user_id);
        $this->db->select('contact.*, contact_type');
        $this->db->from($this->table);
        $this->db->join('contact_type', "contact_type_id=contact_type.id");
        $query = $this->db->get();
        return $query->result_array();
    }

#user_id, mobile_number, contact_type_id, date_created, date_modified, created_by, modified_by

    public function add_contact($user_id = false, $post_data = []) {
        if ($user_id == false) {
            $user_id = $this->input->post('user_id');
            $contact_type_id = $this->input->post('contact_type_id');
        } else {
            $contact_type_id = 1;
        }
        if ($post_data === []){
            $data = array(
                'user_id' => $user_id,
                'mobile_number' => $this->input->post('mobile_number'),
                'contact_type_id' => $contact_type_id,
                'date_created' => time(),
                'created_by' => $_SESSION['id']
            );
        }else{
            $data = $post_data;
        }

        $this->db->insert('contact', $data);
        return $this->db->insert_id();
    }

    public function validate_contact($mobile_number) {
        $user_id = $this->input->post('user_id');
        $id = $this->input->post('id');
        $mobile_number1 = substr($mobile_number, -9);

        if ($id === NULL || empty($id)) {
            $query_result = $this->db
                    ->limit(1)
                    ->like('mobile_number', $mobile_number1, 'before')
                    ->get('contact');
            return ($query_result->num_rows() === 0);
        } else {
            $query_result = $this->db
                    ->limit(1)
                    ->where('user_id=', $user_id)
                    ->like('mobile_number', $mobile_number1, 'before')
                    ->get('contact');
            if ($query_result->num_rows() === 1) {
                return TRUE;
            } else {
                $query_result = $this->db
                        ->limit(1)
                        ->like('mobile_number', $mobile_number1, 'before')
                        ->get('contact');
                return ($query_result->num_rows() === 0);
            }
        }
    }

    public function update_contact() {

        $data = array(
            'mobile_number' => $this->input->post('mobile_number'),
            'contact_type_id' => $this->input->post('contact_type_id'),
            'date_modified' => time(),
            'modified_by' => $_SESSION['id']
        );

        $id = $this->input->post('id');
        $this->db->where('id', $id);
        $query = $this->db->update($this->table, $data);
        //print_r ($this->db->last_query());die();
        //$this->db->affected_rows();
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

// contact_type dropdown
    public function get_contact_type($filter = FALSE){
        $response = array();
        $this->db->select('*');
        $q = $this->db->get('contact_type');
        $response = $q->result_array();
        return $response;
    }

    public function delete_by_id(){
        $contact_id = $this->input->post('id');
        $this->db->where('id', $contact_id);
        $query = $this->db->delete($this->table);
        //print_r($this->db->last_query()); die();
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

}
