<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Group_member extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->load->model("group_member_model");
        $this->load->library(array("form_validation"));
    }

    public function jsonList()
    {                    //used on membership tab and depositing from outside saving acc
        $data['data'] = $this->group_member_model->get_group_member_savings();
        echo json_encode($data);
    }

    public function jsonList_member_on_click()
    {
        //fetch members on deposit,withdraw in group details
        $data['group_members'] = $this->group_member_model->get_group_member_savings();
        //get_detail("group.id =" . $group_id);
        echo json_encode($data);
    }

    function check_member()
    {
        $group_members =  array_unique($this->input->post('group_member'), SORT_REGULAR);
        $empty = true;

        foreach ($group_members as $key => $value) {
            if (!empty($value['member_id'])) {
                $empty = false;
                $group_id = $value['group_id'];
                $member_id = $value['member_id'];
                $member =  $this->group_member_model->get2("gm.group_id='{$group_id}' AND gm.member_id='{$member_id}'");
                if (!empty($member)) {
                    $this->form_validation->set_message('check_member', 'Member - ' . $member[0]['client_no'] . ' is Already part of this group');

                    return false;
                }
            } else {
                $empty = $empty ? true : false; // Leave $empty as true if no member_id has been found 
            }
        }

        if ($empty) {
            $this->form_validation->set_message('check_member', 'Atleast One member is required.');
            return false;
        }

        return true;
    }

    public function create()
    {
        $this->form_validation->set_rules('group_member', 'Group member(s)', 'callback_check_member');



        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {

            $group_members = array_unique($this->input->post('group_member'), SORT_REGULAR);
            if (empty($group_members)) {
                $feedback['success'] = false;
                $feedback['message'] = "All fields are required";
            } else {
                if ($this->group_member_model->set()) {
                    $this->load->model("member_model");
                    //$feedback['available_group_members'] = $this->member_model->get_few_details("m.id NOT IN (SELECT member_id FROM fms_group_member WHERE group_id=" . $group_members[0]['group_id'] . ")");
                    $feedback['success'] = true;
                    $feedback['message'] = "Members successfully attached to the group";

                    $this->helpers->activity_logs($_SESSION['id'], 6, "Editing group", $feedback['message'], NULL, NULL);
                } else {
                    $feedback['success'] = false;
                    $feedback['message'] = "There was a problem attaching the member to the group";
                }
            }
        }



        echo json_encode($feedback);
    }

    public function delete()
    {
        $response['message'] = "Data could not be deleted, contact support.";
        $response['success'] = FALSE;
        if ($this->group_member_model->delete()) {
            $this->load->model("member_model");
            $response['available_group_members'] = $this->member_model->get_few_details("m.id NOT IN (SELECT member_id FROM fms_group_member WHERE group_id IN (SELECT group_id FROM fms_group_member WHERE id=" . $this->input->post('id') . "))");
            $response['success'] = TRUE;
            $response['message'] = "Data successfully deleted.";
        }
        echo json_encode($response);
    }

    public function change_status()
    {
        $response['message'] = "Group member details could not be deactivated, please try again or contact IT support.";
        $response['success'] = FALSE;
        if ($this->group_member_model->change_status()) {
            $response['success'] = TRUE;
            $response['message'] = "Group member details successfully deactivated.";
        }
        echo json_encode($response);
    }


     public function mark_group_leader()
    {
        $response['message'] = "Group Leader could not be assigned, please try again or contact IT support.";
        $response['success'] = FALSE;
        $group = $this->group_member_model->get("group_id=".$this->input->post('group_id')." AND group_leader =1");
        if (empty($group)) {

        if ($this->group_member_model->mark_group_leader()) {
            $response['success'] = TRUE;
            
            $response['message'] = "Group Leader Assigned successfully.";
        }
    }else{
      $response['message'] = "Please Remove the current Group Leader, Two or more group leaders are not allowed!";
    }
        echo json_encode($response);
    }

     public function unmark_group_leader()
    {
        $response['message'] = "Group Leader could not be assigned, please try again or contact IT support.";
        $response['success'] = FALSE;
    
        if ($this->group_member_model->mark_group_leader()) {
            $response['success'] = TRUE;
            
            $response['message'] = "Group Leader Removed successfully.";
        }
   
        echo json_encode($response);
    }
}
