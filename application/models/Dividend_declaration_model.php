<?php

/**
 * Description of Dividend_declaration_model
 *
 * @author Allan J. Odeke   Modified by Ajuna Reagan
 * 
 */
class Dividend_declaration_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select("dd.id, paying_preference_sh, cash_stock, paying_ordinary_sh, dd.status_id, dd.share_issuance_id, dividends_payable_acc_id,	total_computed_share, retained_earnings_acc_id, dividends_cash_acc_id, declaration_date, record_date, payment_date, total_computed_share, total_dividends,dividend_per_share, notes, payment_notes, attachment_url,start_date,end_date,fiscal_year_id");
        $this->db->select("re_ac.account_code re_acc_code, re_ac.account_name re_acc_name");
        $this->db->select("dp_ac.account_code dp_acc_code, dp_ac.account_name dp_acc_name");
        $this->db->select("dc_ac.account_code dc_acc_code, dc_ac.account_name dc_acc_name");
        $this->db->from('dividend_declaration dd');
        $this->db->join("fiscal_year", "fiscal_year.id=dd.fiscal_year_id");
        $this->db->join("accounts_chart re_ac", "re_ac.id=dd.retained_earnings_acc_id");
        $this->db->join("accounts_chart dp_ac", "dp_ac.id=dd.dividends_payable_acc_id", "LEFT");
        $this->db->join("accounts_chart dc_ac", "dc_ac.id=dd.dividends_cash_acc_id", "LEFT");
        if ($filter === FALSE) {
            //$this->db->where('dd.organisation_id=' . $_SESSION['organisation_id']);
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('dd.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set($dividend_declaration_attachment_url = NULL,$total_computed_share) {
        $id = $this->input->post('id');

        $data['declaration_date'] = $this->helpers->yr_transformer($this->input->post('declaration_date'));
        $data['record_date'] = $this->helpers->yr_transformer($this->input->post('record_date'));
        $data['payment_date'] = $this->helpers->yr_transformer($this->input->post('payment_date'));
        $data['total_dividends'] = $this->input->post('total_dividends');

        $data['dividend_per_share'] = floor($this->input->post('dividend_per_share'));
        $data['total_computed_share'] = $total_computed_share;

        $data['retained_earnings_acc_id'] = $this->input->post('retained_earnings_acc_id');
        $data['dividends_payable_acc_id'] = $this->input->post('dividends_payable_acc_id');
        $data['dividends_cash_acc_id'] = $this->input->post('dividends_cash_acc_id');
        $data['paying_preference_sh'] = $this->input->post('paying_preference_sh');
        $data['paying_ordinary_sh'] = $this->input->post('paying_ordinary_sh');
        $data['fiscal_year_id'] = $this->input->post('fiscal_year_id');
        $data['share_issuance_id'] = $this->input->post('share_issuance_id');
        $data['notes'] = $this->input->post('notes');
        $data['cash_stock'] = $this->input->post('cash_stock');
        $data['modified_by'] = $_SESSION['id'];

        if (is_numeric($id)) {
            $data["attachment_url"] = $dividend_declaration_attachment_url;

            $this->db->where('id', $id);
            return $this->db->update('dividend_declaration', $data);
        } else {
            $data["attachment_url"] = $dividend_declaration_attachment_url;
            $data['date_created'] = time();
            $data['status_id'] = 1;
            $data['created_by'] = $data['modified_by'];
            $this->db->insert('dividend_declaration', $data);
            return $this->db->insert_id();
        }
    }
    public function set_dividend_paid($share_account_id,$amount,$no_of_shares,$record_amount,$payment_type) {
        $data['date_paid'] = $this->helpers->yr_transformer($this->input->post('transaction_date'));
        $data['declaration_id']=$this->input->post('dividend_declaration_id');
        $data['share_account_id']=$share_account_id;
        $data['no_of_shares']=$no_of_shares;
        $data['record_share_amount']=$record_amount;
        $data['amount']=$amount;
        $data['payment_type']=$payment_type;
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        return $this->db->insert('dividend_payment', $data);
    }

    public function pay_dividend() {
        $dividend_declaration_id = $this->input->post('id');

        $data['payment_date'] = $this->helpers->yr_transformer($this->input->post('transaction_date'));
        $data['status_id'] = 2;
        $data['modified_by'] = $_SESSION['id'];

        if (is_numeric($dividend_declaration_id)) {
            $this->db->where('id', $dividend_declaration_id);
            return $this->db->update('dividend_declaration', $data);
        }
        return false;
    }

    public function update() {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['id'];
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('dividend_declaration', $data);
    }

    public function deactivate($id = FALSE) {
        if ($id !== FALSE) {
            $id = $this->input->post('id');
        }
        $data = array(
            'status_id' => $this->input->post('status_id'),
        );
        $this->db->where('id', $id);
        return $this->db->update('dividend_declaration', $data);
    }

    public function temp_delete($id = FALSE) {
        if ($id !== FALSE) {
            $id = $this->input->post('id');
        }
        $data = ['status_id' => 0];
        $this->db->where('id', $id);
        return $this->db->update('dividend_declaration', $data);
    }

    public function perma_delete($id = FALSE) {
        if ($id !== FALSE) {
            $id = $this->input->post('id');
        }
        $this->db->where('id', $id);
        return $this->db->delete('bill');
    }

    public function get_unpaid_accounts($filter=false,$date_record,$share_issuance_id) {
        $this->current_state = "(SELECT share_account_id,state_id,narrative,action_date FROM fms_share_state
                WHERE id in (
                    SELECT MAX(id) from fms_share_state GROUP BY share_account_id
                )
            )";
         $this->acc_sums1 = "(SELECT share_account_id, (SUM(IFNULL(credit,0))-SUM(IFNULL(debit,0))) as total_amount FROM fms_share_transactions WHERE transaction_date<='$date_record' GROUP BY share_account_id) shtd";

        $this->db->select('share_account.*,savings_account.account_no,savings_account.id as s_acc_id,user.salutation, user.firstname,user.lastname,user.othernames, concat( concat(salutation,".")," ",firstname," ", lastname," ", othernames) AS member_name,share_state.narrative,share_state.state_id,shtd.share_account_id,IFNULL(shtd.total_amount,0) as total_amount,price_per_share,dp.amount,dp.payment_type,dp.status_id as paid_status,dp.date_paid');
        $query = $this->db->from('share_account');
        $this->db->join('dividend_payment dp', 'dp.share_account_id =share_account.id', 'left');
        $this->db->join('savings_account', 'savings_account.id = share_account.default_savings_account_id', 'left');
        $this->db->join('member', 'member.id =share_account.member_id', 'left');
        $this->db->join('user', 'user.id= member.user_id', 'left');
        $this->db->join('share_issuance', 'share_issuance.id=share_account.share_issuance_id', 'left');
        $this->db->join("$this->current_state share_state", 'share_state.share_account_id=share_account.id', 'left');
        $this->db->join("$this->acc_sums1", "share_account.id=shtd.share_account_id", "left");
        $this->db->where('shtd.total_amount>0');
        $this->db->where('share_account.id NOT IN (select share_account_id from fms_dividend_payment)');
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

  
    public function get_delete_jtr($ref_no = FALSE,$ref_id = FALSE,$journal_type_id) {
        $this->db->where('journal_transaction.journal_type_id=' . $journal_type_id);
        if(is_numeric($ref_id)){
        $this->db->where('journal_transaction.ref_id=' . $ref_id);
        } else {
        $this->db->where('journal_transaction.ref_no=' . $ref_no);
        }
        return $this->db->delete('journal_transaction');
    }
   
}
