<?php

/**
 * Description of Payment_engine_model
 *
 * @author Eric
 */
class Payment_engine_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select('a.*,name,link');
        $this->db->from('client_payment_engines a');
        $this->db->join("payment_engines", "payment_engines.id=a.payment_id");
        if($this->input->post('organisation_id') != NULL && is_numeric($this->input->post('organisation_id'))){
            $this->db->where('a.organisation_id=' . $this->input->post('organisation_id'));
        }
        if ($this->input->post('status_id') != NULL && is_numeric($this->input->post('status_id'))){
           $this->db->where('a.status_id=' . $this->input->post('status_id'));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                if ($this->input->post('status_id') == NULL && empty($this->input->post('status_id')) ){
                    $this->db->where('a.status_id=1');
                }
                $this->db->where('a.organisation_id=',$filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    } 

    public function get_sms($filter = FALSE) {
        $this->db->select('a.*,name,api_key');
        $this->db->from('sms_engines a');
        if($this->input->post('organisation_id') != NULL && is_numeric($this->input->post('organisation_id'))){
            $this->db->where('a.organisation_id=' . $this->input->post('organisation_id'));
        }
        if ($this->input->post('status_id') != NULL && is_numeric($this->input->post('status_id'))){
           $this->db->where('a.status_id=' . $this->input->post('status_id'));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.id=',$filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    } 
    public function get_requirement($filter = FALSE) {
        $this->db->select('a.*,channel_name');
        $this->db->from('payment_engine_requirement a');
        $this->db->join("transaction_channel", "transaction_channel.id=a.transaction_channel_id");
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.id=',$filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->row_array();
            }
        }
    }

    public function set() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['hidden'], $data['tbl'],$data['btn_submit']);

        if(empty($this->input->post('organisation_id'))){
        $data['organisation_id'] = $_SESSION['organisation_id'];
        }
        $data['status_id'] = 2;
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $this->db->insert('client_payment_engines', $data);
        return $this->db->insert_id();
    }

    public function set_sms() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['hidden'], $data['tbl'],$data['btn_submit']);

        if(empty($this->input->post('organisation_id'))){
        $data['organisation_id'] = $_SESSION['organisation_id'];
        }
        $data['status_id'] = 1;
        $this->db->insert('sms_engines', $data);
        return $this->db->insert_id();
    }
    public function set_requirement() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['hidden'], $data['tbl'],$data['btn_submit']);

        $data['status_id'] = 1;
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] =$_SESSION['id'];
        $this->db->insert('payment_engine_requirement', $data);
        return $this->db->insert_id();
    }

    public function update() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['hidden'], $data['btn_submit']);
        $data['modified_by'] = $_SESSION['id'];

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('client_payment_engines', $data);
    } 

     public function update_sms() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['hidden'], $data['btn_submit']);

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('sms_engines', $data);
    } 
    public function update_requirement() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['hidden'], $data['btn_submit'],$data['id']);
        $data['modified_by'] = $_SESSION['id'];

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('payment_engine_requirement', $data);
    }

    public function deactivate() {
        $data = array(
            'status_id' =>$this->input->post('status_id'),
            'modified_by' =>$_SESSION['id']
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('client_payment_engines', $data);
    }
    public function check_payment_engine(){
        $query_result = $this->db
                    ->limit(1)
                    ->where('status_id', 1)
                    ->where('organisation_id', $_SESSION['organisation_id'])
                    ->get('client_payment_engines');
            return ($query_result->num_rows() === 1);
    }


}
