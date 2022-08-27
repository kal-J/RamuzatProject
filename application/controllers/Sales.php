<?php

/**
 * Description of Sales
 *
 * @author Joshua
 */
class Sales extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('sales_model');
        $this->load->model('Savings_account_model');
        $this->load->model('accounts_model');
        $this->load->model('transactionChannel_model');
        $this->load->model('journal_transaction_model');
        $this->load->model('DepositProduct_model');
        $this->load->model('transaction_model');
        $this->load->model('Loan_guarantor_model');
        $this->load->model('journal_transaction_line_model');
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->data['privilege_list'] = $this->rolePrivilege_model->get_user_privileges($module_id = 8, $this->session->userdata('staff_id'));
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['approval_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
        }
    }

    public function jsonList()
    {
        $organisation_id = $this->input->post('organisation_id') ? $this->input->post('organisation_id') : $_SESSION['organisation_id'];
        $filter = "st.organisation_id='{$organisation_id}' ";
        $data['data'] = $this->sales_model->get();

        echo json_encode($data);
    }

    public function jsonList_items()
    {
        $organisation_id = $this->input->post('organisation_id') ? $this->input->post('organisation_id') : $_SESSION['organisation_id'];
        $filter = "st.organisation_id='{$organisation_id}' ";
        $data['data'] = $this->sales_model->get_items();

        echo json_encode($data); die;
    }

    public function create()
    {
        $this->form_validation->set_rules('amount', 'Amount', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('savings_account_id', 'Savings account', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('narrative', 'Narrative', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('income_account_id', 'Income Account Number', array('required'), array('required' => '%s must be provided'));
        $this->form_validation->set_rules('transaction_date', 'transaction date', array('required'), array('required' => '%s must be provided'));

        $feedback['success'] = false;

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $where = $this->input->post('savings_account_id');
             $savings_data = $this->Loan_guarantor_model->get_guarantor_savings2('j.state_id=7', $this->input->post('savings_account_id'));
                $current_balance = $savings_data['cash_bal'];
                if ($current_balance >= $this->input->post('amount')) {
            $this->db->trans_begin();

            $sales_transaction = $this->sales_model->set();

            $charge['transaction_no'] = date('ymdhms') . mt_rand(100, 999);
            $charge['account_no_id'] = $this->input->post('savings_account_id');
            $charge['amount'] =  $this->input->post('amount');
            $charge['transaction_type_id'] = 4;
            $charge['ref_no'] = $sales_transaction['ref_no'];
            $charge['payment_id'] = 5;
            $charge['transaction_date'] =  $this->input->post('transaction_date');
            $charge['narrative'] = $this->input->post('narrative');
            $charge['date_created'] = time();
            $charge['created_by'] = $_SESSION['id'];

            $savings_transaction = $this->transaction_model->deduct_savings($charge);

            $savings_product_id = $this->Savings_account_model->get($where);

            if (!empty($sales_transaction) && !empty($savings_transaction)) {
                //echo json_encode($sales_transaction); die();
                $this->do_sales_journal($sales_transaction, $savings_product_id['deposit_Product_id'], $this->input->post('income_account_id'));
            }


            if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_commit();
                $feedback['success'] = TRUE;
                $feedback['message'] =  " Sale transaction successful";
            } else {
                $this->db->trans_rollback();
                $feedback['success'] = FALSE;
                $feedback['message'] = "A problem happened while recording the transaction. Please Try again later or contact the system administrator";
            }
             } else {
            $feedback['success'] = FALSE;

                $feedback['message'] = "Insufficient balance to complete the payment";

            }
        }

        echo json_encode($feedback);
    }

    public function create_item()
    {
        $this->form_validation->set_rules('name', 'Item Name', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('narrative', 'Narrative', array('required'), array('required' => '%s must be entered'));

        $feedback['success'] = false;

        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {

            $result = $this->sales_model->set_for_items();
            if ($result) {
                $feedback['success'] = TRUE;
                $feedback['message'] =  " Item added successfully";
            }
        }
        echo json_encode($feedback);
    }

    public function deactivate($id) {
        $result = $this->sales_model->deactivate_item($id);
        if ($result) {
            $feedback['success'] = TRUE;
            $feedback['message'] =  " Item Deactivated successfully";
        }

        echo json_encode($feedback);
    }

    private function do_sales_journal($transaction_data, $savings_product_id, $transaction_channel)
    {

        if ($this->input->post('transaction_date') != NULL && $this->input->post('transaction_date') != '') {
            $date =  $this->input->post('transaction_date');
        } else {
            $date = date('d-m-Y');
        }

        $sale_amount = round($this->input->post('amount'), 2);
        //echo json_encode($sale_amount); die();
        $data = [
            'transaction_date' => $date,
            'description' => $this->input->post('narrative'),
            'ref_no' => $transaction_data['ref_no'],
            'ref_id' => $transaction_data['transaction_no'],
            'status_id' => 1,
            'journal_type_id' => 14,
        ];
        //then we post this to the journal transaction
        //echo json_encode($data); die();
        $journal_transaction_id = $this->journal_transaction_model->set($data);
        unset($data);
        //echo json_encode($journal_transaction_id); die();
        $savings_product_details = $this->DepositProduct_model->get($savings_product_id);
        if (empty($savings_product_details)) {
            $feedback = array(
                'success' => false,
                'message' => 'Deposit transaction failed when performing a Journal line transaction. Check if you have savings products assigned and active for your Branch.'
            );
            echo json_encode($feedback);
            die;
        }

        //$debit_or_credit1 = $this->accounts_model->get_normal_side(, true);
        //echo json_encode($debit_or_credit1); die;
        //$debit_or_credit2 = $this->accounts_model->get_normal_side($transaction_channel, true);

        //if deposit amount has been received
        if ($sale_amount != null && !empty($sale_amount) && $sale_amount != '0') {
            $data[0] = [
                'debit_amount' => $sale_amount,
                'reference_no' => $transaction_data['ref_no'],
                'reference_id' => $transaction_data['transaction_no'],
                'transaction_date' => $date,
                'narrative' => "Sales transaction made on " . $date. " ".$this->input->post('narrative'),
                'account_id' => $savings_product_details['savings_liability_account_id'],
                'status_id' => 1,
            ];
            $data[1] = [
                'credit_amount' => $sale_amount,
                'reference_no' => $transaction_data['ref_no'],
                'reference_id' => $transaction_data['transaction_no'],
                'transaction_date' => $date,
                'narrative' => "Sales transaction made on " . $date . " ".$this->input->post('narrative'),
                'account_id' => $transaction_channel,
                'status_id' => 1,
            ];

            $this->journal_transaction_line_model->set($journal_transaction_id, $data);
        }
        //end of the if
    }
}
