<?php

/**
 * Description of Chart of accounts
 *
 * @author reagan
 */
class Accounts extends CI_Controller {
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
        $this->load->model('accounts_model');
        $this->load->model('ledger_model');
        $this->load->model('Fiscal_month_model');
        $this->load->model('miscellaneous_model');
        $this->load->model('RolePrivilege_model');
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

    public function jsonList() {
        $data['data'] = $this->accounts_model->get();
        echo json_encode($data);
    }

    public function index() {
        // $mydata =$this->accounts_model->get3("category_id IN(1,2,3)");
        // print_r($mydata);die();
        $this->load->model('transactionChannel_model');
        $this->load->model('staff_model');
        $this->load->model('country_model');
        $this->load->model('fiscal_model');
        $this->load->model('Share_issuance_model');
        $rand_no = mt_rand(1000, 1200);
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/selectize/standalone/selectize.min.js","plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js","plugins/steps/jquery.steps.min.js?v=$rand_no","plugins/steps/jquery.steps.fix.js");

        $neededcss = array("fieldset.css","plugins/selectize/css/selectize.default.css", "plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css","plugins/steps/jquery.steps.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['transaction_channels'] = $this->transactionChannel_model->get();
        $this->data['payment_modes'] = $this->miscellaneous_model->get_payment_mode();
        $this->data["subcat_list"] = $this->accounts_model->get_subcat_list();
        $this->data["journal_types"] = $this->accounts_model->get_journal_types();
        $this->data['trial_balance'] =$this->reports_model->get_accounts_sums();
        $this->data['balance_sheet'] =$this->reports_model->get_accounts_sums();
        $this->data["depreciation_method"] = $this->miscellaneous_model->get_depreciation_method();
        $this->data['countries'] = $this->country_model->get();
        $this->data['staff_list'] = $this->staff_model->get_registeredby("status_id=1");
        $this->data['fiscal_years'] = $this->fiscal_model->get('close_status=1');
        $this->data['share_issuances'] = $this->Share_issuance_model->get(['share_issuance.status_id', 1]);
        $this->data['module_list']=$this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] =array_column($this->data['module_list'],"module_id");
        $this->data['title'] = $this->data['sub_title'] = "Accounting";
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('accounts/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function view($id) {
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js");

        $neededcss = array("fieldset.css", "plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css");
        $this->data["subcat_list"] = $this->accounts_model->get_subcat_list();

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->load->model('Ledger_model');
        $this->data['ledger_account'] = $this->accounts_model->get($id);
        if (empty($this->data['ledger_account'])) {
            redirect("my404");
        }

        $this->data['title'] = $this->data['sub_title'] = $this->data['ledger_account']['account_name'];
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('accounts/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function create() {
        if ((is_numeric($this->input->post('account_type_id'))) && ($this->input->post('account_type_id') == 2)) {
            $this->form_validation->set_rules('manual_entry', 'Manual Entry', array('required'), array('required' => 'Select whether its a %s or Not'));
        }
        $this->form_validation->set_rules('description', 'description', array('required'), array('required' => '%s must be entered'));
        if ($_POST['opening_balance']>0) {
            $this->form_validation->set_rules('normal_balance_side', 'Debit or Credit', array('required'), array('required' => 'Select whether it is a %s'));
        }
        $this->form_validation->set_rules('account_code', 'Account number', array('required', "callback_check_account_no"), array('required' => '%s must be entered', "check_account_no" => '%s already taken, input another'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $account_id = $this->input->post("id");
            $this->load->model('journal_transaction_model');
            $this->load->model('journal_transaction_line_model');
            if (is_numeric($account_id)) {
                if ($this->accounts_model->set()) {
                $old_account_details = $this->accounts_model->get($account_id);
                    //lets get the transaction id for the opening balance, if any
                    $op_bal_transaction = $this->journal_transaction_model->get_op_bal_trans($old_account_details);
                    if ($this->input->post('opening_balance') !== NULL || $this->input->post('opening_balance') > 0) {
                        if (!empty($op_bal_transaction)) {
                            $feedback['success'] = $this->journal_transaction_line_model->set_open_balance_line($account_id, $op_bal_transaction['id']);
                        } else {
                            $journal_transaction_id = $this->journal_transaction_model->set_open_balance(TRUE);
                            $feedback['success'] = $this->journal_transaction_line_model->set_open_balance_line($account_id, $journal_transaction_id, TRUE);
                        }
                    } else {
                        if (!empty($op_bal_transaction)) {
                            $this->journal_transaction_model->delete2($op_bal_transaction['id']);
                        }
                        //lets delete the 
                        $feedback['success'] = true;
                    }
                    $feedback['message'] = "Account details successfully updated";
                    $feedback['accounts'] = $this->accounts_model->get($account_id);

                      $this->helpers->activity_logs($_SESSION['id'],8,"Creating Journal",$feedback['message']." -# ".$account_id,$account_id,$account_id);

                } else {
                    $feedback['message'] = "There was a problem updating the account details";
                     $this->helpers->activity_logs($_SESSION['id'],8,"Creating Journal",$feedback['message']." -# ".$account_id,NULL,null);
                }
            } else {
                //$account_code=$this->get_account_code();
                $account_id = $this->accounts_model->set();

                if (is_numeric($account_id)) {
                    if (is_numeric($this->input->post('opening_balance')) && $this->input->post('opening_balance') > 0) {
                        $journal_transaction_id = $this->journal_transaction_model->set_open_balance();
                        $feedback['success'] = $this->journal_transaction_line_model->set_open_balance_line($account_id, $journal_transaction_id);
                        $feedback['message2'] = "Opening balance saved";
                    } else {
                        $feedback['success'] = true;
                    }
                    $feedback['message'] = "Account details successfully saved";
                    $this->helpers->activity_logs($_SESSION['id'],8,"Created Journal Account",$feedback['message'],$account_id,$account_id);
                    //$feedback['parent_accounts'] = $this->accounts_model->get_parent_accounts2();
                } else {
                    $feedback['message'] = "There was a problem saving the account data";
                    $this->helpers->activity_logs($_SESSION['id'],8,"Creating Journal",$feedback['message'],NULL,NULL);
                }
            }
        }
        echo json_encode($feedback);
    }

    public function change_status() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
        if (in_array('4', $this->data['accounts_privilege'])) {
            $this->data['message'] = $this->accounts_model->change_status();
            if ($this->data['message'] === true) {
                $this->data['success'] = TRUE;
            }
        }
        echo json_encode($this->data);
    }

    public function pay_with(){
        if ($this->input->post('action')!=NULL && is_numeric($this->input->post('action'))) {
            $action=$this->input->post('action');
        }else{
            $action=false;
        }
        $data['pay_with'] = $this->accounts_model->get_pay_with($this->input->post('bank_or_cash'),$action);
        echo json_encode($data);
    }

    function delete(){
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
        if (in_array('4', $this->data['accounts_privilege'])) {
            if (($response['success'] = $this->accounts_model->delete($this->input->post('id'))) === true) {
                $response['message'] = "Account Details successfully deleted";
            }
        }
        echo json_encode($response);
    }

    public function parent_accounts() {
        $data['sub_accounts'] = $this->accounts_model->get_sub_accounts($this->input->post('account_type_id'));
        echo json_encode($data);
    }

    public function validate_acc_no() {
        $response = ['success' => $this->check_account_no()];
        if (!$response['success']) {
            echo json_encode("Account number already taken, input another");
        } else {
            echo json_encode($response['success']);
        }
    }

    public function check_account_no($account_code = false) {
        if ($account_code === false) {
            $account_code = $this->security->xss_clean($this->input->post("account_code"));
        }
        $existing_account = $this->accounts_model->check_account_no_exists($account_code);
        $acc_no_not_taken = empty($existing_account);
        if (!$acc_no_not_taken) {
            $this->form_validation->set_message("check_account_no", "Account number already taken by {$existing_account['account_name']} ( {$existing_account['account_code']} ), input another");
        }
        return $acc_no_not_taken;
    }

    public function get_account_code() {
        $group_code = $this->input->post('account_code');
        $num = substr($group_code, -1);
        $next_group = substr_replace($group_code, $num + 1, -1);
        $this->data['last_item'] = $this->accounts_model->get_last_account($group_code, $next_group);
        if (empty($this->data['last_item']['account_code'])) {
            $last_code = $group_code . "-0";
        } else {
            $last_code = $this->data['last_item']['account_code'];
        }
        $num2 = substr($last_code, -1);
        $next_code = substr_replace($last_code, $num2 + 1, -1);

        return $next_code;
    }


    public function print_receipt()
    {
        //echo json_encode($this->input->post());
        //die;
        if (empty($this->session->userdata('id'))) {
            redirect("welcome", "refresh");
        }
        //$payment_id = $this->input->post('payment_id');
        $this->load->model('branch_model');
        $this->load->model('organisation_model');
        //$this->load->model('loan_installment_payment_model');

        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        //$data['data'] = $this->loan_installment_payment_model->get_receipt();
        $data['date'] = $this->input->post('date');
        $data['narrative'] = $this->input->post('narrative');
        $data['t_amount'] = $this->input->post('amount');
        $data['trans_id'] = $this->input->post('trans_id');

        

        $this->load->view('accounts/transaction/print_receipt', $data);
    }

}
