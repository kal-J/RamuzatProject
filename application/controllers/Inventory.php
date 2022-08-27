<?php
/**
 * Description of INVENTORY
 *
 * @author reagan
 */
class Inventory extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->data['privilege_list'] = $this->helpers->user_privileges(8, $this->session->userdata('staff_id'));
        $this->data['fiscal_list'] = $this->helpers->user_privileges(20, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module(8, $_SESSION['organisation_id']);
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['accounts_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
            $this->data['fiscal_privilege'] = array_column($this->data['fiscal_list'], "privilege_code");
        }
        $this->load->model('inventory_model');
        $this->load->model('miscellaneous_model');
        $this->load->model('accounts_model');
        $fiscal_year = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
            if(empty($fiscal_year)){
                redirect('dashboard');
            }else{
            $this->data['fiscal_year'] = array_merge($fiscal_year,['start_date2'=>date("d-m-Y", strtotime($fiscal_year['start_date'])),'end_date2'=>date("d-m-Y", strtotime($fiscal_year['end_date']))]);
            $this->data['lock_month_access'] = $this->helpers->org_access_module($module_id = 23, $_SESSION['organisation_id']);
                if(!empty($this->data['lock_month_access'])){
                    $this->data['active_month'] = $this->Fiscal_month_model->get_active_month();
                    if(empty($this->data['active_month'])){
                       redirect('dashboard');
                    }
                } 
            }
        }

    public function index() {
        $this->load->model('transactionChannel_model');
        $this->load->model('staff_model');
        $this->load->model('country_model');
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js");

        $neededcss = array("fieldset.css", "plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['account_list'] = $this->accounts_model->get();
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 5');
        $this->data['transaction_channels'] = $this->transactionChannel_model->get();
        $this->data['depre_appre_type'] = $this->miscellaneous_model->depre_appre_type();
        $this->data["subcat_list"] = $this->accounts_model->get_subcat_list();
        $this->data["depreciation_method"] = $this->miscellaneous_model->get_depreciation_method();
        $this->data['countries'] = $this->country_model->get();
        $this->data['staff_list'] = $this->staff_model->get_registeredby("status_id=1");
     

       // print_r($this->data['investment_data']);die();
        

        $this->data['title'] = $this->data['sub_title'] = "Asset Management";
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('inventory/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function jsonList_fixed_asset() {
        $where = "a.status_id IN(1,4)";
        if ($this->input->post('organisation_id') !== NULL) {
            $where = "organisation_id = " . $this->input->post('organisation_id')."AND a.status_id IN(1,4)";
        }
        $data['data'] = $this->inventory_model->get($where);
       
        echo json_encode($data);
    }

    public function asset($id){
        $this->load->model('transactionChannel_model');
        $this->load->model('user_income_type_model');
        $this->load->model('user_expense_type_model'); 
        $this->load->model('asset_payment_model'); 
        $this->load->model('depreciation_model'); 


        $neededcss = array("fieldset.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $asset_paid_amount=$this->asset_payment_model->sum_payment("asset_id=$id AND status_id =1 AND transaction_type_id =2");
        $this->data['asset_paid_amount'] =$asset_paid_amount['total_payment'];
        
         $this->data['fixed_asset'] = $this->inventory_model->get($id);
         
        $purchase_date = date('Y',strtotime($this->data['fixed_asset']['purchase_date']));
        $date_range = range($purchase_date, date('Y'));
        $i=0;

        foreach($date_range as  $value) {
         $yearList[$i]['year']=$value;
          if($i++){
          }
        }
    
        $this->data['years']=$yearList;
        
      
   
        if (empty($this->data['fixed_asset'])) {
            redirect("my404");
        }
        $this->data['account_list'] = $this->accounts_model->get();
        $this->data["subcat_list"] = $this->accounts_model->get_subcat_list();
        $this->data['income_items'] =  $this->user_income_type_model->get();
        $this->data['expense_items'] = $this->user_expense_type_model->get();
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id <> 5');
        $this->data['transaction_channels'] = $this->transactionChannel_model->get();
        $this->data['title'] = $this->data['sub_title'] = $this->data['fixed_asset']['asset_name'];
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('inventory/fixed_asset/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

     public function create_asset() {
        $this->form_validation->set_rules('description', 'description', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('asset_name', 'Asset Name', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('asset_account_id', 'Asset Account', array('required'), array('required' => '%s must be selected'));
        $this->form_validation->set_rules('depre_appre_id', 'Depreciation / Appreciation', array('required'), array('required' => '%s must be selected'));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $this->load->model('ledger_model');
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->inventory_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Asset details successfully updated";
                    $feedback['assets'] = $this->inventory_model->get($_POST['id']);
                   

                     $this->helpers->activity_logs($_SESSION['id'],8,"Editing Asset details",$feedback['message'],$this->input->post('id'),$this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the assets details";
                    $this->helpers->activity_logs($_SESSION['id'],8,"Editing Asset details",$feedback['message'],$this->input->post('id'),$this->input->post('id'));
                }
            } else {
                $asset_id = $this->inventory_model->set();
                if (is_numeric($asset_id)) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Asset details successfully saved";

                    $this->helpers->activity_logs($_SESSION['id'],8,"Creating Asset details",$feedback['message']." # ".$this->input->post('identity_no'),$asset_id,$asset_id);
                } else {
                    $feedback['message'] = "There was a problem saving the assets data";

                    $this->helpers->activity_logs($_SESSION['id'],8,"Creating Asset details",$feedback['message']." # ".$this->input->post('identity_no'),$this->input->post('identity_no'),$asset_id);

                }
            }
        }
        echo json_encode($feedback);
    }
   //monthly depreciation /Appreciation payment dates.
    public function monthlyDepreAprePayDate(){
        $this->load->model('Depreciation_model');
        $this->load->model('Appreciation_model');
        $this->load->model('Inventory_model');
        $startDate = $this->data['fiscal_year']['start_date'];
        $endDate   = $this->data['fiscal_year']['end_date'];
        $asset_id = (int) $this->input->post('id');
        $depre_appre_id= (int) $this->input->post('depre_appre_id');
       
        $assetDetails = $this->Inventory_model->get_last_depre_appre_tnx_date($depre_appre_id==2 ? "fms_appreciation":"fms_depreciation",$asset_id);
        if($assetDetails[0]['transaction_date'] !=null){
        $lastTransactionDate =$assetDetails[0]['transaction_date'];
        $startAtFinancialYear =false;
        }
        else{
        $lastTransactionDate = $startDate;
        $startAtFinancialYear =true;
        }
        $yearMonthDate = explode("-",$lastTransactionDate);
        $year =intval($yearMonthDate[0]);
        $monthPaid =intval($yearMonthDate[1]);
        $nextTransactionMonth =$startAtFinancialYear==false? intval($monthPaid)+1:intval($monthPaid);
        if($nextTransactionMonth !=0 && $nextTransactionMonth<=12){
        $nextPayMonth= intval($nextTransactionMonth);
        }
        //generate the 
        $number_of_days = cal_days_in_month(CAL_GREGORIAN, $nextPayMonth, $year); 
        $nextPayMonth = strlen($nextPayMonth)==2 ? $nextPayMonth:"0".$nextPayMonth;
        for($i=1;$i<=$number_of_days;$i++){ 
        $next_available_tnx_month[] =  strlen($i)==1?"0".$i."-".$nextPayMonth."-".$year:$i."-".$nextPayMonth."-".$year;
        }
        echo json_encode($next_available_tnx_month);
            }
}
