<?php
class Loan_reversal_model extends CI_Model
{
    public function __construct()
    {
        $this->max_state_id = "SELECT client_loan_id,state_id,comment,action_date FROM fms_loan_state
        WHERE id in ( SELECT MAX(id) from fms_loan_state GROUP BY client_loan_id ) ";

        $this->max_trans_id = "SELECT * FROM fms_trans_tracking
        WHERE id in ( SELECT MAX(id) from fms_trans_tracking where status_id = 1 GROUP BY client_loan_id ) ";
    }


    public function get_transactions()
    {
        $this->db->select('t.*, l.loan_no');
        $this->db->where("t.id in ( SELECT MAX(fms_trans_tracking.id) from fms_trans_tracking WHERE status_id=1 GROUP BY fms_trans_tracking.client_loan_id )");
        $this->db->where('t.status_id', 1);
        $this->db->from("fms_trans_tracking t");
        $this->db->join("fms_client_loan l", "l.id=t.client_loan_id");
        $this->db->where("t.status_id", 1);
        $this->db->group_by("t.client_loan_id");

        $query = $this->db->get();
        return $query->result_array();
    }

    public function update_schedule($schedule_data, $id)
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('repayment_schedule', $schedule_data);
        } else {
            return false;
        }
    }


    public function pay_off_reverse_loan_state($state_id)
    {

        $this->db->where('unique_id', $this->input->post('unique_id'));
        $this->db->update('fms_loan_state', ['state_id' => $state_id]);
    }

    public function pay_off_reverse_schedule($base_installment)
    {
        $this->db->trans_start();

        $this->db->where('id', $base_installment['repayment_schedule_id']);
        $data = [
            'payment_status' => $base_installment['prev_payment_status'],
            'demanded_penalty' => $base_installment['prev_demanded_penalty'],
            'actual_payment_date' => $base_installment['prev_payment_date'],
        ];
        $this->db->update('fms_repayment_schedule', $data);

        $this->db->trans_complete();


        $this->db->trans_start();
        $loan_id = $this->input->post('client_loan_id');
        $this->db->where("client_loan_id", $loan_id);
        $this->db->where("status_id", 1);
        $this->db->where("payment_status", 3);
        $this->db->update('fms_repayment_schedule', ['payment_status' => 4]);
        $this->db->trans_complete();
    }

    public function reverse_loan_payment($id)
    {
        $this->db->where('id', $id);

        $this->db->update('fms_loan_installment_payment', ['status_id' => 0]);
    }
    public function get_loan($id)
    {
        $this->db->select("approved_installments, interest_rate, approved_repayment_made_every, approved_repayment_frequency, status_id, modified_by");
        $this->db->from('client_loan');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_max_loan_installment_payment($filter = false)
    {
        // $loan_id = $this->input->post('client_loan_id');

        $this->db->select('*,MAX(id)');
        $this->db->from('fms_loan_installment_payment');
        $this->db->where($filter);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_max_loan_state($filter = false)
    {
        if (is_numeric($filter)) {
            $loan_id = $filter;
        } else {
            $loan_id = $this->input->post('client_loan_id');
        }


        $query = $this->db->query($this->max_state_id . " AND client_loan_id='{$loan_id}'");
        $loan_state = $query->row_array();

        if (!empty($loan_state)) {
            return $loan_state['state_id'];
        }

        return null;
    }

    public function get_trans_tracking($filter)
    {
        $this->db->select('*');
        $this->db->where($filter);
        $this->db->from('fms_trans_tracking');
        $query = $this->db->get();

        return $query->row_array();
    }

    public function get_loan_payments($filter)
    {
        $this->db->select('*');
        $this->db->where($filter);
        $this->db->from('fms_loan_installment_payment');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function reverse_applied_loan_fees($filter)
    {
        $this->db->where($filter);
        // Turn off
        $this->db->update('fms_applied_loan_fee', ['status_id' => 0, 'paid_or_not' => 0]);
    }

    public function turn_off_schedules($filter = false)
    {
        $this->db->where($filter);

        $this->db->update('fms_repayment_schedule', ['status_id' => 0]);
    }

    public function turn_on_schedules($filter = false)
    {
        $this->db->where($filter);
        $this->db->update('fms_repayment_schedule', ['status_id' => 1]);
    }

    public function update_client_loan($data)
    {
        $this->db->where('id', $this->input->post('client_loan_id'));
        $this->db->update('client_loan', $data);
    }

    public function update_loan_payments($filter = false)
    {
        $this->db->where($filter);
        $this->db->update('fms_loan_installment_payment', ['status_id' => 0]);
    }


    public function set_trans_tracking($data)
    {
        $this->db->insert('fms_trans_tracking', $data);
    }

    public function reverse_journals($filter, $reverse_journal_schedules_data = false)
    {

        if ($reverse_journal_schedules_data) {
            $action_type_id = $reverse_journal_schedules_data['action_type_id'];
            $base_repayment_schedule_id = $reverse_journal_schedules_data['base_repayment_schedule_id'];

            $this->db->trans_start();
            $this->db->where($filter);
            if ($action_type_id == 3) { // loan curtailment
                $this->db->where("reference_id > '{$base_repayment_schedule_id}' ");
            }
            if ($action_type_id == 6) { // reschedule
                $this->db->where("reference_id >= '{$base_repayment_schedule_id}' ");
            }
            $this->db->from('fms_journal_transaction_line');
            $query = $this->db->get();
            $entries = $query->result_array();
            $this->db->trans_complete();

            foreach ($entries as $key => $value) {
                if ($value['status_id'] == 1) {
                    // turn off
                    $this->db->trans_start();
                    $this->db->where('id', $value['id']);
                    $update_data = [
                        'reversed_by' => $_SESSION['id'],
                        'reversed_date' => date("Y-m-d H:i:s"),
                        'reverse_msg' => $this->input->post('reverse_msg'),
                        'status_id' => 3
                    ];

                    $this->db->update('fms_journal_transaction_line', $update_data);
                    $this->db->trans_complete();
                }
                if ($value['status_id'] == 3) {
                    // turn off
                    $this->db->trans_start();
                    $this->db->where('id', $value['id']);
                    $update_data = [
                        'reversed_by' => $_SESSION['id'],
                        'reversed_date' => date("Y-m-d H:i:s"),
                        'reverse_msg' => $this->input->post('reverse_msg'),
                        'status_id' => 1
                    ];

                    $this->db->update('fms_journal_transaction_line', $update_data);
                    $this->db->trans_complete();
                }
            }
        } else {

            $this->db->trans_start();
            $this->db->where($filter);
            $update_data = [
                'reversed_by' => $_SESSION['id'],
                'reversed_date' => date("Y-m-d H:i:s"),
                'reverse_msg' => $this->input->post('reverse_msg'),
                'status_id' => 3
            ];
            $this->db->update('fms_journal_transaction', $update_data);
            $this->db->trans_complete();

            $this->db->trans_start();
            $this->db->where($filter);
            $update_data = [
                'reversed_by' => $_SESSION['id'],
                'reversed_date' => date("Y-m-d H:i:s"),
                'reverse_msg' => $this->input->post('reverse_msg'),
                'status_id' => 3
            ];
            $this->db->update('fms_journal_transaction_line', $update_data);
            $this->db->trans_complete();
        }
    }

    public function update_trans_tracking($filter)
    {
        $this->db->where($filter);
        $this->db->update('fms_trans_tracking', ['status_id' => 0]);
    }



    public function reverse_savings($data, $unique_id)
    {
        if (!is_null($unique_id)) {
            $this->db->where('unique_id', $unique_id);
            return $this->db->update('transaction', $data);
        } else {
            return false;
        }
    }
}
