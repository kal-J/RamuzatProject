<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Applied_share_fee extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("applied_share_fee_model");
    }
    
    public function jsonList() {
        $data['data'] = $this->applied_share_fee_model->get();
        echo json_encode($data);
    }


    public function create() {
        $shareFee = $this->input->post('shareFee');
        if (empty($shareFee)) {
            $feedback['success'] = false;
            $feedback['message'] = "All fields are required";
        } else {
            $transaction_no = $this->generate_transaction_no(); 
            if ($this->applied_share_fee_model->set($transaction_no)) {                
                $feedback['success'] = true;
                $feedback['message'] = "Share fee(s) successfully applied";
            } else {
                $feedback['success'] = false;
                $feedback['message'] = "There was a problem applying the share fees";
            }
        }
        echo json_encode($feedback);
    }

    public function change_status() {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Applied share fee data could not be $msg activated, contact IT support.";
        $response['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            if ($this->applied_share_fee_model->change_status_by_id($this->input->post('id'))) {
                $response['message'] = "Applied share fee data has successfully been $msg Deactivated.";
                $response['success'] = TRUE;
                echo json_encode($response);
            }
        //}
    }

    public function generate_transaction_no() {
        $this->load->model( 'organisation_format_model' );
        $this->data['transaction_no_format'] =$this->organisation_format_model->get_transaction_format();
        $org_id = $this->data['transaction_no_format']['id'];
        $counter =  $this->data['transaction_no_format']['counter_applied_share_fees'];
        $letter =  $this->data['transaction_no_format']['letter_applied_share_fees'];
        $initial =  $this->data['transaction_no_format']['org_initial'];
        if ($counter == 999999999999) {
                $letter++;
                $counter=0;
            }
        $transaction = $initial . 'AS'.sprintf("%012d", $counter + 1) . $letter;
        $this->db->where('id',$org_id);
        $upd = $this->db->update('fms_organisation', ["counter_applied_share_fees"=> $counter+1,"letter_applied_share_fees"=> $letter]);
        return $transaction;
    }

    public function delete() {
            $response['success'] = FALSE;
            if ($this->applied_share_fee_model->delete_by_id()) {
                $response['success'] = TRUE;
                $response['message'] = "Data successfully deleted.";
            }
        echo json_encode($response);
    }

    public  function pdf( $member_id, $share_id=false, $transaction_no=false )
    {
        $this->load->model( 'member_model' );
        $this->load->helper('pdf_helper');
        $data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = "Share fees";
        $data['receipt_items'] = $this->applied_share_fee_model->get( "transaction_no = '".$transaction_no."'"  );
        $data['single_receipt_items'] = $this->applied_share_fee_model->get( "transaction_no = '".$transaction_no."' group by '".$transaction_no."'"  );
        $data['member'] = $this->member_model->get_member($member_id);
        $data['receipt_item_sum'] = $this->applied_share_fee_model->get_sum( "transaction_no = '".$transaction_no."'" );
        $data['the_page_data'] = $this->load->view('shares/fees/pdf', $data, TRUE);
        $this->load->view('includes/pdf_template', $data);
    }

}
