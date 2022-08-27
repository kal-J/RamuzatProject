<?php
/**
 * This class helps to create the mode for the database operations
 */
class Signature_model extends CI_Model {

    Public function __construct() {
        parent :: __construct();
        $this->load->database();
        $this->current_state ="(SELECT user_id,signature,date_created FROM fms_user_signatures,created_by
                WHERE id in (
                    SELECT MAX(id) from fms_user_signatures GROUP BY user_id
                )
            )";
        $this->table = "fms_user_signatures";
    }
    public function get($filter = FALSE) {
        $this->db->select('fms_user_signatures.*,concat( concat(salutation,".")," ",firstname," ", lastname," ", othernames) AS member_name');
        $query = $this->db->from('fms_user_signatures');
        $query = $this->db->join('user', 'fms_user_signatures.user_id=user.id','left');
        $query = $this->db->join('staff', 'fms_user_signatures.created_by=staff.id','left');
        $this->db->order_by("fms_user_signatures.id", "desc");
     
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('fms_user_signatures.id=' .$filter); 
                $query = $this->db->get();
                return $query->row_array();
            }else{
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
              //  print_r($this->db->last_query()).die;
             }
        }
    } 
   
}
