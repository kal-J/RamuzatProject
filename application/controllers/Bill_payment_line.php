<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**

 * Bill Payment Line Controller
 *  */
class Bill_payment_line extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('bill_payment_line_model');
    }

    public function jsonlist() {
        $data['data'] = $this->bill_payment_line_model->get();
        echo json_encode($data);
    }
    public function jsonlist2() {
        //getting transactions for a specific account
        $data['data'] = $this->bill_payment_line_model->get2();
        echo json_encode($data);
    }
    
    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have the rights to delete this record";
        $response['success'] = FALSE;

        $bill_line_id = $this->input->post('id');
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        if (($response['success'] = $this->bill_payment_line_model->delete($bill_line_id)) === true) {
            $response['message'] = "General journal entry details successfully deleted";
        }
        // }
        echo json_encode($response);
    }

}
