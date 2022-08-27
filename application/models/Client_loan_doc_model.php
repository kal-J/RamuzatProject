<?php
class Client_loan_doc_model extends CI_Model {

public function __construct() {
  $this->load->database();
}

var $table = 'client_loan_doc';

  //save picture data to db
  function add_loan_doc($data){
    $insert_data['client_loan_id'] = $this->input->post('client_loan_id');
    $insert_data['file_name'] = $data['file_name'];
    $insert_data['loan_doc_type_id'] = $data['loan_doc_type_id'];
    $insert_data['description'] = $data['description'];
    $insert_data['date_created'] = time();
    $insert_data['created_by'] = $_SESSION['id'];
    $this->db->insert($this->table, $insert_data);
    return $this->db->insert_id();
  }

  public function set($loan_id=false,$files) {
    $query=false;
        if ($loan_id !== false) {
            $client_loan_id=$loan_id;
        }else{
            $client_loan_id=$this->input->post('client_loan_id');
        } 
        $loan_docs = $this->input->post('loan_docs');
        foreach ($loan_docs as $key => $value) {//it is a new entry, so we insert afresh
            $value['date_created'] = time();
            $value['file_name'] = $files[$key];
            $value['client_loan_id'] =$client_loan_id;
            $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
            $query=$this->db->insert($this->table, $value);
        }
        return $query;
    }

    public function duplicate_entry($new_loan_id){
        $sql_query="INSERT INTO fms_client_loan_doc(client_loan_id, loan_doc_type_id, description, file_name, date_created,created_by,modified_by) SELECT ".$new_loan_id.", loan_doc_type_id, description, file_name, UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP('1970-01-01 03:00:00'),".$_SESSION['id'].",".$_SESSION['id']." FROM fms_client_loan_doc WHERE client_loan_id =".$this->input->post('linked_loan_id');
        $query = $this->db->query($sql_query);
        return $this->db->insert_id();
        // print_r($this->db->last_query()); die;
    }

public function get($filter=false) {
  //$this->db->where('a.status_id', $this->input->post('status_id') );
  $this->db->select('a.*, loan_doc_type');
  $this->db->from('client_loan_doc a');
  $this->db->join('loan_doc_type b', 'a.loan_doc_type_id = b.id','left' );
  if(!empty($this->input->post('client_loan_id'))){
      $this->db->where('client_loan_id', $this->input->post('client_loan_id'));
  }
  if ($filter === false) {
      $query = $this->db->get();
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


public function update_loan_doc() {

  $data = array(
    //'loan_doc_type_name' => $this->input->post('loan_doc_type_name'),
    'loan_doc_type_id' => $this->input->post('loan_doc_type_id'),
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

  public function delete_by_id(){
    if (isset($_POST['id']) === true) {
      $id = $this->input->post('id');
      //get the document by id
      $fectch_link['document_to_del'] = $this->get($id);
      $path = "./uploads/organisation_" . $_SESSION['organisation_id'] . "/loan_docs/other_docs/" . $fectch_link['document_to_del']['file_name'];

      if (!empty($fectch_link['document_to_del']['file_name'])) {
        if (file_exists($path)) {
          if (unlink($path) === true) {
            $this->db->where('id', $id);
            $this->db->delete($this->table);
            return true;
          }else {
            return false;
          }
        }else {
          return false;
        }
      }else{
        return false;
      }
    } else {
      return false;
    }

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
