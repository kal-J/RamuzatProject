<?php

/**
 * Description of share_issuance_fee_model
 *
 * @author Eric
 */
class Share_issuance_fees_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select('share_issuance_fees.id AS id,chargetrigger_id,feename,amountcalculatedas,amount,'
                . 'amountcalculatedas.id AS amountcalculatedas_id,share_fees_income_receivable_account_id,share_fees_income_account_id,ac_inc.account_name as account_name_ac,ac_rec.account_name as account_name_rec,share_issuance_fees.status_id');
        $this->db->from('share_issuance_fees')
                ->join('share_fees', 'share_issuance_fees.sharefee_id=share_fees.id','left')
                ->join('amountcalculatedas', 'share_fees.amountcalculatedas_id=amountcalculatedas.id','left');
        $this->db->join('fms_accounts_chart ac_inc', 'ac_inc.id=share_issuance_fees.share_fees_income_account_id', 'left');
        $this->db->join('fms_accounts_chart ac_rec', 'ac_rec.id=share_issuance_fees.share_fees_income_receivable_account_id', 'left');
        
        $this->db->where('share_fees.status_id', 1);
        if ($this->input->post('status_id') != "" || !empty($this->input->post('status_id'))) {
            $this->db->where('share_issuance_fees.status_id=',$this->input->post('status_id'));
        }
        
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('share_issuance_fees.shareproduct_id', $filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
               $this->db->where($filter);
                $query = $this->db->get();
                //print_r(json_encode( $query)); die;
                return $query->result_array();
            }
        }
    }

      public function get_fees($filter = FALSE){
        $this->db->select('*');
        $this->db->from('share_issuance_fees');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("share_issuance_fees.id", $filter);
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
        unset($data['id'], $data['requiredfee']);
        $data['status_id'] = '1';
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];

        $this->db->insert('share_issuance_fees', $data);
        return $this->db->insert_id();
    }

    public function update() {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['id'];
        $data['status_id'] = '1';
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('share_issuance_fees', $data);
    }

    /**
     * This method Deactivates share_issuance_fees data from the database
     */
    public function delete_by_id() {
            $data = $this->input->post(NULL, TRUE);
            $data['modified_by'] = $_SESSION['id'];
            $data['status_id'] = 0;
            $this->db->where('id', $this->input->post('id'));
            return $this->db->update('share_issuance_fees', $data);
    }

    /**
     * This method deactivate share_issuance_fees data from the database
     */
    public function change_status_by_id() {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['id'];
        $data['status_id'] = $this->input->post('status_id');
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('share_issuance_fees', $data);

    }

}
