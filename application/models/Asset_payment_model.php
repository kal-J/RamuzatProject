<?php

class Asset_payment_model extends CI_Model {

    public function __construct() {
        $this->load->database();
        date_default_timezone_set("Africa/Kampala");
    }

    public function get($filter = FALSE) {
        $this->db->select("as.*, fa.asset_name,ac.account_code,payment_mode,ac.account_name");
        $this->db->from('asset_payment as');
        $this->db->join("payment_mode p", "p.id=as.payment_id","LEFT");
        $this->db->join("accounts_chart ac", "ac.id=as.fund_source_account_id", "LEFT");
        $this->db->join("fixed_assets fa", "fa.id=as.asset_id","LEFT");
        if (!empty($this->input->post("status_id"))) {
            $this->db->where("as.status_id IN(" . $this->input->post("status_id").")");
        }
        if (is_numeric($this->input->post("fixed_asset_id"))) {
            $this->db->where("as.asset_id=" . $this->input->post("fixed_asset_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("as.id=" . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

     public function sum_payment($filter) {
        $this->db->select("SUM(IFNULL(amount,0)) as total_payment");
        $this->db->from('asset_payment');
        $this->db->where($filter);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function accumulative_depreciation($filter) {
        $this->db->select("SUM(IFNULL(amount,0)) as cumm_depr,purchase_cost");
        $this->db->from('fms_depreciation dpr')
        ->join('fixed_assets fa','fa.id=dpr.fixed_asset_id');
        $this->db->where($filter);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function set($data = array()) {
        if (empty($data)) {
            $data = $this->input->post(NULL, TRUE);
            unset($data['id'], $data['asset_account_id'], $data['tbl']);
        }
        $transaction_date = explode('-', $data['transaction_date'], 3);
        $data['transaction_date'] = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
        $data['transaction_no'] = date('yws').mt_rand(1000, 9999);
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] = $_SESSION['id'];
        $this->db->insert('asset_payment', $data);
        $last_id = $this->db->insert_id();
        if(is_numeric($last_id)){
            $response['transaction_no']=$data['transaction_no'];
            $response['transaction_id']=$last_id;
            return $response;
        }else{
            return false;
        }
    }
    //set2: disposal 
    public function set2($data = array()) {
        if (empty($data)) {
            $data = $this->input->post(NULL, TRUE);
          // print_r($data['amount4']);die();

                if($data['disposal_method']==3){
                 $data['amount'] = $data['amount3'];
                 $data['loss_or_gain']=2;
                  }
                else if($data['disposal_method']==2 && $data['depre_appre_id']==1){
                 $data['amount'] = $data['amount2'];
                 $data['loss_or_gain']=1;
                }
                 else if($data['disposal_method']==2 && $data['depre_appre_id']==2){
                 $data['amount'] = $data['amount4'];
                 $data['loss_or_gain']=1;
                }
                else{
                 $data['amount']=$data['amount1'];
                }
             
            unset($data['id'],$data['status_id'],$data['disposal_method'] ,$data['purchase_cost'],$data['cumm_dep'],$data['asset_account_id'], $data['tbl']);
        }
        $transaction_date = explode('-', $data['transaction_date'], 3);
        $data['transaction_date'] = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . 
        $transaction_date[0]) : null;
        $data['transaction_no'] = date('yws').mt_rand(1000, 9999);
        $data['narrative']= $data['narrative']."- ".$data['asset_name'];
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] = $_SESSION['id'];

        unset($data['asset_name'],$data['depre_appre_id'],$data['amount2'],$data['amount1'],$data['amount3'],$data['amount4']);
        $this->db->insert('asset_payment', $data);
        $last_id = $this->db->insert_id();
        if(is_numeric($last_id)){
            $response['transaction_no']=$data['transaction_no'];
            $response['transaction_id']=$last_id;
            return $response;
        }else{
            return false;
        }
    }

    public function update() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
      
        unset($data['id'],$data['journal_type_id'],$data['transaction_no'],$data['tbl']);
        $data['modified_by'] = $_SESSION['id'];
        $transaction_date = explode('-', $data['transaction_date'], 3);
        $data['transaction_date'] = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('asset_payment',$data);
        } else {
            return false;
        }
    }
    public function reverse() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['journal_type_id'],$data['transaction_no'],$data['tbl']);
        $data['reversed_by'] = $_SESSION['id'];
        $data['reversed_date'] = date("Y-m-d H:i:s");
        $data['reverse_msg'] = $this->input->post('reverse_msg');
        $data['status_id'] = 3;

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('asset_payment', $data);
        } else {
            return false;
        }
    }


}
