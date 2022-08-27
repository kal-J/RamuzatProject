<?php
/**
 * Description of loan_product_model
 *
 * @author Eric
 */
class Loan_product_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select('loan_product.*,penalty_calculation_method.method_description,loan_product_type.type_name,available_to.name,'
                . 'repayment_made_every.made_every_name,a.made_every_name AS offset_every,b.made_every_name AS penalty_charged_every');
        
        $this->db->select("concat(ac_loan.account_code,' ', ac_loan.account_name) loan_receivable_account, concat(ac_penalty.account_code,' ', ac_penalty.account_name) penalty_income_account, concat(ac_penalty1.account_code,' ', ac_penalty1.account_name) penalty_receivable_account,
        concat(ac_interest.account_code,' ', ac_interest.account_name) interest_income_account, concat(ac_interest1.account_code,' ', ac_interest1.account_name) interest_receivable_account,
        concat(ac_bad_debt.account_code,' ', ac_bad_debt.account_name) bad_debt_account,concat(ac_fund_source.account_code,' ', ac_fund_source.account_name) fund_source_account,concat(ac_miscellaneous.account_code,' ', ac_miscellaneous.account_name) miscellaneous_account ");

        $this->db->from('loan_product')
                ->join('loan_product_type','loan_product_type.id=loan_product.product_type_id','left')
                ->join('penalty_calculation_method','penalty_calculation_method.id=loan_product.penalty_calculation_method_id','left')
                ->join('available_to','available_to.id=loan_product.available_to_id','left')
                ->join('repayment_made_every','repayment_made_every.id=loan_product.repayment_made_every','left')
                ->join('repayment_made_every a','a.id=loan_product.offset_made_every','left')
                ->join('repayment_made_every b','b.id=loan_product.penalty_rate_chargedPer','left');

        $this->db->join("accounts_chart ac_loan", "ac_loan.id=loan_product.loan_receivable_account_id","LEFT");
        $this->db->join("accounts_chart ac_penalty", "ac_penalty.id=loan_product.penalty_income_account_id","LEFT");
        $this->db->join("accounts_chart ac_penalty1", "ac_penalty1.id=loan_product.penalty_receivable_account_id","LEFT");
        $this->db->join("accounts_chart ac_interest", "ac_interest.id=loan_product.interest_income_account_id","LEFT");
        $this->db->join("accounts_chart ac_interest1", "ac_interest1.id=loan_product.interest_receivable_account_id","LEFT"); 
        $this->db->join("accounts_chart ac_bad_debt", "ac_bad_debt.id=loan_product.written_off_loans_account_id","LEFT");
        $this->db->join("accounts_chart ac_miscellaneous", "ac_miscellaneous.id=loan_product.miscellaneous_account_id","LEFT");
        $this->db->join("accounts_chart ac_fund_source", "ac_fund_source.id=loan_product.fund_source_account_id","LEFT");
        
        if ($filter === FALSE) {
            //$this->db->where('loan_product.status_id',$this->input->post('status_id'));
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)){
                $this->db->where('loan_product.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['tbl']);
        $data['date_created'] = time();
        $data['created_by'] =$_SESSION['id'];

        $this->db->insert('loan_product', $data);
        return $this->db->insert_id();
    }
	
    public function update() {
        $installment=$this->input->post('max_repayment_installments');
        
        $data = $this->input->post(NULL, TRUE);
        unset($data['id']);
        if (isset($installment)) {
            $data['status_id']='1';
        }
        $data['modified_by'] = $_SESSION['id'];
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('loan_product', $data);
    }
    /**
     * This method Deactivates loan_product data from the database
     */
    public function delete_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->delete('loan_product');
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete('loan_product');
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
            $data = array('status_id' =>'0');
            $this->db->where('id', $id);
            $query = $this->db->update('loan_product',$data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $data = array('status_id' =>'0');
            $this->db->where('id', $id);
            $query = $this->db->update('loan_product',$data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }
    // Loan products dropdown
    public function get_product($filter = FALSE) {
        $this->db->select("loan_product.*,method_description,concat(ac_fund_source.account_code,' ', ac_fund_source.account_name) fund_source_account");
        $q = $this->db->from('loan_product')->join('penalty_calculation_method', 'penalty_calculation_method.id  = loan_product.penalty_calculation_method_id', 'left');
        $this->db->join("accounts_chart ac_fund_source", "ac_fund_source.id=loan_product.fund_source_account_id","LEFT");
        if ($filter === FALSE) {
            $this->db->order_by('loan_product.id', 'ASC');
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)){
                $this->db->where('loan_product.id='.$filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_accounts($filter = FALSE){
        $this->db->select('interest_income_account_id,interest_receivable_account_id,loan_receivable_account_id,penalty_income_account_id, miscellaneous_account_id, written_off_loans_account_id,fund_source_account_id');
        $this->db->from('loan_product');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("loan_product.id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }


}
