<?php

/**
 * Description of Loan_state_model
 *
 * @author Eric
 */
class Loan_state_model extends CI_Model {

    public function __construct() {
        $this->load->database();

        $this->max_state_id = "SELECT client_loan_id,state_id,comment,action_date FROM fms_loan_state
        WHERE id in ( SELECT MAX(id) from fms_loan_state GROUP BY client_loan_id ) ";
    }
    public function set($filter=false, $unique_id = false) {
        //Action date conversation
        $action_date = '';
        if ($this->input->post('payment_date') !=NULL && $this->input->post('payment_date') !='') {
            $action_date =$this->helpers->yr_transformer($this->input->post('payment_date'));
        }elseif ($this->input->post('action_date') !=NULL && $this->input->post('action_date') !=''){
            $action_date = $this->helpers->yr_transformer($this->input->post('action_date'));
        }elseif ($this->input->post('application_date') !=NULL && $this->input->post('application_date') !=''){
            $action_date = $this->helpers->yr_transformer($this->input->post('application_date'));
        }else{
            $action_date =date('Y-m-d');
        }
        if ($filter==false) {
            $client_loan_id=$this->input->post('client_loan_id');
        }elseif (is_numeric($filter)) {
           $client_loan_id=$filter;
        }
        if ($this->input->post('state_id') == NULL || empty($this->input->post('state_id')) || $this->input->post('state_id') == '') {
            
            if ($this->input->post('loan_app_stage') !== NULL && $this->input->post('loan_app_stage') == '0') {
                 $state_id=5;
            }elseif ($this->input->post('loan_app_stage') == 1) {
                 $state_id=6;
            }elseif ($this->input->post('loan_app_stage') == 2) {
                $state_id=7;
            }else{
                 $state_id=1;
            }
        }else{
                $state_id =$this->input->post('state_id');            
        }
        $comment=$this->input->post('comment');
        if (is_array($filter) && array_key_exists('state_id', $filter)) {
           $client_loan_id=$filter['client_loan_id'];
           $state_id=$filter['state_id'];
           $comment=$filter['comment'];
        }
        $data = array(
                'client_loan_id' => $client_loan_id,
                'state_id' =>$state_id,
                'unique_id' =>$unique_id,
                'date_created' =>time(),
                'action_date' => $action_date,
                'comment' =>$comment,
                'unique_id' => $unique_id,
                'created_by' =>(isset($_SESSION['id']))?$_SESSION['id']:1 #system user is meant to be entered               
            );

        $this->db->insert('loan_state', $data);
        return $this->db->insert_id();
    }

    public function set2($sent_data){
        $query = $this->db->insert('loan_state', $sent_data);
        return $this->db->insert_id();
    }

    public function update2($sent_data,$client_loan_id){
        $this->db->where('client_loan_id', $client_loan_id);
        $this->db->where('state_id=',10);
        return $query = $this->db->update('loan_state', $sent_data);         
    }

    public function have_them_active($client_loan_id){
         $data = array(
                'client_loan_id' => $client_loan_id,
                'state_id' =>7,
                'date_created' =>time(),
                'action_date' => date('Y-m-d'),
                'comment' =>"LOAN DISBURSED-MOBILE MONEY SENT",
                'created_by' =>1     
            );

        return $this->db->insert('loan_state', $data);
    }

    public function update_payments_request($client_loan_id,$checkout_request_id,$message,$state){
        $data = array(
                'status_description' =>$state,
                'message' =>$message
            );
         $this->db->where('mobile_money_transactions.client_loan_id',$client_loan_id);
         $this->db->where('mobile_money_transactions.checkout_request_id',$checkout_request_id);
        return $this->db->update('mobile_money_transactions', $data);
    }
    public function update_sente_pay_payments_request($client_loan_id,$ext_ref,$message,$state){
        $data = array(
                'status_description' =>$state,
                'message' =>$message
            );
         $this->db->where('mobile_money_transactions.client_loan_id',$client_loan_id);
         $this->db->where('mobile_money_transactions.ext_ref',$ext_ref);
        return $this->db->update('mobile_money_transactions', $data);
    }
    
    public function have_them_in_arrears($sent_data){
        $data = [];
        $query="No in arrears loans found today ".date('d-m-Y H:i:s');
        $action_date = date('Y-m-d');
        foreach($sent_data as $key=>$loan_data){ 
                $data[] = array(
                    'client_loan_id' => $loan_data['client_loan_id'],
                    'state_id' =>13,
                    'date_created' =>time(),
                    'action_date' => $action_date,
                    'comment' =>'Loan automatically moved to in arrears due to incomplete payments',
                    'created_by' =>1,
                );
        }//End of foreach loop
        if(!empty($data)){
            $query=$this->db->insert_batch('loan_state', $data);
            $query.=" Loans in arrears found today ".date('d-m-Y H:i:s');
        }
         return  $query;
    }
    
    public function have_them_out_of_arrears($sent_data){
        $data = [];
        $query="No out of arrears loans found today ".date('d-m-Y H:i:s');
        $action_date = date('Y-m-d');
        foreach($sent_data as $key=>$loan_data){ 
                $data[] = array(
                    'client_loan_id' => $loan_data['client_loan_id'],
                    'state_id' =>7,
                    'date_created' =>time(),
                    'action_date' => $action_date,
                    'comment' =>'Loan automatically moved out of arrears',
                    'created_by' =>1,
                );
        }//End of foreach loop
        if(!empty($data)){
            $query=$this->db->insert_batch('loan_state', $data);
            $query.=" Loans out of arrears found today ".date('d-m-Y H:i:s');
        }
         return  $query;
    }

    public function get($client_loan_id = FALSE) {
        $this->db->select("loan_state.*,state_name,concat(salutation,' ',firstname,' ', lastname,' ', othernames) staff_name,salutation,firstname,lastname, othernames");
        $query = $this->db->from('loan_state')->join('user','user.id=loan_state.created_by')->join('state','state.id=loan_state.state_id');
        $this->db->order_by("id", "DESC");
        if ($client_loan_id === FALSE) {
            $this->db->where('loan_state.client_loan_id',$this->input->post('client_loan_id'));
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($client_loan_id)) {
                $this->db->where('loan_state.client_loan_id',$client_loan_id);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($client_loan_id);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function delete_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->delete('loan_state');
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete('loan_state');
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function get_max_loan_state_by_id($client_loan_id)
    {
        $query = $this->db->query($this->max_state_id . " AND client_loan_id='{$client_loan_id}'");
        $loan_state = $query->row_array();

        if (!empty($loan_state)) {
            return $loan_state['state_id'];
        }

        return null;
    }

}
