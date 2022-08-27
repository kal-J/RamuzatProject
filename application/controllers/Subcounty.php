<?php

class Subcounty extends CI_Controller {

    public function __construct() {
        //constructor override...
        parent::__construct();

        $this->load->model('Subcounty_model');
    }

    public function jsonList() {

        $this->data['subcounties'] = $this->Subcounty_model->get_subcounties();
        echo json_encode($this->data);
    }

}
