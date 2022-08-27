<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Invoice Line Controller
 * @author Allan J. Odeke <allanjodeke@gmtconsults.com>
 *  */
class Invoice_line extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('invoice_line_model');
    }

    public function jsonlist() {
        $this->data['data'] = $this->invoice_line_model->get();
        echo json_encode($this->data);
    }
    public function jsonlist2() {
        //getting transactions for a specific account
        $this->data['data'] = $this->invoice_line_model->get2();
        echo json_encode($this->data);
    }
    
    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have the rights to delete this record";
        $response['success'] = FALSE;

        $invoice_line_id = $this->input->post('id');
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        if (($response['success'] = $this->invoice_line_model->delete($invoice_line_id)) === true) {
            $response['message'] = "General journal entry details successfully deleted";
        }
        // }
        echo json_encode($response);
    }

}
