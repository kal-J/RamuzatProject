<?php
defined('BASEPATH') or exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');
class Transactions extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('Transaction_model');
        $this->load->model('organisation_model');
        $this->load->model('Loan_guarantor_model');
        $this->load->model('Group_member_model');
        $this->load->model('Share_transaction_model');
        $this->load->model('Api_model');

    }


    public function savingsTransactions_post()
    {
        $transactions = array();
        //fetch transactions
        
        //checking for post data
        if ($this->input->post('account_id') !== NULL ) {
            $account_id = $this->input->post('account_id');
        }else{
            // Set the response and exit
            //BAD_REQUEST (400) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'Provide a valid account id please.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
        
        $where = "account_no_id = " . $account_id;

        $transactions['data'] = $this->Api_model->get(2,"tn.account_no_id=".$account_id);

        
        $this->response([
            'status' => TRUE,
            'message' => 'Savings transactions',
            'data' => $transactions
        ], REST_Controller::HTTP_OK);

    }

    public function sharesTransactions_post()
    {
        if ($this->input->post('share_account_id') !== NULL && 

        $this->input->post('status_id') !== NULL) {

            $acc_id = $this->input->post('share_account_id');
            $status_id = $this->input->post('status_id');
            $where = "share_account_id = ".$acc_id." AND  tn.status_id =". $status_id;

        }else{
            // Set the response and exit
            //BAD_REQUEST (400) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'Provide account id please and status id.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        $this->data['data'] = $this->Share_transaction_model->get($where);

        //pick starting at five elements before the end of the array
        $transactions = array_slice($this->data, -5);

        $this->response([
            'status' => TRUE,
            'message' => 'User login successful.',
            'data' => $transactions
        ], REST_Controller::HTTP_OK);
    }
}
