<?php

class Applied_loan_fee_model extends CI_Model {

    public function __construct() {
        $this->load->database();
        $this->table = 'applied_loan_fee';
    }

    public function get($filter = FALSE) {
        $this->db->select("a.*, b.feename,loanfee_id, amountcalculatedas, feetype, amountcalculatedas_id, c.income_account_id, c.income_receivable_account_id, loan_no, concat(u.firstname, ' ', u.lastname, ' ', u.othernames) member_name");
        $this->db->from("$this->table a");
        $this->db->join('client_loan','client_loan.id=a.client_loan_id');
        $this->db->join('loan_product_fees c', 'a.loan_product_fee_id=c.id');
        $this->db->join('loan_fees b', 'c.loanfee_id=b.id')
                ->join('feetype', 'b.feetype_id=feetype.id')
                ->join('amountcalculatedas', 'b.amountcalculatedas_id=amountcalculatedas.id');
        $this->db->join('member m', 'm.id =client_loan.member_id ');
        $this->db->join('user u', 'u.id= m.user_id');
 
        if (!empty($_POST['start_date'])) {
            //$start_date = str_replace('-', '', $_POST['start_date']);
            $start_date = $_POST['start_date'];
            $this->db->where("a.date_paid >='".$start_date."'");
        }

        if (!empty($_POST['end_date'])) {
            //$end_date = str_replace('-', '', $_POST['end_date']);
            $end_date = $_POST['end_date'];
            $this->db->where("a.date_paid <='".$end_date."'");
        }
        
        if(is_numeric($this->input->post("client_loan_id"))){
            $this->db->where('a.client_loan_id', $this->input->post("client_loan_id"));
        }

        if(is_numeric($this->input->post("status_id"))){
            $this->db->where('a.status_id', $this->input->post("status_id"));
        }
        if ($filter == false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set($loan_id=false) {
        $query=false;
        if ($loan_id !== false) {
            $client_loan_id=$loan_id;
        }else{
            $client_loan_id=$this->input->post('loan_id');
        }
        $loanFees = $this->input->post('loanFees');
        foreach ($loanFees as $key => $value) {//it is a new entry, so we insert afresh
            $value['date_created'] = time();
            $value['client_loan_id'] =$client_loan_id; 
            $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
            $insert_query = $this->db->insert_string('applied_loan_fee', $value);
            $insert_query = str_replace("INSERT INTO","INSERT IGNORE INTO",$insert_query);
            $query=$this->db->query($insert_query);
        }
        return $query;
    }

    public function set2($sent_data, $unique_id = false){
        if($unique_id) {
            $sent_data['unique_id'] = $unique_id;
        }
        $insert_query = $this->db->insert_string('applied_loan_fee', $sent_data);
        $insert_query = str_replace("INSERT INTO","INSERT IGNORE INTO",$insert_query);
        $this->db->query($insert_query);
        $last_id= $this->db->insert_id();
        $response['transaction_no']=$last_id;
        $response['transaction_id']=$last_id;
        return $response;
    }

    public function mark_charge_paid($id , $unique_id = false){
        $data = array('paid_or_not' => '1', 'unique_id' => $unique_id,'date_paid'=>$this->helpers->yr_transformer($this->input->post('action_date')));
        $this->db->where('id', $id);
        $query = $this->db->update('applied_loan_fee', $data);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    public function change_status_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            $query = $this->db->update($this->table, $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            $query = $this->db->update($this->table, $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function get_sum($filter = FALSE) {
        $this->db->select("sum(a.amount) total, transaction_no");
        $this->db->from('applied_loan_fee a');
        $this->db->join('loan_fees b', 'a.client_loan_id = b.id');
        $this->db->group_by('transaction_no');
        $this->db->where('a.status_id', 1);
        if (is_numeric($this->input->post('member_id'))) {
            $this->db->where('member_id', $this->input->post('member_id'));
        }
        if ($filter == false) {
            $query = $this->db->get();
            //print_r( $this->db->last_query()); die;
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.id', $filter);
                $query = $this->db->get();
                //print_r( $this->db->last_query()); die;
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                // print_r( $this->db->last_query()); die;
                return $query->row_array();
            }
        }
    }

    public function delete() {
        $this->db->where('id', $this->input->post('id'));
        return  $this->db->delete($this->table);
       // print_r( $this->db->last_query() ); die;
    }

}
