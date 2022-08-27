<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**

 * Journal Transaction Line Controller
 *  */
class Expense_line extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('expense_line_model');
    }

    public function jsonList() {
        $this->data['data'] = $this->expense_line_model->get();
        echo json_encode($this->data);
    }
    public function jsonList2() {
        //getting transactions for a specific account
        $this->data['data'] = $this->expense_line_model->get2();
        echo json_encode($this->data);
    }
    
    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have the rights to delete this record";
        $response['success'] = FALSE;

        $expense_line_id = $this->input->post('id');
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        if (($response['success'] = $this->expense_line_model->delete($expense_line_id)) === true) {
            $response['message'] = "General journal entry details successfully deleted";
        }
        // }
        echo json_encode($response);
    }

}
