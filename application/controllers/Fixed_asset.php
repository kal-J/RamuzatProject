<?php
/**
 * Description of Fixed Asset controller
 *
 * @author reagan
 */
class Fixed_asset extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('fixed_asset_model');
        $this->load->library(array("form_validation", "helpers"));

        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->data['privilege_list'] = $this->helpers->user_privileges(8, $this->session->userdata('staff_id'));
        $this->data['fiscal_list'] = $this->helpers->user_privileges(20, $_SESSION['staff_id']);
       
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['accounts_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
            $this->data['fiscal_privilege'] = array_column($this->data['fiscal_list'], "privilege_code");
        }
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
        $where = FALSE;
        if ($this->input->post('organisation_id') !== NULL) {
            $where = "organisation_id = " . $this->input->post('organisation_id');
        }
        $data['data'] = $this->fixed_asset_model->get($where);
        echo json_encode($data);
    }

    public function view($id) {
        $this->load->model('transactionChannel_model');
        $neededcss = array("fieldset.css");
        $neededjs = array("plugins/validate/jquery.validate.min.js");
        $this->helpers->dynamic_script_tags($neededjs, $neededcss);

        $this->data['fixed_asset'] = $this->fixed_asset_model->get($id);
        if (empty($this->data['fixed_asset'])) {
            redirect("my404");
        }

        $this->data['transaction_channels'] = $this->transactionChannel_model->get();
        $this->data['title'] = $this->data['sub_title'] = $this->data['fixed_asset']['asset_name'];
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->template->content->view('accounts/fixed_asset/view', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function create() {
        $this->form_validation->set_rules('description', 'description', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('asset_name', 'Asset Name', array('required'), array('required' => '%s must be entered'));
        $this->form_validation->set_rules('asset_account_id', 'Asset Account', array('required'), array('required' => '%s must be selected'));
        $this->form_validation->set_rules('account_pay_with_id', ' Account', array('required'), array('required' => 'Select account credited'));
        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            $this->load->model('ledger_model');
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->fixed_asset_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Fixed asset details successfully updated";
                    $feedback['assets'] = $this->fixed_asset_model->get($_POST['id']);
                } else {
                    $feedback['message'] = "There was a problem updating the assets details";
                }
            } else {
                $asset_id = $this->fixed_asset_model->set();
                if (is_numeric($asset_id)) {
                    //insert the ledger transaction data
                    $asset_transaction_data = [
                        "debit_account_id" => "asset_account_id",
                        "credit_account_id" => "account_pay_with_id",
                        "amount" => "purchase_cost",
                        "narrative" => "description",
                        "transaction_date" => "purchase_date"
                    ];
                    $this->ledger_model->auto_transaction($asset_transaction_data);
                    $this->auto_payment($asset_id);
                    $feedback['success'] = true;
                    $feedback['message'] = "Fixed asset details successfully saved";
                } else {
                    $feedback['message'] = "There was a problem saving the assets data";
                }
            }
        }
        echo json_encode($feedback);
    }

    private function auto_payment($asset_id) {
        $payment_mode_id = $this->input->post('payment_mode_id');
        if ($payment_mode_id == 1 || $payment_mode_id == 2) {
            $this->load->model('asset_payment_model');
            $payment_data = [
                'amount' => $this->input->post('purchase_cost'),
                'transaction_date' => $this->input->post('purchase_date'),
                'narrative' => "Payment for ".$this->input->post('asset_name')." [".$this->input->post('identity_no')."]",
                'fixed_asset_id' => $asset_id,
                'transaction_channel_id' => 1
            ];
            $asset_payment_id = $this->asset_payment_model->set($payment_data);
            return is_numeric($asset_payment_id);
        } else {
            return FALSE;
        }
    }

    public function delete() {
        $response['message'] = "Fixed asset details could not be deleted, contact support.";
        $response['success'] = FALSE;
        if ($this->fixed_asset_model->delete($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] = "Fixed asset details successfully deleted.";
        }
        echo json_encode($response);
    }

    public function change_status() {
        $msg = $this->input->post('status_id') == 1 ? "" : "de";
        $response['message'] = "Fixed asset details could not be $msg activated, contact IT support.";
        $response['success'] = FALSE;
        if ($this->fixed_asset_model->deactivate($this->input->post('id'))) {
            $response['message'] = "Fixed asset details successfully been $msg activated.";
            $response['success'] = TRUE;
            echo json_encode($response);
        }
    }

}
