<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Address extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('Address_model');
    }
    public function jsonList(){
        $this->data['data'] = $this->Address_model->get_addresses();
		//print_r($this->data);
        echo json_encode($this->data);
    }
	public function create() {
        //if user not logged in, take them to the login page
      /*   if (!isset($_SESSION['user_id'])) {
            redirect('user/login');
            return;
        } */
        $this->load->library('form_validation');
		
        $this->form_validation->set_rules('user_id', 'User', 'required');
        $this->form_validation->set_rules('address1', 'Address 1', 'required|trim');
        $this->form_validation->set_rules('address2', 'Address 2', 'trim');
        $this->form_validation->set_rules('address_type_id', 'Address Type','required');
        $this->form_validation->set_rules('village_id', 'Village','required');
        // $this->form_validation->set_rules('start_date', 'Start Date','required');
        if(isset($_POST['end_date'])===true){
            $this->form_validation->set_rules('end_date', 'End Date','min_length[1]|required');
        }
       

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Address_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Address successfully updated";

                       $this->helpers->activity_logs($_SESSION['id'],1,"Editing address",$feedback['message']." # ". $this->input->post('id'),NULL,$this->input->post('id'));
                } else {
                    $feedback['message'] = "Failed, Address details NOT updated";

                     $this->helpers->activity_logs($_SESSION['id'],1,"Editing address",$feedback['message']." # ". $this->input->post('id'),NULL,$this->input->post('id'));
                }
            } else {
                $add = $this->Address_model->set();
                if ($add) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Address successfully saved";

                     $this->helpers->activity_logs($_SESSION['id'],1,"Creating address",$feedback['message'],NULL,$this->input->post('address1'));
              } else {
                    $feedback['message'] = "Failed, couldn't update";

                      $this->helpers->activity_logs($_SESSION['id'],1,"Creating address",$feedback['message'],NULL,$this->input->post('address1'));
                }
            }
        }
        echo json_encode($feedback);
    }
	
	function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            if (($response['success'] = $this->Address_model->delete($this->input->post('id'))) === true) {
                $response['message'] = "Address Details successfully deleted";

                  $this->helpers->activity_logs($_SESSION['id'],1,"Deleting address",$response['message'],NULL,$this->input->post('id'));
            }
       // }
        echo json_encode($response);
    }
	
}

?>
