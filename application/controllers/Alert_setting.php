<?php
/**
 * Description of alert settings
 *
 * @author @mbrose ogwang
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Alert_setting extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->library("num_format_helper");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model("Alert_setting_model");
        $this->load->model("repayment_schedule_model");
        
        $this->load->model("RolePrivilege_model");
        $this->load->model("member_model");
         $this->load->model("staff_model");
          $this->load->model("Client_loan_model");
        $this->load->library(array("form_validation", "helpers"));
        $this->data['privilege_list'] = $this->helpers->user_privileges(12, $_SESSION['staff_id']);
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } 
         
    }
    public function JsonList(){
        $data['data'] = $this->Alert_setting_model->get();
        
        echo json_encode($data);

    }
    
    public function get_alert_types(){
    $this->data['alert_types'] = $this->Alert_setting_model->get2();
         echo json_encode($this->data);

    }
     
     public function create() {
        $this->form_validation->set_rules('alert_method', 'Alert method should be selected', array('required'));
        $this->form_validation->set_rules('alert_type', 'Type of alert should be selected', array('required'));
        $this->form_validation->set_rules('number_of_days_to_duedate', 'Number of reminder must be selected', array('required'));
        $this->form_validation->set_rules('interval_of_reminder', 'Type of reminder must be selected', array('required'));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->shares_model->update()) {
                        $feedback['success'] = true;
                        $feedback['message'] = "Alert setting successfully updated";
                      
                } else {
                    $feedback['message'] = "There was a problem updating the alert setting, please try again or get in touch with the admin";
                }
            } else {
                
                $alert_setting= $this->Alert_setting_model->set();
                if (is_numeric($alert_setting)) {
                        $feedback['success'] = true;
                        $feedback['message'] = "Alert setting details successfully saved";
                 
                } else {
                    $feedback['message'] = "There was a problem saving the alert setting";
                   
                }
            }
        }
        echo json_encode($feedback);
    }
    //custom email sending 
    public function create2() {
        //$this->form_validation->set_rules('sender', 'Sender email should be entered', array('required'));
        $this->form_validation->set_rules('receiver', 'Receiver Email should be entered', array('required'));
        $this->form_validation->set_rules('subject', 'Subject should be entered', array('required'));
        $this->form_validation->set_rules('message', 'Message should be entered', array('required'));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->shares_model->update()) {
                        $feedback['success'] = true;
                        $feedback['message'] = "Email successfully updated";
                       
                } else {
                    $feedback['message'] = "There was a problem updating the Email, please try again or get in touch with the admin";
                   
                }
            } 
            else {
                if($_POST['receiver']==1){
                $receiver2=array();
                 
                $member_email_list= $this->Alert_setting_model->get_member_emails(); 

                foreach ($member_email_list as $value) {
                    
                    $receiver2[]=$value;
                   
                }
                }
               if($_POST['receiver']==2){
                 $receiver2=array();

                 $staff_email_list= $this->Alert_setting_model->get_staff_emails(); 
                  foreach ($staff_email_list as $value) {
                    $receiver2[]=$value;
                }
               
                }
           
               $post_data= array(
                 'receiver'=>$receiver2,
                 'subject' => $this->input->post('subject'),
                  'message' => $this->input->post('message'),
                  'date_created' => time(), 
                  'created_by' => $_SESSION['id'],
                  'message_id'=>$this->input->post('receiver'),
                  'alert_type_id'=>1
             );
              
                $post_data= $this->Alert_setting_model->set2_for_custom_emails($post_data);
                if ($post_data){
                    $this->send_custom_email();
                        $feedback['success'] = true;
                        $feedback['message'] = "Email details successfully saved";                 
                } else {
                    $feedback['message'] = "There was a problem saving the Email";
                }
            }
        }
        echo json_encode($feedback);
    }
 

function get_next_reminder_date()
{ 
     //alert scenarios 
     
    
    $data['alert_details']= $this->Alert_setting_model->get_alerts_tosend();  
    $all_data=array(); 
    $post_data=array();
    foreach($data['alert_details'] as $value){
   
    $week_before=1;
    $exact_due_date=2;
    $duedate_cal=3;
    $previously_sent=4;
    $alert_type=$value['alert_type'];
    $receiver2=array();
 
    $all_data[]=$value;
    
     $number_of_days_to_duedate=$value['number_of_days_to_duedate'];
     if($week_before==1){

      $week_before=date("Y-m-d", strtotime("+1 week"));
      $where='rs.repayment_date="'.$week_before.'"';
      $data['dueloan_details']=$this->repayment_schedule_model->get3($where);
       foreach($data['dueloan_details'] as $value){
         $receiver2[]=$value;
        }
        }

    if($exact_due_date==2){
      $exact_due_date=date('Y-m-d');
      $where='rs.repayment_date="'.$exact_due_date.'"';
      $data['dueloan_details']=$this->repayment_schedule_model->get3($where);
        foreach($data['dueloan_details'] as $value){
            $receiver2[]=$value;
        }
      }
      
      if($duedate_cal==3 &&($alert_type==2||$alert_type==3||$alert_type==4)){
        $duedate_cal=date('Y-m-d',strtotime("+$number_of_days_to_duedate days"));
        $where='rs.repayment_date="'.$duedate_cal.'"';
        $data['dueloan_details']=$this->repayment_schedule_model->get3($where);
        foreach($data['dueloan_details'] as $value){
            $receiver2[]=$value;
        }

      } 
     
  if($previously_sent==4 &&($alert_type==2||$alert_type==3||$alert_type==4)){
     
        $duedate_cal=date('Y-m-d',strtotime("+$number_of_days_to_duedate days"));
        $where='DATE(e.date_sent)>CURDATE() AND DATE(e.date_sent)="'.$duedate_cal.'" AND e.date_sent IS NOT NULL AND mgs_status=1';
        $data['dueloan_details']=$this->Alert_setting_model->get_previously_sent($where);
         foreach($data['dueloan_details'] as $value){
            $receiver2[]=$value;
 
        }
      }
 
    $subject=$alert_type==2?"Due loan installment":(
    $alert_type==3?"Due fees":(
    $alert_type==4?"Loan in arreas":""
    )
   );
   $message=$alert_type==2?"$subject is due":(
    $alert_type==3?"$subject is due":(
    $alert_type==4?"$subject is due":""
    )
   );


  $post_data[]= array(
                 'receiver'=>$receiver2,
                  'subject' =>$subject,
                  'message' =>$message,
                  'date_created' => time(), 
                  'created_by' => $_SESSION['id'],
                  'message_id'=>$alert_type,
                  'alert_type_id'=>$alert_type
             );
}
  
 $post_data= $this->Alert_setting_model->set2($post_data);
 


}
 

public function send_custom_email(){
    $this->data['message_details']= $this->Alert_setting_model->get_emails_to_send();
    if(empty($this->data['message_details'])){
         redirect('shares');
    }
    else{
    foreach ($this->data['message_details'] as $key => $value) {
         
         $contacts= $value['contact'];
         $message=$value['message'];
         $subject=$value['subject'];
         $branch_id=1;
         $message_id= $value['message_id'];
         
    }
   if(
       $this->helpers->send_multiple_email2($branch_id=1,$contacts,$message,$subject,$org_id=1)){
       $this->Alert_setting_model->update_sent_emails_status($message_id);
       
   }
    }

}

    
  
}
