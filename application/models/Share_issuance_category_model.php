<?php
/**
 * Description of Share issuance category model
 *
 * @author Reagan
 */
class Share_issuance_category_model extends CI_Model {

    public function __construct() {
        $this->load->database();
        $this->load->library("session");
    }

    public function get($filter = FALSE) {
        $this->db->select('shic.*,shc.category,shc.description as cat_description,shi.issuance_name');
        $this->db->from('share_issuance_category shic');
        $this->db->join('share_category shc','shc.id=shic.category_id','left');
        $this->db->join('share_issuance shi','shi.id=shic.share_issuance_id','left');
        if(is_numeric($this->input->post('issuance_id'))){
        $this->db->where('shic.share_issuance_id=' . $this->input->post('issuance_id'));
        }
        if ($filter === FALSE) {
            $this->db->where_not_in('shic.status_id',$this->input->post('status_id'));
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)){
                $this->db->where('shic.id=' . $filter);
                $this->db->where_not_in('shic.status_id',0);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public  function  get_active_share_issuance_category($filter = FALSE){
        $response = array();
        $this->db->select( '*' );
        $this->db->where('status_id='. $filter);
        $q = $this->db->get( 'share_issuance_category' );
        $response = $q->result_array();
        return $response;
    }
    public  function  get_active_share_issuance_name($filter = FALSE){
        $response = array();
        $this->db->select( '*' );
        $this->db->where('status_id='. $filter);
        $q = $this->db->get( 'share_issuance' );
        $response = $q->result_array();
        return $response;
    }

     public  function  get_active_share_issuance_price(){
        $this->db->select( '*' );
        $this->db->where('status_id=1');
        $q = $this->db->get( 'share_issuance' );
        return $q->row_array();
    }

    public function set() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['tbl']);
        $data['status_id'] = '1';
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];

        $this->db->insert('share_issuance_category', $data);
        return $this->db->insert_id();
    }
	
    public function update() {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['id'];
        $data['status_id'] = '1';
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('share_issuance_category', $data);
    }
    
    public function deactivate() {
        $data = array(
            'status_id' =>$this->input->post('status_id'),            
            'modified_by' =>$_SESSION['id']
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('share_issuance_category', $data);
    }

    public function delete() {
        $data = array(
            'status_id' =>0,            
            'modified_by' =>$_SESSION['id']
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('share_issuance_category', $data);
    }
}
