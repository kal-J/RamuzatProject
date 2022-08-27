<?php

/**
 * Description of share_calls_model
 *
 * @author Melchisedec
 */
class Share_call_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select('*');
        $this->db->from('share_calls');
        $this->db->where('share_calls.status_id', $this->input->post('status_id'));
        $this->db->where('share_calls.issuance_id', $this->input->post('issuance_id'));

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id']);
        $data['status_id'] = '1';
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['staff_id'];

        $this->db->insert('share_calls', $data);
        return $this->db->insert_id();
    }

    public function update() {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['id'];
        $data['status_id'] = '1';
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('share_calls', $data);
    }


    public function change_first_call() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('share_calls', ['first_call' => $this->input->post('first_call')]);
    }
    public function change_status() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('share_calls', ['status_id' => $this->input->post('status_id')]);
    }

    public function validate_percentage($pecentage) {
            $this->db->select('SUM(IFNULL(percentage,0)) as total_percentage');
            $this->db->from('share_calls');
            $this->db->where('share_calls.status_id', 1);
            $this->db->where('share_calls.issuance_id', $this->input->post('issuance_id'));

            if(!empty($this->input->post('id'))){
            $this->db->where_not_in('share_calls.id', $this->input->post('id'));
            }
            $query = $this->db->get();
            return $query->row_array();
        }

    // Share calls dropdown
    public function get_share_calls($filter) {
    $sql="SELECT call_id, sa_id, per_call_amount,call_name,call_paid_amount, IFNULL(per_call_amount,0)-IFNULL(call_paid_amount,0) call_balance FROM (SELECT ROUND(sum((percentage/100)*total_price),2) as per_call_amount,sa.id as sa_id, call_name,sc.id as call_id from fms_share_calls sc JOIN fms_share_account sa ON sa.share_issuance_id=sc.issuance_id GROUP BY sa.id, sc.id) call_amounts LEFT JOIN (SELECT ROUND(sum(amount),2) as call_paid_amount,share_call_id,application_id FROM `fms_share_transactions` GROUP BY application_id,share_call_id) call_payments ON call_amounts.call_id = call_payments.share_call_id WHERE IFNULL(call_paid_amount,0)<per_call_amount AND sa_id=$filter";

        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    public function get_first_calls($filter = FALSE) {
        $this->db->select('*');
        $this->db->from('share_calls');
        $this->db->where('share_calls.status_id', 1);
        $this->db->where('share_calls.first_call', 1);
        $this->db->where('share_calls.issuance_id', $filter);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function get_all_calls($filter = FALSE) {
        $this->db->select('*');
        $this->db->from('share_calls');
        $this->db->where('share_calls.status_id', 1);
        $this->db->where('share_calls.issuance_id', $filter);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_approved_shares($filter = FALSE) {
        $this->db->select('SUM(IFNULL(approved_shares,0)) as total_approved_shares');
        $this->db->from('share_calls');
        $this->db->where('share_calls.status_id', 1);
        $this->db->where('share_calls.issuance_id', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

}
