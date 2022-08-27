<?php

class DepositProduct_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = false) {
        $this->db->select('sp.productname,sp.product_code as product_code,auto_payment,sp.id,sp.description,ac_main.account_name as account_name_main,ac_paid.account_name as account_name_paid,ac_earned.account_name as account_name_earned,sp.producttype,sp.availableto,sp. 	mindepositamount,sp.maxwithdrawalamount,sp.interestpaid,sp.interestcalmtd_id,defaultinterestrate,sp.mininterestrate,sp.maxinterestrate,sp.pernoofdays,sp.wheninterestispaid,sp.daysinyear,sp.applywhtoninterest,sp.defaultopeningbal,sp.minopeningbal,sp.maxopeningbal,sp.min_balance,sp.mintermlength,sp.maxtermlength,sp.account_balance_for_interest_cal,acm.name as account_balance_for_interest_name,sp.status_id,t.typeName,t.description as typedescription, av.name as name_av,icm.interest_method,wip.name as name_wip,diy.name as name_diy,sp.savings_liability_account_id,sp.interest_paid_expense_account_id, sp.interest_earned_payable_account_id,withdraw_cal_method_id,bal_cal_method_id,mandatory_saving, saving_frequency,saving_made_every,schedule_start_date,schedule_current_date, reminder_frequency, reminder_made_every, min_saving_amount, penalty, penalty_calculated_as, penalty_amount, penalty_income_account_id');
        $this->db->from('savings_product sp');
        $this->db->join('deposit_product_type t', 'sp.producttype=t.id', 'left');
        $this->db->join('available_to av', 'sp.availableto=av.id', 'left');
        $this->db->join('account_balance_for_interest acm', 'sp.account_balance_for_interest_cal=acm.id', 'left');
        $this->db->join('days_in_year diy', 'sp.daysinyear=diy.id', 'left');
        $this->db->join('wheninterestispaid wip', 'sp.wheninterestispaid=wip.id', 'left');
        $this->db->join('interest_cal_method icm', 'sp.interestcalmtd_id=icm.id', 'left');

        $this->db->join('fms_accounts_chart ac_main', 'ac_main.id=sp.savings_liability_account_id', 'left');
        $this->db->join('fms_accounts_chart ac_paid', 'ac_paid.id=sp.interest_paid_expense_account_id', 'left');
        $this->db->join('fms_accounts_chart ac_earned', 'ac_earned.id=sp.interest_earned_payable_account_id', 'left');
        if (is_numeric($this->input->post('status_id'))) {
            $this->db->where_not_in('sp.status_id', [$this->input->post('status_id')]);
        }

        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else if (is_numeric($filter)) { //when given the primary key
            $this->db->where('sp.id', $filter);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->where($filter);
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function set() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['tbl']);
        if (isset($data['schedule_start_date'])) {
            $data['schedule_start_date'] = $this->helpers->yr_transformer($data['schedule_start_date']);
        }
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['status_id'] = 1;
        $data['modified_by'] = $_SESSION['id'];
        

        $this->db->insert('savings_product', $data);
        return $this->db->insert_id();
    }

    public function update() {
        $id = $this->input->post('id');

        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['tbl'],$data['rangeFees']);
        if (isset($data['schedule_start_date'])) {
            $data['schedule_start_date'] = $this->helpers->yr_transformer($data['schedule_start_date']);
        }

        // print($data['schedule_start_date']);die;
        $data['date_modified'] = time();
        $data['modified_by'] = 1;

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('savings_product', $data);
        } else {
            return false;
        }
    }

    public function deactivate() {
        $data = array(
            'status_id' => 2,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('savings_product', $data);
    }

    public function change_status() {
        $data = array(
            'status_id' => $this->input->post('status_id'),
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fms_savings_product', $data);
    }

    public function delete() {
        $data = array(
            'status_id' => 0,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fms_savings_product', $data);
    }

    public function get_products($filter = FALSE){
        $this->db->select('*');
        $this->db->from('savings_product');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("savings_product.id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
    public function update_schedule_details($data) {
        $this->db->where('id', $data['id']);
        return $this->db->update('savings_product', $data);
    }


    public function get_range_rates($filter = FALSE) {
            $this->db->select("sr.id,sr.range_amount,min_range,max_range,product_id");
            $this->db->from('savings_interest_setting sr');
            if ($filter === FALSE) {
                $query = $this->db->get();
                return $query->result_array();
            } else {
                if (is_numeric($filter)) {
                    $this->db->where("sr.id", $filter);
                    $query = $this->db->get();
                    return $query->row_array();
                } else {
                    $this->db->where($filter);
                    $query = $this->db->get();
                    return $query->result_array();
                }
            }
    }

    public function insert_range_rates($product_id,$rangeRates_array) {
        if (!empty($rangeRates_array)) {
            $new_range_rates = array();
            foreach ($rangeRates_array as $range) {
                $data = [
                    'range_amount' =>  $range['range_amount'],
                    'min_range' => $range['min_range'],
                    'max_range' => $range['max_range'],
                    'product_id' => $product_id,
                    'created_by' => $_SESSION['id']
                ];
                if (isset($range['id']) && is_numeric($range['id'])) {
                    $this->db->where('id', $range['id']);
                    $this->db->update('savings_interest_setting', $data);
                } else {
                     $data['created_by'] = $_SESSION['id'];
                     $data['date_created'] = time();
                    $new_range_rates[] = $data;
                }
            }
            if (!empty($new_range_rates)) {
                $this->db->insert_batch('savings_interest_setting', $new_range_rates);
            }
        }
        return true;
    }

    public function remove($product_id, $rangeRates_array) {
        if (!empty($rangeRates_array)) {
            $this->db->where('product_id', $product_id);
            $this->db->where_in('id', $rangeRates_array);
            return $this->db->delete('savings_interest_setting');
        }
        return true;
    }

}
