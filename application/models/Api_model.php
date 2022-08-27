<?php
/**
 * Description of Shares_model
 *
 * @author Joshua
 */
class Api_model extends CI_Model {
    private $columns = [];

    public function __construct() {
        //$this->load->library("loans_col_setup");
        $this->table = 'client_loan';
        $this->columns = $this->loans_col_setup->get_fields();
    }

    public function get($limit=false, $filter = false) {
        $this->db->select('tn.*,sa.account_no,sa.client_type,COALESCE((SELECT SUM(ifnull(credit,0)) - SUM(ifnull(debit,0))
                              FROM fms_transaction b
                              WHERE tn.transaction_date >= b.transaction_date 
                               AND b.account_no_id = tn.account_no_id
                               AND b.status_id = 1 ),0)
                                 AS end_balance,tc.payment_mode,tt.type_name');
        $this->db->from('fms_transaction tn');
        $this->db->join('fms_savings_account sa', 'tn.account_no_id=sa.id','left');
        $this->db->join('fms_transaction_type tt', 'tn.transaction_type_id=tt.id','left');
        $this->db->join('fms_payment_mode tc', 'tn.payment_id=tc.id','left');
        $this->db->order_by('tn.transaction_date DESC');
        $this->db->order_by('tn.transaction_type_id DESC');

        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else if (is_numeric($filter)) { //when given the primary key
            $this->db->where('tn.id', $filter);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->where($filter);
            $query = $this->db->get();
            return $query->result_array();
        }
    }

        private function get_select() {
        $this->db->from('client_loan a');
        $this->db->join('staff s', 'a.credit_officer_id = s.id', 'left');
        $this->db->join('user d', 's.user_id = d.id', 'left');
        $this->db->join('loan_product e', 'e.id=a.loan_product_id')
                ->join('repayment_made_every', 'repayment_made_every.id=a.repayment_made_every', "left")
                ->join('repayment_made_every g', 'g.id=a.approved_repayment_made_every', "left");
        $this->db->join('repayment_made_every f', 'f.id=a.offset_made_every', "left");
        $this->db->join('accounts_chart ac', 'ac.id=e.fund_source_account_id', "left");
        $this->db->join("$this->max_state_id loan_state", 'loan_state.client_loan_id=a.id', "left");
        $this->db->join("state", 'state.id=loan_state.state_id', 'left');
        $this->db->join("$this->paid_amount rsdl", 'rsdl.client_loan_id=a.id', 'left');
        $this->db->join("client_loan b", 'b.id=a.linked_loan_id AND a.topup_application =1', 'left');
        $this->db->join("$this->paid_amount rsdf", 'rsdf.client_loan_id=b.id', 'left');
        $this->db->join("$this->disburse_data c", 'c.client_loan_id=b.id', 'left');
        $this->db->join("$this->approvals fla", 'fla.client_loan_id=a.id', 'left');
        $this->db->join("$this->pay_day pay_day", 'pay_day.client_loan_id=a.id', 'left');
        $this->db->join("$this->disburse_data disburse_data", 'disburse_data.client_loan_id=a.id', 'left');
        $this->db->join("$this->amount_in_demand amount_in_demand", 'amount_in_demand.client_loan_id=a.id', 'left');
        $this->db->join("$this->days_in_demand days_in_demand", 'days_in_demand.client_loan_id=a.id', 'left');
        $this->db->join('payment_details', 'a.id =payment_details.client_loan_id AND payment_details.status_id =1', 'left');
        if (isset($_SESSION['role']) && ($_SESSION['role'] == 'Credit Officer' || $_SESSION['role_id'] == 4)) {
            $this->db->where('a.credit_officer_id', $_SESSION['staff_id']);
        }
        if ($this->input->post('client_id') != "" && !empty($this->input->post('client_id'))) {
            $this->db->where('a.member_id', $this->input->post('client_id'));
        }
        if ($this->input->post('group_id') != "" && !empty($this->input->post('group_id'))) {
            $this->db->select("group_name");
            $this->db->where('a.group_loan_id', $this->input->post('group_id'));
            $this->db->join('member m', 'a.member_id = m.id', 'left');
            //$this->db->join('user_address ua',' m.user_id=ua.user_id', 'left');
            $this->db->join('user c', 'm.user_id = c.id', 'left');
            $this->db->join('group_loan', 'group_loan.id =a.group_loan_id ');
            $this->db->join('group', 'group.id =group_loan.group_id ');
        } else {
            $this->db->where("a.group_loan_id IS NULL");
            $this->db->join('member m', 'a.member_id = m.id');
            //$this->db->join('user_address ua',' m.user_id=ua.user_id', 'left');
            $this->db->join('user c', 'm.user_id = c.id');
        }
    }

     private function conditional_filters() {
        if ($this->input->post('date_to') == NULL && $this->input->post('date_to') == '' && $this->input->post('state_id') == 7) {//for active tab we need a LEFT JOIN
            $this->db->join("$this->unpaid_installments_data repayment_schedule", 'repayment_schedule.client_loan_id=a.id', 'left');
            //$this->db->where("loan_state.action_date <='". $this->input->post('date_to')."'");
            //$this->db->where("loan_state.action_date >='". $this->input->post('date_from')."'");
        }
        if ($this->input->post('date_to') != NULL && $this->input->post('date_to') !== '' && $this->input->post('state_id') == 7) {//for defaulters and risky tabs we need a FULL JOIN
            $this->db->join("$this->unpaid_installments_data repayment_schedule", 'repayment_schedule.client_loan_id=a.id');
        }

        if ($this->input->post('state_id') == 13 || $this->input->post('report') == 'true') {
            $loans_with_last_repayment_date = "(SELECT *,DATEDIFF(CURDATE(),repayment_date) days_in_arrears FROM fms_repayment_schedule
                WHERE id in ( SELECT MAX(id) from fms_repayment_schedule WHERE status_id=1 GROUP BY client_loan_id ))";
            $this->db->join("$loans_with_last_repayment_date repayment_schedule1", 'repayment_schedule1.client_loan_id=a.id', 'left');
        }

        if ($this->input->post('date_to') != NULL && $this->input->post('date_from') !== '' && $this->input->post('state_id') == 1) {
            $this->db->where("a.application_date <='" . $this->input->post('date_to') . "'");
            $this->db->where("a.application_date >='" . $this->input->post('date_from') . "'");
        }
        //End here
        if ($this->input->post('date_to') != NULL && $this->input->post('date_from') !== '' && $this->input->post('state_id') > 1 && $this->input->post('state_id') != 7) {
            $this->db->where("loan_state.action_date <='" . $this->input->post('date_to') . "'");
            $this->db->where("loan_state.action_date >='" . $this->input->post('date_from') . "'");
        }
        if ($this->input->post('state_id') !== NULL && is_numeric($this->input->post('state_id'))) {
            $this->db->where("loan_state.state_id=", $this->input->post('state_id'));
        }
        //Used mainly in loan Reports
        if ($this->input->post('state_ids') !== NULL && is_array($this->input->post('state_ids'))) {
            $this->db->where_in("loan_state.state_id", $this->input->post('state_ids'));
        }
        if ($this->input->post('report') == 'true') {
            $this->db->join("$this->secure_unsecured_loans secure_unsecured_loans", 'secure_unsecured_loans.client_loan_id=a.id', 'left');
        }
        if ($this->input->post('min_amount') != 'All' && $this->input->post('min_amount') != '') {
            $this->db->where("a.requested_amount >=", $this->input->post('min_amount'));
        }
        if ($this->input->post('max_amount') != 'All' && $this->input->post('max_amount') != '') {
            $this->db->where("a.requested_amount <=", $this->input->post('max_amount'));
        }
        if ($this->input->post('min_days_in_arrears') != 'All' && $this->input->post('min_days_in_arrears') != '') {
            $this->db->where("days_in_arrears >=", $this->input->post('min_days_in_arrears'));
        }
        if ($this->input->post('max_days_in_arrears') != 'All' && $this->input->post('max_days_in_arrears') != '') {
            $this->db->where("days_in_arrears <=", $this->input->post('max_days_in_arrears'));
        }
        if ($this->input->post('product_id') != 'All' && $this->input->post('product_id') != '') {
            $this->db->where("a.loan_product_id =", $this->input->post('product_id'));
        }
        if ($this->input->post('loan_type') != 'All' && $this->input->post('loan_type') == '1') {
            $this->db->where("security_amount > 0 ");
        }
        if ($this->input->post('loan_type') != 'All' && $this->input->post('loan_type') == '0') {
            $this->db->where("security_amount IS NULL");
        }
        if ($this->input->post('due_days') != 'All' && $this->input->post('condition') == '1') {
            $this->db->where("days_in_demand >=",$this->input->post('due_days'));
        }
        if ($this->input->post('due_days') != 'All' && $this->input->post('condition') == '2') {
            $this->db->where("days_in_demand <=",$this->input->post('due_days'));
        }
        if ($this->input->post('credit_officer_id') != 0 && $this->input->post('credit_officer_id') != '') {
            $this->db->where("a.credit_officer_id",$this->input->post('credit_officer_id'));
        }
        if ($this->input->post('next_due_month') != 'All' && $this->input->post('next_due_month') != '') {
            $this->db->where("MONTH(next_pay_date)",$this->input->post('next_due_month'));
        }
        if ($this->input->post('next_due_year') != 'All' && $this->input->post('next_due_year') != '') {
            $this->db->where("YEAR(next_pay_date)",$this->input->post('next_due_year'));
        }
    }

    public function get_loans($filter = FALSE) {
        $this->db->select(implode(", ", $this->columns), FALSE);
        $this->get_select();
        $this->conditional_filters();

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("m.id=", $filter);
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