<?php

/**
 * This class helps to create the mode for the database operations
 */
class User_model extends CI_Model {

    Public function __construct() {
        parent :: __construct();
    }

    /**
     * This method adds user  details into the user table
     */
    public function check_client_no($client_no){ //check if exist
        $this->db->select('id');
        $this->db->from('fms_member');
        $this->db->where('client_no', $client_no);
        $query = $this->db->get();
        $client_nos = $query->result_array();
        return !empty($client_nos);
    }

    public function check_staff_no($staff_no){ //check if exist
        $this->db->select('staff_no');
        $this->db->from('fms_staff');
        $this->db->where('staff_no', $staff_no);
        $query = $this->db->get();
        if ($query->result_array()) {
            return true;
        } else {
            return false;
        }
    }

    public function add_user($user_data = []) {

        $data = $user_data;
        //Unsetting the values
        if ($user_data === []) {
            $data = $this->input->post(NULL, TRUE);
            //Date Registered
            if (isset($data['date_registered']) && $data['date_registered'] != '') {
                $registration_date = explode('-', $data['date_registered'], 3);
                $data['date_registered'] = count($registration_date) === 3 ? ($registration_date[2] . "-" . $registration_date[1] . "-" . $registration_date[0]) : null;
            }

            // PASSWORD 
            if (!empty($data['password'])) {
                $rawpassword = $data['password'];
                $options = [
                    'cost' => 12,
                ];
                $password = password_hash($rawpassword, PASSWORD_BCRYPT, $options);
                $data['password'] = $password;
            }
            //Date of Birth
            if (empty($data['date_of_birth'])) {
                $data['date_of_birth'] = date('Y-m-d');
            } else {
                $date_of_birth = explode('-', $data['date_of_birth'], 3);
                $data['date_of_birth'] = count($date_of_birth) === 3 ? ($date_of_birth[2] . "-" . $date_of_birth[1] . "-" . $date_of_birth[0]) : null;
            }
            $data['date_created'] = time();
            $data['created_by'] = $_SESSION['id'];
            $data['modified_by'] = $_SESSION['id'];
            unset($data['id'],$data['client_no3'], $data['user_id'],$data['client_no'],$data['branch_id'], $data['occupation'], $data['confirmpassword'], $data['spouse_name'], $data['subscription_plan_id'], $data['position_id'], $data['mobile_number'], $data['date_registered'], $data['registered_by'],$data['introduced_by_id'],$data['member_referral_on_off']);
        }

        $this->db->insert('user', $data);
        return $this->db->insert_id();
    }

    // login the user
    public function login() {
        $this->db->select('u.id,u.salutation,u.firstname,old_sys_pass,u.photograph,u.lastname,u.othernames,u.password,u.gender,u.email');
        $this->db->select('m.client_no,m.id as member_id,m.branch_id,m.status_id as mystatus');
        $this->db->select('b.organisation_id,org.name org_name,org_initial,c.mobile_number');
        $this->db->from('user u');
        $this->db->join('member m', 'm.user_id=u.id', 'left');
        $this->db->join('branch b', 'm.branch_id=b.id');
        $this->db->join('organisation org', 'b.organisation_id=org.id');
        $this->db->join('contact c', 'c.user_id=u.id', 'left');
        $this->db->or_where('m.client_no', $this->input->post('username')); /* // */
        $this->db->or_where('u.email', $this->input->post('username'));
        $this->db->or_where('c.mobile_number', $this->input->post('username'));
        $query = $this->db->get();
        return $query->row_array();
    }

    public function reset_password_request() {
        $this->db->select('email');
        $this->db->from("user");
        $this->db->where('email', $this->input->post('username'));
        $query = $this->db->get();
        $count = $query->num_rows();
        if($count > 0){
            $response = $query->row_array();
            return $response['email'];
        }
        return false;
    }

    public function save_password_reset_request() {
        $token = $this->generateRandomString();
        $expiry_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' +5 minutes'));
        $data = array(
            "email" => $this->input->post('username'),
            "token" => $token,
            "expiry_at" => $expiry_at
        );

        $query = $this->db->insert('password_reset', $data);
        if($query){
            return $token;
        }else {
            return false;
        }
    }

    public function validate_password_reset_token($token) {
        $this->db->select("*");
        $this->db->from('password_reset');
        $this->db->where('token',$token);
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $response = $query->result_array();
            $now = date('Y-m-d H:i:s');
            if($response[0]['expiry_at'] >= $now){
                return $response[0]['email'];
            }else {
                return false;
            }
        }else {
            return false;
        }

    }

    private function generateRandomString($length = 24) {
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($length / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $length);
    }

    public function update_staff_password() {
        $email = $this->input->post('email');
        if($email == null){
            return false;
        }
        $rawpassword = $this->input->post('password');
        $options = [
            'cost' => 12,
        ];
        $password = password_hash($rawpassword, PASSWORD_BCRYPT, $options);
        $data['password'] = $password;
        $this->db->where('email', $email);
        return $this->db->update('user', $data);
    }

    public function login_staff() {
        $this->db->select('u.id,u.salutation,u.firstname,old_sys_pass,login_time,u.photograph,u.lastname,login_attempt,u.othernames,u.password,u.gender,u.email');
        $this->db->select('s.id as staff_id,s.staff_no,s.branch_id,s.status_id as mystatus');
        $this->db->select('r.id as role_id,r.role,b.organisation_id,org.name org_name,org.member_referral,org_initial,c.mobile_number,two_factor_choice,two_factor');
        $this->db->from('user u');

        $this->db->join('staff s', 's.user_id=u.id', 'left');
        $this->db->join('user_role ur', 'ur.staff_id=s.id AND ur.status_id=1', 'left');
        $this->db->join('role r', 'ur.role_id=r.id', 'left');
        $this->db->join('branch b', 's.branch_id=b.id');
        $this->db->join('organisation org', 'b.organisation_id=org.id');
        $this->db->join('contact c', 'c.user_id=u.id', 'left');
        $this->db->where('s.staff_no', $this->input->post('username'));
        $this->db->or_where('u.email', $this->input->post('username'));
        $this->db->or_where('c.mobile_number', $this->input->post('username'));
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_by_uname_email($uname_email = FALSE) {
        $uname_email1 = $uname_email ? $uname_email : $this->input->post('uname_email');
        $this->db->from('user');
        //$this->db->where("status", 1);
        $this->db->where("username", $uname_email1);
        $this->db->or_where("email", $uname_email1);
        $this->db->or_where("email2", $uname_email1);
        $query = $this->db->get();
        return $query->row_array();
    }

     public function verify($code) {
        $this->db->select('u.id,u.salutation,u.firstname,old_sys_pass,u.photograph,u.lastname,u.othernames,u.password,u.gender,u.email');
        $this->db->select('s.id as staff_id,s.staff_no,s.branch_id,s.status_id as mystatus');
        $this->db->select('r.id as role_id,r.role,b.organisation_id,org.name org_name,org_initial,c.mobile_number');
        $this->db->from('user u');
        $this->db->join('staff s', 's.user_id=u.id', 'left');
        $this->db->join('user_role ur', 'ur.staff_id=s.id AND ur.status_id=1', 'left');
        $this->db->join('role r', 'ur.role_id=r.id', 'left');
        $this->db->join('branch b', 's.branch_id=b.id');
        $this->db->join('organisation org', 'b.organisation_id=org.id');
        $this->db->join('contact c', 'c.user_id=u.id', 'left');
        $this->db->where('u.verification_code', $code);
        $this->db->where('u.verified', 0);
        $this->db->where('u.email', $this->input->post('email'));
        $query = $this->db->get();
        return $query->row_array();
    }

    public function update_code($id) {
        $data = array(
            'verified' => 1
        );
        $this->db->where('user.id', $id);
        return $this->db->update('user', $data);
    }

    public function set_code($id,$code,$login_time) {
        $data = array(
            'verification_code' => $code,
            'login_time' => $login_time,
            'verified' => 0
        );
        $this->db->where('user.id', $id);
        return $this->db->update('user', $data);
    }

    

    public function validate_email($email) {
        $user_id = $this->input->post('user_id');
        $id = $this->input->post('id');

        if ($id === NULL || empty($id)) {
            $query_result = $this->db
                    ->limit(1)
                    ->where('email=', $email)
                    ->get('user');
            return ($query_result->num_rows() === 0);
        } else {
            $query_result = $this->db
                    ->limit(1)
                    ->where('id=', $user_id)
                    ->where('email=', $email)
                    ->get('user');
            if ($query_result->num_rows() === 1) {
                return TRUE;
            } else {
                $query_result = $this->db
                        ->limit(1)
                        ->where('email=', $email)
                        ->get('user');
                return ($query_result->num_rows() === 0);
            }
        }
    }

    public function validate_client_no($client_no) {
        $user_id = $this->input->post('user_id');
        $id = $this->input->post('id');

        if ($id === NULL || empty($id)) {
            $query_result = $this->db
                    ->limit(1)
                    ->where('client_no=', $client_no)
                    ->get('member');
            return ($query_result->num_rows() === 0);
        } else {
            $query_result = $this->db
                    ->limit(1)
                    ->where('user_id=', $user_id)
                    ->where('client_no=', $client_no)
                    ->get('member');
            if ($query_result->num_rows() === 1) {
                return TRUE;
            } else {
                $query_result = $this->db
                        ->limit(1)
                        ->where('client_no=', $client_no)
                        ->get('member');
                return ($query_result->num_rows() === 0);
            }
        }
    }

    /**
     * This method updates staff data in the database
     */
    public function update_user() {
      
        $id = $this->input->post('user_id');
      
        $data = $this->input->post(NULL, TRUE);
        //Date Registered
        if (isset($data['date_registered']) && $data['date_registered'] != '') {
            $registration_date = explode('-', $data['date_registered'], 3);
            $data['date_registered'] = count($registration_date) === 3 ? ($registration_date[2] . "-" . $registration_date[1] . "-" . $registration_date[0]) : null;
        }

        //Date of Birth
        $date_of_birth = explode('-', $data['date_of_birth'], 3);
        $data['date_of_birth'] = count($date_of_birth) === 3 ? ($date_of_birth[2] . "-" . $date_of_birth[1] . "-" . $date_of_birth[0]) : null;

        //Unsetting the values
        unset($data['id'], $data['user_id'],$data['branch_id'], $data['occupation'],$data['client_no'], $data['confirmpassword'], $data['password'], $data['spouse_name'], $data['subscription_plan_id'], $data['position_id'], $data['mobile_number'], $data['date_registered'], $data['registered_by'],$data['member_referral_on_off'],$data['introduced_by_id']);
        $data['modified_by'] = $_SESSION['id'];

        $this->db->where('id', $id);
        $query = $this->db->update('user', $data);
        if ($query) {
            return true;
        } else {
            return false;
        }
   
    }

    public function change_status() {

        $data = array(
            'status_id' => $this->input->post('status') !== NULL ? $this->input->post('status') : 0,
            'modified_by' => $_SESSION['user_id']
        );
        $this->db->where('user.id', $this->input->post('user_id') !== NULL ? $this->input->post('user_id') : $_SESSION['user_id']);
        return $this->db->update('user', $data);
    }

    public function delete($user_id) {
        $this->db->where('id', $user_id);
        return $this->db->delete('user');
    }

    public function user_id() {
        $this->db->select('u.id,u.salutation,u.firstname,u.photograph,u.lastname,u.othernames,u.password,u.gender,u.email');
        $this->db->select('s.id as staff_id,s.staff_no,s.branch_id,s.status_id as mystatus');
        $this->db->select('r.id as role_id,r.role,b.organisation_id,org.name org_name,org_initial,c.mobile_number,org.member_referral');
        $this->db->from('user u');
        $this->db->join('staff s', 's.user_id=u.id', 'left');
        $this->db->join('user_role ur', 'ur.staff_id=s.id AND ur.status_id=1', 'left');
        $this->db->join('role r', 'ur.role_id=r.id', 'left');
        $this->db->join('branch b', 's.branch_id=b.id');
        $this->db->join('organisation org', 'b.organisation_id=org.id');
        $this->db->join('contact c', 'c.user_id=u.id', 'left');
        $this->db->where('s.staff_no', $this->session->userdata('staff_no'));
        $this->db->or_where('u.email', $this->session->userdata('email'));
        $this->db->or_where('c.mobile_number', $this->session->userdata('mobile_number'));
        $query = $this->db->get();
        return $query->row_array();
    }
    

    // login attempts blocking and unblock account
    public function update_login_attempt($user_id,$counter){
         $this->db->set('login_attempt',$counter);
         $this->db->where('id',$user_id);
         return $this->db->update('user');
         
    }

    //updates the time for code resend 
     public function update_resend_code_time($user_id){
         $this->db->set('login_time',$this->helpers->get_date_time(date('Y-m-d')));
         $this->db->where('id',$user_id);
         return $this->db->update('user');
         
        }
     public function get_login_time($user_id)
        {
        $this->db->select('login_time'); 
        $this->db->from('user');   
        $this->db->where('id',$user_id);
        $query = $this->db->get();
        return $query->row_array();
        
    }

    // check if the client has set the password already
    public function has_already_set_password($id){
        // echo $id ; die;
        $this->db->select('*'); 
        $this->db->from('user');   
        $this->db->where('id',$id);
        $query = $this->db->get();
        $result =$query->row_array();
        if(isset($result['has_set_password']) && $result['has_set_password']){
            return true;
        }
        return false;
    }

   


}
