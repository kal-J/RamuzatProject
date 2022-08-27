<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member_add extends CI_Controller {

    public function __construct() {
        //constructor override...
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->library('form_validation');
        $this->load->model('User_model');
        $this->load->model('District_model');
        $this->load->model('Subcounty_model');
        $this->load->model('Parish_model');
        $this->load->model('Village_model');
    }

    public function index() {
        $this->data['title'] = $this->data['sub_title'] = "Member";
        $this->data['districts'] = $this->District_model->get_districts();
        $this->load->view("includes/header", $this->data);
        $this->load->view('user/examplebody');
        $this->load->view('user/user_modal');
        $this->load->view('user/address_add');
        $this->load->view('includes/footer');
    }

    public function create() {

        //if user not logged in, take them to the login page
        /*   if (!isset($_SESSION['user_id'])) {
          redirect('user/login');
          return;
          } */
        $this->load->library('form_validation');

        $this->form_validation->set_rules('firstname', 'Firstname', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('lastname', 'Lastname', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('salutation', 'Salutation', 'required');
        $this->form_validation->set_rules('gender', 'Gender', 'required');
        $this->form_validation->set_rules('village', 'Village', 'required');
        $this->form_validation->set_rules('marital_status', 'Marital Status', array('required'), array('required' => '%s must be seleted'));
        $this->form_validation->set_rules('date_of_birth', 'Date of Birth', array('required'), array('required' => '%s must be provided'));
        $this->form_validation->set_rules('email', 'Email', array('valid_email'), array('valid_email' => '%s provided is incorrect'));
        $this->form_validation->set_rules('disability', 'Disability', array('required'), array('required' => '%s must be provided'));
        $this->form_validation->set_rules('children_no', 'Number of Children', array('required'), array('required' => '%s must be provided'));
        $this->form_validation->set_rules('dependants_no', 'Number of dependants', array('required'), array('required' => '%s must be provided'));
        $this->form_validation->set_rules('crb_card_no', 'CRB Card number', array('required'), array('required' => '%s must be provided'));
        $this->form_validation->set_rules('village', 'Village', array('required'), array('required' => '%s must be provided'));
        $this->form_validation->set_rules('date_registered', 'Date Registered', ['required'], ['required' => '%s must be provided']);

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->User_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Member successfully updated";

                     $this->helpers->activity_logs($_SESSION['id'],1,"Creating Member",$feedback['message']." # ".$this->input->post('id'),NULL,$this->input->post('id'));
                } else {
                    $feedback['message'] = "Failed, Member details NOT updated";
                    
                     $this->helpers->activity_logs($_SESSION['id'],1,"Creating Member",$feedback['message']." # ".$this->input->post('id'),NULL,$this->input->post('id'));
                }
            }
             else {
                $user_id = $this->User_model->set();
                if ($user_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "User successfully saved";

                      $this->helpers->activity_logs($_SESSION['id'],1,"Creating Member",$feedback['message']." # ".$user_id,NULL,$user_id);
                } else {
                    $feedback['message'] = "Failed, couldnt update";
                    $this->helpers->activity_logs($_SESSION['id'],1,"Creating Member",$feedback['message']." # ".$user_id,NULL,$user_id);
                }
            }
        }
        echo json_encode($feedback);
    }

}
