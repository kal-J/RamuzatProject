
<?php

class Village extends CI_Controller {

    public function __construct() {
        //constructor override...
        parent::__construct();

        $this->load->model('Village_model');
    }

    public function jsonList() {

        $this->data['villages'] = $this->Village_model->get_villages();
        echo json_encode($this->data);
    }

   

}
