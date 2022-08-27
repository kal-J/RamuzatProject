<?php
class InterestCalMethod_model extends CI_Model {

    public function __construct() {
        $this->load->database();
	}
	
	public function get($id=false) {
	$this->db->from('interest_cal_method m');
      if ($id !==false) {
      $this->db->select('*');
      $this->db->where('m.id', $id );
      $this->db->where('m.status_id', $this->input->post('status_id'));
      $query= $this->db->get();
      return $query->row_array();
      }else{
        $this->db->select('*');
        $this->db->where('m.status_id', $this->input->post('status_id'));
        $query= $this->db->get();
        return $query->result_array();
      }
       
     }
     public function set() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['tbl']);
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] =$_SESSION['id'];

        $this->db->insert('interest_cal_method', $data);
        return $this->db->insert_id();
    }

    public function update() {
        $id=$this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
          unset($data['id'],$data['tbl']);
          $data['date_modified'] = time();
          $data['modified_by'] = $_SESSION['id'];
  
          if (is_numeric($id)) {
            $this->db->where('id',$id);
            return $this->db->update('interest_cal_method', $data); 
          }else{
            return false;
          }
      }
    public function delete() {
        $data = array(
            'status_id' =>0,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('interest_cal_method', $data);
    }

    public function deactivate() {
        $data = array(
            'status_id' =>2,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('interest_cal_method', $data);
    }
}
?>