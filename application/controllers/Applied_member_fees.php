<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Applied_member_fees extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model("applied_member_fees_model");
        $this->load->model("Loan_guarantor_model");
        $this->load->model("Transaction_model");
        $this->load->model("Automated_fees_model");
        $this->load->library("helpers");
        $this->data['privilege_list'] = $this->helpers->user_privileges($module_id = 2, $_SESSION['staff_id']);

        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['privileges'] = array_column($this->data['privilege_list'], "privilege_code");
        }
    }

    public function jsonList()
    {
        $data['data'] = $this->applied_member_fees_model->get();
        echo json_encode($data);
    }


    public function create()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('transaction_date', 'Payment date', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
        $this->form_validation->set_rules('payment_id', 'payment mode', 'required|numeric');
        $feedback['success'] = false;
        $membership_data['member_id'] = $this->input->post('member_id');

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {
            $amount = $this->input->post('amount');
            if ($this->input->post('payment_id') == 5) {
                $savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $this->input->post('account_no_id'));
                $current_balance = $savings_data['cash_bal'];
                if ($current_balance >= $amount) {
                    $return_ids = $this->applied_member_fees_model->update($this->input->post('id'));
                    $this->Automated_fees_model->update_schedule($membership_data);
                    if ($return_ids) {
                        $transaction_data = $this->Transaction_model->set($this->input->post('account_no_id'), 4, $amount, $this->input->post('transaction_no'), "[Membership Fees Payment]");
                        $this->do_journal_transaction($this->input->post('transaction_no'), $transaction_data);
                        $feedback['success'] = true;
                        $feedback['message'] = "Member fee(s) has  successfully been received";
                        //$this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'], $member_id, $member_id);
                    } else {
                        $feedback['message'] = "There was a problem applying the member fee, please contact IT support";

                        //$this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'], $member_id, $member_id);
                    }
                } else {
                    $feedback['message'] = "Insufficient balance to complete the payment";

                    //$this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'], $member_id, $member_id);
                }
            } else {
                $return_ids = $this->applied_member_fees_model->update($this->input->post('id'));
                $this->Automated_fees_model->update_schedule($membership_data);
                if ($return_ids) {
                    $this->do_journal_transaction($this->input->post('transaction_no'));
                    $feedback['success'] = true;
                    $feedback['message'] = "Member fee(s) has  successfully been received";
                    //$this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'], $member_id, $member_id);
                } else {
                    $feedback['message'] = "There was a problem applying the member fees";
                    //$this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'], $member_id, $member_id);
                }
            }
            //adding a new item
        }
        echo json_encode($feedback);
    }

    private function do_journal_transaction($ref_id, $transaction_data = false)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('member_fees_model');

        $this->load->model('accounts_model');
        $this->load->model('subscription_plan_model');
        $this->load->model('DepositProduct_model');
        $this->load->model('savings_account_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('journal_transaction_line_model');

        if (!empty($transaction_data['transaction_no'])) {
            $ref_no = $transaction_data['transaction_no'];
        } else {
            $ref_no = NULL;
        }
        $data = [
            'transaction_date' => $this->input->post('transaction_date'),
            'description' => $this->input->post('narrative'),
            'ref_no' => $ref_id,
            'ref_id' => $ref_no,
            'status_id' => 1,
            'journal_type_id' => 12
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);

        unset($data);

        //then we prepare the journal transaction lines

        if ($this->input->post('payment_id') == 5) {
            $savings_account = $this->savings_account_model->get($this->input->post('account_no_id'));
            $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);

            $linked_account_id = $savings_product_details['savings_liability_account_id'];
            $debit_or_credit2 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);
        } else {
            $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
            $linked_account_id = $transaction_channel['linked_account_id'];

            $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id'], false);
        }
        $memberFees_details = $this->member_fees_model->get($this->input->post('member_fee_id'));
        $debit_or_credit1 = $this->accounts_model->get_normal_side($memberFees_details['receivable_account_id'], true);
        $data = [
            [
                $debit_or_credit1 => $this->input->post('amount'),
                'transaction_date' => $this->input->post('transaction_date'),
                'reference_no' => $ref_id,
                'reference_id' => $ref_no,
                'narrative' => $memberFees_details['feename'] . " " . $this->input->post('transaction_date') . " " . $this->input->post('narrative'),
                'account_id' => $memberFees_details['receivable_account_id'],
                'status_id' => 1
            ],
            [
                $debit_or_credit2 => $this->input->post('amount'),
                'transaction_date' => $this->input->post('transaction_date'),
                'reference_no' => $ref_id,
                'reference_id' => $ref_no,
                'narrative' => $memberFees_details['feename'] . " " . $this->input->post('transaction_date') . " " . $this->input->post('narrative'),
                'account_id' => $linked_account_id,
                'status_id' => 1
            ]
        ];
        $this->journal_transaction_line_model->set($journal_transaction_id, $data);
    }

    private function generate_transaction_no()
    {
        $this->load->model('organisation_format_model');
        $this->data['transaction_no_format'] = $this->organisation_format_model->get_transaction_format();
        $org_id = $this->data['transaction_no_format']['id'];
        $counter =  $this->data['transaction_no_format']['counter_applied_member_fees'];
        $letter =  $this->data['transaction_no_format']['letter_applied_member_fees'];
        $initial =  $this->data['transaction_no_format']['org_initial'];
        if ($counter == 999999999999) {
            $letter++;
            $counter = 0;
        }
        $transaction = 'MF' . sprintf("%012d", $counter + 1) . $letter;
        $this->db->where('id', $org_id);
        $this->db->update('fms_organisation', ["counter_applied_member_fees" => $counter + 1, "letter_applied_member_fees" => $letter]);
        return $transaction;
    }


    public function create2()
    {
        $memberFees = $this->input->post('memberFees1');
        $membership_data['member_id'] = $this->input->post('member_id');
        $amount = 0;
        foreach ($memberFees as $feetotal) {
            $amount = $feetotal['amount'] + $amount;
        }
        if (empty($memberFees)) {
            $feedback['success'] = false;
            $feedback['message'] = "All fields are required";
        } else {
            if ($this->input->post('fee_paid') == 1) {
                if ($this->input->post('payment_id') == 5) {
                    $savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $this->input->post('account_no_id'));
                    $current_balance = $savings_data['cash_bal'];
                    if ($current_balance >= $amount) {
                        foreach ($memberFees as $key => $fee) { //it is a new entry, so we insert afresh
                            $transaction_no = $this->generate_transaction_no();
                            $value['date_created'] = time();
                            $value['payment_date'] = $this->helpers->yr_transformer($this->input->post('transaction_date'));
                            $value['amount'] = $fee['amount'];
                            $value['member_fee_id'] = $fee['member_fee_id'];

                            $value['payment_id'] = $this->input->post('payment_id');
                            $value['member_id'] = $this->input->post('member_id');
                            $value['fee_paid'] = $this->input->post('fee_paid');
                            $value['narrative'] = $this->input->post('narrative');
                            $value['transaction_no'] = $transaction_no;
                            $value['created_by'] = $value['modified_by'] = $_SESSION['id'];

                            $return_ids = $this->applied_member_fees_model->set($value);
                            $this->Automated_fees_model->update_schedule($membership_data);
                            if (!empty($return_ids)) {
                                $transaction_data = $this->Transaction_model->set($this->input->post('account_no_id'), 4, $fee['amount'], $return_ids['transaction_no'], "[Membership Fees Payment]");

                                $this->do_attached_journal_transaction($return_ids, $fee['amount'], $fee['member_fee_id']);
                            }
                        }
                        if (!empty($transaction_data)) {
                            $feedback['success'] = true;
                            $feedback['message'] = "Member fee(s) has  successfully been received";
                            //$this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'], $member_id, $member_id);
                        } else {
                            $feedback['message'] = "There was a problem applying the member fee, please contact IT support";
                            //$this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'], $member_id, $member_id);
                        }
                    } else {
                        $feedback['message'] = "Insufficient balance to complete the payment";

                        //$this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'], $member_id, $member_id);
                    }
                } else {
                    foreach ($memberFees as $key => $fee) {
                        $transaction_no = $this->generate_transaction_no();
                        $value['date_created'] = time();
                        $value['payment_date'] = $this->helpers->yr_transformer($this->input->post('transaction_date'));
                        $value['payment_id'] = $this->input->post('payment_id');
                        $value['amount'] = $fee['amount'];
                        $value['member_fee_id'] = $fee['member_fee_id'];

                        $value['member_id'] = $this->input->post('member_id');
                        $value['fee_paid'] = $this->input->post('fee_paid');
                        $value['narrative'] = $this->input->post('narrative');
                        $value['transaction_no'] = $transaction_no;
                        $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
                        $return_ids = $this->applied_member_fees_model->set($value);
                        $this->Automated_fees_model->update_schedule($membership_data);
                        $this->do_attached_journal_transaction($return_ids, $fee['amount'], $fee['member_fee_id']);
                    }

                    if (!empty($return_ids)) {
                        $feedback['success'] = true;
                        $feedback['message'] = "Member fee(s) has  successfully been received";
                        $this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'] . "#" . $value['member_id'],  $value['transaction_no'],  $value['transaction_no']);
                    } else {
                        $feedback['message'] = "There was a problem applying the member fees";

                        $this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'] . "#" . $value['member_id'], $value['member_id'], $transaction_no);
                    }
                }
            } else {
                foreach ($memberFees as $key => $fee) {
                    $transaction_no = $this->generate_transaction_no();
                    $value['date_created'] = time();
                    $value['payment_date'] = $this->helpers->yr_transformer($this->input->post('transaction_date'));
                    $value['amount'] = $fee['amount'];
                    $value['member_fee_id'] = $fee['member_fee_id'];

                    $value['payment_id'] = $this->input->post('payment_id');
                    $value['member_id'] = $this->input->post('member_id');
                    $value['fee_paid'] = $this->input->post('fee_paid');
                    $value['narrative'] = $this->input->post('narrative');
                    $value['transaction_no'] = $transaction_no;
                    $value['created_by'] = $value['modified_by'] = $_SESSION['id'];

                    $return_ids = $this->applied_member_fees_model->set($value);
                    $this->Automated_fees_model->update_schedule($membership_data);
                    if (!empty($return_ids)) {
                        $this->do_attached_journal_transaction($return_ids, $fee['amount'], $fee['member_fee_id']);
                    }
                }
                if (!empty($return_ids)) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Member fee(s) has  successfully been received";

                    $this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'] . "#" . $value['member_id'],  $value['transaction_no'],  $value['transaction_no']);
                } else {
                    $feedback['message'] = "There was a problem applying the member fees";

                    $this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'] . "#" . $value['member_id'],  $value['transaction_no'],  $value['transaction_no']);
                }
            }
        }
        echo json_encode($feedback);
    }



    ///funtion for making manual payments
    public function pay_manual()
    {
        $memberFees = $this->input->post('memberFees1');
        $amount = 0;
        foreach ($memberFees as $feetotal) {
            $amount = $feetotal['amount'] + $amount;
        }
        if (empty($memberFees)) {
            $feedback['success'] = false;
            $feedback['message'] = "All fields are required";
        } else {
            if ($this->input->post('fee_paid') == 1) {
                if ($this->input->post('payment_id') == 5) {
                    $savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $this->input->post('account_no_id'));
                    $current_balance = $savings_data['cash_bal'];
                    if ($current_balance >= $amount) {
                        foreach ($memberFees as $key => $fee) { //it is a new entry, so we insert afresh
                            $transaction_no = $this->generate_transaction_no();
                            $value['date_created'] = time();
                            $value['payment_date'] = $this->helpers->yr_transformer($this->input->post('transaction_date'));
                            $value['amount'] = $fee['amount'];
                            $value['member_fee_id'] = $fee['member_fee_id'];

                            $value['payment_id'] = $this->input->post('payment_id');
                            $value['member_id'] = $this->input->post('member_id');
                            $value['fee_paid'] = $this->input->post('fee_paid');
                            $value['narrative'] = $this->input->post('narrative');
                            $value['transaction_no'] = $transaction_no;
                            $value['created_by'] = $value['modified_by'] = $_SESSION['id'];

                            $return_ids = $this->applied_member_fees_model->set($value);
                            if (!empty($return_ids)) {
                                $transaction_data = $this->Transaction_model->set($this->input->post('account_no_id'), 4, $fee['amount'], $return_ids['transaction_no'], "[Membership Fees Payment]");

                                $this->do_attached_journal_transaction($return_ids, $fee['amount'], $fee['member_fee_id']);
                            }
                        }
                        if (!empty($transaction_data)) {
                            $feedback['success'] = true;
                            $feedback['message'] = "Member fee(s) has  successfully been received";
                            //$this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'], $member_id, $member_id);
                        } else {
                            $feedback['message'] = "There was a problem applying the member fee, please contact IT support";
                            //$this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'], $member_id, $member_id);
                        }
                    } else {
                        $feedback['message'] = "Insufficient balance to complete the payment";

                        //$this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'], $member_id, $member_id);
                    }
                } else {
                    foreach ($memberFees as $key => $fee) {
                        $transaction_no = $this->generate_transaction_no();
                        $value['date_created'] = time();
                        $value['payment_date'] = $this->helpers->yr_transformer($this->input->post('transaction_date'));
                        $value['payment_id'] = $this->input->post('payment_id');
                        $value['amount'] = $fee['amount'];
                        $value['member_fee_id'] = $fee['member_fee_id'];

                        $value['member_id'] = $this->input->post('member_id');
                        $value['fee_paid'] = $this->input->post('fee_paid');
                        $value['narrative'] = $this->input->post('narrative');
                        $value['transaction_no'] = $transaction_no;
                        $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
                        $return_ids = $this->applied_member_fees_model->set($value);
                        $this->do_attached_journal_transaction($return_ids, $fee['amount'], $fee['member_fee_id']);
                    }

                    if (!empty($return_ids)) {
                        $feedback['success'] = true;
                        $feedback['message'] = "Member fee(s) has  successfully been received";
                        $this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'] . "#" . $value['member_id'],  $value['transaction_no'],  $value['transaction_no']);
                    } else {
                        $feedback['message'] = "There was a problem applying the member fees";

                        $this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'] . "#" . $value['member_id'], $value['member_id'], $transaction_no);
                    }
                }
            } else {
                foreach ($memberFees as $key => $fee) {
                    $transaction_no = $this->generate_transaction_no();
                    $value['date_created'] = time();
                    $value['payment_date'] = $this->helpers->yr_transformer($this->input->post('transaction_date'));
                    $value['amount'] = $fee['amount'];
                    $value['member_fee_id'] = $fee['member_fee_id'];

                    $value['payment_id'] = $this->input->post('payment_id');
                    $value['member_id'] = $this->input->post('member_id');
                    $value['fee_paid'] = $this->input->post('fee_paid');
                    $value['narrative'] = $this->input->post('narrative');
                    $value['transaction_no'] = $transaction_no;
                    $value['created_by'] = $value['modified_by'] = $_SESSION['id'];

                    $return_ids = $this->applied_member_fees_model->set($value);
                    if (!empty($return_ids)) {
                        $this->do_attached_journal_transaction($return_ids, $fee['amount'], $fee['member_fee_id']);
                    }
                }
                if (!empty($return_ids)) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Member fee(s) has  successfully been received";

                    $this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'] . "#" . $value['member_id'],  $value['transaction_no'],  $value['transaction_no']);
                } else {
                    $feedback['message'] = "There was a problem applying the member fees";

                    $this->helpers->activity_logs($_SESSION['id'], 8, "Creating member fees", $feedback['message'] . "#" . $value['member_id'],  $value['transaction_no'],  $value['transaction_no']);
                }
            }
        }
        echo json_encode($feedback);
    }

    private function do_attached_journal_transaction($transaction_data, $amount, $member_fee_id)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('member_fees_model');

        $this->load->model('accounts_model');
        $this->load->model('subscription_plan_model');
        $this->load->model('DepositProduct_model');
        $this->load->model('savings_account_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('journal_transaction_line_model');


        $ref_no = $transaction_data['transaction_no'];
        $ref_id = $transaction_data['transaction_id'];
        $data = [
            'transaction_date' => $this->input->post('transaction_date'),
            'description' => $this->input->post('narrative'),
            'ref_no' => $ref_no,
            'ref_id' => $ref_id,
            'status_id' => 1,
            'journal_type_id' => 12
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        $memberFees_details = $this->member_fees_model->get($member_fee_id);
        //then we prepare the journal transaction lines
        if ($this->input->post('fee_paid') == 1) {

            if ($this->input->post('payment_id') == 5) {
                $savings_account = $this->savings_account_model->get($this->input->post('account_no_id'));
                $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);

                $linked_account_id = $savings_product_details['savings_liability_account_id'];
                $debit_or_credit2 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);
            } else {
                $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
                $linked_account_id = $transaction_channel['linked_account_id'];

                $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id'], false);
            }
        } else {
            $linked_account_id = $memberFees_details['receivable_account_id'];
            $debit_or_credit2 = $this->accounts_model->get_normal_side($memberFees_details['receivable_account_id'], false);
        }
        $debit_or_credit1 = $this->accounts_model->get_normal_side($memberFees_details['income_account_id'], false);
        $data = [
            [
                $debit_or_credit1 => $amount,
                'transaction_date' => $this->input->post('transaction_date'),
                'reference_no' => $ref_no,
                'reference_id' => $ref_id,
                'narrative' => $memberFees_details['feename'] . " " . $this->input->post('transaction_date') . " " . $this->input->post('narrative'),
                'account_id' => $memberFees_details['income_account_id'],
                'status_id' => 1
            ],
            [
                $debit_or_credit2 => $amount,
                'transaction_date' => $this->input->post('transaction_date'),
                'reference_no' => $ref_no,
                'reference_id' => $ref_id,
                'narrative' => $memberFees_details['feename'] . " " . $this->input->post('transaction_date') . " " . $this->input->post('narrative'),
                'account_id' => $linked_account_id,
                'status_id' => 1
            ]
        ];
        $this->journal_transaction_line_model->set($journal_transaction_id, $data);
    }


    public function change_status()
    {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Applied member fee data could not be $msg activated, contact IT support.";
        $this->helpers->activity_logs($_SESSION['id'], 8, "Changing member fees status", $response['message'],  "",  "");

        $response['success'] = FALSE;
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        if ($this->applied_member_fees_model->change_status_by_id($this->input->post('id'))) {
            $response['message'] = "Applied member fee data has successfully been $msg Deactivated.";
            $response['success'] = TRUE;

            $this->helpers->activity_logs($_SESSION['id'], 8, "Changing member fees status", $response['message'],  "",  "");
            echo json_encode($response);
        }
        //}
    }

    public function delete()
    {
        $response['success'] = FALSE;
        $applied_fee = $this->applied_member_fees_model->get($this->input->post("id"));
       if ($this->applied_member_fees_model->delete2($applied_fee['transaction_no'])) {
            $this->load->model('Member_fees_model');
            $response['success'] = TRUE;
            $response['message'] = "Fee successfully deleted.";
            $member_id = $this->input->post('member_id');
            $response['available_member_fees'] = $this->Member_fees_model->get("`fms_member_fees`.`id` NOT IN ( SELECT member_fee_id 
                FROM `fms_applied_member_fees` WHERE member_id = '" . $applied_fee['member_id'] . "' AND status_id=1)");
             
            $this->helpers->activity_logs($_SESSION['id'], 8, "Deleting Member Fees", $response['message'] . "#" . $member_id, null, null);
        }
        echo json_encode($response);
    }

    public  function pdf($member_id, $transaction_no = false)
    {
        $this->load->model('member_model');
        $this->load->helper('pdf_helper');
        $data['title'] = $_SESSION["org_name"];
        $data['sub_title'] = "Member fees";
        $data['font'] = 'helvetica';
        $data['fontSize'] = 8;
        $data['single_receipt_items'] = $this->applied_member_fees_model->get("transaction_no = '" . $transaction_no . "'");
        $data['member'] = $this->member_model->get_member($member_id);
        $data['receipt_item_sum'] = $this->applied_member_fees_model->get_sum("transaction_no = '" . $transaction_no . "'");
        $data['the_page_data'] = $this->load->view('user/member/member_fees/pdf', $data, TRUE);
        $this->load->view('includes/pdf_template', $data);
    }
}
