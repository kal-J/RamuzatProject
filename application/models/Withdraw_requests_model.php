<?php

class Withdraw_requests_model extends CI_Model {

  public function __construct() {
    $this->load->database();
  }

  public function save_request() {
    $amount = $this->input->post('amount');
    $reason = $this->input->post('reason');
    $account_no_id = $this->input->post('account_no_id');
    $transaction_channel_id = $this->input->post('transaction_channel_id');
    $member_id = $this->input->post('member_id');
    $curr_balance = $this->input->post('cash_bal');

    if($amount > $curr_balance){
      $response['data'] = null;
      $response['success'] = false;
      $response['message'] = 'Insufficient balance, please deposit funds to this account or request for a smaller amount';

      echo json_encode($response);
      exit;
    }

    $data = array (
      "amount" => $amount,
      "reason" => $reason,
      "account_no_id" => $account_no_id,
      "transaction_channel_id" => $transaction_channel_id,
      "member_id" => $member_id
    );

    $query = $this->db->insert('fms_withdraw_requests',$data);
    if($query){
      $response['data'] = null;
      $response['success'] = true;
      $response['message'] = 'Successfully requested for a cash withdraw of '.$amount. ', you will be contacted when your request has been varified!';
    }else {
      $response['data'] = null;
      $response['success'] = false;
      $response['message'] = 'Something went wrong, please try again later or contact our support team';
    }
    print_r(json_encode($response));
  }

  public function accept_withdraw() {
    $accept_note = $this->input->post("narrative");
    $id = $this->input->post("id");
    $data = array(
      "accept_note" => $accept_note,
      "status" => 2,
      "accepted_by" => $_SESSION['id']
    );
    $this->db->where("id", $id);
    $query = $this->db->update("fms_withdraw_requests", $data);
    if($query){
      return true;
    }
    return false;
  }

  public function decline_withdraw() {
    $decline_note = $this->input->post("decline_note");
    $id = $this->input->post("id");
    $data = array(
      "decline_note" => $decline_note,
      "status" => 3,
      "declined_by" => $_SESSION['id']
    );
    $this->db->where("id", $id);
    $query = $this->db->update("fms_withdraw_requests", $data);
    if($query){
      return true;
    }
    return false;
  }

  public function get_requests($status=1) {
    // statuses
    // 1 - ongoing
    // 2 - accepted
    // 3 - declined
    $this->db->select("wr.*, u.salutation, u.firstname, u.lastname,a.deposit_Product_id");
    
    $this->db->from('user u');
    $this->db->join('member m', 'u.id=m.user_id', 'left');
    $this->db->join('savings_account a', 'm.id=a.member_id', 'left');
    $this->db->join("withdraw_requests wr", "m.id = wr.member_id");
    $this->db->where("wr.status",$status);
    if($this->input->post("client_id") !== null){
      $this->db->where("m.id",$this->input->post("client_id"));
    }
    $query = $this->db->get();
    $results = $query->result_array();
    return $results;
  }

  public function get_all_member_requests() {
    $this->db->select("wr.*, u.salutation, u.firstname, u.lastname,a.deposit_Product_id, a.account_no");
    
    $this->db->from('user u');
    $this->db->join('member m', 'u.id=m.user_id', 'left');
    $this->db->join('savings_account a', 'm.id=a.member_id', 'left');
    $this->db->join("withdraw_requests wr", "m.id = wr.member_id");
    $this->db->order_by("wr.status","asc");
    if($this->input->post("client_id") !== null){
      $this->db->where("m.id",$this->input->post("client_id"));
    }
    $query = $this->db->get();
    $results = $query->result_array();
    return $results;
  }

  public function get_count($status = 1) {
    $this->db->select("*");
    $this->db->where("status", $status);
    $this->db->from("withdraw_requests");
    $query = $this->db->get();
    return $query->num_rows();
  }
}