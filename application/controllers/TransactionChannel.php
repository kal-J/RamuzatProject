<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransactionChannel extends CI_Controller {
	
    public function __construct() {
		 parent::__construct(); 
            $this->load->library("session");
        $this->load->model("TransactionChannel_model");
    }
    public function jsonList(){
        $this->data['data'] = $this->TransactionChannel_model->get2();
        echo json_encode($this->data);
    }
      public function create(){

        $this->form_validation->set_rules('channel_name', 'Channel Name', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->TransactionChannel_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Transaction channel successfully updated";
                    $feedback['channel'] = $this->TransactionChannel_model->get($_POST['id']);
                    //activity log 

                      $this->helpers->activity_logs($_SESSION['id'],18,"Editing Transaction channel ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));

                } else {
                    $feedback['message'] = "There was a problem updating Transaction channel details";

                      $this->helpers->activity_logs($_SESSION['id'],18,"Editing Transaction channel ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                }
            } else {
                $channel_id = $this->TransactionChannel_model->set();
                if ($channel_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Transaction channel details successfully saved";

                      $this->helpers->activity_logs($_SESSION['id'],18,"Editing Transaction channel ",$feedback['message']." # ".$channel_id,NULL,"id #".$channel_id);
                } else {
                    $feedback['message'] = "There was a problem saving Transaction channel data";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing Transaction channel ",$feedback['message']." # ".$channel_id,NULL,"id #".$channel_id);
                }
            }
        }
        echo json_encode($feedback);
    }

    public function change_status() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
         # $this->helpers->activity_logs($_SESSION['id'],18,"Deactivating Transaction channel ",$data['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));

        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            $this->data['message'] = $this->TransactionChannel_model->deactivate();
            if ($this->data['message'] === true) {
                $this->data['success']= TRUE;
                $this->data['message'] =  "Transaction Channel successfully deactivated";

                 $this->helpers->activity_logs($_SESSION['id'],18,"Deactivating Transaction channel ",$this->data['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
            }
         // }
        echo json_encode($this->data);
    }

    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";

        $response['success'] = FALSE;
          # $this->helpers->activity_logs($_SESSION['id'],18,"Deleting Transaction channel ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
       // if (isset($_SESSION['role']) && isset($_SESSION['role']) == 1) {
            if (($response['success'] = $this->TransactionChannel_model->delete($this->input->post('id'))) === true) {
                $response['message'] = "Transaction Channel successfully deleted";

                  $this->helpers->activity_logs($_SESSION['id'],18,"Deleting Transaction channel ",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
            }
       // }
        echo json_encode($response);
    }
}
