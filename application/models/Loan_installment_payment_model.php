<?php

/**
 * Description of loan_installment_payment_model
 *
 * @author Eric
 */
class Loan_installment_payment_model extends CI_Model
{

    public function __construct()
    {
        $this->max_state_id = "(SELECT client_loan_id,state_id,comment,action_date FROM fms_loan_state
        WHERE id in ( SELECT MAX(id) from fms_loan_state GROUP BY client_loan_id ) )";
        $this->load->database();
    }

    //inserting installment payment details for a loan
    public function set($data_trans = false)
    {
        //payment_date conversion
        if (!empty($this->input->post('action_date'))) {
            $sent_date = explode('-', $this->input->post('action_date'), 3);
            $payment_date = count($sent_date) === 3 ? ($sent_date[2] . "-" . $sent_date[1] . "-" . $sent_date[0]) : null;
        } else {
            $sent_date = explode('-', $this->input->post('payment_date'), 3);
            $payment_date = count($sent_date) === 3 ? ($sent_date[2] . "-" . $sent_date[1] . "-" . $sent_date[0]) : null;
        }
        $paid_interest = ($this->input->post('paid_interest') > $this->input->post('expected_interest')) ? (($this->input->post('expected_interest') != null) ? $this->input->post('expected_interest') : 0) : (($this->input->post('paid_interest') != null) ? $this->input->post('paid_interest') : 0);
        $paid_principal = ($this->input->post('paid_principal') > $this->input->post('expected_principal')) ? (($this->input->post('expected_principal') != null) ? $this->input->post('expected_principal') : 0) : (($this->input->post('paid_principal') != null) ? $this->input->post('paid_principal') : 0);
        if ($this->input->post('extra_principal') != NULL) {
            $paid_principal += round($this->input->post('extra_principal'), 2);
        }

        if (!empty($this->input->post('extra_amount_use'))) {
            $extra_amount_use = $this->input->post('extra_amount_use');

            if ($extra_amount_use == 2) {
                # Record Extra money as Interest Over
                $interest_over = $this->input->post('extra_amount');
            }
        }

        # Get Forgiven Penalty or Interest
        $forgiven_penalty = !empty($this->input->post('forgiven_penalty')) ? round($this->input->post('forgiven_penalty'), 2) : 0;
        $forgiven_interest = !empty($this->input->post('forgiven_interest')) ? round($this->input->post('forgiven_interest'), 2) : 0;

        $data = array(
            'payment_date' => $payment_date,
            'paid_interest' => $paid_interest,
            'interest_over' => !empty($interest_over) ? $interest_over : (
                ($this->input->post('paid_interest') > $this->input->post('expected_interest')) ? ($this->input->post('paid_interest') - $this->input->post('expected_interest')) : 0),
            'paid_principal' => $paid_principal,
            'principal_over' => ($this->input->post('paid_principal') > $this->input->post('expected_principal')) ? ($this->input->post('paid_principal') - $this->input->post('expected_principal')) : 0,
            'repayment_schedule_id' => $this->input->post('repayment_schedule_id'), //Its null for a pay off payment since the client is paying all money at once
            'paid_penalty' => $this->input->post('paid_penalty'),
            'expected_penalty' => $this->input->post('expected_penalty'), //Its null for a pay off and installment payments made on time
            'comment' => $this->input->post('comment'),
            'status_id' => 1,
            'client_loan_id' => $this->input->post('client_loan_id'),
            'transaction_channel_id' => ($this->input->post('transaction_channel_id')) ? $this->input->post('transaction_channel_id') : 0,
            'date_created' => time(),
            'created_by' => $_SESSION['id'],
            'forgiven_penalty' => $forgiven_penalty,
            'forgiven_interest' => $forgiven_interest,
            'prev_demanded_penalty' => $data_trans ? $data_trans['prev_demanded_penalty'] : $this->input->post('prev_demanded_penalty'),
            'prev_payment_status' => $data_trans ? $data_trans['prev_payment_status'] : $this->input->post('prev_payment_status'),
            'prev_payment_date' => $data_trans ? $data_trans['prev_payment_date'] : $this->input->post('prev_payment_date'),
            'unique_id' => $data_trans ? $data_trans['unique_id'] : ''
        );
        if (!empty($data)) {
            $this->db->insert('loan_installment_payment', $data);
            return $this->db->insert_id();
        }
    } //end of the function
    public function set2($sent_data)
    {
        return $this->db->insert_batch('loan_installment_payment', $sent_data);
    }
    public function set3($sent_data)
    {
        $this->db->insert('loan_installment_payment', $sent_data);
        return $this->db->insert_id();
    }

    public function auto_payment($sent_data,$forgive=TRUE)
    {
        $payment_date = date('Y-m-d');
        # Get Forgiven Penalty or Interest
        $forgiven_penalty = !empty($this->input->post('forgiven_penalty')) && $forgive ? round($this->input->post('forgiven_penalty'), 2) : (!empty($sent_data['forgiven_penalty']) ? $sent_data['forgiven_penalty'] : 0);
        $forgiven_interest = !empty($this->input->post('forgiven_interest')) &&$forgive ? round($this->input->post('forgiven_interest'), 2) : (!empty($sent_data['forgiven_interest']) ? $sent_data['forgiven_interest'] : 0);
        $receipt_amount = !empty($sent_data['receipt_amount']) ? $sent_data['receipt_amount'] : null;

        $data = array(
            'payment_date' => (array_key_exists('payment_date', $sent_data)) ? $sent_data['payment_date'] : $payment_date,
            'paid_interest' => $sent_data['paid_interest'],
            'paid_principal' => $sent_data['paid_principal'],
            'repayment_schedule_id' => $sent_data['repayment_schedule_id'],
            'paid_penalty' => (array_key_exists('paid_penalty', $sent_data)) ? $sent_data['paid_penalty'] : 0,
            'expected_penalty' => (array_key_exists('expected_penalty', $sent_data)) ? $sent_data['expected_penalty'] : 0,
            'comment' => (array_key_exists('comment', $sent_data)) ? $sent_data['comment'] : 'Automatic payment of the loan installment from client\'s savings account', #
            'status_id' => 1,
            'client_loan_id' => $sent_data['client_loan_id'],
            #'transaction_channel_id'=> $sent_data['transaction_channel_id'],
            'date_created' => time(),
            'created_by' => (isset($_SESSION['id']) ? $_SESSION['id'] : 1), #system user id, to be confirmed
            'forgiven_penalty' => $forgiven_penalty,
            'forgiven_interest' => $forgiven_interest,
            'receipt_amount' => $receipt_amount,
            'prev_demanded_penalty' => $sent_data['prev_demanded_penalty'],
            'prev_payment_status' => $sent_data['prev_payment_status'],
            'prev_payment_date' => $sent_data['prev_payment_date'],
            'unique_id' => $sent_data['unique_id']
        );
        $this->db->insert('loan_installment_payment', $data);
        return $this->db->insert_id();
    }

    public function get($filter = FALSE)
    {

        $this->db->select('lp.*, concat(u.firstname, " ", u.lastname, " ", u.othernames) member_name,COALESCE((SELECT SUM(ifnull(principal_amount,0)) + SUM(ifnull(interest_amount,0))
                              FROM fms_repayment_schedule b
                              WHERE b.client_loan_id = lp.client_loan_id AND b.status_id=1),0)-COALESCE((SELECT SUM(ifnull(paid_principal,0)) + SUM(ifnull(paid_interest,0))+ SUM(ifnull(forgiven_interest,0))
                              FROM fms_loan_installment_payment c
                              WHERE lp.id >= c.id AND c.client_loan_id =lp.client_loan_id),0)
                                 AS end_balance,staff_no,su.firstname,su.lastname,su.othernames,loan_no,repayment_schedule.installment_number');

        if (!empty($_POST['start_date'])) {
            $start_date = str_replace('-', '', $_POST['start_date']);
            $this->db->where('lp.payment_date >=' . $start_date);
        }

        if (!empty($_POST['end_date'])) {
            $end_date = str_replace('-', '', $_POST['end_date']);
            $this->db->where('lp.payment_date <= ' . $end_date);
        }

        $query = $this->db->from('loan_installment_payment lp')
            ->join('client_loan', 'client_loan.id=lp.client_loan_id', 'left')
            ->join('member', 'member.id=client_loan.member_id', 'left')
            ->join('user u', 'u.id=member.user_id', 'left')
            ->join('user su', 'su.id=lp.created_by', 'left')
            ->join('staff', 'staff.user_id=su.id', 'left')
            ->join('repayment_schedule', 'repayment_schedule.id=lp.repayment_schedule_id', 'left');

        $this->db->order_by("id", "DESC");
        if ($this->input->post('status_id') != '') {
            $this->db->where('lp.status_id', $this->input->post('status_id'));
            $this->db->where('repayment_schedule.status_id', $this->input->post('status_id'));
        } else {
            $this->db->where('lp.status_id', 1);
            $this->db->where('repayment_schedule.status_id', 1);
        }

        if ($this->input->post('client_loan_id') != '' && $this->input->post('client_loan_id') != 0 && is_numeric($this->input->post('client_loan_id'))) {
            $this->db->where('lp.client_loan_id', $this->input->post('client_loan_id'));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('lp.client_loan_id=' . $filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_receipt($filter = FALSE)
    {
        $this->db->select('lp.*,COALESCE((SELECT SUM(ifnull(principal_amount,0)) + SUM(ifnull(interest_amount,0))
                              FROM fms_repayment_schedule b
                              WHERE b.client_loan_id = lp.client_loan_id AND b.status_id=1),0)-COALESCE((SELECT SUM(ifnull(paid_principal,0)) + SUM(ifnull(paid_interest,0))
                              FROM fms_loan_installment_payment c
                              WHERE lp.id >= c.id AND c.client_loan_id =lp.client_loan_id),0)
                                 AS end_balance,staff_no,d.firstname,d.lastname,d.othernames,loan_no,repayment_schedule.installment_number');
        $query = $this->db->from('loan_installment_payment lp')
            ->join('client_loan', 'client_loan.id=lp.client_loan_id')
            ->join('user', 'user.id=lp.created_by')
            ->join('staff', 'staff.user_id=user.id')
            ->join('member', 'member.id=client_loan.member_id')
            ->join('user d', 'd.id=member.user_id')
            ->join('repayment_schedule', 'repayment_schedule.id=lp.repayment_schedule_id', 'left');

        $this->db->order_by("id", "DESC");
        if ($this->input->post('status_id') != '') {
            $this->db->where('lp.status_id', $this->input->post('status_id'));
        }
        if ($this->input->post('client_loan_id') != '' && $this->input->post('client_loan_id') != 0 && is_numeric($this->input->post('client_loan_id'))) {
            $this->db->where('lp.client_loan_id', $this->input->post('client_loan_id'));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('lp.client_loan_id=' . $filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }


    public function loarn_sums()
    {

        $this->db->select('loan_product_id,SUM(paid_interest+paid_principal) AS already_paid_sum,SUM(paid_penalty) AS already_paid_penalty,SUM(paid_interest) AS already_interest_amount,SUM(paid_principal) AS already_principal_amount,SUM(interest_amount+principal_amount) AS total_payment,SUM(interest_amount) AS interest_sum,SUM(principal_amount) AS principal_sum, SUM(interest_amount) AS to_date_interest_sum');
        $query = $this->db->from('client_loan')->join('loan_installment_payment', 'client_loan.id=loan_installment_payment.client_loan_id', 'left')->join('fms_repayment_schedule', 'client_loan.id=fms_repayment_schedule.client_loan_id', 'left')->group_by('loan_product_id');

        $this->db->where('loan_installment_payment.status_id=1');
        $this->db->where('fms_repayment_schedule.status_id=1');

        $query = $this->db->get();
        //echo $this->db->last_query(); die;
        return $query->result_array();
    }
    public function loan_payment_data($client_loan_id)
    {
        $this->db->select('client_loan.id, SUM(paid_interest) AS already_interest_amount,SUM(paid_principal) AS already_principal_amount');
        $query = $this->db->from('client_loan')->join('loan_installment_payment', 'client_loan.id=loan_installment_payment.client_loan_id', 'left')->join('fms_repayment_schedule', 'client_loan.id=fms_repayment_schedule.client_loan_id', 'left')->group_by('client_loan.id');

        $this->db->where('loan_installment_payment.status_id=1');
        $this->db->where('fms_repayment_schedule.status_id=1');
        $this->db->where("client_loan.id=$client_loan_id");

        $query = $this->db->get();
        //echo $this->db->last_query(); die;
        return $query->row_array();
    }

    //Getting sum of already paid installment
    public function sum_paid_installment($filter = FALSE)
    {
        $where = "WHERE 1 ";
        if ($this->input->post('end_date') != NULL && $this->input->post('end_date') != '') {
            $where .= " AND action_date <='" . $this->input->post('end_date') . "'";
        }
        if ($this->input->post('start_date') != NULL && $this->input->post('start_date') != '') {
            $where .= " AND action_date >='" . $this->input->post('start_date') . "'";
        }
        $this->max_state_id = "(SELECT client_loan_id,state_id,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state $where GROUP BY client_loan_id ) )";

        $this->db->select('ifnull(SUM(paid_interest+paid_principal),0) AS already_paid_sum,ifnull(SUM(paid_penalty),0) AS already_paid_penalty,ifnull(SUM(paid_interest),0) AS already_interest_amount,ifnull(SUM(paid_principal),0) AS already_principal_amount,loan_installment_payment.id as installment_id');
        $this->db->from('loan_installment_payment')->join('repayment_schedule', 'repayment_schedule.id=loan_installment_payment.repayment_schedule_id', 'left')->join("$this->max_state_id loan_state", "loan_state.client_loan_id=loan_installment_payment.client_loan_id")->limit(1, 0);

        $this->db->join("client_loan", "client_loan.id=loan_state.client_loan_id");

        if ($this->input->post('staff_id') !== NULL && is_numeric($this->input->post('staff_id'))) {
            $this->db->where('client_loan.created_by =', $this->input->post('staff_id'));
        }
        if ($this->input->post('credit_officer_id') !== NULL && is_numeric($this->input->post('credit_officer_id'))) {
            $this->db->where('client_loan.credit_officer_id', $this->input->post('credit_officer_id'));
        }
        $this->db->where('loan_installment_payment.status_id=1');
        $this->db->where('repayment_schedule.status_id=1');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('loan_installment_payment.client_loan_id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->row_array();
            }
        }
    }
    public function sum_paid_installment_before($filter = FALSE, $start_date = FALSE, $end_date = FALSE)
    {
        $where = "WHERE 1 ";
        if ($end_date != FALSE) {
            $where .= " AND action_date <='" . $end_date . "'";
        }
        if ($start_date != FALSE) {
            $where .= " AND action_date >='" . $start_date . "'";
        }
        $this->max_state_id = "(SELECT client_loan_id,state_id,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state $where GROUP BY client_loan_id ) )";

        $this->db->select('ifnull(SUM(paid_interest+paid_principal),0) AS already_paid_sum,ifnull(SUM(paid_penalty),0) AS already_paid_penalty,ifnull(SUM(paid_interest),0) AS already_interest_amount,ifnull(SUM(paid_principal),0) AS already_principal_amount,loan_installment_payment.id as installment_id');
        $this->db->from('loan_installment_payment')->join('repayment_schedule', 'repayment_schedule.id=loan_installment_payment.repayment_schedule_id', 'left')->join("$this->max_state_id loan_state", "loan_state.client_loan_id=loan_installment_payment.client_loan_id", "left")->limit(1, 0);

        $this->db->join("client_loan", "client_loan.id=loan_state.client_loan_id");

        if ($this->input->post('staff_id') !== NULL && is_numeric($this->input->post('staff_id'))) {
            $this->db->where('client_loan.created_by =', $this->input->post('staff_id'));
        }
        if ($this->input->post('credit_officer_id') !== NULL && is_numeric($this->input->post('credit_officer_id'))) {
            $this->db->where('client_loan.credit_officer_id =', $this->input->post('credit_officer_id'));
        }
        $this->db->where('loan_installment_payment.status_id=1');
        $this->db->where('repayment_schedule.status_id=1');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('loan_installment_payment.client_loan_id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->row_array();
            }
        }
    }

    //deactivate a loan installment payment
    public function deactivate_payment()
    {
        $id = $this->input->post('client_loan_id');
        $installment_number = $this->input->post('current_installment');
        $data = array(
            'status_id' => '2',
            'modified_by' => $_SESSION['id'],
        );
        if (is_numeric($id)) {
            $this->db->where('client_loan_id', $id);
            $this->db->where('loan_installment_payment.installment_number = ' . $installment_number);
            $this->db->update('loan_installment_payment', $data);
            return true;
        } else {
            return false;
        }
    }

    public function delete_payment($id = false)
    {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->delete('loan_installment_payment');
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete('loan_installment_payment');
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }
}
