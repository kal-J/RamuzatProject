<?php

/**
 * Description of Reports_model
 *
 * @author reagan
 */
class Reports_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get_income_statement_totals($filter = FALSE) {
        $this->db->select('SUM(IFNULL(credit_amounts,0)-IFNULL(debit_amounts,0)) AS amount');
        $this->db->from('fms_accounts_chart ac');
        $virtual_table1 = "(SELECT account_id, sum(debit_amount) debit_amounts, sum(credit_amount) credit_amounts  FROM `fms_journal_transaction_line` WHERE status_id=1 AND journal_transaction_id IN (SELECT id FROM `fms_journal_transaction` WHERE status_id=1) GROUP BY account_id) transactions";
        //$virtual_table2 = "(SELECT account_id, sum(credit_amount) credit_amounts FROM `fms_journal_transaction_line` GROUP BY account_id) credits";
        $this->db->join($virtual_table1, "transactions.account_id=ac.id", "LEFT");
        //$this->db->join($virtual_table2, "credits.account_id=ac.id","LEFT");
        $this->db->join("account_sub_categories acc_sub", "ac.sub_category_id = acc_sub.id", 'left');
        $this->db->join("account_categories acc_cat", "acc_sub.category_id = acc_cat.id", 'left');
        $this->db->where("acc_sub.category_id", $filter);
        $query = $this->db->get();
        return $query->row_array();
    }

    /*
      public function get_liabilities_equity($filter = FALSE) {
      $query = $this->db->query('SELECT ac.id as account_id, ac.account_name,ac.sub_category_id,ac.account_code,credit_amount,debit_amount,'
      . 'IFNULL(credit_amount,0)-IFNULL(debit_amount,0) AS amount FROM fms_accounts_chart ac LEFT JOIN (SELECT debit_amount,account_id, '
      . 'sum(debit_amount) as debit_amounts FROM `fms_journal_transaction_line` GROUP BY account_id) AS debits ON debits.account_id=ac.id '
      . 'LEFT JOIN (SELECT credit_amount,account_id, sum(credit_amount) as credit_amounts FROM `fms_journal_transaction_line` '
      . 'GROUP BY account_id) AS credits ON credits.account_id=ac.id LEFT JOIN fms_account_sub_categories AS acc_sub ON ac.sub_category_id = acc_sub.id '
      . 'WHERE acc_sub.category_id IN(2,3)');
      return $query->result_array();
      } */

    public function get_accounts_sums($filter = FALSE,$sub_category=FALSE,$myfilter=FALSE) {
        if ($myfilter!=FALSE) {
                $myfilter = $myfilter;
            }else{
                $myfilter = "1";
            }

        $date_range_filter = "1";
        if ($this->input->post("fisc_date_from") !== NULL) {
            $date_range_filter = "(jtl.transaction_date BETWEEN '".$this->input->post("fisc_date_from")."' AND '". $this->input->post("fisc_date_to")."') AND $myfilter";
        }
        $account_summations = "(SELECT `account_id`,SUM(IFNULL(debit_amount,0)) `debit_sum`, SUM(IFNULL(credit_amount,0)) `credit_sum` "
                . "FROM `fms_journal_transaction_line` `jtl` JOIN `fms_journal_transaction` `jt` ON `jt`.`id`=`journal_transaction_id` WHERE `jtl`.`status_id`=1 AND `jt`.`status_id`=1 AND $date_range_filter GROUP BY `account_id`) acc_sums";

        $this->db->select('ac.id,ac.account_code,ac.account_name, ac.description, ac.opening_balance, ac.opening_balance_date, '
                . 'pac.account_name p_account_name, normal_balance_side,debit_sum, credit_sum, (debit_sum-credit_sum) as debit_balance,(credit_sum-debit_sum) as credit_balance');
        $this->db->from('accounts_chart ac');
        $this->db->join("accounts_chart pac", "pac.id=ac.parent_account_id", "LEFT");
        $this->db->join("$account_summations", "ac.id=acc_sums.account_id", "LEFT");
        $this->db->join("account_sub_categories sc", "sc.id=ac.sub_category_id", 'left');
        $this->db->join("account_categories acat", "acat.id=sc.category_id", "LEFT");
        $this->db->order_by("ac.account_code", "asc");
        if ($this->input->post("organisation_id") !== NULL) {
            $this->db->where("organisation_id = ", $this->input->post("organisation_id"));
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

    public function get_category_sums($category = FALSE, $sub_category = FALSE,$where=FALSE) {

        $this->db->select('ABS(SUM(IFNULL(credit_amount,0)-IFNULL(debit_amount,0))) amount, SUM(IFNULL(credit_amount,0)) as credit_sum,SUM(IFNULL(debit_amount,0)) as debit_sum,acc_sub.*,acc_cat.normal_balance_side');
        $this->db->from("fms_journal_transaction_line jtl");
        $this->db->join("fms_journal_transaction jt", "jt.id=jtl.journal_transaction_id", 'left');
        $this->db->join('accounts_chart ac', "ac.id=jtl.account_id", 'left');
        $this->db->join("fms_account_sub_categories acc_sub", "ac.sub_category_id=acc_sub.id", 'left');
        $this->db->join("fms_account_categories acc_cat", "acc_sub.category_id=acc_cat.id", "LEFT");
        $this->db->where("jtl.status_id =", 1);
        $this->db->where("jt.status_id =", 1);
        $this->db->where("ac.organisation_id = ", $_SESSION["organisation_id"]);

        if ($this->input->post('print')!=1) {
            $this->db->where("jt.journal_type_id !=26");
        }
        if ($this->input->post("fisc_date_from") !== NULL) {
            $this->db->where("jtl.transaction_date >= ", $this->input->post("fisc_date_from"));
            $this->db->where("jtl.transaction_date <= ", $this->input->post("fisc_date_to"));
        }else{
          if ($where!=FALSE) {
                $this->db->where($where);
            }  
        }
        if ($category === FALSE) {
            $this->db->where($sub_category);
            $this->db->group_by("acc_sub.id");
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($category)) {
                $this->db->where("acc_cat.id = ", $category);
                $query = $this->db->get();
                return $query->row_array();
            } else if (is_numeric($sub_category)) {
                $this->db->where("acc_sub.id = ", $sub_category);
                $query = $this->db->get();
                //print_r($this->db->last_query());die();
                return $query->row_array();
            } else {
                $this->db->where_in("acc_cat.id", $category);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_account_sums_highcharts($filter = FALSE, $check = FALSE) {
        $this->db->select("ABS(SUM(IFNULL(credit_amount,0)-IFNULL(debit_amount,0))) AS amount");
        $this->db->from('accounts_chart ac');
        $this->db->join("journal_transaction_line jtl", "ac.id=jtl.account_id", "LEFT");
        $this->db->join("journal_transaction j_tr", "jtl.journal_transaction_id=j_tr.id", "LEFT");
        $this->db->join("account_sub_categories acc_sub", "ac.sub_category_id=acc_sub.id", 'left');
        $this->db->where("jtl.status_id =", 1);
        $this->db->where("j_tr.status_id =", 1);
        if ($check == TRUE) {
            $this->db->select("category_id");
            $this->db->group_by("acc_sub.category_id");
        } else {
            $this->db->select("acc_sub.id");
        }
        if ($this->input->post("organisation_id") !== NULL) {
            $this->db->where("ac.organisation_id = ", $this->input->post("organisation_id"));
        }

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("acc_sub.category_id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_aging_invoices($filter = FALSE) {
        $tt_amount_subquery = "(SELECT `invoice_id`, SUM(`amount`) `t_amount` FROM `fms_invoice_line`  GROUP BY `invoice_id`) `lines`";
        $payment_lines_subquery = "(SELECT `invoice_id`, SUM(`amount`) `a_paid` FROM `fms_invoice_payment_line`  GROUP BY `invoice_id`) bpls";
        $this->db->select("SUM(discount) as total_discount,SUM(t_amount) AS total_amount, SUM(a_paid) AS amount_paid");
        $this->db->from('invoice');
        $this->db->join("$tt_amount_subquery", "`lines`.`invoice_id`=`fms_invoice`.`id`", "LEFT");
        $this->db->join("$payment_lines_subquery", "bpls.invoice_id=invoice.id", "LEFT");
        if (is_numeric($this->input->post("status_id"))) {
            $this->db->where("invoice.status_id=" . $this->input->post("status_id"));
        }

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("invoice.id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
    public function get_aging_bills($filter = FALSE) {
        $tt_amount_subquery = "(SELECT `bill_id`, SUM(`amount`) `t_amount` FROM `fms_bill_line`  GROUP BY `bill_id`) `lines`";
        $payment_lines_subquery = "(SELECT `bill_id`, SUM(`amount`) `a_paid` FROM `fms_bill_payment_line`  GROUP BY `bill_id`) bpls";
        $this->db->select("SUM(discount) as total_discount,SUM(t_amount) AS total_amount, SUM(a_paid) AS amount_paid");
        $this->db->from('bill');
        $this->db->join("$tt_amount_subquery", "`lines`.`bill_id`=`fms_bill`.`id`","LEFT");
        $this->db->join("$payment_lines_subquery", "bpls.bill_id=bill.id", "LEFT");
        if (is_numeric($this->input->post("status_id"))) {
            $this->db->where("bill.status_id=" . $this->input->post("status_id"));
        }

        if ($filter === FALSE) {
            $query = $this->db->get();
           //echo $this->db->last_query(); die;
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("bill.id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
    public function get_aging_user_invoices($filter = FALSE) {
        $tt_amount_subquery = "(SELECT `invoice_id`, SUM(`amount`) `t_amount` FROM `fms_invoice_line`  GROUP BY `invoice_id`) `lines`";
        $payment_lines_subquery = "(SELECT `invoice_id`, SUM(`amount`) `a_paid` FROM `fms_invoice_payment_line`  GROUP BY `invoice_id`) bpls";
        $this->db->select("SUM(discount) as total_discount,client_id,SUM(t_amount) AS total_amount, SUM(a_paid) AS amount_paid"); 
        $this->db->select("concat(fms_user.firstname, ' ', fms_user.lastname, ' ', fms_user.othernames) AS client_names");
        $this->db->from('invoice');
        $this->db->join('member', 'member.id = invoice.client_id', 'LEFT');
        $this->db->join('user', 'user.id=member.user_id', "LEFT");
        $this->db->join("$tt_amount_subquery", "`lines`.`invoice_id`=`fms_invoice`.`id`","LEFT");
        $this->db->join("$payment_lines_subquery", "bpls.invoice_id=invoice.id", "LEFT");
        if (is_numeric($this->input->post("status_id"))) {
            $this->db->where("invoice.status_id=" . $this->input->post("status_id"));
        }

        $this->db->having('(IFNULL(total_amount,0)-(IFNULL(total_discount,0)+IFNULL(amount_paid,0)))>',0);
        $this->db->group_by("client_id");
        if ($filter === FALSE) {
            $query = $this->db->get();
           //echo $this->db->last_query(); die;
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("invoice.id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_aging_user_bills($filter = FALSE) {
        $tt_amount_subquery = "(SELECT `bill_id`, SUM(`amount`) `t_amount` FROM `fms_bill_line`  GROUP BY `bill_id`) `lines`";
        $payment_lines_subquery = "(SELECT `bill_id`, SUM(`amount`) `a_paid` FROM `fms_bill_payment_line`  GROUP BY `bill_id`) bpls";
        $this->db->select("SUM(discount) as total_discount,supplier_id,SUM(t_amount) AS total_amount, SUM(a_paid) AS amount_paid,supplier_names"); 
        $this->db->from('bill');
        $this->db->join('supplier', 'supplier.id = bill.supplier_id', 'LEFT');
        $this->db->join("$tt_amount_subquery", "`lines`.`bill_id`=`fms_bill`.`id`","LEFT");
        $this->db->join("$payment_lines_subquery", "bpls.bill_id=bill.id", "LEFT");
        if (is_numeric($this->input->post("status_id"))) {
            $this->db->where("bill.status_id=" . $this->input->post("status_id"));
        }
        $this->db->having('(IFNULL(total_amount,0)-(IFNULL(total_discount,0)+IFNULL(amount_paid,0)))>',0);
        $this->db->group_by("supplier_id");
        if ($filter === FALSE) {
            $query = $this->db->get();
         //  echo $this->db->last_query(); die;
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("bill.id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
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

     public function get_acc_balance($account_id, $normal_bal_side) {
        $active=$this->get_current_fiscal_year(1);
        if ($normal_bal_side==1) {
        $this->db->select('SUM(IFNULL(debit_amount,0)-IFNULL(credit_amount,0)) AS amount,account_name');
        }else{
        $this->db->select('SUM(IFNULL(credit_amount,0)-IFNULL(debit_amount,0)) AS amount,account_name');
        }
        $this->db->from("fms_journal_transaction_line jtl");
        $this->db->join('accounts_chart ac', "ac.id=jtl.account_id", 'left');
        $this->db->join('fms_journal_transaction jt', "jt.id=jtl.journal_transaction_id", 'left');
        $this->db->where("jt.status_id", 1);
        $this->db->where("jtl.status_id", 1);

        if ($this->input->post("organisation_id") !== NULL) {
            $this->db->where("ac.organisation_id = ", $this->input->post("organisation_id"));
        }
        // if ($this->input->post("fisc_date_from") !== NULL) {
        //     $this->db->where("jtl.transaction_date >= ", $this->input->post("fisc_date_from"));
        //     $this->db->where("jtl.transaction_date <= ", $this->input->post("fisc_date_to"));
        // }
        $this->db->where("jtl.transaction_date >= ", $active['start_date']);
        $this->db->where("jtl.transaction_date <= ", $active['end_date']);
       
            if (is_numeric($account_id)) {
                $this->db->where("jtl.account_id = ", $account_id);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where_in("jtl.account_id = ", $account_id);
                $query = $this->db->get();
                return $query->row_array();
            }
        
    }
    public function get_credit_debit_sums($filter = FALSE) {

        $this->db->select('SUM(`debit_amount`) as debit_sum,SUM(`credit_amount`) as credit_sum');
        $this->db->from("fms_journal_transaction_line jtl");
        $this->db->join("fms_journal_transaction jt", "jt.id=jtl.journal_transaction_id", 'left');
        $this->db->join('accounts_chart ac', "ac.id=jtl.account_id", 'left');
        $this->db->join("fms_account_sub_categories acc_sub", "ac.sub_category_id=acc_sub.id", 'left');
        $this->db->join("fms_account_categories acc_cat", "acc_sub.category_id=acc_cat.id", "LEFT");
        $this->db->where("jtl.status_id =", 1);
        $this->db->where("jt.status_id =", 1);
        if ($this->input->post("organisation_id") !== NULL) {
            $this->db->where("ac.organisation_id = ", $this->input->post("organisation_id"));
        }
        if ($this->input->post("fisc_date_from") !== NULL) {
            $this->db->where("jtl.transaction_date >= ", $this->input->post("fisc_date_from"));
            $this->db->where("jtl.transaction_date <= ", $this->input->post("fisc_date_to"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->row_array();
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
     public function get_accounts_sums_one($account_id,$start_date=FALSE,$end_date=false) {

        $this->db->select('SUM(`debit_amount`) as debit_sum,SUM(`credit_amount`) as credit_sum');
        $this->db->from("fms_journal_transaction_line jtl");
        $this->db->join("fms_journal_transaction jt", "jt.id=jtl.journal_transaction_id", 'left');
        $this->db->where("jtl.status_id =", 1);
        $this->db->where("jt.status_id =", 1);
        if ($this->input->post("organisation_id") !== NULL) {
            $this->db->where("ac.organisation_id = ", $this->input->post("organisation_id"));
        }
        /* if (is_numeric($this->input->post("created_by")) && $this->input->post("all")==1) {
            $this->db->where("jtl.created_by = ", $this->input->post("created_by"));
        } */
        if (($start_date !== false)&&($end_date !== false)) {
            $this->db->where("jtl.transaction_date >= ", $start_date);
            $this->db->where("jtl.transaction_date <= ", $end_date);
        }
        
        $this->db->where("jtl.account_id", $account_id);
        $query = $this->db->get();
        return $query->row_array();
           
    }

    public function sum_loan_disbursed_credit_debit($loans_disbursed_account_id = 1, $start_date = '', $end_date = '')
    {
        $start_date = $start_date ? $start_date : $this->input->post('start_date');
        $end_date = $end_date ? $end_date : $this->input->post('end_date');

        if($start_date) {
            $this->db->where("jtl.transaction_date >=", $start_date);
        }

        if($end_date) {
            $this->db->where("jtl.transaction_date <=", $end_date);
        }
        # code...

        $this->db->select("SUM(credit_amount) total_paid_back , SUM(debit_amount) total_disbursed");
        $this->db->from("journal_transaction_line jtl");
        $this->db->join("fms_journal_transaction jt", "jt.id=jtl.journal_transaction_id", 'left');
        $this->db->where("jtl.status_id =", 1);
        $this->db->where("jt.status_id =", 1);
        $this->db->where("account_id", $loans_disbursed_account_id);

        $query = $this->db->get();
        return $query->row_array();
    }
}

