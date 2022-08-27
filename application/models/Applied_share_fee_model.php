<?php

class Applied_share_fee_model extends CI_Model {

    public function __construct() {
        $this->load->database();
        $this->table = 'applied_share_fee';
    }

    public function get($filter = FALSE) {
        $this->db->select("a.id,transaction_no, a.date_created, b.feename,amountcalculatedas,a.amount,(case when b.requiredfee=0 then 'No' else 'Yes' end) requiredfee_,share_account_id, share_fee_id, shareproduct_id, b.amountcalculatedas_id, b.requiredfee");
        $this->db->distinct();
        $this->db->from("$this->table a");
        $this->db->join('share_fees b', 'a.share_fee_id = b.id');
        $this->db->join('share_issuance_fees c', 'c.sharefee_id=b.id');
        $this->db->join('amountcalculatedas d', 'b.amountcalculatedas_id = d.id');
        $this->db->where('a.status_id', 1);
        if ($filter == false) {
                $this->db->where('a.share_account_id', $this->input->post('share_id'));
                $query = $this->db->get();
                return $query->result_array();
        }else{
            if (is_numeric($filter)) {
                $this->db->where('a.share_account_id', $filter);
                $query = $this->db->get();
                return $query->row_array(); 
            }else{
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set( $transaction_no ) {
        $shareFee = $this->input->post('shareFee');
        $data=[];
        foreach ($shareFee as $key => $value) {//it is a new entry, so we insert afresh
            $value['date_created'] = time();
            $value['share_account_id'] = $this->input->post('share_account_id');
            $value['transaction_no'] =  $transaction_no;
            $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
            $this->db->insert('applied_share_fee', $value);
            $this->db->insert_id();
        }
        return true;
    }

    public function get_sum($filter = FALSE) {
        $this->db->select("a.id, sum(a.amount) total, transaction_no");
        $this->db->from( 'applied_share_fee a' );  
        $this->db->join( 'share_fees b', 'a.share_fee_id = b.id' );
        $this->db->group_by( 'transaction_no' );
        $this->db->where('a.status_id', 1);
        if(is_numeric($this->input->post('member_id')))
        $this->db->where('member_id', $this->input->post('member_id'));
                
        if ($filter == false) {
                $query = $this->db->get();
                //print_r( $this->db->last_query()); die;
                return $query->result_array();
        }else{
                if (is_numeric($filter)) {
                    $this->db->where('a.id', $filter);
                    $query = $this->db->get();
                    //print_r( $this->db->last_query()); die;
                    return $query->row_array(); 
                }else{
                    $this->db->where($filter);
                    $query = $this->db->get();
                    // print_r( $this->db->last_query()); die;
                    return $query->row_array();
                }
        }        
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

    public function delete_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->delete('applied_share_fee');
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete('applied_share_fee');
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

}
