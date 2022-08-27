<?php

/**
 * Description of Loan_fees_model
 *
 * @author Eric
 */
class Loan_fees_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {

        $this->db->select("loan_fees.id,feename,feetype,amountcalculatedas,chargetrigger_id,charge_trigger_name,loan_fees.amount,feetype.id AS feetype_id,amountcalculatedas.id AS amountcalculatedas_id,income_receivable_account_id,income_account_id, concat(ac.account_code,' ', ac.account_name) income_account, concat(ac1.account_code,' ', ac1.account_name) income_receivable_account");
        $this->db->from('loan_fees')
                ->join('feetype', 'loan_fees.feetype_id=feetype.id')->join('amountcalculatedas', 'loan_fees.amountcalculatedas_id=amountcalculatedas.id');
        $this->db->join("accounts_chart ac", "ac.id=loan_fees.income_account_id","LEFT");
        $this->db->join("loan_charge_trigger tr", "tr.id=loan_fees.chargetrigger_id","LEFT");
        $this->db->join("accounts_chart ac1", "ac1.id=loan_fees.income_receivable_account_id","LEFT");
        if ($this->input->post('status_id') != NULL) {
            $this->db->where('loan_fees.status_id', $this->input->post('status_id'));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_range_fees($filter = FALSE) {
        $this->db->select("lr.id,lr.amount as range_amount,min_range,max_range,calculatedas_id,loan_fee_id");
        $this->db->from('loan_fee_ranges lr');
       
        if ($this->input->post('status_id') != NULL) {
            $this->db->where('lr.status_id', $this->input->post('status_id'));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('loan_fee_id', $filter);
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
        $data = array(
            'feename' =>  $this->input->post('feename'),
            'feetype_id' => $this->input->post('feetype_id'),
            'amountcalculatedas_id' => $this->input->post('amountcalculatedas_id'),
            'income_receivable_account_id' => $this->input->post('income_receivable_account_id'),
            'income_account_id' => $this->input->post('income_account_id'),
            'chargetrigger_id' => $this->input->post('chargetrigger_id'),
            'fee_applied_to' => $this->input->post('applied_to_id'),
            'amount' => $this->input->post('amount'),
            'date_created' => time(),
            'created_by' => $_SESSION['id'],
            'modified_by' => $_SESSION['id']
        );
        $this->db->insert('loan_fees', $data);
        return $this->db->insert_id();
    }

     public function insert_range_fees($loan_fee_id,$rangeFees_array) {
        if (!empty($rangeFees_array)) {
            $new_range_Fees = array();
            foreach ($rangeFees_array as $range) {
                $data = [
                    'amount' =>  $range['range_amount'],
                    'min_range' => $range['min_range'],
                    'max_range' => $range['max_range'],
                    'calculatedas_id' => $range['calculatedas_id'],
                    'loan_fee_id' => $loan_fee_id,
                    'amount' => $range['range_amount'],
                    'modified_by' => $_SESSION['id']
                ];
                if (isset($range['id']) && is_numeric($range['id'])) {
                    $this->db->where('id', $range['id']);
                    $this->db->update('loan_fee_ranges', $data);
                } else {
                     $data['created_by'] = $_SESSION['id'];
                     $data['date_created'] = time();
                    $new_range_Fees[] = $data;
                }
            }
            if (!empty($new_range_Fees)) {
                $this->db->insert_batch('loan_fee_ranges', $new_range_Fees);
            }
        }
        return true;
    }

    public function remove($loan_fee_id, $rangeFees_array) {
        if (!empty($rangeFees_array)) {
            $this->db->where('loan_fee_id', $loan_fee_id);
            $this->db->where_in('id', $rangeFees_array);
            return $this->db->delete('loan_fee_ranges');
        }
        return true;
    }


    public function update() {
        $data = array(
            'feename' =>  $this->input->post('feename'),
            'feetype_id' => $this->input->post('feetype_id'),
            'amountcalculatedas_id' => $this->input->post('amountcalculatedas_id'),
            'income_receivable_account_id' => $this->input->post('income_receivable_account_id'),
            'income_account_id' => $this->input->post('income_account_id'),
            'chargetrigger_id' => $this->input->post('chargetrigger_id'),
            'fee_applied_to' => $this->input->post('applied_to_id'),
            'amount' => $this->input->post('amount'),
            'date_created' => time(),
            'created_by' => $_SESSION['id'],
            'modified_by' => $_SESSION['id']
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('loan_fees', $data);
    }

    // Loan fees dropdown
    public function get_loan_fees($filter = FALSE) {
        $response = array();
        $this->db->select('id,feename,amount,amountcalculatedas_id');
        if ($filter !== FALSE) {
            $this->db->where($filter);
        } 
        $q = $this->db->get('loan_fees');
        $response = $q->result_array();
        return $response;
    }

    /**
     * This method Deactivates loan_fees data from the database
     */
    public function delete_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->delete('loan_fees');
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete('loan_fees');
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * This method deactivate loan_fees data from the database
     */
    public function change_status_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            $query = $this->db->update('loan_fees', $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            $query = $this->db->update('loan_fees', $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

}
