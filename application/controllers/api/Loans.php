<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');
class Loans extends REST_Controller
{

       public function __construct() {
               parent::__construct();
               $this->load->model('user_model');
               $this->load->model('client_loan_model');
               $this->load->model('Api_model');
       }

    public function loanAccounts_post()
    {

        //checking for post data
        if ($this->input->post('member_id') !== NULL) {
                $member_id = $this->input->post('member_id');
            }else{
                // Set the response and exit
                //BAD_REQUEST (400) being the HTTP response code
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide a valid member id to access account.'
                ], REST_Controller::HTTP_NOT_FOUND);
            }

         $data['accounts_data'] = $this->client_loan_model->get_loans('a.member_id='.$member_id);

         $this->response([
                'status' => TRUE,
                'message' => 'Loans available',
                'data' => $data,
            ], REST_Controller::HTTP_OK);
    }

    public function accountsDetails_post()
    {
         //checking for post data
         if ($this->input->post('loan_id') !== NULL) {
                $loan_id = $this->input->post('loan_id');
            }else{
                // Set the response and exit
                //BAD_REQUEST (400) being the HTTP response code
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide a valid Loan id to access account.'
                ], REST_Controller::HTTP_NOT_FOUND);
            }

         $data['accounts_data'] = $this->client_loan_model->get_loans('a.id='.$loan_id);
         $this->response([
                'status' => TRUE,
                'message' => 'Loans available',
                'data' => $data,
            ], REST_Controller::HTTP_OK);
    }
}         