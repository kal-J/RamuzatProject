<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {

    protected $CI;

    function __construct() {
        parent::__construct();
        $this->CI = & get_instance();
    }

    public function valid_phone_ug($phone_no) {
        if (preg_match('/^(0|\+256)[2347]([0-9]{8})$/', $phone_no)) {
            return TRUE;
        }
        return FALSE;
    }

    public function valid_phone_int($phone_no) {
        if (preg_match('/^(0|\+\d{1,4})([0-9]{8,11})$/', $phone_no)) {
            return TRUE;
        }
        return FALSE;
    }

    public function valid_email_pass($uname_email) {
        if (preg_match('/^((?=[A-Z0-9][A-Z0-9@._%+-]{5,253}+$)[A-Z0-9._%+-]{1,64}+@(?:(?=[A-Z0-9-]{1,63}+\.)[A-Z0-9]++(?:-[A-Z0-9]++)*+\.){1,8}+[A-Z]{2,63}+)|([a-zA-Z0-9.-]{2,})$/', $uname_email)) {
            return TRUE;
        }
        return FALSE;
    }

    public function valid_date($date, $format) {
        $d = DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) == $date) {
            return true;
        }
        return false;
    }

    public function valid_user($pass, $user_id) {
        $query_result = $this->CI->db->limit(1)->get_where('user', array('id' => $user_id));
        if ($query_result->num_rows() > 0) {
            $users = $query_result->row_array();
            return password_verify($pass, $users['password']);
        }
        return FALSE;
    }

    public function valid_dep_year($dep_year, $fixed_asset_id) {
        $query_result = $this->CI->db
                ->limit(1)
                ->where("`financial_year_id`=$dep_year AND  `fixed_asset_id`=$fixed_asset_id")
                ->get("depreciation");
        return ($query_result->num_rows() === 0);
    }

    public function valid_app_year($dep_year, $fixed_asset_id) {
        $query_result = $this->CI->db
                ->limit(1)
                ->where("`financial_year_id`=$dep_year AND  `fixed_asset_id`=$fixed_asset_id")
                ->get("appreciation");
        return ($query_result->num_rows() === 0);
    }

}
