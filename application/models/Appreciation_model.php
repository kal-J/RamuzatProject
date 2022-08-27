<?php

class Appreciation_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select("app.*, fa.asset_name,fa.purchase_cost,fa.appreciation_rate");
        $this->db->from('appreciation app');
        $this->db->join("fixed_assets fa", "fa.id=app.fixed_asset_id");
        if(is_numeric($this->input->post("status_id"))){
            $this->db->where("app.status_id=" . $this->input->post("status_id"));
        }
        if(is_numeric($this->input->post("fixed_asset_id"))){
            $this->db->where("app.fixed_asset_id=" . $this->input->post("fixed_asset_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("app.id=" . $filter);
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
        $data['transaction_date'] = $data['appre_transaction_date'];
         
        //Application date
         $transaction_date = explode('-', $data['transaction_date'], 3);
        $data['transaction_date'] = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
        //echo json_encode($data['transaction_date']);die;
        unset($data['id'],$data['income_account_id'], $data['appreciation_account_id'], $data['tbl'],$data['appre_transaction_date']);
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] =$_SESSION['id'];
        $this->db->insert('appreciation', $data);
        $transaction_data['transaction_no']= $data['fixed_asset_id'];
        $transaction_data['transaction_id']=$this->db->insert_id();
        return $transaction_data;
    }

    public function update() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['expense_account_id'],$data['appreciation_account_id'],$data['tbl']);
        $data['modified_by'] = $_SESSION['id'];

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('appreciation', $data);
        } else {
            return false;
        }
    }

    public function deactivate() {
        $data = array(
            'status_id' => $this->input->post('status_id'),
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('appreciation', $data);
    }

    public function delete() {
        $data = array(
            'status_id' => 0,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('appreciation', $data);
    }

}
