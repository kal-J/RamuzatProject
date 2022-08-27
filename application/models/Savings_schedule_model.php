<?php

/**
 * @author Eric Kasakya.
 */
class Savings_schedule_model extends CI_Model {
	
	public function __construct(){
		$this->load->database();
        $this->single_contact = "
                (SELECT `user_id`, `mobile_number` FROM `fms_contact`
                WHERE `id` in (
                    SELECT MAX(`id`) from `fms_contact` WHERE `contact_type_id`=1 GROUP BY `user_id` 
                )
            )";
	}


	public function set($sent_data){
        return $this->db->insert_batch('savings_schedule', $sent_data);
    }

    public function update($data, $where_clause) {
        $this->db->where($where_clause);
        $query = $this->db->update('savings_schedule', $data);
        return $query;
    }

    public function get_schedule_numbers(){
        $where_clause="from_date <='".$this->helpers->yr_transformer($this->input->post('transaction_date'))."' AND to_date >='".$this->helpers->yr_transformer($this->input->post('transaction_date'))."' AND fulfillment_code=1 AND saving_acc_id=".$this->input->post('account_no_id');
        $query=$this->db->query("
            SELECT id from `fms_savings_schedule` WHERE fulfillment_code=1 AND `id` < (
                    SELECT `id` from `fms_savings_schedule` WHERE $where_clause
                ) AND  saving_acc_id=".$this->input->post('account_no_id')."
            ");
        return $query->result_array();
    }

    public function get($filter = false,$having_clause=false) {
        $this->db->select("a.*,account_no,concat(salutation,' ',firstname,' ', lastname,' ', othernames) AS member_name,firstname, email, mobile_number,c.productname");
        $this->db->from('savings_schedule a');
        $this->db->Join('savings_account b','b.id = a.saving_acc_id');  
        $this->db->Join('savings_product c','c.id=b.deposit_Product_id');         
        $this->db->join("member", "member.id = b.member_id");
        $this->db->join("user", "member.user_id = user.id");         
        $this->db->join($this->single_contact . " c", "c.user_id = user.id", "left");
        if ($this->input->post('account_id')) {
            $this->db->where('a.saving_acc_id', $this->input->post('account_id'));
        }
        if ($this->input->post('product_id')) {
            $this->db->where('c.id', $this->input->post('product_id'));
        }if ($this->input->post('status_id')) {
            $this->db->where('a.fulfillment_code', $this->input->post('status_id'));
        }
        if ($this->input->post('from_date')) {
            $this->db->where('a.from_date >=', $this->helpers->yr_transformer($this->input->post('from_date')));
        }
        if ($this->input->post('to_date')) {
            $this->db->where('a.to_date <=', $this->helpers->yr_transformer($this->input->post('to_date')));
        }
        if ($filter === false) {
            $this->db->order_by('a.id','desc');
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.saving_acc_id', $filter);
                $this->db->order_by('a.id','desc');
                $query = $this->db->get();

                return $query->result_array();
            } else {
                $this->db->where($filter);
                $this->db->having($having_clause);  
                $this->db->order_by('a.id','desc');
                $query = $this->db->get();
                
                return $query->result_array();
            }
        }
    }

    public function get_accounts_under_mandatory_saving($filter = false,$excluded_accounts=false) {
        $this->db->select("a.id,mandatory_saving, saving_frequency, saving_made_every");
        $this->db->from('savings_account a');
        $this->db->Join('savings_product b','b.id=a.deposit_Product_id');
        if ($excluded_accounts && is_array($excluded_accounts)) {
            $this->db->where_not_in('a.id', $excluded_accounts);
        }
        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.id', $filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_savings_account_product_data($filter = false) {
        $this->db->select('sa.id,sa.account_no, mandatory_saving, saving_frequency, saving_made_every,schedule_start_date,schedule_current_date');
        $this->db->from('fms_savings_account sa');
        $this->db->join('fms_savings_product sp', 'sp.id=sa.deposit_Product_id');

        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('sa.id', $filter);
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