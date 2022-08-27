<?php

/**
 * Description of Loan_product_fee_model
 *
 * @author Eric
 */
class Loan_product_fee_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select("loan_product_fees.id,loanfee_id,loan_product_fees.income_account_id,loan_product_fees.income_receivable_account_id,feename,feetype,loan_fees.fee_applied_to,"
                . "amountcalculatedas,amountcalculatedas_id,feetype_id,chargetrigger_id , amount,concat(ac.account_code,' ', ac.account_name) income_account, "
                . "concat(ac1.account_code,' ', ac1.account_name) income_receivable_account,loan_product_fees.loanproduct_id");
        $this->db->from('loan_product_fees')
                ->join('loan_fees', 'loan_product_fees.loanfee_id=loan_fees.id')
                ->join('feetype', 'loan_fees.feetype_id=feetype.id')
                ->join('amountcalculatedas', 'loan_fees.amountcalculatedas_id=amountcalculatedas.id');
        
        $this->db->join("accounts_chart ac", "ac.id=loan_product_fees.income_account_id","LEFT");
        $this->db->join("accounts_chart ac1", "ac1.id=loan_product_fees.income_receivable_account_id","LEFT");
        if ($this->input->post('status_id') != NULL && $this->input->post('status_id') !='') {
            $this->db->where('loan_product_fees.status_id',$this->input->post('status_id'));
        }else{
            $this->db->where('loan_product_fees.status_id=1');
        }
        $this->db->where('loan_fees.status_id=1');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('loan_product_fees.id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                // print_r($this->db->last_query());die;
                return $query->result_array();
            }
        }
    }

    public function get1($filter = FALSE) {
        // get product fees on Loan topup
        $this->db->select("loan_product_fees.id,loanfee_id,loan_product_fees.income_account_id,loan_product_fees.income_receivable_account_id,feename,feetype,loan_fees.fee_applied_to,"
                . "amountcalculatedas,amountcalculatedas_id,feetype_id,chargetrigger_id , amount,concat(ac.account_code,' ', ac.account_name) income_account, "
                . "concat(ac1.account_code,' ', ac1.account_name) income_receivable_account,loan_product_fees.loanproduct_id");
        $this->db->from('loan_product_fees')
                ->join('loan_fees', 'loan_product_fees.loanfee_id=loan_fees.id')
                ->join('feetype', 'loan_fees.feetype_id=feetype.id')
                ->join('amountcalculatedas', 'loan_fees.amountcalculatedas_id=amountcalculatedas.id');
        
        $this->db->join("accounts_chart ac", "ac.id=loan_product_fees.income_account_id","LEFT");
        $this->db->join("accounts_chart ac1", "ac1.id=loan_product_fees.income_receivable_account_id","LEFT");
        if ($this->input->post('status_id') != NULL && $this->input->post('status_id') !='') {
            $this->db->where('loan_product_fees.status_id',$this->input->post('status_id'));
        }else{
            $this->db->where('loan_product_fees.status_id=1');
        }
        $this->db->where('loan_fees.status_id=1');
        if ($filter === FALSE) {
            $query = $this->db->get1();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('loan_product_fees.id', $filter);
                $query = $this->db->get1();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get1();
                // print_r($this->db->last_query());die;
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

        $this->db->insert('loan_product_fees', $data);
        return $this->db->insert_id();
    }

    public function update() {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['id'];
        $data['status_id'] = '1';
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('loan_product_fees', $data);
    }

    /**
     * This method Deactivates loan_product_fees data from the database
     */
    public function delete_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->delete('loan_product_fees');
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete('loan_product_fees');
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * This method deactivate loan_product_fees data from the database
     */
    public function change_status_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' => '2');
            $this->db->where('id', $id);
            $query = $this->db->update('loan_product_fees', $data);
            print_r($this->db->last_query());
            die();
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $data = array('status_id' => '2');
            $this->db->where('id', $id);
            $query = $this->db->update('loan_product_fees', $data);
            //print_r($this->db->last_query());die(); print_r($this->db->last_query());die();
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

}
