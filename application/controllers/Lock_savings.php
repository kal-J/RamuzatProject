<?php
/**
 * Description of Lock_savings
 *
 * @author REAGAN
 */
class Lock_savings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('Lock_savings_model');
        $this->load->model('Loan_guarantor_model');
    }

    public function jsonList(){
        $where = FALSE;
        if (isset($_POST['acc_id'])===TRUE) {
            $where = "saving_account_id=". $this->input->post("acc_id");
        }
        $data['data'] = $this->Lock_savings_model->get($where);
        echo json_encode($data);
    }

    public function create(){

        $this->form_validation->set_rules('amountcalculatedas_id', 'Locked As', array('required'), array('required' => 'Please select %s'));
        if($this->input->post('amountcalculatedas_id')==2){
        $this->form_validation->set_rules('amount', 'Amount', array('required'), array('required' => '%s must be entered'));
        } else {
        $this->form_validation->set_rules('percentage', 'Percentage', array('required'), array('required' => '%s must be entered'));
        }
        $this->form_validation->set_rules('narrative', 'Narrative', array('required'), array('required' => '%s must be Provided'));
        $this->form_validation->set_rules('locked_date', 'Narrative', array('required'), array('required' => '%s must be selected'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->Lock_savings_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Locked amount successfully updated";
                    $feedback['nextofkin'] = $this->Lock_savings_model->get($_POST['id']);
                    $feedback['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings2("(j.state_id = 5 OR j.state_id = 7 OR j.state_id = 12 OR j.state_id = 17 OR j.state_id = 18)", $_POST['saving_account_id']);
                } else {
                    $feedback['message'] = "There was a problem updating Locked amount";

                     $this->helpers->activity_logs($_SESSION['id'],6,"Editing lock amount",$feedback['message']." # ".$this->input->post('id'),NULL,$this->input->post('id'));
                }
            } else {
                $locked_id = $this->Lock_savings_model->set();
                if ($locked_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Amount successfully Locked";
                    $feedback['nextofkin'] = $this->Lock_savings_model->get($locked_id);
                    $feedback['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings2("(j.state_id = 5 OR j.state_id = 7 OR j.state_id = 12 OR j.state_id = 17 OR j.state_id = 18)", $_POST['saving_account_id']);

                      $this->helpers->activity_logs($_SESSION['id'],6,"Editing lock amount",$feedback['message']." # ". $locked_id,NULL,$locked_id);

                } else {
                    $feedback['message'] = "There was a problem, locking failed, contact IT support";

                     $this->helpers->activity_logs($_SESSION['id'],6,"Editing lock amount",$feedback['message']." # ". $locked_id,NULL,$locked_id);
                }
            }
        }
        echo json_encode($feedback);
    }

    function unlock() {
        $response['message'] = "You do not have rights to unlock this amount";
        $response['success'] = FALSE;
        
         $this->helpers->activity_logs($_SESSION['id'],6,"Editing lock amount",$response['message']." # ". $this->input->post('id'),NULL,$this->input->post('id'));

            if (($response['success'] = $this->Lock_savings_model->delete($this->input->post('id'))) === true) {
                $response['message'] = "Savings amount unlocked";
                 $response['accounts'] = $this->Loan_guarantor_model->get_guarantor_savings2("(j.state_id = 5 OR j.state_id = 7 OR j.state_id = 12 OR j.state_id = 17 OR j.state_id = 18)", $_POST['acc_id']);

                  $this->helpers->activity_logs($_SESSION['id'],6,"Editing lock amount",$response['message']." # ". $this->input->post('id'),NULL,$this->input->post('id'));

                
            }
        echo json_encode($response);
    }
}
