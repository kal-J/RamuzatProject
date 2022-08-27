<?php
/**
 * Description of Billing Model
 * @author kalujja
 */

class Logs_model extends CI_Model
{

    private $single_contact;
    public function __construct()
    {
        $this->load->database();
    }

    public $table = 'fms_login_log';

    public function get($filter = false)
    {
        
        $this->db->select("al.*,concat(firstname,' ',lastname,' ',othernames )as member_name,module_name,al.date_created")
            ->from('fms_activity_log al')
            ->join('fms_user u', "al.user_id = u.id", "LEFT")
            ->join('fms_modules mod', "mod.id = al.moudle_id", "LEFT");
            $this->db->where('u.status !=9');
            $this->db->order_by('al.date_created','DESC');
        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            $this->db->where($filter);
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function get_login_log_list() {
        // alias ll is login log table.
        $this->db->select("ll.*,concat(firstname,' ',lastname,' ',othernames )as member_name")
        ->from('fms_login_log ll')
        ->join('fms_user u', "ll.user_id = u.id", "LEFT");
        $this->db->where('u.status !=9');
        $this->db->order_by('ll.login_time','DESC');
        $query = $this->db->get();

        return $query->result_array();
    }

}
