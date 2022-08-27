<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_role extends CI_Controller {
    public function __construct() {
		 parent::__construct(); 
     $this->load->library("session");
        $this->load->model("UserRole_model");
        $this->load->model('Staff_model');
        $this->load->model('User_model');
    }
    public function jsonList(){
        $this->data['data'] = $this->UserRole_model->get($this->input->post('staff_id'));
        echo json_encode($this->data);
    }

     public function create(){
      $this->load->library('form_validation');
      $this->form_validation->set_rules('role_id', 'Role', 'required');
      $feedback['success'] = false;
      
        if($this->form_validation->run() === FALSE ){
        $feedback['message'] = validation_errors('<li>','</li>');
        
            }else{
                if($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))){ //editing exsting item
    
                  if($this->UserRole_model->update()){
                    $feedback['success'] = true;
                    $feedback['message'] = "Role history successfully updated";

                      $this->helpers->activity_logs($_SESSION['id'],1,"Editing user role",$feedback['message'],NULL,$this->input->post('id'));
                  }else{
                    $feedback['message'] = "Role history could not be updated";
                    
                      $this->helpers->activity_logs($_SESSION['id'],1,"Editing user role",$feedback['message'],NULL,$this->input->post('id'));
                  }
                }else{
                  //adding a new user
                  $return_id = $this->UserRole_model->set();
                  if(is_numeric($return_id)){
                    $feedback['success'] = true;
                    $feedback['message'] = "Role successfully assigned";

                     $this->helpers->activity_logs($_SESSION['id'],1,"Creating user role",$feedback['message'],NULL,$return_id);

                  }else{
                    $feedback['message'] = "There was a problem saving the Role , please contact IT support";

                      $this->helpers->activity_logs($_SESSION['id'],1,"Creating user role",$feedback['message'],NULL,$return_id);

                  }
                }
            }
        echo json_encode($feedback);
    }
    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
       // if (isset($_SESSION['role']) && isset($_SESSION['role']) == 1) {
            if (($response['success'] = $this->UserRole_model->delete($this->input->post('id'))) === true) {
                $response['message'] = "Role successfully deleted";

                 $this->helpers->activity_logs($_SESSION['id'],1,"Deleting",$response['message'],NULL,$_POST['id']);
            }
       // }
        echo json_encode($response);
    }
    
    public function change_status() {
        $msg = $this->input->post('status_id')==1?"":"de";
        $response['message'] = "Role could not be $msg activated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->UserRole_model->deactivate($this->input->post('id'))) {
            $response['message'] = "Role has successfully been $msg activated.";
            $response['success'] = TRUE;
            echo json_encode($response);

             $this->helpers->activity_logs($_SESSION['id'],1,"Changing user role",$response['message'],NULL,$_POST['id']);
        }
    }

}
