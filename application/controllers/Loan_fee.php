<?php
/**
 * Description of Loan Fee
 *
 * @author Eric
 */
class Loan_fee extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->load->model('loan_fees_model');
    }


    public function jsonlist() {
        $all_data =$this->loan_fees_model->get();
        $new_ranges = [];
        foreach ($all_data as $mydata) {
        $mydata['ranges'] = $this->loan_fees_model->get_range_fees("loan_fee_id=" . $mydata['id']);
            $new_ranges[] = $mydata;
        }
        $data['data'] = $new_ranges;
        echo json_encode($data);
    }


    public function create() {
        $this->form_validation->set_rules('feename', 'Fee name', array('required', 'min_length[2]', 'max_length[230]'), array('required' => '%s must be entered'));

        $form_rangeFees = $this->input->post('rangeFees');
        $loan_fee_id = $this->input->post('id');

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                 $loan_fee = $this->loan_fees_model->update();
                if(($this->input->post('rangeFees')!=NULL) &&($this->input->post('rangeFees')!="")){
                    $db_existing_range_fees = $this->loan_fees_model->get_range_fees("loan_fee_id=" . $loan_fee_id);
                    $range_fees_deleted_array = $this->get_deleted_elements($db_existing_range_fees, $form_rangeFees);
                    $this->loan_fees_model->remove($loan_fee_id, $range_fees_deleted_array);
                    $loan_fee_ranges = $this->loan_fees_model->insert_range_fees($loan_fee_id, $form_rangeFees);
                }
                if ($loan_fee) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan Fee details successfully updated";
                    $feedback['loan_fee'] = $this->loan_fees_model->get($_POST['id']);
                    //activity log 

                     $this->helpers->activity_logs($_SESSION['id'],18,"Editing loan fee",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the loan fee details";

                      $this->helpers->activity_logs($_SESSION['id'],18,"Editing loan fee",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                }
            } else {
                $loan_fee_id = $this->loan_fees_model->set();
                if ($loan_fee_id) {
                    if(($this->input->post('rangeFees')!=NULL) &&($this->input->post('rangeFees')!="")){
                        $this->loan_fees_model->insert_range_fees($loan_fee_id,$form_rangeFees);
                     }
                    $feedback['success'] = true;
                    $feedback['message'] = "Loan fee details successfully saved";

                      $this->helpers->activity_logs($_SESSION['id'],18,"Creating loan fee",$feedback['message']." # ". $loan_fee_id,NULL,"id #". $loan_fee_id);
                } else {
                    $feedback['message'] = "There was a problem saving the loan fee data";

                       $this->helpers->activity_logs($_SESSION['id'],18,"Creating loan fee",$feedback['message']." # ". $loan_fee_id,NULL,"id #". $loan_fee_id);
                }
            }
        }
        echo json_encode($feedback);
    }
  private function get_deleted_elements($db_array, $form_array) {
        $delete_array = [];
        if (is_array($form_array)) {
            foreach ($db_array as $db_array_el) {
                $search_result = $this->find_in_array($db_array_el, $form_array);
                if ($search_result === FALSE) {
                    $delete_array[] = $db_array_el['id'];
                }
                //then add it to the $delete_array
            } /* */
        }
        return $delete_array;
    }

    private function find_in_array($elem, $array) {
        foreach ($array as $array_item) {
            if (isset($array_item['id']) && $array_item['id'] === $elem['id']) {
                return TRUE;
            }
        }
        return FALSE;
    }

     public function delete() {
        $response['message'] = "Loan Fee could not be deleted, contact IT support.";

          $this->helpers->activity_logs($_SESSION['id'],18,"Deleting loan fee",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
         $response['success'] = FALSE;

        if ($this->loan_fees_model->delete_by_id($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] = "Loan Fee successfully deleted.";

             $this->helpers->activity_logs($_SESSION['id'],18,"Deleting loan fee",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        }

        
        echo json_encode($response);
    }

    public function change_status() {
        $response['message'] = "Loan Fee could not be deactivated, contact IT support.";

        
        $response['success'] = FALSE;

         $this->helpers->activity_logs($_SESSION['id'],18,"Deactivating loan fee",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));

        if ($this->loan_fees_model->change_status_by_id($this->input->post('id'))) {
            $response['message'] = "Loan Fee successfully deactivated.";
            $response['success'] = TRUE;

             $this->helpers->activity_logs($_SESSION['id'],18,"Deactivating loan fee",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
            echo json_encode($response);
        }
    }
}
