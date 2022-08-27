<?php
class Share_category extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        }
        $this->load->model('Share_category_model');
        $this->load->library("helpers");
        $this->data['settings_list'] = $this->helpers->user_privileges($module_id = 11, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 11, $_SESSION['organisation_id']);
        if(empty($this->data['module_access'])){
            redirect('my404');
        } else {
        if (empty($this->data['settings_list'])) {
            redirect('my404');
        } else {
            $this->data['privileges'] = array_column($this->data['settings_list'], "privilege_code");
        }
      }
    }

    public function jsonList() {
        $where = FALSE;
        if ($this->input->post('organisation_id') !== NULL) {
            $where = "organisation_id = " . $this->input->post('organisation_id');
        }
        $this->data['data'] = $this->Share_category_model->get($where);
        echo json_encode($this->data);
    }

    public function create() {
        $this->form_validation->set_rules('category', 'Category name', array('required', 'min_length[2]', 'max_length[300]'), array('required' => '%s must be entered'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Share_category_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Share Category details successfully updated";

                    $this->helpers->activity_logs($_SESSION['id'],18,"Editing share category",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                    
                } else {
                    $feedback['message'] = "There was a problem updating the Share Category ";
                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing share category details",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
              }
            } else {
                $role_id = $this->Share_category_model->set();
                if ($role_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Share Category details successfully saved";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Creating share category ",$feedback['message']." # ".$role_id,NULL,"id #".$role_id);
                } else {
                    $feedback['message'] = "There was a problem saving the Share Category data";
                     $this->helpers->activity_logs($_SESSION['id'],18,"Creating share category ",$feedback['message']." # ".$role_id,NULL,"id #".$role_id);
                }
            }
        }
        echo json_encode($feedback);
    }
    public function view($id){
        $this->data['title'] = $this->data['sub_title'] = "Share Prices";
         $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("fieldset.css","plugins/select2/select2.min.css");
        $this->data['category'] = $this->Share_category_model->get($id);

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        
        $this->template->title = $this->data['title'];
        $this->template->content->view('setting/shares/categories/view', $this->data);
        $this->template->publish();
    }

    public function delete() {
        $response['message'] = "Share Category could not be deleted, IT support.";

         $this->helpers->activity_logs($_SESSION['id'],18,"Deleting share category ",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        $response['success'] = FALSE;
        if ($this->Share_category_model->delete($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] = "Share Category successfully deleted.";

              $this->helpers->activity_logs($_SESSION['id'],18,"Deleting share category ",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        $response['success'] = FALSE;
        }
        echo json_encode($response);
    }

    public function change_status() {
        $msg = $this->input->post('status_id')==1?"":"de";
        $response['message'] = "Share Category could not be $msg activated, contact IT support.";
          $this->helpers->activity_logs($_SESSION['id'],18,"Deactivating share category ",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        $response['success'] = FALSE;
        if ($this->Share_category_model->deactivate($this->input->post('id'))) {
            $response['message'] = "Share Category has successfully been $msg activated.";
            $response['success'] = TRUE;
            
             $this->helpers->activity_logs($_SESSION['id'],18,"Deactivating share category ",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        }
      echo json_encode($response);
    }


}
