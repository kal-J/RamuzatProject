<?php

class Supplier_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select("supplier.*");
        $this->db->select("`country_id`, `country_name`, IF(supplier_type_id=1,'Supplier','Vendor') supplier_type");
        $this->db->select('`supply_sum`, `tax_amount`,supply_count');
        $this->db->select('`bills_sum`, `bills_tax_amount`,bills_count');
        $this->db->from('supplier');
        $this->db->join('country', '`fms_country`.`id` =country_id', 'left');
        
        $supplies_sales_qry = "(SELECT `supplier_id`, COUNT(`id`) `supply_count`, SUM(`sales_amount`) `supply_sum`, COALESCE(SUM(`tax_rate_source_id`*sales_amount),0) `tax_amount`"
                . " FROM `fms_expense` "
                . " JOIN (SELECT `expense_id`, SUM(`amount`) `sales_amount` FROM fms_expense_line GROUP BY `expense_id`) supply_sumation "
                . "ON `id`=`expense_id`"
                . "GROUP BY `supplier_id`) `suppliers`";
        $this->db->join("$supplies_sales_qry", "`fms_supplier`.`id` = `supplier_id`", "LEFT");
        $supplier_bills_qry = "(SELECT `supplier_id` `biller_id`, COUNT(`id`) `bills_count`, SUM(`bills_amount`) `bills_sum`, COALESCE(SUM(`applied_tax_id`*bills_amount),0) `bills_tax_amount`"
                . " FROM `fms_bill` "
                . " JOIN (SELECT `bill_id`, SUM(`amount`) `bills_amount` FROM fms_bill_line GROUP BY `bill_id`) bill_sumation "
                . "ON `id`=`bill_id`"
                . "GROUP BY `supplier_id`) `billers`";
        $this->db->join("$supplier_bills_qry", "`fms_supplier`.`id` = `biller_id`", "LEFT");

        if ($this->input->post("organisation_id") !== NULL) {
            $this->db->where("organisation_id = ", $this->input->post("organisation_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('supplier.id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                !empty($filter) ? $this->db->where($filter) : "";
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_pure($filter = FALSE) {
        $this->db->from('supplier');
        
        if ($_SESSION['role'] != 5) {
            $this->db->where("bond_id", $_SESSION['bond_id']);
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                !empty($filter) ? $this->db->where($filter) : "";
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set($data = []) {

        if (is_array($data) && empty($data)) {
            $data = $this->input->post(NULL, TRUE);
        }
        $data['date_created'] = time();
        $data['modified_by'] = $_SESSION['id'];

        if (isset($data['id']) && is_numeric($data['id'])) { //when updating
            $this->db->where('id', $data['id']);
            unset($data['id'], $data['tbl']);
            return $this->db->update('supplier', $data);
        } else { //for new entries
            $data['date_created'] = time();
            $data['created_by'] = $data['modified_by'];
            unset($data['id'], $data['tbl']);
            $this->db->insert('supplier', $data);
            return $this->db->insert_id();
        }
    }
    
    public function delete($supplier_id) {
        $this->db->where('id', $supplier_id);
        return $this->db->delete('supplier');
    }

}
