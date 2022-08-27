<?php

/**
 * Description of Loans_col_setup
 *
 * @author allan_jes
 */
if (!defined('BASEPATH')){
    exit("No direct script access allowed");
}
class Loans_col_setup {

    protected $CI;

    private $columns = ["a.id",  "a.loan_no", "a.member_id", "concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name",
        "a.credit_officer_id","concat(d.firstname, ' ', d.lastname, ' ', d.othernames) credit_officer_name","a.approved_installments","a.approved_repayment_frequency",
        "a.approved_repayment_made_every","a.group_loan_id", "a.status_id", "a.loan_product_id","e.min_guarantor","e.min_collateral","e.product_name", 
        "a.requested_amount", "a.application_date","a.disbursement_date", "a.disbursement_note", "a.interest_rate","a.offset_made_every", "a.offset_period",
        "f.made_every_name AS offset_every","g.made_every_name AS approved_made_every_name", "a.grace_period", "a.repayment_frequency",
        "repayment_made_every.made_every_name", "a.repayment_made_every","a.installments", "a.penalty_calculation_method_id", "a.penalty_tolerance_period",
        "a.penalty_rate_charged_per", "a.penalty_rate", "a.link_to_deposit_account","a.loan_purpose", "loan_state.comment","loan_state.action_date",
        "a.amount_approved", "a.approval_date","ac.account_name as fund_source_account","e.fund_source_account_id","e.interest_income_account_id", "a.approved_by","a.suggested_disbursement_date", "a.approval_note", "a.created_by", "a.modified_by", "a.date_created", "a.date_modified","rsdl.paid_amount", "rsdl.paid_interest","rsdl.paid_principal",
        "state_id","state.state_name","approvals","ifnull(approvals,0) _approvals","product_type_id","disburse_data.expected_interest","disburse_data.expected_principal","next_pay_date","last_pay_date","a.preferred_payment_id","payment_mode","ac_name","ac_number","bank_branch","bank_name","payment_details.phone_number","a.topup_application","a.linked_loan_id","ifnull(b.disbursed_amount,0) disbursed_amount","ifnull(rsdf.paid_principal,0) parent_paid_principal","ifnull(rsdf.paid_interest,0) parent_paid_interest","ifnull(c.expected_interest,0) parent_expected_interest","ifnull(c.expected_principal,0) parent_expected_principal","ifnull(amount_in_demand,0) amount_in_demand","ifnull(days_in_demand,0) days_in_demand","ifnull(principal_in_demand,0) principal_in_demand","m.branch_id"];//,"(principal_in_demand*(days_in_demand-a.penalty_tolerance_period)*(a.penalty_rate/100)) penalty_demanded"
    
    public function __construct() {
        // Assign the CodeIgniter super-object
        $this->CI = & get_instance();
        $this->CI->load->model('RolePrivilege_model', '', TRUE);
    }
    
    public function get_fields(){
        $return_cols = [];
        if ($this->CI->input->post('state_id') ==1) {
            $return_cols = ["a.loan_no",  "concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name","e.product_name","concat(d.firstname, ' ', d.lastname, ' ', d.othernames) credit_officer_name","a.requested_amount","a.application_date","loan_state.comment","a.id"];
        }

        if ($this->CI->input->post('state_id') ==2 || $this->CI->input->post('state_id') ==4) {
            $return_cols = ["a.loan_no",  "concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name","e.product_name","a.requested_amount","a.application_date","loan_state.action_date","loan_state.comment","a.id"];
        }
        if ($this->CI->input->post('state_id') ==3) {

            $return_cols = ["a.loan_no",  "concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name","e.product_name","a.requested_amount","a.application_date","loan_state.action_date","loan_state.comment"];
        }
        if ($this->CI->input->post('state_id') ==5) {
            $return_cols = ["a.loan_no",  "concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name","concat(d.firstname, ' ', d.lastname, ' ', d.othernames) credit_officer_name","a.requested_amount","a.interest_rate","a.application_date","a.id"];
        }

        if ($this->CI->input->post('state_id') ==6) {
            $return_cols = ["a.loan_no",  "concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name","e.product_name","a.requested_amount","a.amount_approved","loan_state.action_date","a.suggested_disbursement_date","a.approval_note","a.id"];
        }
        if ( $this->CI->input->post('date_to') == NULL && $this->CI->input->post('date_to') == ''  && $this->CI->input->post('state_id') ==7 ) {//for active tab we need a LEFT JOIN
            $return_cols = [
                "a.loan_no",  "concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name","group.group_name","a.requested_amount","disburse_data.expected_principal",
                "disburse_data.expected_interest","rsdl.paid_amount","ifnull(amount_in_demand,0) amount_in_demand","ifnull(days_in_demand,0) days_in_demand",
                "disburse_data.expected_interest","loan_state.action_date","next_pay_date","last_pay_date","a.id","group_loan_type.type_name as group_type_name"
                ];
        }
        if ( $this->CI->input->post('date_to') != NULL && $this->CI->input->post('date_to') !=='' && $this->CI->input->post('state_id') ==7 ) {//for defaulters and risky tabs we need a FULL JOIN

            $return_cols = ["a.loan_no",  "concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name","e.product_name","a.amount_approved","rsdl.paid_principal","rsdl.paid_interest","total_payment","a.id"];
        }

        if ($this->CI->input->post('state_id') ==8 || $this->CI->input->post('state_id') ==9) {
            $return_cols = ["a.loan_no",  "concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name","e.product_name","disburse_data.expected_principal","disburse_data.expected_interest","rsdl.paid_amount","disburse_data.expected_interest","loan_state.action_date","loan_state.comment","a.id"];
        }
        if ($this->CI->input->post('state_id') ==10) {
            $return_cols = ["a.loan_no",  "concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name","e.product_name","a.amount_approved","rsdl.paid_amount","loan_state.action_date","a.id"];
        }

        if ($this->CI->input->post('state_id') ==12) {
            $return_cols = ["a.loan_no",  "concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name","e.product_name","a.amount_approved","rsdl.paid_amount","loan_state.action_date","loan_state.comment","a.id"];
        }

        if ($this->CI->input->post('state_id') ==13 || $this->CI->input->post('report')=='true') {
             $return_cols = ["a.loan_no",  "concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name","e.product_name","disburse_data.expected_principal","disburse_data.expected_interest","rsdl.paid_amount","disburse_data.expected_interest","last_pay_date","days_in_arrears","a.id"];
        }
        if ($this->CI->input->post('state_id') ==14) {
            $return_cols = ["a.loan_no",  "concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name","e.product_name","disburse_data.expected_principal","disburse_data.expected_interest","rsdl.paid_amount","disburse_data.expected_interest","loan_state.action_date","last_pay_date","a.id"];
        }
        $other_cols = array_diff($this->columns, $return_cols);
        return array_merge($return_cols,$other_cols);
    }

}
