<?php
class Password_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->from('user');
        if ($filter!== FALSE) {
            $this->db->where_not_in('id',$this->input->post('username'));
            $query = $this->db->get();
            //echo $this->db->last_query();
            return $query->row_array();
        }
    }

    public function update() {
        $rawpassword = $this->input->post('password');
        $options = [
            'cost' => 12,
        ];
       
        $password =password_hash($rawpassword, PASSWORD_BCRYPT, $options);
        $data = array(
            'password' => $password, 
            'modified_by' => $_SESSION['id'],
            'login_attempt' =>4

        );

        if($this->input->post("has_set_password") != null){
            $data['has_set_password'] = true;
        }
  
        $this->db->where('id', $this->input->post('user_id'));
        return $this->db->update('user', $data);

    }

    public function check_pass($user_id) {
        $this->db->where('email', $this->input->post('email'));
        $this->db->where('phone', $this->input->post('phone'));
        $query = $this->db->get('user');
        return $query->row_array();
    }

    public function update_pass($user_id, $new_pass = FALSE) {
        if ($new_pass === FALSE) {
            $new_pass = $this->input->post('pwd');
            if($new_pass === null){
              $new_pass = $this->input->post('pwd');
              }
        }
        $data = array('password' => password_hash($new_pass, PASSWORD_DEFAULT)/* , ['cost' => 12] */);
        $this->db->where('id', $user_id);
        return $this->db->update('user', $data);
    }

}
