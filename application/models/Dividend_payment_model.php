<?php
/**
 * Description of Dividend_payment_model
 *
 * @author  Ajuna Reagan
 * 
 */
class Dividend_payment_model extends CI_Model {

    public function __construct() {

        $this->load->database();
        $this->current_state = "(SELECT share_account_id,state_id,narrative,action_date FROM fms_share_state
                WHERE id in (
                    SELECT MAX(id) from fms_share_state GROUP BY share_account_id
                )
            )";
    }

    public function get($filter=false,$date_record,$share_issuance_id) {
         $this->acc_sums1 = "(SELECT share_account_id, (SUM(IFNULL(credit,0))-SUM(IFNULL(debit,0))) as total_amount FROM fms_share_transactions WHERE transaction_date<='$date_record' GROUP BY share_account_id) shtd";

        $this->db->select('share_account.*,savings_account.account_no,user.salutation, user.firstname,user.lastname,user.othernames, concat( concat(salutation,".")," ",firstname," ", lastname," ", othernames) AS member_name,share_state.narrative,share_state.state_id,shtd.share_account_id,IFNULL(shtd.total_amount,0) as total_amount,price_per_share,dp.amount,dp.no_of_shares,dp.record_share_amount,dp.status_id as paid_status,dp.date_paid,declaration_id,record_date,dividend_per_share,payment_date,cash_stock,start_date,end_date');
        $query = $this->db->from('dividend_payment dp');
        $this->db->join('share_account', 'dp.share_account_id =share_account.id', 'left');
        $this->db->join('savings_account', 'savings_account.id = share_account.default_savings_account_id', 'left');
        $this->db->join('member', 'member.id =share_account.member_id', 'left');
        $this->db->join('user', 'user.id= member.user_id', 'left');
        $this->db->join('dividend_declaration', 'dividend_declaration.id= dp.declaration_id', 'left');
        $this->db->join('fiscal_year', 'fiscal_year.id= dividend_declaration.fiscal_year_id', 'left');
        $this->db->join('share_issuance', 'share_issuance.id=share_account.share_issuance_id', 'left');
        $this->db->join("$this->current_state share_state", 'share_state.share_account_id=share_account.id', 'left');
        $this->db->join("$this->acc_sums1", "share_account.id=shtd.share_account_id", "left");
        $this->db->where('shtd.total_amount>0');
        $this->db->where('share_account.share_issuance_id', $share_issuance_id);

        if (isset($_POST['status_id'])) {
            $this->db->where('share_account.status_id', $this->input->post('status_id'));
        }
        if (isset($_POST['state_id'])) {
            $this->db->where('share_state.state_id', $this->input->post('state_id'));
        } 
        if (isset($_POST['acc_id'])) {
            $this->db->where('share_account.default_savings_account_id', $this->input->post('acc_id'));
        }
        if (isset($_POST['member_id'])) {
            $this->db->where('share_account.member_id', $this->input->post('member_id'));
        }
        if (isset($_POST['share_account_id'])) {
            $this->db->where('dp.share_account_id', $this->input->post('share_account_id'));
        }
        
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('share_account.id=' . $filter); //fetch according to a share account
                $query = $this->db->get();
                return $query->row_array();
            } elseif (is_array($filter)) {
                $this->db->where($filter);  //gets detail of a share account expects array with id
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

}