<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Description of Loan_state
 *
 * @author Eric modified by Reagan
 */
class Loan_state extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->library("session");
    if (empty($this->session->userdata('id'))) {
      redirect('welcome');
    }
    $this->load->model('loan_state_model');
    $this->load->model('client_loan_model');
    $this->load->model('miscellaneous_model');
    $this->load->model('RolePrivilege_model');
    $this->load->model('loan_approval_model');
    $this->load->model('approving_staff_model');
    $this->load->model('applied_loan_fee_model');
    $this->load->model('repayment_schedule_model');
    $this->load->model('loan_reversal_model');
    $this->load->model('Payment_model');
    date_default_timezone_set('Africa/Kampala');
  }
  public function jsonList()
  {
    $this->data['data'] = $this->loan_state_model->get();
    echo json_encode($this->data);
  }
  //Cancling loan application
  public function cancle()
  {
    $response['message'] = "Loan application could not be cancled, contact IT support.";
    $response['success'] = FALSE;
    if ($this->loan_state_model->set()) {
      $response['success'] = TRUE;
      $response['message'] = "Loan application successfully cancled.";

      if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] != '') {
        $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['client_loan_id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id =" . $_POST['group_loan_id']);

        $this->helpers->activity_logs($_SESSION['id'], 4, "Cancelling Loan approval", $response['message'] . " # " . $this->input->post('group_loan_id'), NULL, $this->input->post('group_loan_id'));
      } else {
        $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id IS NULL");
        $this->helpers->activity_logs($_SESSION['id'], 4, "Cancelling Loan approval", $response['message'] . " # " . $this->input->post('client_loan_id'), NULL, $this->input->post('client_loan_id'));
      }
    }
    echo json_encode($response);
  }
  //Rejecting loan application
  public function reject()
  {
    $response['message'] = "Loan application could not be rejected, contact IT support.";
    $response['success'] = FALSE;
    if ($this->loan_state_model->set()) {
      $this->loan_approval_model->deactivate_loan_approvals();
      $response['success'] = TRUE;
      $response['message'] = "Loan application successfully rejected.";
      if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] != '') {
        $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['client_loan_id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id =" . $_POST['group_loan_id']);
        $this->helpers->activity_logs(
          $_SESSION['id'],
          14,
          "Rejecting loan",
          $response['message'] . "#" . $_POST['group_loan_id'],
          $_POST['group_loan_id'],
          null
        );
      } else {
        $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id IS NULL");

        $this->helpers->activity_logs(
          $_SESSION['id'],
          4,
          "Rejecting loan",
          $response['message'] . "#" . $_POST['client_loan_id'],
          $_POST['client_loan_id'],
          null
        );
      }
    }
    echo json_encode($response);
  }

  //withdrawing loan application
  public function application_withdraw()
  {
    $response['message'] = "Loan application could not be withdrawn, contact IT support.";
    $this->helpers->activity_logs(
      $_SESSION['id'],
      4,
      "withdrawing loan",
      $response['message'] . "-" . $_POST['client_loan_id'],
      null,
      null
    );
    $response['success'] = FALSE;
    if ($this->loan_state_model->set()) {
      $this->loan_approval_model->deactivate_loan_approvals();
      $response['success'] = TRUE;
      $response['message'] = "Loan application successfully withdrawn.";

      if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] != '') {
        $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['client_loan_id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id =" . $_POST['group_loan_id']);
        $this->helpers->activity_logs(
          $_SESSION['id'],
          4,
          "withdrawing group loan",
          $response['message'],
          $this->input->post('group_loan_id'),
          $this->input->post('group_loan_id')
        );
      } else {
        $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id IS NULL");

        $this->helpers->activity_logs(
          $_SESSION['id'],
          4,
          "withdrawing client loan",
          $response['message'],
          $this->input->post('client_loan_id'),
          $this->input->post('client_loan_id')
        );
      }
    }
    echo json_encode($response);
  }

  public function email_approving_staffs($loan_id, $requested_amount)
  {
    $where_clause = round($requested_amount) . " >=loan_approval_setting.min_amount AND " . round($requested_amount) . " <=loan_approval_setting.max_amount AND rank!=1";
    $approving_staffs = $this->approving_staff_model->get($where_clause, $loan_id);

    if (is_array($approving_staffs) && $approving_staffs != '') {
      foreach ($approving_staffs as $key => $approving_staff) {
        if ($approving_staff['email']) {
          if ($approving_staff['email'] != '') {
            //print_r($approving_staffs);die;
            $this->org_site_url = base_url();
            $message = "Dear " . ucfirst(strtolower($approving_staff['firstname'])) . ",<br><br>" . " Loan no <b>" . $this->input->post('loan_no') . "</b> of amount <b>" . number_format($this->input->post('requested_amount'), 2) . "</b> has been forwarded for your approval.<br/> You can access the loan by clicking this link " . $this->org_site_url;
            $subject = "Loan approval request - " . $this->input->post('loan_no');
            $this->helpers->send_multiple_email2(1, $approving_staff['email'], $message, $subject);
          }
        }
      }
      return true;
    } else {
      return false;
    }
  }
  //forwarding loan application
  public function forward_application()
  {
    $response['message'] = "Loan application could not be forwarded for approval, contact IT support.";
    $response['success'] = FALSE;
    if ($this->loan_state_model->set()) {
      $response['success'] = TRUE;
      $response['message'] = "Loan application successfully forwarded for approval.";
      if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] != '') {
        //echo "mmmmmm";die;
        $response['email_notification'] = $this->email_approving_staffs($_POST['client_loan_id'], $this->input->post('requested_amount'));
        $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['client_loan_id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id =" . $_POST['group_loan_id']);
      } else {

        $response['email_notification'] = $this->email_approving_staffs($this->input->post('client_loan_id'), $this->input->post('requested_amount'), 2);

        $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id IS NULL");
      }
    }
    echo json_encode($response);
  }

  //locking loan application
  public function lock()
  {

    //check the client or group loan
    $response['message'] = "Loan could not be locked, contact IT support.";

    $response['success'] = FALSE;

    $this->db->trans_begin();

    $installment_data = $this->repayment_schedule_model->get2("client_loan_id=" . $this->input->post('client_loan_id') . " AND payment_status IN (2,4)");

    $penalty_response = $this->calculate_penalty();

    $schedule_penalties = $penalty_response['installments_penalty'];

    $sent_date = explode('-', $this->input->post('action_date'), 3);
    $payment_date = count($sent_date) === 3 ? ($sent_date[2] . "-" . $sent_date[1] . "-" . $sent_date[0]) : null;


    //echo json_encode($penalty_response);die();

    if ($this->loan_state_model->set()) {
      $schedule_data = array();

      foreach ($installment_data as $key => $value) {
        if (isset($schedule_penalties[$value['id']])) {
          $schedule_data[] = [
            'id' => $value['id'],
            'actual_payment_date' => $payment_date,
            'demanded_penalty' => $value['demanded_penalty'] + $schedule_penalties[$value['id']],
            'modified_by' => $_SESSION['id'],
            'comment' => $this->input->post('comment')
          ];
        }
      }
      if (!empty($schedule_penalties)) {
        $this->repayment_schedule_model->clear_multiple_installment($schedule_data);
      } // Just for updating the schedules although we are calling a clear_multiple_installment method. ðŸ˜„  yeah I know we can do better.

      $response['success'] = TRUE;
      $response['message'] = "Loan successfully locked.";


      if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] != '') {
        $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['client_loan_id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id =" . $_POST['group_loan_id']);
      } else {
        $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id IS NULL");

        if ($this->input->post('group_loan_id')) {
          $this->helpers->activity_logs(
            $_SESSION['id'],
            4,
            "Locking loan",
            $response['message'] . "-" . $this->input->post('group_loan_id'),
            $this->input->post('group_loan_id'),
            $this->input->post('group_loan_id')
          );
        } else {
          $this->helpers->activity_logs(
            $_SESSION['id'],
            4,
            "Locking loan",
            $response['message'] . "#" . $_POST['client_loan_id'],
            $this->input->post('client_loan_id'),
            $this->input->post('client_loan_id')
          );
        }
      }
    }

    if ($this->db->trans_status()) {
      $this->db->trans_commit();
    } else {
      $this->db->trans_rollback();
      $response['status'] = false;
      $response['success'] = false;
      $response['message'] = "Loan could not be locked, contact IT support.";
    }


    echo json_encode($response);
  }
  //writting off loan application
  public function write_off()
  {
    $response['message'] = "Loan application could not be written off, contact IT support.";
    $response['success'] = FALSE;
    $this->db->trans_begin();
    $unique_id = $this->generate_unique_id();

    if ($this->loan_state_model->set(false, $unique_id)) {
      $response['success'] = TRUE;
      $response['message'] = "Loan application successfully written off.";
      $this->write_off_journal_transaction($this->input->post('client_loan_id'), $unique_id);
      if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] != '') {
        $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['client_loan_id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id =" . $_POST['group_loan_id']);
      } else {
        $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id IS NULL");
      }
    }

    # set transacting tracking
    // Post to trans_tracking table
    $trans_data = [
      'action_type_id' => 5,
      'payment_mode' => 0,
      'unique_id' => $unique_id,
      'client_loan_id' => $this->input->post('client_loan_id'),
      'loan_state' => $this->loan_reversal_model->get_max_loan_state(),
      'created_by' => $_SESSION['id'],
      'date_created' => date('Y-m-d h:i:s'),
      'modified_by' => $_SESSION['id'],
      'status_id' => 1
    ];

    $this->loan_reversal_model->set_trans_tracking($trans_data);

    if ($this->db->trans_status()) {
      $this->db->trans_commit();
    } else {
      $this->db->trans_rollback();
      $response['message'] = "An Error happened while recording the payment. Please Try again later";
    }
    echo json_encode($response);
  }
  //Revesing action on loan application before approval
  public function reverse()
  {
    if ($this->input->post('state_id') == 7) {
      $response['message'] = "Loan could not be unlocked , contact IT support.";
    } else {
      $response['message'] = "Loan application actions could not be reversed , contact IT support.";
    }
    $response['success'] = FALSE;
    if ($this->loan_state_model->set()) {
      $this->loan_approval_model->deactivate_loan_approvals();

      $response['success'] = TRUE;
      if ($this->input->post('state_id') == 7) {
        $response['message'] = "Loan successfully unlocked taken to active state.";

        $this->helpers->activity_logs(
          $_SESSION['id'],
          4,
          "unlocking loan",
          $response['message'] . " " . $_POST['state_id'],
          $this->input->post('loan_no'),
          $this->input->post('loan_no')
        );
      } else {

        $response['message'] = "Loan application actions successfully reversed taken to partial state.";
      }
      if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] != '') {
        $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['client_loan_id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id =" . $_POST['group_loan_id']);

        $this->helpers->activity_logs(
          $_SESSION['id'],
          4,
          "unlocking loan",
          $response['message'] . " " . $_POST['state_id'],
          $this->input->post('group_loan_id'),
          $this->input->post('group_loan_id')
        );
      } else {
        $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id IS NULL");

        $this->helpers->activity_logs(
          $_SESSION['id'],
          4,
          "unlocking loan",
          $response['message'] . " " . $_POST['state_id'],
          $this->input->post('client_loan_id'),
          "L-ID " . $this->input->post('client_loan_id')
        );
      }
    }
    echo json_encode($response);
  }

  //Revesing action on approved loan application 
  public function reverse_approval()
  {
    $response['message'] = "Loan application Approval  could not be reversed , contact IT support.";
    $response['success'] = FALSE;
    if ($this->loan_state_model->set()) {
      $this->loan_approval_model->deactivate_loan_approvals();
      $response['success'] = TRUE;
      $response['message'] = "Loan application Approval successfully reversed taken to pending Approval state.";
      if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] != '') {
        $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['client_loan_id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id =" . $_POST['group_loan_id']);
      } else {
        $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id IS NULL");
      }
    }
    echo json_encode($response);
  }
  //disbursing a loan
  public function disburse()
  {
    $this->load->model('Loan_attached_saving_accounts_model');
    $this->load->model('loan_reversal_model');

    $this->unique_id = $unique_id = $this->generate_unique_id();
    $org = $this->organisation_model->get($_SESSION['organisation_id']);



    if ($this->input->post('preferred_payment_id') != NULL && $this->input->post('preferred_payment_id') == '4' && $org['mobile_payments'] == 1) {
      $json_loan_disbursement_data = json_encode($this->input->post());
      $this->loan_state_model->set(false, $unique_id);
      $result = $this->Payment_model->merchantTransactionLoans($unique_id, $json_loan_disbursement_data);
      if ($result) {
        echo json_encode(['success' => true]);
      } else {
        echo json_encode(['success' => false]);
      }

      return;
    }

    $old_loan_state = $this->loan_reversal_model->get_max_loan_state();
    $old_linked_loan_state = $this->loan_reversal_model->get_max_loan_state($this->input->post('linked_loan_id'));

    $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
    $this->data['modules'] = array_column($this->data['module_list'], "module_id");
    $response['message'] = "Loan application could not be disbursed, contact IT support.";
    $response['success'] = FALSE;

    //loans to savings account
    if ($this->input->post('preferred_payment_id') != NULL && $this->input->post('preferred_payment_id') == '5' && $org['loans_to_savings'] == 1) {
      $attached_savings_accounts = $this->Loan_attached_saving_accounts_model->get('a.loan_id=' . $this->input->post('client_loan_id'));
      //print_r($attached_savings_accounts); die;
      if (!empty($attached_savings_accounts)) {
        $response = $this->record_disburse($attached_savings_accounts[0], $unique_id);
      } else {
        $response['message'] = 'Savings account not attached to the loan';
        $this->helpers->activity_logs($_SESSION['id'], 4, "Disbursing loan", $response['message'] . " # " . $this->input->post('client_loan_id'), NULL, $this->input->post('client_loan_id'));
      }
    } else {
      //$org = $this->organisation_model->get($_SESSION['organisation_id']);

      $response = $this->record_disburse(false, $unique_id);
    }

    // Post to trans_tracking table
    $loan_data = $this->client_loan_model->get_disbursement_updated_fields($this->input->post('client_loan_id'));
    $trans_data = [
      'action_type_id' => 7,
      'loan_approval_note' => $loan_data['approval_note'],
      'loan_approved_installments' => $loan_data['approved_installments'],
      'loan_approved_repayment_frequency' => $loan_data['approved_repayment_frequency'],
      'loan_approved_repayment_made_every' => $loan_data['approved_repayment_made_every'],
      'loan_amount_approved' => $loan_data['amount_approved'],
      'loan_suggested_disbursement_date' => $loan_data['suggested_disbursement_date'],
      'loan_approval_date' => $loan_data['approval_date'],
      'loan_approved_by' => $loan_data['approved_by'],
      'loan_source_fund_account_id' => $loan_data['source_fund_account_id'],
      'loan_disbursed_amount' => $loan_data['disbursed_amount'],
      'linked_loan_id' => $this->input->post('linked_loan_id'),
      'unique_id' => $unique_id,
      'client_loan_id' => $this->input->post('client_loan_id'),
      'loan_state' => $old_loan_state,
      'linked_loan_state_id' => $old_linked_loan_state,
      'created_by' => $_SESSION['id'],
      'date_created' => date('Y-m-d h:i:s'),
      'modified_by' => $_SESSION['id'],
      'status_id' => 1
    ];
    $this->loan_reversal_model->set_trans_tracking($trans_data);


    echo json_encode($response);
  }


  private function record_disburse($attached_savings_accounts = false, $unique_id = false)
  {

    $this->load->model('transaction_model');
    $response = array();
    $this->db->trans_begin();
    #adding loan fees for this loan disbursement
    $client_loan = $this->client_loan_model->get_client_data($this->input->post('client_loan_id'));
    //print_r($client_loan); die();

    $loan_fees_sum = 0;
    if ($this->input->post('loanFees') != NULL && $this->input->post('loanFees') != '') {

      $loanFees = $this->input->post('loanFees');
      foreach ($loanFees as $key => $value) { //it is a new entry, so we insert afresh

        if (isset($value['remove_or_not'])) {
          unset($value['remove_or_not']);

          $loan_fees_sum += $value['amount'];

          $value['date_created'] = time();
          $value['client_loan_id'] = $this->input->post('client_loan_id');
          $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
          $this->applied_loan_fee_model->set2($value, $unique_id);
        }
      }
      if ($attached_savings_accounts == false && $loanFees) {
        $this->do_journal_transaction_loan_fees($this->input->post('action_date'), $this->input->post('client_loan_id'), $unique_id);
      }
    }
    // die();
    #the transaction queries start
    $this->loan_state_model->set(false, $unique_id);

    $this->repayment_schedule_model->set(false, $unique_id);
    if ($this->input->post('steps') == 1) {

      $this->client_loan_model->approve($_POST['client_loan_id'], $unique_id);
      $this->loan_approval_model->set($_POST['client_loan_id'], $unique_id);
    }
    $this->client_loan_model->update_source_fund(false, $unique_id);

    if ($attached_savings_accounts != false) {

      if ($this->input->post('unpaid_principal') != NULL && $this->input->post('unpaid_principal') != '' && $this->input->post('unpaid_principal') != '0') {
        $principal_amount = round($this->input->post('principal_value') - $this->input->post('unpaid_principal'), 2);
      } else {
        $principal_amount = round($this->input->post('principal_value'), 2);
      }

      $deduction_data['amount'] = $principal_amount;
      $deduction_data['transaction_type_id'] = 2;
      $deduction_data['transaction_date'] = $this->input->post('action_date');
      $deduction_data['account_no_id'] = $attached_savings_accounts['saving_account_id'];
      $deduction_data['narrative'] = 'LOAN DEPOSIT of ' . $client_loan['loan_no'];
      $transaction_data = $this->transaction_model->deduct_savings($deduction_data, $unique_id);
    }

    if ($attached_savings_accounts != false) {
      if ($this->input->post('preffered_payment_id') == 1) {
        $charge_trigger_id = array('2', '3', '4');
      } elseif ($this->input->post('preffered_payment_id') == 2) {
        $charge_trigger_id = array('2', '3', '5');
      } elseif ($this->input->post('preffered_payment_id') == 4) {
        $charge_trigger_id = array('2', '3', '6');
      } else {
        $charge_trigger_id = array('2', '3', '4', '5', '6', '8', '9', '10');
      }
      $this->helpers->deduct_charges($_POST['client_loan_id'], $charge_trigger_id, false, $this->input->post('action_date'), $unique_id);
    }


    if ($attached_savings_accounts != false) {
      $deduction_data['transaction_type_id'] = 1;
      $deduction_data['transaction_date'] = $this->input->post('action_date');
      $deduction_data['narrative'] = 'LOAN WITHDRAW of ' . $client_loan['loan_no'];

      $org = $this->organisation_model->get($_SESSION['organisation_id']);
      if ($org['deduct_loan_fees_from_loan'] == 1) {
        $deduction_data['amount'] = $deduction_data['amount'] - $loan_fees_sum;
      }
      // Deduct Loan from savings A/C
      if (isset($org['deduct_loan']) && intval($org['deduct_loan']) == 1) {
      $transaction_data = $this->transaction_model->deduct_savings($deduction_data, $unique_id);
      }

      $deduction_data['account_no'] = $attached_savings_accounts['account_no'];
      //print_r($deduction_data['account_no']); die;
      $this->do_journal_transaction($deduction_data, $loan_fees_sum, $attached_savings_accounts, $unique_id);
      
    } else {
      $this->do_journal_transaction(false, false, false, $unique_id);
    }
    //closing off the parent loan if it's a Top up loan
    if ($this->input->post('linked_loan_id') != NULL && $this->input->post('linked_loan_id') != '') {
      $filter['client_loan_id'] = $this->input->post('linked_loan_id');
      $filter['state_id'] = 14;
      $filter['comment'] = 'Loan closed due to a refinance / Top Up';
      $this->helpers->activity_logs($_SESSION['id'], 4, "Topped up loan", " # " . $this->input->post('client_loan_id'), NULL, $this->input->post('client_loan_id'));

      $this->loan_state_model->set($filter, $unique_id);
      $this->repayment_schedule_model->clear_installment($this->input->post('linked_loan_id'), 'refinance', $unique_id);
    }

    #the transaction queries end here
    if ($this->db->trans_status()) {
      $this->db->trans_commit();
      $response['success'] = TRUE;
      $response['message'] = "Loan application successfully disbursed.";
      if (isset($_POST['group_loan_id']) && $_POST['group_loan_id'] != '') {
        $response['client_loan'] = $this->client_loan_model->get_client_loan("a.id=" . $_POST['client_loan_id'] . " AND a.group_loan_id=" . $_POST['group_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id =" . $_POST['group_loan_id']);
      } else {
        $response['client_loan'] = $this->client_loan_model->get_client_loan($_POST['client_loan_id']);
        $response['state_totals'] = $this->client_loan_model->state_totals("a.group_loan_id IS NULL");

        $data['org'] = $this->organisation_model->get(1);
        $data['branch'] = $this->organisation_model->get_org(1);
        $organisation = $data['org']['name'];
        $contact_number = $data['branch']['office_phone'];

        $message = "Your loan with loan number " . $response['client_loan']['loan_no'] . " has been disbursed today on " . date('d-m-Y') . " Remember to go with a disbursement sheet.";

        $email_response = $this->helpers->send_email($this->input->post('client_loan_id'), $message);

        if (!empty($result = $this->miscellaneous_model->check_org_module(22))) {
          $message = $message . ".
" . $organisation . ", Contact " . $contact_number;
          $text_response = $this->helpers->notification($this->input->post('client_loan_id'), $message);
          $response['message'] = $response['message'] . $text_response;
        }
      }
    } else {
      $this->db->trans_rollback();
    }

    return $response;
  }

  private function do_journal_transaction($transaction_data = false, $total_loan_fees = false, $attached_savings_accounts = false, $unique_id = false)
  {
    $org = $this->organisation_model->get($_SESSION['organisation_id']);
    
    $this->load->model('journal_transaction_model');
    $this->load->model('loan_product_model');
    $client_loan = $this->client_loan_model->get_client_data($this->input->post('client_loan_id'));    
    $membere_id = $this->input->post('member_id');
    if ($this->input->post('unpaid_principal') != NULL && $this->input->post('unpaid_principal') != '' && $this->input->post('unpaid_principal') != '0') {
      $principal_amount = round($this->input->post('principal_value') - $this->input->post('unpaid_principal'), 0);
    } else {
      $principal_amount = round($this->input->post('principal_value'), 2);
    }
    $repayment_schedules = $this->input->post('repayment_schedule');
    $interest_amount_total = 0;
    foreach ($repayment_schedules as $key => $value) {
      $interest_amount_total += $value['interest_amount'];
    }
    $interest_amount = round($interest_amount_total, 2);
    $data = [
      'transaction_date' => $this->input->post('action_date'),
      'description' =>  strtoupper("Loan Disbursement on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . isset($client_loan['member_name']) ? $client_loan['member_name'] : $client_loan['group_name'] . " ] ",
      'ref_no' => $client_loan['loan_no'],
      'ref_id' => $this->input->post('client_loan_id'),
      'status_id' => 1,
      'journal_type_id' => 4,
      'unique_id' => $unique_id
    ];
    //then we post this to the journal transaction
    $journal_transaction_id = $this->journal_transaction_model->set($data);
    unset($data);
    //then we prepare the journal transaction lines
    if (!empty($client_loan)) {

      $this->load->model('accounts_model');
      $this->load->model('journal_transaction_line_model');
      $this->load->model('savings_account_model');

      $loan_product_details = $this->loan_product_model->get_accounts($client_loan['loan_product_id']);
      $Loan_account_details = $this->accounts_model->get($loan_product_details['loan_receivable_account_id']);
      $source_fund_ac_details = $this->accounts_model->get($this->input->post('source_fund_account_id'));
      $Interest_receivable_ac_details = $this->accounts_model->get($loan_product_details['interest_receivable_account_id']);
      $Interest_income_ac_details = $this->accounts_model->get($loan_product_details['interest_income_account_id']);

      $index_key = 6;
      $interest_data = $this->repayment_schedule_model->get($this->input->post('client_loan_id'));

      $debit_or_credit1 = ($Loan_account_details['normal_balance_side'] == 1) ? 'debit_amount' : 'credit_amount';
      $debit_or_credit2 = ($source_fund_ac_details['normal_balance_side'] == 1) ? 'credit_amount' : 'debit_amount'; //Although the normal balancing side is debit side, in this scenario money is being given out so we shall instead credit it.
      $debit_or_credit3 = ($Interest_income_ac_details['normal_balance_side'] == 1) ? 'debit_amount' : 'credit_amount';
      $debit_or_credit4 = ($Interest_receivable_ac_details['normal_balance_side'] == 1) ? 'debit_amount' : 'credit_amount';
      //for Top up loan purpose
      $debit_or_credit5 = ($Interest_receivable_ac_details['normal_balance_side'] == 1) ? 'credit_amount' : 'debit_amount';
      $debit_or_credit6 = ($Interest_income_ac_details['normal_balance_side'] == 1) ? 'credit_amount' : 'debit_amount';

      if (isset($transaction_data['account_no_id']) && $transaction_data['account_no_id'] != '') { //used if money passes through member savings
        $savings_product_details = $this->savings_account_model->get($transaction_data['account_no_id']);
        $debit_or_credit7 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id']);
        $debit_or_credit8 = $this->accounts_model->get_normal_side($savings_product_details['savings_liability_account_id'], true);
      }

      if ($attached_savings_accounts && $total_loan_fees > 0) {

        $org = $this->organisation_model->get($_SESSION['organisation_id']);
        if ($org['deduct_loan_fees_from_loan'] == 1) {
          $principal_less_fees = $principal_amount - $total_loan_fees;
        } else {
          $principal_less_fees = $principal_amount;
        }
        $debit_or_credit2_amount = $principal_less_fees;
        $debit_or_credit8_amount = $principal_less_fees;
      } else {
        $debit_or_credit2_amount = $principal_amount;
        $debit_or_credit8_amount = $principal_amount;
      }

      $data[0] =   [
        'reference_no' =>  $client_loan['loan_no'],
        'reference_id' => $this->input->post('client_loan_id'),
        'transaction_date' => $this->input->post('action_date'),
        'member_id' => $membere_id,
        'reference_key' => $client_loan['loan_no'],
        $debit_or_credit2 => $debit_or_credit2_amount, ##
        'narrative' =>  strtoupper("Loan Disbursement on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
        'account_id' => $this->input->post('source_fund_account_id'),
        'status_id' => 1,
        'unique_id' => $unique_id
      ];

      if (isset($transaction_data['account_no_id']) && $transaction_data['account_no_id'] != '') { //used if money passes through member savings
        $data[1] =  [
          'reference_no' => $client_loan['loan_no'],
          'reference_id' => $this->input->post('client_loan_id'),
          'transaction_date' => $this->input->post('action_date'),
          'member_id' => $membere_id,
          'reference_key' => $transaction_data['account_no'],
          $debit_or_credit7 => $principal_amount,
          'narrative' => strtoupper("Loan Disbursement on " . $this->input->post('action_date')),
          'account_id' => $savings_product_details['savings_liability_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
        if(isset($org['deduct_loan']) && $org['deduct_loan'] == 0) {
          // Do not auto deduct loan from savings
        } else {
            $data[2] =  [
            'reference_no' => $client_loan['loan_no'],
            'reference_id' => $this->input->post('client_loan_id'),
            'transaction_date' => $this->input->post('action_date'),
            'member_id' => $membere_id,
            'reference_key' => $transaction_data['account_no'],
            $debit_or_credit8 => $debit_or_credit8_amount, ##
            'narrative' => strtoupper("Loan Disbursement on " . $this->input->post('action_date')),
            'account_id' => $savings_product_details['savings_liability_account_id'],
            'status_id' => 1,
            'unique_id' => $unique_id
          ];
        }
        

      }

      $data[3] = [
        'reference_no' => $client_loan['loan_no'],
        'reference_id' => $this->input->post('client_loan_id'),
        'transaction_date' => $this->input->post('action_date'),
        'member_id' => $membere_id,
        'reference_key' => $client_loan['loan_no'],
        $debit_or_credit1 => $principal_amount,
        'narrative' =>  strtoupper("Loan Disbursement on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
        'account_id' => $loan_product_details['loan_receivable_account_id'],
        'status_id' => 1,
        'unique_id' => $unique_id
      ];

      if ($this->input->post('linked_loan_id') != NULL && $this->input->post('linked_loan_id') != '') {
        // check for unpaid interest
        $parent_loan_partial_install = $this->repayment_schedule_model->get_due_schedules('repayment_schedule.client_loan_id=' . $this->input->post('linked_loan_id') . ' AND payment_status=2');
        //print_r($parent_loan_partial_install);die;
        //if there is unpaid interest
        $LINKED_LOAN = $this->client_loan_model->get_client_data($this->input->post('linked_loan_id'));

        if (!empty($parent_loan_partial_install)) {
          if ($parent_loan_partial_install[0]['interest_amount'] != null && !empty($parent_loan_partial_install[0]['interest_amount']) && $parent_loan_partial_install[0]['interest_amount'] != '0') {

            $parent_loan_product_details = $this->loan_product_model->get_accounts($LINKED_LOAN['loan_product_id']);
            $debit_or_credit10 = $this->accounts_model->get_normal_side($parent_loan_product_details['interest_receivable_account_id'], true);
            $debit_or_credit11 = $this->accounts_model->get_normal_side($parent_loan_product_details['interest_income_account_id'], true);

            $data[4] = [
              'reference_no' => $parent_loan_partial_install[0]['id'],
              'reference_id' => $parent_loan_partial_install[0]['client_loan_id'],
              'transaction_date' => $this->input->post('action_date'),
              'member_id' => $membere_id,
              'reference_key' => $client_loan['loan_no'],
              $debit_or_credit10 => $parent_loan_partial_install[0]['interest_amount'],
              'narrative' => strtoupper("Parent Loan unpaid interest write off on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
              'account_id' => $parent_loan_product_details['interest_receivable_account_id'],
              'status_id' => 1,
              'unique_id' => $unique_id
            ];
            $data[5] = [
              'reference_no' => $parent_loan_partial_install[0]['id'],
              'reference_id' => $parent_loan_partial_install[0]['client_loan_id'],
              'transaction_date' => $this->input->post('action_date'),
              'member_id' => $membere_id,
              'reference_key' => $client_loan['loan_no'],
              $debit_or_credit11 => $parent_loan_partial_install[0]['interest_amount'],
              'narrative' => strtoupper("Parent Loan unpaid interest write off on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
              'account_id' => $parent_loan_product_details['interest_income_account_id'],
              'status_id' => 1,
              'unique_id' => $unique_id
            ];
          }
        }
        $this->load->model('journal_transaction_line_model');
        $parent_loan = $this->repayment_schedule_model->get2('repayment_schedule.client_loan_id=' . $this->input->post('linked_loan_id') . ' AND status_id=1 AND payment_status=4');

        foreach ($parent_loan as $key => $value) {
          $line_data['status_id'] = 3;
          $line_data['unique_id'] = $unique_id;
          $RRR = $this->journal_transaction_line_model->update_status_topup($line_data, $LINKED_LOAN['loan_no'], $value['id'], $value['repayment_date']);
        }
      }

      foreach ($interest_data as $key => $value) {
        $index_key += 2;
        $transaction_date = date('d-m-Y', strtotime($value['repayment_date']));
        $data[$index_key - 1] = [
          'reference_no' => $client_loan['loan_no'],
          'reference_id' => $value['id'],
          'transaction_date' => $transaction_date,
          'member_id' => $membere_id,
          'reference_key' => $client_loan['loan_no'],
          $debit_or_credit3 => $value['interest_amount'],
          'narrative' => strtoupper("Interest on Loan Disbursed on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
          'account_id' => $loan_product_details['interest_income_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];

        $data[$index_key] =  [
          'reference_no' => $client_loan['loan_no'],
          'reference_id' => $value['id'],
          'transaction_date' => $transaction_date,
          'member_id' => $membere_id,
          'reference_key' => $client_loan['loan_no'],
          $debit_or_credit4 => $value['interest_amount'],
          'narrative' => strtoupper("Interest on Loan Disbursed on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
          'account_id' => $loan_product_details['interest_receivable_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
      }
      $this->journal_transaction_line_model->set($journal_transaction_id, $data);
    }
  }
  private function write_off_journal_transaction($transaction_id, $unique_id = false)
  {
    $this->load->model('journal_transaction_model');
    $this->load->model('loan_product_model');
    $membere_id = $this->input->post('member_id');

    $client_loan = $this->client_loan_model->get_client_data($this->input->post('client_loan_id'));

    $principal_amount = round($this->input->post('un_paid_principal'), 2);
    $interest_amount = round($this->input->post('un_paid_interest'), 2);
    $penalty_amount = round($this->input->post('un_paid_penalty'), 2);
    $expected_total = round($this->input->post('unpaid_total'), 2);
    //then we prepare the journal transaction lines
    if (!empty($client_loan)) {
      $this->load->model('accounts_model');
      $this->load->model('transactionChannel_model');
      $this->load->model('journal_transaction_line_model');

      $data = [
        'transaction_date' => $this->input->post('action_date'),
        'description' => strtoupper("Loan write off on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
        'ref_no' => $this->input->post('loan_ref_no'),
        'ref_id' => $transaction_id,
        'status_id' => 1,
        'journal_type_id' => 19,
        'unique_id' => $unique_id
      ];
      //then we post this to the journal transaction
      $journal_transaction_id = $this->journal_transaction_model->set($data);
      unset($data);

      $loan_product_details = $this->loan_product_model->get_accounts($client_loan['loan_product_id']);

      $debit_or_credit1 =  $this->accounts_model->get_normal_side($loan_product_details['loan_receivable_account_id'], true);
      $debit_or_credit4 = $this->accounts_model->get_normal_side($loan_product_details['interest_receivable_account_id'], true);
      $debit_or_credit5 = $this->accounts_model->get_normal_side($loan_product_details['interest_income_account_id'], true);
      $debit_or_credit6 = $this->accounts_model->get_normal_side($loan_product_details['written_off_loans_account_id']);

      //if there is unpaid principal
      if ($principal_amount != null && !empty($principal_amount) && $principal_amount != '0') {
        $data[0] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $transaction_id,
          'transaction_date' => $this->input->post('action_date'),
          'member_id' => $membere_id,
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit1 => $principal_amount,
          'narrative' => strtoupper("Loan write off on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
          'account_id' => $loan_product_details['loan_receivable_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
        $data[1] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $transaction_id,
          'transaction_date' => $this->input->post('action_date'),
          'member_id' => $membere_id,
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit6 => $principal_amount,
          'narrative' => strtoupper("Loan write off on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
          'account_id' => $loan_product_details['written_off_loans_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
      }

      //if there is unpaid interest
      if ($interest_amount != null && !empty($interest_amount) && $interest_amount != '0') {
        $data[2] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $transaction_id,
          'transaction_date' => $this->input->post('action_date'),
          'member_id' => $membere_id,
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit4 => $interest_amount,
          'narrative' => strtoupper("Loan interest write off on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
          'account_id' => $loan_product_details['interest_receivable_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
        $data[3] = [
          'reference_no' => $this->input->post('loan_ref_no'),
          'reference_id' => $transaction_id,
          'transaction_date' => $this->input->post('action_date'),
          'member_id' => $membere_id,
          'reference_key' => $this->input->post('loan_ref_no'),
          $debit_or_credit5 => $interest_amount,
          'narrative' => strtoupper("Loan interest write off on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
          'account_id' => $loan_product_details['interest_income_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ];
      }
      $this->journal_transaction_line_model->set($journal_transaction_id, $data);
    }
  }
  private function penalty_journal_transaction($transaction_id)
  {
    $this->load->model('journal_transaction_model');
    $this->load->model('loan_product_model');


    $client_loan = $this->client_loan_model->get_client_data($this->input->post('client_loan_id'));

    $penalty_amount = round($this->input->post('paid_penalty'), 2);
    //then we prepare the journal transaction lines
    if (!empty($client_loan)) {
      $this->load->model('accounts_model');
      $this->load->model('transactionChannel_model');
      $this->load->model('journal_transaction_line_model');

      $data = [
        'transaction_date' => $this->input->post('action_date'),
        'description' => strtoupper("Loan penalty write off on " . $this->input->post('action_date')) . " [ " . strtoupper($this->input->post('comment')) . "] [ " . $client_loan['member_name'] . " ] ",
        'ref_no' => $this->input->post('loan_ref_no'),
        'ref_id' => $transaction_id,
        'status_id' => 1,
        'journal_type_id' => 18
      ];
      //then we post this to the journal transaction
      $journal_transaction_id = $this->journal_transaction_model->set($data);
      unset($data);
      $loan_product_details = $this->loan_product_model->get_accounts($client_loan['loan_product_id']);

      $debit_or_credit2 = $this->accounts_model->get_normal_side($loan_product_details['penalty_income_account_id']);

      //if penalty has been recieved
      if ($penalty_amount != null && !empty($penalty_amount) && $penalty_amount != '0') {
        $data[0] =
          [
            'reference_no' => $this->input->post('loan_ref_no'),
            'reference_id' => $transaction_id,
            'transaction_date' => $this->input->post('action_date'),
            $debit_or_credit2 => $penalty_amount,
            'narrative' => strtoupper("Loan penalty write off on " . $this->input->post('action_date')),
            'account_id' => $loan_product_details['penalty_income_account_id'],
            'status_id' => 1
          ];
      }
      $this->journal_transaction_line_model->set($journal_transaction_id, $data);
    }
  }


  public function do_journal_transaction_loan_fees($transaction_date, $loan_id, $unique_id = false)
  {
    $this->load->model('journal_transaction_model');
    $this->load->model('accounts_model');
    $this->load->model('transactionChannel_model');
    $this->load->model('journal_transaction_line_model');
    $client_loan = $this->client_loan_model->get_client_data($loan_id);
    $update = false;
    $membere_id = $this->input->post('member_id');
    $data = [
      'transaction_date' => $transaction_date,
      'description' => "Loan Fees Payment [ " . $client_loan['loan_no'] . " ][ " . $client_loan['member_name'] . " ]",
      'ref_no' => $client_loan['loan_no'],
      'ref_id' => $loan_id,
      'status_id' => 1,
      'journal_type_id' => 28,
      'unique_id' => $unique_id
    ];
    //then we post this to the journal transaction
    $journal_transaction_id = $this->journal_transaction_model->set($data);
    unset($data);
    //then we prepare the journal transaction lines
    $linked_account_id = $this->input->post('source_fund_account_id');

    $debit_or_credit2 = $this->accounts_model->get_normal_side($linked_account_id, false);

    $where = "a.client_loan_id=" . $loan_id . " AND a.paid_or_not=0";
    $attached_fees = $this->applied_loan_fee_model->get($where);


    foreach ($attached_fees as $fee) {
      $debit_or_credit1 = $this->accounts_model->get_normal_side($fee['income_account_id'], false);
      $data = [
        [
          $debit_or_credit1 => $fee['amount'],
          'transaction_date' => $transaction_date,
          'reference_no' =>  $client_loan['loan_no'],
          'reference_id' => $loan_id,
          'member_id' => $membere_id,
          'reference_key' => $client_loan['loan_no'],
          'narrative' => 'Income received from ' . $fee['feename'] . ' on ' . $transaction_date . " [ " . $client_loan['loan_no'] . " ][ " . $client_loan['member_name'] . " ]",
          'account_id' => $fee['income_account_id'],
          'status_id' => 1,
          'unique_id' => $unique_id
        ],
        [
          $debit_or_credit2 => $fee['amount'],
          'transaction_date' => $transaction_date,
          'reference_no' =>  $client_loan['loan_no'],
          'reference_id' => $loan_id,
          'member_id' => $membere_id,
          'reference_key' => $client_loan['loan_no'],
          'narrative' => 'Income received from ' . $fee['feename'] . ' on ' . $transaction_date . " [ " . $client_loan['loan_no'] . " ][ " . $client_loan['member_name'] . " ]",
          'account_id' => $linked_account_id,
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

  public function dateDiff($d1, $d2)
  {

    $datediff = $d1 - $d2;

    return round($datediff / (60 * 60 * 24));

    //return round(abs(strtotime($d1) - strtotime($d2)) / 86400);
  }

  private function calculate_penalty()
  {
    $this->load->model('repayment_schedule_model');
    $this->load->model('loan_installment_payment_model');

    $loan_id = $this->input->post('client_loan_id');

    $schedule['data'] = $this->repayment_schedule_model->get("client_loan_id=" . $loan_id . " AND payment_status IN (2,4)");

    $installments_penalty = array();
    $total_penalty = 0;

    foreach ($schedule['data'] as $key => $value) {


      $installment_number = $value['installment_number'];

      $payment_date = $this->input->post('action_date');


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

              $penalty_value = $over_due_principal * $number_of_late_period * $penalty_rate;
              $installments_penalty[$value['id']] = $penalty_value;
              $total_penalty += $penalty_value;
            }
          }
        }
      }
    }

    $response = [
      'installments_penalty' => $installments_penalty,
      'total_penalty' => $total_penalty
    ];

    return $response;
  }

}
