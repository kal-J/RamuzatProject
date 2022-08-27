<?php

/**
 * Description of fiscal year
 *
 * @author reagan
 */
class Fiscal_year extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model('fiscal_model');
        $this->load->model('Dashboard_model');
        $this->load->model('journal_transaction_model');
        $this->load->model('journal_transaction_line_model');
    }

    public function jsonList() {
        $where = FALSE;
        if ($this->input->post('organisation_id') !== NULL) {
            $where = "organisation_id = " . $this->input->post('organisation_id');
        }
        $this->data['data'] = $this->fiscal_model->get($where);
        echo json_encode($this->data);
    }

    public function create() {
        $this->form_validation->set_rules('start_date', 'Start Date', array('required'), array('required' => '%s must be entered'));
        //$this->form_validation->set_rules('end_date', 'End Date', 'callback_end_date_check');

        $end_date = $this->end_date_generate();
        $feedback['success'] = false;

        if (empty($this->input->post('organisation_id'))) {
            $organisation_id = $_SESSION['organisation_id'];
        } else {
            $organisation_id = $this->input->post('organisation_id');
        }

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {

                if ($this->fiscal_model->update($end_date)) {
                    $feedback['success'] = true;
                    $feedback['message'] = "The updated Fiscal Year will end on [ {$end_date} ]";
                    $feedback['organisation'] = $this->fiscal_model->get($_POST['id']);
                } else {
                    $feedback['message'] = "There was a problem updating the Fiscal year details";
                }
            } else {
                $this->data['fiscal_new'] = $this->Dashboard_model->get_current_fiscal_year($organisation_id, 2);
                if (empty($this->data['fiscal_new'])) {
                    $org = $this->fiscal_model->set($end_date);
                    if ($org) {
                        $feedback['success'] = true;
                        $feedback['message'] = "The New Fiscal Year will end on [ {$end_date} ]";
                    } else {
                        $feedback['message'] = "There was a problem saving the Fiscal year";
                    }
                } else {
                    $feedback['message'] = "Please Activate the existing fiscal year, Two or more Inactive fiscal years are not allowed!";
                }
            }
        }
        echo json_encode($feedback);
    }

    //////////////////////////////////////////////////////////////////////////////
    // ======================     START ADJUSTING ENTRIES  ========================//
    //////////////////////////////////////////////////////////////////////////////
    
  public function close_fiscal_year() {
        $this->load->model("loan_installment_payment_model");
        $this->load->model('client_loan_model');
        
        $feedback = ['message' => "Access denied, you do not have the appropriate privileges to perform this operation", "success" => false];
        $module_access = $this->helpers->org_access_module(8, $_SESSION['organisation_id']);
        if (!empty($module_access)) {
            $privilege_list = $this->helpers->user_privileges(8, $_SESSION['staff_id']);
            if (!empty($privilege_list)) {
                $this->form_validation->set_rules('fiscal_id', 'Fiscal year', array('required'), array('required' => '%s must be selected'));
                // $this->form_validation->set_rules('dividend_amount', 'Fiscal year', array('required'), array('required' => '%s must be selected'));
                if ($this->form_validation->run() === FALSE) {
                    $feedback['message'] = validation_errors();
                } else {
                    $this->db->trans_begin();
               // $fiscal_active = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'],1);
               // $start_date = date('d-m-Y', strtotime($fiscal_active['start_date']));
                
               //  // ACTIVE AND LOANS IN ARREARS
               // $active_and_arrears = $this->client_loan_model->get_active_arrears(FALSE,$fiscal_active['end_date']);

               // foreach ($active_and_arrears as $key => $loans) {
               //          $this_year_opening_bal = $loans['expected_interest'];
               //            // get current year installment
               //          $current_year_loan_intallments = $this->loan_installment_payment_model->sum_paid_installment("loan_installment_payment.client_loan_id=".$loans['id']." AND (payment_date BETWEEN '".$fiscal_active['start_date']."' AND '".$fiscal_active['end_date']."')");

               //          // get earned interest and unearned interest 
               //           $earned_interest =$current_year_loan_intallments['already_interest_amount'];
               //           $unearned_interest =$this_year_opening_bal-$current_year_loan_intallments['already_interest_amount'];
               //           // get income account id form product table
               //           $interest_income_account_id =$loans['interest_income_account_id']; // disbursement (get loan_id)
                         
               //           // journal transactions for adjusting income account with unearned income
               //              $debit_or_credit ="debit_amount";
               //              $end_date = date('d-m-Y', strtotime($fiscal_active['end_date']));

               //            $this->do_adjusting_journal_transaction($end_date,$interest_income_account_id,$loans['loan_no'],$unearned_interest,$debit_or_credit);
               //            // journal transactions for opening income account (new financial year) with unearned income
               //            $debit_or_credit2 ="credit_amount";
               //            //increament end_date to get start date (TRANSACTION DATE) for new financial year
               //            $start_date2 = date('d-m-Y', strtotime($fiscal_active['end_date']. ' +1 day'));
               //            if($this->do_adjusting_journal_transaction($start_date2,$interest_income_account_id,$loans['loan_no'],$unearned_interest,$debit_or_credit2)){
               //            }
               //  }
        $result =$this->zeroout_create_opening_balance();
        if($this->db->trans_status()){
            $this->db->trans_commit();
            $feedback['success'] = $result['success'];
            $feedback['message'] = $result['message'];
        }else{
            $this->db->trans_rollback();
           $feedback['success'] = false;
           $feedback['message'] = "Closing the financial year failed, contact IT Support";
        }
           
       } 
     }                    
   }
        echo json_encode($feedback);
  }

    private function do_adjusting_journal_transaction($start_date,$interest_income_account_id,$loan_no,$amount,$debit_or_credit){
                $data = [
                    'transaction_date' => $start_date,
                    'description' => "Adjusting entries as of ". $start_date,
                    'ref_no' => $loan_no,
                    'ref_id' => $interest_income_account_id,
                    'status_id' => 1,
                    'journal_type_id' => 27 // new journal type id for adjusting entries
                ];
                $journal_transaction_id = $this->journal_transaction_model->set($data);
                unset($data);
                if(is_numeric($journal_transaction_id)){
                         $data1[] = [
                            $debit_or_credit => $amount,
                            'narrative' =>"Adjusting entries as of ". $start_date,
                            'transaction_date' => $start_date,
                            'account_id' => $interest_income_account_id,
                            'status_id' => 1
                        ];
                }
            return $this->journal_transaction_line_model->set($journal_transaction_id, $data1);
    }

    ///////////////////////////////////////////////////////////////////////////////////////
    //=========================== START ADJUSTING ENTRIES ==============================///
    ///////////////////////////////////////////////////////////////////////////////////////

    //closing the financial year
    public function zeroout_create_opening_balance()  {
        $this->load->model('accounts_model');
                 
                    $active_fiscal_year = $this->fiscal_model->get($this->input->post('fiscal_id'));
                  //print_r($active_fiscal_year); die();
                    
                    //$accounts_privilege = array_column($privilege_list, "privilege_code");
                    //we should check if the logged in user has the privilege to close the financial year
                    $income_summary_account = $this->accounts_model->get("ac.sub_category_id=17");
                    $retained_earning_account = $this->accounts_model->get("ac.sub_category_id=19");
                    $income_expense_accounts = $this->accounts_model->get3("category_id IN (4,5) AND (trl.transaction_date BETWEEN '".$active_fiscal_year['start_date']."' AND '".$active_fiscal_year['end_date']."' )");
                    
                    if (is_array($income_expense_accounts) && count($income_expense_accounts) > 0) {
                        if ($this->do_journal_transaction($income_expense_accounts, $income_summary_account[0], $retained_earning_account[0], $active_fiscal_year)) {
                            $start_date = date('d-m-Y', strtotime($this->input->post('end_date') . ' +1 day'));
                            
                            $end_date = $this->end_date_generate($this->input->post('end_date'));
                            $fiscal_id = $this->fiscal_model->set($end_date, $start_date);
                            if (is_numeric($fiscal_id)) {
                            $asset_liability_equity_account=$this->accounts_model->get3("category_id IN(1,2,3) AND (trl.transaction_date BETWEEN '".$active_fiscal_year['start_date']."' AND '".$active_fiscal_year['end_date']."' )");
                                if($this->do_opening_balance_journal_transaction($asset_liability_equity_account,$start_date, $fiscal_id)){
                                    $this->fiscal_model->update_close_status($this->input->post('fiscal_id'),0);
                          
                                $feedback['message'] = "Financial Year Successfully closed";
                                $feedback['success'] = true;
                                }else{
                                    $feedback['success'] = false;
                                    $feedback['message'] = "There was a problem posting opening balances";
                                }
                            } else {
                                $feedback['success'] = false;
                                $feedback['message'] = "There was a problem creating the next Financial year";
                            }
                        } else {
                            $feedback['success'] = false;
                            $feedback['message'] = "Failure in adjusting expense and income accounts";
                        }
                    } else {
                            $feedback['success'] = false;
                            $feedback['message'] = "There are no expense and income accounts available, in order to close the financial year";
                        }
                
         return $feedback;
    }

    private function do_journal_transaction($income_expense_accounts, $income_summary_account, $retained_earning_account, $fiscal_year) {
        $this->load->model("journal_transaction_model");
        $income_expense_totals = ['debit' => 0, 'credit' => 0];

        $data = [
            'transaction_date' => $this->input->post('end_date'),
            'description' => "Closing accounts, " . $fiscal_year['start_date'] . " " . $fiscal_year['end_date'],
            'ref_no' => $fiscal_year['id'],
            'ref_id' => $fiscal_year['id'],
            'status_id' => 1,
            'journal_type_id' => 26
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        if (is_numeric($journal_transaction_id)) {
            foreach ($income_expense_accounts as $income_expense_account) {
                $debit_credit_amount = $income_expense_account['account_balance'];  //the credit or debit amount
                if ($income_expense_account['normal_balance_side'] == 1) {
                    $debit_or_credit1 = 'credit_amount';
                    $income_expense_totals['debit'] = $income_expense_totals['debit'] + $debit_credit_amount;
                } else{
                      $debit_or_credit1 = 'debit_amount'; //closing the accounts requires doing the opposite transaction
                     $income_expense_totals['credit'] = $income_expense_totals['credit'] + $debit_credit_amount;
                }

                if ($debit_credit_amount != null && !empty($debit_credit_amount) && $debit_credit_amount != '0') {
                    $data[] = [
                        $debit_or_credit1 => $debit_credit_amount,
                        'narrative' => "Closing the financial year [".$income_expense_account['account_name']."]",
                        'transaction_date' => $this->input->post('end_date'),
                        'account_id' => $income_expense_account['id'],
                        'status_id' => 1
                    ];
                }
            }


            $normal_balance_side = 'credit_amount';
            $total_amount = $income_expense_totals['credit'];
            if (abs($income_expense_totals['credit']) >= abs($income_expense_totals['debit'])) {
                $income_summary = abs($income_expense_totals['credit']) - abs($income_expense_totals['debit']);
                $debit_or_credit3 = "credit_amount";
                $debit_or_credit4 = "debit_amount";
            } else {
                $income_summary = abs($income_expense_totals['debit']) - abs($income_expense_totals['credit']);
                $debit_or_credit3 = "debit_amount";
                $debit_or_credit4 = "credit_amount";
            }
            //posting income
            $data[] = [
                'credit_amount' => $income_expense_totals['credit'],
                'narrative' => "Total Income",
                'transaction_date' => $this->input->post('end_date'),
                'account_id' => $income_summary_account['id'],
                'status_id' => 1
            ];

            //and expenses to the income summary account
            $data[] = [
                'debit_amount' => $income_expense_totals['debit'],
                'narrative' => "Total expenses",
                'transaction_date' => $this->input->post('end_date'),
                'account_id' => $income_summary_account['id'],
                'status_id' => 1
            ];
            // transactions between income summary a/c and retained earnings
            $data[] = [
                $debit_or_credit4 => $income_summary,
                'narrative' => "Posting to income summary account",
                'transaction_date' => $this->input->post('end_date'),
                'account_id' => $income_summary_account['id'],
                'status_id' => 1
            ];

            $data[] = [
                $debit_or_credit3 => $income_summary,
                'narrative' => "Posting from income summary account",
                'transaction_date' => $this->input->post('end_date'),
                'account_id' => $retained_earning_account['id'],
                'status_id' => 1
            ];
            return $this->journal_transaction_line_model->set($journal_transaction_id, $data);
        }
    }

    private function do_opening_balance_journal_transaction($asset_liability_equity_totals, $start_date, $fiscal_id) {
        $this->load->model("journal_transaction_model");

        $data = [
            'transaction_date' => $start_date,
            'description' => "Opening Balance as of , " . $start_date,
            'ref_no' => $fiscal_id,
            'ref_id' => $fiscal_id,
            'status_id' => 1,
            'journal_type_id' => 18
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        if (is_numeric($journal_transaction_id)) {
            foreach ($asset_liability_equity_totals as $asset_liability_equity_account) {

                // Dont Set opening balance to 0 if an account had no transaction
                if (($asset_liability_equity_account['total_credit'] !=0) || ($asset_liability_equity_account['total_debit'] !=0)) {
                    $credit_amount = $asset_liability_equity_account['total_credit'];  
                    $debit_amount = $asset_liability_equity_account['total_debit'];  
                
                // check for normal balancing side
                if ($asset_liability_equity_account['normal_balance_side'] == 1) {
                    $closing_balance = abs($debit_amount) - abs($credit_amount);
                    if($closing_balance<0){
                        $debit_or_credit = 'credit_amount';
                    }else{
                        $debit_or_credit = 'debit_amount';
                    }
               }else {
                    $closing_balance = abs($credit_amount) - abs($debit_amount);
                     if($closing_balance<0){
                        $debit_or_credit = 'debit_amount';
                    }else{
                        $debit_or_credit = 'credit_amount';
                    }
                }
                //prepare data for each account
                    $data[] = [
                        $debit_or_credit => abs($closing_balance),
                        'narrative' => $asset_liability_equity_account['account_name']." Opening Balance as of , " . $start_date,
                        'transaction_date' => $start_date,
                        'account_id' => $asset_liability_equity_account['id'],
                        'status_id' => 1
                    ];
                }
            }
            return $this->journal_transaction_line_model->set($journal_transaction_id, $data);
        }
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
        date_add($date1, date_interval_create_from_date_string('1 year'));
        $auto_end_date = date_create(date_format($date1, 'Y-m-d'));
        date_sub($auto_end_date, date_interval_create_from_date_string('1 day'));
        $gen_end_year = date_format($auto_end_date, 'd-m-Y');

        return $gen_end_year;
    }

    public function start_date_check() {
        $start_date = $this->input->post('start_date');
        if (empty($start_date)) {
            $this->form_validation->set_message('start_date_check', 'The {field} Can not be empty');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function check_leap_year($date) {
        $year = date("Y", strtotime($date));
        $leap = date('L', mktime(0, 0, 0, 1, 1, $year));
        if ($leap == 1) {
            $daysinyear = 366;
        } else {
            $daysinyear = 365;
        }
        return $daysinyear;
    }

    public function change_status() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
        //   if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {

        $this->data['fiscal_new'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        if (empty($this->data['fiscal_new'])) {
            $this->data['message'] = $this->fiscal_model->change_status();
            if ($this->data['message'] === true) {
                $this->data['success'] = TRUE;
                $this->data['message'] = "Successfully activated! Please refresh your page to continue";
            }
        } else {
            $this->data['message'] = "Please Deactivate the current fiscal year, Two or more Active fiscal years are not allowed!";
        }
        //   }
        echo json_encode($this->data);
    }

    public function inactivate() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
        //   if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {

        $this->data['fiscal_new'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 2);
        if (empty($this->data['fiscal_new'])) {
            $this->data['message'] = $this->fiscal_model->change_status();
            if ($this->data['message'] === true) {
                $this->data['success'] = TRUE;
                $this->data['message'] = "Fiscal year now Inactive!";
            }
        } else {
            $this->data['message'] = "Please Deactivate any (Inactive) fiscal year, Two or more Inactive fiscal years are not allowed!";
        }
        //   }
        echo json_encode($this->data);
    }

    public function deactivate() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
        //   if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {

        $this->data['message'] = $this->fiscal_model->change_status();
        if ($this->data['message'] === true) {
            $this->data['success'] = TRUE;
            $this->data['message'] = "Fiscal year Deactivated";
        }
        //   }
        echo json_encode($this->data);
    }
    public function undo_close_fy() {
        $this->load->model('accounts_model');

        $this->data['message'] = "Rollback failed, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
        $fy_id = $this->fiscal_model->get_fiscal_id();
        $variables=$this->accounts_model->get_journal_ids();
        
        foreach ($variables as $key => $id) {
            $ids[]=$id['id'];
        }
        $this->data['message1'] = $this->fiscal_model->delete($fy_id['ref_no']);
        $this->fiscal_model->update_close_status($this->input->post('fiscal_id'),1);
        if ($this->data['message1'] === true) {
            $this->data['message2'] = $this->accounts_model->rollback_jtrl($ids);
            if ($this->data['message2'] === true) {
                 $this->data['message3'] = $this->accounts_model->rollback_jtr($ids);
                  if ($this->data['message3'] === true) {
                    $this->data['success'] = TRUE;
                    $this->data['message'] = "Fiscal year Rollback successfully";
                  }
            }
        }
        echo json_encode($this->data);
    }
    
    public function activate() {
        $this->data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
        $this->data['success'] = FALSE;
        //   if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        $this->data['message'] = $this->fiscal_model->activate();
        if ($this->data['message'] === true) {
            $this->data['success'] = TRUE;
            $this->data['message'] = "Fiscal year has been Activated";
        }
        //   }
        echo json_encode($this->data);
    }

    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
        //  if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        if (($response['success'] = $this->fiscal_model->delete($this->input->post('id'))) === true) {
            $response['message'] = "Fiscal year successfully deleted";
        }
        //  }
        echo json_encode($response);
    }

}
