<?php

/**
 * Description of Helper
 *
 * @author allan modified by reagan
 */
#required for Africaistalking API
use AfricasTalking\SDK\AfricasTalking;
use HTTP\Request2; // Only when installed with PEAR

if (!defined('BASEPATH'))
    exit("No direct script access allowed");

class Helpers {

    protected $CI;  
    protected $org_details;

    public function __construct() {
        // Assign the CodeIgniter super-object
        $this->CI = & get_instance();
        $this->CI->load->model('RolePrivilege_model', '', TRUE);
        $this->CI->load->model('client_loan_model', '', TRUE);
        $this->CI->load->model('savings_account_model', '', TRUE);
        $this->CI->load->model('member_model', '', TRUE);
        $this->CI->load->model('Dashboard_model', '', TRUE);
        $this->CI->load->model('sms_model', '', TRUE);
        $this->CI->load->model('miscellaneous_model', '', TRUE);
        $this->CI->load->model('payment_engine_model', '', TRUE);
        $this->CI->load->model('organisation_model', '', TRUE);
        $this->CI->load->model('user_model', '', TRUE);
        $this->org_details = $this->CI->organisation_model->get(1);
    }

    public function dynamic_script_tags($js = false, $css = false) {

        $requiredjs = "";
        $requiredcss = "";
        $assets_folder = "myassets/";
        if (!($js === false)) {
            foreach ($js as $key => $link) {
                $requiredjs .= $this->CI->template->javascript->add(base_url($assets_folder . "js/" . $link));
            }
        }
        if (!($css === false)) {
            foreach ($css as $key => $link) {
                $requiredcss .= $this->CI->template->stylesheet->add(base_url($assets_folder . "css/" . $link), array('media' => 'all'));
            }
        }

        return json_encode($requiredjs . '' . $requiredcss);
    }

    public function user_privileges($module_id, $staff_id) {
        return $this->CI->RolePrivilege_model->get_user_privileges($module_id, $staff_id);
    }

    public function org_access_module($module_id, $org_id) {
        return $this->CI->organisation_model->get_module_access($module_id, $org_id);
    }

    public function yr_transformer($form_date) {
        $exploded_date = explode('-', $form_date, 3);
        $new_date = count($exploded_date) === 3 ? ($exploded_date[2] . "-" . $exploded_date[1] . "-" . $exploded_date[0]) : null;
        return preg_replace("/^19/", "20", $new_date);
    }
    public function get_date_time($date){ 
        if ($date!=null) {
            $t = microtime(true);
            $micro = sprintf("%06d",($t - floor($t)) * 1000000);
            $d2=new DateTime($date." ".date('H:i:s.'.$micro,$t));
            return $d2->format("Y-m-d H:i:s.u");
        }else{
            return null;
        }
        
    }

    public function pure_phone_no($messy_phone_no = "") {
        return preg_replace(["/\s/", "/-/"], "", $messy_phone_no);
    }

    public function valid_email($email) {
        if (preg_match('/^((?=[A-Z0-9][A-Z0-9@._%+-]{5,253}+$)[A-Z0-9._%+-]{1,64}+@(?:(?=[A-Z0-9-]{1,63}+\.)[A-Z0-9]++(?:-[A-Z0-9]++)*+\.){1,8}+[A-Z]{2,63}+)$/', $email)) {
            return TRUE;
        }
        return FALSE;
    }

    public function extract_date_time($date_time_string, $return_format = "U") {
        $date_format = "d/m/Y" . (strlen($date_time_string) > 10 ? " H:i:s" : "");
        $date_time_obj = DateTime::createFromFormat($date_format, $date_time_string);
        return $date_time_obj->format($return_format);
    }

    public function extract_date_time_hyphen($date_time_string, $return_format = "U") {
        $date_format = "Y-m-d" . (strlen($date_time_string) > 10 ? " H:i:s" : "");
        $date_time_obj = DateTime::createFromFormat($date_format, $date_time_string);
        return $date_time_obj->format($return_format);
    }
     public function extract_date_time_dot($date_time_string, $return_format = "U") {
        $date_format = "d.m.Y" . (strlen($date_time_string) > 10 ? " H:i:s" : "");
        $date_time_obj = DateTime::createFromFormat($date_format, $date_time_string);
        return $date_time_obj->format($return_format);
    }

        
    public function get_graph_periods($end_date, $start_date) {
        $time_format = " H:i:s";
        $begin = DateTime::createFromFormat('Y-m-d'.$time_format, $start_date." 00:00:00");
        $end = DateTime::createFromFormat('Y-m-d'.$time_format, $end_date." 23:59:59");
        //arrays with data for the past i days/months
        $graph_period_dates = $categories = array("date_range" => $begin->format("j M, y") . " - " . $end->format('j M, y'));
        
        $interval = new DateInterval('P1D');

        //$period = new DatePeriod($begin, $interval, $end);
        $period = new DatePeriod($begin, $interval, $end);
        //$period = new DatePeriod($begin, $interval, $end->add($interval));


        $days = iterator_count($period);
        $period_dates = iterator_to_array($period);

        $period_dates1 = [];
        //if days are 7 or less
        
        if ($days == 0 || $days < 13) {
            foreach ($period_dates as $period_date) {
                $period_dates1[] = ["start_obj"=>$period_date, "end_obj"=>$period_date];
                $categories[] = $period_date->format("l, j");
                //$categories[] = $period_date->format("D, j/n");
            }
        } elseif ($days > 12 && $days < 85) {
            /* split the days into weeks
             * generate an array holding the start and end dates of the given period
             */

            $period_dates1 = [];
            $index = 0;
            $period_dates1[$index]['start_obj'] = $begin;
            if ($begin->format('N') == 7) {
                $period_dates1[$index++]['end_obj'] = $start_date;
            }
            for ($i = $index; $i < count($period_dates); $i++) {
                $period_date = $period_dates[$i];
                if ($period_date->format('N') == 1) {
                    $period_dates1[$index]['start_obj'] = $period_date;
                }
                if ($period_date->format('N') == 7) {
                    $period_dates1[$index++]['end_obj'] = $period_date;
                }
            }

            $period_dates1[$index]['end_obj'] = $end;

            // if ($end->format('N') == 1) {
            //     $period_dates1[$index]['start_obj'] = $end;
            // }
            foreach ($period_dates1 as $week) {
                if(isset($week['start_obj'])){
                $start_obj = $week['start_obj'];
                $end_obj = $week['end_obj'];
                $categories[] = $start_obj->format('j/M' ) . "-" . $end_obj->format('j/M');
                }
            }
        } elseif ($days > 84) {
            /* split the days into months
             * generate an array holding the start and end dates of the given period */
            $period_dates1 = [];
            $index = 0;
            $period_dates1[$index]['start_obj'] = $begin;
            if ($begin->format('j') == $begin->format('t')) {
                $period_dates1[$index++]['end_obj'] = $begin;
            }
            for ($i = $index; $i < count($period_dates); $i++) {
                $period_date = $period_dates[$i];
                if ($period_date->format('j') == 1) {
                    $period_dates1[$index]['start_obj'] = $period_date;
                }
                if ($period_date->format('j') == $period_date->format('t')) {
                    $period_dates1[$index++]['end_obj'] = $period_date;
                }
            }
            $period_dates1[$index]['end_obj'] = $end;
            if ($end->format('j') == $end->format('t')) {
                $period_dates1[$index]['start_obj'] = $period_dates1[$index]['end_obj'] = $end;
            }
            foreach ($period_dates1 as $period_date_arr) {
                $end_obj = $period_date_arr['end_obj'];
                $categories[] = $end_obj->format('M/Y');
            }

        }
        if (!empty($graph_period_dates)) {
            $graph_period_dates['period_dates'] = $period_dates1;
            $graph_period_dates['xAxis']['categories'] = $categories;
            return $graph_period_dates;
        } else{
            return false;
        }
    }

    public function upload_file($location, $max_size = 1024, $allowed_types = "gif|jpg|jpeg|png|pdf") {
        $config['upload_path'] = APPPATH . "../uploads/$location/";
        $config['allowed_types'] = $allowed_types;
        $config['max_size'] = $max_size;
        $config['max_filename'] = 120;
        $config['overwrite'] = true;
        $config['file_ext_tolower'] = true;
        $config['file_name'] = $_FILES['file_attachment']['name'];

        //$upload_feedback = [];
        $this->CI->load->library('upload', $config);
        //if the folder doesn't exist
        if (!is_dir($config["upload_path"])) {
            mkdir($config["upload_path"], 0777, true);
        }
        if (!$this->CI->upload->do_upload('file_attachment')) {
            //$upload_feedback['error'] = array('error' => $this->upload->display_errors());
            //return false;
        } else {
            return $this->CI->upload->data('file_name');
        }
    }
    public function notification($id,$message,$loan=True){
        $sent_data=$this->client_details($id,$loan);        
        $sent_data['message']=$message;
        $contacts=$this->CI->member_model->get_member_contact($sent_data['member_id']);
        if (!empty($contacts) && array_key_exists('mobile_number', $contacts)) {
            #if the number starts with a plus mostly likely is in an international format else we append the domestic international code
            if (preg_match("/^[\+]+[0-9]{12,12}$/", $contacts['mobile_number'])) {
                $mobile_number = $contacts['mobile_number'];
            } elseif (preg_match("/^[07]+[0-9]{9,10}$/", $contacts['mobile_number'])) {
                $mobile_number = '+256' . substr($contacts['mobile_number'], -9);
            } else {
                $mobile_number = '';
            }
            # send SMS
            if (!empty($mobile_number)) {
                if ($this->send_sms($mobile_number, $message)) {
                    $this->CI->sms_model->set($sent_data);
                    return " and client notified";
                } else {
                    return " but client couldn't be notified";
                }
            } else {
                return " but client couldn't be notified, invalid contact";
            }
        } else {
            return " but client couldn't be notified, no contact";
        }
    }

    private function client_details($id, $loan) {
        $sent_data = array();
        if ($loan) {
            $module_data = $this->CI->client_loan_model->get_client_data($id);
            $sent_data['message_type'] = 'Loan';
            $sent_data['ref_no'] = $module_data['loan_no'];
        } else {#for now the savings account use this
            $module_data = $this->CI->savings_account_model->get_for_payments($id);
            $sent_data['message_type'] = 'Savings';
            $sent_data['ref_no'] = $module_data['account_no'];
        }
        $sent_data['member_id'] = $module_data['member_id'];

        return $sent_data;
    }

    // Public function send_sms($recipients, $message) {
    //     $username = "efms";
    //     $apiKey = "1d765fbd1de29641704ed35a7ec9e49e0089ede6b11d969e3ca724d098e52aa1"; 
    //     $AT = new AfricasTalking($username, $apiKey);
    //     $sms = $AT->sms();
    //     $recipients = $recipients;
    //     $message = $message;

    //     // Set your shortCode or senderId
    //     #$from       = "GMTefms";#myShortCode or mySenderId

    //     try {
    //         $result = $sms->send([
    //             'to' => $recipients,
    //             'message' => $message
    //             #'from'    => $from
    //         ]);

    //         return True;
    //     } catch (Exception $e) {
    //         $error = "Error: " . $e->getMessage();
    //         return False;
    //     }
    // }

   public function send_sms($mobile_number, $message)
    {       $api_key=$this->CI->payment_engine_model->get_sms(1);
            $request = new HTTP_Request2();
            $request->setUrl('https://api.textug.com/sms/send');
            $request->setMethod(HTTP_Request2::METHOD_POST);
            $request->setConfig(array(
              'follow_redirects' => TRUE
            ));
            $headers = array(
               'api-key' => $api_key['api_key'],
            );
            $request->setHeader($headers);
            
            $request->setBody(json_encode(array(
                 "recipients"=> $mobile_number,
                 "message"=> $message
            )));
            try {
              $response = $request->send();
              if ($response->getStatus() == 200) {
                return true;
              }
              else {
                return false;
              }
            }
            catch(HTTP_Request2_Exception $e) {
              return false;
            }
    }

    //end of function

    public function send_email_to_credit_officer($email, $message,$subject) {
        if (!empty($result=$this->CI->miscellaneous_model->check_org_module(24,1))) {
            $this->CI->load->library('email');
            $this->branch_details = $this->CI->organisation_model->get_org($_SESSION['branch_id'],$_SESSION['organisation_id']);
            $this->org_site_url = base_url();
            if (empty($this->org_details['organisation_logo'])) {
                $logo_url = base_url('uploads/organisation_1/logo/efinance.png');
            } else {
                $logo_url = base_url("uploads/organisation_1/logo/" . $this->org_details['organisation_logo']);
            }
            $this->message_template = '<div style="width:620px; margin:auto;text-align:justify;">
                <div style="width:49%%; float:left;">
                    <a href="' . $this->org_site_url . '"><img width="100%%" src="' . $logo_url . '" alt="' . $this->org_details['name'] . '"></a>
                    </div>
                    <div style="clear:both;"></div>
                    <div style="border-bottom: 2px solid #8DC53F;"></div>
                    %s
                    <div style="clear:both;"></div>
                    <div style=" border-bottom: 2px solid #8DC53F;"></div>
                    <p>Best regards.</p>
                    <p>Management</p>
                    
                </div>';
                //'</a><br/>Web: <a href="' . $this->org_site_url . '" title="System">' . $this->org_site_url . '</a></p>'

            $the_message = "<p>" .$message . "</p>";

            $final_msg = "You are receiving this email because you are a member of ".$this->org_details['name'].". If you are no longer interested, please reply to this email.";
            $email_message = sprintf($this->message_template, $the_message, $final_msg);
            $this->CI->email->set_mailtype("html");
            $this->CI->email->from('support@efinanci.com','Management');
            $this->CI->email->reply_to($this->branch_details['email_address'], $this->org_details['name'] . " - System Support ");
            $this->CI->email->to($email);
            $this->CI->email->subject($subject);
            $this->CI->email->message($email_message);
            return $this->CI->email->send();
        }else {
            return true;
        }

    }

    // password reset send email
        public function send_password_reset_email($email, $message,$subject) {
        if (!empty($result=$this->CI->miscellaneous_model->check_org_module(24,1))) {
            $this->CI->load->library('email');
            $this->branch_details = $this->CI->organisation_model->get_org($_SESSION['branch_id'],$_SESSION['organisation_id']);
            $this->org_site_url = base_url();
            if (empty($this->org_details['organisation_logo'])) {
                $logo_url = base_url('uploads/organisation_1/logo/efinance.png');
            } else {
                $logo_url = base_url("uploads/organisation_1/logo/" . $this->org_details['organisation_logo']);
            }
            $this->message_template = '<div style="width:620px; margin:auto;text-align:justify;">
                <div style="width:49%%; float:left;">
                    <a href="' . $this->org_site_url . '"><img width="100%%" src="' . $logo_url . '" alt="' . $this->org_details['name'] . '"></a>
                    </div>
                    <div style="clear:both;"></div>
                    <div style="border-bottom: 2px solid #8DC53F;"></div>
                    %s
                    <div style="clear:both;"></div>
                    <div style=" border-bottom: 2px solid #8DC53F;"></div>
                    <p>Best regards.</p>
                    <p>Management</p>
                    
                </div>';
                //'</a><br/>Web: <a href="' . $this->org_site_url . '" title="System">' . $this->org_site_url . '</a></p>'

            $the_message = "<p>" .$message . "</p>";

            $final_msg = "You are receiving this email because an account under ".$this->org_details['name'].". , has requested for a password reset";
            $email_message = sprintf($this->message_template, $the_message, $final_msg);
            $this->CI->email->set_mailtype("html");
            $this->CI->email->from('support@efinanci.com','Management');
            $this->CI->email->reply_to($this->branch_details['email_address'], $this->org_details['name'] . " - System Support ");
            $this->CI->email->to($email);
            $this->CI->email->subject($subject);
            $this->CI->email->message($email_message);
            return $this->CI->email->send();
        }else {
            return true;
        }

    }

    public function send_email($id, $message, $loan = True, $title = '') {
        if (!empty($result=$this->CI->miscellaneous_model->check_org_module(24,1))) {
        $sent_data = $this->client_details($id, $loan);
        if (is_numeric($sent_data['member_id'])) {
            $contacts = $this->CI->member_model->get_member_contact($sent_data['member_id']); 
            if (isset($contacts['email']) && $contacts['email'] != '') {
                $pattern = "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix";
                if (preg_match($pattern, $contacts['email'])) {
                    $the_message = "Dear " . ucfirst(strtolower($contacts['firstname'])). ",<br><br>".$message;
                      //$this->CI->load->library('VerifyEmail');
                      //if ($this->CI->verifyemail->check($contacts['email'])==true) {
                        //$this->send_multiple_email($contacts['branch_id'],$contacts['email'],$the_message);
                      //}
                     $this->send_multiple_email2($contacts['branch_id'],$contacts['email'],$the_message, $title);
                }else{ return false;}
            } else { return false; }
        }else{
            return false;
        }
        }else{
            return true;
      }
    }

    public function send_multiple_email($branch_id,$contacts,$message,$subject=false,$org_id=1){
        if (!empty($result=$this->CI->miscellaneous_model->check_org_module(24,$org_id))) {
        $this->CI->load->library('email');
        if ($subject === false) {
            $subject=$this->org_details['name'] . ' - Info';
        }
        $this->branch_details = $this->CI->organisation_model->get_org($branch_id,$org_id);
        if (isset($this->branch_details['email_address']) && $this->branch_details['email_address'] != '') {
            $this->org_site_url = base_url();
            if (empty($this->org_details['organisation_logo'])) {
                $logo_url = base_url('uploads/organisation_1/logo/efinance.png');
            } else {
                $logo_url = base_url("uploads/organisation_1/logo/" . $this->org_details['organisation_logo']);
            }
            $this->message_template = '<div style="width:620px; margin:auto;text-align:justify;">
                <div style="width:49%%; float:left;">
                    <a href="' . $this->org_site_url . '"><img width="100%%" src="' . $logo_url . '" alt="' . $this->org_details['name'] . '"></a>
                    </div>
                    <div style="clear:both;"></div>
                    <div style="border-bottom: 2px solid #8DC53F;"></div>
                    %s
                    <div style="clear:both;"></div>
                    <div style=" border-bottom: 2px solid #8DC53F;"></div>
                    <p>Best regards.</p>
                    <p>Management</p>
                    <p style="margin-bottom:4px;margin-top: 12px;"><strong>' . $this->org_details['name'] . '</strong><br/> 
                    ' . $this->branch_details['physical_address'] . '<br/>
                    ' . $this->branch_details['postal_address'] . '</p>
                    <p style="margin-bottom:4px;margin-top: 12px;">
                    Tel: ' . $this->branch_details['office_phone'] . '<br/>
                    Email: <a href="mailto:' . $this->branch_details['email_address'] . '" title="Send email">' . $this->branch_details['email_address'] .'</p>
                    <p style="font-size:9px;">%s</p>
                </div>';
                //'</a><br/>Web: <a href="' . $this->org_site_url . '" title="System">' . $this->org_site_url . '</a></p>'

            $the_message = "<p>" .$message . "<br><br>For enquiries, Please contact us on " . $this->branch_details['office_phone'] ."</p>";

            $final_msg = "You are receiving this email because you are a member of ".$this->org_details['name'].". If you are no longer interested, please reply to this email.";
            $email_message = sprintf($this->message_template, $the_message, $final_msg);
            $this->CI->email->set_mailtype("html");
            $this->CI->email->from('support@efinanci.com','Management');
            $this->CI->email->reply_to($this->branch_details['email_address'], $this->org_details['name'] . " - System Support ");
            $this->CI->email->to($contacts);
            $this->CI->email->subject($subject);
            $this->CI->email->message($email_message);
            return $this->CI->email->send();

        } else {//Branch/company email not set
            return false;
        }
        }else{
            return true;
      }
    }
    //send multiple email2
    public function send_multiple_email2($branch_id,$contacts,$message,$subject=false,$org_id=1){
        if (!empty($result=$this->CI->miscellaneous_model->check_org_module(24,$org_id))) {
        $this->CI->load->library('email');
        if ($subject === false) {
            $subject=$this->org_details['name'] . ' - Info';
        }
        elseif($subject !=''){
            $subject=$subject;
        }
        $this->branch_details = $this->CI->organisation_model->get_org($branch_id,$org_id);
        if (isset($this->branch_details['email_address']) && $this->branch_details['email_address'] != '') {
            $this->org_site_url = base_url();
            if (empty($this->org_details['organisation_logo'])) {
                $logo_url = base_url('uploads/organisation_1/logo/efinance.png');
                
            } else {
                $logo_url = base_url("uploads/organisation_1/logo/" . $this->org_details['organisation_logo']);
              
            }
                 
              $this->message_template = '
              <div style="width:100%%;background-color:#fafbfc;height:100%%;">
             <div style="width:620px; margin:auto;text-align:justify;margin-left:1px solid #CCC;background-color:#ffffff;font-family: arial,sans-serif;
              font-size:24px;color:#102231;">&nbsp;
                   <div align="center" style="background-color:#fff;padding:0px; width:100%%;padding:1px;"style="float:both;">
                           <a href="' .$this->org_site_url . '"><img width="40%%"  src="' . $logo_url . '" alt="' . $this->org_details['name'] . '"></a>
                     
                    </div><br>
                  
                     <table  width="100%%" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#3498db;padding:30px 40px 20px 40px">
                    <tbody style="background-color:#3498db;text-align:center;">
                    <tr>
                    <td>
                    <div style="width:80%%;background-color:#3498db;color:#fff;text-align:center;padding:0px;line-height: 0px;font-weight:" >
                      <h3 style="font-size:19px;padding:5px;font-family:sans-serif;line-height:middle;text-align:center;color:#ffffff;">
                       ' .$subject . '
                       </h3>
                    </div>
                      </div>
                    </tbody>
                    </table>
                     
                     <div style="width:620px; margin:auto;text-align:justify;margin-left:1px solid #CCC;background-color:#ffffff;font-family: arial,sans-serif;color:#102231;padding:1px;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);font-size:18px;">
                    
                   <table class="col-620" width="100%%" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#fff;padding:30px 40px 20px 40px">
                    <tbody>
                    <tr>
                    <td>
                     <p style="color:#525c65;font-size:18px;font-family: arial,sans-serif">
                     %s
                     </p>
                      
                     <p>Best regards.</p> 
                     <p>Management</p>
                      </td>
                      </tr>
                  
                    </tbody>
                    </table>
                    </div>


                     <div style="width:620px; margin:auto;text-align:justify;margin-left:1px solid #CCC;background-color:#ffffff;font-family: arial,sans-serif;color:#102231;padding:1px;box-shadow: 0 4px 8px 0 rgba(0, 0, 0.3, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);font-size:18px;">
                    
                    <table class="col-600" width="100%%" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color: rgb(245, 247, 249);padding:30px;">
                    <tbody>
                    <tr>
                    <td style="font-family: arial,sans-serif;font-size:16px;color:#102231;line-height:20px;">
                     
                   <p style="margin-bottom:4px;margin-top: 18px;"><strong>' . $this->org_details['name'] . '</strong><br/> 
                    ' . $this->branch_details['physical_address'] . '<br/>
                    ' . $this->branch_details['postal_address'] . '</p>
                    <p style="margin-bottom:4px;margin-top: 12px;">
                    Tel: ' . $this->branch_details['office_phone'] . '<br/>
                    Email: <a href="mailto:' . $this->branch_details['email_address'] . '" title="Send email">' . $this->branch_details['email_address'] .'</p>
                   </td>
                   </tr>
                    </tbody>
                    </table>
                    </div>

                      <div style="width:620px; margin:auto;text-align:justify;margin-left:1px solid #CCC;background-color:#ffffff;font-family: arial,sans-serif;color:#102231;padding:1px;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);font-size:18px;">
                    
                    <table class="col-600" width="100%%" border="0" align="center" cellpadding="0" cellspacing="0" style=" ;padding:30px;color:#fff;background-color: rgb(125, 151, 173);">
                    <tbody>
                    <tr>
                    <td style="font-family: arial,sans-serif;font-size:12px;color:#fff;">
                     
                    <p style="font-size:9px;">%s</p>
                   </td>
                   </tr>
                    </tbody>
                    </table>
                    </div>
                      
                   </div>
                </div>';
                 $the_message = "<p>" .$message . "<br><br>For enquiries, Please contact us on " . $this->branch_details['office_phone'] ."</p>";

                $final_msg = "You are receiving this email because you are a member of ".$this->org_details['name'].". If you are no longer interested, please reply to this email.";
            
              
 
            $email_message = sprintf($this->message_template, $the_message,$final_msg);
            $this->CI->email->set_mailtype("html");
            $this->CI->email->from('support@efinanci.com','Management');
            $this->CI->email->reply_to($this->branch_details['email_address'], $this->org_details['name'] . " - System Support ");
            $this->CI->email->to($contacts);
            $this->CI->email->subject($subject);
            $this->CI->email->message($email_message);
            return $this->CI->email->send();

        } else {//Branch/company email not set
            return false;
        }
        }else{
            return true;
      }
    }

    public function deduct_charges($client_loan_id, $charge_trigger_id, $savings_account = false,$transaction_date=false, $unique_id = false) {

        $this->CI->load->model('loan_attached_saving_accounts_model');
        $this->CI->load->model('applied_loan_fee_model');
        $this->CI->load->model('Loan_guarantor_model');
        $this->CI->load->model('transaction_model');
        #$charge_trigger_id = array('1', '3', '4', '1');
        $where = "a.client_loan_id=" . $client_loan_id . " AND a.paid_or_not=0 AND b.chargetrigger_id IN ( '" . implode("', '", $charge_trigger_id) . "' )";
        $attached_fees = $this->CI->applied_loan_fee_model->get($where);

        if($savings_account != false) {
            $attached_savings_accounts = array($savings_account);
        } else {
            $attached_savings_accounts = $this->CI->loan_attached_saving_accounts_model->get('a.loan_id=' . $client_loan_id);
        }

        foreach ($attached_fees as $key => $value) {//loop for attached fees
            foreach ($attached_savings_accounts as $key => $savings_account) {//loop for attached a/c
                //fecting the details of a savings account
                $savings_data[$key] = $this->CI->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $savings_account['saving_account_id']);
                $current_balance = $savings_data[$key]['cash_bal'];
                if ($current_balance >= $value['amount']) {
                    #deduct the money
                    $deduction_data['amount'] = $value['amount'];
                    if ($transaction_date!=false) {
                    $deduction_data['transaction_date'] = $transaction_date;
                    }
                    $deduction_data['account_no_id'] = $savings_account['saving_account_id'];
                    $deduction_data['narrative'] = 'Payment deduction made to clear ' . ucfirst($value['feename']) . " for your loan";
                    $transaction_data = $this->CI->transaction_model->deduct_savings($deduction_data, $unique_id);

                    if (is_array($transaction_data)) {
                        $charge_payment_data['account_no_id'] = $savings_account['saving_account_id'];
                        $charge_payment_data['comment'] = 'Payment  for ' . $value['feename'];
                        $charge_payment_data['transaction_no'] = $transaction_data['transaction_no'];
                        $charge_payment_data['transaction_id'] = $transaction_data['transaction_id'];
                        $charge_payment_data['client_loan_id'] = $client_loan_id;
                        $charge_payment_data['charge_amount'] = $value['amount'];
                        $charge_payment_data['feename'] = $value['feename'];
                        $charge_payment_data['income_account_id'] = $value['income_account_id'];
                        $this->charges_journal_transaction($charge_payment_data, $unique_id);
                       
                        $this->CI->applied_loan_fee_model->mark_charge_paid($value['id'], $unique_id);
                        $message = "Payment of amount " . round($value['amount'], 2) . "/= has been made from your account " . $savings_data[$key]['account_no'] . " today " . date('d-m-Y H:i:s') . " to clear <strong>" . ucfirst($value['feename']) . "</strong> for your loan application";
                        $this->send_email($savings_account['saving_account_id'],$message, false);
                        #check for the sms module
                        if (!empty($result = $this->CI->miscellaneous_model->check_org_module(22, 1))) {
                            $this->notification($savings_account['saving_account_id'], $message, false);
                        }
                        break; //To avoid double payment
                    } else {
                        
                    }
                }
            }//end of loop for a/c   
        }//end of attached fees loop
    }

    public function deduct_charges2($client_loan_id, $charge_trigger_id, $savings_account_id,$transaction_date=false, $unique_id = false) {

        $this->CI->load->model('loan_attached_saving_accounts_model');
        $this->CI->load->model('applied_loan_fee_model');
        $this->CI->load->model('Loan_guarantor_model');
        $this->CI->load->model('transaction_model');
        #$charge_trigger_id = array('1', '3', '4', '1');
        $where = "a.client_loan_id=" . $client_loan_id . " AND a.paid_or_not=0 AND b.chargetrigger_id IN ( '" . implode("', '", $charge_trigger_id) . "' )";
        $attached_fees = $this->CI->applied_loan_fee_model->get($where);
        foreach ($attached_fees as $key => $value) {//loop for attached fees
                $savings_data = $this->CI->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $savings_account_id);
                $current_balance = $savings_data['cash_bal'];
                if ($current_balance >= $value['amount']) {
                    #deduct the money
                    $deduction_data['amount'] = $value['amount'];
                    if ($transaction_date!=false) {
                    $deduction_data['transaction_date'] = $transaction_date;
                    }
                    $deduction_data['account_no_id'] = $savings_account_id;
                    $deduction_data['narrative'] = 'Payment deduction made to clear ' . ucfirst($value['feename']) . " for your loan";
                    $transaction_data = $this->CI->transaction_model->deduct_savings($deduction_data, $unique_id);

                    if (is_array($transaction_data)) {
                        $charge_payment_data['account_no_id'] = $savings_account_id;
                        $charge_payment_data['comment'] = 'Payment  for ' . $value['feename'];
                        $charge_payment_data['transaction_no'] = $transaction_data['transaction_no'];
                        $charge_payment_data['transaction_id'] = $transaction_data['transaction_id'];
                        $charge_payment_data['client_loan_id'] = $client_loan_id;
                        $charge_payment_data['charge_amount'] = $value['amount'];
                        $charge_payment_data['feename'] = $value['feename'];
                        $charge_payment_data['income_account_id'] = $value['income_account_id'];
                        $this->charges_journal_transaction($charge_payment_data, $unique_id);
                       
                        $this->CI->applied_loan_fee_model->mark_charge_paid($value['id'], $unique_id);
                        $message = "Payment of amount " . round($value['amount'], 2) . "/= has been made from your account " . $savings_data['account_no'] . " today " . date('d-m-Y H:i:s') . " to clear <strong>" . ucfirst($value['feename']) . "</strong> for your loan application";
                        $this->send_email($savings_account_id,$message, false);
                        #check for the sms module
                        if (!empty($result = $this->CI->miscellaneous_model->check_org_module(22, 1))) {
                            $this->notification($savings_account_id, $message, false);
                        }
                       // break; //To avoid double payment
                    } else {
                        
                    }
                }
     
        }//end of attached fees loop
    }

    public function charges_journal_transaction($transaction_data, $unique_id = false) {

        $this->CI->load->model('journal_transaction_model');
        $this->CI->load->model('savings_account_model');
        $this->CI->load->model('DepositProduct_model');

        if ($this->CI->input->post('application_date') != NULL && $this->CI->input->post('application_date') != '') {
            $transaction_date = $this->CI->input->post('application_date');
        } elseif ($this->CI->input->post('action_date') != NULL && $this->CI->input->post('action_date') != '') {
            $transaction_date = $this->CI->input->post('action_date');
        } elseif ($this->CI->input->post('transaction_date') != NULL && $this->CI->input->post('transaction_date') != '') {
            $transaction_date = $this->CI->input->post('transaction_date');
        } else {
            $transaction_date = date('d-m-Y');
        }
        $savings_account = $this->CI->savings_account_model->get_for_payments($transaction_data['account_no_id']);
        
        $client_loan = $this->CI->client_loan_model->get_client_data($transaction_data['client_loan_id']);

        //then we prepare the journal transaction lines
        if (!empty($transaction_data) && !empty($savings_account) && !empty($client_loan)) {
            $this->CI->load->model('accounts_model');
            $this->CI->load->model('transactionChannel_model');
            $this->CI->load->model('journal_transaction_line_model');

            $data = [
                'transaction_date' => $transaction_date,
                'description' => $transaction_data['comment'],
                'ref_no' => $transaction_data['transaction_no'],
                'ref_id' => $transaction_data['transaction_id'],
                'status_id' => 1,
                'journal_type_id' => 28,
                'unique_id' => $unique_id
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->CI->journal_transaction_model->set($data);
            unset($data);

            $savings_product_details = $this->CI->DepositProduct_model->get_products($savings_account['deposit_Product_id']);

            $debit_or_credit2 = $this->CI->accounts_model->get_normal_side($transaction_data['income_account_id']);
            $debit_or_credit1 = $this->CI->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);

            //if charges have been received
            $data[0] = [
                $debit_or_credit1 => $transaction_data['charge_amount'],
                'transaction_date' => $transaction_date,
                'reference_no' => $transaction_data['transaction_no'],
                'reference_id' => $transaction_data['transaction_id'],
                'narrative' => ucfirst($transaction_data['feename']) . " charge on loan at " . $transaction_date,
                'account_id' => $savings_product_details['savings_liability_account_id'],
                'status_id' => 1,
                'unique_id' => $unique_id
            ];
            $data[1] = [
                $debit_or_credit2 => $transaction_data['charge_amount'],
                'transaction_date' => $transaction_date,
                'reference_no' => $transaction_data['transaction_no'],
                'reference_id' => $transaction_data['transaction_id'],
                'narrative' => ucfirst($transaction_data['feename']) . " charge on loan at " . $transaction_date,
                'account_id' => $transaction_data['income_account_id'],
                'status_id' => 1,
                'unique_id' => $unique_id
            ];
            $this->CI->journal_transaction_line_model->set($journal_transaction_id, $data);
        }
    }
    public function check_acc_balance($account_id=false,$amount=false){
        if($account_id === false){
            $account_id = $this->security->xss_clean($this->input->post("account_id"));
        }
        if($amount === false){
        $amount =$this->input->post('amount');
        }
        $response = ['success'=>$this->account_balance($account_id,$amount)];
        if (!$response['success']) {
            echo json_encode($response['success']);
        }else{
            echo json_encode($response['success']);  
        }
    }
    public function account_balance($account_id,$amount){
        $this->CI->load->model('accounts_model');
        $this->CI->load->model('reports_model');
        $debit_or_credit = $this->CI->accounts_model->get_normal_side($account_id);
        $account_details =$this->CI->accounts_model->get_account_details($account_id);
        $normal_side=($debit_or_credit=='credit_amount')?2:1;
        $balance = $this->CI->reports_model->get_acc_balance($account_id,$normal_side);
         
        if ($amount>$balance['amount']){
            setlocale(LC_MONETARY, 'en_US');
            $message="Insufficient balance. Account (<b> [".$account_details['account_code']."] ".$account_details['account_name']." </b>) does not have enough funds to complete the transaction. <br>Required {<b> ".number_format ($amount,0,".","," )." </b>} and got: {<b> ".number_format ($balance['amount'],0,".","," )." </b>}";
        } else{
             $message =true;
        } 
        return $message;
    }
    //Audit logs.
    public function login_logs($user_id,$username,$message,$msg_type){
       
        if($msg_type==1){
            // login success
            $status_id=$msg_type;
            $status=$message;
            $uid=$user_id;
            $usn=$username;
            $login_time=time();
            $forced_logout_time=NULL;
            $logout_time=NULL;
            
        }
        else if($msg_type==2){
            //forced logout
            $status_id=$msg_type;
            $status=$message;
            $uid= $user_id;
            $usn=$username;
            $forced_logout_time=time();
            $login_time=NULL;
            $logout_time=NULL;
           
        }
        else if($msg_type==3){
            // login failed
            $status_id=$msg_type;
            $status=$message;
            $uid=NULL;
            $usn=$username;
            $forced_logout_time=Null;
            $logout_time=Null;
            $login_time=time();
            
        }
        else if($msg_type==4){
            //user logs out
            $status_id=$msg_type;
            $status=$message;
            $uid=$user_id;
            $usn=$username;
            $forced_logout_time=Null;
            $logout_time=time();
            $login_time=null;
            
        }

          else if($msg_type==5){
            //user logs out
            $status_id=$msg_type;
            $status=$message;
            $uid=$user_id;
            $usn=$username;
            $forced_logout_time=Null;
            $logout_time=null;
            $login_time=time();
            
        }

        $post_data = array(
            'user_id' => $uid, 
            'username' => $usn, 
            'status_id' => $msg_type,
            'status'=>$status,
            'login_time'=>$login_time,
            'forced_logout_time'=>$forced_logout_time,
            'logout_time'=>$logout_time
        );
        $post_data=$this->CI->miscellaneous_model->set_login_log($post_data);

    }


    public function activity_logs($user_id,$moudle_id,$action,$activity,$reference_id,$reference_number){
        
        $post_data=array(
        'action' =>$action,
        'date_created' => time(),
        'user_id'=>$user_id,
        'activity'=> $activity,
        'moudle_id'=>$moudle_id,
        'reference_id'=> $reference_id,
        'reference_number'=> $reference_number,
        'reference_url'=>current_url(),
    );
    $post_data=$this->CI->miscellaneous_model->set_activity_logs($post_data);
    }

    // updating user.login_time after auth two factor code resending 
    public function update_resend_code_time($user_id){
        /*$post_data=array(
            'id'=>$user_id,
            'login_time'=>time()
        );*/
        $this->CI->user_model->update_resend_code_time($user_id);
       }
    
}
