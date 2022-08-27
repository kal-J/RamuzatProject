<?php
/**
 * Description of shares
 *
 * @author REAGAN AJUNA
 */
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Shares extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->library("num_format_helper");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        
        $this->load->model("shares_model");
        $this->load->model("RolePrivilege_model");
        $this->load->model("Fiscal_month_model");
        $this->load->model("Share_transaction_model");
        $this->load->model('organisation_format_model');
        $this->load->model("Share_issuance_model");
        $this->load->model("Share_state_model");
        $this->load->model("share_call_model");
        $this->load->model("Share_issuance_fees_model");
        $this->load->library(array("form_validation", "helpers"));
        $this->data['privilege_list'] = $this->helpers->user_privileges(12, $_SESSION['staff_id']);
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['share_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        }
       $this->data['fiscal_active'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
            if(empty($this->data['fiscal_active'])){
                redirect('dashboard');
            }else{
            $this->data['lock_month_access'] = $this->helpers->org_access_module($module_id = 23, $_SESSION['organisation_id']);
                if(!empty($this->data['lock_month_access'])){
                    $this->data['active_month'] = $this->Fiscal_month_model->get_active_month();
                    if(empty($this->data['active_month'])){
                       redirect('dashboard');
                    }
                } 
            }

    }

    public function jsonList() {
     
        $data['data'] = $this->shares_model->get3();
        echo json_encode($data);
    }

    public function jsonList_group_shares() {
       // echo json_encode($this->input->post('group_id'));
       $client_type = $this->input->post('client_type');     
       $data['data'] = $this->shares_model->get_group_shares($client_type);
       echo json_encode($data);
        
        
    }
    public function jsonList_member_shares() {
        // echo json_encode($this->input->post('group_id'));
        $client_type = $this->input->post('client_type');
        //print_r($client_type); die;    
        $data['data'] = $this->shares_model->get_member_shares($client_type);
        echo json_encode($data);
         
         
     }

    public function export_excel($state_id, $status_id) {
        $_POST['state_id'] = $state_id;
        $_POST['status_id'] = $status_id;
        $dataArray = $this->shares_model->get();

        $shares_type = $state_id == 7 ? 'Active' : (
            $state_id == 5 ? 'Pending' : (
                $state_id == 19 ? 'In-Active' : ''
            )
        );

        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'SHARE ACCOUNT NO');
        $sheet->setCellValue('B1', 'ACCOUNT NAME');
        $sheet->setCellValue('C1', 'PRICE PER SHARE');
        $sheet->setCellValue('D1', 'NUMBER OF SHARES');
        $sheet->setCellValue('E1', 'TOTAL AMOUNT');

        $sheet->getStyle("A1:E1")->getFont()->setBold(true);

        $rowCount   =   2;
        foreach ($dataArray as $data) {
            $sheet->setCellValue('A' . $rowCount, $data['share_account_no']);
            $sheet->setCellValue('B' . $rowCount, mb_strtoupper($data['firstname']. ' '. $data['lastname'], 'UTF-8'));
            $sheet->setCellValue('C' . $rowCount, $data['price_per_share'] ? $data['price_per_share'] : 0);
            $sheet->setCellValue('D' . $rowCount, round($data['total_amount'] / $data['price_per_share'], 2));
            $sheet->setCellValue('E' . $rowCount, $data['total_amount']);

            $rowCount++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('E2:E' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'H' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTAL');
        $sheet->getStyle($total_row)->getFont()->setBold(true);

        // calculate totals
        $sheet->setCellValue('E' . ($highestRow + 2), '=SUM(E2:E' . $highestRow . ')');

        $sheet->getStyle('E' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');

        $writer = new Xlsx($spreadsheet);
        $filename = $shares_type.' Share Accounts';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

     public function get_account_details() {
        $data['data'] = $this->shares_model->get($this->input->post('id'));
        echo json_encode($data);
    }
    public function jsonList2() {
        $data['data'] = $this->shares_model->get_applications();
        echo json_encode($data);
    }

    public function payments() {
        $data['data'] = $this->shares_model->get_payments();
        echo json_encode($data);
    }
     public function payment_calls() {
        $data['data'] = $this->shares_model->get_payment_calls();
        echo json_encode($data);
    }

    public function get_product() {
        $data['share_issuances'] = $this->Share_issuance_model->get(['share_issuance.status_id', 1]);
        echo json_encode($data);
    }
    public function get_share_accounts() {
        $response['share_accounts'] = $this->shares_model->get('share_account.id!='.$this->input->post('share_account_no_id'));
        echo json_encode($response);
    }

    public  function inactive_shares_pdf_print_out() {
        $this->load->model('branch_model');
        $this->load->model('organisation_model');        
        $this->load->helper('pdf_helper');
        //$data['start_date']=$start_date;
        //$data['end_date']=$end_date;
        $data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = "In-active Share Accounts";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['data'] = $this->shares_model->get();
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $data['the_page_data'] = $this->load->view('shares/share_account/states/inactive/inactive_pdf_print_out', $data, TRUE);

        echo json_encode($data);
        // $this->load->view('includes/pdf_template', $data);
    }

    public  function pending_shares_pdf_print_out() {
        $this->load->model('branch_model');
        $this->load->model('organisation_model');        
        $this->load->helper('pdf_helper');
        //$data['start_date']=$start_date;
        //$data['end_date']=$end_date;
        $data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = "Pending Share Accounts";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['data'] = $this->shares_model->get();
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $data['the_page_data'] = $this->load->view('shares/share_account/states/pending/pending_pdf_print_out', $data, TRUE);

        echo json_encode($data);
        //$this->load->view('includes/pdf_template', $data);
    }

    public  function active_shares_pdf_print_out() {
        $this->load->model('branch_model');
        $this->load->model('organisation_model');        
        $this->load->helper('pdf_helper');
        //$data['start_date']=$start_date;
        //$data['end_date']=$end_date;
        $data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = " Share Report Summary";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['data'] = $this->shares_model->get();
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $data['the_page_data'] = $this->load->view('shares/share_account/states/active/active_pdf_print_out', $data, TRUE);
        echo json_encode($data);
        //$this->load->view('includes/pdf_template', $data);
    }

  

    public  function share_acc_transactions( $acc_id, $start_date,$end_date)
    {
        $this->data['share_details'] = $this->shares_model->get($acc_id);
        $this->load->model('Share_transaction_model');
        $this->load->model('branch_model');
        $this->load->model('organisation_model');
        $where = "  share_account_id = " . $acc_id ." AND transaction_date BETWEEN '" . ($start_date) . "' AND '" . ($end_date) . "' AND tn.status_id=1";
        
        $this->load->helper('pdf_helper');
        $this->data['start_date']=$start_date;
        $this->data['end_date']=$end_date;
        $this->data['title'] = $_SESSION["org_name"];
        $this->data['get_share_by_id'] = $this->shares_model->get_by_id($acc_id);

        $this->data['sub_title'] = " Share Account Statement";
        $this->data['font'] = 'helvetica';
        $this->data['fontSize'] = 7;

        $this->data['transactions'] = $this->Share_transaction_model->get($where);

        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $this->data['the_page_data'] = $this->load->view('shares/transaction/pdf_transaction', $this->data, TRUE);
        $this->load->view('includes/pdf_template', $this->data);
    }

    public function index() {
        $this->load->model("Member_model");
        $this->load->model("share_call_model");
        $this->load->model("TransactionChannel_model");
        $this->load->model("miscellaneous_model");
        $this->load->model("Savings_account_model");
        $this->load->model("Share_issuance_category_model");
        $this->load->model("Share_transaction_model");
        $this->load->model("Shares_model");
        $this->load->model('Alert_setting_model');
        // Get organisation settings
        $this->data['org_settings'] = $this->organisation_model->get("id = " . $_SESSION['organisation_id'])[0];
        $this->data['tchannel'] = $this->TransactionChannel_model->get();
        $this->data['firstcall'] = $this->share_call_model->get_first_calls(null);
        $this->data['members'] = $this->Savings_account_model->get_clients('status_id=1');
        //print_r($this->data['members']);die();
        $this->data['share_issuances'] = $this->Share_issuance_model->get(['share_issuance.status_id', 1]);
        $this->data['new_account_no'] =$this->num_format_helper->new_share_acc_no();
       
        $this->data['alert_types'] = $this->Alert_setting_model->get2();
    

        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id IN(1,2,4,5)');
        $this->data['payment_modes_bulk_trans'] = $this->miscellaneous_model->get_payment_mode('id IN(1,2,4)');

        $this->data['title'] = $this->data['sub_title'] = 'Shares';
        $neededjs = array("plugins/validate/jquery.validate.min.js", "plugins/select2/select2.full.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js", "plugins/printjs/print.min.js");
        $neededcss = array("plugins/select2/select2.min.css","plugins/daterangepicker/daterangepicker-bs3.css");
        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
    
        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        
         // share accounts 
          $this->data['share_accounts'] = count($this->Shares_model->get("state_id IN(7,19) AND share_account.status_id=1"));
          $this->data['sh_cat'] = $this->Share_issuance_category_model->get_active_share_issuance_price();
        
        //  $this->data['monthly_report']=$this->Share_transaction_model->get_account_sums1();
            
        $this->data['modules'] = array_column($this->data['module_list'], "module_id");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->title = $this->data['title'];
        // Load a view in the content partial
        $this->template->content->view('shares/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function share_state() {
        $this->form_validation->set_rules('member_id', 'Member should be selected', array('required'));
        $this->form_validation->set_rules('approved_shares', 'Approved Shares', array('required'));
        $this->form_validation->set_rules('approval_date', 'Approval Date', array('required'));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->shares_model->approve_share_application()) {
                    $call_list=$this->share_call_model->get_all_calls($this->input->post('share_issuance_id'));
                    //print_r($call_list);die();
                    if ($this->shares_model->create_payments($call_list)) {
                        $feedback['success'] = true;
                        $feedback['message'] = "Share application successfully approved";

                          $this->helpers->activity_logs($_SESSION['id'],12,"Approving share application",$feedback['message']." # ".$_POST['id'],$share_id,$_POST['id']);
                    }
                } else {
                    $feedback['message'] = "There was a problem approving share application, please try again or get in touch with the admin";

                    $this->helpers->activity_logs($_SESSION['id'],12,"Approving share application",$feedback['message']." # ".$_POST['id'],$share_id,$_POST['id']);
                }
            }
        }
        echo json_encode($feedback);
    }

    public function create() {
        $this->load->model('share_state_model');

        if($this->input->post('client_type') == 2) {
            $this->form_validation->set_rules('member_id', 'Group should be selected', array('required'));
        }else {
            $this->form_validation->set_rules('member_id', 'Member should be selected', array('required'));
        }
        

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->shares_model->update()) {
                    if ($this->share_state_model->update($_POST['id'])) {
                        $feedback['success'] = true;
                        $feedback['message'] = "Share application successfully updated";
                        $feedback['share_details'] = $this->shares_model->get(['share_account.id' => $this->input->post('id')]);
                       
                        $this->helpers->activity_logs($_SESSION['id'],12,"Editing share application",$feedback['message']." # ".$this->input->post('share_account_id'),$this->input->post('share_account_id'),$this->input->post('share_account_id'));
                   
                    }

                } else {
                    $feedback['message'] = "There was a problem updating the Share application, please try again or get in touch with the admin";
                    $this->helpers->activity_logs($_SESSION['id'],12,"Editing share application",$feedback['message']." # ".$this->input->post('share_account_id'),$this->input->post('share_account_id'),$this->input->post('share_account_id'));
                   
                }
            } else {
                $share_no = $this->input->post('share_account_no') ? $this->input->post('share_account_no') : $this->num_format_helper->new_share_acc_no();
                $share_id= $this->shares_model->set($share_no);
                if (is_numeric($share_id)) {
                    $this->share_state_model->set($share_id);
                    $feedback['success'] = true;
                    $feedback['message'] = "Share application details successfully saved";
                    $this->helpers->activity_logs($_SESSION['id'],12,"Editing share application",$feedback['message']." # ".$share_id,$share_id,$share_id);
                   
                  
                } else {
                    $feedback['message'] = "There was a problem creating the share account";
                    $this->helpers->activity_logs($_SESSION['id'],12,"Editing share application",$feedback['message']." # ".$share_id,$share_id,$share_id);
                   
                }
            }
        }
        echo json_encode($feedback);
    }

     public function get_new_account_no()
    {
        $this->load->library("num_format_helper");
        $new_account_no = $this->num_format_helper->new_share_acc_no();
        $data['data'] = ['new_account_no' => $new_account_no];
        echo json_encode($data);
    }

     public function create2() {
        $this->form_validation->set_rules('share_account_id', 'Share account should be selected', array('required'));
        $this->form_validation->set_rules('application_date', 'application_date should be selected', array('required'));
        $this->form_validation->set_rules('category_id', 'category id must be selected', array('required'));
        $this->form_validation->set_rules('shares_requested', 'Number of shares', array('required'));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->shares_model->update_application()) {
                        $feedback['success'] = true;
                        $feedback['message'] = "Share application successfully updated";
                        $this->helpers->activity_logs($_SESSION['id'],12,"Editing share application",$feedback['message']." -# ".$this->input->post('share_account_id'),$this->input->post('share_account_id'),$this->input->post('share_account_id'));
                   
                } else {
                    $feedback['message'] = "There was a problem updating the Share application, please try again or get in touch with the admin";
                    $this->helpers->activity_logs($_SESSION['id'],12,"Editing share application",$feedback['message']." -# ".$this->input->post('share_account_id'),$this->input->post('share_account_id'),$this->input->post('share_account_id'));
                   
                }
            } else {
                //change to share application number
                $share_application_no =$this->num_format_helper->new_share_app_no();
                $share_appid= $this->shares_model->set_application($share_application_no);
                if (is_numeric($share_appid)) {
                        $feedback['success'] = true;
                        $feedback['message'] = "Share application details successfully saved";

                        $this->helpers->activity_logs($_SESSION['id'],12,"Creating share application",$feedback['message']." -# ".$share_appid,$this->input->post('share_account_id'),$this->input->post('share_account_id'));
                   
                 
                } else {
                    $feedback['message'] = "There was a problem saving the share application";
                    $this->helpers->activity_logs($_SESSION['id'],12,"Creating share application",$feedback['message']." -# ".$share_appid,$this->input->post('share_account_id'),$this->input->post('share_account_id'));
                   
                }
            }
        }
        echo json_encode($feedback);
    }
    public function create3() {
        $this->form_validation->set_rules('alert_method', 'Alert method should be selected', array('required'));
        $this->form_validation->set_rules('alert_type', 'Type of alert should be selected', array('required'));
        $this->form_validation->set_rules('number_of_reminder', 'Number of reminder must be selected', array('required'));
        $this->form_validation->set_rules('type_of_reminder', 'Type of reminder must be selected', array('required'));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->shares_model->update_alert_setting()) {
                        $feedback['success'] = true;
                        $feedback['message'] = "Alert setting successfully updated";
                        /*$this->helpers->activity_logs($_SESSION['id'],12,"Editing  Alert Setting",$feedback['message']." -# ".$this->input->post('share_account_id'),$this->input->post('share_account_id'),$this->input->post('share_account_id'));*/
                   
                } else {
                    $feedback['message'] = "There was a problem updating the alert setting, please try again or get in touch with the admin";
                   /* $this->helpers->activity_logs($_SESSION['id'],12,"Editing share application",$feedback['message']." -# ".$this->input->post('share_account_id'),$this->input->post('share_account_id'),$this->input->post('share_account_id'));
                   */
                }
            } else {
                //change to share application number
               // $share_application_no =$this->num_format_helper->new_share_app_no();
                $alert_setting= $this->shares_model->set_alert_setting();
                if (is_numeric($alert_setting)) {
                        $feedback['success'] = true;
                        $feedback['message'] = "Alert setting details successfully saved";

                     /*   $this->helpers->activity_logs($_SESSION['id'],12,"Creating share application",$feedback['message']." -# ".$share_appid,$this->input->post('share_account_id'),$this->input->post('share_account_id'));
                     */
                 
                } else {
                    $feedback['message'] = "There was a problem saving the alert setting";
                   /* $this->helpers->activity_logs($_SESSION['id'],12,"Creating share application",$feedback['message']." -# ".$share_appid,$this->input->post('share_account_id'),$this->input->post('share_account_id'));
                   */
                }
            }
        }
        echo json_encode($feedback);
    }

    public function view($share_id, $client_type=false) {
        $this->load->model("share_issuance_fees_model");
        $this->load->model("Share_issuance_model");
        $this->load->model('dashboard_model');
        $this->data['share_issuances'] = $this->Share_issuance_model->get(['share_issuance.status_id', 1]);
        $this->load->library(array("form_validation", "helpers"));
        $this->data['title'] = 'Share account details';
        $this->data['modalTitle'] = 'Edit Share';
        $this->template->title = $this->data['title'];
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js");
        $neededcss = array("fieldset.css", "plugins/select2/select2.min.css","plugins/daterangepicker/daterangepicker-bs3.css");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['fiscal_year'] = $this->dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] = array_column($this->data['module_list'], "module_id");
        $this->data['get_share_by_id'] = $this->shares_model->get_by_id($share_id);
        $this->data['acc_id'] = $share_id;
       // $this->data['share_price_amount'] = $this->data['get_share_by_id']['share_price'] * $this->data['get_share_by_id']['shares'];
        $this->data['share_detail'] = $this->shares_model->get_share_fee($share_id);
       
        $this->data['share_details'] = $this->shares_model->get($share_id);
       
        $this->data['available_share_fees'] = $this->share_issuance_fees_model->get("shareproduct_id=" .$share_id);
        
        $this->template->content->view('shares/shares_detail', $this->data);
        $this->template->publish();
    }

    private function share_call_journal_transaction($transaction_data, $application_id) {
        $this->load->model('journal_transaction_model');
        $this->load->model('Shares_model');
        $this->load->model('Share_issuance_model');
        $this->load->model('Share_issuance_category_model');
        $date = date('d-m-Y');
        $share_category= $this->Share_issuance_category_model->get($this->input->post('category_id'));
        //$share_issuance = $this->Shares_model->get_by_id($application_id);
        $deposited_amount = round($this->input->post('amount'), 2);
        $deposit_amount = $deposited_amount;
        //then we prepare the journal transaction lines
        if (!empty($share_category)) {
            $this->load->model('accounts_model');
            $this->load->model('transactionChannel_model');
            $this->load->model('journal_transaction_line_model');

            $data = [
                'transaction_date' => $date,
                'description' => $this->input->post('narrative'),
                'ref_no' => $transaction_data['transaction_no'],
                'ref_id' => $transaction_data['transaction_id'],
                'status_id' => 1,
                'journal_type_id' => 22
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);

            $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
         
            $debit_or_credit1 = $this->accounts_model->get_normal_side($share_category['share_capital_account_id']);
            $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);

            //if deposit amount has been received
            if ($deposit_amount != null && !empty($deposit_amount) && $deposit_amount != '0') {
                $data[0] = [
                    $debit_or_credit1 => $deposit_amount,
                    'narrative' => "Share payment made on " . $date,
                    'account_id' => $share_category['share_capital_account_id'],
                    'status_id' => 1
                ];
                $data[1] = [
                    $debit_or_credit2 => $deposit_amount,
                    'narrative' => "Share payment made on " . $date,
                    'account_id' => $transaction_channel['linked_account_id'],
                    'status_id' => 1
                ];
                $this->journal_transaction_line_model->set($journal_transaction_id, $data);
            }//end of the if
        }
    }

    public function deposit_fees() {
        //fetch deposite fees  
        $this->data['deposit_fees'] = $this->Share_issuance_fees_model->get('shareproduct_id='.$this->input->post('new_product_id').' AND fms_share_fees.chargetrigger_id =4 AND share_issuance_fees.status_id=1');
        $response['deposit_fees'] = $this->data['deposit_fees'];
        echo json_encode($response);
    }

    public function withdraw_fees() {
        //fetch withdraw fees  
        $this->data['withdraw_fees'] = $this->Share_issuance_fees_model->get('shareproduct_id='.$this->input->post('new_product_id').' AND fms_share_fees.chargetrigger_id =3 AND share_issuance_fees.status_id=1');
        $response['withdraw_fees'] = $this->data['withdraw_fees'];
        echo json_encode($response);
    }
    public function transfer_fees() {
        //fetch transfer_fees  
        $this->data['transfer_fees'] = $this->Share_issuance_fees_model->get('shareproduct_id='.$this->input->post('new_product_id').' AND fms_share_fees.chargetrigger_id =5 AND share_issuance_fees.status_id=1');
        $response['transfer_fees'] = $this->data['transfer_fees'];
        echo json_encode($response);
    }

    public function change_status() {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Share account data could not be $msg activated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->Share_state_model->set($this->input->post('id'))) {
            $response['message'] = "Share account data has successfully been $msg activated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }
    public function delete() {
        $response['message'] = "Share account data could not be Deleted, contact IT support.";
        $response['success'] = FALSE;
        if ($this->shares_model->change_status_by_id($this->input->post('id'))) {
            $response['message'] = "Share account data has successfully been Deleted.";
            $response['success'] = TRUE;
            echo json_encode($response);

            $this->helpers->activity_logs($_SESSION['id'],12,"Deleting share",$feedback['message'],NULL,
            $this->input->post('id'));
        }
    }

    function change_state() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        $this->form_validation->set_rules('share_account_id', 'Share Account', 'required');
        $this->form_validation->set_rules('action_date', 'Action date', 'required');
        $this->form_validation->set_rules('state_id', 'State', 'required');
        $this->form_validation->set_rules('comment', 'Comment', 'required|trim');
        if ($this->form_validation->run() === true) {
            if (($response['success'] = $this->shares_model->change_state()) === true) {
                $response['message'] = " Status Changed";
                $response['share_details'] = $this->shares_model->get(['share_account.id' => $this->input->post('share_account_id')]);
                
                 $this->helpers->activity_logs($_SESSION['id'],12,"Deactivating share",$response['message'],NULL,$this->input->post('id'));
            }
        } else {
            $response['message'] = validation_errors();

             $this->helpers->activity_logs($_SESSION['id'],12,"Deactivating share",$response['message'],NULL,$this->input->post('id'));
        }
        echo json_encode($response);

       
    }

    private function share_state_journal_transaction($transaction_data, $application_id) {
        $this->load->model('journal_transaction_model');
        $this->load->model('Shares_model');
        $this->load->model('Share_issuance_model');
        $date = date('d-m-Y');
        $share_issuance = $this->Shares_model->get($application_id);
        $deposited_amount = round($this->input->post('amount'), 2);
        $deposit_amount = $deposited_amount;
        //then we prepare the journal transaction lines
        if (!empty($share_issuance)) {
            $this->load->model('accounts_model');
            $this->load->model('transactionChannel_model');
            $this->load->model('journal_transaction_line_model');

            $data = [
                'transaction_date' => $date,
                'description' => $this->input->post('narrative'),
                'ref_no' => $transaction_data['transaction_no'],
                'ref_id' => $transaction_data['transaction_id'],
                'status_id' => 1,
                'journal_type_id' => 22
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);

            $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
            $share_issuance_details = $this->Share_issuance_model->get_accounts($share_issuance['share_issuance_id']);

            $debit_or_credit1 = $this->accounts_model->get_normal_side($share_issuance_details['share_application_account_id']);
            $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);

            //if deposit amount has been received
            if ($deposit_amount != null && !empty($deposit_amount) && $deposit_amount != '0') {
                $data[0] = [
                    $debit_or_credit1 => $deposit_amount,
                    'narrative' => "Share call made on " . $date,
                    'account_id' => $share_issuance_details['share_application_account_id'],
                    'status_id' => 1
                ];
                $data[1] = [
                    $debit_or_credit2 => $deposit_amount,
                    'narrative' => "Share call made on " . $date,
                    'account_id' => $transaction_channel['linked_account_id'],
                    'status_id' => 1
                ];
                $this->journal_transaction_line_model->set($journal_transaction_id, $data);
            }//end of the if
        }
    }

 function generate_share_no() {
    $this->data['share_no_format'] =$this->organisation_format_model->get_share_format();
    $org_id = $this->data['share_no_format']['id'];
    $org =  $this->data['share_no_format']['share_format'];
    $counter =  $this->data['share_no_format']['share_counter'];
    $letter =  $this->data['share_no_format']['share_letter'];
   
    $initial = "SH";
    if ($org == '1') {
        if ($counter == 99999) {
            $letter++;
            $counter=0;
        }
        $share_no = $initial . sprintf("%05d", $counter + 1) . $letter;
    } else if ($org == '2') {
        if ($counter == 99999) {
            $letter++;
            $counter=0;
        }
        $share_no = $letter . sprintf("%05d", $counter + 1) . $initial;
    } else if ($org == '3') {
        $share_no = $initial . sprintf("%05d", $counter + 1);
    } else {
        $share_no = false;
    }
    $this->db->where('id',$org_id);
    $upd = $this->db->update('fms_organisation', ["share_counter"=> $counter+1,"share_letter"=> $letter]);
    return $share_no;
    }

    public function print_receipt($share_account_no, $transaction_id){
        if (empty($this->session->userdata('id'))) {
            redirect("welcome", "refresh");
        }
        $this->load->model('branch_model');
        $this->load->model('organisation_model');
        $this->load->model('Share_transaction_model');

        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);

        if (isset($_POST['acc_id'])) {
            $where = "share_account_id = " . $this->input->post('acc_id') . " AND  tn.status_id = " . $this->input->post('status_id') . " ";
        } else {
            $where = "tn.status_id = " . $this->input->post('status_id') . " ";
        }

        $data['data'] = $this->Share_transaction_model->get($where);

        foreach($data['data'] as $transaction) {
            if($transaction['id'] == $transaction_id) {
                $data['trans'] = $transaction;
                break;
            }
        }

        $this->load->view('shares/transaction/receipt_print_out', $data);
    }

    public function share_full_report()
    {
        $this->data['summary_data'] = $this->Share_transaction_model->client_share_sums(); 
        
        if (!empty($this->data['summary_data'])) {
            $data['summary_data'] = $this->data['summary_data'];
        } 
        else {
            $data['summary_data']['issuance_name'] = 'No category data available';
        }
        
        echo json_encode($data); 
        return $data;
    }
    
   
   //performace report query 
    public function performance_report_query(){

          $period = $this->input->post('period');
          

        if ($period == 2) {
            $start_date = $data['start_date'] = $this->input->post('start_date');
            $end_date = $data['end_date'] = $this->input->post('end_date');
            $data['general_data'] = $this->shares_and_others($start_date, $end_date);
           // $data['loan_data'] = $this->get_loan_indicators_data($start_date, $end_date);
        } 
        else if ($period == 1) {
            $end_date = $data['end_date'] = $this->input->post('end_date');
            $start_date = date('Y-m-d', strtotime("now - 2 months"));

            $data['general_data'] = $this->shares_and_others($start_date, $end_date);
            $end_date = $data['end_date'] = $this->input->post('end_date');

            // $data['general_data'] = $this->shares_and_others('2021-10-01', $end_date);

            //$data['loan_data'] = $this->get_loan_indicators_data(false, $end_date);
        }
         else {
            if (($period == 3) && ($this->input->post('fiscal_1') != "") && ($this->input->post('fiscal_1') != NULL)) {
                $year = $this->fiscal_model->get($this->input->post('fiscal_1'));
                $end_date = $year['end_date'];
                $data['general_data'] = $this->shares_and_others(false, $end_date);
                //$data['loan_data'] = $this->get_loan_indicators_data(false, $end_date);
                $data['start_date'] = $year['start_date'];
                $data['end_date'] = $year['end_date'];
            }
            if (($period == 3) && ($this->input->post('fiscal_2') != "") && ($this->input->post('fiscal_2') != NULL)) {
                $year = $this->fiscal_model->get($this->input->post('fiscal_2'));
                $end_date = $year['end_date'];
                $data['general_data1'] = $this->shares_and_others(false, $end_date);
                //$data['loan_data1'] = $this->get_loan_indicators_data(false, $end_date);
                $data['start_date1'] = $year['start_date'];
                $data['end_date1'] = $year['end_date'];
            }
            if (($period == 3) && ($this->input->post('fiscal_3') != "") && ($this->input->post('fiscal_3') != NULL)) {
                $year = $this->fiscal_model->get($this->input->post('fiscal_3'));
                $end_date = $year['end_date'];
                $data['general_data2'] = $this->shares_and_others(false, $end_date);
                //$data['loan_data2'] = $this->get_loan_indicators_data(false, $end_date);
                $data['start_date2'] = $year['start_date'];
                $data['end_date2'] = $year['end_date'];
            }
        }

        $data['period'] = $period;
        $data['fiscal_1'] = is_numeric($this->input->post('fiscal_1')) ? $this->input->post('fiscal_1') : 0;
        $data['fiscal_2'] = is_numeric($this->input->post('fiscal_2')) ? $this->input->post('fiscal_2') : 0;
        $data['fiscal_3'] = is_numeric($this->input->post('fiscal_3')) ? $this->input->post('fiscal_3') : 0;
        $data['membership'] = $this->input->post('membership');
        $data['month'] = $this->input->post('month');
         $data['category'] = $this->input->post('category');
       
        $data['shares'] = $this->input->post('shares');
        
        if (is_numeric($this->input->post('print'))) {
            return $data;
        } else {
            $data['success'] = TRUE;
            echo json_encode($data);
        }
    }

    public function shares_and_others($start_date = false, $end_date = false) {
       
        if ($start_date == false) {
            $transaction_range = "(DATE(transaction_date) <= '" . $end_date . "')";
            $member_range = "( DATE(st.transaction_date) <= '" . $end_date . "')";
           
            $shares_range = "(DATE(fms_share_account.date_opened) <= '" . $end_date . "')";

            $shares_trans_range = "(DATE(transaction_date) <= '" . $end_date . "') GROUP  BY share_issuance_id";
              
        }  
         else {
            $transaction_range = "(DATE(transaction_date) BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
            $member_range = "( DATE(st.transaction_date) BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
            $savings_range = "(st.transaction_date BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
            $shares_range = "( DATE(fms_share_account.date_opened) BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
            $shares_trans_range = "( DATE(transaction_date) BETWEEN '" . $start_date . "' AND '" . $end_date . "') GROUP  BY share_issuance_id";
            $transaction_status =1;
        }

        $this->load->model('share_transaction_model');
        $this->load->model('Share_issuance_category_model');
        $this->load->model('Shares_model');
        $this->load->model('Transaction_model');
        $this->load->model("Share_issuance_model");
          
        $this->load->model('member_model');
        $data['male_members'] = count($this->Share_transaction_model->get2("u.gender=1 AND m.status_id !=9 AND " . $member_range));
        
        $data['female_members'] = count($this->share_transaction_model->get2("u.gender=2 AND m.status_id !=9 AND " . $member_range));

       
        $sh_cat = $this->Share_issuance_category_model->get_active_share_issuance_price();
 
        $data['share_accounts'] = count($this->Shares_model->get("share_account.status_id=1 AND " . $shares_range));
        $data['inactive_accounts'] = count($this->Shares_model->get("fms_share_account.id AND " . "fms_share_account.status_id=3"));
        $data['deactivated_accounts'] = count($this->Shares_model->get("fms_share_account.id AND " . "fms_share_account.status_id=0"));

        $data['no_trans_reversed'] = count($this->share_transaction_model->get2("st.id AND "."st.status_id=0"));
        $data['no_of_shares_transfered'] =$this->share_transaction_model->get_total_shares_transfer($transaction_range);
        $data['total_shares_amount']=$this->share_transaction_model->get_total_shares($transaction_range);
        $data['no_of_shares_bought']=$this->share_transaction_model->get_total_shares_bought($transaction_range);
        $data['no_of_shares_sold'] =$this->share_transaction_model->get_total_shares_sold($transaction_range);
        $data['gender_summary_data']=$this->share_transaction_model->get_account_sums2($transaction_range);
        //total summary for gender table on UI.
        $overall_total_t1=0;
        foreach ($data['gender_summary_data'] as $summary_data) {

              $overall_total_t1+=$summary_data['amount'];
              
        }
        $data['overall_total_t1']=number_format($overall_total_t1,0);
        //print_r( $data['total_amount']);die();
            
        //issuance
         $data['active_shares_accounts'] = count($this->Share_issuance_model->get("fms_share_issuance.id AND " . "fms_share_issuance.status_id=1"));

        $data['share_report'] = $this->share_transaction_model->get_account_sums1($shares_trans_range);

         $total_debit=0;
         $total_credit=0;
         $overall_total_t2=0;
         $total_shares_t2=0;
         $total_shares=0;
         $totpps=0;
        foreach ($data['share_report'] as $sum_data) {

              $overall_total_t2+=$sum_data['amount'];
              $total_debit+=$sum_data['debit_sum'];
              $total_credit+=$sum_data['credit_sum'];
              $totpps+=$sum_data['price_per_share'];
              $total_shares_t2+=round(($overall_total_t2/$totpps),2);
        }
        
        $data['overall_total_t2']=number_format($overall_total_t2,0);
        $data['total_shares_t2']=round($total_shares_t2,2);
        $data['total_debit']=number_format(round($total_debit,2),0);
        $data['total_credit']=number_format(round($total_credit,2),0);
        $data['total_shares']=(number_format(round($total_credit-$total_debit,2),0));

        $data['rowSpan_value'] = count($data['share_report']) + 3;
       
        //monthly
        
        $startDate = new DateTime($start_date);
        $endDate = new DateTime($end_date);

        $dateInterval = new DateInterval('P1M');
        $datePeriod   = new DatePeriod($startDate, $dateInterval, $endDate);
        $data['monthly_report']=$data1["monthly_report"] = array();  $counter1 = 1;
        //$data['category']=[];
            foreach ($datePeriod as $key => $date) {
                $issuances=$this->Share_issuance_model->get();
                $data['monthly_report'][$key]=array('month_name' => $date->format('F') . " / " . $date->format('Y'),"counter"=>count($issuances));
                $counter=1;
                foreach ($issuances as $key2=> $value) {
                 $summations = $this->Share_transaction_model->get3("share_issuance_id=". $value['id'] ." AND  (DATE(transaction_date) BETWEEN '" . $date->format('Y-m-d') . "' AND '" . $date->format('Y-m-t') . "')");
                   $total_amount = ($summations['total_amount'] != NULL) ? $summations['total_amount'] : 0;

                  
                   $data['monthly_report'][$key]['category'][$key2]=array( "total_amount" => $total_amount,"issuance_name" => $value['issuance_name'], "price_per_share" => $value['price_per_share']);
                //array_push($data['category'], $data_sum["cat"][$counter]);
               $counter++;
                }
               //array_push($data1["monthly_report"], $data['category']);
               $counter1++;
            }
            //comparison in the months performances
            $monthly_data = [];
              foreach ($data['monthly_report'] as $key => $value) {

            if ($key > 0) {
                $new_value = $value;
                $current = $value['category'];
                $prev = ($data['monthly_report'][$key - 1]) < 0 ? 0 : $data['monthly_report'][$key - 1]['category'];
                $change_ordinary_share =  $current[0]['total_amount'] <=  0 ? 0 : (($current[0]['total_amount'] - $prev[0]['total_amount']) / $current[0]['total_amount']) * 100;
                $new_value['category'][0]['change'] = $change_ordinary_share;
                if (!empty($current[1])) {
                    $change_premium_share = $current[1]['total_amount'] <=  0 ? 0 : (($current[1]['total_amount'] - $prev[1]['total_amount']) / $current[1]['total_amount']) * 100;
                    $new_value['category'][1]['change'] = $change_premium_share;
                } else {
                    $new_value['category'][0]['change'] = $change_ordinary_share;
                }

                $monthly_data[] = $new_value;
            }
        }

            $data['monthly_report'] = $monthly_data;
         
       json_encode($data);
 
        return $data;
    }

   
  
}
