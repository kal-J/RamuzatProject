<?php
defined('BASEPATH') or exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');
class Accounts extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('Loan_guarantor_model');
        $this->load->model("Member_model");
        $this->load->model("Shares_model");
        $this->load->model("Share_issuance_model");
        $this->load->model("share_issuance_fees_model");
        $this->load->model("Savings_account_model");
        $this->load->model("Savings_product_fee_model");
        $this->load->model("DepositProduct_model");
        $this->load->model("Group_member_model");
        
    }

    public function savingsAccount_post()
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
        //fetch accounts
        $this->savingsData['savings_data'] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7 AND a.member_id='.$member_id);
        if (!empty($this->savingsData)) {
            $this->response([
                'status' => TRUE,
                'message' => 'User savings accounts.',
                'data' => $this->savingsData
            ], REST_Controller::HTTP_OK);
        } else {
            // Set the response and exit
            //BAD_REQUEST (400) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'data' => $this->savingsData,
                'message' => 'No accounts registered under this user'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function sharesAccount_post()
    {
        $sharesData = array();
        //fetch accounts
        //$this->data['members'] = $this->Member_model->get_member('member_id=' . $member_id);

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
        $this->data['share_issuances'] = $this->Shares_model->get('share_account.member_id=' . $member_id);
        $sharesData = $this->data;
        if (!empty($sharesData)) {
            $this->response([
                'status' => TRUE,
                'message' => 'User shares accounts.',
                'data' => $sharesData
            ], REST_Controller::HTTP_OK);
        } else {
            // Set the response and exit
            //BAD_REQUEST (400) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No accounts registered under this user'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function viewSavingsAccount_post()
    {

         //checking for post data
         if ($this->input->post('account_id') !== NULL) {
            $account_id = $this->input->post('account_id');
        }else{
            // Set the response and exit
            //BAD_REQUEST (400) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'Provide a valid account id to access account.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        $data['accounts_data'] = $this->Savings_account_model->get('sa.id='.$account_id);

            $this->response([
                'status' => TRUE,
                'message' => 'Savings account.',
                'data' => $data,
            ], REST_Controller::HTTP_OK);
    
       
    }

    public function viewShareAccount_get($share_id)
    {
        $accountData = array();
        //bind up data
        $this->data['share_issuances'] = $this->Share_issuance_model->get(['share_issuance.status_id', 1]);
        $this->data['get_share_by_id'] = $this->Shares_model->get_by_id($share_id);
        // $this->data['share_price_amount'] = $this->data['get_share_by_id']['share_price'] * $this->data['get_share_by_id']['shares'];
        $this->data['share_price_amount'] = 10000;
        $this->data['share_detail'] = $this->Shares_model->get_share_fee($share_id);
        $this->data['share_details'] = $this->Shares_model->get(['share_account.id' => $share_id]);
        $this->data['available_share_fees'] = $this->share_issuance_fees_model->get("shareproduct_id=" . $this->data['share_detail']['feename']);

        $accountData = $this->data;

        $this->response([
            'status' => TRUE,
            'message' => 'User shares accounts.',
            'data' => $accountData,
        ], REST_Controller::HTTP_OK);
    }
}
