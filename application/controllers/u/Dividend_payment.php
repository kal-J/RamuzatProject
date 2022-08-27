<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Dividend Payment Controller for client
 * @author Reagan ajuna
 *  */
class Dividend_payment extends CI_Controller {

    public function __construct() {
       parent::__construct(); 
         $this->load->library("session");
         if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model("dividend_payment_model");
        $this->load->library(array("form_validation", "helpers"));
      
        $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
        
    }

 
    public function get_member_dividends() {
        $record_date = date('Y-m-d');
        $data['data'] = $this->dividend_payment_model->get(false,$record_date);
        echo json_encode($data);
    }


}
