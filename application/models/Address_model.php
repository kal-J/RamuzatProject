<?php

class Address_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get_address_types() {
        $this->db->select('*');
        $this->db->from('fms_address_type');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_addresses($filter = FALSE) {

        $this->db->select('ua.id,ua.user_id,ua.address1,ua.address2,ua.address_type_id,village_id,ua.date_created,ua.date_modified,ua.created_by,ua.modified_by,p.parish,v.village,s.subcounty,d.district,v.parish_id,p.subcounty_id,s.district_id, at.address_type_name,ua.start_date,ua.end_date');

        $this->db->from('fms_user_address ua');
        $this->db->join('village v', 'v.id=ua.village_id', 'left');
        $this->db->join('parish p', 'p.id=v.parish_id', 'left');
        $this->db->join('subcounty s', 's.id=p.subcounty_id', 'left');
        $this->db->join('district d', 'd.id=s.district_id', 'left');
        $this->db->join('address_type at', 'at.id=ua.address_type_id', 'left');
        if ($filter === FALSE) {
            $this->db->where('ua.user_id', $this->input->post('user_id'));
            
            
            $query = $this->db->get();
            //print_r( $this->db->last_query() ).die;
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('ua.user_id=' . $filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->row_array();
            }
        }
    }

    public function set() {
        $start_date = explode('-', $this->input->post('start_date'), 3);
        $start_date_prepared = count($start_date) === 3 ? ($start_date[2] . "-" . $start_date[1] . "-" . $start_date[0]) : null;

        //End date
        if($this->input->post('end_date') != ""){
            $end_date = explode('-', $this->input->post('end_date'), 3);
            $end_date_prepared = count($end_date) === 3 ? ($end_date[2] . "-" . $end_date[1] . "-" . $end_date[0]) : null;}
        else{
            $end_date_prepared='';
        }

        $data = array(
            'user_id' => $this->input->post('user_id'), 
            'address1' => $this->input->post('address1'),
            'address2' => $this->input->post('address2'),
            'address_type_id' => $this->input->post('address_type_id'),
            'village_id' => $this->input->post('village_id'),
            'start_date' => $start_date_prepared,
            'end_date' => $end_date_prepared,
            'date_created' => time(),
            'created_by' => $_SESSION['id']           //$_SESSION['user_id']    
        );

        $this->db->insert('fms_user_address', $data);
        return $this->db->insert_id();
    }


    public function set2($data){
        $this->db->insert('fms_user_address', $data);
        return $this->db->insert_id();
    }
    public function update() {
        $start_date = explode('-', $this->input->post('start_date'), 3);
        $start_date_prepared = count($start_date) === 3 ? ($start_date[2] . "-" . $start_date[1] . "-" . $start_date[0]) : null;
        //End date
        if( $this->input->post('end_date')==""){
            $end_date = explode('-', $this->input->post('end_date'), 3);
            $end_date_prepared = count($end_date) === 3 ? ($end_date[2] . "-" . $end_date[1] . "-" . $end_date[0]) : null;}
        else{
            $end_date_prepared='';
        }
        $data = array(
            'user_id' => $this->input->post('user_id'),
            'address1' => $this->input->post('address1'),
            'address2' => $this->input->post('address2'),
            'address_type_id' => $this->input->post('address_type_id'),
            'village_id' => $this->input->post('village_id'),
            'start_date' => $start_date_prepared,
            'end_date' => $end_date_prepared,
            'date_modified' => time(),
            'modified_by' => $_SESSION['id']    //$_SESSION['user_id']
        );

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fms_user_address', $data);
    }

    public function delete() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->delete('fms_user_address');
    }
    // Used by import script
    public function set3($data){
        $this->db->insert('fms_user_address',$data);
        return $this->db->insert_id();

    }

}
