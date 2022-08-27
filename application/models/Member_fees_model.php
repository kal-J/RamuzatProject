<?php
class Member_fees_model extends CI_Model {

public function __construct() {
  $this->load->database();
  $this->table = 'member_fees';
}


  //save picture data to db
  function set(){
    $created_by = explode('-',$this->input->post('date_created'),3);
    $data['created_by'] = count($created_by)===3?($created_by[2] . "-" . $created_by[1] . "-" . $created_by[0]):null;

    $insert_data['feename'] = $this->input->post('feename');
    $insert_data['amount'] = $this->input->post('amount');
    $insert_data['requiredfee'] = $this->input->post('requiredfee');
    $insert_data['description'] = $this->input->post('description');
    $insert_data['income_account_id'] = $this->input->post('income_account_id');
    $insert_data['receivable_account_id'] = $this->input->post('receivable_account_id');
    $insert_data['repayment_made_every'] = $this->input->post('repayment_made_every');
    $insert_data['repayment_frequency'] = $this->input->post('repayment_frequency');
    $insert_data['date_created'] = time();
    $insert_data['created_by'] = $_SESSION['id'];
    $query = $this->db->insert($this->table, $insert_data);
    return $this->db->insert_id();
  }

public function member_fees()
{
  $this->db->select("member_fees.* ");
  $this->db->from( $this->table );
  $this->db->where( 'member_fees.status_id', '1');

  $query = $this->db->get();
  return $query->result_array();

}

public function get( $filter = FALSE ) {
  $this->db->select("member_fees.*,concat(ac.account_code,' ', ac.account_name) income_account ,concat(ac2.account_code,' ', ac2.account_name) receivable_account ");
  $this->db->join("accounts_chart ac", "ac.id=member_fees.income_account_id","LEFT");
  $this->db->join("accounts_chart ac2", "ac2.id=member_fees.receivable_account_id","LEFT");
  $this->db->from( $this->table );
  $this->db->where( 'member_fees.status_id', '1');

  if( $filter === FALSE ){
    $query = $this->db->get();
    return $query->result_array();
  } else {
    if( is_numeric( $filter ) ) {
      $this->db->where('member_fees.id', $filter );
      $query = $this->db->get();
      return $query->row_array();     
    } else {
      $this->db->where( $filter );
      $query = $this->db->get();
      return $query->result_array();
    }
  }
  
  return $query->result_array();
}


public function update() {

    $data = array(
    'feename' => $this->input->post('feename'),
    'amount' => $this->input->post('amount'),
    'requiredfee' => $this->input->post('requiredfee'),
    'description' => $this->input->post('description'),
    'income_account_id' => $this->input->post('income_account_id'),
    'receivable_account_id' => $this->input->post('receivable_account_id'),
    'repayment_made_every' => $this->input->post('repayment_made_every'),
    'repayment_frequency' => $this->input->post('repayment_frequency'),
    'modified_by' => $_SESSION['id']
  );

  $id = $this->input->post( 'id' );
  $this->db->where('id', $id );
  $this->db->update($this->table,$data);
  $this->db->affected_rows();
  return true;
}


  public function delete_by_id()
  {
    $member_fees_id = $this->input->post( 'id' );
    $this->db->where('id', $member_fees_id);
    $this->db->delete($this->table);
    print_r($this->db->last_query()); die();
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
