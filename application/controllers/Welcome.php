<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('user_model');
        $this->load->model('organisation_model');
        $this->load->library('helpers');
        
           // $da=$this->helpers->getBrowser();
           // if($da['version']==87.0){
           //  redirect(base_url('Browser')); 
           //  }else{
           //  echo $da['version']; 
           // }
        
        
    }

    // public function index()
    // {
    // 	$this->data['org'] = $this->organisation_model->get(1);
    //     $this->data['title'] = $this->data['sub_title'] = "Welcome to eFMS";
    //     $this->load->view('subscription',$this->data);
    // }


    /// ================staff member login ========================================
    public function index()
    {


        if ((!empty($this->session->userdata('id')) && ($_SESSION['curr_interface'] == 'staff'))) {
            $this->helpers->login_logs($_SESSION['id'], $_SESSION['firstname'] . " " . $_SESSION['lastname'] . " " . $_SESSION['othernames'], 'Success', 1);

            redirect('dashboard');
        } elseif ((!empty($this->session->userdata('id')) && ($_SESSION['curr_interface'] == 'client'))) {
            $this->helpers->login_logs($_SESSION['id'], $_SESSION['firstname'] . " " . $_SESSION['lastname'] . " " . $_SESSION['othernames'], 'Success', 1);
            redirect('u/home');
        } else {

            $this->data['org'] = $this->organisation_model->get(1);
            $this->data['title'] = $this->data['sub_title'] = "Welcome to eFMS";
            $this->load->view('staff_login', $this->data);
        }
    }

    public function password_reset() {
        $this->data['org'] = $this->organisation_model->get(1);
        $this->data['title'] = $this->data['sub_title'] = "Welcome to eFMS - Did you forgot password?";
        $this->load->view('reset_password', $this->data);
    }

    public function save_new_password() {
        $this->data['org'] = $this->organisation_model->get(1);
        $this->data['title'] = $this->data['sub_title'] = "Welcome to eFMS - password reset";
        $this->form_validation->set_rules('password', 'password', 'trim|required|min_length[8]');
        $this->form_validation->set_rules('confirmPassword', 'confirmPassword', 'required|matches[password]');
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('update_password', $this->data);
        }else {
            if($this->user_model->update_staff_password()){
                $this->session->set_flashdata('message', '<div class="alert alert-success text-center alert-dismissable">Your password has been updated, please login <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');
                redirect('welcome');
            }else {
                $this->session->set_flashdata('message', '<div class="alert alert-warning text-center alert-dismissable">Something went wrong while updating your password, please try again later <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');
            }
        }
    }

    public function password_reset_request() {
        $this->load->library('email');
        $this->data['org'] = $this->organisation_model->get(1);
        $this->data['title'] = $this->data['sub_title'] = "Login";
        $this->form_validation->set_rules('username', 'username', 'required');
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('reset_password', $this->data);
        } else {
            $response = $this->user_model->reset_password_request();
            if($response){
                // continue
                $email = $this->input->post('username');
                $token = $this->user_model->save_password_reset_request();
                if($token) {
                    $message = '<p>Click <a href="'. base_url('welcome/update_password/'.$token) .'">here</a> to change your password</p>';
                    $subject = "Reset password link";
                    $this->helpers->send_password_reset_email($email, $message, $subject);
                    $this->session->set_flashdata('message', '<div class="alert alert-success text-center alert-dismissable">Your password reset link has been emailed to you <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');
                }else {
                    $this->session->set_flashdata('message', '<div class="alert alert-warning text-center alert-dismissable">We couldn\'t process your request please try again later or contact support <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');

                }
                $this->load->view('reset_password', $this->data);
            }else {
                $this->session->set_flashdata('message', '<div class="alert alert-warning text-center alert-dismissable">Invalid details given <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');
                $this->load->view('reset_password', $this->data);
            }

        }
        
    }

    public function update_password($token=FALSE) {
        $this->data['org'] = $this->organisation_model->get(1);
        $this->data['title'] = $this->data['sub_title'] = "Welcome to eFMS - password reset";
        if($token == FALSE){
            redirect('welcome');
            
        }
        $is_valid = $this->user_model->validate_password_reset_token($token);
        if($is_valid){
            $this->data['email'] = $is_valid;
            $this->load->view('update_password', $this->data);

        }else {
            // send back
            $this->session->set_flashdata('message', '<div class="alert alert-warning text-center alert-dismissable">Invalid token for password reset provided, or it might be expired <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');
            // $this->load->view('reset_password', $this->data);
            redirect('welcome/password_reset');
        }
    }

    function sendMail($message){

        $config = array();
        $config['useragent']           = "CodeIgniter";
        $config['mailpath']            = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
        $config['protocol']            = "smtp";
        $config['smtp_host']           = "localhost";
        $config['smtp_port']           = "25";
        $config['mailtype'] = 'html';
        $config['charset']  = 'utf-8';
        $config['newline']  = "\r\n";
        $config['wordwrap'] = TRUE;

        // $message = 'Testing email';
        $this->load->library('email');
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $this->email->from('ssenogaedward52@gmail.com'); // change it to yours
        $this->email->to('senoeddie@gmail.com');// change it to yours
        $this->email->subject('Reset password mail');
        $this->email->message($message);
        if(!$this->email->send()){
            show_error($this->email->print_debugger());
        }

}

    public function auth($msg_type = false) {
        
        
        if ((!empty($this->session->userdata('id')) && ($_SESSION['curr_interface'] == 'staff'))) {
            redirect('dashboard');
        }

        $this->data['org'] = $this->organisation_model->get(1);
        $this->data['title'] = $this->data['sub_title'] = "Login";
        $this->form_validation->set_rules('username', 'username', 'required');
        $this->form_validation->set_rules('password', 'password', 'required');
        if ($msg_type == 1) {
            $this->session->set_flashdata('message', '<div class="alert alert-success text-center alert-dismissable">Successfully Logged out <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');
        } elseif ($msg_type == 2) {
            $this->session->set_flashdata('message', '<div class="alert alert-warning text-center alert-dismissable">Logged out due to inactivity, login again <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');
        } elseif ($msg_type == 3) {
            $this->session->set_flashdata('message', '<div class="alert alert-primary text-center alert-dismissable">Please login to access this resource. <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');
        } elseif ($msg_type == 4) {
            $this->session->set_flashdata('message', '<div class="alert alert-primary text-center alert-dismissable">You do not have sufficient privilleges.<br/>Please contact the administrator first. <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');
        }

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('staff_login', $this->data);
        } else {
            $this->data['users'] = $this->user_model->login_staff();
            if (empty($this->data['users'])) {

                $this->session->set_flashdata('message', '<div class="alert alert-danger text-center alert-dismissable">Incorrect Username or Password ! <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');
                $this->helpers->login_logs(null, $this->input->post('username'), 'Incorrect Username or Password', 3);

                $this->load->view('staff_login', $this->data);
            } else {
                if ($this->data['users']['mystatus'] == 1 || $this->data['users']['mystatus'] == 9) {
                    if ($this->data['users']['login_attempt'] > 0) {
                        if (password_verify($this->input->post('password'), $this->data['users']['password'])) {
                            if ($this->data['users']['two_factor'] == 1) {
                                if ($this->data['users']['two_factor_choice'] == 1) {
                                    $code = rand(1000, 9999);

                                    $this->data['form_name'] = "Phone Number";

                                    $message = "LOGIN VERIFICATION CODE: " . $code;

                                    if ($this->data['users']['login_time'] == NULL) {

                                        if ($this->helpers->send_sms($this->data['users']['login_time'], $message)) {
                                            $login_time = $this->helpers->get_date_time(date('Y-m-d'));
                                            $this->user_model->set_code($this->data['users']['id'], $code, $login_time);
                                            $this->helpers->update_resend_code_time($this->data['users']['id']);
                                            $this->helpers->login_logs($this->data['users']['id'], $this->data['users']['firstname'] . " " . $this->data['users']['lastname'] . " " . $this->data['users']['othernames'], 'LOGIN VERIFICATION CODE SENT', 5);
                                            $this->load->view('user/otp', $this->data);
                                        } else {
                                            $this->session->set_flashdata('message', '<div class="alert alert-warning text-center">Code sending Failed, Try again</div>');
                                            $this->helpers->login_logs($this->data['users']['id'], $this->data['users']['firstname'] . " " . $this->data['users']['lastname'] . " " . $this->data['users']['othernames'], 'Code sending Failed, Try again', 5);
                                            redirect('/');
                                        }
                                    } elseif ($this->data['users']['login_time'] != NULL) {
                                        $db_time = new DateTime($this->data['users']['login_time']);
                                        $now = new DateTime(date('Y-m-d H:i:s'));
                                        $diff = ($now->getTimeStamp() - $db_time->getTimeStamp());
                                        if ($diff < 180) {
                                            $this->load->view('user/otp', $this->data);
                                        } else {
                                            if ($this->helpers->send_sms($this->data['users']['login_time'], $message)) {
                                                $login_time = $this->helpers->get_date_time(date('Y-m-d'));
                                                $this->user_model->set_code($this->data['users']['id'], $code, $login_time);
                                                $this->load->view('user/otp', $this->data);
                                            } else {
                                                $this->session->set_flashdata('message', '<div class="alert alert-warning text-center">Code sending Failed, Try again</div>');
                                                $this->helpers->login_logs($this->data['users']['id'], $this->input->post('Username'), 'Code sending Failed, Try again', 5);
                                                redirect('/');
                                            }
                                        }
                                    }
                                } else if ($this->data['users']['two_factor_choice'] == 2) {
                                    $code = rand(1000, 9999);
                                    $this->data['form_name'] = "Email";
                                    $message = "Dear " . ucfirst(strtolower($this->data['users']['firstname'])) . ",<br><br> LOGIN VERIFICATION CODE: <b>" . $code . "</b>";
                                    //updating the login time.
                                    $this->helpers->update_resend_code_time($this->data['users']['id']);

                                    if ($this->helpers->send_multiple_email($this->data['users']['branch_id'], $this->data['users']['email'], $message, false, $this->data['users']['organisation_id'])) {
                                        $this->user_model->set_code($this->data['users']['id'], $code);
                                        $this->helpers->login_logs($this->data['users']['id'], $this->data['users']['firstname'] . " " . $this->data['users']['lastname'] . " " . $this->data['users']['othernames'], 'LOGIN VERIFICATION CODE SENT', 5);
                                        $this->load->view('user/otp', $this->data);
                                    } else {
                                        $this->session->set_flashdata('message', '<div class="alert alert-warning text-center">Code sending Failed, Try again</div>');
                                        //code verification failure 
                                        $this->helpers->login_logs(null, $this->input->post('username'), 'Code sending Failed, Try again', 5);
                                        redirect('/');
                                    }
                                } else {
                                    $this->session->set_flashdata('message', '<div class="alert alert-danger text-center">Authentication Methord Failed!, contact IT Support</div>');
                                    $this->load->view('staff_login', $this->data);
                                    $this->helpers->login_logs($this->data['users']['id'], $this->data['users']['firstname'] . " " . $this->data['users']['lastname'] . " " . $this->data['users']['othernames'], 'Authentication Methord Failed!, contact IT Support', 5);
                                }
                            } else {
                                //login success,commented out for testing
                                // $this->helpers->login_logs($_SESSION['id'],$_SESSION['firstname']." ".$_SESSION['lastname']." ".$_SESSION['othernames'],'Success',1);

                                $this->setSessionData($this->data['users']);

                                redirect('dashboard');
                            }
                        } else {

                            $counter = $this->data['users']['login_attempt'] - 1;
                            $this->user_model->update_login_attempt($this->data['users']['id'], $counter);
                            $this->session->set_flashdata('message', '<div class="alert alert-danger text-center">Incorrect Username or Password ! <br/> <br> <b> You have ' . $this->data['users']['login_attempt'] . ' attempts left</b> </div>');
                            $this->helpers->login_logs(null, $this->input->post('username'), 'Incorrect Username or Password', 3);

                            $this->load->view('staff_login', $this->data);
                        }
                    } else {
                        $this->session->set_flashdata('message', '<div class="alert alert-danger text-center">Your Account has been blocked!, <br/> Please contact the system administrator.</div>');
                        $this->load->view('staff_login', $this->data);
                        $this->helpers->login_logs(null, $_POST['username'], 'Please contact the system administrator', 3);
                    }
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger text-center alert-dismissable">Account Inactive !<br/>Please contact the administrator .  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');
                    $this->load->view('staff_login', $this->data);
                    $this->helpers->login_logs(null, $_POST['username'], 'Account Inactive !<br/>Please contact the administrator .', 3);
                }
            }
        }
    }


    public function member()
    {

        if ((!empty($this->session->userdata('id')) && ($_SESSION['curr_interface'] == 'staff'))) {
            redirect('dashboard');
        } elseif ((!empty($this->session->userdata('id')) && ($_SESSION['curr_interface'] == 'client'))) {
            redirect('u/home');
        } else {

            $this->data['org'] = $this->organisation_model->get(1);
            $this->data['title'] = $this->data['sub_title'] = "Welcome to eFMS";
            $this->load->view('login', $this->data);
        }
    }

    public function login($msg_type = false)
    {

        if ((!empty($this->session->userdata('id')) && ($_SESSION['curr_interface'] == 'client'))) {
            redirect('u/home');
        }

        $this->data['org'] = $this->organisation_model->get(1);
        $this->data['title'] = $this->data['sub_title'] = "Login";
        $this->form_validation->set_rules('username', 'username', 'required');
        $this->form_validation->set_rules('password', 'password', 'required');
        if ($msg_type == 1) {
            $this->session->set_flashdata('message', '<div class="alert alert-success text-center alert-dismissable">Successfully Logged out <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');
        } elseif ($msg_type == 2) {
            $this->session->set_flashdata('message', '<div class="alert alert-warning text-center alert-dismissable">Logged out due to inactivity, login again <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');
        } elseif ($msg_type == 3) {
            $this->session->set_flashdata('message', '<div class="alert alert-primary text-center alert-dismissable">Please login to access this resource. <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');
        } elseif ($msg_type == 4) {
            $this->session->set_flashdata('message', '<div class="alert alert-primary text-center alert-dismissable">You do not have sufficient privilleges.<br/>Please contact the administrator first. <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');
        }
        // redirect to login Modified by Ambrose


        if ($this->form_validation->run() === FALSE) {
            $this->load->view('login', $this->data);
        } else {
            $this->data['users'] = $this->user_model->login();
            if (empty($this->data['users'])) {

                $this->session->set_flashdata('message', '<div classF="alert alert-danger text-center alert-dismissable">Incorrect Username or Password ! <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');
                $this->helpers->login_logs(null, $this->input->post('username'), 'Incorrect Username or Password', 3);

                $this->load->view('login', $this->data);
            } else {
                //verify the password
                $this->pass_check($this->data['users']);
            }
        }
    }

    private function pass_check($user_data)
    {
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
                $this->session->set_userdata($userdata);

                // if($this->session->userdata('page_url')){
                //redirect($this->session->userdata('page_url'));
                //} else {
                // } 
                redirect('u/home');
            } elseif ($user_data['old_sys_pass'] === sha1($this->input->post('password'))) {
                /*
                 * lets create a new password with the new stronger hashing algo.
                 * This will be checked first, the next time, when logging in.
                 * Then redirect the user to the dashboard
                 */
                $this->user_model->update_pass($user_data['id']);
                redirect('u/home');
            } else {

                $this->session->set_flashdata('message', '<div class="alert alert-danger text-center">Incorrect Username or Password !</div>');
                $this->helpers->login_logs($user_data['id'], $_POST['username'], 'Incorrect Username or Password', 3);

                $this->load->view('login', $this->data);
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger text-center alert-dismissable">Access denied !<br/>Please contact your administrator .  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>');

            $this->load->view('login', $this->data);
        }
    }

    public function verify()
    {
        $code = $this->input->post('d1') . $this->input->post('d2') . $this->input->post('d3') . $this->input->post('d4');
        $this->data['users'] = $this->user_model->verify($code);

        if (!empty($this->data['users'])) {
            //$this->helpers->login_logs($_SESSION['id'],$_SESSION['firstname'],1);

            $this->user_model->update_code($this->data['users']['id']);
            $this->setSessionData($this->data['users']);
            $feedback['success'] = true;
            $feedback['message'] = "Verified !";
        } else {
            // $this->helpers->login_logs($_SESSION['id'],$_SESSION['firstname'],1);
            $feedback['success'] = false;
            $feedback['message'] = "Code Invalid !";
        }
        echo json_encode($feedback);
    }

    public function setSessionData($users)
    {
        $this->helpers->login_logs($users['id'], $users['firstname'] . " " . $users['lastname'] . " " . $users['othernames'], 'Success', 1);
        $this->user_model->update_login_attempt($users['id'], 4);

        $userdata = array(
            'id' => $users['id'],
            'salutation' => $users['salutation'],
            'firstname' => $users['firstname'],
            'lastname' => $users['lastname'],
            'othernames' => $users['othernames'],
            'gender' => $users['gender'],
            'email' => $users['email'],
            'photograph' => $users['photograph'],
            'mobile_number' => $users['mobile_number'],
            'staff_id' => $users['staff_id'],
            'mystatus' => $users['mystatus'],
            'staff_no' => $users['staff_no'],
            'branch_id' => $users['branch_id'],
            'role' => $users['role'],
            'role_id' => $users['role_id'],
            'curr_interface' => "staff",
            'organisation_id' => $users['organisation_id'],
            'org_name' => $users['org_name'],
            'member_referral' => $users['member_referral']
        );
        return $this->session->set_userdata($userdata);
    }
    public function logout($msg_type = 1)
    {

        if (isset($_SESSION['curr_interface']) && $_SESSION['curr_interface'] == 'client') {
            if ($_SESSION['id'] != null) {
                $this->session->sess_destroy();
                $this->helpers->login_logs($_SESSION['id'], $_SESSION['firstname'] . " " . $_SESSION['lastname'] . " " . $_SESSION['othernames'], 'Successfully logged out', 4);
                redirect("welcome/login/$msg_type", "refresh");
            } else {
                $feedback['message'] = "An error occured on logged out";
                echo json_encode($feedback['message']);
                redirect("dashboard/", "refresh");
            }
        } else {
            //staff logout
            if ($_SESSION['id'] != null) {
                $this->helpers->login_logs($_SESSION['id'], $_SESSION['firstname'] . " " . $_SESSION['lastname'] . " " . $_SESSION['othernames'], 'Successfully logged out', 4);
                $this->session->sess_destroy();

                redirect("welcome/auth/$msg_type", "refresh");
            } else {
                $feedback['message'] = "An error occured on logged out";
                echo json_encode($feedback['message']);
                redirect("dashboard/", "refresh");
            }
        }
    }

    public function clear_session_id()
    {
        if ($this->input->post("idleTime") == 20) {
            $this->helpers->login_logs($_SESSION['id'], $_SESSION['firstname'] . " " . $_SESSION['lastname'] . " " . $_SESSION['othernames'], 'Logged out due to inactivity, login again', 2);
            //at 10 minutes lock the user's screen
            $this->session->unset_userdata('id');
        }

        //  * possible scenarios here
        //  * The user is logged in but is inactive for sometime, on one tab
        //  * There are multiple tabs/windows opened at the same time in the same browser by a user at a given time
        //  * In case the user is active in one, then the rest of the tabs should be syncing with the active one (they should be updated with the same time
        //  * If there is no activity, then all the tabs should have the time when the user was last active

        $idleTime = (isset($_SESSION['idleTime']) ? (($_SESSION['idleTime'] > ($this->input->post("idleTime") + 1)) ? $this->input->post("idleTime") : (($this->input->post("idleTime") - $_SESSION['idleTime'] == 0) ? $_SESSION['idleTime'] : $this->input->post("idleTime"))) : $this->input->post("idleTime"));
        $response = ["idleTime" => ((empty($this->session->userdata('firstname'))) ? 30 : $idleTime)];
        $this->session->set_userdata('idleTime', $response['idleTime']);
        echo json_encode($response);
    }

    public function unlock()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('password', 'password', 'required');
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {
            $this->data['users'] = $this->user_model->user_id();
            if (empty($this->data['users'])) {
                $feedback['message'] = "Please refresh the page to login";
            } else {
                $pass_verify = password_verify($this->security->xss_clean($this->input->post('password')), $this->data['users']['password']);
                //print_r($pass_verify);die();
                if ($pass_verify) {
                    //Login successful
                    $this->helpers->login_logs($this->data['users']['id'], $this->data['users']['firstname'] . " " . $this->data['users']['lastname'] . " " . $this->data['users']['othernames'], 'Success', 1);

                    $hash = password_hash($this->input->post('password'), PASSWORD_DEFAULT, ['cost' => 12]);
                    if (password_needs_rehash($hash, PASSWORD_DEFAULT, ['cost' => 12])) {
                        // Recalculate a new password_hash() and overwrite the one we stored previously
                        $this->user_model->update_pass($this->data['users']['id']);
                    }

                    // Add user data in session
                    $userid = array(
                        'id' => $this->data['users']['id'],
                        'salutation' => $this->data['users']['salutation'],
                        'firstname' => $this->data['users']['firstname'],
                        'lastname' => $this->data['users']['lastname'],
                        'othernames' => $this->data['users']['othernames'],
                        'gender' => $this->data['users']['gender'],
                        'email' => $this->data['users']['email'],
                        'photograph' => $this->data['users']['photograph'],
                        'mobile_number' => $this->data['users']['mobile_number'],
                        'staff_id' => $this->data['users']['staff_id'],
                        'mystatus' => $this->data['users']['mystatus'],
                        'staff_no' => $this->data['users']['staff_no'],
                        'branch_id' => $this->data['users']['branch_id'],
                        'role' => $this->data['users']['role'],
                        'role_id' => $this->data['users']['role_id'],
                        'curr_interface' => "staff",
                        'organisation_id' => $this->data['users']['organisation_id'],
                        'org_name' => $this->data['users']['org_name'],
                        'member_referral' => $this->data['users']['member_referral']
                    );
                    $this->session->set_userdata($userid);
                    $feedback['success'] = true;
                    $feedback['message'] = "Session Unlocked!";
                    //reset the idle time back to zero
                    $this->session->set_userdata('idleTime', 0);
                    $feedback['idleTime'] = $_SESSION['idleTime'];
                } else {
                    $feedback['message'] = "Incorrect Password!";
                }
            }
        }
        echo json_encode($feedback);
    }

    public function resend()
    {
        $this->data['users'] = $this->user_model->login_staff(1);
        $this->load->view('user/otp', $this->data);
    }
}
