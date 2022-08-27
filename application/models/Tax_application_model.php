<?php

class Tax_application_model extends CI_Model {

    public function __construct() {
        $this->load->database();
        $this->table = 'tax_application_model';
    }

    public function get($filter = FALSE) {
        $tax_fee_id = $this->input->post('tax_fee_id');
        $this->db->select("a.id, income_source_name, income_source_id");
        $this->db->from('tax_application a');
        $this->db->join('income_source b', 'income_source_id = b.id');
        if (is_numeric($this->input->post('tax_rate_source_id'))) {
            $this->db->where('a.tax_rate_source_id =' . $this->input->post('tax_rate_source_id'));
        }

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.id=' . $filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set() {
        $created_by = explode('-', $this->input->post('date_created'), 3);
        $data['created_by'] = count($created_by) === 3 ? ($created_by[2] . "-" . $created_by[1] . "-" . $created_by[0]) : null;
        $data = array(
            'tax_rate_source_id' => $this->input->post('tax_rate_source_id'),
            'tax_applied_to' => $this->input->post('tax_applied_to'),
            'tax_rate' => $this->input->post('tax_rate'),
            'date_created' => time(),
            'created_by' => $_SESSION['id']
        );
        $this->db->insert('tax_application', $data);
        return $this->db->insert_id();
    }

    public function get_tax_fees($filter = FALSE) {
        $this->db->select("a.id,source,rate");
        $this->db->from('tax_rate_source a');
        $this->db->join('tax_rate b', 'a.id = b.tax_rate_source_id');
        $this->db->where('a.id=' . $filter);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_tax_fee($filter = FALSE) {
        $this->db->select("a.id,source,rate");
        $this->db->from('tax_rate_source a');
        $this->db->join('tax_rate b', 'a.id = b.tax_rate_source_id');
        $this->db->where("(b.id in ( SELECT MAX(id) from fms_tax_rate GROUP BY tax_rate_source_id ))");
        $this->db->where('a.id=' . $filter);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function change_status_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            $query = $this->db->update($this->table, $data);
            print_r($this->db->last_query());
            die();
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            $query = $this->db->update($this->table, $data);
            print_r($this->db->last_query());
            die();
            print_r($this->db->last_query());
            die();
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

}
