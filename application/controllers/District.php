<?php

class District extends CI_Controller {

    public function __construct() {
        //constructor override...
        parent::__construct();
       $this->load->library("session");
        $this->load->model('District_model');
    }

    public function index() {

        $this->data['districts'] = $this->District_model->get_districts();
        echo json_encode($this->data);
    }

}
