<?php

/**
 * Description of loan_installment_payment
 *
 * @author Eric
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Loan_installment_payment extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->library("session");
    $this->load->library('Loan_schedule_generation');
    $this->load->model("loan_installment_payment_model");
    $this->load->model("repayment_schedule_model");
    $this->load->model("client_loan_model");
    $this->load->model("loan_state_model");
    $this->load->model("loan_product_model");
    $this->load->model('transaction_model');
    $this->load->model("Loan_guarantor_model");
    $this->load->model("miscellaneous_model");
    $this->load->model('journal_transaction_model');
    $this->load->model('accounts_model');
    $this->load->model('DepositProduct_model');
    $this->load->model('savings_account_model');
    $this->load->model('transactionChannel_model');
    $this->load->model('journal_transaction_line_model');
    $this->load->model('loan_state_model');
    $this->load->model('loan_reversal_model');
    date_default_timezone_set('Africa/Kampala');
    $this->orgdata['org'] = $this->organisation_model->get(1);
    $orgdata['branch'] = $this->organisation_model->get_org(1);
    $this->organisation = $this->orgdata['org']['name'];
    $this->contact_number = $orgdata['branch']['office_phone'];
  }

  public function jsonList()
  {
    $data['data'] = $this->loan_installment_payment_model->get();
    echo json_encode($data);
  }

  public function dateDiff($d1, $d2)
  {

    $datediff = $d1 - $d2;

    return round($datediff / (60 * 60 * 24));

    //return round(abs(strtotime($d1) - strtotime($d2)) / 86400);
  }
  //checking interest amount & principal amount for paying off a loan
  public function get_pay_off_data()
  {
    $this->load->model("loan_product_fee_model");
    $response['success'] = FALSE;
    $loan_id = $this->input->post('id');
    if ($this->input->post('payment_date') != '') {
      $payment_date = $this->input->post('payment_date');
    } else {
      $payment_date = false;
    }
    if (!(empty($loan_id))) {
      if ($this->input->post('state_id') == 13) {
        $data['interest_principal_sum'] = $this->repayment_schedule_model->sum_interest_principal($loan_id, false, True);
      } else {
        if ($this->orgdata['org']['no_current_interest'] == 1) {
          $no_current_interest = 1;
        } else {
          $no_current_interest = FALSE;
        }
        $data['interest_principal_sum'] = $this->repayment_schedule_model->sum_interest_principal($loan_id, false, false, $no_current_interest);
      }

      $data['paid_sum'] = $this->loan_installment_payment_model->sum_paid_installment($loan_id);

      $response['success'] = True;
      $response['pay_off_data']['total_interest_sum'] = floatval($data['interest_principal_sum']['interest_sum']);
      $response['pay_off_data']['to_date_interest_sum'] = floatval($data['interest_principal_sum']['to_date_interest_sum']);
      $response['pay_off_data']['principal_sum'] = floatval($data['interest_principal_sum']['principal_sum']);
      $response['pay_off_data']['already_paid_sum'] = floatval($data['paid_sum']['already_paid_sum']);
      $response['pay_off_data']['already_principal_amount'] = floatval($data['paid_sum']['already_principal_amount']);
      $response['pay_off_data']['already_interest_amount'] = floatval($data['paid_sum']['already_interest_amount']);

      ###
      $response['pay_off_data']['penalty_value'] = 0;
      $response['pay_off_data']['late_days'] = 0;
      $response['pay_off_data']['penalty_message'] = '';
      $penalty_total = 0;

      $response['available_loan_fees'] = $this->loan_product_fee_model->get(" loanproduct_id = '" . $this->input->post('loan_product_id') . "' and fms_loan_fees.id not in 
        ( SELECT loan_product_fee_id from fms_applied_loan_fee WHERE client_loan_id = '" . $this->input->post('id') . "' and status_id = 1 ) ");


      $repayment_schedule_data = $this->repayment_schedule_model->get("client_loan_id=" . $loan_id . " AND payment_status IN (2,4)");
      //calculating penalty charged
      foreach ($repayment_schedule_data as $key => $value) {
        $penalty_data = $this->get_penalty_data($value['client_loan_id'], $value['installment_number'], 1, $payment_date);
        $response['pay_off_data']['penalty_value'] += $penalty_data['penalty_data']['penalty_value'];
        $penalty_total += ($penalty_data['penalty_data']['penalty_value'] + $penalty_data['penalty_data']['demanded_penalty']);
        /* if ($penalty_data['penalty_data']['penalty_value'] > 0) {
          $penalty_rate = $penalty_data['penalty_data']['penalty_rate'];
          $response['pay_off_data']['late_days'] += $penalty_data['penalty_data']['late_days'];
        } */
      }

      $response['pay_off_data']['penalty_value'] = round($penalty_total, 2);

      /* if ($response['pay_off_data']['penalty_value'] > 0) {
        $response['pay_off_data']['penalty_message'] = "Note: This payment has been made late for about " . $response['pay_off_data']['late_days'] . " day(s), therefore penalty on the due principal for each installment has been applied at a rate of " . $penalty_rate . "%";
      } else {
        $response['pay_off_data']['penalty_message'] = " This payment is on time or the product does not attract penalty, therefore no penalty charged";
      } */

      if ($response['pay_off_data']['penalty_value'] <= 0) {
        $response['pay_off_data']['penalty_message'] = " This payment is on time or the product does not attract penalty, therefore no penalty charged";
      }

      #####

    }

    echo json_encode($response);
  } //End of the get_pay_off_data function

  public function payment_data($loan_ref_no = false, $installment_number = false, $call_type = false)
  {
    $response[] = '';
    if (!empty($_POST['loan_ref_no']) && !empty($_POST['installment_number'])) {
      $loan_ref_no = $this->input->post('loan_ref_no');
      $installment_number = $this->input->post('installment_number');
      $call_type = $this->input->post('call_type');
    }
    //$response['payment_data'] = $this->client_loan_model->get_payment_data($loan_ref_no,$installment_number);
    if (!empty($call_type) && $call_type != '' && $call_type != 'client_loan') {
      $response['payment_data'] = $this->client_loan_model->get_payment_data($loan_ref_no, $installment_number, $call_type);
    } else {
      if (is_array($installment_number)) {
        // print_r($installment_number); die;
        $response['payment_data'] = $this->client_loan_model->get_payment_data_2($loan_ref_no, $installment_number);
      } else {
        $response['payment_data'] = $this->client_loan_model->get_payment_data($loan_ref_no, $installment_number);
      }
    }
    //print_r($response['payment_data']['id']); die;
    if ($response['payment_data']['id'] == '') {
    } else {
      $response['penalty_data']['message'] = '';
      $response['penalty_data']['penalty_value'] = 0;
      if (is_array($installment_number)) {
        foreach ($installment_number as $key => $value) {
          $data = $this->get_penalty_data($response['payment_data']['id'], $value, 1);
          $response['penalty_data']['message'] = $data['penalty_data']['message'];
          $response['penalty_data']['penalty_value'] += $data['penalty_data']['penalty_value'];
        }
      } else {
        $data = $this->get_penalty_data($response['payment_data']['id'], $installment_number, 1);
        $response['penalty_data']['message'] = $data['penalty_data']['message'];
        $response['penalty_data']['penalty_value'] = $data['penalty_data']['penalty_value'];
      }
    }

    $response['payment_data']['total_amount'] = $response['payment_data']['remaining_principal'] + $response['payment_data']['remaining_interest'];
    echo json_encode($response);
  }
  //penalty calculation
  public function penalty_calculation()
  {
    $penalty_total = 0;
    $due_installments_data = $this->repayment_schedule_model->due_installments_data();
    foreach ($due_installments_data as $key => $value) {
      $over_due_principal = $value['due_principal'];
      $number_of_late_days = $value['due_days'] - $value['grace_period_after'];
      $penalty_rate = (($value['penalty_rate']) / 100);
      $penalty_value = ($over_due_principal * $number_of_late_days * $penalty_rate) - $value['paid_penalty_amount'];
      $penalty_total += $penalty_value;
    }
    $penalty_data['penalty_total'] = $penalty_total;
    return $penalty_data;
  }
  //checking the penalty if any for loan installment
  public function get_penalty_data($loan_id = false, $installment_number = false, $call_type = false, $payment_date = false)
  {

    $response['success'] = FALSE;
    if ($loan_id === false && $installment_number === false) {
      $loan_id = $this->input->post('client_loan_id');
      $installment_number = $this->input->post('installment_number');
    }
    if ($this->input->post('payment_date') != '') {
      $payment_date = $this->input->post('payment_date');
    }
    if (!(empty($loan_id))) {
      $loan_details = $this->client_loan_model->get_client_loan($loan_id);
      $penalty_applicable_after_due_date = $loan_details['penalty_applicable_after_due_date'];
      $fixed_penalty_amount = $loan_details['fixed_penalty_amount'];
      $penalty_calculation_method_id = $loan_details['penalty_calculation_method_id'];
      $last_pay_date = $loan_details['last_pay_date'];
      // $next_pay_date = $loan_details['next_pay_date'];

      $data['penalty_data'] = $this->repayment_schedule_model->get_due_date($loan_id, $installment_number);

      $data['payment_data'] = $this->loan_installment_payment_model->sum_paid_installment("loan_installment_payment.client_loan_id=$loan_id AND repayment_schedule.installment_number=$installment_number");
      $due_date = date('d-m-Y', strtotime($data['penalty_data']['repayment_date']));
      if ($payment_date !== false) {
        $payment_date = date('d-m-Y', strtotime($payment_date));
      } else {
        $payment_date = date('d-m-Y');
      }
      $penalty_value = 0;
      $demanded_penalty = $data['penalty_data']['demanded_penalty']; // We shall eventually need to sum penalty_value & demanded_penalty in the future. We are not summing now because alot of our frontend code is adding the demanded_penalty to the penalty_value
      if ((strtotime($payment_date)) > (strtotime($due_date))) {

        $number_of_days =  $this->dateDiff(strtotime($payment_date), strtotime($due_date));

        if (!empty($data['penalty_data']['penalty_rate'])) {
          $grace_period = $data['penalty_data']['grace_period_after'];
          if (($this->input->post('state_id') != '') && ($this->input->post('state_id') != 12)) {
            if ($number_of_days > $grace_period) {
              $over_due_principal = ($data['penalty_data']['principal_amount'] - $data['payment_data']['already_principal_amount']);

              if ($data['penalty_data']['demanded_penalty'] > 0) {

                $number_of_late_days =  strtotime($payment_date) < strtotime($data['penalty_data']['actual_payment_date']) ? 0 :  $this->dateDiff(strtotime($payment_date), strtotime($data['penalty_data']['actual_payment_date']));
              } else {
                $number_of_late_days = $number_of_days - $grace_period;
              }



              ##
              if (intval($penalty_calculation_method_id) == 1) {
                $penalty_rate = (($data['penalty_data']['penalty_rate']) / 100);
              } else {
                $penalty_rate = 1;
              }

              $response['penalty_data']['penalty_rate'] = $data['penalty_data']['penalty_rate'];
              $response['penalty_data']['late_days'] = $number_of_late_days;

              if ($data['penalty_data']['penalty_rate_charged_per'] == 4) { // One time penalty 
                $number_of_late_period = 1;
                $response['penalty_data']['late_period'] = "";
              } elseif ($data['penalty_data']['penalty_rate_charged_per'] == 3) {
                $number_of_late_period = intdiv($number_of_late_days, 30);
                $response['penalty_data']['late_period'] = $number_of_late_period . ' Month(s)';
              } elseif ($data['penalty_data']['penalty_rate_charged_per'] == 2) {
                $number_of_late_period = intdiv($number_of_late_days, 7);
                $response['penalty_data']['late_period'] = $number_of_late_period . ' Week(s)';
              } else {
                $number_of_late_period = $number_of_late_days;
                $response['penalty_data']['late_period'] = $number_of_late_period . ' Day(s)';
              }

              if ($number_of_late_period > 0) {

                if (intval($penalty_calculation_method_id) == 2) { // Fixed amount Penalty

                  $penalty_value = ($fixed_penalty_amount * $number_of_late_period);

                  $penalty_value = $data['penalty_data']['penalty_rate_charged_per'] == 4 ? ($data['payment_data']['already_paid_penalty'] > 0 ? 0 : ($fixed_penalty_amount * $number_of_late_period)) : ($fixed_penalty_amount * $number_of_late_period);
                } else {
                  $penalty_value = ($over_due_principal * $number_of_late_period * $penalty_rate);

                  $penalty_value = $data['penalty_data']['penalty_rate_charged_per'] == 4 ? ($data['payment_data']['already_paid_penalty'] > 0 ? 0 : ($over_due_principal * $number_of_late_period * $penalty_rate)) : ($over_due_principal * $number_of_late_period * $penalty_rate);
                }

                if ((intval($penalty_applicable_after_due_date) == 1)) {

                  if ($last_pay_date >= date('Y-m-d')) {
                    $penalty_value = 0;
                  }
                }

                if ($penalty_value > 0) { //this only happens if principal is not fully covered else principal was covered and no penalty
                  $response['penalty_data']['message'] = "Note: This payment has been made late for about " . $response['penalty_data']['late_period'] . ", therefore penalty on the due principal has been applied at a rate of " . $data['penalty_data']['penalty_rate'] . "%";
                } else {
                  $response['penalty_data']['message'] = "Note: This payment has been made late for about " . $response['penalty_data']['late_period'] . ",";
                }
              } else {
                $response['penalty_data']['message'] = "Note: This payment has been made late for about $number_of_late_days day(s), Which is " . $response['penalty_data']['late_period'] . ' so no penalty charged';
              }
            } else {
              $penalty_value = 0;
              $response['penalty_data']['message'] = "Note: This payment has been made late for about $number_of_days day(s), but no penalty charged due to the grace period of $grace_period days available";
            }
          } else {
            $penalty_value = 0;
            $response['penalty_data']['message'] = "Note: This payment has been made late for about $number_of_days day(s), so ONLY the penalty that accrued at the time of locking is charged";
          }
        } else {
          $response['penalty_data']['message'] = "Note: This payment has been made late for about $number_of_days day(s), but no penalty charged because this loan product doesnt attract penalty";
        }
      } else {
        $response['penalty_data']['message'] = "Note: This payment is on time, therefore no penalty charged";
      }
      $response['success'] = True;
      $response['penalty_data']['penalty_value'] = round($penalty_value, 0);
      $response['penalty_data']['demanded_penalty'] = round($demanded_penalty, 0);
      $response['penalty_data']['total_installment_penalty'] = round(($penalty_value + $demanded_penalty), 0);
    }

    if ($call_type !== false) {
      return $response;
    } else {
      echo json_encode($response);
    }
  } //End of the get_penalty_data function

  private function do_journal_reschedule($client_loan_id, $unique_id = false)
  {
    $this->load->model('journal_transaction_model');
    $this->load->model('loan_product_model');
    $this->load->model('accounts_model');
    $this->load->model('journal_transaction_line_model');
    $action_date = date('d-m-Y');
    if ($this->input->post('action_date') !== NULL) {
      $action_date = $this->input->post('action_date');
    }
    if ($this->input->post('payment_date') !== NULL) {
      $action_date = $this->input->post('payment_date');
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
      $line_data['unique_id'] = $unique_id;

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
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $client_loan['loan_no'],
          $debit_or_credit3 => $value['interest_amount'],
          'narrative' => strtoupper("Interest on Loan Restructure done on " . $action_date),
          'account_id' => $loan_product_details['interest_income_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];

        $data[$index_key] =  [
          'reference_no' => $client_loan['loan_no'],
          'reference_id' => $value['id'],
          'transaction_date' => $transaction_date,
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $client_loan['loan_no'],
          $debit_or_credit4 => $value['interest_amount'],
          'narrative' => strtoupper("Interest on Loan Restructure done on " . $action_date),
          'account_id' => $loan_product_details['interest_receivable_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
      }
      $this->journal_transaction_line_model->set($journal_transaction_id, $data);
    }
  }

  private function schedule_reconstruction($unique_id = false)
  {
    $filter = array(
      'current_installment' => ($this->input->post('installment_number') + 1),
      'client_loan_id' => $this->input->post('client_loan_id'),
      'unique_id' => $unique_id
    );
    $schedule_data = $this->repayment_schedule_model->get_due_date($this->input->post('client_loan_id'), $this->input->post('installment_number'));
    $loan_data = $this->repayment_schedule_model->get_loan_data($this->input->post('client_loan_id'));
    if ($this->repayment_schedule_model->deactivate_schedule($filter)) {
      //create new schedules
      $repayment_frequency = $loan_data['approved_repayment_frequency'];
      $repayment_made = $loan_data['approved_repayment_made_every'];
      if ($repayment_made == 1) {
        $schedule_date = $repayment_frequency . ' day';
        $repayment_made_every = '365';
      } elseif ($repayment_made == 2) {
        $schedule_date = $repayment_frequency . ' week';
        $repayment_made_every = '52';
      } else {
        $schedule_date = $repayment_frequency . ' month';
        $repayment_made_every = '12';
      }
      $p = ($loan_data['amount_approved'] - $this->input->post('extra_principal'));
      $n = $loan_data['approved_installments'];
      $r = $interest_rate_per_annum = $interest_rate_per_installment = ((($loan_data['interest_rate']) * 1) / 100);
      $l = $length_of_a_period = ($repayment_frequency / $repayment_made_every);
      $i = $interest_rate_per_period = ($r * $l);
      $number_of_years = $n * $l;
      $current_installment = $this->input->post('installment_number');
      $installment_counter = $this->input->post('installment_number');

      $payment_date = strtotime($schedule_data['repayment_date']);
      $payment_date1 = $schedule_data['repayment_date'];

      $support_data = array(
        'p' => $p, 'n' => $n, 'r' => $r, 'i' => $i,
        'current_installment' => $current_installment,
        'installment_counter' => $installment_counter,
        'number_of_years' => $number_of_years,
        'product_type_id' => $loan_data['product_type_id'],
        'schedule_date' => $schedule_date,
        'payment_date' => $payment_date,
        'payment_date1' => $payment_date1,
      );
      $response = $this->loan_schedule_generation->generate($support_data);

      $repayment_schedules = $response['payment_schedule'];
      $data = [];
      foreach ($repayment_schedules as $key => $repayment_schedule) {
        $data[] = array(
          'repayment_date' => date('Y-m-d', $repayment_schedule['payment_date']),
          'interest_amount' => $repayment_schedule['interest_amount'],
          'principal_amount' => $repayment_schedule['principal_amount'],
          'client_loan_id' => $this->input->post('client_loan_id'),
          'payment_status' => '4', //meaning pending
          'grace_period_on' => '3', //meaning days
          'status_id' => '1', //meaning active schedule
          'installment_number' => $repayment_schedule['installment_number'],
          'interest_rate' => $loan_data['interest_rate'],
          'grace_period_after' => $schedule_data['grace_period_after'],
          'repayment_made_every' => $repayment_made,
          'repayment_frequency' => $repayment_frequency,
          'comment' => $this->input->post('comment'),
          'date_created' => time(),
          'created_by' => $_SESSION['id'],
          'unique_id' => $unique_id
        );
      }
      $this->repayment_schedule_model->set2($data);
      $this->do_journal_reschedule($this->input->post('client_loan_id'), $unique_id);
    }
  }

  # Forgive Interest Journal
  public function do_journal_forgive_interest($sent_id, $interest_amount = false, $repayment_date, $installment_number, $unique_id = false)
  {
    $this->load->model('accounts_model');
    $this->load->model('transactionChannel_model');
    $this->load->model('journal_transaction_line_model');

    $data = [
      'transaction_date' => $repayment_date,
      'description' => strtoupper("Un paid Interest / Written Off ") . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $this->input->post('member_name') . " ] ",
      'ref_no' => $this->input->post('loan_ref_no'),
      'ref_id' => $sent_id,
      'status_id' => 1,
      'journal_type_id' => 35,
      'unique_id' => $unique_id
    ];
    //then we post this to the journal transaction
    $journal_transaction_id = $this->journal_transaction_model->set($data);
    unset($data);

    $loan_product_details = $this->loan_product_model->get_accounts($this->input->post('loan_product_id'));

    $debit_or_credit4 = $this->accounts_model->get_normal_side($loan_product_details['interest_receivable_account_id'], true);
    $debit_or_credit5 = $this->accounts_model->get_normal_side($loan_product_details['interest_income_account_id'], true);

    $data[0] = [
      'reference_no' => $this->input->post('loan_ref_no'),
      'reference_id' => $sent_id,
      'transaction_date' => $repayment_date,
      'member_id' => $this->input->post('member_id'),
      'reference_key' => $this->input->post('loan_ref_no'),

      $debit_or_credit4 => $interest_amount ? $interest_amount : $this->input->post('forgiven_interest'),
      'narrative' => strtoupper("Un paid Interest / Cleared ") . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $this->input->post('member_name') . " ] ",
      'account_id' => $loan_product_details['interest_receivable_account_id'],
      'status_id' => 1,
      'unique_id' => $unique_id
    ];
    $data[1] = [
      'reference_no' => $this->input->post('loan_ref_no'),
      'reference_id' => $sent_id,
      'transaction_date' => $repayment_date,
      'member_id' => $this->input->post('member_id'),
      'reference_key' => $this->input->post('loan_ref_no'),
      $debit_or_credit5 => $interest_amount ? $interest_amount : $this->input->post('forgiven_interest'),
      'narrative' => strtoupper("Un paid Interest / Cleared ") . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $this->input->post('member_name') . " ] ",
      'account_id' => $loan_product_details['interest_income_account_id'],
      'status_id' => 1,
      'unique_id' => $unique_id,
    ];
    $this->journal_transaction_line_model->set($journal_transaction_id, $data);

    $where_clause = "reference_id =" . $installment_number . " AND reference_no='" . $this->input->post('loan_ref_no') . "' AND transaction_date='" . $this->helpers->yr_transformer($repayment_date) . "'";
    $line_data['transaction_date'] = $this->helpers->yr_transformer($repayment_date);
    $line_data['unique_id'] = $unique_id;

    $this->journal_transaction_line_model->update_status($line_data, $where_clause);
  }

  # Multiple Installment Payment
  public function multiple_loan_installment_payment()
  {

    $response['message'] = "Loan amount could not be paid, contact IT support.";
    $response['success'] = FALSE;

    // Generate a unique_id for tracking
    $this->unique_id = $this->generate_unique_id();

    $this->form_validation->set_rules('payment_date', 'Payment date', array('required'));
    $this->form_validation->set_rules('paid_total', 'Total Amount', 'required|greater_than[0]');
    $this->form_validation->set_rules('forgive_penalty', 'Forgive Penalty', 'required');
    $this->form_validation->set_rules('with_interest', 'Collect Interest Not Due', 'required');

    if ($this->form_validation->run() === FALSE) {
      $response['message'] = validation_errors();
    } else {
      $this->db->trans_begin();
      if ($this->input->post('payment_id') == 5) {
        $amount = $this->input->post('paid_total');

        $savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $this->input->post('savings_account_id'));
        $current_balance = $savings_data['cash_bal'];

        if ($current_balance >= $amount) {
          $transaction_data = $this->deduct_payment_multiple_installment($this->input->post('paid_total'), $this->unique_id);
          if ($transaction_data) {
            $response = $this->multiple_installment_payment_functions($transaction_data, $this->unique_id);
          } else {
            $response['message'] = "There was a problem, please contact IT support";
          }
        } else {
          $response['message'] = "Insufficient balance to complete the payment";
        }
      } else { //Payment method other than savings
        $response = $this->multiple_installment_payment_functions(false, $this->unique_id);
      }

      // Post to trans_tracking table
      $trans_data = [
        'action_type_id' => 2,
        'payment_mode' => $this->input->post('payment_id'),
        'unique_id' => $this->unique_id,
        'client_loan_id' => $this->input->post('client_loan_id'),
        'loan_state' => $this->loan_reversal_model->get_max_loan_state(),
        'created_by' => isset($_SESSION['id']) ? $_SESSION['id'] : 0,
        'date_created' => date('Y-m-d h:i:s'),
        'modified_by' => isset($_SESSION['id']) ? $_SESSION['id'] : 0,
        'status_id' => 1
      ];

      $this->loan_reversal_model->set_trans_tracking($trans_data);

      if ($this->db->trans_status()) {
        $this->db->trans_commit();
      } else {
        $this->db->trans_rollback();
        $response['message'] = "An Error happened while recording the payment. Please Try again later";
      }
    }

    echo json_encode($response);
  }

  public function get_total_penalty_data()
  {
    $penalty_data = $this->calculate_penalty();

    echo json_encode(['total_penalty' => $penalty_data['total_penalty']]);
  }

  private function calculate_penalty()
  {
    $this->load->model('repayment_schedule_model');

    $loan_id = $this->input->post('client_loan_id');

    $schedule['data'] = $this->repayment_schedule_model->get("client_loan_id=" . $loan_id . " AND payment_status IN (2,4)");

    $installments_penalty = array();
    $total_penalty = 0;

    foreach ($schedule['data'] as $key => $value) {


      $installment_number = $value['installment_number'];

      if ($this->input->post('payment_date') != '') {
        $payment_date = $this->input->post('payment_date');
      }

      if (!(empty($loan_id))) {

        $data['penalty_data'] = $this->repayment_schedule_model->get_due_date($loan_id, $installment_number);
        $data['payment_data'] = $this->loan_installment_payment_model->sum_paid_installment("loan_installment_payment.client_loan_id=$loan_id AND repayment_schedule.installment_number=$installment_number");
        $due_date = date('d-m-Y', strtotime($data['penalty_data']['repayment_date']));

        if ($payment_date !== false) {
          $payment_date = date('d-m-Y', strtotime($payment_date));
        } else {
          $payment_date = date('d-m-Y');
        }

        $penalty_value = 0;
        if ((strtotime($payment_date)) > (strtotime($due_date))) {
          $number_of_days = $this->dateDiff((strtotime($payment_date)), (strtotime($due_date)));

          if (!empty($data['penalty_data']['penalty_rate'])) {

            $grace_period = $data['penalty_data']['grace_period_after'];

            if ($number_of_days > $grace_period) {
              $over_due_principal = ($data['penalty_data']['principal_amount'] - $data['payment_data']['already_principal_amount']);


              if ($data['penalty_data']['demanded_penalty'] > 0) {
                $number_of_late_days =  $this->dateDiff(strtotime($payment_date), strtotime($data['penalty_data']['actual_payment_date']));
              } else {
                $number_of_late_days = $number_of_days - $grace_period;
              }

              $penalty_rate = (($data['penalty_data']['penalty_rate']) / 100);

              $response['penalty_data']['penalty_rate'] = $data['penalty_data']['penalty_rate'];
              $response['penalty_data']['late_days'] = $number_of_late_days;
              if ($data['penalty_data']['penalty_rate_charged_per'] == 3) {
                $number_of_late_period = intdiv($number_of_late_days, 30);
                $response['penalty_data']['late_period'] = $number_of_late_period . ' Month(s)';
              } elseif ($data['penalty_data']['penalty_rate_charged_per'] == 2) {
                $number_of_late_period = intdiv($number_of_late_days, 7);
                $response['penalty_data']['late_period'] = $number_of_late_period . ' Week(s)';
              } else {
                $number_of_late_period = $number_of_late_days;
                $response['penalty_data']['late_period'] = $number_of_late_period . ' Day(s)';
              }
              if (($this->input->post('state_id') != '') && ($this->input->post('state_id') == 12)) {
                $penalty_value = $data['penalty_data']['demanded_penalty'];
              } else {
                $penalty_value = ($over_due_principal * $number_of_late_period * $penalty_rate) + $data['penalty_data']['demanded_penalty'];
              }
              $installments_penalty[$value['id']] = $penalty_value;
              $total_penalty += $penalty_value;
            }
          }
        }
      }
    }

    $response = [
      'installments_penalty' => round($installments_penalty, 0),
      'total_penalty' => round($total_penalty, 0)
    ];

    return $response;
  }

  # Multiple Installment Payment Functions
  private function multiple_installment_payment_functions($savings_payment_data = false, $unique_id = false)
  {
    // $penalty = $this->calculate_penalty();
    // $total_penalty = $penalty['total_penalty'];
    // $installments_penalty = $penalty['installments_penalty'];

    $response['message'] = "Loan amount could not be paid, contact IT support.";
    $response['success'] = FALSE;

    // this assumed no payments were done $installment_data = $this->repayment_schedule_model->get2("client_loan_id=" . $this->input->post('client_loan_id') . " AND payment_status IN (2,4)");
    $installment_data = $this->repayment_schedule_model->get_due_schedules("client_loan_id=" . $this->input->post('client_loan_id') . " AND payment_status IN (2,4)");

    $sent_date = explode('-', $this->input->post('payment_date'), 3);
    $payment_date = count($sent_date) === 3 ? ($sent_date[2] . "-" . $sent_date[1] . "-" . $sent_date[0]) : null;

    $balance = round(($this->input->post('paid_total')), 2);

    $total_forgiven_interest = 0;
    $total_forgiven_penalty = 0;
    $total_paid_penalty = 0;
    $total_paid_principal = 0;
    $total_paid_interest = 0;
    $total_expected_penalty = 0;
    $total_expected_principal = 0;
    $total_expected_interest = 0;

    $compiled_installment_payments = array();

    $last_insert_id = '';

    foreach ($installment_data as $key => $installment) {
      $penalty_data1 = $this->get_penalty_data($this->input->post('client_loan_id'), $installment['installment_number'], TRUE, $payment_date);


      $installment_data = array();

      $installment_data['prev_demanded_penalty'] = $installment['demanded_penalty'];
      $installment_data['prev_payment_status'] = $installment['payment_status'];
      $installment_data['prev_payment_date'] = $installment['actual_payment_date'];
      $installment_data['unique_id'] = $unique_id;

      $installment_data['id'] = $installment['id'];
      $installment_data['expected_principal'] = $installment['principal_amount'];
      $installment_data['expected_penalty'] = $penalty_data1['penalty_data']['total_installment_penalty'];
      $installment_data['expected_interest'] = $installment['interest_amount'];
      $installment_data['repayment_date'] = $this->helpers->extract_date_time_hyphen($installment['repayment_date'], "d-m-Y");
      $installment_data['id'] = $installment['id'];
      $installment_data['expected_total'] = $installment['interest_amount'] + $installment['principal_amount'] + $installment_data['expected_penalty'];

      $forgive_penalty = false; // Default to collecting Penalty
      $forgive_interest = false; // Default to collecting Interest even when not due

      if (!empty($this->input->post('forgive_penalty'))) { // Determine whether to forgive penalty
        $forgive_penalty = intval($this->input->post('forgive_penalty')) == 1 ? true : false;
      }

      if (isset($_POST['with_interest'])) {
        if (intval($this->input->post('with_interest')) == 0) { // Donot collect Interest not due
          $forgive_interest = $payment_date > $installment['repayment_date'] ? false : true;
        } else { // Collect Interest even when its not due
          $forgive_interest = false;
        }
      }


      $installment_data['forgiven_penalty'] = ($forgive_penalty ? $installment_data['expected_penalty'] : 0);
      $demanded_penalty = $forgive_penalty ? 0 : $installment_data['expected_penalty'];

      $total_forgiven_penalty += $installment_data['forgiven_penalty'];

      $demanded_interest = $forgive_interest ? 0 : $installment['interest_amount'];
      $installment_data['forgiven_interest'] = $forgive_interest ? $installment['interest_amount'] : 0;

      $payable_amount = $installment['principal_amount'] + $demanded_interest + $demanded_penalty;

      $total_forgiven_interest += $installment_data['forgiven_interest'];

      if (($balance > 0) && ($payable_amount > 0)) {
        if ($balance >= $payable_amount) {
          $installment_data['paid_principal'] = $installment['principal_amount'];

          $installment_data['paid_interest'] = $demanded_interest;

          $installment_data['paid_penalty'] = $demanded_penalty;

          $installment_data['paid_total'] = $installment_data['paid_principal'] + $installment_data['paid_interest'] + $installment_data['paid_penalty'];

          $installment_data['payment_status'] = 1;

          $balance = $balance - $installment_data['paid_total'];
        } elseif ((($installment['principal_amount'] + $demanded_interest)  > 0) && ($balance > ($installment['principal_amount'] + $demanded_interest))) {
          $installment_data['paid_principal'] = $installment['principal_amount'];

          $installment_data['paid_interest'] = $demanded_interest;

          $installment_data['paid_penalty'] = $demanded_penalty > 0 ? $balance - ($installment['principal_amount'] + $demanded_interest) : 0;

          $installment_data['paid_total'] = $installment_data['paid_principal'] + $installment_data['paid_interest'] + $installment_data['paid_penalty'];

          $balance = $balance - $installment_data['paid_total'];

          $installment_data['payment_status'] = 2;
        } elseif (($installment['principal_amount'] > 0) && ($balance > $installment['principal_amount'])) {

          $installment_data['paid_principal'] = $installment['principal_amount'];
          $bal = $balance - $installment['principal_amount'];
          $installment_data['paid_interest'] = $forgive_interest ? 0 : ($bal <= $demanded_interest ? $bal : $demanded_interest);
          $bal2 = $balance - ($installment_data['paid_principal'] + $installment_data['paid_interest']);
          $installment_data['paid_penalty'] = $demanded_penalty > 0 ? ($bal2 <= $demanded_penalty ? $bal2 : $demanded_penalty) : 0;

          $installment_data['paid_total'] = $installment_data['paid_principal'] + $installment_data['paid_interest'] + $installment_data['paid_penalty'];

          $balance = $balance - $installment_data['paid_total'];

          $installment_data['payment_status'] = 2;
        } elseif (($installment['principal_amount'] > 0) && ($balance <= $installment['principal_amount'])) {

          $installment_data['paid_principal'] = $balance;
          $installment_data['paid_interest'] = 0;
          $installment_data['paid_penalty'] = 0;

          $balance = $balance - $installment_data['paid_principal'];

          $installment_data['paid_total'] = $installment_data['paid_principal'];

          $installment_data['payment_status'] = 2;
        } elseif (($demanded_interest > 0) && ($balance > $demanded_interest)) {

          $installment_data['paid_interest'] = $demanded_interest;
          $installment_data['paid_principal'] = 0;
          $bal = $balance - $installment_data['paid_interest'];
          $installment_data['paid_penalty'] = $demanded_penalty > 0 ? ($bal <= $demanded_penalty ? $bal : $demanded_penalty) : 0;

          $balance = $balance - ($installment_data['paid_interest'] + $installment_data['paid_penalty']);
          $installment_data['paid_total'] = $installment_data['paid_interest'] + $installment_data['paid_penalty'];

          $installment_data['payment_status'] = 2;
        } elseif (($demanded_interest > 0) && ($balance <= $demanded_interest)) {

          $installment_data['paid_interest'] = $balance;
          $installment_data['paid_principal'] = 0;
          $installment_data['paid_penalty'] = 0;

          $balance = $balance - $installment_data['paid_interest'];
          $installment_data['paid_total'] = $installment_data['paid_interest'];

          $installment_data['payment_status'] = 2;
        } elseif (($demanded_penalty > 0) && ($balance > $demanded_penalty)) { // This Condition might never be reached!! ..IT WAS REACHED
          $installment_data['paid_interest'] = 0;
          $installment_data['paid_principal'] = 0;
          $installment_data['paid_penalty'] = $demanded_penalty;

          $balance = $balance - $installment_data['paid_penalty'];
          $installment_data['paid_total'] = $installment_data['paid_penalty'];
        } elseif (($demanded_penalty > 0) && ($balance <= $demanded_penalty)) {
          $installment_data['paid_interest'] = 0;
          $installment_data['paid_principal'] = 0;
          $installment_data['paid_penalty'] = $balance;

          $balance = $balance - $installment_data['paid_penalty'];
          $installment_data['paid_total'] = $installment_data['paid_penalty'];

          if ($demanded_penalty ==  $installment_data['paid_penalty']) {
            $installment_data['payment_status'] = 1;
          } else {
            $installment_data['payment_status'] = 2;
          }
        }


        if ($installment_data['paid_total'] > 0) {
          $installment_data['demanded_penalty'] = $forgive_penalty ? 0 : ($installment_data['expected_penalty'] - $installment_data['paid_penalty']);
          $installment_data['actual_payment_date'] = $payment_date;
          $installment_data['modified_by'] = (isset($_SESSION['id'])) ? $_SESSION['id'] : 1;

          $total_paid_penalty += $installment_data['paid_penalty'];
          $total_paid_principal += $installment_data['paid_principal'];
          $total_paid_interest += $installment_data['paid_interest'];
          $total_expected_penalty += $installment_data['expected_penalty'];
          $total_expected_principal += $installment_data['expected_principal'];
          $total_expected_interest += $installment_data['expected_interest'];

          $compiled_installment_payments[] = [
            'id' => $installment_data['id'],
            'demanded_penalty' => $installment_data['demanded_penalty'],
            'payment_status' => $installment_data['payment_status'],
            'actual_payment_date' => $installment_data['actual_payment_date'],
            'modified_by' => $installment_data['modified_by'],
            'unique_id' => $unique_id
          ];

          $sent_data = array();
          $sent_data['receipt_amount'] = $this->input->post('paid_total');
          $sent_data['payment_date'] = $payment_date;
          $sent_data['repayment_schedule_id'] = $installment_data['id'];
          $sent_data['client_loan_id'] = $this->input->post('client_loan_id');
          $sent_data['comment'] = $this->input->post('comment');
          $sent_data['paid_interest'] = $installment_data['paid_interest'];
          $sent_data['paid_principal'] = $installment_data['paid_principal'];
          $sent_data['paid_penalty'] = $installment_data['paid_penalty'];
          $sent_data['forgiven_penalty'] = $installment_data['forgiven_penalty'];
          $sent_data['forgiven_interest'] = $installment_data['forgiven_interest'];
          $sent_data['expected_penalty'] = $installment_data['expected_penalty'];
          $sent_data['prev_demanded_penalty'] = $installment_data['prev_demanded_penalty'];
          $sent_data['prev_payment_status'] = $installment_data['prev_payment_status'];
          $sent_data['prev_payment_date'] = $installment_data['prev_payment_date'];
          $sent_data['unique_id'] = $installment_data['unique_id'];

          $inserted_id = $this->loan_installment_payment_model->auto_payment($sent_data);
          $last_insert_id = $inserted_id;

          # Update Journal with  Transaction
          $transaction_data['transaction_id'] = $inserted_id;
          $transaction_data['paid_interest'] = $installment_data['paid_interest'];
          $transaction_data['paid_principal'] = $installment_data['paid_principal'];

          if ($this->input->post('payment_id') == 5) {
            $this->do_journal_transaction_multiple_installment($transaction_data, $savings_payment_data, $unique_id);
            //print_r($res);
          } else {
            $this->do_journal_transaction($transaction_data, $unique_id);
          }


          # Write Off forgiven interest
          if ($installment_data['forgiven_interest'] > 0) {
            $this->do_journal_forgive_interest($inserted_id, $installment_data['forgiven_interest'], $installment_data['repayment_date'], $installment_data['id'], $unique_id);
          }

          # Update Journal with paid penalty
          if ($installment_data['paid_penalty'] > 0) {
            $this->penalty_journal_transaction($inserted_id, $installment_data['paid_penalty'], $unique_id);
          }
        }
      } elseif (($balance > 0) && ($payable_amount == 0)) {
        // cater for installments which have only penalty left and its forgiven
        $compiled_installment_payments[] = [
          'id' => $installment_data['id'],
          'demanded_penalty' => 0,
          'payment_status' => 1,
          'actual_payment_date' => $payment_date,
          'modified_by' => (isset($_SESSION['id'])) ? $_SESSION['id'] : 1,
          'unique_id' => $unique_id
        ];
      } else {
        break; //break out of loop
      }
    }

    //end of the loop

    # Clear Installments at Once
    $this->repayment_schedule_model->clear_multiple_installment($compiled_installment_payments);

    #if more money exists and no more installments then again check 
    if ($balance > 0) {

      // if ($this->input->post('payment_id') == 5) {
      //   $this->db->trans_rollback();
      //   $response['error'] = true;
      //   $response['message'] = 'Provided Amount is Greater than Total Demanded, Please enter the exact amount or less if your payment mode is savings or Contact Admin/customer support for assistance';

      //   return $response;
      // }

      $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
      $this->data['modules'] = array_column($this->data['module_list'], "module_id");

      if ((isset($this->data['modules']) &&  (in_array('6', $this->data['modules'])) && (in_array('5', $this->data['modules'])))) { //Checking whether the company has savings module
        $this->load->model("transaction_model");

        $savings_account = $this->client_loan_model->get_member_account("a.id=" . $this->input->post('client_loan_id') . " AND d.producttype IN(1,2,3)");

        if (!empty($savings_account)) {
          $deduction_data['transaction_date'] = $this->input->post('payment_date');
          $deduction_data['account_no_id'] = $savings_account['id'];
          $deduction_data['transaction_type_id'] = 2;
          $deduction_data['amount'] = $balance;
          $deduction_data['narrative'] = $this->input->post('comment') . " Extra Amount Refunded";

          $transaction_data = $this->transaction_model->deduct_savings($deduction_data, $unique_id);

          $transaction_data['account_no_id'] = $savings_account['id'];
          $transaction_data['amount'] = $deduction_data['amount'];
          if ($this->input->post('payment_id') != 5) {
            $this->deposit_journal_transaction($transaction_data, false, $unique_id);
          }
        } else { //Since he has no savings account then 
          $this->other_income_journal_transaction_multiple_installment($last_insert_id, $balance, $unique_id);
        }
      } else { //Put money in other income
        $this->other_income_journal_transaction_multiple_installment($last_insert_id, $balance, $unique_id);
      }
    } //End of the if clause

    # Check for partial or unpaid installments again
    $unpaid_installments = $this->repayment_schedule_model->get_due_schedules("client_loan_id=" . $this->input->post('client_loan_id') . " AND payment_status IN (2,4)");

    if (count($unpaid_installments) == 0) {
      # Close Loan, installments fully paid
      $filter['client_loan_id'] = $this->input->post('client_loan_id');
      $filter['state_id'] = 10;
      $filter['comment'] = $this->input->post('comment');
      $filter['unique_id'] = $unique_id;
      $filter['date_created'] = time();
      $filter['action_date'] = $payment_date;
      $filter['created_by'] = (isset($_SESSION['id'])) ? $_SESSION['id'] : 1;

      $this->loan_state_model->set2($filter);
    }

    $response['message'] = "Multiple Installment Payment Received";
    $response['success'] = true;

    return $response;
  }

  # Single Installment Payment
  public function single_loan_installment_payment()
  {
    $this->load->model('loan_reversal_model');
    // Generate a unique_id for tracking
    $this->unique_id = $this->generate_unique_id();

    $response['message'] = "Loan amount could not be paid, contact IT support.";
    $response['success'] = FALSE;

    $this->form_validation->set_rules('payment_date', 'Payment date', array('required'));
    $this->form_validation->set_rules('totalAmount', 'Total Amount', 'required|greater_than[-1]');

    if ($this->input->post('extra_principal') != NULL) {
      $this->form_validation->set_rules("extra_principal", "Extra principal", "callback__check_remaining_balance", array("_check_remaining_balance" => "%s covers the remaining principal, Use a Payoff Form"));
    }

    if ($this->form_validation->run() === FALSE) {
      $response['message'] = validation_errors();
    } else {
      $this->db->trans_begin();

      $action_type = 1;

      if (!empty($_POST['extra_principal']) && $this->input->post('extra_principal') != NULL && $this->input->post('extra_principal') > 0) { //then restructure
        $action_type = 3;
        $this->schedule_reconstruction($this->unique_id);
      }

      if ($this->input->post('payment_id') == 5) {
        // Calculate loan amount paid
        // if (!empty($this->input->post('extra_amount'))) {
        //   $amount =  round($this->input->post('totalAmount'), 2) - round($this->input->post('extra_amount'), 2);
        // } else {
        //   $amount =  round($this->input->post('totalAmount'), 2);
        // }
        $amount =  round($this->input->post('totalAmount'), 2);

        $expected_amount = round($this->input->post('expected_total'), 2);

        // if ($amount <= $expected_amount) {
        $savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $this->input->post('savings_account_id'));
        $current_balance = $savings_data['cash_bal'];
        if ($this->input->post('extra_principal') != NULL) {
          $amount += round($this->input->post('extra_principal'), 2);
        }
        if ($current_balance >= $amount) {
          if ($this->deduct_payment_single_installment($amount, $this->unique_id)) {
            $response = $this->single_installment_payment_functions($this->unique_id);
          } else {
            $response['message'] = "There was a problem, please contact IT support";
          }
        } else {
          $response['message'] = "Insufficient balance to complete the payment";
        }
        // } else {
        //   $response['message'] = "Paid/Charged amount on Principal and Interest beyond expected";
        // }
      } else { //Payment method other than savings
        $response = $this->single_installment_payment_functions($this->unique_id);
      }

      // Post to trans_tracking table
      $trans_data = [
        'action_type_id' => $action_type,
        'payment_mode' => $this->input->post('payment_id'),
        'unique_id' => $this->unique_id,
        'client_loan_id' => $this->input->post('client_loan_id'),
        'loan_state' => $this->loan_reversal_model->get_max_loan_state(),
        'repayment_schedule_id' => $this->input->post('repayment_schedule_id'),
        'created_by' => $_SESSION['id'],
        'date_created' => date('Y-m-d h:i:s'),
        'modified_by' => $_SESSION['id'],
        'status_id' => 1
      ];

      if ($action_type == 3) { // Loan curtailment
        $trans_data['base_installment'] = $this->input->post('installment_number');
      }

      $this->loan_reversal_model->set_trans_tracking($trans_data);

      if ($this->db->trans_status()) {
        $this->db->trans_commit();
      } else {
        $this->db->trans_rollback();
        $response['message'] = "An Error happened while recording the payment. Please Try again later";
      }
    }

    echo json_encode($response);
  }

  # Single Installment Payment Functions
  public function single_installment_payment_functions($unique_id = false)
  {
    $sent_date = explode('-', $this->input->post('payment_date'), 3);
    $payment_date = count($sent_date) === 3 ? ($sent_date[2] . "-" . $sent_date[1] . "-" . $sent_date[0]) : null;

    $installment = $this->repayment_schedule_model->get("repayment_schedule.id= '{$this->input->post('repayment_schedule_id')}' ")[0];
    $data_trans = [
      'unique_id' => $unique_id,
      'prev_demanded_penalty' => $installment['demanded_penalty'],
      'prev_payment_status' => $installment['payment_status'],
      'prev_payment_date' => $installment['actual_payment_date'],
    ];

    $inserted_id = $this->loan_installment_payment_model->set($data_trans);

    if (is_numeric($inserted_id)) {
      if ($this->repayment_schedule_model->clear_single_installment($this->input->post('repayment_schedule_id'), $unique_id)) {
        $response['message'] = "Loan amount successfully paid.";
        $response['installments'] = $this->repayment_schedule_model->get('payment_status IN (2,4)  AND repayment_schedule.status_id=1');

        if ($this->input->post('paid_penalty') > 0) {
          $this->penalty_journal_transaction($inserted_id, $this->input->post('paid_penalty'), $unique_id);
        }
        # Check for forgiven Interest
        if (!empty($this->input->post('forgiven_interest'))) {

          $this->do_journal_forgive_interest($inserted_id, false, $this->input->post('payment_date'), $this->input->post('installment_number'), $unique_id);
        }

        $transaction_data['transaction_id'] = $inserted_id;
        $this->do_journal_transaction($transaction_data, $unique_id);
        // check for Extra Amount
        if (!empty($this->input->post('extra_amount')) && $this->input->post('extra_amount') > 0) {

          if (!empty($this->input->post('extra_amount_use'))) {
            $extra_amount_use = $this->input->post('extra_amount_use');

            if (intval($extra_amount_use) == 1) {
              # use Extra Amount to clear next Installment
              $this->more_money_single_installment($inserted_id, $unique_id);
            }

            if (intval($extra_amount_use) == 2) {
              # use Extra Amount as Income Earned
              $this->handle_single_installment_extra_amount($inserted_id, $unique_id);
            }
          }
        }

        // if ($this->input->post('paid_penalty') !== NULL && $this->input->post('paid_penalty') != '' && $this->input->post('paid_penalty') != '0') {
        //   $this->penalty_journal_transaction($inserted_id);
        // }

        if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] != '') {
          $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['client_loan_id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);
        } else {
          $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
        }

        $expected_payment = $this->repayment_schedule_model->sum_interest_principal($this->input->post('client_loan_id'));
        $paid_amount = $this->loan_installment_payment_model->sum_paid_installment($this->input->post('client_loan_id'));

        $message = number_format($this->input->post('paid_total'), 2) . "/= as loan payment for installment " . $this->input->post('installment_number') . " of loan number " . $this->input->post('loan_ref_no') . " has been received today " . date('d-m-Y');

        if ($paid_amount['already_paid_sum'] >= $expected_payment['total_payment']) {
          //echo "HERE";
          //$this->loan_state_model->set();
          $response['message'] = "The required Loan amount for this loan has been fully paid";
          $message = $message . " settling the whole loan";
        }

        if (!empty($this->miscellaneous_model->check_org_module(24))) {
          // $email_response = $this->helpers->send_email($this->input->post('client_loan_id'), $message);
        }


        if (!empty($result = $this->miscellaneous_model->check_org_module(22))) {
          $message = $message . ".
" . $this->organisation . ", Contact " . $this->contact_number;
          $text_response = $this->helpers->notification($this->input->post('client_loan_id'), $message);
          $response['message'] = $response['message'] . $text_response;
        }
        $response['success'] = TRUE;
      } else {
        $this->loan_installment_payment_model->delete_payment($inserted_id);
      }

      # Check for partial or unpaid installments again
      $unpaid_installments = $this->repayment_schedule_model->get_due_schedules("client_loan_id=" . $this->input->post('client_loan_id') . " AND payment_status IN (2,4)");

      if (count($unpaid_installments) == 0) {
        # Close Loan, installments fully paid
        $filter['unique_id'] = $unique_id;
        $filter['client_loan_id'] = $this->input->post('client_loan_id');
        $filter['state_id'] = 10;
        $filter['comment'] = $this->input->post('comment');
        $filter['date_created'] = time();
        $filter['action_date'] = $payment_date;
        $filter['created_by'] = (isset($_SESSION['id'])) ? $_SESSION['id'] : 1;

        $this->loan_state_model->set2($filter);

        $response['message'] = "The required Loan amount for this loan has been fully paid";
        $message = $message . " settling the whole loan";
      }
      return $response;
    }
  }

  // Deposit Extra Money to Savings
  public function handle_single_installment_extra_amount($sent_id, $unique_id = false)
  {
    $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
    $this->data['modules'] = array_column($this->data['module_list'], "module_id");

    if ((isset($this->data['modules']) &&  (in_array('6', $this->data['modules'])) && (in_array('5', $this->data['modules'])))) { //Checking whether the company has savings module
      $this->load->model("transaction_model");

      $savings_account = $this->client_loan_model->get_member_account("a.id=" . $this->input->post('client_loan_id') . " AND d.producttype IN(1,2,3)");

      if (!empty($savings_account)) {

        $deduction_data['transaction_date'] = $this->input->post('payment_date');
        $deduction_data['account_no_id'] = $savings_account['id'];
        $deduction_data['transaction_type_id'] = 2;
        $deduction_data['amount'] = $this->input->post('extra_amount');
        $deduction_data['narrative'] = $this->input->post('comment') . " Extra Amount Refunded";

        $transaction_data = $this->transaction_model->deduct_savings($deduction_data, $unique_id);

        $transaction_data['account_no_id'] = $savings_account['id'];
        $transaction_data['amount'] = $deduction_data['amount'];
        if ($this->input->post('payment_id') != 5) {
          $this->deposit_journal_transaction($transaction_data, false, $unique_id);
        }
      } else { //Since he has no savings account then 

        $this->other_income_journal_transaction_single_installment($sent_id, $unique_id);
      }
    } else { //Put money in other income
      $this->other_income_journal_transaction_single_installment($sent_id, $unique_id);
    }
  }


  //paying for a loan installment
  // public function loan_installment_repayment()
  // {
  //   $response['message'] = "Loan amount could not be paid, contact IT support.";
  //   $response['success'] = FALSE;
  //   $this->form_validation->set_rules('payment_date', 'Payment date', array('required'));
  //   if ($this->input->post('extra_principal') != NULL) {
  //     $this->form_validation->set_rules("extra_principal", "Extra principal", "callback__check_remaining_balance", array("_check_remaining_balance" => "%s covers the remaining principal, Use a Payoff Form"));
  //   }

  //   if ($this->form_validation->run() === FALSE) {
  //     $response['message'] = validation_errors();
  //   } else {

  //     $this->db->trans_begin();

  //     if ($this->input->post('extra_principal') != NULL) { //then restructure
  //       $this->schedule_reconstruction();
  //     }

  //     if ($this->input->post('payment_id') == 5) {
  //       $amount = round($this->input->post('paid_principal'), 2) + round($this->input->post('paid_interest'), 2) + round($this->input->post('paid_penalty'), 2);

  //       $expected_amount = round($this->input->post('expected_principal'), 2) + round($this->input->post('expected_interest'), 2) + round($this->input->post('expected_penalty'), 2);

  //       if ($amount <= $expected_amount) {
  //         $savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $this->input->post('savings_account_id'));
  //         $current_balance = ($savings_data['cash_bal'] - $savings_data['min_balance']);
  //         if ($this->input->post('extra_principal') != NULL) {
  //           $amount += round($this->input->post('extra_principal'), 2);
  //         }
  //         if ($current_balance >= $amount) {
  //           if ($this->deduct_payment($amount)) {
  //             $response = $this->installment_payment_functions();
  //           } else {
  //             $response['message'] = "There was a problem, please contact IT support";
  //           }
  //         } else {
  //           $response['message'] = "Insufficient balance to complete the payment";
  //         }
  //       } else {
  //         $response['message'] = "Paid/Charged amount on Principal and Interest beyond expected";
  //       }
  //     } else { //Payment method other than savings
  //       $response = $this->installment_payment_functions();
  //     }



  //     if ($this->db->trans_status()) {
  //       $this->db->trans_commit();
  //     } else {
  //       $this->db->trans_rollback();
  //       $response['message'] = "An Error happened while recording the payment. Please Try again later";
  //     }
  //   }
  //   echo json_encode($response);
  // }

  function _check_remaining_balance($extra_principal)
  {
    $loan_data = $this->repayment_schedule_model->get_loan_data($this->input->post('client_loan_id'));
    if ($this->input->post('extra_principal') >= $loan_data['amount_approved']) {
      return false;
    }
    return true;
  }

  //   public function installment_payment_functions()
  //   {
  //     $inserted_id = $this->loan_installment_payment_model->set();
  //     if (is_numeric($inserted_id)) {
  //       if ($this->repayment_schedule_model->clear_installment($this->input->post('repayment_schedule_id'))) {
  //         $response['message'] = "Loan amount successfully paid.";
  //         $response['installments'] = $this->repayment_schedule_model->get('payment_status IN (2,4)  AND repayment_schedule.status_id=1');
  //         if ($this->input->post('payment_id') != 5) {
  //           $transaction_data['transaction_id'] = $inserted_id;
  //           $this->do_journal_transaction($transaction_data);

  //           if ($this->input->post('expected_total') !== NULL && $this->input->post('paid_total') !== NULL && $this->input->post('paid_total') > $this->input->post('expected_total')) {
  //             $this->more_money($inserted_id);
  //           }
  //         }
  //         if ($this->input->post('paid_penalty') !== NULL && $this->input->post('paid_penalty') != '' && $this->input->post('paid_penalty') != '0') {
  //           $this->penalty_journal_transaction($inserted_id);
  //         }
  //         if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] != '') {
  //           $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['client_loan_id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);
  //         } else {
  //           $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
  //         }
  //         $expected_payment = $this->repayment_schedule_model->sum_interest_principal($this->input->post('client_loan_id'));
  //         $paid_amount = $this->loan_installment_payment_model->sum_paid_installment($this->input->post('client_loan_id'));

  //         $message = number_format($this->input->post('paid_total'), 2) . "/= as loan payment for installment " . $this->input->post('installment_number') . " of loan number " . $this->input->post('loan_ref_no') . " has been received today " . date('d-m-Y');

  //         if ($paid_amount['already_paid_sum'] >= $expected_payment['total_payment']) {

  //           $this->loan_state_model->set();
  //           $response['message'] = "The required Loan amount for this loan has been fully paid";
  //           $message = $message . " settling the whole loan";
  //         }

  //         $email_response = $this->helpers->send_email($this->input->post('client_loan_id'), $message);

  //         if (!empty($result = $this->miscellaneous_model->check_org_module(22))) {
  //           $message = $message . ".
  // " . $this->organisation . ", Contact " . $this->contact_number;
  //           $text_response = $this->helpers->notification($this->input->post('client_loan_id'), $message);
  //           $response['message'] = $response['message'] . $text_response;
  //         }
  //         $response['success'] = TRUE;
  //       } else {
  //         $this->loan_installment_payment_model->delete_payment($inserted_id);
  //       }
  //       return $response;
  //     }
  //   }

  private function more_money_single_installment($sent_id, $unique_id)
  {
    $installment_data = $this->repayment_schedule_model->get_due_schedules("client_loan_id=" . $this->input->post('client_loan_id') . " AND payment_status IN (2,4) AND installment_number >" . $this->input->post('installment_number'));

    $sent_date = explode('-', $this->input->post('payment_date'), 3);
    $payment_date = count($sent_date) === 3 ? ($sent_date[2] . "-" . $sent_date[1] . "-" . $sent_date[0]) : null;

    $balance = round($this->input->post('extra_amount'), 2);

    foreach ($installment_data as $key => $installment) {
      $payable_amount = ($installment['principal_amount'] + $installment['interest_amount']);
      if (($balance > 0) && ($payable_amount > 0)) {
        $sent_data['unique_id'] = $unique_id;
        $sent_data['prev_demanded_penalty'] = $installment['demanded_penalty'];
        $sent_data['prev_payment_status'] = $installment['payment_status'];
        $sent_data['prev_payment_date'] = $installment['actual_payment_date'];

        if ($balance >= $payable_amount) {
          $sent_data['paid_principal'] = $installment['principal_amount'];
          $sent_data['paid_interest'] = $installment['interest_amount'];

          $loan_payment_data['paid_total'] = $installment['principal_amount'] + $installment['interest_amount'];
          $balance = $balance - $loan_payment_data['paid_total'];
        } elseif (($installment['principal_amount'] > 0) && ($balance > $installment['principal_amount'])) {
          $sent_data['paid_principal'] = $installment['principal_amount'];
          $sent_data['paid_interest'] = ($balance - $installment['principal_amount']);

          $loan_payment_data['paid_total'] = ($installment['principal_amount'] + $sent_data['paid_interest']);
          $balance = $balance - $loan_payment_data['paid_total'];
        } elseif (($installment['principal_amount'] > 0) && ($balance <= $installment['principal_amount'])) {
          $sent_data['paid_principal'] = $balance;
          $sent_data['paid_interest'] = 0;

          $balance = $balance - $sent_data['paid_principal'];
          $loan_payment_data['paid_total'] = $sent_data['paid_principal'];
        } elseif (($installment['interest_amount'] > 0) && ($balance > $installment['interest_amount'])) {
          $sent_data['paid_interest'] = $installment['interest_amount'];
          $sent_data['paid_principal'] = 0;

          $balance = $balance - $sent_data['paid_interest'];
          $loan_payment_data['paid_total'] = $sent_data['paid_interest'];
        } elseif (($installment['interest_amount'] > 0) && ($balance <= $installment['interest_amount'])) {
          $sent_data['paid_interest'] = $balance;
          $sent_data['paid_principal'] = 0;

          $balance = $balance - $sent_data['paid_interest'];
          $loan_payment_data['paid_total'] = $sent_data['paid_interest'];
        }
        $sent_data['payment_date'] = $payment_date;
        $sent_data['repayment_schedule_id'] = $installment['id'];
        $sent_data['client_loan_id'] = $installment['client_loan_id'];
        $sent_data['comment'] = $this->input->post('comment');

        $inserted_id = $this->loan_installment_payment_model->auto_payment($sent_data, FALSE);

        $loan_payment_data['expected_total'] = ($installment['principal_amount'] + $installment['interest_amount']);
        $loan_payment_data['paid_penalty'] = 0;
        $loan_payment_data['repayment_schedule_id'] = $installment['id'];

        $this->repayment_schedule_model->clear_installment($loan_payment_data, false, $unique_id);
        $transaction_data['transaction_id'] = $inserted_id;
        $transaction_data['paid_interest'] = $sent_data['paid_interest'];
        $transaction_data['paid_principal'] = $sent_data['paid_principal'];
        $this->do_journal_transaction($transaction_data, $unique_id);
      } else {
        break; //No more money left
      }
    } //end of the loop

    #if more money exists and no more installments then again check 
    if ($balance > 0) {
      $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
      $this->data['modules'] = array_column($this->data['module_list'], "module_id");

      if ((isset($this->data['modules']) &&  (in_array('6', $this->data['modules'])) && (in_array('5', $this->data['modules'])))) { //Checking whether the company has savings module
        $this->load->model("transaction_model");

        $savings_account = $this->client_loan_model->get_member_account("a.id=" . $this->input->post('client_loan_id') . " AND d.producttype IN(1,2,3)");

        if (!empty($savings_account)) {

          $deduction_data['transaction_date'] = $this->input->post('payment_date');
          $deduction_data['account_no_id'] = $savings_account['id'];
          $deduction_data['transaction_type_id'] = 2;
          $deduction_data['amount'] = $balance;
          $deduction_data['narrative'] = $this->input->post('comment') . " Extra Amount Refunded";

          $transaction_data = $this->transaction_model->deduct_savings($deduction_data, $unique_id);

          $transaction_data['account_no_id'] = $savings_account['id'];
          $transaction_data['amount'] = $deduction_data['amount'];
          if ($this->input->post('payment_id') != 5) {
            $this->deposit_journal_transaction($transaction_data, false, $unique_id);
          }
        } else { //Since he has no savings account then 

          $this->other_income_journal_transaction_single_installment($sent_id, $unique_id);
        }
      } else { //Put money in other income
        $this->other_income_journal_transaction_single_installment($sent_id, $unique_id);
      }
    } //End of the if clause

  }

  // private function more_money($sent_id)
  // {
  //   $installment_data = $this->repayment_schedule_model->get2("client_loan_id=" . $this->input->post('client_loan_id') . " AND payment_status IN (2,4) AND installment_number >" . $this->input->post('installment_number'));

  //   $sent_date = explode('-', $this->input->post('payment_date'), 3);
  //   $payment_date = count($sent_date) === 3 ? ($sent_date[2] . "-" . $sent_date[1] . "-" . $sent_date[0]) : null;

  //   $paid_amount = round($this->input->post('paid_principal'), 2) + round($this->input->post('paid_interest'), 2) + round($this->input->post('paid_penalty'), 2);

  //   $expected_amount = round($this->input->post('expected_principal'), 2) + round($this->input->post('expected_interest'), 2) + round($this->input->post('expected_penalty'), 2);

  //   $balance = round(($paid_amount - $expected_amount), 2);

  //   foreach ($installment_data as $key => $installment) {
  //     $payable_amount = ($installment['principal_amount'] + $installment['interest_amount']);
  //     if (($balance > 0) && ($payable_amount > 0)) {
  //       if ($balance >= $payable_amount) {
  //         $sent_data['paid_principal'] = $installment['principal_amount'];
  //         $sent_data['paid_interest'] = $installment['interest_amount'];

  //         $loan_payment_data['paid_total'] = $installment['principal_amount'] + $installment['interest_amount'];
  //         $balance = $balance - $loan_payment_data['paid_total'];
  //       } elseif (($installment['principal_amount'] > 0) && ($balance > $installment['principal_amount'])) {
  //         $sent_data['paid_principal'] = $installment['principal_amount'];
  //         $sent_data['paid_interest'] = ($balance - $installment['principal_amount']);

  //         $loan_payment_data['paid_total'] = ($installment['principal_amount'] + $sent_data['paid_interest']);
  //         $balance = $balance - $loan_payment_data['paid_total'];
  //       } elseif (($installment['principal_amount'] > 0) && ($balance <= $installment['principal_amount'])) {
  //         $sent_data['paid_principal'] = $balance;
  //         $sent_data['paid_interest'] = 0;

  //         $balance = $balance - $sent_data['paid_principal'];
  //         $loan_payment_data['paid_total'] = $sent_data['paid_principal'];
  //       } elseif (($installment['interest_amount'] > 0) && ($balance > $installment['interest_amount'])) {
  //         $sent_data['paid_interest'] = $installment['interest_amount'];
  //         $sent_data['paid_principal'] = 0;

  //         $balance = $balance - $sent_data['paid_interest'];
  //         $loan_payment_data['paid_total'] = $sent_data['paid_interest'];
  //       } elseif (($installment['interest_amount'] > 0) && ($balance <= $installment['interest_amount'])) {
  //         $sent_data['paid_interest'] = $balance;
  //         $sent_data['paid_principal'] = 0;

  //         $balance = $balance - $sent_data['paid_interest'];
  //         $loan_payment_data['paid_total'] = $sent_data['paid_interest'];
  //       }
  //       $sent_data['payment_date'] = $payment_date;
  //       $sent_data['repayment_schedule_id'] = $installment['id'];
  //       $sent_data['client_loan_id'] = $installment['client_loan_id'];
  //       $sent_data['comment'] = $this->input->post('comment');

  //       $inserted_id = $this->loan_installment_payment_model->auto_payment($sent_data);

  //       $loan_payment_data['expected_total'] = ($installment['principal_amount'] + $installment['interest_amount']);
  //       $loan_payment_data['paid_penalty'] = 0;
  //       $loan_payment_data['repayment_schedule_id'] = $installment['id'];

  //       $this->repayment_schedule_model->clear_installment($loan_payment_data);
  //       $transaction_data['transaction_id'] = $inserted_id;
  //       $transaction_data['paid_interest'] = $sent_data['paid_interest'];
  //       $transaction_data['paid_principal'] = $sent_data['paid_principal'];
  //       $this->do_journal_transaction($transaction_data);
  //     } else {
  //       break; //No more money left
  //     }
  //   } //end of the loop

  //   #if more money exists and no more installments then again check 
  //   if ($balance > 0) {
  //     $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
  //     $this->data['modules'] = array_column($this->data['module_list'], "module_id");

  //     if ((isset($this->data['modules']) &&  (in_array('6', $this->data['modules'])) && (in_array('5', $this->data['modules'])))) { //Checking whether the company has savings module
  //       $this->load->model("transaction_model");

  //       $savings_account = $this->client_loan_model->get_member_account("a.id=" . $this->input->post('client_loan_id') . " AND d.producttype=2");


  //       if (!empty($savings_account)) {
  //         $deduction_data['transaction_date'] = $this->input->post('payment_date');
  //         $deduction_data['account_no_id'] = $savings_account['id'];
  //         $deduction_data['transaction_type_id'] = 2;
  //         $deduction_data['amount'] = $balance;
  //         $deduction_data['narrative'] = $this->input->post('comment');

  //         $transaction_data = $this->transaction_model->deduct_savings($deduction_data);

  //         $transaction_data['account_no_id'] = $savings_account['id'];
  //         $transaction_data['amount'] = $deduction_data['amount'];
  //         $this->deposit_journal_transaction($transaction_data);
  //       } else { //Since he has no savings account then 
  //         $this->other_income_journal_transaction($sent_id);
  //       }
  //     } else { //Put money in other income
  //       $this->other_income_journal_transaction($sent_id);
  //     }
  //   } //End of the if clause

  // }


  private function deduct_payment_multiple_installment($amount, $unique_id = false)
  {
    $deduction_data['amount'] = $amount;
    $deduction_data['account_no_id'] = $this->input->post('savings_account_id');
    $deduction_data['transaction_date'] = $this->input->post('payment_date');
    $deduction_data['narrative'] = $this->input->post('comment') . ' { Payment made to clear your loan ( ' . ucfirst($this->input->post('loan_no')) . " ) }";
    $transaction_data = $this->transaction_model->deduct_savings($deduction_data, $unique_id);

    return $transaction_data;
  }

  private function deduct_payment_single_installment($amount, $unique_id = false)
  {
    $deduction_data['amount'] = $amount;
    $deduction_data['account_no_id'] = $this->input->post('savings_account_id');
    $deduction_data['transaction_date'] = $this->input->post('payment_date');
    $deduction_data['narrative'] = $this->input->post('comment') . ' { Payment made to clear your loan ( ' . ucfirst($this->input->post('loan_no')) . " ) }";
    $transaction_data = $this->transaction_model->deduct_savings($deduction_data, $unique_id);

    return $transaction_data;
  }

  private function deduct_payment($amount, $unique_id = false)
  {
    $update = false;
    $deduction_data['unique_id'] = $unique_id;
    $deduction_data['amount'] = $amount;
    $deduction_data['account_no_id'] = $this->input->post('savings_account_id');
    $deduction_data['transaction_date'] = $this->input->post('payment_date');
    $deduction_data['narrative'] = $this->input->post('comment') . ' { Payment made to clear your loan ( ' . ucfirst($this->input->post('loan_no')) . " ) }";
    $transaction_data = $this->transaction_model->deduct_savings($deduction_data, $unique_id);
    if (is_array($transaction_data)) {
      //then we prepare the journal transaction lines
      $savings_account = $this->savings_account_model->get_savings_account($this->input->post('savings_account_id'));

      $data = [
        'transaction_date' => $this->input->post('payment_date'),
        'description' => "Loan payment { " . $this->input->post('loan_no') . " } " . $this->input->post('narrative'),
        'ref_no' => $savings_account['account_no'],
        'ref_id' => $transaction_data['transaction_no'],
        'status_id' => 1,
        'journal_type_id' => 6,
        'unique_id' => $unique_id
      ];
      //then we post this to the journal transaction
      $journal_transaction_id = $this->journal_transaction_model->set($data);
      unset($data);

      $un_paid_interest = round($this->input->post('un_paid_interest'), 2);

      if ($this->input->post('expected_principal') != NULL && ($this->input->post('paid_principal') > $this->input->post('expected_principal'))) {
        $principal_amount = round($this->input->post('expected_principal'), 2);
      } else {
        $principal_amount = round($this->input->post('paid_principal'), 2);
      }

      if ($this->input->post('extra_principal') != NULL) {
        $principal_amount += round($this->input->post('extra_principal'), 2);
      }
      if ($this->input->post('expected_interest') != NULL && ($this->input->post('paid_interest') > $this->input->post('expected_interest'))) {
        $interest_amount = round($this->input->post('expected_interest'), 2);
      } else {
        $interest_amount = round($this->input->post('paid_interest'), 2);
      }



      $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);
      $debit_or_credit1 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);

      $client_loan = $this->client_loan_model->get_client_data($this->input->post('client_loan_id'));
      $loan_product_details = $this->loan_product_model->get_accounts($client_loan['loan_product_id']);
      $debit_or_credit2 =  $this->accounts_model->get_normal_side($loan_product_details['loan_receivable_account_id'], true);
      $debit_or_credit3 = $this->accounts_model->get_normal_side($loan_product_details['interest_receivable_account_id'], true);
      $debit_or_credit4 = $this->accounts_model->get_normal_side($loan_product_details['interest_income_account_id'], true);


      //if principal has been received
      if ($principal_amount != null && !empty($principal_amount) && $principal_amount != '0') {
        $data[0] = [
          'reference_no' => $savings_account['account_no'],
          'reference_id' => $transaction_data['transaction_no'],
          'transaction_date' => $this->input->post('payment_date'),
          $debit_or_credit1 => $principal_amount,
          'narrative' => strtoupper("Loan principal payment on [ " . $this->input->post('loan_no') . " ] " . $this->input->post('payment_date')),
          'account_id' => $savings_product_details['savings_liability_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
        $data[1] = [
          'reference_no' => $savings_account['account_no'],
          'reference_id' => $transaction_data['transaction_no'],
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit2 => $principal_amount,
          'narrative' => strtoupper("Loan principal payment on [ " . $this->input->post('loan_no') . " ] " . $this->input->post('payment_date')),
          'account_id' => $loan_product_details['loan_receivable_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
      }

      //if interest has been received
      if ($interest_amount != null && !empty($interest_amount) && $interest_amount != '0') {
        $data[2] = [
          'reference_no' => $savings_account['account_no'],
          'reference_id' => $transaction_data['transaction_no'],
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit3 => $interest_amount,
          'narrative' => strtoupper("Loan interest payment on [ " . $this->input->post('loan_no') . " ] " . $this->input->post('payment_date')),
          'account_id' => $loan_product_details['interest_receivable_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
        $data[3] = [
          'reference_no' => $savings_account['account_no'],
          'reference_id' => $transaction_data['transaction_no'],
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit4 => $interest_amount,
          'narrative' => strtoupper("Loan interest payment on [ " . $this->input->post('loan_no') . " ] " . $this->input->post('payment_date')),
          'account_id' => $savings_product_details['savings_liability_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
      }

      //if some interest is not going to be received normally during pay off
      if ($un_paid_interest != null && !empty($un_paid_interest) &&  $un_paid_interest != '0') {
        $installment = $this->repayment_schedule_model->get_payoff_installment($this->input->post('client_loan_id'));

        $where_clause = "reference_id >" . $installment[0]['id'] . " AND reference_no='" . $this->input->post('loan_ref_no') . "'";
        $line_data['status_id'] = 3;
        $line_data['unique_id'] = $unique_id;

        $this->journal_transaction_line_model->update_status($line_data, $where_clause);
      }

      if ($this->journal_transaction_line_model->set($journal_transaction_id, $data)) {
        $update = true;
        $message = "Payment of amount " . number_format($amount, 2) . "/= has been made from your account " . $savings_account['account_no'] . " today " . date('d-m-Y H:i:s');
        
        if (!empty($this->miscellaneous_model->check_org_module(24))) {
          //$this->helpers->send_email($this->input->post('savings_account_id'),$message,false);
        }
        
        #check for the sms module
        if (!empty($result = $this->miscellaneous_model->check_org_module(22, 1))) {
          $message = $message . ".
" . $this->organisation . ", Contact " . $this->contact_number;
          $this->helpers->notification($this->input->post('savings_account_id'),$message,false);
        }
      }
    }

    if ($update) {
      return true;
    } else {
      return false;
    }
  }
  //paying off a loan
  public function pay_off()
  {
    // activity log pay off.
    $response['message'] = "Loan amount could not be paid off, contact IT support.";
    $response['success'] = FALSE;
    $this->db->trans_begin();

    # Find Partially paid Installment or Pending Payment Installment
    $installment_data = $this->repayment_schedule_model->get2("client_loan_id=" . $this->input->post('client_loan_id') . " AND payment_status IN (2,4,5)")[0];

    $unique_id = $this->generate_unique_id();

    $trans_tracking_data = [
      'unique_id' => $unique_id,
      'client_loan_id' => $this->input->post('client_loan_id'),
      'action_type_id' => 4,
      'payment_mode' => $this->input->post('payment_id'),
      'loan_state	' => $this->loan_reversal_model->get_max_loan_state(),
      'payment_status' => $installment_data['payment_status'],
      'repayment_schedule_id' => $installment_data['id'],
      'created_by' => $_SESSION['id'],
      'date_created' => date('Y-m-d h:i:s'),
      'date_modified' => date('Y-m-d h:i:s'),
      'modified_by' => $_SESSION['id'],
      'status_id' => 1
    ];
    #Update trans_tracking Table
    $this->loan_reversal_model->set_trans_tracking($trans_tracking_data);

    # START: Compile Data used for loan installment payment table trans reversals
    $data_trans['prev_payment_status'] = $installment_data['payment_status'];
    $data_trans['prev_demanded_penalty'] = $installment_data['demanded_penalty'];
    $data_trans['prev_payment_date'] = $installment_data['actual_payment_date'];
    $data_trans['unique_id'] = $unique_id;
    # END: Compile Data used for loan installment payment table trans reversals


    //record payment
    if ($this->input->post('payment_id') == 5) {
      $amount = round($this->input->post('paid_total'), 2);
      $savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $this->input->post('savings_account_id'));
      $current_balance = $savings_data['cash_bal'];
      if ($current_balance >= $amount) {
        if ($this->deduct_payment($amount, $unique_id)) {
          $response = $this->pay_off_functions($data_trans, $unique_id);
        } else {
          $response['message'] = "There was a problem, please contact IT support";

          $this->helpers->activity_logs($_SESSION['id'], 4, "Pay off loan", $response['message'] . " -# " .
            $savings_data, NULL, $savings_data);
        }
      } else {
        $response['message'] = "Insufficient balance to complete the payment";
        $this->helpers->activity_logs($_SESSION['id'], 4, "Pay off loan", $response['message'] . " -# " .
          $savings_data, NULL, $savings_data);
      }
    } else {
      $response = $this->pay_off_functions($data_trans, $unique_id);
    }

    if ($this->db->trans_status()) {
      $this->db->trans_commit();
    } else {
      $this->db->trans_rollback();
      $response['message'] = "An Error happened while recording the payment. Please Try again later";

      $this->helpers->activity_logs($_SESSION['id'], 4, "Pay off loan", $response['message'] . " -# " . $this->input->post('savings_account_id'), NULL, $this->input->post('savings_account_id'));
    }
    echo json_encode($response);
  }

  public function pay_off_functions($data_trans = false, $unique_id = false)
  {
    $this->load->model('applied_loan_fee_model');
    $unique_id = isset($data_trans['unique_id']) ? $data_trans['unique_id'] : $unique_id;

    #adding loan fees for this loan
    if ($this->input->post('loanFees') != NULL && $this->input->post('loanFees') != '') {

      $loanFees = $this->input->post('loanFees');
      foreach ($loanFees as $key => $value) { //it is a new entry, so we insert afresh

        if (isset($value['remove_or_not'])) {
          unset($value['remove_or_not']);

          $value['unique_id'] = $unique_id;
          $value['date_created'] = time();
          $value['client_loan_id'] = $this->input->post('client_loan_id');
          $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
          $this->applied_loan_fee_model->set2($value);
        }
      }

      if ($this->input->post('payment_id') == 5) {
        $savings_account = array();
        $savings_account['saving_account_id'] = $this->input->post('savings_account_id');
        $this->helpers->deduct_charges($_POST['client_loan_id'], array('7'), $savings_account, false, $unique_id);
      } else {

        $this->do_journal_transaction_loan_fees($this->input->post('payment_date'), $this->input->post('client_loan_id'), $unique_id);
      }
    }


    $inserted_id = $this->loan_installment_payment_model->set($data_trans);

    $response = [];
    if (is_numeric($inserted_id)) {
      //change the state of the loan
      $inserted_state_id = $this->loan_state_model->set(false, $unique_id);
      if (is_numeric($inserted_state_id)) {
        //clear the installment
        if ($this->repayment_schedule_model->clear_installment($this->input->post('client_loan_id'), 'payoff', $unique_id)) {
          $response['success'] = TRUE;
          $response['message'] = "Loan amount successfully paid off.";
          $response['installments'] = $this->repayment_schedule_model->get('payment_status <> 1 AND repayment_schedule.status_id=1');
          if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] != '') {
            $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['client_loan_id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);
          } else {
            $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
          }
          //accounting concept on pay off and specifically when not using savings
          if ($this->input->post('payment_id') != 5) {
            $transaction_data['transaction_id'] = $inserted_id;
            $this->do_journal_transaction($transaction_data, $unique_id);
            if ($this->input->post('expected_total') !== NULL && $this->input->post('paid_total') !== NULL && $this->input->post('paid_total') > $this->input->post('expected_total')) {
              $this->other_income_journal_transaction($inserted_id, $unique_id);
            }
          }

          if ($this->input->post('paid_penalty') !== NULL && $this->input->post('paid_penalty') != '' && $this->input->post('paid_penalty') != '0') {
            $this->penalty_journal_transaction($inserted_id, false, $unique_id);
          }
        } else {
          $this->loan_installment_payment_model->delete_payment($inserted_id);
          $this->loan_state_model->delete_by_id($inserted_state_id);
        }
        $message = number_format($this->input->post('paid_total'), 2) . "/= as loan payment for loan number " . $this->input->post('loan_ref_no') . " has been received today " . date('d-m-Y') . ". Thanks";
        if (!empty($result = $this->miscellaneous_model->check_org_module(22))) {
          $message = $message . ".
" . $this->organisation . ", Contact " . $this->contact_number;
          $text_response = $this->helpers->notification($this->input->post('client_loan_id'), $message);
          $response['message'] = $response['message'] . $text_response;
        }
        $email_response = $this->helpers->send_email($this->input->post('client_loan_id'), $message);
      } else {
        $this->loan_installment_payment_model->delete_payment($inserted_id);
      }
      return $response;
    }
  }

  private function do_journal_transaction_multiple_installment($transaction_data, $savings_payment_data, $unique_id = false)
  {

    $client_loan = $this->client_loan_model->get_client_data($this->input->post('client_loan_id'));

    if (array_key_exists('paid_principal', $transaction_data)) {
      $principal_amount = round($transaction_data['paid_principal'], 2);
    } else {
      if ($this->input->post('expected_principal') != NULL && ($this->input->post('paid_principal') > $this->input->post('expected_principal'))) {
        $principal_amount = round($this->input->post('expected_principal'), 2);
      } else {
        $principal_amount = round($this->input->post('paid_principal'), 2);
      }
      if ($this->input->post('extra_principal') != NULL) {
        $principal_amount += round($this->input->post('extra_principal'), 2);
      }
    }

    if (array_key_exists('paid_interest', $transaction_data)) {
      $interest_amount = round($transaction_data['paid_interest'], 2);
    } else {
      if ($this->input->post('expected_interest') != NULL && ($this->input->post('paid_interest') > $this->input->post('expected_interest'))) {
        $interest_amount = round($this->input->post('expected_interest'), 2);
      } else {
        $interest_amount = round($this->input->post('paid_interest'), 2);
      }
    }

    // $un_paid_interest = round($this->input->post('un_paid_interest'), 2);
    //then we prepare the journal transaction lines
    if (!empty($client_loan)) {
      $data = [
        'transaction_date' => $this->input->post('payment_date'),
        'description' => strtoupper($this->input->post('comment')),
        'ref_no' => $this->input->post('loan_ref_no'),
        'ref_id' => $savings_payment_data['transaction_no'],
        'status_id' => 1,
        'journal_type_id' => 6,
        'unique_id' => $unique_id
      ];
      //then we post this to the journal transaction
      $journal_transaction_id = $this->journal_transaction_model->set($data);
      unset($data);

      $savings_account = $this->savings_account_model->get_savings_account($this->input->post('savings_account_id'));

      $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);
      $debit_or_credit3 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);


      //$transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
      $loan_product_details = $this->loan_product_model->get_accounts($client_loan['loan_product_id']);

      $debit_or_credit1 =  $this->accounts_model->get_normal_side($loan_product_details['loan_receivable_account_id'], true);

      //$debit_or_credit3 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);

      $debit_or_credit4 = $this->accounts_model->get_normal_side($loan_product_details['interest_receivable_account_id'], true);
      // $debit_or_credit5 = $this->accounts_model->get_normal_side($loan_product_details['interest_income_account_id'], true);

      //if principal has been received
      if ($principal_amount != null && !empty($principal_amount) && $principal_amount != '0') {
        $data[0] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $savings_payment_data['transaction_no'],
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit1 => $principal_amount,
          'narrative' => strtoupper("Loan principal payment on " . $this->input->post('payment_date')),
          'account_id' => $loan_product_details['loan_receivable_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
        $data[1] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $savings_payment_data['transaction_no'],
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit3 => $principal_amount,
          'narrative' => strtoupper("Loan principal payment on " . $this->input->post('payment_date')),
          'account_id' => $savings_product_details['savings_liability_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
      }

      //if interest has been received
      if ($interest_amount != null && !empty($interest_amount) && $interest_amount != '0') {
        $data[2] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $savings_payment_data['transaction_no'],
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit4 => $interest_amount,
          'narrative' => strtoupper("Loan interest payment on " . $this->input->post('payment_date')),
          'account_id' => $loan_product_details['interest_receivable_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
        $data[3] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $transaction_data['transaction_id'],
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit3 => $interest_amount,
          'narrative' => strtoupper("Loan interest payment on " . $this->input->post('payment_date')),
          'account_id' => $savings_product_details['savings_liability_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
      }

      //if some interest is not going to be received normally during pay off
      /* if ($un_paid_interest != null && !empty($un_paid_interest) &&  $un_paid_interest != '0') {

        $installment = $this->repayment_schedule_model->get_payoff_installment($this->input->post('client_loan_id'));
        $where_clause = "reference_id >" . $installment[0]['id'] . " AND reference_no='" . $this->input->post('loan_ref_no') . "'";
        $line_data['status_id'] = 3;

        $this->journal_transaction_line_model->update_status($line_data, $where_clause);
      } */

      if (!empty($data)) {
        $this->journal_transaction_line_model->set($journal_transaction_id, $data);
      }
    }
  }
  private function do_journal_transaction($transaction_data, $unique_id = false)
  {

    $client_loan = $this->client_loan_model->get_client_data($this->input->post('client_loan_id'));

    if (array_key_exists('paid_principal', $transaction_data)) {
      $principal_amount = $transaction_data['paid_principal'];
    } else {
      if ($this->input->post('expected_principal') != NULL && ($this->input->post('paid_principal') > $this->input->post('expected_principal'))) {
        $principal_amount = round($this->input->post('expected_principal'), 2);
      } else {
        $principal_amount = round($this->input->post('paid_principal'), 2);
      }
      if ($this->input->post('extra_principal') != NULL) {
        $principal_amount += round($this->input->post('extra_principal'), 2);
      }
    }

    if (array_key_exists('paid_interest', $transaction_data)) {
      $interest_amount = $transaction_data['paid_interest'];
    } else {
      if ($this->input->post('expected_interest') != NULL && ($this->input->post('paid_interest') > $this->input->post('expected_interest'))) {
        $interest_amount = round($this->input->post('expected_interest'), 2);
      } else {
        $interest_amount = round($this->input->post('paid_interest'), 2);
      }
    }

    $un_paid_interest = round($this->input->post('un_paid_interest'), 2);
    //then we prepare the journal transaction lines


    if (!empty($client_loan)) {
      $data = [
        'transaction_date' => $this->input->post('payment_date'),
        'description' => strtoupper($this->input->post('comment')),
        'ref_no' => $this->input->post('loan_ref_no'),
        'ref_id' => $transaction_data['transaction_id'],
        'status_id' => 1,
        'journal_type_id' => 6,
        'unique_id' => $unique_id,
      ];
      //then we post this to the journal transaction
      $journal_transaction_id = $this->journal_transaction_model->set($data);
      unset($data);
      if ($this->input->post('payment_id') == 5) {
        $savings_account = $this->savings_account_model->get_savings_account($this->input->post('savings_account_id'));
        $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);
        $debit_or_credit3 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);
        $linked_account_id = $savings_product_details['savings_liability_account_id'];
      } else {
        $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
        $debit_or_credit3 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);
        $linked_account_id = $transaction_channel['linked_account_id'];
      }
      $loan_product_details = $this->loan_product_model->get_accounts($client_loan['loan_product_id']);

      $debit_or_credit1 =  $this->accounts_model->get_normal_side($loan_product_details['loan_receivable_account_id'], true);

      $debit_or_credit4 = $this->accounts_model->get_normal_side($loan_product_details['interest_receivable_account_id'], true);
      $debit_or_credit5 = $this->accounts_model->get_normal_side($loan_product_details['interest_income_account_id'], true);

      //if principal has been received
      if ($principal_amount != null && !empty($principal_amount) && $principal_amount != '0') {
        $data[0] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $transaction_data['transaction_id'],
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit1 => $principal_amount,
          'narrative' => strtoupper("Loan principal payment on " . $this->input->post('payment_date')),
          'account_id' => $loan_product_details['loan_receivable_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id,
        ];
        $data[1] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $transaction_data['transaction_id'],
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit3 => $principal_amount,
          'narrative' => strtoupper("Loan principal payment on " . $this->input->post('payment_date')),
          'account_id' => $linked_account_id,
          'status_id' => 1,
          'unique_id' => $unique_id,
        ];
      }

      //if interest has been received
      if ($interest_amount != null && !empty($interest_amount) && $interest_amount != '0') {
        $data[2] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $transaction_data['transaction_id'],
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit4 => $interest_amount,
          'narrative' => strtoupper("Loan interest payment on " . $this->input->post('payment_date')),
          'account_id' => $loan_product_details['interest_receivable_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id,
        ];
        $data[3] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $transaction_data['transaction_id'],
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit3 => $interest_amount,
          'narrative' => strtoupper("Loan interest payment on " . $this->input->post('payment_date')),
          'account_id' => $linked_account_id,
          'status_id' => 1,
          'unique_id' => $unique_id,
        ];
      }

      //if some interest is not going to be received normally during pay off
      if ($un_paid_interest != null && !empty($un_paid_interest) &&  $un_paid_interest != '0') {

        $installment = $this->repayment_schedule_model->get_payoff_installment($this->input->post('client_loan_id'));
        //print_r($installment);die();
        $where_clause = "reference_id >" . $installment[0]['id'] . " AND reference_no='" . $this->input->post('loan_ref_no') . "'";
        $line_data['status_id'] = 3;
        $line_data['unique_id'] = $unique_id;

        $this->journal_transaction_line_model->update_status($line_data, $where_clause);
      }

      if (!empty($data)) {
        $this->journal_transaction_line_model->set($journal_transaction_id, $data);
      }
    }
  }


  private function penalty_journal_transaction($transaction_id, $penalty_amount = false, $unique_id = false)
  {
    $client_loan = $this->client_loan_model->get_client_data($this->input->post('client_loan_id'));
    $penalty_amount =  $penalty_amount ? $penalty_amount : round($this->input->post('paid_penalty'), 2);
    //then we prepare the journal transaction lines
    if (!empty($client_loan) && $penalty_amount != null && !empty($penalty_amount) && $penalty_amount != '0') {
      $data = [
        'transaction_date' => $this->input->post('payment_date'),
        'description' => strtoupper($this->input->post('comment')),
        'ref_no' => $this->input->post('loan_ref_no'),
        'ref_id' => $transaction_id,
        'status_id' => 1,
        'journal_type_id' => 5,
        'unique_id' => $unique_id
      ];
      //then we post this to the journal transaction
      $journal_transaction_id = $this->journal_transaction_model->set($data);
      unset($data);

      $loan_product_details = $this->loan_product_model->get_accounts($client_loan['loan_product_id']);

      $debit_or_credit2 = $this->accounts_model->get_normal_side($loan_product_details['penalty_income_account_id']);
      if ($this->input->post('payment_id') != 5) {
        $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
        $debit_or_credit3 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);
        $linked_account_id = $transaction_channel['linked_account_id'];
      } else {
        $savings_account = $this->savings_account_model->get_savings_account($this->input->post('savings_account_id'));
        $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);
        $debit_or_credit3 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);
        $linked_account_id = $savings_product_details['savings_liability_account_id'];
      }
      //if penalty has been recieved
      if ($penalty_amount != null && !empty($penalty_amount) && $penalty_amount != '0') {
        $data[0] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $transaction_id,
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit2 => $penalty_amount,
          'narrative' => strtoupper("Loan penalty payment on " . $this->input->post('payment_date')),
          'account_id' => $loan_product_details['penalty_income_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
        $data[1] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $transaction_id,
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit3 => $penalty_amount,
          'narrative' => strtoupper("Loan penalty payment on " . $this->input->post('payment_date')),
          'account_id' => $linked_account_id,
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
      }
      $this->journal_transaction_line_model->set($journal_transaction_id, $data);
    }
  }

  private function other_income_journal_transaction_single_installment($transaction_id, $unique_id = false)
  {

    $client_loan = $this->client_loan_model->get_client_data($this->input->post('client_loan_id'));
    $expected_total = round($this->input->post('expected_total'), 2);
    $paid_total = round($this->input->post('totalAmount'), 2);
    //then we prepare the journal transaction lines
    if (!empty($client_loan) && $paid_total > $expected_total) {
      $data = [
        'transaction_date' => $this->input->post('payment_date'),
        'description' => strtoupper($this->input->post('comment')),
        'ref_no' => $this->input->post('loan_ref_no'),
        'ref_id' => $transaction_id,
        'status_id' => 1,
        'journal_type_id' => 14,
        'unique_id' => $unique_id
      ];
      //then we post this to the journal transaction
      $journal_transaction_id = $this->journal_transaction_model->set($data);
      unset($data);

      $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
      $loan_product_details = $this->loan_product_model->get_accounts($client_loan['loan_product_id']);

      $debit_or_credit3 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);
      $debit_or_credit6 = $this->accounts_model->get_normal_side($loan_product_details['miscellaneous_account_id']);

      if ($paid_total > $expected_total) {
        $data[0] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $transaction_id,
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit6 => ($paid_total - $expected_total),
          'narrative' => strtoupper("Received amount over above the expected on loan payment made on " . $this->input->post('payment_date')),
          'account_id' => $loan_product_details['miscellaneous_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
        $data[1] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $transaction_id,
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit3 => ($paid_total - $expected_total),
          'narrative' => strtoupper("Received amount over above the expected on loan payment made on " . $this->input->post('payment_date')),
          'account_id' => $transaction_channel['linked_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
      }
      $this->journal_transaction_line_model->set($journal_transaction_id, $data);
    }
  }

  private function other_income_journal_transaction_multiple_installment($transaction_id, $balance, $unique_id = false)
  {
    //then we prepare the journal transaction lines
    $data = [
      'transaction_date' => $this->input->post('payment_date'),
      'description' => strtoupper($this->input->post('comment')),
      'ref_no' => $this->input->post('loan_ref_no'),
      'ref_id' => $transaction_id,
      'status_id' => 1,
      'journal_type_id' => 14,
      'unique_id' => $unique_id
    ];
    //then we post this to the journal transaction
    $journal_transaction_id = $this->journal_transaction_model->set($data);
    unset($data);

    $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
    $loan_product_details = $this->loan_product_model->get_accounts($this->input->post('loan_product_id'));

    $debit_or_credit3 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);
    $debit_or_credit6 = $this->accounts_model->get_normal_side($loan_product_details['miscellaneous_account_id']);

    $data[0] = [
      'reference_no' => $this->input->post('loan_ref_no'),
      'reference_id' => $transaction_id,
      'transaction_date' => $this->input->post('payment_date'),
      'member_id' => $this->input->post('member_id'),
      'reference_key' => $this->input->post('loan_ref_no'),
      $debit_or_credit6 => $balance,
      'narrative' => strtoupper("Received amount over above the expected on loan payment made on " . $this->input->post('payment_date')),
      'account_id' => $loan_product_details['miscellaneous_account_id'],
      'status_id' => 1,
      'unique_id' => $unique_id
    ];
    $data[1] = [
      'reference_no' => $this->input->post('loan_ref_no'),
      'reference_id' => $transaction_id,
      'transaction_date' => $this->input->post('payment_date'),
      'member_id' => $this->input->post('member_id'),
      'reference_key' => $this->input->post('loan_ref_no'),
      $debit_or_credit3 => $balance,
      'narrative' => strtoupper("Received amount over above the expected on loan payment made on " . $this->input->post('payment_date')),
      'account_id' => $transaction_channel['linked_account_id'],
      'status_id' => 1,
      'unique_id' => $unique_id
    ];
    $this->journal_transaction_line_model->set($journal_transaction_id, $data);
  }

  private function other_income_journal_transaction($transaction_id, $unique_id = false)
  {

    $client_loan = $this->client_loan_model->get_client_data($this->input->post('client_loan_id'));
    $expected_total = round($this->input->post('expected_total'), 2);
    $paid_total = round($this->input->post('paid_total'), 2);
    //then we prepare the journal transaction lines
    if (!empty($client_loan) && $paid_total > $expected_total) {
      $data = [
        'transaction_date' => $this->input->post('payment_date'),
        'description' => strtoupper($this->input->post('comment')),
        'ref_no' => $this->input->post('loan_ref_no'),
        'ref_id' => $transaction_id,
        'status_id' => 1,
        'journal_type_id' => 14,
        'unique_id' => $unique_id
      ];
      //then we post this to the journal transaction
      $journal_transaction_id = $this->journal_transaction_model->set($data);
      unset($data);

      $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
      $loan_product_details = $this->loan_product_model->get_accounts($client_loan['loan_product_id']);

      $debit_or_credit3 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);
      $debit_or_credit6 = $this->accounts_model->get_normal_side($loan_product_details['miscellaneous_account_id']);

      if ($paid_total > $expected_total) {
        $data[0] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $transaction_id,
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit6 => ($paid_total - $expected_total),
          'narrative' => strtoupper("Received amount over above the expected on loan payment made on " . $this->input->post('payment_date')),
          'account_id' => $loan_product_details['miscellaneous_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
        $data[1] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $transaction_id,
          'transaction_date' => $this->input->post('payment_date'),
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit3 => ($paid_total - $expected_total),
          'narrative' => strtoupper("Received amount over above the expected on loan payment made on " . $this->input->post('payment_date')),
          'account_id' => $transaction_channel['linked_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
      }
      $this->journal_transaction_line_model->set($journal_transaction_id, $data);
    }
  }
  #deposit journal entry function
  private function deposit_journal_transaction($transaction_data, $charges = false, $unique_id = false)
  {

    if ($this->input->post('payment_date') != NULL && $this->input->post('payment_date') != '') {
      $date = $this->input->post('payment_date');
    } else {
      $date = date('d-m-Y');
    }
    $deposit_amount = $transaction_data['amount'];

    if (is_numeric($this->input->post('account_no_id'))) {
      $account_no_id = $this->input->post('account_no_id');
    } else {
      $account_no_id = $transaction_data['account_no_id'];
    }
    $savings_account = $this->savings_account_model->get_savings_acc_details($account_no_id);
    // print_r($savings_account); die;

    if ($this->input->post('comment') != NULL && $this->input->post('comment') != '') {
      $narrative = $this->input->post('comment');
    } elseif ($this->input->post('narrative') != NULL && $this->input->post('narrative') != '') {
      $narrative = $this->input->post('narrative');
    } else {
      $narrative = "Member deposit";
    }
    //then we prepare the journal transaction lines
    if (!empty($savings_account)) {

      $data = [
        'transaction_date' => $date,
        'description' => $narrative,
        'ref_no' => $savings_account['account_no'],
        'ref_id' => $transaction_data['transaction_no'],
        'status_id' => 1,
        'journal_type_id' => 7,
        'unique_id' => $unique_id
      ];
      //then we post this to the journal transaction
      $journal_transaction_id = $this->journal_transaction_model->set($data);
      unset($data);
      $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));

      $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);

      $debit_or_credit1 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id']);
      $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);

      //if deposit amount has been received
      if ($deposit_amount != null && !empty($deposit_amount) && $deposit_amount != '0') {
        $data[0] = [
          'reference_no' => $savings_account['account_no'],
          'reference_id' => $transaction_data['transaction_no'],
          'transaction_date' => $date,
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit1 => $deposit_amount,
          'narrative' => "Deposit transaction made on " . $date,
          'account_id' => $savings_product_details['savings_liability_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
        $data[1] = [
          'reference_no' => $savings_account['account_no'],
          'reference_id' => $transaction_data['transaction_no'],
          'transaction_date' => $date,
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit2 => $deposit_amount,
          'narrative' => "Deposit transaction made on " . $date,
          'account_id' => $transaction_channel['linked_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];

        $this->journal_transaction_line_model->set($journal_transaction_id, $data);
      } //end of the if
    }
  }

  #deposit journal entry function for Multiple Payments
  private function deposit_journal_transaction_multiple_payments($transaction_data, $charges = false)
  {

    if ($this->input->post('payment_date') != NULL && $this->input->post('payment_date') != '') {
      $date = $this->input->post('payment_date');
    } else {
      $date = date('d-m-Y');
    }
    $deposit_amount = $transaction_data['amount'];

    if (is_numeric($this->input->post('account_no_id'))) {
      $account_no_id = $this->input->post('account_no_id');
    } else {
      $account_no_id = $transaction_data['account_no_id'];
    }
    $savings_account = $this->savings_account_model->get_savings_acc_details($account_no_id);
    // print_r($savings_account); die;

    if ($this->input->post('comment') != NULL && $this->input->post('comment') != '') {
      $narrative = $this->input->post('comment');
    } elseif ($this->input->post('narrative') != NULL && $this->input->post('narrative') != '') {
      $narrative = $this->input->post('narrative');
    } else {
      $narrative = "Member deposit";
    }
    //then we prepare the journal transaction lines
    if (!empty($savings_account)) {

      $data = [
        'transaction_date' => $date,
        'description' => $narrative,
        'ref_no' => $savings_account['account_no'],
        'ref_id' => $transaction_data['transaction_no'],
        'status_id' => 1,
        'journal_type_id' => 7
      ];
      //then we post this to the journal transaction
      $journal_transaction_id = $this->journal_transaction_model->set($data);
      unset($data);
      $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
      // print_r('<=||==================================================||=>');


      $savings_product_details = $this->DepositProduct_model->get_products($savings_account['deposit_Product_id']);

      $debit_or_credit1 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id']);
      $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);

      //if deposit amount has been received
      if ($deposit_amount != null && !empty($deposit_amount) && $deposit_amount != '0') {
        $data[0] = [
          'reference_no' => $savings_account['account_no'],
          'reference_id' => $transaction_data['transaction_no'],
          'transaction_date' => $date,
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit1 => $deposit_amount,
          'narrative' => "Deposit transaction made on " . $date,
          'account_id' => $savings_product_details['savings_liability_account_id'],
          'status_id' => 1
        ];
        $data[1] = [
          'reference_no' => $savings_account['account_no'],
          'reference_id' => $transaction_data['transaction_no'],
          'transaction_date' => $date,
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit2 => $deposit_amount,
          'narrative' => "Deposit transaction made on " . $date,
          'account_id' => $transaction_channel['linked_account_id'],
          'status_id' => 1
        ];

        $this->journal_transaction_line_model->set($journal_transaction_id, $data);
      } //end of the if
    }
  }

  public function do_journal_transaction_loan_fees($transaction_date, $loan_id, $unique_id = false)
  {
    $this->load->model('journal_transaction_model');
    $this->load->model('accounts_model');
    $this->load->model('transactionChannel_model');
    $this->load->model('journal_transaction_line_model');
    $this->load->model('applied_loan_fee_model');
    $update = false;
    $data = [
      'transaction_date' => $transaction_date,
      'description' => "Loan Fees Payment",
      'ref_no' => $this->input->post('loan_ref_no'),
      'ref_id' => $loan_id,
      'status_id' => 1,
      'journal_type_id' => 28,
      'unique_id' => $unique_id
    ];
    //then we post this to the journal transaction
    $journal_transaction_id = $this->journal_transaction_model->set($data);
    unset($data);
    //then we prepare the journal transaction lines

    $transaction_channel = $this->transactionChannel_model->get($this->input->post('transaction_channel_id'));
    $debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel['linked_account_id']);

    $where = "a.client_loan_id=" . $loan_id . " AND a.paid_or_not=0";
    $attached_fees = $this->applied_loan_fee_model->get($where);


    foreach ($attached_fees as $fee) {
      $debit_or_credit1 = $this->accounts_model->get_normal_side($fee['income_account_id'], false);
      $data = [
        [
          $debit_or_credit1 => $fee['amount'],
          'transaction_date' => $transaction_date,
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $loan_id,
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          'narrative' => 'Income received from ' . $fee['feename'] . ' on ' . $transaction_date,
          'account_id' => $fee['income_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ],
        [
          $debit_or_credit2 => $fee['amount'],
          'transaction_date' => $transaction_date,
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $loan_id,
          'member_id' => $this->input->post('member_id'),
          'reference_key' => $this->input->post('loan_ref_no'),
          'narrative' => 'Income received from ' . $fee['feename'] . ' on ' . $transaction_date,
          'account_id' => $transaction_channel['linked_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ]
      ];
      if ($this->journal_transaction_line_model->set($journal_transaction_id, $data)) {
        $update = $this->applied_loan_fee_model->mark_charge_paid($fee['id'], $unique_id);
      }
    }
    if ($update == true) {
      return true;
    } else {
      return false;
    }
  }

  private function generate_unique_id()
  {
    $key = implode('-', str_split(substr(strtolower(md5(microtime() . rand(1000, 9999))), 0, 30), 6));
    $unique_id = join("", explode('-', $key));

    return $unique_id;
  }
}
