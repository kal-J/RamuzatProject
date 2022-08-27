<?php

/**
 * Description of Loan_product_guarantor_setting_model
 *
 * @author Eric
 */
class Loan_product_guarantor_setting_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->from('loan_product_guarantor_setting')->join('loan_product', 'loan_product_guarantor_setting.loan_product_id=loan_product.id')->join('guarantor_setting', 'loan_product_guarantor_setting.guarantor_setting_id=guarantor_setting.id');

        $this->db->select('loan_product_guarantor_setting.id AS id,setting,guarantor_setting.description,guarantor_setting.id AS guarantor_setting_id,loan_product.id AS loan_product_id');
        
        $this->db->where('loan_product_guarantor_setting.status_id', $this->input->post('status_id'));
        if ($filter === FALSE) {
            $query = $this->db->get();
            //print_r($this->db->last_query()); die();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('loan_product_guarantor_setting.loan_product_id',$filter);
                $query = $this->db->get();

                //print_r($this->db->last_query()); die();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                // $this->db->where('status_id=',0);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
    public function set() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id']);
        $data['status_id'] = '1';
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];

        $this->db->insert('loan_product_guarantor_setting', $data);
        return $this->db->insert_id();
    }
    
    public function update() {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['id'];
        $data['status_id'] = '1';
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('loan_product_guarantor_setting', $data);
    }
    /**
     * This method Deactivates loan_product_guarantor_setting data from the database
     */
    public function delete_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->delete('loan_product_guarantor_setting');
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete('loan_product_guarantor_setting');
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * This method deactivate loan_product_guarantor_setting data from the database
     */
    public function change_status_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' =>'0');
            $this->db->where('id', $id);
            $query = $this->db->update('loan_product_guarantor_setting',$data);
            print_r($this->db->last_query());die();
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $data = array('status_id' =>'0');
            $this->db->where('id', $id);
            $query = $this->db->update('loan_product_guarantor_setting',$data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

}
