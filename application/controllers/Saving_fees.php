<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Saving_fees extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model("Saving_fees_model");
    }

    public function jsonlist() {
        $all_data =$this->Saving_fees_model->get_saving_fees();
        $new_ranges = [];
        foreach ($all_data as $mydata) {
        $mydata['ranges'] = $this->Saving_fees_model->get_range_fees("saving_fee_id=" . $mydata['id']);
            $new_ranges[] = $mydata;
        }
        $data['data'] = $new_ranges;
        echo json_encode($data);
    }

    public function create() {
        $this->form_validation->set_rules('feename', 'Fee name', array('required', 'min_length[2]'), array('required' => '%s must be entered'));
        
       
        $this->form_validation->set_rules('chargetrigger_id', 'Charge Trigger', 'required');
        if ($this->input->post("chargetrigger_id") == "Monthly") {
            $this->form_validation->set_rules('dateapplicationmethod_id', 'Date Application method', 'trim|htmlentities');
            $this->form_validation->set_rules('repayment_made_every', 'Frequency of Payment','required', array('required' => '%s must be entered'));
            $this->form_validation->set_rules('repayment_frequency', 'Frequency of Payment','required', array('required' => '%s must be entered'));
        }

        $this->form_validation->set_rules('cal_method_id', 'Calculation Method', 'required', array('required' => '%s must be entered'));
        $this->form_validation->set_rules('fee_type', 'Fee Type', 'required', array('required' => '%s must be entered'));
        /* if($this->input->post("taxable")=="on"){
          $this->form_validation->set_rules('tax','Tax','required', array('required' => '%s must be entered'));
          } */

        $form_rangeFees = $this->input->post('rangeFees');
        $saving_fee_id = $this->input->post('id');

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                 $saving_fee = $this->Saving_fees_model->update();
                if(($this->input->post('rangeFees')!=NULL) &&($this->input->post('rangeFees')!="")){
                $db_existing_range_fees = $this->Saving_fees_model->get_range_fees("saving_fee_id=" . $saving_fee_id);
                $range_fees_deleted_array = $this->get_deleted_elements($db_existing_range_fees, $form_rangeFees);
                $this->Saving_fees_model->remove($saving_fee_id, $range_fees_deleted_array);
                $saving_fee_ranges = $this->Saving_fees_model->insert_range_fees($saving_fee_id, $form_rangeFees);
               }
                if ($saving_fee) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Fee successfully updated";

                    //activity log 
                    $this->helpers->activity_logs($_SESSION['id'],18,"Editing saving fee",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));

                } else {
                    $feedback['message'] = "There was a problem updating the fee";

                      $this->helpers->activity_logs($_SESSION['id'],18,"Editing saving fee",$feedback['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                }
            } else {
                $checker = $this->Saving_fees_model->set();
                if ($checker) {
                    if(($this->input->post('rangeFees')!=NULL) &&($this->input->post('rangeFees')!="")){
                        $this->Saving_fees_model->insert_range_fees($checker,$form_rangeFees);
                     }
                    $feedback['success'] = true;
                    $feedback['message'] = "Fee successfully saved";

                      $this->helpers->activity_logs($_SESSION['id'],18,"Creating saving fee",$feedback['message']." # ".$this->input->post('feename'),NULL,"feename #".$this->input->post('feename'));
                } else {
                    $feedback['message'] = "There was a problem saving the fee";

                     $this->helpers->activity_logs($_SESSION['feename'],18,"Creating saving fee",$feedback['message']." # ".$this->input->post('feename'),NULL,"Fee #".$this->input->post('feename'));
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
    function delete() {
        //if user not logged in, take them to the login page
        $response['message'] = "You do not have access to delete this record";
        $response['success'] = FALSE;

         $this->helpers->activity_logs($_SESSION['id'],18,"Deleting saving fee",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        //if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        if (($response['success'] = $this->Saving_fees_model->delete($this->input->post('id'))) === true) {
            $response['message'] = "Saving fees details successfully deleted";

             $this->helpers->activity_logs($_SESSION['id'],18,"Deleting saving fee",$response['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        }
        // }
        echo json_encode($response);
    }

}
