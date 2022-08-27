<?php
class Role extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('role_model');
    }

    public function index() {
        //error_reporting(0);
        $this->load->library("helpers");

        $this->data['title'] = $this->data['sub_title'] = 'Sacco roles';

        $this->template->title = $this->data['title'];

        // Load a view in the content partial
        $this->data['add_modal'] = $this->load->view('role/add_modal', $this->data, TRUE);
        $this->template->content->view('role/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function jsonList() {
        $where = FALSE;
        if ($this->input->post('organisation_id') !== NULL) {
            $where = "organisation_id = " . $this->input->post('organisation_id');
        }
        $this->data['data'] = $this->role_model->get($where);
        echo json_encode($this->data);
    }

    public function create() {
        $this->form_validation->set_rules('role', 'Role name', array('required', 'min_length[2]', 'max_length[30]'), array('required' => '%s must be entered'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->role_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Role details successfully updated";
                    $feedback['role'] = $this->role_model->get($_POST['id']);
                    //activity log 

                      $this->helpers->activity_logs($_SESSION['id'],18,"Editing role details",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the role details";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing role details",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
              }
            } else {
                $role_id = $this->role_model->set();
                if ($role_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Role details successfully saved";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Creating role details",$feedback['message']." # ".$role_id,NULL,"id #".$role_id);
                } else {
                    $feedback['message'] = "There was a problem saving the role data";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Creating role details",$feedback['message']." # ".$role_id,NULL,"id #".$role_id);
                }
            }
        }
        echo json_encode($feedback);
    }

    public function delete() {
        $response['message'] = "Role could not be deleted, IT support.";
        $response['success'] = FALSE;
          $this->helpers->activity_logs($_SESSION['id'],18,"Deleting role details",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));

        if ($this->role_model->delete($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] = "Role successfully deleted.";

              $this->helpers->activity_logs($_SESSION['id'],18,"Deleting role details",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        }
        echo json_encode($response);
    }

    public function change_status() {
        $msg = $this->input->post('status_id')==1?"":"de";
        $response['message'] = "Role could not be $msg activated, contact IT support.";
        $response['success'] = FALSE;

          $this->helpers->activity_logs($_SESSION['id'],18,"Deleting role details",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        if ($this->role_model->deactivate($this->input->post('id'))) {
            $response['message'] = "Role has successfully been $msg activated.";
            $response['success'] = TRUE;
            
              $this->helpers->activity_logs($_SESSION['id'],18,"Deleting role details",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        }
      echo json_encode($response);
    }


}
