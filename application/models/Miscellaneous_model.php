<?php

class Miscellaneous_model extends CI_Model {

    Public function __construct() {
        parent :: __construct();
    }

    public function get($filter = FALSE) {
        $this->db->select('repayment_made_every.id,made_every_name');
        $query = $this->db->from('repayment_made_every');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            $this->db->where($filter);
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function get_member_subscription_schedule($start_date,$end_date,$table1,$column) {
        $this->db->select('m.*');
        $this->db->from($table1.' table1');
        $this->db->join("fms_member m", "m.id=table1.".$column,"RIGHT OUTER");
        $this->db->where('table1.'.$column.' IS null');
        $this->db->where('m.date_registered > ', $start_date);
        $this->db->where('m.date_registered < ', $end_date);
        $query = $this->db->get();
        return $query->result_array();
    }


    
     public function get_all_member_subscription_schedule() {
        $this->db->select('m.*');
        $this->db->from('fms_member m');
        $this->db->where('m.status_id=1');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function set_data($filter) {
        $this->db->select('*');
        $this->db->from('fms_auto_savings');
        if ($filter == false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('product_id', $filter);
                $query = $this->db->get();
                //print_r( $this->db->last_query()); die;
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                // print_r( $this->db->last_query()); die;
                return $query->result_array();
            }
        }
        
    }

    public function update_data($data,$id) {
        $this->db->where('id', $id);
        $last_pay_date =  $this->db->update('fms_auto_savings', $data);
        if($last_pay_date == true)
            return $data;
        else
            return false;

    }


    public function get_defaulters($table,$column){
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($column.' = 0');
        $query = $this->db->get();
        return $query->result_array();
    }
     public function depre_appre_type() {
        $this->db->select('*');
        $this->db->from('fms_depre_appre_option');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function get_months() {
        $this->db->select('*');
        $this->db->from('fms_months');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_available_to($filter = FALSE) {
        $this->db->select('available_to.id,name');
        $query = $this->db->get('available_to');
        return $query->result_array();
    }
   /*  public function get_account_cat($filter = FALSE) {
        $this->db->select('fms_account_categories.id,cat_name');
        $query = $this->db->get('fms_account_categories');
        return $query->result_array();
    }
    public function get_account_subcat($filter = FALSE) {
        $this->db->select('fms_accounts_chart.id,account_name');
        if ($filter === "Credit") {
            $this->db->where('id',$filter=2);
            $query = $this->db->get('fms_accounts_chart');
            return $query->result_array();
        } else {
            $this->db->where('bank_or_cash',$filter);
            $query = $this->db->get('fms_accounts_chart');
            return $query->result_array();
        }
    } */
    
    public function get_payment_mode($filter = FALSE) {
        $this->db->select("id,payment_mode");
        if ($filter !== FALSE && !(is_numeric($filter))) {
            $this->db->where($filter);
        }
        $query = $this->db->get("payment_mode");
        // print_r($this->db->last_query()); die;
        return $query->result_array();
    }
    public function get_payment_engine() {
        $this->db->select("*");
        $query = $this->db->get("payment_engines");
        return $query->result_array();
    }
    public function get_depreciation_method($filter = FALSE) {
        $this->db->select("id,method_name");
        $query = $this->db->get("depreciation_method");
        return $query->result_array();
    }
    public function check_org_module($module_id,$org_id= FALSE) {
        if ($org_id == FALSE) {
            $org_id= $_SESSION['organisation_id'];
        }
        $this->db->select("id");
        $this->db->from("org_modules");
        $this->db->where("org_modules.module_id=",$module_id);
        $this->db->where("org_modules.organisation_id=",$org_id);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function get_charge_trigger($filter = FALSE) {
        $this->db->from('fms_charge_trigger');

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            $this->db->where($filter);
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function get_product_type() {
        $q = $this->db->get('deposit_product_type');
        return $q->result_array();
    }

    public function get_daysinyear() {
        $q = $this->db->get('days_in_year');
        return $q->result_array();
    } 
    public function get_account_balance_interest() {
        $q = $this->db->get('account_balance_for_interest');
        return $q->result_array();
    }

    public function get_interest_cal_mthd() {
        $q = $this->db->get('interest_cal_method');
        return $q->result_array();
    }

    public function get_wheninterestispaid() {
        $q = $this->db->get('wheninterestispaid');
        return $q->result_array();
    }

    public function get_term_time_unit() {
        $q = $this->db->get('term_time_unit');
        return $q->result_array();
    }

    // amountcalculatedas dropdown
    public function get_amountcalculatedas($filter = FALSE) {
        $this->db->select('id AS amountcalculatedas_id,amountcalculatedas');
        $this->db->where("id!=",3);
        $q = $this->db->get('amountcalculatedas');
        return $q->result_array();
    }
    public function get_amountcalculatedas_other($filter = FALSE) {
        $this->db->select('id AS amountcalculatedas_id,amountcalculatedas');
        $q = $this->db->get('amountcalculatedas');
        return $q->result_array();
    }
     public function get_loan_charge_trigger($filter = FALSE) {
        $this->db->select('*');
        $q = $this->db->get('loan_charge_trigger');
        return $q->result_array();
    }
    // fee types dropdown
    public function get_feetype($filter = FALSE) {
        $this->db->select('id AS feetype_id,feetype');
        $q = $this->db->get('feetype');
        return $q->result_array();
    }

    // fee get_date_application_methods dropdown
    public function get_date_application_mtd($filter = FALSE) {
        $q = $this->db->get('fms_date_application_methods');
        return $q->result_array();
    }

// relationship_type dropdown
    public function get_relationship_type($filter = FALSE) {
        $q = $this->db->get('relationship_type');
        return $q->result_array();
    }
// holiday_frequency_every dropdown
    public function get_holiday_frequency_every($filter = FALSE) {        
        $q = $this->db->get('holiday_frequency_every');
        return $q->result_array();
    }
// holiday_frequency_day dropdown
    public function get_holiday_frequency_day($filter = FALSE) {        
        $q = $this->db->get('holiday_frequency_day');
        return $q->result_array();
    }
// holiday_frequency_of dropdown
    public function get_holiday_frequency_of($filter = FALSE) {        
        $q = $this->db->get('holiday_frequency_of');
        return $q->result_array();
    }
    
    //list of possible incomes earned by the organization
    public function get_income_source( $filter = FALSE ) {
        $this->db->from('income_source');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    //list of account types
    public function get_account_types( $filter = FALSE ) {
        $this->db->from('account_type');
        $query = $this->db->get();
        return $query->result_array();
    }

    //list of loan types
    public function get_loan_type( $filter = FALSE ) {
        $this->db->from('group_loan_type');
        $query = $this->db->get();
        return $query->result_array();
    }

    //list of when to start subscription repayment options
    public function get_repayment_start_options( $filter = FALSE ) {
        $this->db->from('first_repayment_start_options');
        $query = $this->db->get();
        return $query->result_array();
    }

    //list of marital status options
    public function get_marital_status_options( $filter = FALSE ) {
        $this->db->from('marital_status');
        $query = $this->db->get();
        return $query->result_array();
    }
    //insertion test login log.
    public function set_login_log($post_data=[]){
        return $this->db->insert('fms_login_log',$post_data);

    }
    //
    public function set_activity_logs($post_data=[]){
        return $this->db->insert('fms_activity_log',$post_data);

    }

    public function get_min_last_penalty_pay_date($filter = false) {
        $this->db->select('min(last_saving_penalty_pay_date) as min_last_saving_penalty_pay_date');
        $this->db->from('fms_auto_savings s_auto');
        $this->db->join('fms_savings_product sp', 'sp.id=s_auto.product_id', 'left');
        $this->db->join('fms_account_states acc', "acc.account_id = s_auto.savings_account_id", 'left');
        $this->db->where($filter);
        $query = $this->db->get();
        return $query->row_array();
            
    }

    public function update_last_penalty_pay_date($post_data=[], $filter) {
        $this->db->where($filter);
        return $this->db->update('fms_auto_savings', $post_data);
    }

}
