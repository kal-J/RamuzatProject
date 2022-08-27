<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Client_subscription extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model("client_subscription_model");
        $this->load->model("Transaction_model");
        $this->load->model("Loan_guarantor_model");
        $this->load->model("Member_fees_model");
    }

    public function jsonList()
    {
        $this->data['data'] = $this->client_subscription_model->get();
        echo json_encode($this->data);
    }
    public function jsonList2()
    {
        $this->data['data'] = $this->client_subscription_model->get3();
        echo json_encode($this->data);
    }
    public function get_max()
    {
        $id = $this->input->post('client_id');
        $this->data['data'] = $this->client_subscription_model->get_max();
        echo json_encode($this->data);
    }
    public function get_accounts()
    {
        $id = $this->input->post('id');
        $this->data['data'] = $this->Loan_guarantor_model->get_guarantor_savings("(ifnull( deposit ,0) ) - ( ifnull( withdraw ,0) + ifnull( transfer ,0)  +ifnull(charges, 0) + ifnull( amount_locked, 0) ) > 0 AND j.state_id = 7 AND a.client_type=1 AND member_id = $id");
        echo json_encode($this->data);
    }

    public function get_attached_fees()
    {
        $id = $this->input->post('id');
        $this->data['data'] = $this->Member_fees_model->get("fms_member_fees.`id` NOT IN ( SELECT member_fee_id 
            FROM `fms_membership_schedule` WHERE member_id = '" . $id . "' AND state=5)");
        echo json_encode($this->data);
    }


    public function create()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('transaction_date', 'Payment date', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
        $feedback['success'] = false;

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors('<li>', '</li>');
        } else {
            if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) { //editing exsting item

                if ($this->input->post('payment_id') == 5) {
                    $savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $this->input->post('account_no_id'));
                    $current_balance = $savings_data['cash_bal'];
                    if ($current_balance >= $this->input->post('amount')) {
                        $return_id = $this->client_subscription_model->update($this->input->post('id'));
                        if ($return_id) {
                            $transaction_data = $this->Transaction_model->set($this->input->post('account_no_id'), 4, $this->input->post('amount'), "SUB" . $this->input->post('id'), "[Subscription Fees Payment]");
                            $this->do_pay_journal_transaction($this->input->post('transaction_no'), $transaction_data);
                            //$this->do_journal_transaction($return_id);
                            $feedback['success'] = true;
                            $feedback['message'] = "Client subscription successfully saved";
                        } else {
                            $feedback['message'] = "There was a problem saving the client subscription details , please contact IT support";
                        }
                    } else {
                        $feedback['message'] = "Insufficient balance to complete the payment";
                    }
                } else {
                    $return_id = $this->client_subscription_model->update($this->input->post('id'));;
                    if ($return_id) {
                        $this->do_pay_journal_transaction($this->input->post('transaction_no'));
                        $feedback['success'] = true;
                        $feedback['message'] = "Client subscription successfully saved";
                        //log
                        $this->helpers->activity_logs($_SESSION['id'], 8, "Client subscription", $feedback['message'], $this->input->post('id'), $this->input->post('id'));
                    } else {
                        $feedback['message'] = "There was a problem saving the client subscription details , please contact IT support";

                        $this->helpers->activity_logs($_SESSION['id'], 8, "Client subscription", $feedback['message'], $this->input->post('id'), $this->input->post('id'));
                    }
                }
            } else {

                if ($this->input->post('payment_id') == 5) {
                    $savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $this->input->post('account_no_id'));
                    $current_balance = $savings_data['cash_bal'];
                    if ($current_balance >= $this->input->post('amount')) {
                        $return_id = $this->client_subscription_model->set();
                        if (is_numeric($return_id)) {
                            $transaction_data = $this->Transaction_model->set($this->input->post('account_no_id'), 4, $this->input->post('amount'), "SUB" . $return_id, "[Subscription Payment]");
                            $this->do_journal_transaction($return_id, $transaction_data);
                            //$this->do_journal_transaction($return_id);
                            $feedback['success'] = true;
                           $feedback['subscription_date'] = $this->client_subscription_model->get3();
                            $feedback['message'] = "Client subscription successfully saved";
                        } else {
                            $feedback['message'] = "There was a problem saving the client subscription details , please contact IT support";
                            $this->helpers->activity_logs($_SESSION['id'], 8, "Client subscription", $feedback['message'], $return_id, $return_id);
                        }
                    } else {
                        $feedback['message'] = "Insufficient balance to complete the payment";

                        $this->helpers->activity_logs($_SESSION['id'], 8, "Client subscription", $feedback['message'], "","");
                    }
                } else {
                    $return_id = $this->client_subscription_model->set();
                    if (is_numeric($return_id)) {
                        $this->do_journal_transaction($return_id);
                        $feedback['success'] = true;
                        $feedback['subscription_date'] = $this->client_subscription_model->get3();
                        $feedback['message'] = "Client subscription successfully saved";
                        $this->helpers->activity_logs($_SESSION['id'], 8, "Client subscription", $feedback['message'], $return_id, $return_id);
                    } else {
                        $feedback['message'] = "There was a problem saving the client subscription details , please contact IT support";
                        $this->helpers->activity_logs($_SESSION['id'], 8, "Client subscription", $feedback['message'], $return_id, $return_id);
                    }
                }
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
                if ($this->client_subscription_model->reverse()) {
                    if ($this->input->post('payment_id') == 5) {
                        $this->load->model('Transaction_model');
                        $this->Transaction_model->reverse("MS" . $_POST['id']);
                    }
                    $ref_id = $_POST['id'];
                    $this->journal_transaction_model->reverse($ref_id, false, "(11)");

                    $feedback['success'] = true;
                    $feedback['message'] = "Subscription successfully cancled";
                } else {
                    $feedback['message'] = "There was a problem reversing Subscription payment,";
                }
            }
        }
        echo json_encode($feedback);
    }

    private function do_pay_journal_transaction($subscription_id, $transaction_data = false)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('member_model');
        if (!empty($transaction_data['transaction_no'])) {
            $ref_no = $transaction_data['transaction_no'];
        } else {
            $ref_no = NULL;
        }
        $data = [
            'transaction_date' => $this->input->post('transaction_date'),
            'description' => $this->input->post('narrative'),
            'ref_no' => $ref_no,
            'ref_id' => $subscription_id,
            'status_id' => 1,
            'journal_type_id' => 11
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        //then we prepare the journal transaction lines
        $client = $this->member_model->get($this->input->post('client_id'));
        // print_r($client); die();

        if (!empty($client)) {
            $this->load->model('accounts_model');
            $this->load->model('subscription_plan_model');
            $this->load->model('DepositProduct_model');
            $this->load->model('savings_account_model');
            $this->load->model('transactionChannel_model');
            $this->load->model('journal_transaction_line_model');
            $subscription_plan = $this->subscription_plan_model->get($client['subscription_plan_id']);

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

            $debit_or_credit1 = $this->accounts_model->get_normal_side($subscription_plan['income_receivable_account_id'], true);
            $data = [
                [
                    $debit_or_credit1 => $this->input->post('amount'),
                    'transaction_date' => $this->input->post('transaction_date'),
                    'reference_no' => $ref_no,
                    'reference_id' => $subscription_id,
                    'narrative' => "Paid Subscription [ " . $this->input->post('subscription_date') . " ] " . $this->input->post('narrative'),
                    'account_id' => $subscription_plan['income_receivable_account_id'],
                    'status_id' => 1
                ],
                [
                    $debit_or_credit2 => $this->input->post('amount'),
                    'transaction_date' => $this->input->post('transaction_date'),
                    'reference_no' => $ref_no,
                    'reference_id' => $subscription_id,
                    'narrative' => "Paid Subscription [ " . $this->input->post('subscription_date') . " ] " . $this->input->post('narrative'),
                    'account_id' => $linked_account_id,
                    'status_id' => 1
                ]
            ];
            $this->journal_transaction_line_model->set($journal_transaction_id, $data);
        }
    }

    private function do_journal_transaction($subscription_id, $transaction_data = false)
    {
        $this->load->model('journal_transaction_model');
        $this->load->model('member_model');
        if (!empty($transaction_data['transaction_no'])) {
            $ref_no = $transaction_data['transaction_no'];
        } else {
            $ref_no = NULL;
        }
        $data = [
            'transaction_date' => $this->input->post('transaction_date'),
            'description' => $this->input->post('narrative'),
            'ref_no' => $ref_no,
            'ref_id' => $subscription_id,
            'status_id' => 1,
            'journal_type_id' => 11
        ];
        //then we post this to the journal transaction
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        //then we prepare the journal transaction lines
        $client = $this->member_model->get($this->input->post('client_id'));
        // print_r($client); die();

        if (!empty($client)) {
            $this->load->model('accounts_model');
            $this->load->model('subscription_plan_model');
            $this->load->model('DepositProduct_model');
            $this->load->model('savings_account_model');
            $this->load->model('transactionChannel_model');
            $this->load->model('journal_transaction_line_model');
            $subscription_plan = $this->subscription_plan_model->get($client['subscription_plan_id']);
            if ($this->input->post('sub_fee_paid') == 1) {
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
                $linked_account_id = $subscription_plan['income_receivable_account_id'];
                $debit_or_credit2 = $this->accounts_model->get_normal_side($subscription_plan['income_receivable_account_id'], false);
            }

            $debit_or_credit1 = $this->accounts_model->get_normal_side($subscription_plan['income_account_id'], false);
            $data = [
                [
                    $debit_or_credit1 => $this->input->post('amount'),
                    'transaction_date' => $this->input->post('transaction_date'),
                    'reference_no' => $ref_no,
                    'reference_id' => $subscription_id,
                    'narrative' => "Subscription " . $this->input->post('subscription_date') . " " . $this->input->post('narrative'),
                    'account_id' => $subscription_plan['income_account_id'],
                    'status_id' => 1
                ],
                [
                    $debit_or_credit2 => $this->input->post('amount'),
                    'transaction_date' => $this->input->post('transaction_date'),
                    'reference_no' => $ref_no,
                    'reference_id' => $subscription_id,
                    'narrative' => "Subscription " . $this->input->post('subscription_date') . " " . $this->input->post('narrative'),
                    'account_id' => $linked_account_id,
                    'status_id' => 1
                ]
            ];
            $this->journal_transaction_line_model->set($journal_transaction_id, $data);
        }
    }

    public function delete()
    {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;
        // if (isset($_SESSION['role']) && isset($_SESSION['role']) == 1) {
        if (($response['success'] = $this->client_subscription_model->delete($this->input->post('id'))) === true) {
            $response['message'] = "Client subscription details successfully deleted";
            $this->helpers->activity_logs($_SESSION['id'], 8, "Deleting client subscription", $response['message'], $this->input->post('id'), $this->input->post('id'));
        }
        // }
        echo json_encode($response);
    }

    public function change_status()
    {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Client subscription could not be $msg activated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->client_subscription_model->deactivate($this->input->post('id'))) {
            $response['message'] = "Client Subscription has successfully been $msg activated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }
}
