<?php
class User_doc_type_model extends CI_Model {

public function __construct() {
  $this->load->database();
}

var $table = 'user_doc_type';

  //save picture data to db
  function set(){
    $created_by = explode('-',$this->input->post('date_created'),3);
    $data['created_by'] = count($created_by)===3?($created_by[2] . "-" . $created_by[1] . "-" . $created_by[0]):null;

    $insert_data['user_doc_type'] = $this->input->post('user_doc_type');
    $insert_data['description'] = $this->input->post('description');
    $insert_data['date_created'] = time();
    $insert_data['created_by'] = $_SESSION['id'];
    //print_r ($this->db->last_query());die();
    $query = $this->db->insert($this->table, $insert_data);
    return $this->db->insert_id();
  }


public function get() {
  //$this->db->where('status_id', $this->input->post('status_id') );
  $this->db->from( $this->table );
  $this->db->where( 'status_id', '1' );
  $query= $this->db->get();
  return $query->result_array();
}


public function update() {

  $data = array(
    'user_doc_type' => $this->input->post('user_doc_type'),
    'description' => $this->input->post('description'),
    'modified_by' => $_SESSION['id']
  );

  $id = $this->input->post( 'id' );
  $this->db->where('id', $id );
  $this->db->update($this->table,$data);
  //print_r ($this->db->last_query());die();
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
    $this->db->where( 'status_id', 1 );
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
