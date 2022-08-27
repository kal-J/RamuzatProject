<?php
/**
 * Description of Billing Model
 * @author kalujja
 */

class Billing_model extends CI_Model
{

    private $single_contact;
    public function __construct()
    {
        $this->load->database();
        $this->single_contact = "
                (SELECT `user_id`, `mobile_number` FROM `fms_contact`
                WHERE `id` in (
                    SELECT MAX(`id`) from `fms_contact` WHERE `contact_type_id`=1 GROUP BY `user_id`
                )
            )";
    }

    public $table = 'fms_sms';

    public function get($filter = false)
    {
        $this->db->select('(SELECT cost FROM fms_sms_type where status_id=1 limit 1 ) AS cost');

        if(!empty($this->input->post('month'))) {
            // First day of the month.
            $start_date = $this->input->post('month') . '-01';
           // Last day of the month.
           $end_date =  date('Y-m-t', strtotime($start_date));
           
           // Get 00 hours timestamp
           $start_date = date('Y-m-d', strtotime($start_date . ' - 1 day')); 
           $start_date_timestamp = strtotime($start_date . ' midnight');
           $end_date = date('Y-m-d', strtotime($end_date . ' + 1 day'));
           $end_date_timestamp = strtotime($end_date . ' midnight') - 1;

            $this->db->select('client_no, t2.id AS member_id, mobile_number, concat(t3.firstname, " ", t3.lastname, " ",t3.othernames) AS member_name, (SELECT COUNT(id) from fms_sms WHERE member_id=t2.id AND date_created >='. $start_date_timestamp . ' AND date_created <= '. $end_date_timestamp .' ) as no_of_msgs');
        } else {
            $this->db->select('client_no, t2.id AS member_id, mobile_number, concat(t3.firstname, " ", t3.lastname, " ",t3.othernames) AS member_name, (SELECT COUNT(id) from fms_sms WHERE member_id=t2.id) as no_of_msgs');

        }
        
        $this->db->from('member t2')
            ->join('user t3', 't2.user_id = t3.id', 'LEFT')
            ->join($this->single_contact . " c", "c.user_id = t3.id", "left");
        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            $this->db->where($filter);
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function get_member_sms_list($member_id) {
        $this->db->select('*')->from($this->table);
        $this->db->select('(SELECT cost FROM fms_sms_type where status_id=1 limit 1 ) AS cost');
        $this->db->where('member_id=', $member_id);
        $query = $this->db->get();

        return $query->result_array();
    }

}
