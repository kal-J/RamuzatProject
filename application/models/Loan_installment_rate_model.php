<?php
/**
 * Description of Branch_model
 *
 * @author Melchisedec
 */
class Loan_installment_rate_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    var $table = 'fms_loan_installment_rate';

    public function get($filter = FALSE) {
        $this->db->from($this->table);
        $this->db->where('active_id', $this->input->post('active_id'));
        if ($filter === FALSE) {
            $query = $this->db->get();
            //print_r($this->db->last_query()); die();
            return $query->result_array();
        } else {
            if (is_numeric($filter)){
                //$this->db->where('branch.id=' . $filter);
                // $this->db->where('active_id=',0);
                $this->db->where('active_id', $this->input->post('active_id'));
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                // $this->db->where('active_id=',0);
                $this->db->where('active_id', $this->input->post('active_id'));
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set() {

        $created_by = explode('-',$this->input->post('date_created'),3);
        $data['created_by'] = count($created_by)===3?($created_by[2] . "-" . $created_by[1] . "-" . $created_by[0]):null;
        //'created_by' => isset($_SESSION)?$_SESSION['staff_id']:1

        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['tbl']);
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];

        

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
	
    public function update() {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['id'];

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update($this->table, $data);
    }

  public function delete_by_id()
  {
    $id = $this->input->post( 'id' );
    $this->db->set('active_id', '0', FALSE );
    $this->db->where('id', $id );
    $this->db->update($this->table);    
    // print_r($this->db->last_query()); die();
    return true;
  }

  public function change_status()
  {
    $id = $this->input->post( 'id' );
    $this->db->set('active_id', '2', FALSE );
    $this->db->where('id', $id );
    $query = $this->db->update($this->table);    
    // print_r($this->db->last_query()); die();
    if( $query ) {
        return true;
    } else {
        return false;
    }
    
  }

    // units dropdown
    public  function  get_unit($filter = FALSE){
        $response = array();
        $this->db->select( '*' );
        $q = $this->db->get( 'units' );
        $response = $q->result_array();
        return $response;
    }

    

}
