<?php


class Loan_portfolio_report_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();

        $this->from_date = $this->input->post('from_date');
        $this->to_date = $this->input->post('to_date');

        $this->paid_amount = "(SELECT client_loan_id,SUM(paid_principal) total_paid_principal,SUM(paid_interest) total_paid_interest FROM fms_loan_installment_payment WHERE fms_loan_installment_payment.status_id=1 GROUP BY client_loan_id)";

        $this->active_state = "(SELECT client_loan_id,comment,action_date AS loan_active_date FROM fms_loan_state
                WHERE id in ( SELECT MIN(id) from fms_loan_state WHERE state_id=7 GROUP BY client_loan_id ) )";

        $un_cleared_installments = "(SELECT SUM(lp.paid_principal) total_paid_principal, SUM(lp.paid_interest) total_paid_interest, rs.id AS schedule_id FROM fms_loan_installment_payment lp LEFT JOIN fms_repayment_schedule rs ON rs.id=lp.repayment_schedule_id WHERE lp.status_id=1 AND rs.payment_status<>1 AND rs.payment_status<>3 AND rs.repayment_date <= CURDATE() GROUP BY rs.id)";

        $disbursed = "(SELECT rps.client_loan_id, SUM(ifnull(principal_amount,0)) total_disbursed, SUM(ifnull(interest_amount,0)) total_interest_expected
                FROM fms_repayment_schedule rps
                LEFT JOIN $this->active_state state ON state.client_loan_id=rps.client_loan_id
                WHERE rps.status_id=1 AND state.loan_active_date <= CURDATE()
                GROUP BY rps.client_loan_id
            )";

        $this->amount_in_demand = "(SELECT client_loan_id, (SUM(interest_amount+principal_amount) - SUM(ifnull(uci.total_paid_principal,0) + ifnull(uci.total_paid_interest, 0)) ) amount_in_demand, (SUM(principal_amount) - ifnull(uci.total_paid_principal, 0)) principal_in_demand, (SUM(interest_amount) - ifnull(uci.total_paid_interest, 0) ) interest_in_demand  FROM `fms_repayment_schedule` 
        LEFT JOIN $un_cleared_installments uci ON uci.schedule_id=fms_repayment_schedule.id
        WHERE payment_status IN(2,4) AND status_id=1 AND repayment_date <= CURDATE() GROUP BY client_loan_id)";

        $this->credit_officer_amounts = "(SELECT cl.credit_officer_id, SUM(ifnull(disbursed.total_disbursed,0)) total_disbursed, SUM(ifnull(disbursed.total_interest_expected,0)) total_interest_expected, SUM(ifnull(paid_amount.total_paid_principal,0)) total_paid_principal , SUM(ifnull(paid_amount.total_paid_interest,0)) total_paid_interest, SUM(ifnull(amount_in_demand.principal_in_demand,0)) principal_in_demand , SUM(ifnull(amount_in_demand.interest_in_demand,0)) interest_in_demand
            FROM fms_client_loan cl 
            LEFT JOIN $disbursed disbursed ON cl.id=disbursed.client_loan_id 
            LEFT JOIN $this->paid_amount paid_amount ON cl.id=paid_amount.client_loan_id 
            LEFT JOIN $this->amount_in_demand amount_in_demand ON cl.id=amount_in_demand.client_loan_id  
            GROUP BY cl.credit_officer_id) co";

        $this->credit_officer_loan_count = "(SELECT credit_officer_id, COUNT(DISTINCT id) loan_count FROM fms_client_loan GROUP BY credit_officer_id) loan_counts";

        $this->max_state_id = "(SELECT client_loan_id,state_id,comment,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state GROUP BY client_loan_id ) )";

        if ($this->from_date && $this->to_date) {
            $expected = "(SELECT rps.client_loan_id, SUM(principal_amount) expected_principal, SUM(interest_amount) total_interest_expected
            FROM fms_repayment_schedule rps
            WHERE rps.status_id=1 AND rps.repayment_date >= '{$this->from_date}' AND rps.repayment_date <= '{$this->to_date}'
            GROUP BY rps.client_loan_id)";
            $disbursed = "(   SELECT rps.client_loan_id, SUM(principal_amount) total_disbursed
                FROM fms_repayment_schedule rps
                LEFT JOIN $this->active_state state ON state.client_loan_id=rps.client_loan_id
                WHERE rps.status_id=1 AND state.loan_active_date >= '{$this->from_date}' AND state.loan_active_date <= '{$this->to_date}'
                GROUP BY rps.client_loan_id
            )";
            $paid_amount = "(SELECT client_loan_id,SUM(paid_principal) total_paid_principal,SUM(paid_interest) total_paid_interest FROM fms_loan_installment_payment WHERE fms_loan_installment_payment.status_id=1 AND fms_loan_installment_payment.payment_date >= '{$this->from_date}' AND fms_loan_installment_payment.payment_date <= '{$this->to_date}' GROUP BY client_loan_id)";

            $un_cleared_installments = "(SELECT SUM(lp.paid_principal) total_paid_principal, SUM(lp.paid_interest) total_paid_interest, rs.id AS schedule_id FROM fms_loan_installment_payment lp LEFT JOIN fms_repayment_schedule rs ON rs.id=lp.repayment_schedule_id WHERE lp.status_id=1 AND rs.payment_status<>1 AND rs.payment_status<>3 AND rs.repayment_date >= '{$this->from_date}' AND rs.repayment_date <= '{$this->to_date}' GROUP BY rs.id)";

            $this->amount_in_demand = "(SELECT client_loan_id, (SUM(interest_amount+principal_amount) - SUM(ifnull(uci.total_paid_principal,0) + ifnull(uci.total_paid_interest, 0)) ) amount_in_demand, (SUM(principal_amount) - ifnull(uci.total_paid_principal, 0)) principal_in_demand, (SUM(interest_amount) - ifnull(uci.total_paid_interest, 0) ) interest_in_demand  FROM `fms_repayment_schedule` 
            LEFT JOIN $un_cleared_installments uci ON uci.schedule_id=fms_repayment_schedule.id
            WHERE payment_status IN(2,4) AND status_id=1 AND repayment_date >= '{$this->from_date}' AND repayment_date <= '{$this->to_date}' GROUP BY client_loan_id)";

            $this->credit_officer_amounts = "(SELECT cl.credit_officer_id, SUM(ifnull(disbursed.total_disbursed,0)) total_disbursed, SUM(ifnull(expected.total_interest_expected,0)) total_interest_expected,
            SUM(ifnull(expected.expected_principal,0)) expected_principal, SUM(ifnull(paid_amount.total_paid_principal,0)) total_paid_principal, SUM(ifnull(paid_amount.total_paid_interest,0)) total_paid_interest, SUM(ifnull(amount_in_demand.principal_in_demand,0)) principal_in_demand , SUM(ifnull(amount_in_demand.interest_in_demand,0)) interest_in_demand
            FROM fms_client_loan cl 
            LEFT JOIN $disbursed disbursed ON cl.id=disbursed.client_loan_id 
            LEFT JOIN $expected expected ON cl.id=expected.client_loan_id 
            LEFT JOIN $paid_amount paid_amount ON cl.id=paid_amount.client_loan_id 
            LEFT JOIN $this->amount_in_demand amount_in_demand ON cl.id=amount_in_demand.client_loan_id
            GROUP BY cl.credit_officer_id) co";

            $this->credit_officer_loan_count = "(SELECT credit_officer_id, COUNT(DISTINCT id) loan_count FROM fms_client_loan cl
                LEFT JOIN $this->active_state state ON cl.id=state.client_loan_id
                WHERE state.loan_active_date >= '{$this->from_date}' AND state.loan_active_date <= '{$this->to_date}'
                GROUP BY credit_officer_id) loan_counts";
        }
    }

    public function get_current_report()
    {
        $query = $this->db->query("SELECT * FROM fms_loan_portfolio_report ORDER BY id DESC LIMIT 1");
        $result = $query->row_array();
        return $result;
    }

    public function compute_loan_portfolio_report()
    {
        $data = [
            'total_principal_disbursed' => $this->compute_total_principal_disbursed(),
            'total_active_loan_principal' => 0,
            'total_principal_defaulted' => 0,
            'total_recovered_principal' => 0,
            'total_written_off_principal' => 0,
            'total_interest_collected' => 0,
        ];

        $this->db->insert('fms_loan_portfolio_report', $data);
    }

    public function compute_total_principal_disbursed()
    {
        $query = $this->db->query("SELECT SUM(principal_amount) AS total_principle FROM fms_repayment_schedule WHERE status_id=1");
        $result = $query->row_array();
        return $result['total_principle'];
    }

    public function compute_credit_officers_report()
    {
        $this->db->select("staff.id staff_id, concat(u.firstname, ' ', u.lastname) staff_name, co.*, loan_counts.loan_count");
        $this->db->where('staff.status_id', 1);
        $this->db->from('fms_staff staff');

        $this->db->join("fms_user u", "u.id=staff.user_id", "left");
        $this->db->join("fms_client_loan cl", "cl.credit_officer_id=staff.id", "left");
        $this->db->join($this->credit_officer_amounts, "co.credit_officer_id=staff.id", "left");
        $this->db->join($this->credit_officer_loan_count, "loan_counts.credit_officer_id=staff.id", "left");
        $this->db->group_by('staff.id');
        $query = $this->db->get();
        // print_r($this->db->last_query()); die;
        $data = $query->result_array();
        $report = [
            'data' => json_encode($data),
            'from_date' => $this->from_date ? $this->from_date : null,
            'to_date' => $this->to_date ? $this->to_date : null,
            'created_by' => 1
        ];
        $inserted_id = $this->db->insert('fms_credit_officers_report', $report);
        return $inserted_id;
    }

    public function get_credit_officers_report()
    {
        $query = $this->db->query("SELECT * FROM fms_credit_officers_report WHERE from_date IS NULL AND to_date IS NULL ORDER BY id DESC LIMIT 1");

        if ($this->from_date && $this->to_date) {
            $query = $this->db->query("SELECT * FROM fms_credit_officers_report WHERE from_date='{$this->from_date}' AND to_date='{$this->to_date}' ORDER BY id DESC LIMIT 1");
        }

        $result = $query->row_array();
        $result['data'] = isset($result['data']) ? json_decode($result['data'], true) : [];
        return $result;
    }
}
