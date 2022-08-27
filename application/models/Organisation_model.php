<?php

/**
 * Description of Organization_model
 *
 * @author Diphas
 */
class Organisation_model extends CI_Model {

    public function __construct() {
        $this->load->database();
        $my_sql = "alter table fms_organisation "
                . "change letter_applied_member_fees letter_applied_member_fees varchar(10) null default null, change letter_applied_loan_fees letter_applied_loan_fees varchar(10) null default null, change letter_applied_share_fees letter_applied_share_fees varchar(10) null default null, change letter_applied_loan_approval letter_loan_approval varchar(10) null default null, change letter_loan_ref_no letter_loan_ref_no varchar(10) null default null, "
                . "add column def_loan_portfolio_account_id int(11) null default null, add column def_loan_portfolio_recvable_acc_id int(11) null default null, "
                . "add column def_loan_fees_income_acc_id int(11) null default null, add column def_loan_fees_recvable_acc_id int(11) null default null, "
                . "add column def_loan_penalty_income_acc_id int(11) null default null, add column def_loan_penalty_recvable_acc_id int(11) null default null, "
                . "add column def_loan_interest_income_acc_id int(11) null default null, add column def_loan_interest_recvable_acc_id int(11) null default null, "
                . "add column def_customer_deposits_payable_acc_id int(11) null default null, add column def_savings_fees_income_acc_id int(11) null default null, "
                . "add column def_savings_fees_recvable_acc_id int(11) null default null, add column def_saving_interest_expense_acc_id int(11) null default null, "
                . "add column def_saving_interest_payable_acc_id int(11) null default null, add column def_customer_shares_payable_acc_id int(11) null default null, "
                . "add column def_share_fees_income_acc_id int(11) null default null, add column def_share_fees_recvable_acc_id int(11) null default null, "
                . "add column def_share_interest_expense_acc_id int(11) null default null, add column def_share_interest_payable_acc_id int(11) null default null, "
                . "add column def_subscription_income_acc_id int(11) null default null, add column def_subscription_fees_recvable_acc_id int(11) null default null, "
                . "add column def_membership_income_acc_id int(11) null default null, add column def_tax_expense_acc_id int(11) null default null, "
                . "add column def_tax_payable_acc_id int(11) null default null, add column def_general_expense_acc_id int(11) null default null, "
                . "add column def_general_expense_payable_acc_id int(11) null default null, add column def_general_income_acc_id int(11) null default null, "
                . "add column def_general_income_recvable_acc_id int(11) null default null";
    }

    public function get($filter = FALSE) {
        $this->db->select('*');
        $this->db->from('organisation');
        $this->db->where_in('status_id',array('1','2'));
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('organisation.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
    public function get_org($filter = FALSE,$organisation=false) {
        $organisation_id=$organisation==false?$_SESSION['organisation_id']:$organisation;
        $this->db->from('branch');
        if ($filter === FALSE) {
            $this->db->where('organisation_id',$organisation_id);
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)){
                $this->db->where('branch.id=' . $filter);
                $query = $this->db->get();
                //echo $this->db->last_query();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set($file_name) {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['tbl']);
        if (empty($_FILES['organisation_logo']['name'])) {
            unset( $data['organisation_logo'], $data['tbl']);
        }else{
            $data['organisation_logo'] = $file_name;
        }
        $data['account_counter'] = 0;
        $data['account_format'] = 1;
        $data['client_counter'] = 0;
        $data['client_format'] = 1;
        $data['client_letter'] = 'A';
        $data['staff_counter'] = 0;
        $data['staff_format'] = 1;
        $data['staff_letter'] = 'A';
        $data['group_counter'] = 0;
        $data['group_format'] = 1;
        $data['group_letter'] = 'A';
        $data['share_counter'] = 0;
        $data['share_format'] = 1;
        $data['share_letter'] = 'A';
        $data['letter_applied_member_fees'] = 'A';
        $data['letter_applied_loan_fees'] = 'A';
        $data['letter_applied_share_fees'] = 'A';
        $data['letter_loan_approval'] = 'A';
        $data['letter_loan_ref_no'] = 'A';
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $inser = $this->db->insert('organisation', $data);
        $here = $this->db->insert_id();

        if ($inser === true) {
            return $here;
        } else {
            return false;
        }
    }

    public function update($file_name) {
        $data = $this->input->post(NULL, TRUE);
        if (empty($_FILES['organisation_logo']['name'])) {
            unset( $data['organisation_logo'], $data['tbl'],$data['btn_submit']);
        }else{
            $data['organisation_logo'] = $file_name;
             unset( $data['tbl'],$data['btn_submit']);
        }
        $data['date_modified'] = time();
        $data['modified_by'] = $_SESSION['id'];

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('organisation', $data);
    }
 
    public function get_org_modules($org_id) {
            $this->db->from('org_modules om');
            $this->db->where('om.organisation_id', $org_id);
            $query = $this->db->get();
            return $query->result_array();
    }
    public function get_module_access($module_id,$org_id) {
        $this->db->from('org_modules om');
        $this->db->where('om.organisation_id', $org_id);
        $this->db->where("om.module_id", $module_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function delete() {
        if($this->delete_all_accounts($this->input->post('id'))){
        $this->db->where('id', $this->input->post('id'));
        return $this->db->delete('organisation');
        } else{
            return false;
        }
    }
    public function delete_all_accounts($org_id) {
        $this->db->where('organisation_id',$org_id);
        if($this->db->delete('fms_accounts_chart')){
        return $this->delete_all_modules($org_id);
        }else{
            return false;
        }
    }
    public function delete_all_modules($org_id) {
        $this->db->where('organisation_id',$org_id);
        return $this->db->delete('fms_org_modules');
    }

    public function change_status() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('organisation', ['status_id' => $this->input->post('status_id')]);
    }

}
