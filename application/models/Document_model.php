<?php
class Document_model extends CI_Model {

public function __construct() {
  $this->load->database();
}

var $table = 'document';


  //save picture data to db
  function add_document($data){
    
    $created_by = explode('-',$this->input->post('date_created'),3);
    $data['created_by'] = count($created_by)===3?($created_by[2] . "-" . $created_by[1] . "-" . $created_by[0]):null;

    $insert_data['user_id'] = $this->input->post('user_id');
    $insert_data['document_name'] = $data['document_name'];
    $insert_data['document_type_id'] = $data['document_type_id'];
    $insert_data['description'] = $data['description'];
    $insert_data['date_created'] = time();
    $insert_data['created_by'] = $_SESSION['id'];
    //print_r ($this->db->last_query());die();
    $query = $this->db->insert('document', $insert_data);
    return $this->db->insert_id();
  }


public function get($filter = false) {
        
        $this->db->select('a.id id, user_id, document_name, document_type_id, description, a.date_created, created_by, date_modified, modified_by, document_type');
        $this->db->from('document a');
        $this->db->join('document_type b', 'a.document_type_id = b.id','left' );
        if (isset($_POST['user_id']) === true) {
              $user_id = $this->input->post('user_id');
              $this->db->where('user_id', $user_id);
            }
        if ($filter === false) {
          $query= $this->db->get();
          return $query->result_array();
        } else {
          if (is_numeric($filter)) {
              $this->db->where('a.id=' . $filter);
              $query= $this->db->get();
              return  $query->row_array();
          } else {
              $this->db->where($filter);
              $query = $this->db->get();
              return $query->result_array();
          }
        }

}


public function update_document() {

  $data = array(
    //'document_name' => $this->input->post('document_name'),
    'document_type_id' => $this->input->post('document_type_id'),
    'description' => $this->input->post('description'),
    'date_modified' => time(),
    'modified_by' =>$_SESSION['id']
  );

  $id = $this->input->post( 'id' );
  $this->db->where('id', $id );
  $this->db->update($this->table,$data);
  //print_r ($this->db->last_query());die();
  $this->db->affected_rows();
  return true;
}

  public function delete_by_id() { 
    if(isset($_POST['id'])===true){
      $id=$this->input->post('id');
      //get the document by id
      $fectch_link['document_to_del']=$this->get($id);
      $path = "./uploads/organisation_".$_SESSION['organisation_id']."/user_docs/other_docs/".$fectch_link['document_to_del']['document_name'];
//print_r($fectch_link['document_to_del']); die();
    if (!empty($fectch_link['document_to_del']['document_name'])) {
        if (file_exists($path)) {
            if(unlink($path)===true){
              $this->db->where('id', $id);
              $this->db->delete($this->table);
              return true;
            }
      }
    } 
    return false;
  }else{
      return false;
    } 
   
  }
}
?>
