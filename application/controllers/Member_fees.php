<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Member_fees extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->library("session");
    if (empty($this->session->userdata('id'))) {
      redirect('welcome');
    }
    $this->load->model("member_fees_model");
    $this->load->model("user_model");
  }

  public function jsonList()
  {
    $data['data'] = $this->member_fees_model->get();
    echo json_encode($data);
  }

  public function create()
  {
    $this->load->library('form_validation');
    $this->form_validation->set_rules('feename', 'Fee name', 'required');
    $this->form_validation->set_rules('amount', 'Amount', 'required');
    $this->form_validation->set_rules('requiredfee', 'Required fee', 'required');
    $this->form_validation->set_rules('description', 'Description', 'required');
    $this->form_validation->set_rules('chargetrigger_id', 'Charge Trigger', 'required');
    if ($this->input->post("chargetrigger_id") == 3) {
      $this->form_validation->set_rules('dateapplicationmethod_id', 'Date Application method', 'trim|htmlentities');
      $this->form_validation->set_rules('repayment_made_every', 'Frequency of Payment', 'required', array('required' => '%s must be entered'));
      $this->form_validation->set_rules('repayment_frequency', 'Frequency of Payment', 'required', array('required' => '%s must be entered'));
    }
    $feedback['success'] = false;

    if ($this->form_validation->run() === FALSE) {
      $feedback['message'] = validation_errors('<li>', '</li>');
    } else {
      if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
        if ($this->member_fees_model->update()) {
          $feedback['success'] = true;
          $feedback['message'] = "Member fees Details successfully updated";
          //activity log 
          $this->helpers->activity_logs($_SESSION['id'], 18, "Editing member fee", $feedback['message'] . " # " . $this->input->post('id'), NULL, "id #" . $this->input->post('id'));
        } else {
          $feedback['message'] = "There was a problem updating the member fees data, please try again";

          $this->helpers->activity_logs($_SESSION['id'], 18, "Editing member fee", $feedback['message'] . " # " . $this->input->post('id'), NULL, "id #" . $this->input->post('id'));
        }
      } else {
        if ($this->member_fees_model->set()) {
          $feedback['success'] = true;
          $feedback['message'] = "Member fees data has successfully been added";

          $this->helpers->activity_logs($_SESSION['id'], 18, "Creating member fee", $feedback['message'] . " # " . $this->input->post('feename'), NULL, "Fee #" . $this->input->post('feename'));
        } else {
          $feedback['message'] = "There was a problem saving the member fees data, please try again";

          $this->helpers->activity_logs($_SESSION['id'], 18, "Creating member fee", $feedback['message'] . " # " . $this->input->post('feename'), NULL, "Fee #" . $this->input->post('feename'));
        }
      }
    }
    echo json_encode($feedback);
  }


  public function change_status()
  {
    $data['message'] = "Access denied. You do not have the permission to perform this operation, contact the admin for further assistance.";
    $data['success'] = FALSE;

    $this->helpers->activity_logs($_SESSION['id'], 18, "Deleting member fee", $data['message'] . " # " . $this->input->post('id'), NULL, "id #" . $this->input->post('id'));
    $data['message'] = $this->member_fees_model->change_status_by_id();
    if ($data['message'] === true) {
      $data['success'] = TRUE;
      $data['message'] = "Data successfully DELETED.";

      $this->helpers->activity_logs($_SESSION['id'], 18, "Deleting member fee", $data['message'] . " # " . $this->input->post('id'), NULL, "id #" . $this->input->post('id'));
    }
    echo json_encode($data);
  }
}
