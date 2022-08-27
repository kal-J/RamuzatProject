<?php

class Accounts_model extends CI_Model {

    public function __construct() {
        $this->load->database();
        $active=$this->get_current_fiscal_year(1);

        $WHERE ="(transaction_date>='".$active['start_date']."' AND transaction_date<='".$active['end_date']."') AND status_id=1";
        $this->account_summations = "(SELECT status_id, account_id,ABS(SUM(IFNULL(credit_amount,0))) as total_credit,ABS(SUM(IFNULL(debit_amount,0))) as total_debit, ABS(SUM(IFNULL(credit_amount,0))-SUM(IFNULL(debit_amount,0))) as account_balance FROM `fms_journal_transaction_line` WHERE status_id=1 AND  `journal_transaction_id` IN (SELECT `id` FROM `fms_journal_transaction` WHERE status_id=1) AND $WHERE GROUP BY `account_id`) trl";
    }
    public function get_current_fiscal_year($status_id) {
        $this->db->select('id, start_date, end_date');
        $this->db->from('fiscal_year');
        if ($status_id === FALSE) {
            //$this->db->where("fiscal_year.organisation_id=", $org_id);
            $query = $this->db->get();
            return $query->result_array();
        } else {
            //$this->db->where("fiscal_year.organisation_id=", $org_id);
            $this->db->where("fiscal_year.status_id=", $status_id);
            $query = $this->db->get();
            return $query->row_array();
        }
    }

    public function get($filter = FALSE) {
        $this->db->select('ac.id,ac.parent_account_id,ac.account_code,ac.sub_category_id, ac.account_name, ac.description, ac.opening_balance, ac.opening_balance_date, pac.account_name as p_account_name, sub_cat_name,cat_name, normal_balance_side,trl.account_id,total_debit,total_credit,account_balance');
        $this->db->from('accounts_chart ac');
        $this->db->join("accounts_chart pac", "pac.id=ac.parent_account_id", "LEFT");
        $this->db->join("account_sub_categories sc", "sc.id=ac.sub_category_id","LEFT");
        $this->db->join("account_categories acat", "acat.id=sc.category_id","LEFT");
        $this->db->join("$this->account_summations", "ac.id=trl.account_id","left");
        //$this->db->join("fms_journal_transaction jt", "jt.id=trl.journal_transaction_id","left");
        //$this->db->order_by("ac.account_code", "asc");
        if ($this->input->post("status_id") !== NULL) {
            $this->db->where("ac.status_id = ", $this->input->post("status_id"));
        }
        if ($this->input->post("organisation_id") !== NULL) {
            $this->db->where("ac.organisation_id = ", $this->input->post("organisation_id"));
        }

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("ac.id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }


    public function get3($filter = FALSE) {
     
        $this->db->select('ac.id,trl.transaction_date,account_name,normal_balance_side,ABS(SUM(IFNULL(credit_amount,0))) as total_credit,ABS(SUM(IFNULL(debit_amount,0))) as total_debit,ABS(SUM(IFNULL(credit_amount,0))-SUM(IFNULL(debit_amount,0))) as account_balance,sub_category_id');
        $this->db->from('accounts_chart ac');
        $this->db->join("fms_journal_transaction_line trl", "trl.account_id=ac.id");
        $this->db->join("fms_journal_transaction jt", "trl.journal_transaction_id=jt.id");
        $this->db->join("account_sub_categories sc", "sc.id=ac.sub_category_id");
        $this->db->join("account_categories acat", "acat.id=sc.category_id");
        $this->db->where("trl.status_id =1");
        $this->db->where("jt.status_id =1");
        if ($this->input->post("fisc_date_from") !== NULL) {
            $this->db->where("trl.transaction_date >= ", $this->input->post("fisc_date_from"));
            $this->db->where("trl.transaction_date <= ", $this->input->post("fisc_date_to"));
        }
        $this->db->group_by("trl.account_id");
        
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("ac.id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_normal_side($id, $opposite = false) {

        $this->db->select('ac.id,ac.account_code,ac.sub_category_id, ac.account_name, ac.description, ac.opening_balance, ac.opening_balance_date, pac.account_name p_account_name, sub_cat_name, cat_name, normal_balance_side');
        $this->db->from('accounts_chart ac');
        $this->db->join("accounts_chart pac", "pac.id=ac.parent_account_id", "LEFT");
      
        $this->db->join("account_sub_categories sc", "sc.id=ac.sub_category_id");
        $this->db->join("account_categories acat", "acat.id=sc.category_id");

        $this->db->where("ac.id", $id);
        $query = $this->db->get();
        $account_details = $query->row_array();
        return ($account_details['normal_balance_side']==1)?(($opposite !==true)?'debit_amount':'credit_amount'):(($opposite !==true)?'credit_amount':'debit_amount');
    }

    public function set($data = []) {
        if (is_array($data) && empty($data)) {
            $data = $this->input->post(NULL, TRUE);
            $data['organisation_id'] = $_SESSION['organisation_id'];
            $data['opening_balance_date'] = $this->helpers->yr_transformer($data['opening_balance_date']);
        }
        $data['modified_by'] = $_SESSION['id'];

        if (isset($data['id']) && is_numeric($data['id'])) {
            $this->db->where('id', $data['id']);
            unset($data['id'], $data['tbl'], $data['tbl'], $data['new_account_code'],$data['normal_balance_side'], $data["parent_account"], $data["account_type_id"]);
            return $this->db->update('fms_accounts_chart', $data);
        } else {
            unset($data['id'], $data['tbl'], $data['tbl'], $data['new_account_code'], $data['normal_balance_side'], $data["parent_account"], $data["account_type_id"]);
            $data['date_created'] = time();
            $data['created_by'] = $data['modified_by'];
            $this->db->insert('fms_accounts_chart', $data);
            return $this->db->insert_id();
        }
    }

    
    public function set_batch($data){
        return $this->db->insert_batch('fms_accounts_chart', $data);
    }

    public function update() {
        $data = $this->input->post(NULL, TRUE);
        $id = $data;
        unset($data['parent_name'], $data['account_code'], $data['normal_balance_side'], $data['level'], $data['tbl']);
        unset($data['id'], $data['tbl']);
        $data['modified_by'] = $_SESSION['id'];

        if (is_numeric($id)) {
            
        } else {
            return false;
        }
    }

    public function get_pay_with($filter = FALSE,$action=false) {
        $this->db->select('accounts_chart.id,account_name,account_code');
        if (is_numeric($filter)) {
            if ($filter === "3") {
                if(is_numeric($action)){
                $this->db->where("sub_category_id =1");
                }else{
                $this->db->where("sub_category_id IN(8,9,10)");
                }
            } else if ($filter === "2") {
                $this->db->where("sub_category_id =4");
            } else if ($filter === "4") {
                $this->db->where("sub_category_id =5");
            } else if ($filter === "10") {
                $this->db->where("sub_category_id IN(4,5,3)");
            }  else {
                $this->db->where("sub_category_id =3");
            }
        } else {
            $this->db->where($filter);
        }
        $this->db->order_by("account_code", "asc");
        $query = $this->db->get("accounts_chart");
        return $query->result_array();
    }

    public function get_parent_accounts2() {
        //still questonable
        $this->db->select("accounts_chart.id,account_name,account_code");
        //$this->db->where("account_type_id=", 1);
        $this->db->order_by("account_code", "asc");
        $query = $this->db->get("accounts_chart");
        return $query->result_array();
    }

    public function get_subcat_list($filter = FALSE) {
        $this->db->select('acs.*, cat_name');
        $this->db->from('account_sub_categories acs');
        $this->db->join('account_categories ac', 'ac.id=acs.category_id');
        //$this->db->order_by('sub_cat_code', 'asc');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("ac.id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set_organisation_defaults($organisation_id) {
        //set the default accounts for a given organization
        $data = [];
        $data['def_loan_portfolio_account_id'] = $this->set(['sub_category_id' => 1, 'organisation_id' => $organisation_id, 'account_code' => "1-1-1", 'account_name' => "Loan Portfolio", 'description' => "Loan Portfolio"]);
        $data['def_loan_fees_income_acc_id'] = $this->set(['sub_category_id' => 12, 'organisation_id' => $organisation_id, 'account_code' => "4-1-1", 'account_name' => "Loan Fees Income", 'description' => "Loan Fees Income"]);
        $data['def_loan_fees_recvable_acc_id'] = $this->set(['sub_category_id' => 1, 'organisation_id' => $organisation_id, 'account_code' => "1-1-2", 'account_name' => "Receivable Loan Fees", 'description' => "Receivable Loan Fees"]);
        $data['def_loan_penalty_income_acc_id'] = $this->set(['sub_category_id' => 12, 'organisation_id' => $organisation_id, 'account_code' => "4-1-2", 'account_name' => "Loan Default Penalties Income", 'description' => "Loan Default Penalties Income"]);
        $data['def_loan_penalty_recvable_acc_id'] = $this->set(['sub_category_id' => 1, 'organisation_id' => $organisation_id, 'account_code' => "1-1-3", 'account_name' => "Receivable Loan Default Penalties", 'description' => "Receivable Loan Default Penalties"]);
        $data['def_loan_interest_income_acc_id'] = $this->set(['sub_category_id' => 12, 'organisation_id' => $organisation_id, 'account_code' => "4-1-3", 'account_name' => "Client Loans Interest Income", 'description' => "Client Loans Interest Income"]);
        $data['def_loan_interest_recvable_acc_id'] = $this->set(['sub_category_id' => 1, 'organisation_id' => $organisation_id, 'account_code' => "1-1-4", 'account_name' => "Receivable Client Loans Interest", 'description' => "Receivable Client Loans Interest"]);
        $data['def_client_deposits_payable_acc_id'] = $this->set(['sub_category_id' => 8, 'organisation_id' => $organisation_id, 'account_code' => "2-1-1", 'account_name' => "Client Deposits", 'description' => "Client Deposits"]);
        $data['def_savings_fees_income_acc_id'] = $this->set(['sub_category_id' => 12, 'organisation_id' => $organisation_id, 'account_code' => "4-1-4", 'account_name' => "Savings accounts fees/penalties", 'description' => "Income earned from transactions on client savings accounts"]);
        $data['def_savings_fees_recvable_acc_id'] = $this->set(['sub_category_id' => 1, 'organisation_id' => $organisation_id, 'account_code' => "1-1-5", 'account_name' => "Receivable Savings accounts fees", 'description' => "Income anticipated to be earned from client saving accounts' fees"]);
        $data['def_saving_interest_expense_acc_id'] = $this->set(['sub_category_id' => 14, 'organisation_id' => $organisation_id, 'account_code' => "5-1-1", 'account_name' => "Clients Savings accounts interest", 'description' => "Interest earned by clients from holding savings accounts which earn interest"]);
        $data['def_saving_interest_payable_acc_id'] = $this->set(['sub_category_id' => 8, 'organisation_id' => $organisation_id, 'account_code' => "2-1-2", 'account_name' => "Payable savings interest", 'description' => "Interest earned by savings accounts holders but not yet paid out"]);
        $data['def_customer_shares_payable_acc_id'] = $this->set(['sub_category_id' => 8, 'organisation_id' => $organisation_id, 'account_code' => "2-1-3", 'account_name' => "Payments for shares", 'description' => "Account holding money with which shares are paid for"]);
        $data['def_share_fees_income_acc_id'] = $this->set(['sub_category_id' => 12, 'organisation_id' => $organisation_id, 'account_code' => "4-1-5", 'account_name' => "Share fees income", 'description' => "Income earned from charges levied on purchase of shares"]);
        $data['def_share_fees_recvable_acc_id'] = $this->set(['sub_category_id' => 1, 'organisation_id' => $organisation_id, 'account_code' => "1-1-6", 'account_name' => "Receivable share fees", 'description' => "Income anticipated to be earned from the sale of shares"]);
        $data['def_share_interest_expense_acc_id'] = $this->set(['sub_category_id' => 14, 'organisation_id' => $organisation_id, 'account_code' => "5-1-2", 'account_name' => "Share interest expense", 'description' => "Interest earned from the sale of shares to be given out to a client"]);
        $data['def_share_interest_payable_acc_id'] = $this->set(['sub_category_id' => 8, 'organisation_id' => $organisation_id, 'account_code' => "2-1-4", 'account_name' => "Payable share interest", 'description' => "Interest earned on the sale of shares but not yet paid out to the respective client selling it"]);
        $data['def_subscription_income_acc_id'] = $this->set(['sub_category_id' => 12, 'organisation_id' => $organisation_id, 'account_code' => "4-1-6", 'account_name' => "Subscriptions income", 'description' => "Income earned from the annual subscription of clients"]);
        $data['def_subscription_fees_recvable_acc_id'] = $this->set(['sub_category_id' => 1, 'organisation_id' => $organisation_id, 'account_code' => "1-1-7", 'account_name' => "Receivable subscriptions income", 'description' => "Income to be earned from clients when they subscribe"]);
        $data['def_membership_income_acc_id'] = $this->set(['sub_category_id' => 12, 'organisation_id' => $organisation_id, 'account_code' => "4-1-7", 'account_name' => "Membership fees", 'description' => "Income earned from client membership fees"]);
        $data['def_tax_expense_acc_id'] = $this->set(['sub_category_id' => 14, 'organisation_id' => $organisation_id, 'account_code' => "5-1-3", 'account_name' => "Taxes and Statutory obligations", 'description' => "Taxes and Statutory obligations"]);
        $data['def_tax_payable_acc_id'] = $this->set(['sub_category_id' => 8, 'organisation_id' => $organisation_id, 'account_code' => "2-1-5", 'account_name' => "Payable taxes", 'description' => "Taxes and Statutory obligations yet to be paid out"]);
        $data['def_general_expense_acc_id'] = $this->set(['sub_category_id' => 15, 'organisation_id' => $organisation_id, 'account_code' => "5-2-1", 'account_name' => "General Expenses", 'description' => "General expenses"]);
        $data['def_general_expense_payable_acc_id'] = $this->set(['sub_category_id' => 8, 'organisation_id' => $organisation_id, 'account_code' => "2-1-6", 'account_name' => "General Payable expenses", 'description' => "Unpaid invoices/expenses"]);
        $data['def_general_income_acc_id'] = $this->set(['sub_category_id' => 12, 'organisation_id' => $organisation_id, 'account_code' => "4-1-8", 'account_name' => "General Income", 'description' => "General Income earned"]);
        $data['def_general_income_recvable_acc_id'] = $this->set(['sub_category_id' => 1, 'organisation_id' => $organisation_id, 'account_code' => "1-1-8", 'account_name' => "Receivable general Income", 'description' => "General income anticipated to be earned"]);
        $data['def_cash_acc_id'] = $this->set(['sub_category_id' => 3, 'organisation_id' => $organisation_id, 'account_code' => "1-3-1", 'account_name' => "Cash at hand", 'description' => "Cash at hand"]);
        $data['def_bank_acc_id'] = $this->set(['sub_category_id' => 4, 'organisation_id' => $organisation_id, 'account_code' => "1-4-1", 'account_name' => "Bank", 'description' => "Cash in the Bank"]);
        $data['def_mm_acc_id'] = $this->set(['sub_category_id' => 5, 'organisation_id' => $organisation_id, 'account_code' => "1-5-1", 'account_name' => "Mobile Money Cash", 'description' => "Cash in mobile money accounts"]);
        $data['def_equity_acc_id'] = $this->set(['sub_category_id' => 11, 'organisation_id' => $organisation_id, 'account_code' => "3-1-1", 'account_name' => "Owners' Equity", 'description' => "Owners' and Stakeholders' Equity"]);

        $data['modified_by'] = $_SESSION['staff_id'];
        $this->db->where('id', $organisation_id);

        return $this->db->update('organisation', $data);
    }
 
    public function get_sub_accounts($filter = FALSE) {
        $this->db->select("fms_accounts_chart.*");
        $this->db->where('sub_category_id', $filter);
        $this->db->order_by('account_code', 'asc');
        $query = $this->db->get('fms_accounts_chart');
        return $query->result_array();
    }

     public function get_journal_types() {
        $this->db->select("id,type_name");
        $this->db->from('fms_journal_type');
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_account_details($account_id) {
        $this->db->select('account_code, account_name');
        $this->db->from('fms_accounts_chart');
        $this->db->where("fms_accounts_chart.id", $account_id);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function check_account_no_exists($account_no) {
        $this->db->select('account_code, account_name');
        $this->db->from('fms_accounts_chart');
        $this->db->where("fms_accounts_chart.account_code", $account_no);
        $this->db->where("fms_accounts_chart.organisation_id", $_SESSION['organisation_id']);
        if (is_numeric($this->input->post('id'))) {
            $this->db->where("fms_accounts_chart.id <>", $this->input->post('id'));
        }
        $this->db->order_by("account_code", "desc");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_journal_ids() {
        $where_in = array('18,26');
        $this->db->select('id');
        $this->db->from('fms_journal_transaction');
        $this->db->where("transaction_date >=", $this->input->post("fisc_date_to"));
        $this->db->where("transaction_date <=", $this->input->post("new_year_start_date"));
        $this->db->where("journal_type_id in(18,26)");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function rollback_jtrl($ids) {
        $this->db->where_in("journal_transaction_id",$ids);
        return $this->db->delete("fms_journal_transaction_line");
    }
    public function rollback_jtr($ids) {
        $this->db->where_in("id",$ids);
        return $this->db->delete("fms_journal_transaction");
    }
    public function delete($id = false) {
        $delete_id = isset($id) ? $id : $this->input->post("id");
        $this->db->where("id=", $delete_id);
        return $this->db->delete("fms_accounts_chart");
    }

}
