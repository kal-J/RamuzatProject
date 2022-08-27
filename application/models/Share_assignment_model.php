<?php

/**
 * Description of Share_fees_model
 *
 * @author Melchisedec
 */
class Share_assignment_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select('share_fees.id AS id,feename,feetype,amountcalculatedas,amount,feetype.id AS feetype_id,amountcalculatedas.id AS amountcalculatedas_id');
        $this->db->from('share_fees')
                ->join('feetype', 'share_fees.feetype_id=feetype.id', 'LEFT')
                ->join('amountcalculatedas', 'share_fees.amountcalculatedas_id=amountcalculatedas.id');
        $this->db->where('share_fees.status_id', $this->input->post('status_id'));
        if ($filter === FALSE) {
            $query = $this->db->get();
            //print_r($this->db->last_query()); die();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                // $this->db->where('status_id=',0);
                //$this->db->where('share_fees.status_id', $this->input->post('status_id'));
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                // $this->db->where('status_id=',0);
                //$this->db->where('share_fees.status_id', $this->input->post('status_id'));
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['requiredfee']);
        $data['status_id'] = '1';
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];

        $this->db->insert('share_fees', $data);
        return $this->db->insert_id();
    }

    public function update() {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['id'];
        $data['status_id'] = '1';
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('share_fees', $data);
    }

    // Loan fees dropdown
    public function get_share_fees($filter = FALSE) {
        $response = array();
        $this->db->select('id,feename,amount,amountcalculatedas_id');
        $q = $this->db->get('share_fees');
        $response = $q->result_array();
        return $response;
    }

    /**
     * This method Deactivates share_fees data from the database
     */
    public function delete_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->delete('share_fees');
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete('share_fees');
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * This method deactivate share_fees data from the database
     */
    public function change_status_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            $query = $this->db->update('share_fees', $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            $query = $this->db->update('share_fees', $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

}
