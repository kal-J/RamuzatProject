<?php
class Client_loan_monthly_income_model extends CI_Model {

public function __construct() {
  $this->load->database();
}

var $table = 'client_loan_monthly_income';

  function set($user_data = []){
    if ($user_data === []) {
    $user_data['client_loan_id'] = $this->input->post('client_loan_id');
    $user_data['income_id'] = $this->input->post('income_id');
    $user_data['amount'] = $this->input->post('amount');
    $user_data['description'] = $this->input->post('description');
    $user_data['date_created'] = time();
    $user_data['created_by'] = $_SESSION['id'];
    }
    $query = $this->db->insert($this->table, $user_data);
    return $this->db->insert_id();
  }
  public function set2($loan_id=false) {
      $query=false;
        if ($loan_id !== false) {
            $client_loan_id=$loan_id;
        }else{
            $client_loan_id=$this->input->post('client_loan_id');
        } 
        $incomes = $this->input->post('incomes');
        foreach ($incomes as $key => $value) {//it is a new entry, so we insert afresh
            $value['date_created'] = time();
            $value['client_loan_id'] =$client_loan_id;
            $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
            $query=$this->db->insert($this->table, $value);
        }
        return  $query;
    }


public function get( $filter = FALSE ) {
  $this->db->select( ' a.id,a.client_loan_id,b.income_type, a.income_id, a.amount, a.description, a.status_id' );
  $this->db->from( $this->table.' a' );  
  $this->db->join('user_income_type b', 'a.income_id = b.id', 'LEFT');
  $this->db->where( 'a.status_id', '1' );
  $client_loan_id = $this->input->post( 'client_loan_id' );
  if( $client_loan_id != '' ){
    $this->db->where( 'a.client_loan_id', $client_loan_id );
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


public function update() {

  $data = array(
    'income_id' => $this->input->post('income_id'),
    'client_loan_id' => $this->input->post('client_loan_id'),
    'amount' => $this->input->post('amount'),
    'description' => $this->input->post('description'),
    'modified_by' => isset($_SESSION)?$_SESSION['staff_id']:1
  );

  $id = $this->input->post( 'id' );
  $this->db->where('id', $id );
  $this->db->update($this->table,$data);
  // print_r ($this->db->last_query());die();
  $this->db->affected_rows();
  return true;
}


  public function delete_by_id()
  {
    $user_doc_type_id = $this->input->post( 'id' );
    $this->db->where('id', $user_doc_type_id);
    $this->db->delete($this->table);
    // print_r($this->db->last_query()); die();
    return true;
  }

  // document_type dropdown
  public  function  get_doc_type($filter = FALSE){
    $response = array();
    $this->db->select( '*' );
    $q = $this->db->get( $this->table );
    $response = $q->result_array();
    return $response;

  }
    public function change_status_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' =>'0');
            $this->db->where('id', $id);
            $query = $this->db->update($this->table,$data);
            //print_r($this->db->last_query());die();
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $data = array('status_id' =>'0');
            $this->db->where('id', $id);
            $query = $this->db->update($this->table,$data);
             //print_r($this->db->last_query());die(); print_r($this->db->last_query());die();
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }


}
?>
