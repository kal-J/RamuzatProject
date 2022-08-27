<?php

class Saving_fees_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get_saving_fees($filter = FALSE) {

        $this->db->select('sf.*,ct.charge_trigger_name,ct.charge_trigger_description,adm.date_method,adm.date_method_description,amountcalculatedas,amountcalculatedas.id AS amountcalculatedas_id');

        $this->db->from('fms_saving_fees sf')->join('amountcalculatedas', 'sf.cal_method_id=amountcalculatedas.id');
		$this->db->join('fms_charge_trigger ct','ct.id=sf.chargetrigger_id','left');
		$this->db->join('fms_date_application_methods adm','adm.id=sf.dateapplicationmethod_id','left');
        if(isset($_POST["status_id"])){
            $this->db->where('sf.status_id',$this->input->post('status_id'));
         }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('sf.id=' . $filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

     public function get_range_fees($filter = FALSE) {
        $this->db->select("sr.id,sr.amount as range_amount,min_range,max_range,calculatedas_id,saving_fee_id");
        $this->db->from('savings_fee_ranges sr');
        if ($this->input->post('status_id') != NULL) {
            $this->db->where('sr.status_id', $this->input->post('status_id'));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('saving_fee_id', $filter);
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
            'feename' => $this->input->post('feename'),
			'amount' => $this->input->post('amount'),
            'chargetrigger_id' => $this->input->post('chargetrigger_id'),
            'dateapplicationmethod_id' => $this->input->post('dateapplicationmethod_id'),
            'cal_method_id' => $this->input->post('cal_method_id'),
            'fee_type' => $this->input->post('fee_type'),
            'repayment_made_every' => $this->input->post('repayment_made_every'),
            'repayment_frequency' => $this->input->post('repayment_frequency'),
            'date_created' => time(),
            'created_by' => $_SESSION['id'],                  //$_SESSION['user_id'] 
            'status_id' =>$_SESSION['id']
        );

        $this->db->insert('fms_saving_fees', $data);
        return $this->db->insert_id();
    }
     public function insert_range_fees($saving_fee_id,$rangeFees_array) {
        if (!empty($rangeFees_array)) {
            $new_range_Fees = array();
            foreach ($rangeFees_array as $range) {
                $data = [
                    'amount' =>  $range['range_amount'],
                    'min_range' => $range['min_range'],
                    'max_range' => $range['max_range'],
                    'calculatedas_id' => $range['calculatedas_id'],
                    'saving_fee_id' => $saving_fee_id,
                    'amount' => $range['range_amount'],
                    'modified_by' => $_SESSION['id']
                ];
                if (isset($range['id']) && is_numeric($range['id'])) {
                    $this->db->where('id', $range['id']);
                    $this->db->update('savings_fee_ranges', $data);
                } else {
                     $data['created_by'] = $_SESSION['id'];
                     $data['date_created'] = time();
                    $new_range_Fees[] = $data;
                }
            }
            if (!empty($new_range_Fees)) {
                $this->db->insert_batch('savings_fee_ranges', $new_range_Fees);
            }
        }
        return true;
    }

    public function remove($loan_fee_id, $rangeFees_array) {
        if (!empty($rangeFees_array)) {
            $this->db->where('saving_fee_id', $loan_fee_id);
            $this->db->where_in('id', $rangeFees_array);
            return $this->db->delete('savings_fee_ranges');
        }
        return true;
    }

    public function update() {

        $data = array(
            'feename' => $this->input->post('feename'),
			'amount' => $this->input->post('amount'),
            'chargetrigger_id' => $this->input->post('chargetrigger_id'),
            'dateapplicationmethod_id' => $this->input->post('dateapplicationmethod_id'),
            'cal_method_id' => $this->input->post('cal_method_id'),
            'fee_type' => $this->input->post('fee_type'),
            'date_modified' => time(),
            'modified_by' => $_SESSION['id']  //$_SESSION['user_id']
        );

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fms_saving_fees', $data);
    }
	
	public function delete() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fms_saving_fees',['status_id'=>"0"]);
    }

}