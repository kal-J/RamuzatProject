<?php

class Depreciation_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select("d.*, fa.asset_name");
        $this->db->from('depreciation d');
        $this->db->join("fixed_assets fa", "fa.id=d.fixed_asset_id","LEFT");

        if(is_numeric($this->input->post("status_id"))){
            $this->db->where("d.status_id=" . $this->input->post("status_id"));
        }
        if(is_numeric($this->input->post("fixed_asset_id"))){
            $this->db->where("d.fixed_asset_id=" . $this->input->post("fixed_asset_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("d.id=" . $filter);
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
        //echo json_encode($data);die;
        //Application date
        // $transaction_date = explode('-', $data['transaction_date'], 3);
        // $data['transaction_date'] = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
        unset($data['id'], $data['expense_account_id'],$data['transaction_no'],$data['income_account_id'], $data['depreciation_account_id'], $data['tbl']);
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] =$_SESSION['id'];
        $this->db->insert('depreciation', $data);
        $transaction_data['transaction_no']= $data['fixed_asset_id'];
        $transaction_data['transaction_id']=$this->db->insert_id();
        return $transaction_data;
    }

    public function update() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['tbl']);
        $data['modified_by'] = $_SESSION['id'];

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('depreciation', $data);
        } else {
            return false;
        }
    }

    public function deactivate() {
        $data = array(
            'status_id' => $this->input->post('status_id'),
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('depreciation', $data);
    }

    public function delete() {
        $data = array(
            'status_id' => 0,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('depreciation', $data);
    }

 public function next_financial_dep_app_pay($financial_year_id='2016'){

        $query=$this->db->query("SELECT (case when financial_year_id='".$financial_year_id."'  then financial_year_id+1
             else '".$financial_year_id."' end) as fin_yr
                 FROM 
                 (
                 SELECT MIN(financial_year_id)as financial_year_id
                 FROM fms_depreciation
                 WHERE status_id=1 
             
         )a");
   foreach ($query->result() as $row) {
       return $row->fin_yr;
   }

    }

}
