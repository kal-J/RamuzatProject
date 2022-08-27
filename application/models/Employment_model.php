<?php

class Employment_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($id = false) {
        $this->db->from('user_employment');
        if ($id !== false) {
            $this->db->where('user_employment.user_id', $id);
            $this->db->select('user_employment.id,position,nature_of_employment_id,employer,name,start_date,end_date,monthly_salary')->join('nature_of_employment', 'nature_of_employment.id=user_employment.nature_of_employment_id');
            ;
        } else {
            $this->db->select('user_employment.id,position,nature_of_employment_id,employer,name,start_date,end_date,monthly_salary')->join('nature_of_employment', 'nature_of_employment.id=user_employment.nature_of_employment_id');
        }
        $query = $this->db->get();
        // print_r ($this->db->last_query());die();
        return $query->result_array();
    }

    public function add_employment() {
        $id = $this->input->post('user_id');
        //print_r($id);die();
        $data = $this->input->post(NULL, TRUE);
        //Start of Employment
        $start_date = explode('-', $data['start_date'], 3);
        $data['start_date'] = count($start_date) === 3 ? ($start_date[2] . "-" . $start_date[1] . "-" . $start_date[0]) : null;

        //End of employment
        if (isset($data['end_date']) && $data['end_date'] !='') {
            $end_date = explode('-', $data['end_date'], 3);
            $data['end_date'] = count($end_date) === 3 ? ($end_date[2] . "-" . $end_date[1] . "-" . $end_date[0]) : null;
        }

        unset($data['id'], $data['tbl']);
        //print_r($data['years_of_employment']); die();
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] =$_SESSION['id'];

        $this->db->insert('user_employment', $data);
        return $this->db->insert_id();
    }

    public function update_employment() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        //Start of Employment
        $start_date = explode('-', $data['start_date'], 3);
        $data['start_date'] = count($start_date) === 3 ? ($start_date[2] . "-" . $start_date[1] . "-" . $start_date[0]) : null;

        //End of employment
       if (isset($data['end_date']) && $data['end_date'] !='') {
            $end_date = explode('-', $data['end_date'], 3);
            $data['end_date'] = count($end_date) === 3 ? ($end_date[2] . "-" . $end_date[1] . "-" . $end_date[0]) : null;
        }

        unset($data['id'], $data['user_id']);
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] =$_SESSION['id'];

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $this->db->update('user_employment', $data);
            //print_r($this->db->last_query()); exit();
            return '1';
        } else {
            return false;
        }
    }

    public function delete_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->delete('user_employment');
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete('user_employment');
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function change_status_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' => '2');
            $this->db->where('id', $id);
            $query = $this->db->update('user_employment', $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $data = array('status_id' => '2');
            $query = $this->db->update('user_employment', $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function get_nature_of_employment($filter = FALSE) {
        $this->db->select('nature_of_employment.id,name');
        $this->db->from('nature_of_employment');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('nature_of_employment.id=' . $filter);
                $query = $this->db->get('', 1);
                //print_r($query->row_array());die();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

}
