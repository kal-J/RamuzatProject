<?php
/**
 * Description of Alert_setting_model
 *
 * @author Ambrose Ogwang
 */
class Alert_setting_model extends CI_Model {

    public function __construct() {
        $this->load->database();
          $this->single_contact = "
                (SELECT `user_id`, `mobile_number` FROM `fms_contact`
                WHERE `id` in (
                    SELECT MAX(`id`) from `fms_contact` WHERE `contact_type_id`=1 GROUP BY `user_id` 
                )
            )";

        $this->date_since_last_sent="(SELECT MAX(`date_sent`) as max_date_sent,member_id FROM `fms_emails` WHERE `alert_type_id`!=1 GROUP BY `member_id` 
                ) md";
    }
    //get
     public function get($filter= FALSE) {

        $this->db->select('*');
        $this->db->from('fms_alert_setting');
        $this->db->where('status_id !=','');
        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('status_id',$filter);

                $query = $this->db->get();
                return $query->row_array();
            }
             else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
} 
  public function get2($filter= FALSE) {
    $sql=$this->db->query("SELECT * FROM `fms_alert_types`");
    return $sql->result_array();
     
 }

 public function get_alerts_tosend($filter=false) {

        $this->db->select('*');
        $this->db->from('fms_alert_setting');
        $this->db->where('status_id !=','');
        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('status_id',$filter);

                $query = $this->db->get();
                return $query->result_array();
            }
             else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
}    
    //setting alert 
     public function set(){
           $data = array(
                'alert_method' =>$this->input->post('alert_method'), 
                'alert_type' => $this->input->post('alert_type'), 
                'number_of_days_to_duedate' => $this->input->post('number_of_days_to_duedate'), 
                'interval_of_reminder' => $this->input->post('interval_of_reminder'), 
                'date_created' => time(), 
                'created_by' => $_SESSION['id'],   
                'alert_id'=>$this->input->post('alert_id')
            );

          $this->db->insert('fms_alert_setting', $data);
          return $this->db->insert_id();
    }
    //custom  schedules the alert details.
     public function set2($post_data=[]){  

           foreach($post_data as $key => $values){
            foreach ($values['receiver'] as $key => $receiver) {
            $data['receiver']=$receiver['email'];
            $data['mobile_number']=$receiver['mobile_number'];
            $data['member_id']=$receiver['member_id'];
            $data['subject']=$values['subject'];
            $data['message']=$values['message'];
            $data['alert_type_id']=$values['alert_type_id'];
            $data['date_created']=$values['date_created'];
            $data['created_by']=$values['created_by'];
             $this->db->insert('fms_emails', $data);
            }
          
          }
      }
           public function set2_for_custom_emails($post_data=[]){  

            foreach ($post_data['receiver'] as $key => $value) {

            $post_data['receiver']=$value['email'];
            $post_data['mobile_number']=$value['mobile_number'];
            $post_data['member_id']=$value['member_id'];
           
             }
            $this->db->insert('fms_emails', $post_data);
            
            return $this->db->insert_id();
    }
    public function update($id){
        $data = array(
                'alert_method' =>$this->input->post('alert_method'), 
                'alert_type' => $this->input->post('alert_type'), 
                'number_of_reminder_in_days' => $this->input->post('number_of_reminder_in_days'), 
                'interval_of_reminder' => $this->input->post('interval_of_reminder'), 
                'modified_by' => $_SESSION['id'],
            );
        $this->db->where('share_account.id', $id);
        return $this->db->update('share_applications', $data);

    }
    public function get_staff_emails($id = false) {

            $this->db->select('u.email,mobile_number,m.id as member_id');
            $this->db->from('staff s');
            $this->db->join('user u', 'u.id=s.user_id', 'left');
            $this->db->join($this->single_contact . " c", "c.user_id = u.id", "left");
             $this->db->join('fms_member m', 'm.id = s.user_id', 'left');
            $this->db->where('u.email!=','');

            $query = $this->db->get();
            return $query->result_array();
        }  

    public function get_member_emails($filter = false){
        $this->db->distinct('cl.member_id');
        $this->db->select('email,mobile_number,m.id as member_id');
        $this->db->from('member m');
        $this->db->join('user u', 'u.id = m.user_id', 'left');
        $this->db->join($this->single_contact . " c", "c.user_id = u.id", "left");
        $this->db->where('m.status_id=1');
        $this->db->where('email!=','');
        $this->db->join('fms_client_loan cl','m.id=cl.member_id','left');
          if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        }
        else {
            if (is_numeric($filter)) {
                $this->db->where('m.status_id',$filter);

                $query = $this->db->get();
                return $query->row_array();
            }
             else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
         
    }

        //functions to get emails to be sent
     public function get_emails_to_send(){

            $this->db->select('receiver as contact,subject,message,message_id');
            $this->db->from('fms_emails');
            $this->db->where(array('status_id'=>1,'mgs_status'=>0));
            $query = $this->db->get();
            return $query->result_array();
        }  
    public function update_sent_emails_status($message_id){
         $data = array(
                'mgs_status' =>1, 
                'modified_by' => $_SESSION['id']
                
            );
        $this->db->where('fms_emails.message_id', $message_id);
        $this->db->update('fms_emails', $data);
        
    }
    //update the alert counter
    public function update_alert_counter($id){
        $modified_at=time();
        $modified_by=$_SESSION['id'];
        $sql="UPDATE fms_alert_setting SET alert_sent_count=alert_sent_count+1,
        modified_by=$modified_by,modified_at=$modified_at WHERE id=?";
        $this->db->query($sql, $id);
    }

    public function get_previously_sent($filter=false){
         $this->db->select('alert_type_id,e.member_id,md.member_id,receiver as email,mobile_number,max_date_sent');
        $this->db->from('fms_emails e');
        $this->db->join($this->date_since_last_sent,"md.member_id=e.member_id","left");
        $this->db->where('status_id !=','');
         $this->db->where('alert_type_id !=',1);
        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('status_id',$filter);

                $query = $this->db->get();
                return $query->row_array();
            }
             else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }


    }
  
  
 
    

     
    }


