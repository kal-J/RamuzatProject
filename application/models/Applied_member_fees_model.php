<?php

class Applied_member_fees_model extends CI_Model {

    public function __construct() {
        $this->load->database();
        $this->table = 'applied_member_fees';
    }

    public function get($filter = FALSE) {
        $this->db->select("a.id,a.payment_date,a.payment_id,reverse_msg,reversed_date,payment_mode,feename,a.amount, a.fee_paid,transaction_no,a.date_created,member_fee_id, member_id,b.requiredfee,a.status_id,concat(u.firstname,' ', u.lastname,' ', u.othernames) AS member_name");
        $this->db->from('applied_member_fees a');
        $this->db->join('member m', 'm.id=a.member_id');
        $this->db->join('user u', 'm.user_id=u.id','left');
        $this->db->join('member_fees b', 'a.member_fee_id = b.id');
        $this->db->join('fms_payment_mode pm', 'a.payment_id = pm.id','left');
        $this->db->where('a.status_id', 1);
        if (is_numeric($this->input->post('member_id'))){
            $this->db->where('member_id', $this->input->post('member_id'));
        }
        if ($filter == false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                // print_r( $this->db->last_query()); die;
                return $query->result_array();
            }
        }
    }
    

    public function get_sum($filter = FALSE) {
        $this->db->select("sum(a.amount) total, transaction_no");
        $this->db->from('applied_member_fees a');
        $this->db->join('member_fees b', 'a.member_fee_id = b.id');
        $this->db->join('fms_payment_mode pm', 'a.payment_id = pm.id');
        $this->db->group_by('transaction_no');
        $this->db->where('a.status_id', 1);
        if (is_numeric($this->input->post('member_id'))) {
            $this->db->where('member_id', $this->input->post('member_id'));
        }
        if ($filter == false) {
            $query = $this->db->get();
            //print_r( $this->db->last_query()); die;
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.id', $filter);
                $query = $this->db->get();
                //print_r( $this->db->last_query()); die;
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                // print_r( $this->db->last_query()); die;
                return $query->row_array();
            }
        }
    }

    public function set($data) {
        $this->db->insert('applied_member_fees', $data);
        $last_id= $this->db->insert_id();
        if (is_numeric($last_id)) {
            $response['transaction_no']=$data['transaction_no'];
            $response['transaction_id']=$last_id;
            return $response;
        }else{
            return false;
        }
    }

    public function auto_update($sent_data) {
        $id = $sent_data['member_id'];
        $start_date = explode('-', $this->input->post('transaction_date'), 3);
        $start_date_prepared = count($start_date) === 3 ? ($start_date[2] . "-" . $start_date[1] . "-" . $start_date[0]) : null;

        $data = array(
            'fee_paid' => 1,
            'payment_date' => $sent_data['payment_date'],
            'payment_id' => $sent_data['payment_id'],
            'date_modified' => time(),
            'modified_by' => $sent_data['modified_by']
        );

        $this->db->where('id', $id);
        return $this->db->update('applied_member_fees', $data);
    }

    public function update($id) {
        $start_date = explode('-', $this->input->post('transaction_date'), 3);
        $start_date_prepared = count($start_date) === 3 ? ($start_date[2] . "-" . $start_date[1] . "-" . $start_date[0]) : null;
       
        $data = array(
            'fee_paid' => 1,
            'payment_date' => $start_date_prepared,
            'payment_id' => $this->input->post('payment_id'),
            'date_modified' => time(),
            'modified_by' => $_SESSION['id']    
        );        
        
        $this->db->where('id', $id);
        return $this->db->update('applied_member_fees', $data);
    }

    public function change_status_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            $query = $this->db->update($this->table, $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            $query = $this->db->update($this->table, $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }


    public function delete() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update($this->table, ['status_id' => "0"]);
        // print_r( $this->db->last_query() ); die;
    }

    public function delete2($membership_no) {
         if ($this->delete_jt($membership_no)) {
            $this->delete_savings($membership_no);
            $this->db->where('id', $this->input->post('id'));
            return $this->db->delete($this->table);
            // print_r( $this->db->last_query() ); die;
        } else {
            return false;
        }
    }

     public function delete_jt($membership_no) {
            if ($this->delete_jtrl($membership_no)) {
                $this->db->where('ref_no', $membership_no);
                return $this->db->delete('journal_transaction');
            } else {
                return false;
            }
        }

    public function delete_jtrl($membership_no) {
        $this->db->where('reference_no', $membership_no);
        return $this->db->delete('journal_transaction_line');
    }

     public function delete_savings($membership_no) {
        $this->db->where('ref_no', $membership_no);
        return $this->db->delete('transaction');
    }

}
