<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');
class Authentication extends REST_Controller
{

       public function __construct() {
               parent::__construct();
               $this->load->model('user_model');

       }    

       public function login_post() {
        // Get the post data
        $this->form_validation->set_rules('username', 'username', 'required');
        $this->form_validation->set_rules('password', 'password', 'required');
        
        // Validate the post data
        if ($this->form_validation->run() === FALSE) {
            // Set the response and exit
            //BAD_REQUEST (400) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'Provide username and password.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }else{
            $this->data['users'] = $this->user_model->login();
            if (empty($this->data['users'])) {
              
                 // $this->helpers->login_logs(null,$this->input->post('username'),'Incorrect Username or Password',3);
            
            // Set the response and exit
            //BAD_REQUEST (400) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'Incorrect Username or Password.',
            ], REST_Controller::HTTP_BAD_REQUEST);
            } else {
                //verify the password
                $this->pass_check($this->data['users']);
            }
        }
    }



    private function pass_check($user_data) {
        if ($user_data['mystatus'] == 1 || $user_data['mystatus'] == 9) {
            if (password_verify($this->input->post('password'), $user_data['password'])) {
                //Login successful
                $hash = password_hash($this->input->post('password'), PASSWORD_DEFAULT, ['cost' => 12]);
                if (password_needs_rehash($hash, PASSWORD_DEFAULT, ['cost' => 12])) {
                    // Recalculate a new password_hash() and overwrite the one we stored previously
                    $this->user_model->update_pass($user_data['id']);
                }
                 //$this->helpers->login_logs($_SESSION['id'],$_SESSION['firstname']." ".$_SESSION['lastname']." ".$_SESSION['othernames'],1);

                // Add user data in session
                $userdata = array(
                    'id' => $user_data['id'],
                    'salutation' => $user_data['salutation'],
                    'firstname' => $user_data['firstname'],
                    'lastname' => $user_data['lastname'],
                    'othernames' => $user_data['othernames'],
                    'gender' => $user_data['gender'],
                    'email' => $user_data['email'],
                    'client_no' => $user_data['client_no'],
                    'member_id' => $user_data['member_id'],
                    'photograph' => $user_data['photograph'],
                    'mobile_number' => $user_data['mobile_number'],
                    'branch_id' => $user_data['branch_id'],
                    'curr_interface' => "client",
                    'organisation_id' => $user_data['organisation_id'],
                    'org_name' => $user_data['org_name']
                );

                $this->response([
                    'status' => TRUE,
                    'message' => 'User login successful.',
                    'data' => $userdata
                ], REST_Controller::HTTP_OK);
                
            }else {
                // $this->helpers->login_logs($user_data['id'],$_POST['username'],'Incorrect Username or Password',3);

            // Set the response and exit
            //BAD_REQUEST (400) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'Incorrect Username or Password.',
            ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            // Set the response and exit
            //BAD_REQUEST (401) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'Unauthorised access',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }
}