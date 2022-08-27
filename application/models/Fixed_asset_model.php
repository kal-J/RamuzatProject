<?php

class Fixed_asset_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {

        $this->db->select("a.id,a.asset_name,a.asset_account_id,IFNULL(cumm_dep,0) cumm_dep, a.identity_no, a.purchase_date, a.purchase_cost, a.date_when, "
                . "a.depreciation_account_id, a.expense_account_id, a.depreciation_method_id, a.depreciation_rate, a.description, a.status_id,a.salvage_value, "
                . "a.expected_age, a.payment_mode_id, a.account_pay_with_id, at.account_name, at.account_code, dm.method_name,pm.payment_mode, "
                . "expac.account_name expense_account_name, expac.account_code expense_account_code,"
                . "pmac.account_name payment_mode_account_name, pmac.account_code payment_mode_account_code,"
                . "depac.account_name depreciation_account_name, depac.account_code depreciation_account_code");
        $this->db->from('fixed_assets a');
        $this->db->join("accounts_chart at", "at.id=a.asset_account_id");
        $this->db->join("accounts_chart depac", "`depac`.`id`=a.depreciation_account_id");
        $this->db->join("accounts_chart expac", "`expac`.`id`=a.expense_account_id");
        $this->db->join("accounts_chart pmac", "`pmac`.`id`=a.account_pay_with_id", "LEFT");
        $this->db->join("depreciation_method dm", "`dm`.`id`=a.depreciation_method_id");
        $this->db->join("payment_mode pm", "`pm`.`id`=a.payment_mode_id","LEFT");
        $this->db->join("(SELECT SUM(`amount`) `cumm_dep`, `fixed_asset_id` FROM `fms_depreciation` GROUP BY `fixed_asset_id`) cum_dep", "`a`.`id`=cum_dep.fixed_asset_id","LEFT",FALSE);
        if(is_numeric($this->input->post("status_id"))){
            $this->db->where("a.status_id=" . $this->input->post("status_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("a.id=" . $filter);
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
        //Application date
        $purchase_date = explode('-', $data['purchase_date'], 3);
        $data['purchase_date'] = count($purchase_date) === 3 ? ($purchase_date[2] . "-" . $purchase_date[1] . "-" . $purchase_date[0]) : null;
        $date_when = explode('-', $data['date_when'], 3);
        $data['date_when'] = count($date_when) === 3 ? ($date_when[2] . "-" . $date_when[1] . "-" . $date_when[0]) : null;
        unset($data['id'], $data['tbl']);
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] =$_SESSION['id'];

        $this->db->insert('fixed_assets', $data);
        return $this->db->insert_id();
    }

    public function update() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['tbl']);
        $data['modified_by'] = $_SESSION['id'];

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('fixed_assets', $data);
        } else {
            return false;
        }
    }

    public function deactivate() {
        $data = array(
            'status_id' => $this->input->post('status_id'),
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fixed_assets', $data);
    }

    public function delete() {
        $data = array(
            'status_id' => 0,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fixed_assets', $data);
    }

}
