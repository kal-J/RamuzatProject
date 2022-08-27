<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Document extends CI_Controller {
	
    public function __construct() {
     parent::__construct(); 
     $this->load->library("session");
     if(empty($this->session->userdata('id'))){
      redirect('welcome');
     } 
        $this->load->model("document_model");
        $this->load->model("user_model");
    }
    public function jsonList(){
        $this->data['data'] = $this->document_model->get();
        echo json_encode($this->data);
    }
#id, user_id, name, type, description, date_created, created_by, date_modified, modified_by
    public function create(){
      $this->load->library('form_validation');
      //$this->form_validation->set_rules('document_name', 'Document name', 'required');
      $this->form_validation->set_rules('description', 'Document description', array('required', 'min_length[2]', 'max_length[80]'), array('required' => '%s must be entered'));
      $feedback['success'] = false;
      
        if($this->form_validation->run() === FALSE ){
        $feedback['message'] = validation_errors('<li>','</li>');
            }else{
                    $this->data['document_type_id'] = $this->input->post('document_type_id');
                    $this->data['description'] = $this->input->post('description');
                    //set file upload settings 
                    $organisation_id = $_SESSION[ 'organisation_id'];
                    $user_id = 'user_id_' . trim($_POST['user_id']);
                    $config['upload_path']  = APPPATH. '../uploads/organisation_'.$organisation_id.'/user_docs/other_docs/';
                    $config['file_name']    = $user_id.$_FILES['document_name']['name'];
                    $config['allowed_types']= 'docx|doc|gif|jpg|png|pdf';
                    $config['max_size'] = 10000;
                    $config['max_width'] = 0;
                    $config['max_height'] = 0;
                    $config['remove_spaces'] = false;
                    $config['overwrite'] = true;
                    $config['encrypt_name']= false;

                    $document_name = $config['file_name'];
                    $this->load->library('upload', $config);
                    if(!$this->upload->do_upload('document_name')){
                      $error =  $this->upload->display_errors();
                      $feedback['message']= $error;
                     
                    }else{
                      $feedback['success'] = true;
                      $upload_data = $this->upload->data();
                      //get the uploaded file name
                      $this->data['document_name'] = $document_name;
                      $this->save_document_data($this->data);  
                    }
           
            }
        echo json_encode($feedback);
    }

    private function save_document_data($data){
      $feedback=[];
      $feedback['success'] = false;
      if($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))){ //editing exsting item
        if($this->document_model->update_document($data)){
              $feedback['success'] = true;
              $feedback['message'] = "Document Details successfully updated";
              $feedback['document' ]= $this->document_model->get($this->input->post('user_id'));
            }else{
              $feedback['message'] = "There was a problem updating the document data, please try again";
            }
    }else{

        //adding a new item
        $inserted_id = $this->document_model->add_document($data);

            if($inserted_id){
              $feedback['success'] = true;
              $feedback['message'] = "Document has been successfully Added";
              $feedback['document']=$inserted_id;
            }else{
              $feedback['message'] = "There was a problem saving the document data, please try again";
            }
    }
    return $feedback;
    }
public function delete() {
  $response['message'] = "Data could not be deleted, document support.";
  $response['success'] = FALSE;
  if($this->document_model->delete_by_id()){
    $response['success'] = TRUE;
    $response['message'] = "User document successfully deleted.";
  }
  echo json_encode($response);
}



  
}
