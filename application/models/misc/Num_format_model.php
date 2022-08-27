<?php

/**
 * Description of Num_format_model
 *
 * @author Allan J. Odeke and modified by Ambrose Ogwang
 */
class Num_format_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    public function get_last_no($table, $field)
    {
        $this->db->select($field);
        $this->db->from($table);
        $this->db->order_by('id', "DESC");
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->row_array();
        //return sprintf("%04d", !empty($result)?($result[$field]+1):1);
        return $result[$field];
    }

    public function get_last_no2($table, $field, $account_initials2, $product_code = false, $product_id = false)
    {
        // var_dump(); die();
        // print_r($field . ' LIKE ' . "'" . $product_code . "%" . "'"); die();

        $this->db->select_max($field);
        $this->db->from($table);

        if (($product_code == false || $product_code == "null" || $product_code == NULL || $product_code == "") && !empty($account_initials2)) {
            $this->db->where($field . ' LIKE ' . "'" . $account_initials2 . "%" . "'");
        }

        if ($table == 'savings_account' && $product_code) {
            $field2 = "deposit_Product_id";
            $this->db->where($field . ' LIKE ' . "'" . $product_code . "%" . "'");
            $this->db->where($field2,$product_id);
        } else if ($table == 'client_loan' && $product_code) {
            //$field2 = "loan_product_id";
            $this->db->where($field . ' LIKE ' . "'" . $product_code . "%" . "'");
            //$this->db->where($field2,"$product_id");
        } else if ($table == 'share_account' && $product_code) {
            //$field2 = "share_issuance_id";
            $this->db->where($field . ' LIKE ' . "'" . $product_code . "%" . "'");
            //$this->db->where($field2, "$product_id");
        }

        $this->db->group_by($field);

        $this->db->order_by($field, 'DESC');
        //ignores the admin member and generates only for the members .
        if ($table == 'member') {
            $this->db->where('user_id!=', 1);
        }
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->row_array();

        // print_r($this->db->last_query()); die;
        
        if ($result)
            return $result[$field];
        else return null;
    }
}
