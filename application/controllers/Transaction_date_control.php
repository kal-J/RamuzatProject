<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Transaction_date_control extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('Transaction_date_control_model');
    }

    public function jsonList()
    {
        $data['data'] = $this->Transaction_date_control_model->get("c.status_id=1");
        echo json_encode($data);
    }

    public function create()
    {
        $this->form_validation->set_rules('control_name', 'Control Name', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Transaction_date_control_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Transaction date control successfully updated";
                    $feedback['date_control'] = $this->Transaction_date_control_model->get($_POST['id']);
                    //activity log 

                    # $this->helpers->activity_logs($_SESSION['id'],18,"Editing Transaction channel ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));

                } else {
                    $feedback['message'] = "There was a problem updating Transaction date control details";

                    # $this->helpers->activity_logs($_SESSION['id'],18,"Editing Transaction channel ",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                }
            } else {
                $date_control_id = $this->Transaction_date_control_model->set();
                if ($date_control_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Transaction date control details successfully saved";

                    # $this->helpers->activity_logs($_SESSION['id'],18,"Editing Transaction channel ",$feedback['message']." # ".$channel_id,NULL,"id #".$channel_id);
                } else {
                    $feedback['message'] = "There was a problem saving Transaction date control data";

                    # $this->helpers->activity_logs($_SESSION['id'],18,"Editing Transaction channel ",$feedback['message']." # ".$channel_id,NULL,"id #".$channel_id);
                }
            }
        }

        echo json_encode($feedback);
    }

    public function change_status()
    {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;

        $this->data['message'] = $this->Transaction_date_control_model->deactivate();
        if ($this->data['message'] === true) {
            $this->data['success'] = TRUE;
            $this->data['message'] =  "Transaction Date Control successfully deactivated";

            $this->helpers->activity_logs($_SESSION['id'], 18, "Deactivating Transaction Date Control ", $this->data['message'] . " # " . $this->input->post('id'), NULL, "id #" . $this->input->post('id'));
        }
        echo json_encode($this->data);
    }

    public function delete()
    {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";

        $response['success'] = FALSE;

        if (($response['success'] = $this->Transaction_date_control_model->delete($this->input->post('id'))) === true) {
            $response['message'] = "Transaction Date Control successfully deleted";

            $this->helpers->activity_logs($_SESSION['id'], 18, "Deleting Transaction Date Control ", $response['message'] . " # " . $this->input->post('id'), NULL, "id #" . $this->input->post('id'));
        }
        echo json_encode($response);
    }
}
