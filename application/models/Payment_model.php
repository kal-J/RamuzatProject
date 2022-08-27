<?php
/**
 * @Author Eric
 */
class Payment_model extends CI_Model{
	
	public	function __construct(){
		# code...
	}

     public function uuid(){
      return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
          mt_rand(0, 0xffff), mt_rand(0, 0xffff),
          mt_rand(0, 0xffff),
          mt_rand(0, 0x0fff) | 0x4000,
          mt_rand(0, 0x3fff) | 0x8000,
          mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
      );
    }

     public function get_for_loan($member_id,$client_loan_id) {
        $this->db->select("*");
        $this->db->from('mobile_money_transactions a');
        $this->db->where('a.member_id=', $member_id);
        $this->db->where('a.client_loan_id=', $client_loan_id);
        $query = $this->db->get();
        return $query->row_array();
    }

	public function get($filter = FALSE) {
        $this->db->select("a.*,concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name,account_no");
        $this->db->from('mobile_money_transactions a');
        $this->db->join('savings_account sa', 'sa.id = a.account_number','left');
        $this->db->join('member m', 'a.member_id = m.id');
        $this->db->join('user c', 'm.user_id = c.id');

        if (is_numeric($this->input->post("member_id"))) {
            $this->db->where('a.member_id=', $this->input->post('member_id'));
        }
        if ( $this->input->post('date_to') != NULL && $this->input->post('date_to') !=='' ) {
             $this->db->where("a.date_created <=". strtotime($this->input->post('date_to')));
        }
        if ($this->input->post('date_from') !==NULL && $this->input->post('date_from') !=='' ) {
            $this->db->where("a.date_created >=". strtotime($this->input->post('date_from')));
        }
        if ($filter === FALSE) {
            $this->db->order_by("id", "DESC");
            $query = $this->db->get();
            //print_r($this->db->last_query());die;
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

	public function set($sent_data){
		$data['member_id'] =$sent_data['member_id'];
        $data['payment_id'] =$sent_data['payment_id'];
		$data['merchant_transaction_id'] =$sent_data['merchant_transaction_id'];
		$data['date_created'] = time();       
        $data['created_by'] =$_SESSION['id'];
        $this->db->insert('fms_mobile_money_transactions', $data);
        //print_r($this->db->last_query());
        return $this->db->insert_id();
	}

    public function merchantTransactionLoans($unique_id = false, $json_loan_disbursement_data){
        $final_string=$this->uuid();
        $post_data['merchant_transaction_id']=$final_string;
        $post_data['ext_ref']=$final_string;
        $post_data['member_id']=$this->input->post('member_id');
        $post_data['requested_amount']=$this->input->post('amount_approved');
        $post_data['paid_amount']=$this->input->post('amount_approved');
        $post_data['request_date']=$this->get_date_time(date('Y-m-d'));
        $post_data['client_loan_id']=$this->input->post('client_loan_id');
        $post_data['payment_status']=4;
        $post_data['status_description']='Request Not Sent';
        $post_data['date_created'] = time();       
        $post_data['created_by'] =$_SESSION['id'];
        $post_data['unique_id'] =$unique_id;
        $post_data['status_id'] = 1;
        $post_data['loan_disbursement_data'] = $json_loan_disbursement_data;
        return $this->db->insert('fms_mobile_money_transactions', $post_data);

    }

    //used by mula checkout api
	public function update(){
		$merchantTransactionID =$this->input->post('merchantTransactionID');

		$data['client_contact'] =$this->input->post('MSISDN');
        $data['checkout_request_id'] =$this->input->post('checkoutRequestID');
		$data['requested_amount'] =$this->input->post('requestAmount');
		$data['paid_amount'] =$this->input->post('amountPaid');
		$data['request_date'] =$this->input->post('requestDate');
		$data['account_number'] =$this->input->post('accountNumber');
		$data['payment_status'] =$this->input->post('requestStatusCode');
		$data['status_description'] =$this->input->post('requestStatusDescription');
        $data['modified_by'] =$_SESSION['id'];

		$this->db->where('mobile_money_transactions.merchant_transaction_id',$merchantTransactionID);
		$query=$this->db->update('mobile_money_transactions', $data);
		return $query;
	}

    //used by beyonic api
    public function update_2($sent_data){
        $merchantTransactionID =$sent_data->metadata->mnt_trx_id;
        $string=str_replace('{u\'merchant_transaction_id\': u\'', '', $merchantTransactionID);
        $merchantTransactionID= substr(trim($string), 0, -2);
        $data['client_contact'] =$sent_data->phonenumber;
        $data['checkout_request_id'] =$sent_data->id;
        $data['requested_amount'] =$sent_data->amount;
        // $data['remote_transaction_id'] =$sent_data->remote_transaction_id;
        $data['paid_amount'] =$sent_data->amount;
        $data['request_date'] =date('Y-m-d H:i:s',strtotime($sent_data->created));
        $data['status_description'] =$sent_data->status;        
        $data['modified_by'] =$_SESSION['id'];

        $this->db->where('mobile_money_transactions.merchant_transaction_id',$merchantTransactionID);
        $query=$this->db->update('mobile_money_transactions', $data);
        return $query;
    }

    public function update_3($sent_data){
        $merchantTransactionID =$sent_data->metadata->mnt_trx_id;
        $data['client_contact'] =$sent_data->phone_nos[0]->phonenumber;
        $data['checkout_request_id'] =$sent_data->id;
        $data['requested_amount'] =$sent_data->amount;
        $data['paid_amount'] =$sent_data->amount;
        $data['request_date'] =date('Y-m-d',strtotime($sent_data->created));
        $data['status_description'] =$sent_data->state;        
        $data['message'] = $sent_data->rejected_reason?$sent_data->rejected_reason:($sent_data->last_error?$sent_data->last_error:$sent_data->cancelled_reason);        
        $data['modified_by'] =$_SESSION['id'];

        $this->db->where('mobile_money_transactions.merchant_transaction_id',$merchantTransactionID);
        $query=$this->db->update('mobile_money_transactions', $data);
        return $query;
    }

    public function update_from_sentepay($sent_data, $ext_ref){
        /* $merchantTransactionID =$sent_data->metadata->mnt_trx_id;
        $string=str_replace('{u\'merchant_transaction_id\': u\'', '', $merchantTransactionID);
        $merchantTransactionID= substr(trim($string), 0, -2); */
        $data['client_contact'] =$sent_data['phonenumber'];
        $data['ref_no'] =$sent_data['refNo'];
        //$data['checkout_request_id'] =$sent_data->id;
        $data['requested_amount'] =$sent_data['amount'];
        // $data['remote_transaction_id'] =$sent_data->remote_transaction_id;
        $data['paid_amount'] =$sent_data['amount'];
        $data['request_date'] =date('Y-m-d');
        $data['status_description'] =$sent_data['message'];        
        $data['modified_by'] =$_SESSION['id'];

        $this->db->where('mobile_money_transactions.merchant_transaction_id',$ext_ref);
        $query=$this->db->update('mobile_money_transactions', $data);
        return $query;
    }


    public function mula_test($sent_data){
        $this->db->insert('mula_test', $sent_data);
        return $this->db->insert_id();
    }

      public function get_date_time($date){ 
        if ($date!=null) {
            $t = microtime(true);
            $micro = sprintf("%06d",($t - floor($t)) * 1000000);
            $d2=new DateTime($date." ".date('H:i:s.'.$micro,$t));
            return $d2->format("Y-m-d H:i:s.u");
        }else{
            return null;
        }
        
    }

}