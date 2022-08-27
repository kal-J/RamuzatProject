<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**

 * Invoice Payment Line Controller
 *  */
class Invoice_payment_line extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('invoice_payment_line_model');
    }

    public function jsonlist() {
        $data['data'] = $this->invoice_payment_line_model->get();
        echo json_encode($data);
    }
    public function jsonlist2() {
        //getting transactions for a specific account
        $data['data'] = $this->invoice_payment_line_model->get2();
        echo json_encode($data);
    }
    
    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have the rights to delete this record";
        $response['success'] = FALSE;

        $invoice_line_id = $this->input->post('id');
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        if (($response['success'] = $this->invoice_payment_line_model->delete($invoice_line_id)) === true) {
            $response['message'] = "General journal entry details successfully deleted";
        }
        // }
        echo json_encode($response);
    }

}
