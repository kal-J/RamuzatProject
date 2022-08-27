<?php

/**
 * Description of Subscription_plan_model
 *
 * @author Allan Jes
 */
class Subscription_plan_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select("subscription_plan.*,made_every_name, repayment_start_option_name, concat(ac.account_code,' ', ac.account_name) income_account, concat(ac1.account_code,' ', ac1.account_name) income_receivable_account");
        $this->db->from('subscription_plan')
                ->join('repayment_made_every', 'repayment_made_every.id=subscription_plan.repayment_made_every')
                ->join('fms_first_repayment_start_options', "first_repayment_start_options.id=subscription_plan.first_repayment_starts_upon","left");
        $this->db->join("accounts_chart ac", "ac.id=subscription_plan.income_account_id","LEFT");
        $this->db->join("accounts_chart ac1", "ac1.id=subscription_plan.income_receivable_account_id","LEFT");

        if ($this->input->post('organisation_id') !== NULL) {
            //print_r($this->input->post('organisation_id'));die;
           $this->db->where('subscription_plan.organisation_id', $this->input->post('organisation_id'));
        }
        if ($this->input->post('status_id') !== NULL) {
           $this->db->where('subscription_plan.status_id=',1);
        }
        
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('subscription_plan.id=' . $filter);
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
        unset($data['id'], $data['tbl']);
        $data['date_created'] = time();
        $data['status_id'] = 1;
        $data['created_by'] = $_SESSION['id'];

        $this->db->insert('subscription_plan', $data);
        return $this->db->insert_id();
    }

    public function update() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id']);

        $data['modified_by'] = $_SESSION['id'];
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('subscription_plan', $data);
    }

    public function get_user_sub() {
        $this->db->select("subscription_plan.*,made_every_name, repayment_start_option_name, concat(ac.account_code,' ', ac.account_name) income_account, concat(ac1.account_code,' ', ac1.account_name) income_receivable_account");
        $this->db->from('subscription_plan')
                ->join('repayment_made_every', 'repayment_made_every.id=subscription_plan.repayment_made_every')
                ->join('fms_first_repayment_start_options', "first_repayment_start_options.id=subscription_plan.first_repayment_starts_upon","left");
        $this->db->join("accounts_chart ac", "ac.id=subscription_plan.income_account_id","LEFT");
        $this->db->join("accounts_chart ac1", "ac1.id=subscription_plan.income_receivable_account_id","LEFT");

        if ($this->input->post('organisation_id') !== NULL) {
           $this->db->where('subscription_plan.organisation_id', $this->input->post('organisation_id'));
        }
        if ($this->input->post('status_id') !== NULL) {
           $this->db->where('subscription_plan.status_id', $this->input->post('status_id'));
        }
        
        $this->db->where('subscription_plan.id=' . $this->input->post('id'));
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * This method Deactivates subscription_plan data from the database
     */
    public function delete_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            return $this->db->delete('subscription_plan');
        } else {
            $this->db->where('id', $id);
            return $this->db->delete('subscription_plan');
        }
    }

    public function change_status_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            return $this->db->update('subscription_plan', $data);
        } else {
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            return $this->db->update('subscription_plan', $data);
        }
    }
}
