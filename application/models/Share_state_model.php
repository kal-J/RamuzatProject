<?php
/**
 * Description of Share_state_model
 *
 * @author Reagan
 */
class Share_state_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }
    public function set($share_id=false) {
        if ($share_id==false) {
            $share_id=$this->input->post('id');
        }
        $data = array(
                'share_account_id' => $share_id,
                'state_id' =>$this->input->post('state_id'),
                'date_created' =>time(),
                'action_date' => date('Y-m-d'),
                'narrative' =>$this->input->post('narrative'),
                'created_by' =>$_SESSION['id']                
            );

        $this->db->insert('share_state', $data);
        return $this->db->insert_id();
    }
    // public function update($share_id=false) {
    //     //Action date conversation
    //     $sent_date = explode('-', $this->input->post('submission_date'),3);
    //     $action_date = count($sent_date)===3?($sent_date[2] . "-" . $sent_date[1] . "-" . $sent_date[0]):null;
    //     if ($share_id==false) {
    //         $share_id=$this->input->post('id');
    //     }
    //     $data = array(
    //             'action_date' => $action_date,
    //             'narrative' =>$this->input->post('narrative'),
    //             'modified_by' =>$_SESSION['id']                
    //         );
    //     $this->db->where('share_state.share_account_id', $share_id);
    //     $this->db->where('share_state.state_id', $this->input->post('state_id'));
    //     $query=$this->db->update('share_state', $data);
        
    //     if ($query) {
    //         return true;
    //     }else{
    //         return false;
    //     }
    // }
    public function get($share_id = FALSE) {
        $this->db->select('share_state.*,state_name,salutation,firstname,lastname, othernames');
        $query = $this->db->from('share_state')->join('staff','staff.id=share_state.created_by')->join('user','user.id=staff.user_id')->join('state','state.id=share_state.state_id');
        $this->db->order_by("action_date", "ASC");
        if ($share_id === FALSE) {
            $this->db->where('share_state.share_id',$this->input->post('share_id'));
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($share_id)) {
                $this->db->where('share_state.share_id',$share_id);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($share_id);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

}
