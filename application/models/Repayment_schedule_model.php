<?php

/**
 * Description of repayment_schedule_model
 *
 * @author Eric
 */
class Repayment_schedule_model extends CI_Model
{

    public function __construct()
    {

        $this->max_state_id = "(SELECT client_loan_id,state_id,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state GROUP BY client_loan_id ) )";
        $this->loan_installment_payments = "(SELECT repayment_schedule_id, ifnull(SUM(paid_principal),0) AS paid_principal_amount ,SUM(paid_penalty) AS total_paid_penalty_amount,ifnull(SUM(paid_interest),0) AS paid_interest_amount
                    FROM fms_loan_installment_payment 
                    JOIN fms_repayment_schedule ON fms_repayment_schedule.id=fms_loan_installment_payment.repayment_schedule_id 
                    JOIN($this->max_state_id) loan_state ON loan_state.client_loan_id=fms_loan_installment_payment.client_loan_id
                    WHERE (loan_state.state_id IN(7,12,13)) AND fms_loan_installment_payment.status_id!=0
                    GROUP BY fms_loan_installment_payment.repayment_schedule_id )";
        $this->paid_amount = "(SELECT fms_loan_installment_payment.client_loan_id,SUM(paid_interest+paid_principal) AS paid_amount,SUM(paid_principal) AS paid_principal,SUM(paid_interest) AS paid_interest, SUM(interest_amount) AS parent_expected_interest FROM fms_loan_installment_payment
        LEFT JOIN fms_repayment_schedule ON fms_repayment_schedule.client_loan_id=fms_loan_installment_payment.client_loan_id
         WHERE fms_loan_installment_payment.status_id=1 AND fms_repayment_schedule.status_id=1  GROUP BY client_loan_id)";

        //single contact 
        $this->single_contact = "
                (SELECT `user_id`, `mobile_number` FROM `fms_contact`
                WHERE `id` in (
                    SELECT MAX(`id`) from `fms_contact` WHERE `contact_type_id`=1 GROUP BY `user_id` 
                )
            )";
        $this->load->database();
        date_default_timezone_set('Africa/Kampala');
    }

    //inserting repayment schedule details of a loan
    public function set($client_loan_id = false, $unique_id = false)
    {
        $query = false;
        if ($client_loan_id === false) {
            $client_loan_id = $this->input->post('client_loan_id');
        }
        if ($this->input->post('grace_period') !== NULL) {
            $grace_period_after = $this->input->post('grace_period');
        } else {
            $grace_period_after = $this->input->post('grace_period_after');
        }

        $repayment_schedules = $this->input->post('repayment_schedule');
        $data = [];
        foreach ($repayment_schedules as $key => $repayment_schedule) {
            $data[] = array(
                'repayment_date' => date('Y-m-d', $repayment_schedule['repayment_date']),
                'interest_amount' => $repayment_schedule['interest_amount'],
                'principal_amount' => $repayment_schedule['principal_amount'],
                'client_loan_id' => $client_loan_id,
                'payment_status' => '4', //meaning pending
                'grace_period_on' => '3', //meaning days
                'status_id' => '1', //meaning active schedule
                'installment_number' => $repayment_schedule['installment_number'],
                'interest_rate' => $this->input->post('interest_rate'),
                'grace_period_after' => $grace_period_after,
                'repayment_made_every' => $this->input->post('repayment_made_every'),
                'repayment_frequency' => $this->input->post('repayment_frequency'),
                'comment' => $this->input->post('comment'),
                'date_created' => time(),
                'created_by' => $_SESSION['id'],
                'unique_id' => $unique_id
            );
        } //End of foreach loop
        if (!empty($data)) {
            $query = $this->db->insert_batch('repayment_schedule', $data);
        }

        return $query;
    }
    public function set2($sent_data)
    {
        return $this->db->insert_batch('repayment_schedule', $sent_data);
    }
    public function set3($sent_data)
    {
        $this->db->insert('repayment_schedule', $sent_data);
        return $this->db->insert_id();
    }

    //end of the function

    public function get($filter = FALSE)
    {
        $this->db->select("repayment_schedule.*,(interest_amount+principal_amount) AS total_amount,payment_name,
            CASE 
                WHEN fms_repayment_schedule.repayment_date < NOW() THEN 1
                WHEN fms_repayment_schedule.repayment_date = NOW() THEN 2
                ELSE 0 
            END AS installment_status
            ", FALSE);
        $query = $this->db->from('repayment_schedule')->join('loan_payment_status', 'loan_payment_status.id=repayment_schedule.payment_status', 'left');

        if ($this->input->post('status_id') !== NULL && is_numeric($this->input->post('status_id'))) {
            $this->db->where('repayment_schedule.status_id', $this->input->post('status_id'));
        } else {
            $this->db->where('repayment_schedule.status_id=1');
        }
        if ($this->input->post('client_loan_id') !== NULL && is_numeric($this->input->post('client_loan_id'))) {
            $this->db->where('repayment_schedule.client_loan_id', $this->input->post('client_loan_id'));
        }
        if ($this->input->post('payment_status') !== NULL && is_numeric($this->input->post('payment_status'))) {
            $this->db->where('repayment_schedule.payment_status', $this->input->post('payment_status'));
        }
        if ($this->input->post('payment_status') !== NULL && is_array($this->input->post('payment_status'))) {
            $this->db->where_in('repayment_schedule.payment_status', $this->input->post('payment_status'));
        }
        $this->db->order_by("repayment_date", "ASC");
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('repayment_schedule.client_loan_id=' . $filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_loan_ledger_card($filter = FALSE)
    {
        $amount_paid_per_installment = " (SELECT repayment_schedule_id, SUM(ifnull(paid_interest, 0)+ifnull(paid_principal,0)+ifnull(paid_penalty,0)) amount_paid FROM fms_loan_installment_payment WHERE status_id=1 GROUP BY repayment_schedule_id) ";

        $this->db->select("amount_paid, repayment_schedule.*,(interest_amount+principal_amount) AS total_amount,payment_name,
            CASE 
                WHEN fms_repayment_schedule.repayment_date < NOW() THEN 1
                WHEN fms_repayment_schedule.repayment_date = NOW() THEN 2
                ELSE 0 
            END AS installment_status
            ", FALSE);
        $query = $this->db->from('repayment_schedule')->join('loan_payment_status', 'loan_payment_status.id=repayment_schedule.payment_status', 'left')->join($amount_paid_per_installment . " amount_paid_per_installment", "amount_paid_per_installment.repayment_schedule_id=repayment_schedule.id", "LEFT");
        

        if ($this->input->post('status_id') !== NULL && is_numeric($this->input->post('status_id'))) {
            $this->db->where('repayment_schedule.status_id', $this->input->post('status_id'));
        } else {
            $this->db->where('repayment_schedule.status_id=1');
        }
        if ($this->input->post('client_loan_id') !== NULL && is_numeric($this->input->post('client_loan_id'))) {
            $this->db->where('repayment_schedule.client_loan_id', $this->input->post('client_loan_id'));
        }
        if ($this->input->post('payment_status') !== NULL && is_numeric($this->input->post('payment_status'))) {
            $this->db->where('repayment_schedule.payment_status', $this->input->post('payment_status'));
        }
        if ($this->input->post('payment_status') !== NULL && is_array($this->input->post('payment_status'))) {
            $this->db->where_in('repayment_schedule.payment_status', $this->input->post('payment_status'));
        }
        $this->db->order_by("repayment_date", "ASC");
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('repayment_schedule.client_loan_id=' . $filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get22($filter = FALSE)
    {
        $this->db->select("repayment_schedule.*,(interest_amount+principal_amount) AS total_amount,payment_name,paid_principal_amount,paid_interest_amount,
            CASE 
                WHEN fms_repayment_schedule.repayment_date < NOW() THEN 1
                WHEN fms_repayment_schedule.repayment_date = NOW() THEN 2
                ELSE 0 
            END AS installment_status
            ", FALSE);
        $query = $this->db->from('repayment_schedule')->join('loan_payment_status', 'loan_payment_status.id=repayment_schedule.payment_status');
        $this->db->join("$this->loan_installment_payments loan_installment_payments", "loan_installment_payments.repayment_schedule_id=repayment_schedule.id", "LEFT");

        if ($this->input->post('status_id') !== NULL && is_numeric($this->input->post('status_id'))) {
            $this->db->where('repayment_schedule.status_id', $this->input->post('status_id'));
        } else {
            $this->db->where('repayment_schedule.status_id=1');
        }
        if ($this->input->post('client_loan_id') !== NULL && is_numeric($this->input->post('client_loan_id'))) {
            $this->db->where('repayment_schedule.client_loan_id', $this->input->post('client_loan_id'));
        }
        if ($this->input->post('payment_status') !== NULL && is_numeric($this->input->post('payment_status'))) {
            $this->db->where('repayment_schedule.payment_status', $this->input->post('payment_status'));
        }
        if ($this->input->post('payment_status') !== NULL && is_array($this->input->post('payment_status'))) {
            $this->db->where_in('repayment_schedule.payment_status', $this->input->post('payment_status'));
        }
        $this->db->order_by("repayment_date", "ASC");
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('repayment_schedule.client_loan_id=' . $filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_minimum($filter = false)
    {
        $this->db->select("*, MIN(id)");
        $query = $this->db->from('repayment_schedule');
        $this->db->where('repayment_schedule.status_id=1');
        $this->db->where($filter);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_due_schedules($filter = 1)
    {
        $this->db->select("repayment_date,(interest_amount-ifnull(paid_interest_amount,0)) AS interest_amount,(principal_amount-ifnull(paid_principal_amount,0)) AS principal_amount,demanded_penalty,client_loan_id,grace_period_on,grace_period_after,installment_number,interest_rate,repayment_frequency,repayment_made_every,comment,actual_payment_date,payment_status,repayment_schedule.id");
        $this->db->from('repayment_schedule');
        $this->db->join("$this->loan_installment_payments loan_installment_payments", "loan_installment_payments.repayment_schedule_id=repayment_schedule.id", "LEFT");
        $this->db->where('repayment_schedule.status_id=1');
        $this->db->where($filter);
        $this->db->order_by("repayment_schedule.id", "asc");
        $query = $this->db->get();
        //print_r($this->db->last_query()); die();
        return $query->result_array();
    }
    public function get2($filter = 1)
    {
        $this->db->select("*");
        $query = $this->db->from('repayment_schedule');
        $this->db->where('repayment_schedule.status_id=1');
        $this->db->where($filter);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function get4($filter = 1)
    {
        $this->db->select("*");
        $query = $this->db->from('repayment_schedule');
        $this->db->where('repayment_schedule.status_id=1');
        $this->db->where($filter);
        $query = $this->db->get();
        return $query->row_array();
    }
    //added @mbrose for alert testing 
    public function get3($filter = false)
    {
        $this->db->distinct('client_loan_id');
        $this->db->select("rs.repayment_date,rs.client_loan_id,mobile_number,email,m.id as member_id");
        $this->db->from('repayment_schedule rs');
        $this->db->join('client_loan cl', 'rs.client_loan_id=cl.id', 'left');
        $this->db->join('member m', 'm.id=cl.member_id', 'left');
        $this->db->join('user u', 'u.id=m.user_id', 'left');
        $this->db->join($this->single_contact . " c", "c.user_id = u.id", "left");
        $query = $this->db->from('repayment_schedule');
        $this->db->where('repayment_schedule.status_id=1');
        $this->db->where($filter);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get2_for_import($filter)
    {
        $this->db->select("*");
        $query = $this->db->from('repayment_schedule');
        $this->db->where('repayment_schedule.client_loan_id=' . $filter);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_schedule_data($schedule_id)
    {
        $this->db->select("*");
        $this->db->from('repayment_schedule');
        $this->db->where('repayment_schedule.id=' . $schedule_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_payoff_installment($filter = FALSE)
    {
        $where = "WHERE fms_repayment_schedule.status_id=1";
        if ($filter !== FALSE && is_numeric($filter)) {
            $where .= " AND `fms_repayment_schedule`.`client_loan_id`=" . $filter;
        }
        if ($this->input->post('payment_date') != NULL && $this->input->post('payment_date') != '') {
            $payment_date = $this->helpers->yr_transformer($this->input->post('payment_date'));
        } else {
            $payment_date = date('Y-m-d');
        }

        $sql_query = "SELECT id, MIN(repayment_date) FROM  fms_repayment_schedule $where AND `repayment_date` >='" . $payment_date . "'";

        $query = $this->db->query($sql_query);
        //print_r($this->db->last_query()); die();
        return $query->result_array();
    }

    //Getting interest sum and principal sum
    public function daily_sum_interest_principal()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        # Compute interest expected
        $this->db->trans_start();
        $this->db->select("SUM(rs.interest_amount) as interest_sum, SUM(rs.principal_amount) as principal_expected");
        $this->db->where("rs.status_id=1");
        $this->db->where("rs.payment_status!=5"); // Ignore topped up schedules
        if ($start_date) {
            $this->db->where("rs.repayment_date >= '{$start_date}'");
        }
        if ($end_date) {
            $this->db->where("rs.repayment_date <= '{$end_date}'");
        }
        $this->db->from('fms_repayment_schedule rs');
        $query = $this->db->get();
        $principal_interest_expected = $query->row_array();
        $this->db->trans_complete();
        # End Compute interest expected

        # Compute disbursed principal
        $this->db->trans_start();
        $this->db->select("SUM(ifnull(cl.amount_approved,0)) as principal_sum");
        $this->db->where("ls.state_id >= 7");

        if ($start_date) {
            $this->db->where("ls.action_date >= '{$start_date}'");
        }
        if ($end_date) {
            $this->db->where("ls.action_date <= '{$end_date}'");
        }
        $this->db->from("fms_client_loan cl");
        $this->db->join("$this->max_state_id ls", "cl.id=ls.client_loan_id", "LEFT");
        $query = $this->db->get();
        $principal_disbursed = $query->row_array();
        $this->db->trans_complete();
        # End Compute disbursed principal

        if (!is_numeric($principal_disbursed['principal_sum'])) $principal_disbursed['principal_sum'] = 0;
        if (!is_numeric($principal_interest_expected['interest_sum'])) $principal_interest_expected['interest_sum'] = 0;
        if (!is_numeric($principal_interest_expected['principal_expected'])) $principal_interest_expected['principal_expected'] = 0;

        return (['principal_sum' => $principal_disbursed['principal_sum'], 'interest_sum' =>  $principal_interest_expected['interest_sum'], 'principal_expected' => $principal_interest_expected['principal_expected']]);
    }

    // Compute total paid principle
    public function daily_sum_paid_principal_interest_penalty()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $this->db->select("SUM(ifnull(lp.paid_principal,0)) as paid_principal_sum, SUM(ifnull(lp.paid_interest,0)) as paid_interest_sum, SUM(ifnull(lp.paid_penalty,0)) as paid_penalty_sum");
        $this->db->where("lp.status_id=1");

        if ($start_date) {
            $this->db->where("lp.payment_date >= '{$start_date}'");
        }
        if ($end_date) {
            $this->db->where("lp.payment_date <= '{$end_date}'");
        }

        $this->db->from('fms_loan_installment_payment lp');
        $query = $this->db->get();

        $result = $query->row_array();
        if (!is_numeric($result['paid_principal_sum'])) $result['paid_principal_sum'] = 0;
        if (!is_numeric($result['paid_interest_sum'])) $result['paid_interest_sum'] = 0;
        if (!is_numeric($result['paid_penalty_sum'])) $result['paid_penalty_sum'] = 0;
        return $result;
    }

    //Getting interest sum and principal sum
    public function sum_interest_principal($filter = FALSE, $check = FALSE, $write_off = FALSE, $no_current_interest = FALSE)
    {
        $where = "WHERE fms_repayment_schedule.status_id=1";
        if ($filter !== FALSE && is_numeric($filter)) {
            $where .= " AND `fms_repayment_schedule`.`client_loan_id`=" . $filter;
        }
        if ($filter !== FALSE && !(is_numeric($filter))) {
            $where .= " AND " . $filter;
        }
        if ($check !== FALSE && !(is_numeric($check))) {
            $this->db->where($check);
        }
        if ($this->input->post('payment_date') != NULL && $this->input->post('payment_date') != '') {
            $last_date = date('d-m-Y', strtotime($this->input->post('loan_end_date')));
            $mydate = strtotime($this->input->post('payment_date')) > strtotime($last_date) ? $last_date : $this->input->post('payment_date');
            $payment_date = $this->helpers->yr_transformer($mydate);
        } else {
            $payment_date = date('Y-m-d');
        }


        if ($write_off != FALSE) {
            $sql = "(SELECT *,CASE WHEN repayment_date <= '" . $payment_date . "' THEN interest_amount ELSE 0 END AS remaining_interest FROM fms_repayment_schedule $where)";
        } else if ($no_current_interest != FALSE) {
            $sql1 = "(SELECT MIN(repayment_date) FROM  fms_repayment_schedule $where AND `repayment_date` >='" . $payment_date . "')";
            $sql = "(SELECT *,CASE WHEN repayment_date < $sql1 THEN interest_amount ELSE 0 END AS remaining_interest FROM fms_repayment_schedule $where)";
        } else { //pay off impact
            $sql1 = "(SELECT MIN(repayment_date) FROM  fms_repayment_schedule $where AND `repayment_date` >='" . $payment_date . "')";
            $sql = "(SELECT *,CASE WHEN repayment_date <= $sql1 THEN interest_amount ELSE 0 END AS remaining_interest FROM fms_repayment_schedule $where)";
        }

        $this->db->select("ifnull(SUM(interest_amount+principal_amount),0) AS total_payment,ifnull(SUM(interest_amount),0) AS interest_sum,ifnull(SUM(principal_amount),0) AS principal_sum, ifnull(SUM(remaining_interest),0) AS to_date_interest_sum", FALSE);
        $query = $this->db->from("$sql fms_repayment_schedule")->limit(1, 0);
        if ($this->input->post('staff_id') !== NULL && is_numeric($this->input->post('staff_id'))) {
            $this->db->join("client_loan", "client_loan.id=loan_state.client_loan_id");
            $this->db->where('client_loan.created_by ', $this->input->post('staff_id'));
        }
        $query = $this->db->get();
        // print_r($this->db->last_query()); die();
        return $query->row_array();
    }


    //Getting interest sum and principal sum
    public function sum_interest_principal_report($filter = FALSE, $check = FALSE)
    {
        $where = "WHERE 1 ";

        $this->max_state_id = "(SELECT client_loan_id,state_id,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state $where GROUP BY client_loan_id ) )";

        $this->db->select("ifnull(SUM(interest_amount+principal_amount),0) AS total_payment,ifnull(SUM(interest_amount),0) AS interest_sum,ifnull(SUM(principal_amount),0) AS principal_sum ", FALSE);
        $this->db->from("repayment_schedule")->limit(1, 0);
        $this->db->join("$this->max_state_id loan_state", "loan_state.client_loan_id=repayment_schedule.client_loan_id");

        $this->db->join("client_loan", "client_loan.id=loan_state.client_loan_id");
        if ($this->input->post('staff_id') != NULL && is_numeric($this->input->post('staff_id'))) {
            $this->db->where('client_loan.created_by', $this->input->post('staff_id'));
        }
        if ($this->input->post('credit_officer_id') != NULL && is_numeric($this->input->post('credit_officer_id'))) {
            $this->db->where('client_loan.credit_officer_id', $this->input->post('credit_officer_id'));
        }
        if ($this->input->post('status_id') != NULL && is_numeric($this->input->post('status_id'))) {
            $this->db->where('repayment_schedule.status_id', $this->input->post('status_id'));
        } else {
            $this->db->where('repayment_schedule.status_id', 1);
        }
        if ($filter != false) {
            $this->db->where($filter);
        }
        if ($check !== FALSE && !(is_numeric($check))) {
            $this->db->where($check);
        }
        $query = $this->db->get();
        return $query->row_array();
    }
    public function sum_interest_principal_report_before($filter = FALSE, $check = FALSE, $start_date = FALSE, $end_date = FALSE)
    {
        $where = "WHERE 1 ";
        if ($filter == false) {
            if ($end_date != FALSE && $end_date != NULL) {
                $where .= " AND action_date <='" . $end_date . "'";
            }
            if ($start_date != FALSE && $start_date != NULL) {
                $where .= " AND action_date >='" . $start_date . "'";
            }
        }
        $this->max_state_id = "(SELECT client_loan_id,state_id,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state $where GROUP BY client_loan_id ) )";

        $this->db->select("SUM(interest_amount+principal_amount) AS total_payment,SUM(interest_amount) AS interest_sum,SUM(principal_amount) AS principal_sum ", FALSE);
        $this->db->from("repayment_schedule")->limit(1, 0);
        $this->db->join("$this->max_state_id loan_state", "loan_state.client_loan_id=repayment_schedule.client_loan_id", "left");

        $this->db->join("client_loan", "client_loan.id=loan_state.client_loan_id");
        if ($this->input->post('staff_id') != NULL && is_numeric($this->input->post('staff_id'))) {
            $this->db->where('client_loan.created_by', $this->input->post('staff_id'));
        }
        if ($this->input->post('credit_officer_id') != NULL && is_numeric($this->input->post('credit_officer_id'))) {
            $this->db->where('client_loan.credit_officer_id', $this->input->post('credit_officer_id'));
        }
        if ($this->input->post('status_id') != NULL && is_numeric($this->input->post('status_id'))) {
            $this->db->where('repayment_schedule.status_id', $this->input->post('status_id'));
        } else {
            $this->db->where('repayment_schedule.status_id', 1);
        }
        if ($filter != false) { //used when you startdate and enddate variable are not sent
            $this->db->where($filter);
        }
        if ($check !== FALSE && !(is_numeric($check))) { //used when you startdate and enddate variable are sent     
            $this->db->where($check);
        }
        $query = $this->db->get();
        return $query->row_array();
    }

    ## Multiple Installment 
    public function clear_multiple_installment($data)
    {
        $query = $this->db->update_batch('repayment_schedule', $data, 'id');
        //$query = $this->db->update('repayment_schedule', $data);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }


    ## Single Installment 
    public function clear_single_installment($filter = false, $unique_id = false)
    {
        if (!$filter) return false;

        if (!empty($this->input->post('action_date'))) {
            $sent_date = explode('-', $this->input->post('action_date'), 3);
            $action_date = count($sent_date) === 3 ? ($sent_date[2] . "-" . $sent_date[1] . "-" . $sent_date[0]) : null;
        } elseif (!empty($this->input->post('payment_date'))) {
            $sent_date = explode('-', $this->input->post('payment_date'), 3);
            $action_date = count($sent_date) === 3 ? ($sent_date[2] . "-" . $sent_date[1] . "-" . $sent_date[0]) : null;
        } else {
            $action_date = date('Y-m-d');
        }

        $data = [];

        $paid_total = round($this->input->post('totalAmount'), 2);

        $expected_total = $this->input->post('expected_total');
        $forgiven_penalty = !empty($this->input->post('forgiven_penalty')) ? $this->input->post('forgiven_penalty') : 0;
        $forgiven_interest = !empty($this->input->post('forgiven_interest')) ? $this->input->post('forgiven_interest') : 0;

        $paid_penalty = !empty($this->input->post('paid_penalty')) ? round($this->input->post('paid_penalty'), 2) : 0;
        # Calculate Demanded Penalty
        $data['demanded_penalty'] = $forgiven_penalty > 0 ? 0 : (round($this->input->post('expected_penalty'), 2) - $paid_penalty);


        $schedule_id = $filter;

        if ($this->input->post('extra_principal') != NULL) {
            $schedule_data = $this->get_schedule_data($schedule_id);
            $data['principal_amount'] = $schedule_data['principal_amount'] + round($this->input->post('extra_principal'), 2);
        }

        if ($paid_total < ($expected_total - ($forgiven_interest + $forgiven_penalty))) {
            $data['payment_status'] = 2;
        } else {
            $data['payment_status'] = 1;
        }
        $this->db->where('repayment_schedule.id =' . $schedule_id);
        $data['actual_payment_date'] = $action_date;
        $data['unique_id'] = $unique_id;
        $data['modified_by'] = (isset($_SESSION['id'])) ? $_SESSION['id'] : 1;

        //echo json_encode($data); die;

        $query = $this->db->update('repayment_schedule', $data);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    //clear an installment payment status
    public function clear_installment($filter = false, $payment_type = false, $unique_id = false)
    {

        if (!empty($this->input->post('action_date'))) {
            $sent_date = explode('-', $this->input->post('action_date'), 3);
            $action_date = count($sent_date) === 3 ? ($sent_date[2] . "-" . $sent_date[1] . "-" . $sent_date[0]) : null;
        } elseif (!empty($this->input->post('payment_date'))) {
            $sent_date = explode('-', $this->input->post('payment_date'), 3);
            $action_date = count($sent_date) === 3 ? ($sent_date[2] . "-" . $sent_date[1] . "-" . $sent_date[0]) : null;
        } else {
            $action_date = date('Y-m-d');
        }

        $data = [];
        if ($filter !== false && $payment_type == 'payoff') { //pay off scenario
            $data['payment_status'] = 3;

            $this->db->where('status_id=1');
            $this->db->where('Client_loan_id=' . $filter);
            $this->db->where('repayment_schedule.payment_status <>1');
            $this->db->where('repayment_schedule.payment_status <>5');
        } elseif ($filter !== false && $payment_type == 'refinance') { //refinace scenario
            $data['payment_status'] = 5;
            $this->db->where('status_id=1');
            $this->db->where('Client_loan_id=' . $filter);
            $this->db->where('repayment_schedule.payment_status <>1');
            $this->db->where('repayment_schedule.payment_status <>3');
        } elseif ($filter !== false && $payment_type === false) { //single installment scenario
            if (is_array($filter) && array_key_exists('paid_total', $filter)) {
                $paid_total = $filter['paid_total'];
                $paid_penalty = $filter['paid_penalty'];
                $expected_penalty = (isset($filter['expected_penalty']) ? $filter['expected_penalty'] : 0);
                $expected_total = $filter['expected_total'];
                $schedule_id = $filter['repayment_schedule_id'];
            } else {
                $paid_total = $this->input->post('paid_total');
                $paid_penalty = $this->input->post('paid_penalty');
                $expected_penalty = $this->input->post('expected_penalty');
                $expected_total = $this->input->post('expected_total');
                $schedule_id = $filter;
                if ($this->input->post('extra_principal') != NULL) {
                    $schedule_data = $this->get_schedule_data($schedule_id);
                    $data['principal_amount'] = $schedule_data['principal_amount'] + round($this->input->post('extra_principal'), 2);
                }
            }
            if ($paid_total < ($expected_total - $expected_penalty - $paid_penalty)) {
                $data['payment_status'] = 2;
            } else {
                $data['payment_status'] = 1;
            }
            //overide the above for now===come back here
            if (is_array($filter) && array_key_exists('state', $filter)) {
                $data['payment_status'] = $filter['state'];
            }
            if (is_array($filter) && array_key_exists('demanded_penalty', $filter)) {
                $data['demanded_penalty'] = $filter['demanded_penalty'];
            }
            $this->db->where('repayment_schedule.id =' . $schedule_id);
        }

        $data['unique_id'] = $unique_id;
        $data['actual_payment_date'] = $action_date;
        $data['modified_by'] = (isset($_SESSION['id'])) ? $_SESSION['id'] : 1;
        $query = $this->db->update('repayment_schedule', $data);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function update2($data, $where)
    {
        $this->db->where($where);
        $query = $this->db->update('repayment_schedule', $data);
        return $query;
    }
    //rescheduling a loan
    public function deactivate_schedule($filter = false, $unique_id = false)
    {
        if ($filter != false) {
            $id = $filter['client_loan_id'];
            $installment_number = $filter['current_installment'];
        } else {
            $id = $this->input->post('client_loan_id');
            $installment_number = $this->input->post('current_installment');
        }

        $data = array(
            'status_id' => '2',
            'modified_by' => $_SESSION['id'],
            'unique_id' => isset($filter['unique_id']) ? $filter['unique_id'] : $unique_id,
        );
        if (is_numeric($id)) {
            $this->db->where('client_loan_id', $id);
            $this->db->where('status_id', 1); // active schedule
            $this->db->where('repayment_schedule.installment_number >= ' . $installment_number);
            $this->db->update('repayment_schedule', $data);
            return true;
        } else {
            return false;
        }
    }

    public function get_deactivate_schedule($filter)
    {
        $id = $this->input->post('client_loan_id');
        if ($this->input->post('installment_number') != NULL) {
            $installment_number = ($this->input->post('installment_number') + 1);
        } else {
            $installment_number = $this->input->post('current_installment');
        }
        if (is_numeric($id)) {
            $this->db->select("*");
            $this->db->from('repayment_schedule');
            $this->db->where('client_loan_id', $id);
            $this->db->where('repayment_schedule.installment_number >= ' . $installment_number);
            $this->db->where($filter);
            $query = $this->db->get();
            return $query->result_array();
        } else {
            return false;
        }
    }
    public function get_loan_data($loan_id)
    {
        $this->db->select(" a.offset_period, a.offset_made_every, product_type_id, a.interest_rate, a.approved_installments, a.approved_repayment_made_every,ifnull(sum(principal_amount),a.amount_approved) amount_approved, a.approved_repayment_frequency, a.loan_product_id, a.loan_no, a.member_id, a.topup_application, a.linked_loan_id, ifnull(b.amount_approved,0) disbursed_amount, ifnull(rsdf.paid_principal,0) parent_paid_principal, ifnull(rsdf.paid_interest,0) parent_paid_interest");
        $this->db->from('client_loan a')
            ->join('repayment_schedule', 'a.id=repayment_schedule.client_loan_id', 'left')
            ->join('loan_product', 'a.loan_product_id=loan_product.id')
            ->join("client_loan b", 'b.id=a.linked_loan_id AND a.topup_application =1', 'left')
            ->join("$this->paid_amount rsdf", 'rsdf.client_loan_id=b.id', 'left')
            ->where('a.id', $loan_id);
        if ($this->input->post('current_installment')) {
            $this->db->where('repayment_schedule.installment_number >=', $this->input->post('current_installment'));
        }
        if ($this->input->post('installment_number')) {
            $this->db->where('repayment_schedule.installment_number >', $this->input->post('installment_number'));
        }
        $this->db->where('repayment_schedule.status_id=1');
        $this->db->limit(1, 0);
        $query = $this->db->get();
        //print_r($this->db->last_query());
        //die;
        return $query->row_array();
    }
    public function get_loan_data_penalty($loan_id)
    {
        $this->db->select("a.offset_period, a.offset_made_every, product_type_id, a.interest_rate, a.approved_installments, a.approved_repayment_made_every,a.amount_approved as amount_approved, a.approved_repayment_frequency, a.loan_product_id, a.loan_no, a.member_id, a.topup_application, a.linked_loan_id, ifnull(b.amount_approved,0) disbursed_amount, ifnull(rsdf.paid_principal,0) parent_paid_principal, ifnull(rsdf.paid_interest,0) parent_paid_interest, ifnull(rsdf.parent_expected_interest,0) parent_expected_interest");
        $this->db->from('client_loan a')
            //->join('repayment_schedule', 'a.id=repayment_schedule.client_loan_id', 'left')
            ->join('loan_product', 'a.loan_product_id=loan_product.id')
            ->join("client_loan b", 'b.id=a.linked_loan_id AND a.topup_application =1', 'left')
            ->join("$this->paid_amount rsdf", 'rsdf.client_loan_id=b.id', 'left')
            ->where('a.id', $loan_id);

        //$this->db->where('repayment_schedule.status_id=1');
        $this->db->limit(1, 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_due_date($loan_id = false, $installment_number = false)
    {

        if ($loan_id !== false && $installment_number !== false) {
            $this->db->select('repayment_date,grace_period_after,actual_payment_date, demanded_penalty, principal_amount,penalty_rate,penalty_rate_charged_per');
            $this->db->from('repayment_schedule')->join('client_loan', 'client_loan.id=repayment_schedule.client_loan_id');
            $this->db->where('repayment_schedule.status_id=1 AND client_loan_id=' . $loan_id . ' AND installment_number=' . $installment_number);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            return false;
        }
    }

    public function due_installments_data($filter = false, $penalty_request = true)
    {
        $this->db->select('a.id,demanded_penalty,actual_payment_date,a.payment_status,installment_number,repayment_date, a.client_loan_id, principal_amount, loan_state2.state_id,grace_period_after,penalty_rate, DATEDIFF(now(),repayment_date) due_days,DATEDIFF(now(),actual_payment_date) due_days2,(principal_amount-ifnull(paid_principal_amount,0)) due_principal, (interest_amount-ifnull(paid_interest_amount,0)) due_interest, ifnull(total_paid_penalty_amount,0) paid_penalty_amount,loan_no, ifnull(penalty_rate_charged_per, penalty_rate_chargedPer) penalty_rate_charged_per');
        $this->db->from('repayment_schedule a');
        $this->db->join("client_loan", "client_loan.id=a.client_loan_id");
        $this->db->join("loan_product lp", "lp.id=client_loan.loan_product_id", "left");
        $this->db->join("$this->loan_installment_payments loan_installment_payments", "loan_installment_payments.repayment_schedule_id=a.id", "LEFT");

        $this->db->join("$this->max_state_id loan_state2", "loan_state2.client_loan_id=a.client_loan_id");

        $this->db->where("a.status_id=1 AND (payment_status = 4 OR  payment_status = 2)");

        if ($this->input->post('staff_id')) {
            $this->db->where("client_loan.created_by", $this->input->post('staff_id'));
        }
        if ($this->input->post('date_to') != NULL && $this->input->post('date_to') !== '') {
            $this->db->where("a.repayment_date <='" . $this->input->post('date_to') . "'");
        }
        if ($this->input->post('date_from') != NULL && $this->input->post('date_from') !== '') {
            $this->db->where("a.repayment_date >='" . $this->input->post('date_from') . "'");
        }
        if ($penalty_request === true) {
            $this->db->where("repayment_date < CURDATE()");
            $list_ids = array(7, 13);
            $this->db->where_in('loan_state2.state_id', $list_ids, FALSE);
            $this->db->having("due_days > grace_period_after");
        } else {
            $this->db->where("repayment_date <= CURDATE()");
            $this->db->where("loan_state2.state_id=7");
        }

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function due_installments_report($filter = 1, $end_date = 'now()')
    {
        $list_ids = array(7, 13);
        $this->db->select("a.id,installment_number,repayment_date, a.client_loan_id, principal_amount, loan_state2.state_id,grace_period_after,penalty_rate, DATEDIFF($end_date,repayment_date) due_days,(principal_amount-ifnull(paid_principal_amount,0)) due_principal, demanded_penalty,(interest_amount-ifnull(paid_interest_amount,0)) due_interest, ifnull(total_paid_penalty_amount,0) paid_penalty_amount,loan_no");
        $this->db->from('repayment_schedule a');
        $this->db->join("client_loan", "client_loan.id=a.client_loan_id");
        $this->db->join("$this->loan_installment_payments loan_installment_payments", "loan_installment_payments.repayment_schedule_id=a.id", "LEFT");
        $this->db->join("$this->max_state_id loan_state2", "loan_state2.client_loan_id=a.client_loan_id");

        if ($this->input->post('staff_id')) {
            $this->db->where("client_loan.created_by", $this->input->post('staff_id'));
        }
        if ($this->input->post('credit_officer_id')) {
            $this->db->where("client_loan.credit_officer_id", $this->input->post('credit_officer_id'));
        }
        $this->db->where($filter);
        $this->db->where("a.status_id=1 AND (payment_status = 4 OR  payment_status = 2 OR actual_payment_date > $end_date)");
        $this->db->where_in('loan_state2.state_id', $list_ids, FALSE);
        $this->db->having("due_days > grace_period_after");
        $query = $this->db->get();
        // print_r($this->db->last_query());die();
        return $query->result_array();
    }

    public function due_installments_report_daily_report($filter = 1, $end_date = 'now()')
    {
        $list_ids = array(7, 13);
        $this->db->select("a.id,installment_number,repayment_date, a.client_loan_id, principal_amount, loan_state2.state_id,grace_period_after,penalty_rate, DATEDIFF($end_date,repayment_date) due_days,(principal_amount-ifnull(paid_principal_amount,0)) due_principal, demanded_penalty,(interest_amount-ifnull(paid_interest_amount,0)) due_interest, ifnull(total_paid_penalty_amount,0) paid_penalty_amount,loan_no");
        $this->db->from('repayment_schedule a');
        $this->db->join("client_loan", "client_loan.id=a.client_loan_id");
        $this->db->join("$this->loan_installment_payments loan_installment_payments", "loan_installment_payments.repayment_schedule_id=a.id", "LEFT");
        $this->db->join("$this->max_state_id loan_state2", "loan_state2.client_loan_id=a.client_loan_id");

        if ($this->input->post('staff_id')) {
            $this->db->where("client_loan.created_by", $this->input->post('staff_id'));
        }
        if ($this->input->post('credit_officer_id')) {
            $this->db->where("client_loan.credit_officer_id", $this->input->post('credit_officer_id'));
        }
        $this->db->where($filter);
        $this->db->where(" a.status_id=1 AND a.payment_status IN(4,2) AND a.actual_payment_date > '{$end_date}' ");
        $this->db->where_in('loan_state2.state_id', $list_ids, FALSE);
        $this->db->having("due_days > grace_period_after");
        $query = $this->db->get();
        // print_r($this->db->last_query());die();
        return $query->result_array();
    }

    public function inarrears_loans()
    {
        $loans_with_last_repayment_date = "(SELECT * FROM fms_repayment_schedule
                WHERE id in ( SELECT MIN(id) from fms_repayment_schedule WHERE status_id=1 AND payment_status=2 OR  payment_status=4 AND repayment_date < CURDATE() GROUP BY client_loan_id ))";
        $this->db->select('*');
        $this->db->from("$loans_with_last_repayment_date loans_with_last_repayment_date");
        $this->db->join("$this->max_state_id loan_state", "loan_state.client_loan_id=loans_with_last_repayment_date.client_loan_id", "LEFT");
        $this->db->where("loan_state.state_id=7");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_expected_principal_and_interest($filter = FALSE, $check = FALSE)
    {
        $where = "WHERE 1 ";

        $this->max_state_id = "(SELECT client_loan_id,state_id,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state $where GROUP BY client_loan_id ) )";

        $this->db->where("repayment_schedule.payment_status IN(2,4,5)");

        if ($this->input->post('start_date') != NULL) {
            $start_date = $this->input->post('start_date');
            $this->db->where("repayment_schedule.repayment_date >= '{$start_date}'");
        }

        if ($this->input->post('end_date') != NULL) {
            $end_date = $this->input->post('end_date');
            $this->db->where("repayment_schedule.repayment_date <= '{$end_date}'");
        }

        $this->db->select("ifnull(SUM(principal_amount),0) AS principal_sum, ifnull(SUM(interest_amount),0) AS interest_sum ", FALSE);
        $this->db->from("repayment_schedule");
        $this->db->join("$this->max_state_id loan_state", "loan_state.client_loan_id=repayment_schedule.client_loan_id");
        $this->db->join("client_loan", "client_loan.id=loan_state.client_loan_id");

        if ($this->input->post('staff_id') != NULL && is_numeric($this->input->post('staff_id'))) {
            $this->db->where('client_loan.created_by', $this->input->post('staff_id'));
        }

        if ($this->input->post('credit_officer_id') != NULL && is_numeric($this->input->post('credit_officer_id'))) {
            $this->db->where('client_loan.credit_officer_id', $this->input->post('credit_officer_id'));
        }

        if ($this->input->post('status_id') != NULL && is_numeric($this->input->post('status_id'))) {
            $this->db->where('repayment_schedule.status_id', $this->input->post('status_id'));
        } else {
            $this->db->where('repayment_schedule.status_id', 1);
        }

        if ($filter != false) {
            $this->db->where($filter);
        }

        if ($check !== FALSE && !(is_numeric($check))) {
            $this->db->where($check);
        }



        $query = $this->db->get();

        $result = $query->result_array();

        if (!empty($result[0])) {
            return $result[0];
        } else {
            return (['principal_sum' => 0, 'interest_sum' => 0]);
        }
    }

    public function get_collected_principal_and_interest()
    {

        $loan_installment_payments = "(SELECT repayment_schedule_id, ifnull(SUM(paid_principal),0) AS paid_principal_amount ,SUM(paid_penalty) AS total_paid_penalty_amount,ifnull(SUM(paid_interest),0) AS paid_interest_amount, payment_date, loan_state.client_loan_id
                    FROM fms_loan_installment_payment 
                    JOIN fms_repayment_schedule ON fms_repayment_schedule.id=fms_loan_installment_payment.repayment_schedule_id 
                    JOIN($this->max_state_id)loan_state ON loan_state.client_loan_id=fms_loan_installment_payment.client_loan_id
                    WHERE (loan_state.state_id=7 OR loan_state.state_id=12) AND fms_loan_installment_payment.status_id!=0
                    GROUP BY fms_loan_installment_payment.repayment_schedule_id )";

        $this->db->from('client_loan cl');
        $this->db->join("$loan_installment_payments lp", "lp.client_loan_id = cl.id", "LEFT");


        if ($this->input->post('start_date') != NULL) {
            $start_date = $this->input->post('start_date');
            $this->db->where("payment_date >= '{$start_date}'");
        }

        if ($this->input->post('end_date') != NULL) {
            $end_date = $this->input->post('end_date');
            $this->db->where("payment_date <= '{$end_date}'");
        }

        $query = $this->db->get();

        $result = $query->result_array();

        if (!empty($result[0])) {
            return $result[0];
        } else {
            return (['paid_principal_amount' => 0, 'paid_interest_amount' => 0]);
        }
    }

    public function get_disbursed_principal($filter = FALSE, $check = FALSE)
    {
        $where = "WHERE 1 ";

        $this->max_state_id = "(SELECT client_loan_id,state_id,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state $where GROUP BY client_loan_id ) )";

        $this->db->where("repayment_schedule.payment_status IN(2,4,5)");

        if ($this->input->post('start_date') != NULL) {
            $start_date = $this->input->post('start_date');
            $this->db->where("loan_state.action_date >= '{$start_date}'");
        }

        if ($this->input->post('end_date') != NULL) {
            $end_date = $this->input->post('end_date');
            $this->db->where("loan_state.action_date <= '{$end_date}'");
        }

        $this->db->select("ifnull(SUM(fms_repayment_schedule.principal_amount),0) AS principal_sum", FALSE);
        $this->db->from("client_loan");

        $this->db->join("$this->max_state_id loan_state", "loan_state.client_loan_id=client_loan.id");

        $this->db->join("repayment_schedule", "repayment_schedule.client_loan_id=loan_state.client_loan_id");

        if ($this->input->post('staff_id') != NULL && is_numeric($this->input->post('staff_id'))) {
            $this->db->where('client_loan.created_by', $this->input->post('staff_id'));
        }

        if ($this->input->post('credit_officer_id') != NULL && is_numeric($this->input->post('credit_officer_id'))) {
            $this->db->where('client_loan.credit_officer_id', $this->input->post('credit_officer_id'));
        }

        if ($this->input->post('status_id') != NULL && is_numeric($this->input->post('status_id'))) {
            $this->db->where('repayment_schedule.status_id', $this->input->post('status_id'));
        } else {
            $this->db->where('repayment_schedule.status_id', 1);
        }

        if ($filter != false) {
            $this->db->where($filter);
        }

        if ($check !== FALSE && !(is_numeric($check))) {
            $this->db->where($check);
        }

        $query = $this->db->get();

        $result = $query->result_array();

        if (!empty($result[0])) {
            return $result[0];
        } else {
            return (['principal_sum' => 0]);
        }
    }

    public function count_installments($client_loan_id)
    {
        $this->db->select("COUNT(id) as installments_count");
        $this->db->from('fms_repayment_schedule');
        $this->db->where('status_id', 1);
        $this->db->where('client_loan_id', $client_loan_id);
        $query = $this->db->get();

        $result = $query->row_array();
        if (isset($result['installments_count'])) {
            return $result['installments_count'];
        }

        return false;
    }

    public function get_last_schedule_payment_date($client_loan_id)
    {
        $this->db->select('repayment_date');
        $this->db->from('fms_repayment_schedule');
        $this->db->where('status_id', 1);
        $this->db->where('client_loan_id', $client_loan_id);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }
}
