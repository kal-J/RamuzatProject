<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DepositReturns extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model("Savings_account_model");
        $this->load->model("DepositProduct_model");
        $this->load->model("client_loan_model");
        $this->load->model("Savings_product_fee_model");
        $this->load->model("Staff_model");
        $this->load->model("Organisation_format_model");
        $this->load->model("Member_model");
        $this->load->model("miscellaneous_model");
        $this->load->model("Transaction_model");
        $this->load->model("Transaction_date_control_model");
        $this->load->model("Fiscal_month_model");
        $this->load->model("TransactionChannel_model");
        $this->load->model("DepositReturns_model");
        $this->load->model("Group_member_model");
        $this->load->model("saving_fees_model");
        $this->load->model("RolePrivilege_model");
        $this->load->library(array("form_validation", "helpers"));
        $this->data['allowed_transaction_dates'] = $this->Transaction_date_control_model->generate_allowed_dates();
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 6, $_SESSION['staff_id']);
        $this->data['share_list'] = $this->helpers->user_privileges($module_id = 12, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 6, $_SESSION['organisation_id']);
        if (empty($this->data['module_access'])) {
            redirect('my404');
        } else {
            if (empty($this->data['privilege_list'])) {
                redirect('my404');
            } else {
                $this->data['savings_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
                $this->data['share_privilege'] = array_column($this->data['share_list'], "privilege_code");
            }
            $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
            if (empty($this->data['fiscal_active'])) {
                redirect('dashboard');
            } else {

                $this->data['lock_month_access'] = $this->helpers->org_access_module($module_id = 23, $_SESSION['organisation_id']);
                if (!empty($this->data['lock_month_access'])) {
                    $this->data['active_month'] = $this->Fiscal_month_model->get_active_month();
                    if (empty($this->data['active_month'])) {
                        redirect('dashboard');
                    }
                }
            }
        }
    }

    public function index() {
        $this->load->library("num_format_helper");

        $this->data['acc_id'] = "";    //used to hide transaction tab
        $this->data['switch_deposit_modal'] = 'oustide';
        $this->data['tchannel'] = $this->TransactionChannel_model->get();
        $this->data['products'] = $this->DepositProduct_model->get("sp.status_id=1");
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id IN(1,2,4,6,7,8)');
        //$this->data['format_types'] = $this->Organisation_format_model->get_format_types(FALSE, ['account_format']);
        $this->data['type'] = $this->data['sub_type'] = 'savings';
        $this->data['new_account_no'] = $this->num_format_helper->new_savings_acc_no();
        //echo $this->data['new_account_no']; die();
        //$this->data['organisation'] = $this->Organisation_format_model->get_formats();
        $this->data['sorted_clients'] = $this->Savings_account_model->get_clients('status_id=1');
        // print("<pre>");
        // print_r($this->data['sorted_clients']); die;
        $this->data['ac_state_totals'] = $this->Savings_account_model->state_totals();
        $this->data['available_savings_range_fees'] = $this->saving_fees_model->get_range_fees();

        $this->data['available_interest_range_rates'] = $this->DepositProduct_model->get_range_rates();

        $this->data['title'] = $this->data['sub_title'] = 'Savings Accounts';

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js", "plugins/highcharts/code/highcharts.js", "plugins/highcharts/code/highcharts-3d.js", "plugins/highcharts/code/modules/exporting.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/highslide-full.min.js", "plugins/highcharts/code/modules/export-data.js", "plugins/highcharts/code/modules/series-label.js", "plugins/axios/axios.min.js", "plugins/printjs/print.min.js");

        $neededcss = array("plugins/select2/select2.min.css", "plugins/highcharts/code/css/highslide.css", "plugins/daterangepicker/daterangepicker-bs3.css");

        $this->data['savings_data'] = $this->DepositReturns_model->get_savings_account('j.state_id=7');
        $total_savings = 0;
        foreach ($this->data['savings_data'] as $key => $value) {
            $total_savings = $total_savings + $value['real_bal'];
        }
        $this->data['total_savings'] = $total_savings;
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);

        // $this->data['access_side']='Savings Account';

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->title = $this->data['title'];
        // Load a view in the content partial
        $this->template->content->view('reports/savings/index', $this->data);
        // Publish the template
        $this->template->publish();
    }


    public function jsonList() { //used for fetching union of members and groups
        $data['data'] = $this->DepositReturns_model->get_savings_account('j.state_id='. $this->input->post('state_id'));
        echo json_encode($data);
    }
    public function locked_savings() { //used for fetching rows of lock amount
        $amount_range =  [["start" => 0, "end" => 100000],["start" => 100000, "end" => 500000],["start" => 500000, "end" => 1000000],["start" => 1000000, "end" =>null]]; 

        $response = [];

        foreach($amount_range as $key => $value ){
            $start = $value['start'];
            $end = $value['end'];
            $data = $this->DepositReturns_model->get_locked_savings($start, $end);
            // [0 - 10000] => [{}, {}]
            $name = $start. " - ".$end;
            array_push($response,[$name => $data] );
        }

        return $response;
        //echo json_encode($response);
        
        
    }

    public function fixed_savings() { //used for fetching rows of fixed savings
        $amount_range =  [["start" => 0, "end" => 100000],["start" => 100000, "end" => 500000],["start" => 500000, "end" => 1000000],["start" => 1000000, "end" =>null]]; 

        $response = [];

        foreach($amount_range as $key => $value ){
            $start = $value['start'];
            $end = $value['end'];
            $data = $this->DepositReturns_model->get_fixed_savings($start, $end);

            // [0 - 10000] => [{}, {}]
            $name = $start. " - ".$end;
            array_push($response,[$name => $data] );
        }

        return $response;
        //echo json_encode($response);
        
        
    }

    public function combined_response() {
        $terms = $this->fixed_savings();
        $non_withdrawables = $this->locked_savings();
        $saving = $this->savings_account2();
        $ranges = [];
        // $ranges2 = [];
        $response = [];
        // [[0-1000] => ["terms" => $terms, "saving" => $saving], []]
        foreach($terms as $key => $value){
            foreach($value as $ke => $va){
                if(in_array($ke,$ranges)){
                    $key = array_search($ke, $response); 
                   array_push($response[$ke]['term'],$va);
                }else {
                    array_push($ranges, $ke);
                    array_push($response, [$ke => ['term' => $va]]);

                }
            }
        }
        $i = 0;
        foreach($saving as $key => $value){
            foreach($value as $ke => $va){
                if(in_array($ke,$ranges)){
                       $response[$i][$ke]['savings'] = $va;
                }else {
                    array_push($ranges, $ke);
                    array_push($response, [$ke => ['savings' => $va]]);
                    
                }
                $i += 1;
            }
        }
        $i = 0;
        foreach($non_withdrawables as $key => $value){
            foreach($value as $ke => $va){
                if(in_array($ke,$ranges)){
                       $response[$i][$ke]['non_withdrawable'] = $va;
                }else {
                    array_push($ranges, $ke);
                    array_push($response, [$ke => ['non_withdrawable' => $va]]);
                    
                }
                $i += 1;
            }
        }


        return $response;
        // echo json_encode($response);
    }

    public function testout() {
        $dataArray = $this->combined_response();
        $response = [];
        foreach($dataArray as $key=>$value){
            foreach($value as $ke => $va){
                if($ke == '0-100000'){
                    $data = [
                       "Less than 100000" => [
                           "non_withdrawable" => [
                               "number_of_accounts" => $va['non_withdrawable']['count_locked_savings'],
                               "total" => $va['non_withdrawable']['total']
                           ],
                           "savings" => [
                            "number_of_accounts" => $va['savings']['count_savings_account'],
                               "total" => $va['savings']['total']
                           ],
                           "terms" => [
                               "number_of_accounts" => $va['term']['count_fixed_savings'],
                               "total" =>$va['term']['total']
                           ]
                       ],
                       
                   ];
               }else {
                $data = [
                    $ke => [
                        "non_withdrawable" => [
                            "number_of_accounts" => $va['non_withdrawable']['count_locked_savings'],
                            "total" => $va['non_withdrawable']['total']
                        ],
                        "savings" => [
                         "number_of_accounts" => $va['savings']['count_savings_account'],
                            "total" => $va['savings']['total']
                        ],
                        "terms" => [
                            "number_of_accounts" => $va['term']['count_fixed_savings'],
                            "total" => $va['term']['total']
                        ]
                    ],
                    
                ];
               }

               array_push($response, $data);

            }
        }

        echo json_encode($response);

    }
    public function all_totals() {
        $test_arry = $this->testout();
        $ranges = [];
        // $ranges2 = [];
        $response = [];
        $total = 0;
        $ic = 0;
       

        //return $response;
        //echo json_encode($response);
    }
    public function savings_account2() { //used for fetching  savings accounts
        $amount_range =  [["start" => 0, "end" => 100000],["start" => 100000, "end" => 500000],["start" => 500000, "end" => 1000000],["start" => 1000000, "end" =>null]]; 

        $response = [];

        foreach($amount_range as $key => $value ){
            $start = $value['start'];
            $end = $value['end'];
            $data = $this->DepositReturns_model->get_savings_account2($start, $end);

            // [0 - 10000] => [{}, {}]
            $name = $start. " - ".$end;
            array_push($response,[$name => $data] );
        }

        return $response;
        //echo json_encode($response);
           
        }

        
        
        
    
    

    public function account_list() { //used for fetching union of members and groups for dashboard
        $data['data'] = $this->DepositReturns_model->get_savings_account('j.state_id IN (5,7)');
        echo json_encode($data);
    }

    

    
  

    

    public function channel_balance($account_id = false) {
        $amount = $this->input->post('amount');
        $message = $this->helpers->account_balance($account_id, $amount);
        return $message;
    }


    public function get_savings_accounts() {
        //fetch savings_accounts  
        $this->data['savings_accounts'] = $this->DepositReturns_model->get_savings_account('j.state_id=7 AND a.id!=' . $this->input->post('account_no_id'));
        $response['savings_accounts'] = $this->data['savings_accounts'];
        echo json_encode($response);
    }

   
   
    
}
