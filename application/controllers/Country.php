<?php

class Country extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('country_model');
    }

    public function create() {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('country_name', 'Country', 'required');

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (is_numeric($this->input->post('id'))) {
                if (($feedback['success'] = $this->country_model->set())) {
                    $feedback['message'] = "Country successfully updated";
                } else {
                    $feedback['message'] = "Failed, Country details NOT updated";
                }
            } else {
                $add = $this->country_model->set();
                if ($add) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Country successfully saved";
                } else {
                    $feedback['message'] = "Failed, couldnt update country details";
                }
            }
        }
        echo json_encode($feedback);
    }

}
