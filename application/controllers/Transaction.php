<?php

/**
 * Description of transaction
 *
 * @author diphas modified by Reagan
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Transaction extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        $this->load->library('helpers');
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model('Transaction_model');
        $this->load->model('organisation_model');
        $this->load->model('savings_account_model');
        $this->load->model('Loan_guarantor_model');
        $this->load->model('Group_member_model');
        $orgdata['org'] = $this->organisation_model->get(1);
        $orgdata['branch'] = $this->organisation_model->get_org(1);
        $this->organisation = $orgdata['org']['name'];
        $this->contact_number = $orgdata['branch']['office_phone'];
    }

    public function jsonList2()
    {

        $all_trans = $this->Transaction_model->get_trans();
        $transactions = $this->Transaction_model->get_dTable_trans();
        $trans_rows = current($this->Transaction_model->get_found_rows());
        $data['draw'] = intval($this->input->post('draw'));
        $data['data'] = $transactions;
        $data['recordsTotal'] = $all_trans;
        $data['recordsFiltered'] = $trans_rows;
        echo json_encode($data);
    }

    public function total_deposits()
    {
        $where = FALSE;
        if ($this->input->post('transaction_id') !== NULL) {
            $where = "transaction_id = " . $this->input->post('transaction_id');
        }
        $data['data'] = $this->Transaction_model->total_deposits($where);
        echo json_encode($data);
    }

    public function create()
    {
        $this->load->model("miscellaneous_model");

        $this->form_validation->set_rules('amount', 'Amount', array('required'), array('required' => '%s must be entered'));
        if ($this->input->post('transaction_type_id') == 3) {
            $this->form_validation->set_rules('savings_account_id', 'savings account', array('required'), array('required' => '%s must be selected'));
        } else {

            $this->form_validation->set_rules('payment_id', 'Payment Method', array('required'), array('required' => '%s must be entered'));
        }
        $this->form_validation->set_rules('transaction_type_id', 'Transaction type', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('narrative', 'Narrative', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('account_no_id', 'Account Number', array('required'), array('required' => '%s must be provided'));
        $this->form_validation->set_rules('transaction_date', 'transaction date', array('required'), array('required' => '%s must be provided'));
        //$this->form_validation->set_rules('opening_balance', 'Opening_balance', array('required'), array('required' => '%s must be provided'));
        if ($this->input->post('ahead_or') == 0 && $this->input->post('excess_for') > 0) {
            $this->form_validation->set_rules("excess_for", "More schedules ", "required|callback__check_schedules", array("required" => "%s must be entered", "_check_schedules" => "Some previous schedules can't be found"));
        }
        $this->form_validation->set_rules('state_id', 'Account state', array('required'), array('required' => '%s must be set'));
        $feedback['success'] = false;
        if ($this->input->post('transaction_type_id') == 1) {
            $msg_type = "Withdraw";
        } else if ($this->input->post('transaction_type_id') == 2) {
            $msg_type = "Deposit";
        } else if ($this->input->post('transaction_type_id') == 3) {
            $msg_type = "Transfer";
        } else {
            $msg_type = "";
        }

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $member_id = $this->savings_account_model->get2($_POST['account_no_id']);
            if ($this->input->post('transaction_type_id') == 3) {
                // to be added with transfer journals
                //$member_id2 = $this->savings_account_model->get2($_POST['savings_account_id']);
            }
            $this->db->trans_begin();

            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Transaction_model->update()) {
                    //activity log.
                    $feedback['message'] = $msg_type . " update successful";
                    //$feedback['transaction'] = $this->Transaction_model->get($_POST['id']);
                    $this->helpers->activity_logs($_SESSION['id'], 6, "Quick Savings deposit", $feedback['message'] . " # " . $this->input->post('id'), NULL, $this->input->post('id'));
                } else {
                    $feedback['message'] = $msg_type . " failed";

                    $this->helpers->activity_logs($_SESSION['id'], 6, "Quick Savings deposit", $feedback['message'] . " # " . $this->input->post('id'), NULL, $this->input->post('id'));
                }
            } else {
                if ($this->input->post('transaction_type_id') == 3) {
                    $transaction_data = $this->Transaction_model->set($this->input->post('account_no_id'), false);
                } else {
                    $transaction_data = $this->Transaction_model->set();
                }
                if (is_array($transaction_data)) {
                    $feedback['success'] = true;
                    $feedback['message'] = $msg_type . "  successful";
                    if ($this->input->post('transaction_type_id') == 1) {
                        $this->helpers->activity_logs($_SESSION['id'], 6, "Savings Withdraw", $feedback['message'] . " on account " . $this->input->post('account_no'), $transaction_data['transaction_no'], $this->input->post('account_no'));
                        $this->withdraw_journal_transaction($transaction_data, $member_id);
                        if ($this->input->post('charges') !== NULL && $this->input->post('charges') != '') {
                            $this->we_charges_journal_transaction($transaction_data, $member_id);
                        }
                    } else if ($this->input->post('transaction_type_id') == 2) {

                        $this->helpers->activity_logs($_SESSION['id'], 6, "Deposit", $feedback['message'] . " on account " . $this->input->post('account_no'), $transaction_data['transaction_no'], $this->input->post('account_no'));
                        $this->deposit_journal_transaction($transaction_data, $member_id);
                        if ($this->input->post('charges') !== NULL && $this->input->post('charges') != '') {
                            $this->de_charges_journal_transaction($transaction_data, false, $member_id);
                        }
                        if ($this->input->post('mandatory_saving') == 1) {
                            //schedule hanlder for mandatory savings.
                            // $this->schedule_handler();
                        }
                    } else if ($this->input->post('transaction_type_id') == 3) {
                        $this->Transaction_model->set($this->input->post('savings_account_id'), 2, false, $transaction_data['transaction_id']);
                        $this->helpers->activity_logs($_SESSION['id'], 6, "Transfer", $feedback['message'], $transaction_data['transaction_no'], $this->input->post('account_no'));
                        if ($this->input->post('charges') !== NULL && $this->input->post('charges') != '') {
                            $this->we_charges_journal_transaction($transaction_data, $member_id);
                        }
                    } else {
                    }

                    $acc_id = $this->input->post('account_no_id');
                    $acc_id2 = $this->input->post('savings_account_id');
                    #SMS notification set up
                    $content = (!empty($acc_id2)) ? $this->input->post('savings_account_no') : $this->input->post('account_no');
                    if (!empty($acc_id)) {
                        $balance = $this->Loan_guarantor_model->get_guarantor_savings2("(j.state_id = 5 OR j.state_id = 7)", $acc_id);
                        $message = $msg_type . " of " . number_format($this->input->post('amount'), 2) . "/= is successful. Account: " . $content . ". A/C Balance: " . number_format($balance['real_bal']) . ".";
                        $email_response = $this->helpers->send_email($acc_id, $message, false);
                    }
                    if (!empty($result = $this->miscellaneous_model->check_org_module(22))) {
                        $message = $message . ".
" . $this->organisation . ", Contact " . $this->contact_number;
                        $text_response = $this->helpers->notification($acc_id, $message, false);
                        $feedback['message'] = $feedback['message'] . $text_response;
                    } //End of the check for the sms module
                    if (!empty($acc_id2)) {
                        $balance = $this->Loan_guarantor_model->get_guarantor_savings2("(j.state_id = 5 OR j.state_id = 7)", $acc_id2);

                        $message = $msg_type . " of " . number_format($this->input->post('amount'), 2) . "/= is successful. Account :" . $this->input->post('savings_account_no') . ". On " . date('d-m-Y') . ", Account Balance: " . number_format($balance['real_bal']) . ". ";
                        $email_response = $this->helpers->send_email($acc_id2, $message, false);
                        if (!empty($result = $this->miscellaneous_model->check_org_module(22))) {
                            $message = $message . ".
" . $this->organisation . ", Contact " . $this->contact_number;
                            $text_response = $this->helpers->notification($acc_id2, $message, false);
                        } //End of the check for the sms module

                    }

                    if ($this->db->trans_status()) {
                        $this->db->trans_commit();
                        $feedback['success'] = TRUE;

                        if (isset($_POST['account_details'])) {
                            if ($this->input->post('client_type') == 2) {
                                $feedback['group_members'] = $this->Group_member_model->get_group_member_savings('g.id=' . $this->input->post('group_member_id'), $acc_id);
                                $feedback['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings2("(j.state_id = 5 OR j.state_id = 7)", $acc_id);
                            } else {
                                $feedback['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings("(j.state_id = 5 OR j.state_id = 7)", $acc_id);
                            }
                        } else {
                            $feedback['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings("(j.state_id = 5 OR j.state_id = 7)");
                        }
                        if (isset($_POST['print'])) {
                            $feedback['insert_id'] = $transaction_data['transaction_id'];
                            $feedback['client_type'] = $this->input->post('client_type');
                        }
                    } else {
                        $this->db->trans_rollback();
                        $feedback['success'] = FALSE;
                        $feedback['message'] = "A problem happened while recording the transaction. Please Try again later or contact the system administrator";
                    }
                } else {
                    $feedback['message'] = "There was a problem with the transaction. Please try again";
                }
            }
        }
        echo json_encode($feedback);
    }

    public function _check_schedules()
    {
        $this->load->model('savings_schedule_model');
        $previous_schedules = $this->savings_schedule_model->get_schedule_numbers();
        if ($this->input->post('excess_for') <= count($previous_schedules)) {
            return true;
        }
        return false;
    }

    public function schedule_handler()
    {
        $this->load->model('savings_schedule_model');

        $where_clause = "from_date <='" . $this->helpers->yr_transformer($this->input->post('transaction_date')) . "' AND to_date >='" . $this->helpers->yr_transformer($this->input->post('transaction_date')) . "' AND fulfillment_code=1 AND saving_acc_id=" . $this->input->post('account_no_id');
        $data['fulfillment_code'] = 3; //Fullfilled


        if ($this->input->post('ahead_or') == 0 && $this->input->post('ahead_or') != NULL) {
            $previous_schedules = $this->savings_schedule_model->get_schedule_numbers();
            $this->savings_schedule_model->update($data, $where_clause);
            $new_where = "id in (";
            $rounds = 1;
            foreach ($previous_schedules as $key => $value) {
                if ($rounds > $this->input->post('excess_for')) {
                    break;
                }
                if ($new_where == "id in (") {
                    $new_where .= $value['id'];
                } else {
                    $new_where .= "," . $value['id'];
                }
                $rounds++;
            }
            $new_where .= ")";
            return $this->savings_schedule_model->update($data, $new_where);
        } else {
            $this->savings_schedule_model->update($data, $where_clause);
            $product_data = $this->savings_schedule_model->get_savings_account_product_data($this->input->post('account_no_id'));
            if ($product_data['saving_made_every'] == 1) {
                $schedule_interval = $product_data['saving_frequency'] . ' day';
            } elseif ($product_data['saving_made_every'] == 2) {
                $schedule_interval = $product_data['saving_frequency'] . ' week';
            } elseif ($product_data['saving_made_every'] == 3) {
                $schedule_interval = $product_data['saving_frequency'] . ' month';
            }
            $current_schedule_date = ($product_data['schedule_current_date'] && $product_data['schedule_current_date'] != '0000-00-00') ? $product_data['schedule_current_date'] : $product_data['schedule_start_date'];
            $new_savings_schedule = [];
            $schedule_data = [];

            if ($this->input->post('excess_for') && $this->input->post('excess_for') > 0 && isset($schedule_interval)) {
                for ($i = 0; $i < $this->input->post('excess_for'); $i++) {
                    $new_savings_schedule['start'] = $current_schedule_date;
                    $new_savings_schedule['end'] = date('Y-m-d', strtotime($schedule_interval, strtotime($current_schedule_date)));
                    $new_savings_schedule['end1'] = date('Y-m-d', strtotime('-1 day', strtotime($new_savings_schedule['end'])));

                    $schedule_data[] = array(
                        'saving_acc_id' => $this->input->post('account_no_id'),
                        'from_date' => $new_savings_schedule['start'],
                        'to_date' => $new_savings_schedule['end1'],
                        'fulfillment_code' => 3,
                        'date_created' => time(),
                        'created_by' => $_SESSION['id']
                    );
                    $current_schedule_date = $new_savings_schedule['end'];
                }
                return $this->savings_schedule_model->set($schedule_data);
            }
        }
        return false;
    }

    public function edit_transaction()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('transaction_date', 'Transaction Date', 'required');
        $this->form_validation->set_rules('narrative', 'Narrative', 'required');
        $feedback['success'] = false;

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {
            $this->db->trans_begin();
            if ($this->Transaction_model->edit_transaction()) {
                $this->Transaction_model->edit_journal_transaction();
                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $feedback['message'] = "Transaction has been successfully updated";
                    $feedback['success'] = true;

                    $this->helpers->activity_logs(
                        $_SESSION['id'],
                        6,
                        "Editing Transaction",
                        $feedback['message'] . " -# " . $this->input->post('transaction_no'),
                        NULL,
                        $this->input->post('transaction_no')
                    );
                } else {
                    $this->db->trans_rollback();
                    $feedback['message'] = "There was a problem updating this Transaction, please try again";
                    $this->helpers->activity_logs($_SESSION['id'], 6, "Editing Transaction", $feedback['message'] . " -# " . $this->input->post('transaction_no'), NULL, $this->input->post('transaction_no'));
                }
            } else {
                $this->db->trans_rollback();
                $feedback['message'] = "There was a problem updating Transaction, please try again";
                $this->helpers->activity_logs($_SESSION['id'], 6, "Editing Transaction", $feedback['message'] . " -# " . $this->input->post('transaction_no'), NULL, $this->input->post('transaction_no'));
            }
        }
        echo json_encode($feedback);
    }

    public function reverse_transaction()
    {
        $this->load->model('journal_transaction_model');
        $this->form_validation->set_rules("reverse_msg", "Reason", array("required"), array("required" => "%s must be entered"));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Transaction_model->reverse()) {
                    $journal_type_id = $this->input->post('journal_type_id');
                    $ref_no = $this->input->post('transaction_no');

                    $this->journal_transaction_model->reverse($_POST['id'], $ref_no, "(7,8,9,10,20)");

                    $feedback['success'] = true;
                    $feedback['message'] = "Transaction successfully cancled";
                    $this->helpers->activity_logs($_SESSION['id'],6,"reversing Transaction",$feedback['message']." -# ". $this->input->post('transaction_no'),NULL,$this->input->post('transaction_no'));
                } else {
                    // $feedback['message'] = "There was a problem reversing the transaction";
                    $feedback['message'] = "This transaction can't be reversed in this current fiscal year";
                 $this->helpers->activity_logs($_SESSION['id'],6,"reversing Transaction",$feedback['message']." -# ". $this->input->post('transaction_no'),NULL,$this->input->post('transaction_no'));
                }
            }
        }
        echo json_encode($feedback);
    }

    function change_status()
    {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
        //  if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        if (($response['success'] = $this->Transaction_model->delete($this->input->post('id'))) === true) {
            $response['message'] = "Transaction successfully deleted";
        }
        //  }
        echo json_encode($response);
    }


    private function deposit_journal_transaction($transaction_data, $member_id)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('savings_account_model');
        $this->load->model('DepositProduct_model');
        if ($this->input->post('transaction_date') != NULL && $this->input->post('transaction_date') != '') {
            $date = $this->input->post('transaction_date');
        } else {
            $date = date('d-m-Y');
        }
        if ($this->input->post('total_charges') != NULL && $this->input->post('total_charges') != '') {
            $total_charges = round($this->input->post('total_charges'));
            $deposited_amount = round($this->input->post('amount'), 2);
            $deposit_amount = $deposited_amount - $total_charges;
        } else {
            // $total_charges =0;
            //  foreach ($charges as $charge) {
            //      $total_charges =$charge['amount'];
            //  }
            //$deposit_amount = $transaction_data['amount']-$total_charges;
            $deposit_amount = $transaction_data['amount'];
        }
        if (is_numeric($this->input->post('account_no_id'))) {
            $account_no_id = $this->input->post('account_no_id');
        } else {
            $account_no_id = $transaction_data['account_no_id'];
        }
        $savings_account = $this->savings_account_model->get($account_no_id);

        if ($this->input->post('narrative') != NULL && $this->input->post('narrative') != '') {
            $narrative = $this->input->post('narrative');
        } else {
            $narrative = "Member deposit";
        }
        //then we prepare the journal transaction lines
        if (!empty($savings_account)) {
            $this->load->model('accounts_model');
            $this->load->model('transactionChannel_model');
            $this->load->model('journal_transaction_line_model');

            $data = [
                'transaction_date' => $date,
                'description' => $narrative,
                'ref_no' => $transaction_data['transaction_no'],
                'ref_id' => $transaction_data['transaction_id'],
                'status_id' => 1,
                'journal_type_id' => 7
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);

            $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
            $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);

            # Dont proceed if savings product details is null
            if (empty($savings_product_details)) {
                echo json_encode([
                    'message' => 'Savings Account is attached to a wrong Saving Product. Please Contact support'
                ]);
                die;
            }

            $debit_or_credit1 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id']);
            $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);

            //if deposit amount has been received
            if ($deposit_amount != null && !empty($deposit_amount) && $deposit_amount != '0') {
                $data[0] = [
                    $debit_or_credit1 => $deposit_amount,
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'member_id' => $member_id['member_id'],
                    'reference_key' => $member_id['account_no'],
                    'transaction_date' => $date,
                    'narrative' => "Deposit transaction made on " . $date,
                    'account_id' => $savings_product_details['savings_liability_account_id'],
                    'status_id' => 1
                ];
                $data[1] = [
                    $debit_or_credit2 => $deposit_amount,
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'member_id' => $member_id['member_id'],
                    'reference_key' => $member_id['account_no'],
                    'transaction_date' => $date,
                    'narrative' => "Deposit transaction made on " . $date,
                    'account_id' => $transaction_channel['linked_account_id'],
                    'status_id' => 1
                ];
                $this->journal_transaction_line_model->set($journal_transaction_id, $data);
            } //end of the if
        }
    }

    public function print_receipt($id = false, $client_type = false)
    {
        if (empty($this->session->userdata('id'))) {
            redirect("welcome", "refresh");
        }
        $this->data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $this->data['trans'] = $this->Transaction_model->get_transaction($id);
        $this->data['account'] = $this->Loan_guarantor_model->get_guarantor_savings('client_type=' . $client_type);

        $this->load->view('savings_account/deposits/savings_printout', $this->data);
        //activity log
        $this->helpers->activity_logs($_SESSION['id'], 6, "Printing", "Requested for printing", NULL, null);
    }
    public function savings_transaction_print_out()
    {
        $this->load->model('branch_model');
        $this->load->model('organisation_model');
        $this->load->helper('pdf_helper');
        $data['data'] = $this->Transaction_model->get_dTable_trans();
        $data['balance_end_date'] = $this->input->post('balance_end_date');
        $data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = "Savings Account Transactions";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $data['the_page_data'] = $this->load->view('savings_account/transaction/pdf_print_out', $data, TRUE);
        

        echo json_encode($data);
    }
    public function savings_transaction_print_out2($acc_id, $start_date, $end_date)
    {
        //$acc_id = $this->input->post('acc_id');
        //$acc_id = 750;
        $this->load->model('branch_model');
        $this->load->model('organisation_model');
        $this->load->helper('pdf_helper');
        $data['data'] = $this->Transaction_model->get_dTable_trans2($acc_id);
        $data['balance_end_date'] = $this->input->post('balance_end_date');
        $data['title'] = $_SESSION["org_name"];
        $data['selected_account'] = $this->Loan_guarantor_model->get_guarantor_savings2("(j.state_id = 5 OR j.state_id = 7 OR j.state_id = 12 OR j.state_id = 17 OR j.state_id = 18)", $acc_id);

        $data['sub_title'] = $data['selected_account']['member_name'] . " Account Statement";
        //$data['sub_title'] = "Savings Account Transactions";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 7;
        $data['org'] = $this->organisation_model->get($_SESSION['organisation_id']);
        $data['branch'] = $this->branch_model->get($_SESSION['branch_id']);
        $data['the_page_data'] = $this->load->view('savings_account/transaction/pdf_print_out2', $data, TRUE);
        $this->load->view('includes/pdf_template', $data);

        //echo json_encode($data);
    }
    private function withdraw_journal_transaction($transaction_data, $member_id)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('savings_account_model');
        $this->load->model('DepositProduct_model');
        if ($this->input->post('transaction_date') != NULL && $this->input->post('transaction_date') != '') {
            $date = $this->input->post('transaction_date');
        } else {
            $date = date('d-m-Y');
        }
        $savings_account = $this->savings_account_model->get($this->input->post('account_no_id'));

        $withdraw_amount = round($this->input->post('amount'), 2);
        //then we prepare the journal transaction lines
        if (!empty($savings_account)) {
            $this->load->model('accounts_model');
            $this->load->model('transactionChannel_model');
            $this->load->model('journal_transaction_line_model');

            $data = [
                'transaction_date' => $date,
                'description' => $this->input->post('narrative'),
                'ref_no' => $transaction_data['transaction_no'],
                'ref_id' => $transaction_data['transaction_id'],
                'status_id' => 1,
                'journal_type_id' => 8
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);

            $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
            $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);

            $debit_or_credit1 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);
            $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id'], true);

            //if deposit amount has been received
            if ($withdraw_amount != null && !empty($withdraw_amount) && $withdraw_amount != '0') {
                $data[0] = [
                    $debit_or_credit1 => $withdraw_amount,
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'member_id' => $member_id['member_id'],
                    'reference_key' => $member_id['account_no'],
                    'transaction_date' => $date,
                    'narrative' => "Withdraw transaction made on " . $date,
                    'account_id' => $savings_product_details['savings_liability_account_id'],
                    'status_id' => 1
                ];
                $data[1] = [
                    $debit_or_credit2 => $withdraw_amount,
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'member_id' => $member_id['member_id'],
                    'reference_key' => $member_id['account_no'],
                    'transaction_date' => $date,
                    'narrative' => "Withdraw transaction made on " . $date,
                    'account_id' => $transaction_channel['linked_account_id'],
                    'status_id' => 1
                ];
                $this->journal_transaction_line_model->set($journal_transaction_id, $data);
            } //end of the if
        }
    }

    private function de_charges_journal_transaction($transaction_data, $charges_data = FALSE, $member_id)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('Savings_product_fee_model');

        if ($this->input->post('transaction_date') != NULL && $this->input->post('transaction_date') != '') {
            $transaction_date = $this->input->post('transaction_date');
        } else {
            $transaction_date = date('d-m-Y');
        }
        if (empty($charges_data)) {
            $charges = $this->input->post('charges');
        } else {
            $charges = $charges_data;
        }
        if ($this->input->post('narrative') != NULL && $this->input->post('narrative') != '') {
            $narrative = $this->input->post('narrative');
        } else {
            $narrative = "Charge on deposit";
        }

        //then we prepare the journal transaction lines
        if (!empty($charges)) {
            $this->load->model('accounts_model');
            $this->load->model('transactionChannel_model');
            $this->load->model('journal_transaction_line_model');

            $data = [
                'transaction_date' => $transaction_date,
                'description' => $narrative,
                'ref_no' => $transaction_data['transaction_no'],
                'ref_id' => $transaction_data['transaction_id'],
                'status_id' => 1,
                'journal_type_id' => 10
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);

            $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
            $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);
            //if charges have been received

            foreach ($charges as $key => $value) {
                $savings_product_fee_details = $this->Savings_product_fee_model->get_accounts($value['charge_id']);

                $debit_or_credit1 = $this->accounts_model->get_normal_side($savings_product_fee_details['savings_fees_income_account_id']);
                $data[0] = [
                    $debit_or_credit1 => $value['charge_amount'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'member_id' => $member_id['member_id'],
                    'reference_key' => $member_id['account_no'],
                    'transaction_date' => $transaction_date,
                    'narrative' => "Charge on deposit made on " . $transaction_date,
                    'account_id' => $savings_product_fee_details['savings_fees_income_account_id'],
                    'status_id' => 1
                ];
                $data[1] = [
                    $debit_or_credit2 => $value['charge_amount'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'member_id' => $member_id['member_id'],
                    'reference_key' => $member_id['account_no'],
                    'transaction_date' => $transaction_date,
                    'narrative' => "Charge on deposit made on " . $transaction_date,
                    'account_id' => $transaction_channel['linked_account_id'],
                    'status_id' => 1
                ];
                $this->journal_transaction_line_model->set($journal_transaction_id, $data);
            } //end of foreach
        }
    }

    private function we_charges_journal_transaction($transaction_data, $member_id)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('Savings_product_fee_model');
        $this->load->model('savings_account_model');
        $this->load->model('DepositProduct_model');

        if ($this->input->post('transaction_date') != NULL && $this->input->post('transaction_date') != '') {
            $transaction_date = $this->input->post('transaction_date');
        } else {
            $transaction_date = date('d-m-Y');
        }
        $charges = $this->input->post('charges');
        $savings_account = $this->savings_account_model->get($this->input->post('account_no_id'));
        if ($this->input->post('transaction_type_id') == 3) {
            $journal_type_id = 20;
        } else {
            $journal_type_id = 9;
        }
        //then we prepare the journal transaction lines
        if (!empty($charges)) {
            $this->load->model('accounts_model');
            $this->load->model('transactionChannel_model');
            $this->load->model('journal_transaction_line_model');

            $data = [
                'transaction_date' => $transaction_date,
                'description' => $this->input->post('narrative'),
                'ref_no' => $transaction_data['transaction_no'],
                'ref_id' => $transaction_data['transaction_id'],
                'status_id' => 1,
                'journal_type_id' => $journal_type_id
            ];
            //then we post this to the journal transaction
            $journal_transaction_id = $this->journal_transaction_model->set($data);
            unset($data);

            $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);

            $debit_or_credit2 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);

            //if charges have been received
            foreach ($charges as $key => $value) {
                $savings_product_fee_details = $this->Savings_product_fee_model->get_accounts($value['charge_id']);

                $debit_or_credit1 = $this->accounts_model->get_normal_side($savings_product_fee_details['savings_fees_income_account_id']);
                $data[0] = [
                    $debit_or_credit1 => $value['charge_amount'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'member_id' => $member_id['member_id'],
                    'reference_key' => $member_id['account_no'],
                    'transaction_date' => $transaction_date,
                    'narrative' => "Charge on withdraw transaction made on " . $transaction_date,
                    'account_id' => $savings_product_fee_details['savings_fees_income_account_id'],
                    'status_id' => 1
                ];
                $data[1] = [
                    $debit_or_credit2 => $value['charge_amount'],
                    'reference_no' => $transaction_data['transaction_no'],
                    'reference_id' => $transaction_data['transaction_id'],
                    'member_id' => $member_id['member_id'],
                    'reference_key' => $member_id['account_no'],
                    'transaction_date' => $transaction_date,
                    'narrative' => "Charge on withdraw transaction made on " . $transaction_date,
                    'account_id' => $savings_product_details['savings_liability_account_id'],
                    'status_id' => 1
                ];
                $this->journal_transaction_line_model->set($journal_transaction_id, $data);
            } //end of foreach
        }
    }

    public function import()
    {
        $this->load->model("Savings_account_model");
        $this->load->model("Savings_product_fee_model");
        $feedback['success'] = false;
        $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
            $arr_file = explode('.', $_FILES['file']['name']);
            $extension = end($arr_file);
            if ('csv' == $extension) {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
            $spreadsheet = $reader->load($_FILES['file']['tmp_name']);

            $trandate = explode('-', $this->input->post('transaction_date'), 3);
            $transaction_date = count($trandate) === 3 ? ($trandate[2] . "-" . $trandate[1] . "-" . $trandate[0] . " " . date("H:i:s")) : null;
            $failed = $passed = 0;
            $failed_data = array();
            foreach ($spreadsheet->getWorksheetIterator() as $worksheet_check) {
                $gethighestRow = $worksheet_check->getHighestRow();
                $getSheetByName = $worksheet_check->getTitle();
                $gethighestColumn = $worksheet_check->getHighestColumn();
                for ($row = 2; $row <= $gethighestRow; $row++) {
                    $template_check = $worksheet_check->getCellByColumnAndRow(1, 1)->getValue();
                    $sav_account_id = $worksheet_check->getCellByColumnAndRow(1, $row)->getValue();
                    $client_no = $worksheet_check->getCellByColumnAndRow(2, $row)->getValue();
                    $name_check = $worksheet_check->getCellByColumnAndRow(4, $row)->getValue();
                    $amount_check = $worksheet_check->getCellByColumnAndRow(5, $row)->getValue();
                    //$savings_account = array('' => "");
                    // print_r($client_no);die();
                    // if(!empty($client_no)&&($amount_check>0)){
                    // $savings_account = $this->member_model->get_member_info($client_no);
                    // }
                    if (isset($sav_account_id) && ($amount_check != 0)) {
                        if (($template_check != "Account ID") || empty($sav_account_id) || (!is_numeric($amount_check) && !empty($amount_check))) {

                            if (empty($sav_account_id) && ($amount_check <= 0)) {
                                $message = "Check both (Account ID and Amount). Row Number ( " . $row . " )";
                            } else if ($template_check != "Account ID") {
                                $message = "Wrong Template Submitted !";
                            } else if (empty($sav_account_id)) {
                                $message = "Check Account ID . Row Number ( " . $row . " )";
                            } else if (!is_numeric($amount_check)) {
                                $message = "Amount is not a number . Row Number ( " . $row . " )";
                                // }else if(empty($savings_account)) {
                                //     $message ="Client No does not have an Account Number or Client no does not  EXIST . Row Number ( ".$row." )";
                                // } 
                            } else {
                                $message = "Something is wrong with this record. Row Number ( " . $row . " )";
                            }
                            $failed_data[] = array(
                                'row_id'         =>  $row,
                                'account_no_id'  =>  $sav_account_id,
                                'client_name'    =>  $name_check,
                                'amount'         =>  $amount_check,
                                'message'        =>  $message
                            );
                            //$failed_data = array_merge($failed_data, $data1);
                            //$this->Transaction_model->bulk_error_log($failed_data);
                            $failed++;
                        }
                    }
                }
            }

            if (empty($failed_data)) {
                $this->db->trans_begin();
                foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow();
                    $highestColumn = $worksheet->getHighestColumn();
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $savings_account_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        $client_no = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                        $name = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                        $amount = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                        //$savings_account = $this->member_model->get_member_info($client_no);
                        if (!empty($amount) && ($amount > 0)) {
                            $member_id = $this->savings_account_model->get2($savings_account_id);

                            $data = array(
                                'transaction_no' => date('ymdsu') . mt_rand(000, 999),
                                'account_no_id' => $savings_account_id,
                                'credit' => $amount,
                                'transaction_type_id' => 2,
                                'transaction_date' => $transaction_date . " " . date('H:i:s.u'),
                                'narrative' => $this->input->post('narrative') . "[ " . $name . " ]" . " as of " . $this->input->post('transaction_date'),
                                'date_created' => time(),
                                'created_by' => $_SESSION['id'],
                                'status_id' => 1,
                                'payment_id' => $this->input->post('payment_id')
                            );

                            //$charges = $this->Savings_product_fee_model->get(array('s.saving_product_id' => $savings_account['deposit_Product_id'], 'sf.chargetrigger_id' => '4', 'sf.status_id' => '1', 's.status_id' => '1'));
                            $transaction_data = $this->Transaction_model->bulk_set($data);
                            if (is_array($transaction_data)) {
                                $this->deposit_journal_transaction($transaction_data, $member_id);
                                // if ($charges !== NULL && $charges != '') {
                                //     $this->de_charges_journal_transaction($transaction_data, $charges);
                                // }
                            }
                            $passed++;
                        }
                    }
                }
                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $response = "Records Imported successfully";
                    $feedback['message'] = "( " . $passed . " ) " . $response . " ( " . $failed . " ) Failed , Check error log table";
                    $feedback['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings("(j.state_id = 5 OR j.state_id = 7)");
                    $feedback['success'] = true;
                    //activity log 

                    $this->helpers->activity_logs($_SESSION['id'], 6, "Bulk deposit", $feedback['message'] . " # " . $data['account_no_id'], NULL, $data['account_no_id']);
                } else {
                    $this->db->trans_rollback();

                    $response = "Bulk deposrit Failed, Please try again";
                    $feedback['message'] = "( " . $passed . " ) " . $response . " ( " . $failed . " ) Failed , Check error log table";
                    $feedback['success'] = true;
                    $this->helpers->activity_logs($_SESSION['id'], 6, "Bulk deposit Failure", $feedback['message'] . " # " . $data['account_no_id'], NULL, $data['account_no_id']);
                }
            } else {
                $feedback['message'] = "( " . $failed . " ) records with errors , Check the error log. Fix them and Upload again, Remember to close the form to upload again";
                $feedback['success'] = false;
                $feedback['failed'] = $failed_data;

                $this->helpers->activity_logs($_SESSION['id'], 6, "Bulk deposit", $feedback['message'] . " # " . NULL, NULL, NULL);
            }
        }
        echo json_encode($feedback);
    }
    //=========================for KCCA =========================
    //  public function import()
    // {
    //     $this->load->model("Savings_account_model");
    //     $this->load->model("Savings_product_fee_model");
    //     if(isset($_FILES["file"]["name"]))
    //       {
    //         $path = $_FILES["file"]["tmp_name"];
    //         $object = PHPExcel_IOFactory::load($path);

    //         $trandate = explode('-', $this->input->post('transaction_date'), 3);
    //         $transaction_date = count($trandate) === 3 ? ($trandate[2] . "-" . $trandate[1] . "-" . $trandate[0]." ".date("H:i:s")) : null;
    //         $failed =$passed =0;
    //         $failed_data = array();
    //         foreach($object->getWorksheetIterator() as $worksheet_check)
    //         {
    //             $gethighestRow = $worksheet_check->getHighestRow();
    //             $getSheetByName = $worksheet_check->getTitle();
    //             $gethighestColumn = $worksheet_check->getHighestColumn();
    //             for($row=3; $row<=$gethighestRow; $row++)
    //               {
    //                 $sav_account_id = $worksheet_check->getCellByColumnAndRow(0, $row)->getValue();
    //                 $name_check = $worksheet_check->getCellByColumnAndRow(3, $row)->getValue();
    //                 $amount_check = $worksheet_check->getCellByColumnAndRow(4, $row)->getValue();
    //                 $savings_account = array('' => "");
    //                 if(!empty($sav_account_id)&&($amount_check>0)){
    //                 $savings_account = $this->member_model->get_member_info($sav_account_id);
    //                 }
    //                 if(empty($sav_account_id)|| ($amount_check<=0) ||(empty($savings_account))){ 
    //                     if(empty($sav_account_id) && ($amount_check<=0)){
    //                         $message ="Check both (Client No and Amount). Row Number ( ".$row." )";
    //                     } else if(empty($sav_account_id)){
    //                         $message ="Check Client No . Row Number ( ".$row." )";
    //                     } else if($amount_check<=0) {
    //                         $message ="Amount is less or equal to zero . Row Number ( ".$row." )";
    //                     }else if(empty($savings_account)) {
    //                         $message ="Client No does not have an Account Number or Client no does not  EXIST . Row Number ( ".$row." )";
    //                     } else {
    //                         $message ="Something is wrong with this record. Row Number ( ".$row." )";
    //                     }
    //                   $failed_data[] = array(
    //                     'row_id'         =>  $row,
    //                     'account_no_id'  =>  $sav_account_id,
    //                     'client_name'    =>  $name_check,
    //                     'amount'         =>  $amount_check,
    //                     'message'        =>  $message
    //                   );

    //                  //$this->Transaction_model->bulk_error_log($failed_data);
    //                  $failed++;
    //                 } 
    //             }
    //         }

    //     if(empty($failed_data)){
    //         foreach($object->getWorksheetIterator() as $worksheet)
    //         {
    //             $highestRow = $worksheet->getHighestRow();
    //             $highestColumn = $worksheet->getHighestColumn();
    //             for($row=3; $row<=$highestRow; $row++)
    //               {
    //                 $client_no = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
    //                 $name = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
    //                 $amount = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    //                 $savings_account = $this->member_model->get_member_info($client_no);
    //                 $data = array(
    //                     'transaction_no'      =>  date('yws').mt_rand(000, 999),
    //                     'account_no_id'           =>  $savings_account['id'],
    //                     'credit'              =>  $amount,
    //                     'transaction_type_id'        => 2,
    //                     'transaction_date'        =>  $transaction_date,
    //                     'narrative'        =>  $this->input->post('narrative')." on ".$this->input->post('transaction_date'),
    //                     'date_created'        => time(),
    //                     'created_by'        =>  $_SESSION['id'],
    //                     'status_id'        => 1,
    //                     'payment_id'      =>  $this->input->post('payment_id')
    //                 );

    //                 $charges= $this->Savings_product_fee_model->get(array('s.saving_product_id' => $savings_account['deposit_Product_id'], 'sf.chargetrigger_id' => '4', 'sf.status_id' => '1', 's.status_id' => '1'));

    //                  $transaction_data=$this->Transaction_model->bulk_set($data,$charges);
    //                  if (is_array($transaction_data)) {
    //                     $this->deposit_journal_transaction($transaction_data,$charges);
    //                         if ($charges !==NULL && $charges !='') {
    //                             $this->de_charges_journal_transaction($transaction_data,$charges);
    //                         }
    //                  }
    //                 $passed++;
    //             }
    //         }
    //         $response ="Records Imported successfully";
    //         $feedback['message'] = "( ".$passed." ) ".$response." ( ".$failed." ) Failed , Check error log table";
    //         $feedback['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings("(j.state_id = 5 OR j.state_id = 7)");
    //         $feedback['success'] = true;
    //     } else {
    //         $feedback['message'] = "( ".$failed." ) records with errors , Check the error log. Fix them and Upload again";
    //         $feedback['success'] = false;
    //         $feedback['failed'] =$failed_data;
    //     }

    //     } 
    //     echo json_encode($feedback);
    // }

    public function export_excel()
    {

        $this->load->model("Savings_account_model");
        // $dataArray = $this->Loan_guarantor_model->get_guarantor_savings("(j.state_id = 5 OR j.state_id = 7)");
        $dataArray = $this->Savings_account_model->get_excel_data();

        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Account ID');
        $sheet->setCellValue('B1', 'Member No');
        $sheet->setCellValue('C1', 'Account Number');
        $sheet->setCellValue('D1', 'Account Name');
        $sheet->setCellValue('E1', 'Amount');

        $sheet->getStyle("A1:E1")->getFont()->setBold(true);

        $rowCount   =   2;
        foreach ($dataArray as $data) {

            $sheet->setCellValue('A' . $rowCount, $data['id']);
            $sheet->setCellValue('B' . $rowCount, $data['client_no']);
            $sheet->setCellValue('C' . $rowCount, $data['account_no']);
            $sheet->setCellValue('D' . $rowCount, mb_strtoupper($data['member_name'], 'UTF-8'));
            $rowCount++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Bulk Savings Deposit Template';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

           $this->helpers->activity_logs($_SESSION['id'],6,"Exported savings account Template"," # ".$filename,NULL,$filename);
    }
}
