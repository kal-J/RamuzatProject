<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Client_loan_doc extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("client_loan_doc_model");
        $this->load->model("user_model");
    }

    public function jsonList() {
        $data['data'] = $this->client_loan_doc_model->get();
        echo json_encode($data);
    }

#id, user_id, name, type, description, date_created, created_by, date_modified, modified_by

    public function create() {
        $this->load->library('form_validation');
        //$this->form_validation->set_rules('file_name', 'Document name', 'required');
        $this->form_validation->set_rules('description', 'Document description', 'required');
        $feedback['success'] = false;

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {
            $data['loan_doc_type_id'] = $this->input->post('loan_doc_type_id');

            $data['description'] = $this->input->post('description');
            $client_loan_id = 'client_loan_id_' . trim($_POST['client_loan_id']);
            //set file upload settings 
            $organisation_id = isset($_SESSION[ 'organisation_id' ]) ? $_SESSION[ 'organisation_id' ] : 0;
            $config['upload_path']          = APPPATH. '../uploads/organisation_'. $organisation_id .'/loan_docs/other_docs/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf';
            $config['max_size'] = 10000;
            $config['remove_spaces'] = false;
            $config['overwrite'] = true;
            $config['file_name'] = $client_loan_id . $_FILES['file_name']['name'];
            $file_name = $config['file_name'];
            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('file_name')) {
                $feedback['message'] = $this->upload->display_errors();
            } else {
                $upload_data = $this->upload->data();
                $data['file_name'] = $upload_data['file_name'];
                $inserted_id = $this->client_loan_doc_model->add_loan_doc($data);

                if ($inserted_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Document has been successfully Added";
                } else {
                    $feedback['message'] = "There was a problem saving the document data, please try again";
                }
             }     
        }
        echo json_encode($feedback);
    }

    public function delete() {
        $feedback['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $feedback['success'] = FALSE;
        if ($this->client_loan_doc_model->delete_by_id() === true) {
            $feedback['success'] = TRUE;
            $feedback['message'] = "Loan document successfully deleted.";
        }
        echo json_encode($feedback);
    }

}
