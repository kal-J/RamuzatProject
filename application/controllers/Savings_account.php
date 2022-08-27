<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Savings_account extends CI_Controller
{
    public function __construct()
    {
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
        $this->load->model("Loan_guarantor_model");
        $this->load->model("Group_member_model");
        $this->load->model("saving_fees_model");
        $this->load->model("RolePrivilege_model");
        $this->load->library(array("form_validation", "helpers"));
        $this->data['allowed_transaction_dates'] = $this->Transaction_date_control_model->generate_allowed_dates();
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 6, $_SESSION['staff_id']);
        $this->data['share_list'] = $this->helpers->user_privileges($module_id = 12, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module($module_id = 6, $_SESSION['organisation_id']);
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
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

    public function index()
    {
        $this->load->library("num_format_helper");
        $this->load->model("Withdraw_requests_model");

        $this->data['acc_id'] = "";    //used to hide transaction tab
        $this->data['switch_deposit_modal'] = 'oustide';
        $this->data['tchannel'] = $this->TransactionChannel_model->get();
        $this->data['products'] = $this->DepositProduct_model->get("sp.status_id=1");
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id IN(1,2,4,6,7,8)');
        //$this->data['format_types'] = $this->Organisation_format_model->get_format_types(FALSE, ['account_format']);
        $this->data['type'] = $this->data['sub_type'] = 'savings';
        $this->data['new_account_no'] = $this->num_format_helper->new_savings_acc_no();
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
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

        $this->data['savings_data'] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7');
        $total_savings = 0;
        foreach ($this->data['savings_data'] as $key => $value) {
            $total_savings = $total_savings + $value['real_bal'];
        }
        $this->data['total_savings'] = $total_savings;
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);

        $pending_withdraw_requests = $this->Withdraw_requests_model->get_count(1);
        $declined_withdraw_requests = $this->Withdraw_requests_model->get_count(3);
        $accepted_withdraw_requests = $this->Withdraw_requests_model->get_count(2);
        $withdraw_requests = [
            "pending" => $pending_withdraw_requests,
            "declined" => $declined_withdraw_requests,
            "accepted" => $accepted_withdraw_requests
        ];
        $this->data['withdraw_requests'] = $withdraw_requests;
        // $this->data['access_side']='Savings Account';

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->title = $this->data['title'];
        // Load a view in the content partial
        $this->template->content->view('savings_account/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function mobile_deposits()
    {
        $this->data['title'] = $this->data['sub_title'] = 'Mobile Deposits';
        $this->template->title = $this->data['title'];
        // Load a view in the content partial
        $this->template->content->view('savings_account/mobile_deposit_view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function jsonList()
    {

        $producttype = $this->input->post('producttype');
        $gender = $this->input->post('gender');
        $state_id    = $this->input->post('state_id');
        $where = "j.state_id = " . $state_id;
        if (isset($producttype) && is_numeric($producttype)) {
            $where = 'sp.id =' . $producttype . ' AND ' . $where;
        }
        if (isset($gender) && is_numeric($gender)) {
            $where = $where . ' AND c.gender = ' . $gender;
        }
        //used for fetching union of members and groups
        // print_r($where); die;
        $data['data'] = $this->Loan_guarantor_model->get_guarantor_savings2($where);
        echo json_encode($data);
    }

    public function export_excel($state, $balance_end_date = false)
    {
        if ($balance_end_date) {
            $_POST['balance_end_date'] = $balance_end_date;
        }
        $dataArray = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=' . $state);
        $savings_type = $state == 7 ? 'Active' : ($state == 5 ? 'Pending' : ($state == 17 ? 'In-Active' : ($state == 12 ? 'Locked' : ($state == 18 ? 'Deleted' : ''))));
        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:H1");
        if ($balance_end_date) {
            $sheet->setCellValue('A1', strtoupper($savings_type) . ' SAVINGS ACCOUNTS AS AT ' . date('d-F-Y', strtotime($balance_end_date)));
        } else {
            $sheet->setCellValue('A1', strtoupper($savings_type) . ' SAVINGS ACCOUNTS');
        }


        $sheet->getStyle("A1:H1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:H1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center

        $sheet->setCellValue('A3', 'ACCOUNT NO');
        $sheet->setCellValue('B3', 'ACCOUNT HOLDER');
        $sheet->setCellValue('C3', 'PRODUCT');
        $sheet->setCellValue('D3', 'ACCOUNT TYPE');
        $sheet->setCellValue('E3', 'ACCOUNT BALANCE');
        $sheet->setCellValue('F3', 'DATE REGISTERED');
        $sheet->setCellValue('G3', 'DATE OPENED');

        $sheet->getStyle("A3:G3")->getFont()->setBold(true);

        $rowCount   =   4;
        foreach ($dataArray as $data) {
            $account_type = $data['client_type'] == 1 ? 'Individual' : ($data['client_type'] == 2 ? 'Group' : ($data['client_type'] == 3 ? 'Both' : ''));
            $sheet->setCellValue('A' . $rowCount, $data['account_no']);
            $sheet->setCellValue('B' . $rowCount, mb_strtoupper($data['member_name'], 'UTF-8'));
            $sheet->setCellValue('C' . $rowCount, mb_strtoupper($data['productname'], 'UTF-8'));
            $sheet->setCellValue('D' . $rowCount, $account_type);
            $sheet->setCellValue('E' . $rowCount, $data['real_bal']);
            $sheet->setCellValue('F' . $rowCount, date('d-M-Y', strtotime($data['date_registered'])));
            $sheet->setCellValue('G' . $rowCount, date('d-M-Y', strtotime($data['date_opened'])));

            $rowCount++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('E4:E' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $total_row = 'A' . ($highestRow + 2) . ':' . 'G' . ($highestRow + 2);
        $sheet->setCellValue('A' . ($highestRow + 2), 'TOTAL');
        $sheet->getStyle($total_row)->getFont()->setBold(true);

        // calculate totals
        $sheet->setCellValue('E' . ($highestRow + 2), '=SUM(E4:E' . $highestRow . ')');

        $sheet->getStyle('E' . ($highestRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');

        $writer = new Xlsx($spreadsheet);
        $filename = $savings_type . ' Saving Accounts';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        $this->helpers->activity_logs($_SESSION['id'], 6, "Exporting data", "Exported data" . $filename, NULL, NULL);
    }

    public function account_list()
    { //used for fetching union of members and groups for dashboard
        $data['data'] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id IN (5,7)');
        echo json_encode($data);
    }

    public function jsonList2()
    {

        $data['accounts_data'] = $this->Savings_account_model->get();
        echo json_encode($data);
    }

    public function jsonList_member()
    {  //used for member in case you access through clients->member
        $data['data'] = $this->Loan_guarantor_model->get_guarantor_savings2('client_type=' . $this->input->post('client_type') . ' AND j.state_id=' . $this->input->post('state_id'));
        echo json_encode($data);
    }

    public function checkData()
    {  //used for member in case you access through clients->member
        $data['data'] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7');
        echo json_encode($data);
    }

    public function jsonList_group()
    {   //used for groups in case you access through clients->group
        $data['data'] = $this->Loan_guarantor_model->get_guarantor_savings_group('client_type=' . $this->input->post('client_type') . ' AND j.state_id=' . $this->input->post('state_id'));
        echo json_encode($data);
    }

    public function pending_savings_print_out()
    {
        $this->load->model('branch_model');
        $this->load->model('organisation_model');
        $this->load->helper('pdf_helper');

        $data['data'] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=' . $this->input->post('state_id'));
        // $data['start_date'] = $this->input->post('start_date');
        if ($this->input->post('end_date')) {
            $data['end_date'] = $this->input->post('end_date');
        } else {
            $data['end_date'] = date('d-m-Y', time());
        }
        $data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = "Pending Savings Accounts";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $data['the_page_data'] = $this->load->view('savings_account/states/pending/pdf_print_out', $data, TRUE);
        echo json_encode($data);
    }

    public function active_savings_print_out()
    {
        $this->load->model('branch_model');
        $this->load->model('organisation_model');
        $this->load->helper('pdf_helper');

        $data['data'] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=' . $this->input->post('state_id'));
        // $data['start_date'] = $this->input->post('start_date');
        // if ($this->input->post('end_date')) {
        //     $data['end_date'] = $this->input->post('end_date');
        // } else {
        //     $data['end_date'] = date('d-m-Y', time());
        // }
        $data['balance_end_date'] = $this->input->post('balance_end_date');
        $data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = "Active Savings";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $data['the_page_data'] = $this->load->view('savings_account/states/active/pdf_print_out', $data, TRUE);

        echo json_encode($data);
    }

    public function create()
    {
        $this->form_validation->set_rules('account_no', 'Account No.', array('required', 'min_length[4]'), array('required' => '%s must be more than 6 characters'));
        $this->form_validation->set_rules('deposit_Product_id', 'Product', array('required', 'numeric'), array('required' => '%s must be selected'));
        //$this->form_validation->set_rules('opening_balance', 'Opening balance', 'required');
        $this->form_validation->set_rules('client_type', 'Client Type', 'required');
        $this->form_validation->set_rules('date_opened', 'Opening Date', 'required');
        if (isset($_POST['interest_rate'])) {
            $this->form_validation->set_rules('interest_rate', 'Interest Rate', 'required');;
        }
        if (isset($_POST['term_length'])) {
            $this->form_validation->set_rules('term_length', 'Term Length', 'htmlentities');
        }
        $this->form_validation->set_rules('member_id', 'Account Holder', 'required');

        $this->db->trans_begin();

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {

                if ($this->Savings_account_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Account successfully updated";
                    $feedback['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings2("(j.state_id = 5 OR j.state_id = 7 OR j.state_id = 12 OR j.state_id = 17 OR j.state_id = 18)", $_POST['id']);
                    //activity log 

                    $this->helpers->activity_logs($_SESSION['id'], 6, "Updating savings account", $feedback['message'] . " # " . $this->input->post('id'), NULL, $this->input->post('id'));

                    $feedback['state_totals'] = $this->Savings_account_model->state_totals();
                } else {
                    $feedback['message'] = "There was a problem updating the savings account";

                    $this->helpers->activity_logs($_SESSION['id'], 6, "Updating savings account", $feedback['message'] . " # " . $this->input->post('id'), NULL, $this->input->post('id'));
                }
            } else {

                $new_account_no = $this->input->post('account_no') ? $this->input->post('account_no') : $this->get_sav_acc_no();
                $checker = $this->Savings_account_model->set($new_account_no);
                if (is_numeric($checker) && ($checker > 0)) {
                    $feedback['success'] = true;
                    $feedback['new_account_no'] = ++$new_account_no;
                    $feedback['message'] = "Savings account successfully saved";

                    $this->helpers->activity_logs($_SESSION['id'], 6, "Creating new savings account", $feedback['message'] . " # " . $new_account_no, NULL, $new_account_no);

                    $feedback['state_totals'] = $this->Savings_account_model->state_totals();
                    if ($this->input->post('mandatory_saving') == 1) {
                        $this->schedule_creation($checker);
                    }
                } else {
                    $feedback['message'] = "There was a problem creating the Account";

                    $this->helpers->activity_logs($_SESSION['id'], 6, "Creating savings account", $feedback['message'] . " # " . $new_account_no, NULL, $new_account_no);
                }
            }
        }

        if ($this->db->trans_status() === FALSE || !(is_numeric($checker) && ($checker > 0))) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        echo json_encode($feedback);
    }



    public function schedule_creation($account_id)
    {
        $this->load->model('savings_schedule_model');
        $product_data = $this->DepositProduct_model->get_products($this->input->post('deposit_Product_id'));
        $schedule_interval = '';
        if ($product_data['saving_made_every'] == 1) {
            $schedule_interval = $product_data['saving_frequency'] . ' day';
        } elseif ($product_data['saving_made_every'] == 2) {
            $schedule_interval = $product_data['saving_frequency'] . ' week';
        } elseif ($product_data['saving_made_every'] == 3) {
            $schedule_interval = $product_data['saving_frequency'] . ' month';
        }
        if ($product_data['schedule_current_date'] && $product_data['schedule_current_date'] != '0000-00-00' && $schedule_interval != '') {
            $current_schedule_date = $product_data['schedule_current_date'];
            $from_date = date('Y-m-d', strtotime('-' . $schedule_interval, strtotime($current_schedule_date)));
            $to_date = date('Y-m-d', strtotime('-1 day', strtotime($current_schedule_date)));
            $schedule_data[] = array(
                'saving_acc_id' => $account_id,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'date_created' => time(),
                'created_by' => $_SESSION['id']
            );
            $this->savings_schedule_model->set($schedule_data);
        }
    }

    public function get_sav_acc_no()
    {
        $this->load->library("num_format_helper");
        $new_account_no = $this->num_format_helper->new_savings_acc_no();
        return $new_account_no === FALSE ? $this->input->post("account_no") : $new_account_no;
    }

    public function view($acc_id)
    {
        $this->data['acc_id'] = $acc_id;    //used for the share tab
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode('id IN(1,2,4,6,7,8)');
        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] = array_column($this->data['module_list'], "module_id");

        $this->data['organisation_format'] = $this->Organisation_format_model->get_formats();
        $this->data['amountcalculatedas'] = $this->miscellaneous_model->get_amountcalculatedas();
        $this->data['tchannel'] = $this->TransactionChannel_model->get();
        $this->data['selected_account'] = $this->Loan_guarantor_model->get_guarantor_savings2("(j.state_id = 5 OR j.state_id = 7 OR j.state_id = 12 OR j.state_id = 17 OR j.state_id = 18)", $acc_id);
        // print_r($this->data['selected_account']);die();
        $this->data['available_savings_range_fees'] = $this->saving_fees_model->get_range_fees();

        $this->data['active_loans'] = $this->client_loan_model->get("(loan_state.state_id=7 OR loan_state.state_id=13) AND a.member_id= " . $this->data['selected_account']['member_id']);

        $this->data['withdraw_fees'] = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $this->data['selected_account']['deposit_Product_id'], 'sf.chargetrigger_id' => '3')); //withdraw
        $this->data['deposit_fees'] = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $this->data['selected_account']['deposit_Product_id'], 'sf.chargetrigger_id' => '4'));
        if (empty($this->data['selected_account'])) {
            show_404();
        }
        $this->data['new_account_no'] = $this->data['selected_account']['account_no'];
        if (intval($this->data['selected_account']['client_type']) == 2) {
            $this->data['group_members'] = $this->Group_member_model->get_group_member_savings('g.id=' . $this->data['selected_account']['member_id'], $acc_id);
        }

        $available_to_filter = "availableto IN (" . ($this->data['selected_account']['client_type']) . ",3)";
        $this->data['products'] = $this->DepositProduct_model->get($available_to_filter);
        //fetch withdraw fees   =================================================
        $this->data['withdraw_fees'] = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $this->data['selected_account']['deposit_Product_id'], 'sf.chargetrigger_id' => '3')); //withdraw
        $this->data['deposit_fees'] = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $this->data['selected_account']['deposit_Product_id'], 'sf.chargetrigger_id' => '4'));
        //$this->load->model('staff_model');

        $this->data['available_interest_range_rates'] = $this->DepositProduct_model->get_range_rates();

        $this->data['title'] = $this->data['selected_account']['account_no'] . " - Account Details";
        $this->data['sub_title'] = $this->data['selected_account']['account_no'];
        $this->data['modalTitle'] = "Edit Savings Form";

        $neededjs = array("plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/select2/select2.full.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->template->title = $this->data['title'];

        $this->template->content->view('savings_account/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    function change_status()
    {

        $response['success'] = FALSE;
        if (($response['success'] = $this->Savings_account_model->change_status()) === true) {
            $response['message'] = " Savings account deleted";
            $this->helpers->activity_logs($_SESSION['id'], 6, "Updating savings account", $response['message'] . " # " . $this->input->post('id'), NULL, $this->input->post('id'));
        }


        echo json_encode($response);
    }

    function change_state()
    {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to modify this record";
        $response['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        $this->form_validation->set_rules('account_id', 'Account', 'required');
        $this->form_validation->set_rules('state_id', 'State', 'required');
        $this->form_validation->set_rules('comment', 'Comment', 'required|trim|min_length[5]');
        if ($this->form_validation->run() === true) {
            if (($response['success'] = $this->Savings_account_model->change_state()) === true) {
                if ($this->input->post('state_id') == 7 && $this->input->post('comment') == null) {
                    $this->auto_subscription();
                }
                $response['message'] = " Status Changed";
                $response['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings2('1=1', $_POST['account_id']);
                $response['state_totals'] = $this->Savings_account_model->state_totals();
            }
        } else {
            $response['message'] = validation_errors();
        }

        // }
        echo json_encode($response);
    }

    public function check_channel_balance()
    {
        $account_id = $this->TransactionChannel_model->get($this->input->post('transaction_channel_id'));
        $response = ['success' => $this->channel_balance($account_id['linked_account_id'])];
        if (!$response['success']) {
            echo json_encode($response['success']);
        } else {
            echo json_encode($response['success']);
        }
    }

    public function channel_balance($account_id = false)
    {
        $amount = $this->input->post('amount');
        $message = $this->helpers->account_balance($account_id, $amount);
        return $message;
    }

    public function deposit_fees()
    {
        //fetch deposite fees  
        $this->data['deposit_fees'] = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $this->input->post('new_product_id'), 'sf.chargetrigger_id' => '4', 'sf.status_id' => '1', 's.status_id' => '1'));
        $response['deposit_fees'] = $this->data['deposit_fees'];
        echo json_encode($response);
    }

    public function deposit_fees2()
    {
        $this->data['deposit_fees'] = $this->Savings_product_fee_model->get(' s.saving_product_id=' . $this->input->post('new_product_id') . ' AND sf.chargetrigger_id IN(4,' . $this->input->post('payment_id') . ')');
        $response['deposit_fees'] = $this->data['deposit_fees'];
        echo json_encode($response);
    }


    public function withdraw_fees()
    {
        //fetch withdraw fees  
        $this->data['withdraw_fees'] = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $this->input->post('new_product_id'), 'sf.chargetrigger_id' => '3'));
        $response['withdraw_fees'] = $this->data['withdraw_fees'];
        echo json_encode($response);
    }
    public function withdraw_fees2()
    {
        //fetch withdraw fees  
        $this->data['withdraw_fees'] = $this->Savings_product_fee_model->get(' s.saving_product_id=' . $this->input->post('new_product_id') . ' AND sf.chargetrigger_id IN(3,' . $this->input->post('payment_id') . ')');
        $response['withdraw_fees'] = $this->data['withdraw_fees'];
        echo json_encode($response);
    }
    public function transfer_fees()
    {
        //fetch transfer_fees  
        $this->data['transfer_fees'] = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $this->input->post('new_product_id'), 'sf.chargetrigger_id' => '5'));
        $response['transfer_fees'] = $this->data['transfer_fees'];
        echo json_encode($response);
    }

    public function approval_fees()
    {
        //fetch fees_upon_approval  
        $this->data['fees_upon_approval'] = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $this->input->post('new_product_id'), 'sf.chargetrigger_id' => '7'));
        $response['fees_upon_approval'] = $this->data['fees_upon_approval'];
        echo json_encode($response);
    }

    public function get_savings_accounts()
    {
        //fetch savings_accounts  
        $this->data['savings_accounts'] = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7 AND a.id!=' . $this->input->post('account_no_id'));
        $response['savings_accounts'] = $this->data['savings_accounts'];
        echo json_encode($response);
    }

    public function AcStatement($acc_id, $start_date, $end_date)
    {
        $this->load->model('branch_model');
        $where = "  account_no_id = " . $acc_id . " AND transaction_date BETWEEN '" . ($start_date) . "' AND '" . ($end_date) . "' AND tn.status_id=1";
        $this->load->helper('pdf_helper');
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['title'] = $_SESSION["org_name"];

        $data['selected_account'] = $this->Loan_guarantor_model->get_guarantor_savings2("(j.state_id = 5 OR j.state_id = 7 OR j.state_id = 12 OR j.state_id = 17 OR j.state_id = 18)", $acc_id);

        $data['sub_title'] = $data['selected_account']['member_name'] . " Account Statement";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;

        $data['transactions'] = $this->Transaction_model->get($where);
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $data['the_page_data'] = $this->load->view('savings_account/pdf_account_statement', $data, TRUE);
        $this->load->view('includes/pdf_template', $data);
    }

    //auto subscription
    public function auto_subscription()
    {
        $this->load->model('member_model');
        $this->load->model('subscription_plan_model');
        $this->load->model('client_subscription_model');
        $this->load->model('transaction_model');

        $savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $this->input->post('account_id'));
        $current_balance = $savings_data['cash_bal'];
        // $current_balance=($savings_data['cash_bal']-$savings_data['min_balance']);#Uncomment this if the organisation allows payment on an account with saving after reserving the minimum balance
        $member = $this->member_model->get_member($savings_data['member_id']);

        if (isset($member['subscription_plan_id']) && $member['subscription_plan_id'] != '') {
            $subscription_plan = $this->subscription_plan_model->get($member['subscription_plan_id']);
            if ($current_balance >= $subscription_plan['amount_payable'] && $subscription_plan['first_repayment_starts_upon'] == 2) {
                $deduction_data['amount'] = $subscription_plan['amount_payable'];
                $deduction_data['account_no_id'] = $savings_data['id'];
                $deduction_data['narrative'] = 'Automatic deduction payment made to clear your ' . ucfirst($subscription_plan['plan_name']);

                # call the tranfer function
                $this->db->trans_begin();
                $transaction_data = $this->transaction_model->deduct_savings($deduction_data);
                if (is_array($transaction_data)) {
                    $deduction_data['amount'] = $subscription_plan['amount_payable'];
                    $deduction_data['client_id'] = $savings_data['member_id'];
                    $deduction_data['narrative'] = 'Payment  made to clear ' . ucfirst($subscription_plan['plan_name']);
                    $subscription_payment_id = $this->client_subscription_model->set2($deduction_data);

                    if (is_numeric($subscription_payment_id)) {
                        $transaction_data['member_id'] = $member['id'];
                        $transaction_data['account_no_id'] = $savings_data['id'];
                        $transaction_data['comment'] = 'Payment made for ' . ucfirst($subscription_plan['plan_name']);
                        $transaction_data['amount'] = $subscription_plan['amount_payable'];
                        $this->subscription_journal_transaction($subscription_payment_id, $transaction_data);
                        $message = "Payment of amount " . round($subscription_plan['amount_payable'], 2) . "/= has been made from your account " . $savings_data['account_no'] . " today " . date('d-m-Y H:i:s');
                        $this->helpers->send_email($savings_data['id'], $message, false);
                        #check for the sms module
                        if (!empty($result = $this->miscellaneous_model->check_org_module(22, 1))) {
                            $this->helpers->notification($savings_data['id'], $message, false);
                        }
                        //complete the transaction and check the status of the db
                        if ($this->db->trans_status()) {
                            $this->db->trans_commit();
                        } else {
                            $this->db->trans_rollback();
                        }
                    } else {
                        $this->db->trans_rollback();
                    }
                } else {
                    $this->db->trans_rollback();
                }
            }
        }
    }

    //Subscription jornal entry
    private function subscription_journal_transaction($client_subscription_id, $transaction_data)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('member_model');
        $payment_date = date('d-m-Y');

        $data = [
            'transaction_date' => $payment_date,
            'description' => 'Subscription payment made from a client\'s savings',
            'ref_no' => $transaction_data['transaction_no'],
            'ref_id' => $client_subscription_id,
            'status_id' => 1,
            'journal_type_id' => 11
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        //then we prepare the journal transaction lines
        $client = $this->member_model->get($transaction_data['member_id']);
        if (!empty($client)) {
            $this->load->model('accounts_model');
            $this->load->model('subscription_plan_model');
            $this->load->model('DepositProduct_model');
            $this->load->model('savings_account_model');
            $this->load->model('transactionChannel_model');
            $this->load->model('journal_transaction_line_model');

            $savings_account = $this->savings_account_model->get($transaction_data['account_no_id']);

            $subscription_plan = $this->subscription_plan_model->get($client['subscription_plan_id']);
            $debit_or_credit1 = $this->accounts_model->get_normal_side($subscription_plan['income_account_id'], false);

            $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);
            $debit_or_credit2 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);
            $data = [
                [
                    $debit_or_credit1 => $transaction_data['amount'],
                    'narrative' => $transaction_data['comment'] . " " . $payment_date,
                    'account_id' => $subscription_plan['income_account_id'],
                    'status_id' => 1
                ],
                [
                    $debit_or_credit2 => $transaction_data['amount'],
                    'narrative' => $transaction_data['comment'] . " " . $payment_date,
                    'account_id' => $savings_product_details['savings_liability_account_id'],
                    'status_id' => 1
                ]
            ];
            $this->journal_transaction_line_model->set($journal_transaction_id, $data);
        }
    }

    public function get_new_account_no()
    {
        $this->load->library("num_format_helper");
        $new_account_no = $this->num_format_helper->new_savings_acc_no();
        $data['data'] = ['new_account_no' => $new_account_no];
        echo json_encode($data);
    }
}
