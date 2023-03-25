<?php

/**
 * Description of Repayment_schedule
 *
 * @author Eric
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Repayment_schedule extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->library("session");
    if (empty($this->session->userdata('id'))) {
      redirect('welcome');
    }
    date_default_timezone_set('Africa/Kampala');
    $this->load->model("repayment_schedule_model");
  }

  public function loan_ledger_card()
  {
    $data['data'] = $this->repayment_schedule_model->get_loan_ledger_card();

    $loan_details = $this->client_loan_model->get_client_loan($this->input->post('client_loan_id'));
    $penalty_applicable_after_due_date = $loan_details['penalty_applicable_after_due_date'];
    $fixed_penalty_amount = $loan_details['fixed_penalty_amount'];
    $penalty_calculation_method_id = $loan_details['penalty_calculation_method_id'];
    $last_pay_date = $loan_details['last_pay_date'];
    // $next_pay_date = $loan_details['next_pay_date'];

    foreach ($data['data'] as $key => $value) {
      $due_installments_data = $this->repayment_schedule_model->due_installments_data($value['id']);
      if (!empty($due_installments_data)) {
        $over_due_principal = $due_installments_data['due_principal'];
        if ($value['demanded_penalty'] > 0) {
          $number_of_late_days = $due_installments_data['due_days2'];
        } else {
          $number_of_late_days = $due_installments_data['due_days'] - $due_installments_data['grace_period_after'];
        }

        ##
        if (intval($penalty_calculation_method_id) == 1) {
          $penalty_rate = (($due_installments_data['penalty_rate']) / 100);
        } else {
          $penalty_rate = 1;
        }

        if ($due_installments_data['penalty_rate_charged_per'] == 4) { // One time penalty 
          $number_of_late_period = 1;
        } elseif ($due_installments_data['penalty_rate_charged_per'] == 3) {
          $number_of_late_period = intdiv($number_of_late_days, 30);
        } elseif ($due_installments_data['penalty_rate_charged_per'] == 2) {
          $number_of_late_period = intdiv($number_of_late_days, 7);
        } else {
          $number_of_late_period = $number_of_late_days;
        }


        if (intval($penalty_calculation_method_id) == 2) { // Fixed amount Penalty

          $penalty_value = ($fixed_penalty_amount * $number_of_late_period);

          $penalty_value = $due_installments_data['penalty_rate_charged_per'] == 4 ? ($due_installments_data['paid_penalty_amount'] > 0 ? 0 : ($fixed_penalty_amount * $number_of_late_period)) : ($fixed_penalty_amount * $number_of_late_period);
        } else {
          $penalty_value = ($over_due_principal * $number_of_late_period * $penalty_rate);

          $penalty_value = $due_installments_data['penalty_rate_charged_per'] == 4 ? ($due_installments_data['paid_penalty_amount'] > 0 ? 0 : ($over_due_principal * $number_of_late_period * $penalty_rate)) : ($over_due_principal * $number_of_late_period * $penalty_rate);
        }


        if ((intval($penalty_applicable_after_due_date) == 1)) {

          if ($last_pay_date >= date('Y-m-d')) {
            $penalty_value = 0;
          }
        }

        $data['data'][$key]['penalty_value'] = $value['demanded_penalty'] > 0 ? round($penalty_value + $value['demanded_penalty'], 0) : round($penalty_value, 0);
      } else {
        $data['data'][$key]['penalty_value'] = $value['demanded_penalty'];
      }
    }
    echo json_encode($data);
  }

  public function jsonList()
  {
    $data['data'] = $this->repayment_schedule_model->get();

    $loan_details = $this->client_loan_model->get_client_loan($this->input->post('client_loan_id'));
    $penalty_applicable_after_due_date = $loan_details['penalty_applicable_after_due_date'];
    $fixed_penalty_amount = $loan_details['fixed_penalty_amount'];
    $penalty_calculation_method_id = $loan_details['penalty_calculation_method_id'];
    $last_pay_date = $loan_details['last_pay_date'];
    // $next_pay_date = $loan_details['next_pay_date'];

    foreach ($data['data'] as $key => $value) {
      $due_installments_data = $this->repayment_schedule_model->due_installments_data($value['id']);
      if (!empty($due_installments_data)) {
        $over_due_principal = $due_installments_data['due_principal'];
        if ($value['demanded_penalty'] > 0) {
          $number_of_late_days = $due_installments_data['due_days2'];
        } else {
          $number_of_late_days = $due_installments_data['due_days'] - $due_installments_data['grace_period_after'];
        }

        ##
        if (intval($penalty_calculation_method_id) == 1) {
          $penalty_rate = (($due_installments_data['penalty_rate']) / 100);
        } else {
          $penalty_rate = 1;
        }

        if ($due_installments_data['penalty_rate_charged_per'] == 4) { // One time penalty 
          $number_of_late_period = 1;
        } elseif ($due_installments_data['penalty_rate_charged_per'] == 3) {
          $number_of_late_period = intdiv($number_of_late_days, 30);
        } elseif ($due_installments_data['penalty_rate_charged_per'] == 2) {
          $number_of_late_period = intdiv($number_of_late_days, 7);
        } else {
          $number_of_late_period = $number_of_late_days;
        }


        if (intval($penalty_calculation_method_id) == 2) { // Fixed amount Penalty

          $penalty_value = ($fixed_penalty_amount * $number_of_late_period);

          $penalty_value = $due_installments_data['penalty_rate_charged_per'] == 4 ? ($due_installments_data['paid_penalty_amount'] > 0 ? 0 : ($fixed_penalty_amount * $number_of_late_period)) : ($fixed_penalty_amount * $number_of_late_period);
        } else {
          $penalty_value = ($over_due_principal * $number_of_late_period * $penalty_rate);

          $penalty_value = $due_installments_data['penalty_rate_charged_per'] == 4 ? ($due_installments_data['paid_penalty_amount'] > 0 ? 0 : ($over_due_principal * $number_of_late_period * $penalty_rate)) : ($over_due_principal * $number_of_late_period * $penalty_rate);
        }


        if ((intval($penalty_applicable_after_due_date) == 1)) {

          if ($last_pay_date >= date('Y-m-d')) {
            $penalty_value = 0;
          }
        }

        $data['data'][$key]['penalty_value'] = $value['demanded_penalty'] > 0 ? round($penalty_value + $value['demanded_penalty'], 0) : round($penalty_value, 0);
      } else {
        $data['data'][$key]['penalty_value'] = $value['demanded_penalty'];
      }
    }
    echo json_encode($data);
  }

  public function jsonList2()
  {
    $data['data'] = $this->repayment_schedule_model->get();
    echo json_encode($data);
  }
  public function jsonList3()
  {
    $data['data'] = $this->repayment_schedule_model->get22();
    echo json_encode($data);
  }
  public function reschedule()
  {
    $this->load->model("client_loan_model");
    $this->load->model("loan_reversal_model");

    $response['message'] = "Previous schedule status_id could not be changed, contact IT support.";
    $response['success'] = FALSE;
    $this->db->trans_begin();

    $unique_id = $this->generate_unique_id();
    $loan = $this->loan_reversal_model->get_loan($this->input->post('client_loan_id'));
    $current_repayment_schedule = $this->repayment_schedule_model->get4("installment_number = '{$this->input->post('current_installment')}' AND client_loan_id = '{$this->input->post('client_loan_id')}'");

    $trans_tracking_data = [
      'unique_id' => $unique_id,
      'action_type_id' => 6,
      'client_loan_id' => $this->input->post('client_loan_id'),
      'loan_status_id' => $loan['status_id'],
      'loan_approved_installments' => $loan['approved_installments'],
      'loan_interest_rate' => $loan['interest_rate'],
      'loan_approved_repayment_made_every' => $loan['approved_repayment_made_every'],
      'loan_approved_repayment_frequency' => $loan['approved_repayment_frequency'],
      'loan_modified_by' => $loan['modified_by'],
      'payment_mode' => 0,
      'loan_state	' => $this->loan_reversal_model->get_max_loan_state(),
      'base_installment' => $this->input->post('current_installment'),
      'repayment_schedule_id' => $current_repayment_schedule['id'],
      'created_by' => $_SESSION['id'],
      'date_created' => date('Y-m-d h:i:s'),
      'date_modified' => date('Y-m-d h:i:s'),
      'modified_by' => $_SESSION['id'],
      'status_id' => 1
    ];
    #Update trans_tracking Table
    $this->loan_reversal_model->set_trans_tracking($trans_tracking_data);


    if ($this->repayment_schedule_model->deactivate_schedule(false, $unique_id)) {
      if ($this->client_loan_model->reschedule($unique_id)) {
        if ($this->repayment_schedule_model->set(false, $unique_id)) {

          $this->do_journal_transaction($this->input->post('client_loan_id'), $unique_id);



          if ($this->db->trans_status()) {
            $this->db->trans_commit();
            $response['success'] = TRUE;
            $response['message'] = "Loan schedule successfully Rescheduled to the new date.";
          } else {
            $this->db->trans_rollback();
            $response['message'] = "Internal error happened, contact IT support.";
          }
        } else {
          $this->db->trans_rollback();
          $response['message'] = "Loan schedule could not be changed, contact IT support.";
        }
      } else {
        $this->db->trans_rollback();
        $response['message'] = "Loan status_id and installment could not be changed, contact IT support.";
      }
    }
    echo json_encode($response);
  }

  private function do_journal_transaction($client_loan_id, $unique_id = false)
  {
    $this->load->model('journal_transaction_model');
    $this->load->model('loan_product_model');
    $this->load->model('accounts_model');
    $this->load->model('journal_transaction_line_model');
    $action_date = date('d-m-Y');
    if ($this->input->post('action_date') !== NULL) {
      $action_date = $this->input->post('action_date');
    }
    $client_loan = $this->client_loan_model->get_client_data($client_loan_id);

    if (!empty($client_loan)) {
      $data = [
        'transaction_date' =>  $action_date,
        'description' => strtoupper($this->input->post('comment')),
        'ref_no' => $client_loan['loan_no'],
        'ref_id' => $client_loan_id,
        'status_id' => 1,
        'journal_type_id' => 4,
        'unique_id' => $unique_id
      ];
      //then we post this to the journal transaction
      $journal_transaction_id = $this->journal_transaction_model->set($data);
      unset($data);
      //then we prepare the journal transaction lines
      $loan_product_details = $this->loan_product_model->get_accounts($client_loan['loan_product_id']);

      $Interest_income_ac_details = $this->accounts_model->get($loan_product_details['interest_income_account_id']);
      $Interest_receivable_ac_details = $this->accounts_model->get($loan_product_details['interest_receivable_account_id']);

      $debit_or_credit3 = ($Interest_income_ac_details['normal_balance_side'] == 1) ? 'debit_amount' : 'credit_amount';
      $debit_or_credit4 = ($Interest_receivable_ac_details['normal_balance_side'] == 1) ? 'debit_amount' : 'credit_amount';

      $index_key = 4;

      //writeoffing the interest of old loan schedule
      $old_schedule = $this->repayment_schedule_model->get_deactivate_schedule('repayment_schedule.status_id=2');
      $rescheduled_installments = '';

      foreach ($old_schedule as $key => $value) {
        if (empty($rescheduled_installments)) {
          $rescheduled_installments = $value['id'];
        } else {
          $rescheduled_installments .= ',' . $value['id'];
        }
      }
      $where_clause = "reference_id IN ($rescheduled_installments) AND reference_no='" . $client_loan['loan_no'] . "'";
      $line_data['status_id'] = 3;

      $this->journal_transaction_line_model->update_status($line_data, $where_clause);

      //Posting interest on the journal line
      $interest_data = $this->repayment_schedule_model->get_deactivate_schedule('repayment_schedule.status_id=1');
      foreach ($interest_data as $key => $value) {
        $index_key += 2;
        $transaction_date = date('d-m-Y', strtotime($value['repayment_date']));
        $data[$index_key - 1] = [
          'reference_no' => $client_loan['loan_no'],
          'reference_id' => $value['id'],
          'transaction_date' => $transaction_date,
          $debit_or_credit3 => $value['interest_amount'],
          'narrative' => strtoupper("Interest on Loan Rescheduled on " . $this->input->post('action_date')),
          'account_id' => $loan_product_details['interest_income_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];

        $data[$index_key] =  [
          'reference_no' => $client_loan['loan_no'],
          'reference_id' => $value['id'],
          'transaction_date' => $transaction_date,
          $debit_or_credit4 => $value['interest_amount'],
          'narrative' => strtoupper("Interest on Loan Rescheduled on " . $this->input->post('action_date')),
          'account_id' => $loan_product_details['interest_receivable_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
      }
      $this->journal_transaction_line_model->set($journal_transaction_id, $data);
    }
  }

  private function generate_unique_id()
  {
    $key = implode('-', str_split(substr(strtolower(md5(microtime() . rand(1000, 9999))), 0, 30), 6));
    $unique_id = join("", explode('-', $key));

    return $unique_id;
  }

  public function get_expected_loan_repayments()
  {
    $start_date = $this->input->post('repayment_expected_start_date') ? $this->input->post('repayment_expected_start_date') : ($this->input->post('repayment_expected_end_date') ? $this->input->post('repayment_expected_end_date') : date('Y-m-d')
    );
    $end_date = $this->input->post('repayment_expected_end_date') ? $this->input->post('repayment_expected_end_date') : $start_date;

    $data['data'] = $this->repayment_schedule_model->get_expected_loan_repayments($start_date, $end_date);

    echo json_encode($data);
  }
}
