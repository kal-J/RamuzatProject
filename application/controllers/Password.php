<?php
class Password extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        
        $this->load->model('Password_model');
        $this->load->model('member_model');
        $this->load->model('Staff_model');
    }
    public function create() {
        $this->form_validation->set_rules('password', 'New Password', 'required|min_length[8]', array('required' => 'Please set  %s.','min_length[8]'=>'%s must be alteast 8 Characters'));
       $this->form_validation->set_rules('confirmpassword', 'Confirm Password', 'required|matches[password]', array('required' => 'Please confirm password.',' matches[password]'=>'%s does not match'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
           
                $pass_id = $this->Password_model->update();
                if ($pass_id) {
                    $feedback['success'] = true;
                    if($this->input->post('user_type')=="member"){
                    $feedback['user'] = $this->member_model->get_member($this->input->post('c_id'));
                    } else {
                    $feedback['user'] =  $this->Staff_model->get_staff($this->input->post('c_id'));
                    }
                    $feedback['message'] = "Password details successfully saved";

                       $this->helpers->activity_logs($_SESSION['id'],18,"Set/Reset user password ",$feedback['message']." # ".$pass_id,NULL,"id #".$this->input->post('user_type'));
                } else {
                    $feedback['message'] = "There was a problem setting user Password";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Reset user password ",$feedback['message']." # ".$pass_id,NULL,"id #".$this->input->post('user_type'));
        }
    }
        echo json_encode($feedback);
   }
}
