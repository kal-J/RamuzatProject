<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Guarantor extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }

        $this->load->model("guarantor_model");
    }

    public function create() {
       
        $this->load->library('form_validation');
        if(is_numeric($_POST['guarantor_type']) && $_POST['guarantor_type']==1)
        {
        $member_guarantors= $this->input->post('member_guarantors');

         if (!empty($member_guarantors)){
            // Loop through and add the validation
            foreach ($member_guarantors as $id => $data) {
                $this->form_validation->set_rules('member_guarantors[' . $id . '][member_id]', 'Member', 'required');
                $this->form_validation->set_rules('member_guarantors[' . $id . '][relationship_type_id]', 'Member Relationship', 'required');
            }
            //
        }
         else{
            $this->form_validation->set_rules('member_guarantors', 'Guarantor', 'required');
        }
        }

        if(is_numeric($_POST['guarantor_type']) && $_POST['guarantor_type']==2)
        {
        $member_guarantors2= $this->input->post('member_guarantors2');
       
         if (!empty($member_guarantors2)){
            // Loop through and add the validation
              foreach ($member_guarantors2 as $id => $data) {
                $this->form_validation->set_rules('member_guarantors2[' . $id . '][firstname]', 'First Name', 'required');
                $this->form_validation->set_rules('member_guarantors2[' . $id . '][lastname]', 'Last Name', 'required');
                $this->form_validation->set_rules('member_guarantors2[' . $id . '][gender]', 'Gender', 'required');
                $this->form_validation->set_rules('member_guarantors2[' . $id . '][mobile_number]', 'Phone Number', 'required');
                $this->form_validation->set_rules('member_guarantors2[' . $id . '][nin]', 'NIN', 'required');
               // $this->form_validation->set_rules('member_guarantors2[' . $id . '][attachment]', 'Copy of ID', 'required');
                $this->form_validation->set_rules('member_guarantors2[' . $id . '][comment]', 'Comment', 'required');
                $this->form_validation->set_rules('member_guarantors2[' . $id . '][relationship_type_id]', 'Member Relationship', 'required');
            }
       
        }
         
         else{
            $this->form_validation->set_rules('member_guarantors2', 'Guarantor', 'required');
        }
        
        }
          if ($this->input->post('member_guarantors2') != NULL && $this->input->post('member_guarantors2') != '') { //need to upload
                    $organisation_id = isset($_SESSION['organisation_id']) ? $_SESSION['organisation_id'] : 0;
                    $location = 'organisation_' . $organisation_id . '/attachments/';
                    $member_guarantors2 = $this->input->post('member_guarantors2');
                  
                    #uploading the files
                     if (isset($_FILES)) {
                            foreach ($member_guarantors2 as $key => $value) {
                                  
                                $file[$key] = '';
                                  $file_name = $_FILES['file_name']['name'][$key];
                                    //if (!empty($file_name)) {
                                       $file[$key] = $this->do_upload($file_name, $location);
                                        $this->data['document_name']=$file_name;
                                    //}
                                        
                                    }
                                }
                            //}
                        }
                //}

       
        $feedback = array();
        $feedback['success'] = false;
        $feedback['message'] = "Failed to add Guarantor, contact system admin";

        if($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {
             
            
                #Adding Members as guarantors i.e without savings or shares attached
                if ($this->guarantor_model->add()){
                    $feedback['success'] = true;
                    $feedback['message'] = "Guarantor has been added successfully";
                }
            }
         

        echo json_encode($feedback);
    }
  
  private function do_upload($file_name=array(),$location,$max_size = 2048,$allowed_types = "gif|jpg|jpeg|png|pdf")
    {
        //uploading of the file
        if (!empty($file_name) && !empty($location)){
            $config['upload_path'] = APPPATH . "../uploads/$location/";
            $document_name= $config['file_name'] = $file_name;
            $config['allowed_types'] = $allowed_types;
            $config['max_size'] = $max_size;
            $config['overwrite'] = true;
            $config['remove_spaces'] = false;
            $config['file_ext_tolower'] = true;
            $this->load->library('upload', $config);
            if(!$this->upload->do_multi_upload('file_name')){
                return $this->upload->display_errors();
            }
            else{
                $this->upload->data();
                return  $document_name;
                }
        }
    }
    

    public function jsonList(){
        $this->data['data'] = $this->guarantor_model->get('a.status_id=1');
        echo json_encode($this->data);
    }

    public function delete() {
        $feedback['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $feedback['success'] = FALSE;

        if ($this->guarantor_model->delete_by_id() === true) {
            $feedback['success'] = TRUE;
            $feedback['message'] = "Guarantor Removed from this Loan.";
        }

        echo json_encode($feedback);
    }
}
