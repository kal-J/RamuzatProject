<?php

/**
 * Description of fiscal month
 *
 * @author reagan
 */
class Fiscal_month extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model('Fiscal_month_model');
        $this->load->model('Dashboard_model');
        $this->load->model('journal_transaction_model');
        $this->load->model('journal_transaction_line_model');
    }

    public function jsonList() {
        $this->data['data'] = $this->Fiscal_month_model->get();
        echo json_encode($this->data);
    }

    public function create() {
        $this->form_validation->set_rules('month_id', 'Month', 'required');
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if($this->Fiscal_month_model->check_if_month_exist()<=0){
            $month_id = $this->Fiscal_month_model->set($this->month_start_end_dates());
            if ($month_id) {
                $feedback['success'] = true;
                $feedback['message'] = "Month details successfully saved";
            } else {
                $feedback['message'] = "There was a problem setting up a new month";
            }
          } else{
                $feedback['message'] = "Month already exists in ".$_POST['year'];
          }
            
        }
        echo json_encode($feedback);
    }

  public function close_fiscal_month() {
        $feedback = ['message' => "Access denied, you do not have the appropriate privileges to perform this operation", "success" => false];
        $module_access = $this->helpers->org_access_module(11, $_SESSION['organisation_id']);
        if (!empty($module_access)) {
            $privilege_list = $this->helpers->user_privileges(11, $_SESSION['staff_id']);
            if (!empty($privilege_list)) {
               $latest_month = $this->Fiscal_month_model->get_latest_month($_SESSION['organisation_id']);
               $start_month = date('n', strtotime($latest_month['month_end']));

               if($start_month<=date('n')){
               $month_id = date('n', strtotime($latest_month['month_end']. ' +1 day'));
               $year = date('Y', strtotime($latest_month['month_end']. ' +1 day'));
               $dates_array = $this->month_start_end_dates($month_id,$year);
               $month_id = $this->Fiscal_month_model->set($dates_array,$month_id);
                if (is_numeric($month_id)) {
                $feedback['success'] = TRUE;
                $feedback['message'] = "Current month has been locked and New Month has been activated!";
                 }
                } else {
                $feedback['success'] = TRUE;
                $feedback['message'] = "Current month has been locked!";  
                }
               $this->Fiscal_month_model->change_status();
           } 
        }
        echo json_encode($feedback);
  }

    public function end_date_generate($new_date=FALSE) {
        if(isset($_POST['start_date'])){
        $start_date =$this->input->post('start_date');
        $date1 = date_create($start_date);

        } else {
            $start_date = $new_date;
            $date1 = date_create($start_date);
            date_add($date1, date_interval_create_from_date_string('1 day'));
        }
        date_add($date1, date_interval_create_from_date_string('1 month'));
        $auto_end_date = date_create(date_format($date1, 'Y-m-d'));
        date_sub($auto_end_date, date_interval_create_from_date_string('1 day'));
        $gen_end_month = date_format($auto_end_date, 'd-m-Y');

        return $gen_end_month;
    }

    private function month_start_end_dates($month=false,$yr=false) {
        if(is_numeric($this->input->post('month_id'))){
        $month_id = $this->input->post('month_id');
        $year = $this->input->post('year');
        } else {
        $month_id =$month;
        $year =$yr;
        }
        if($month_id<10){
           $month ="0".$month_id;
        } else{
           $month =$month_id;
        }
        $month_start =strtotime("{$year}-{$month}-01");
        $month_end =strtotime("-1 second",strtotime("+1 month",$month_start));
        $dates_array = array('month_start' => date('Y-m-d',$month_start),'month_end'=>date('Y-m-d',$month_end));
        return $dates_array;
    }
     
    public function change_status() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
            $this->data['success'] = FALSE;
            $this->Fiscal_month_model->deactivate_all_first();
            $this->data['message'] = $this->Fiscal_month_model->change_status();
            if ($this->data['message'] === true) {
                $this->data['success'] = TRUE;
                $this->data['message'] = "Successfully activated! Please refresh your page to continue";
            }
    
        echo json_encode($this->data);
    }

    public function inactivate() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
        //   if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {

        $this->data['fiscal_new'] = $this->Fiscal_month_model->get_current_fiscal_year($_SESSION['organisation_id'], 2);
        if (empty($this->data['fiscal_new'])) {
            $this->data['message'] = $this->Fiscal_month_model->change_status();
            if ($this->data['message'] === true) {
                $this->data['success'] = TRUE;
                $this->data['message'] = "Fiscal month now Inactive!";
            }
        } else {
            $this->data['message'] = "Please Deactivate any (Inactive) month, Two or more Inactive fiscal month are not allowed!";
        }
        //   }
        echo json_encode($this->data);
    }

    public function deactivate() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
        //   if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {

        $this->data['message'] = $this->Fiscal_month_model->change_status();
        if ($this->data['message'] === true) {
            $this->data['success'] = TRUE;
            $this->data['message'] = "Month has been Deactivated";
        }
        //   }
        echo json_encode($this->data);
    }
    
    public function activate() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
        //   if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        $this->data['message'] = $this->Fiscal_month_model->activate();
        if ($this->data['message'] === true) {
            $this->data['success'] = TRUE;
            $this->data['message'] = "Month has been Activated";
        }
        //   }
        echo json_encode($this->data);
    }

    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
        //  if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        if (($response['success'] = $this->Fiscal_month_model->delete($this->input->post('id'))) === true) {
            $response['message'] = "Month  successfully deleted";
        }
        //  }
        echo json_encode($response);
    }

}
