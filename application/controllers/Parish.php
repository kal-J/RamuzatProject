<?php

class Parish extends CI_Controller {

    public function __construct() {
        //constructor override...
        parent::__construct();

        $this->load->model('Parish_model');
    }

    public function jsonList() {

        $this->data['parishes'] = $this->Parish_model->get_parishes();
        echo json_encode($this->data);
    }

}
