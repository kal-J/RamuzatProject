<?php
/**
 * Description of Loan reversal
 *
 * @author Joshua & Kalujja
 */

class Loan_reversal extends CI_Controller
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
        $this->load->model('Loan_guarantor_model');
        $this->load->model('journal_transaction_model');
        $this->load->model('Loan_reversal_model');
        $this->load->model('loan_state_model');

        $this->load->model("logs_model");
        $this->load->model('accounts_model');
        $this->load->model('Staff_model');

        $this->load->library("num_format_helper");
        $this->load->model('miscellaneous_model');
        $this->load->model("organisation_format_model");
        $this->load->model("RolePrivilege_model");
        $orgdata['org'] = $this->organisation_model->get(1);
        $orgdata['branch'] = $this->organisation_model->get_org(1);
        $this->organisation = $orgdata['org']['name'];
        $this->contact_number = $orgdata['branch']['office_phone'];
    }

    public function jsonList()
    {
        $this->data['data'] = $this->Loan_reversal_model->get_transactions();
        echo json_encode($this->data);
    }

    public function index()
    {
        $this->data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
        $this->data['modules'] = array_column($this->data['module_list'], "module_id");


        $this->data['fiscal_year'] = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $fiscal_year = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $this->data['members'] = $this->Staff_model->get_staff();

        $this->data['title'] = $this->data['sub_title'] = "Loan Reversal";

        $this->template->title = $this->data['title'];

        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js");
        $neededcss = array("plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css", "custom.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        //$this->data['summary'] = $this->jsonList(date('d-m-Y', strtotime($fiscal_year['start_date'])),date('d-m-Y', strtotime($fiscal_year['end_date'])));

        $this->template->content->view('client_loan/loan_reversal/index', $this->data);
        // Publish the template
        $this->template->publish();
    }


    public function reverse()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules("reverse_msg", "Reason", array("required"), array("required" => "%s must be entered"));

        $response['success'] = false;
        $response['message'] = 'Failed to reverse transaction';

        if ($this->form_validation->run() === FALSE) {
            $response['message'] = validation_errors('<li>', '</li>');
        } else {

            $this->db->trans_begin();

            $client_loan_id = $this->input->post('client_loan_id');
            $unique_id = $this->input->post('unique_id');

            #1. Get most recent transaction of the specified loan using loan_id and unique_id
            $filter = "client_loan_id='{$client_loan_id}' AND unique_id='{$unique_id}' ";
            $trans_info = $this->Loan_reversal_model->get_trans_tracking($filter);



            if ($trans_info['action_type_id'] == 1) { // Single Installment Payment
                # Reverse Single Installment Payment
                $this->loan_installment_payments($trans_info);
            } else if ($trans_info['action_type_id'] == 2) { // Multiple Installment Payment
                # Reverse Multiple Installment Payment
                $this->loan_installment_payments($trans_info);
            } else if ($trans_info['action_type_id'] == 3) { // Loan Curtailment
                # Reverse Loan Curtailment
                $this->loan_curtailment($trans_info);
            } else if ($trans_info['action_type_id'] == 4) { // Pay Off
                # Reverse Pay Off
                $this->pay_off($trans_info);
            } else if ($trans_info['action_type_id'] == 5) { // Write Off
                # Reverse Write Off
                $this->write_off($trans_info);
            } else if ($trans_info['action_type_id'] == 6) { // Reschedule
                # Reverse Reschedule 
                $this->reschedule($trans_info);
            } else if ($trans_info['action_type_id'] == 7) { // Loan Disbursement
                # Reverse Loan Disbursement 
                $this->reverse_loan_disbursement($trans_info);
            }

            # Delete unique_id
            $id = $trans_info['id'];
            $filter = "id= '{$id}' ";
            $this->Loan_reversal_model->update_trans_tracking($filter);

            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                $response['success'] = true;
                $response['status'] = true;
                $response['message'] = "Transaction Reversed successfully";
            } else {
                $this->db->trans_rollback();
                $response['status'] = false;
                $response['success'] = false;
                $response['message'] = "An Error happened while reversing the Transaction. Please Try again later";
            }
        }

        echo json_encode($response);
    }

    public function pay_off($trans_info)
    {

        $unique_id = $trans_info['unique_id'];
        $loan_id = $trans_info['client_loan_id'];

        #1. Reverse Journal Entries 👇
        $this->Loan_reversal_model->reverse_journals("unique_id = '{$unique_id}' ");

        #2. Reverse Applied Loan Fees
        $this->Loan_reversal_model->reverse_applied_loan_fees("unique_id = '{$unique_id}' ");

        #3. Reverse repayment_schedule
        $repayment_schedule_id = $trans_info['repayment_schedule_id'];

        // Get Max repayment_schedule_id from loan_installment_table
        $installment_payment = $this->Loan_reversal_model->get_max_loan_installment_payment("unique_id = '{$unique_id}' AND repayment_schedule_id = '{$repayment_schedule_id}' AND status_id=1 ");

        // Turn off Loan Installment Payment
        $this->Loan_reversal_model->reverse_loan_payment($installment_payment['id']);

        // Update repayment_schedule table
        $this->Loan_reversal_model->pay_off_reverse_schedule($installment_payment);

        #4. Update Loan state
        $this->Loan_reversal_model->pay_off_reverse_loan_state($trans_info['loan_state']);

        #5. Reverse savings transactions
        $data['reversed_by'] = $_SESSION['id'];
        $data['reversed_date'] = date("Y-m-d H:i:s");
        $data['reverse_msg'] = $this->input->post('reverse_msg');
        $data['status_id'] = 3;

        $this->Loan_reversal_model->reverse_savings($data, $unique_id);
    }

    public function reschedule($trans_info)
    {
        $unique_id = $trans_info['unique_id'];
        $loan_id = $trans_info['client_loan_id'];

        #1. Turn off all active schedules from base_installment
        $base_installment = $trans_info['base_installment'];
        $filter = "installment_number >= '{$base_installment}' AND status_id=1 AND client_loan_id= '{$loan_id}' ";
        $this->Loan_reversal_model->turn_off_schedules($filter);

        #2. Turn on all schedules De-activated due to rescheduling i.e with status_id => 2, starting from the base_installment
        $filter = "installment_number >= '{$base_installment}' AND status_id=2 AND client_loan_id= '{$loan_id}' AND unique_id='{$unique_id}' ";
        $this->Loan_reversal_model->turn_on_schedules($filter);

        #3. Reverse client_loan table details
        $data = [
            'approved_installments' => $trans_info['loan_approved_installments'],
            'interest_rate' => $trans_info['loan_interest_rate'],
            'approved_repayment_made_every' => $trans_info['loan_approved_repayment_made_every'],
            'approved_repayment_frequency' => $trans_info['loan_approved_repayment_frequency'],
            'status_id' => $trans_info['loan_status_id'],
            'modified_by' => $trans_info['loan_modified_by'],
        ];
        $this->Loan_reversal_model->update_client_loan($data);

        #4. Reverse Journal Entries 👇  
        $reverse_journal_schedules_data = [
            'action_type_id' => $trans_info['action_type_id'],
            'base_repayment_schedule_id' => $trans_info['repayment_schedule_id']
        ];
        $this->Loan_reversal_model->reverse_journals("unique_id = '{$unique_id}' ", $reverse_journal_schedules_data);
        #4. Reverse Journal Entries 👆 
    }

    public function write_off($trans_info)
    {
        $unique_id = $trans_info['unique_id'];
        $loan_id = $trans_info['client_loan_id'];

        #1. Update loan state
        $data = [
            'state_id' => $trans_info['loan_state'],
            'client_loan_id' => $loan_id,
            'comment' => $this->input->post('reverse_msg')
        ];
        $this->loan_state_model->set($data);

        #2. Reverse Journal Entries 👇 
        $this->Loan_reversal_model->reverse_journals("unique_id = '{$unique_id}' ");
        #2. Reverse Journal Entries 👆 

    }

    public function loan_curtailment($trans_info)
    {
        $unique_id = $trans_info['unique_id'];
        $loan_id = $trans_info['client_loan_id'];

        #1.  repayment_schedule_id
        $repayment_schedule_id = $trans_info['repayment_schedule_id'];

        #2. Delete/Turn off recently created schedules
        // Turn off all active schedules after base_installment
        $base_installment = $trans_info['base_installment'];
        $filter = "installment_number > '{$base_installment}' AND status_id=1 AND client_loan_id= '{$loan_id}' ";
        $this->Loan_reversal_model->turn_off_schedules($filter);

        #3. Update base_installment in the repayment_schedule table
        // Get Max base_installment repayment_schedule_id from loan_installment_table
        $filter = "client_loan_id = '{$loan_id}' AND unique_id = '{$unique_id}' AND repayment_schedule_id = '{$repayment_schedule_id}' AND status_id=1 ";
        $base_installment_payment = $this->Loan_reversal_model->get_max_loan_installment_payment($filter);
        $update_data = [
            'payment_status' => $base_installment_payment['prev_payment_status'],
            'demanded_penalty' => $base_installment_payment['prev_demanded_penalty'],
            'actual_payment_date' => $base_installment_payment['prev_payment_date'],
        ];
        $this->Loan_reversal_model->update_schedule($update_data, $repayment_schedule_id);

        #4. Update Payments
        $id = $base_installment_payment['id'];
        $filter = "client_loan_id = '{$loan_id}' AND unique_id = '{$unique_id}' AND status_id=1 AND id >= '{$id}' ";
        $this->Loan_reversal_model->update_loan_payments($filter);

        #5. Re-activate old schedule
        // Turn on all schedules De-activated due to curtailment i.e with status_id => 2
        $filter = "installment_number > '{$base_installment}' AND status_id=2 AND client_loan_id= '{$loan_id}' ";
        $this->Loan_reversal_model->turn_on_schedules($filter);

        #6 Do Journal reversals 👇 
        $reverse_journal_schedules_data = [
            'action_type_id' => $trans_info['action_type_id'],
            'base_repayment_schedule_id' => $trans_info['repayment_schedule_id']
        ];
        $this->Loan_reversal_model->reverse_journals("unique_id = '{$unique_id}' ", $reverse_journal_schedules_data);
        #6 Do Journal reversals 👆  

        #7. Reverse savings transactions
        $data['reversed_by'] = $_SESSION['id'];
        $data['reversed_date'] = date("Y-m-d H:i:s");
        $data['reverse_msg'] = $this->input->post('reverse_msg');
        $data['status_id'] = 3;

        $this->Loan_reversal_model->reverse_savings($data, $unique_id);

    }

    public function loan_installment_payments($trans_info)
    {

        $unique_id = $trans_info['unique_id'];
        $loan_id = $trans_info['client_loan_id'];

        #1. Get all payments matching the unique_id from loan_payments table
        $filter = "unique_id= '{$unique_id}' ";
        $payments = $this->Loan_reversal_model->get_loan_payments($filter);

        #2. Update the repayment schedule table
        foreach ($payments as $key => $payment) {

            $update_data = [
                'payment_status' => $payment['prev_payment_status'],
                'demanded_penalty' => $payment['prev_demanded_penalty'],
                'actual_payment_date' => $payment['prev_payment_date'],
            ];

            $this->Loan_reversal_model->update_schedule($update_data, $payment['repayment_schedule_id']);
        }

        #3. Delete Payments
        $filter = "client_loan_id = '{$loan_id}' AND unique_id = '{$unique_id}' ";
        $this->Loan_reversal_model->update_loan_payments($filter);

        #4. Update loan state
        $loan_state_data = [
            'state_id' => $trans_info['loan_state'],
            'client_loan_id' => $loan_id,
            'comment' => $this->input->post('reverse_msg')
        ];
        $this->loan_state_model->set($loan_state_data);


        #5. Reverse savings transactions
        $data['reversed_by'] = $_SESSION['id'];
        $data['reversed_date'] = date("Y-m-d H:i:s");
        $data['reverse_msg'] = $this->input->post('reverse_msg');
        $data['status_id'] = 3;

        $this->Loan_reversal_model->reverse_savings($data, $unique_id);

        #6. Do Journal Entry reversals 👇 
        $this->Loan_reversal_model->reverse_journals("unique_id = '{$unique_id}' ");
        #6. Do Journal Entry reversals 👆 

    }

    public function reverse_loan_disbursement($trans_info)
    {
        $unique_id = $trans_info['unique_id'];
        $loan_id = $trans_info['client_loan_id'];

        #1. Reverse Journal Entries 👇
        $this->Loan_reversal_model->reverse_journals("unique_id = '{$unique_id}' ");

        #2. Reverse Applied Loan Fees
        $this->Loan_reversal_model->reverse_applied_loan_fees("unique_id = '{$unique_id}' ");

        #3. Update loan state
        $data = [
            'state_id' => $trans_info['loan_state'],
            'client_loan_id' => $loan_id,
            'comment' => $this->input->post('reverse_msg')
        ];
        $this->loan_state_model->set($data);

        $filter = "unique_id = '{$unique_id}' AND status_id=1 AND client_loan_id= '{$loan_id}' ";
        $this->Loan_reversal_model->turn_off_schedules($filter);

        if(!empty($trans_info['linked_loan_id'])) {
            $linked_loan_data = [
                'state_id' => $trans_info['linked_loan_state_id'],
                'client_loan_id' => $trans_info['linked_loan_id'],
                'comment' => $this->input->post('reverse_msg')
            ];
            $this->loan_state_model->set($linked_loan_data);

            $this->Loan_reversal_model->update_refinaced_schedules("unique_id = '{$unique_id}' AND payment_status=5", ['payment_status' => 4]);
            
        }

        # Reverse client_loan table details
        $old_data = [
            'approved_installments' => $trans_info['loan_approved_installments'],
            'approval_note' => $trans_info['loan_approval_note'],
            'approved_repayment_made_every' => $trans_info['loan_approved_repayment_made_every'],
            'approved_repayment_frequency' => $trans_info['loan_approved_repayment_frequency'],
            'amount_approved' => $trans_info['loan_amount_approved'],
            'suggested_disbursement_date' => $trans_info['loan_suggested_disbursement_date'],
            'approval_date' => $trans_info['loan_approval_date'],
            'approved_by' => $trans_info['loan_approved_by'],
            'source_fund_account_id' => $trans_info['loan_source_fund_account_id'],
            'disbursed_amount' => $trans_info['loan_disbursed_amount'],
            // 'status_id' => $trans_info['loan_status_id'],
            'modified_by' => $trans_info['loan_modified_by'],
        ];
        $this->Loan_reversal_model->update_client_loan($old_data);

        # Reverse savings transactions
        $sv_data['reversed_by'] = $_SESSION['id'];
        $sv_data['reversed_date'] = date("Y-m-d H:i:s");
        $sv_data['reverse_msg'] = $this->input->post('reverse_msg');
        $sv_data['status_id'] = 3;

        $this->Loan_reversal_model->reverse_savings($sv_data, $unique_id);
        $this->Loan_reversal_model->reverse_mobile_money_transactions(['status_id => 3'], $unique_id);

    }
}
