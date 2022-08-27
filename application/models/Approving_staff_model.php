<?php

class Approving_staff_model extends CI_Model {

    Public function __construct() {
        parent :: __construct();
    }

    public function set() {
        $data = array(
            'staff_id' => $this->input->post('staff_id'),
            'rank' => $this->input->post('rank'),
            'approval_setting_id' => $this->input->post('approval_setting_id'),
            'date_created' => time(),
            'created_by' => $_SESSION['id']
        );

        $this->db->insert('approving_staff', $data);
        return $this->db->insert_id();
    }

    public function update($id = false) {
        if ($id == false) {
            $id = $this->input->post('id');
        }
        $data = array(
            'staff_id' => $this->input->post('staff_id'),
            'rank' => $this->input->post('rank'),
            'modified_by' => $_SESSION['id']
        );

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $query = $this->db->update('approving_staff', $data);
            return true;
        } else {
            return false;
        }
    }

    public function get($requested_amount = FALSE,$client_loan_id='') {
        $this->db->select("staff_id,rank,email,firstname,loan_approval_setting.id approval_setting_id,CASE WHEN staff_id IN (SELECT staff_id FROM `fms_loan_approval` JOIN `fms_staff` ON fms_staff.id=fms_loan_approval.staff_id JOIN `fms_user` ON fms_user.id=fms_staff.user_id WHERE fms_loan_approval.status_id=1 AND fms_loan_approval.client_loan_id=$client_loan_id) THEN 1 ELSE 0
            END AS approved_or_not");

        $this->db->from('approving_staff')->join('loan_approval_setting', 'loan_approval_setting.id=approving_staff.approval_setting_id','left');
        $this->db->join('staff', 'staff.id=approving_staff.staff_id','left');
        $this->db->join('user', 'user.id=staff.user_id','left');
        $this->db->where('approving_staff.status_id', 1);
        if ($requested_amount === FALSE) {
            $this->db->where('approving_staff.staff_id', $_SESSION['staff_id']);
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($requested_amount)) {
                $this->db->where('approving_staff.staff_id', $_SESSION['staff_id']);
                $this->db->where($requested_amount . ' >=loan_approval_setting.min_amount');
                $this->db->where($requested_amount . ' <=loan_approval_setting.max_amount');
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($requested_amount);
                $query = $this->db->get();
                
                return $query->result_array();
            }
        }
    }

    public function get_staffs($approval_setting_id = FALSE) {
        $this->db->select('approving_staff.*,gender,salutation,firstname,lastname, othernames');
        $query = $this->db->from('approving_staff')->join('staff', 'staff.id=approving_staff.staff_id')->join('user', 'user.id=staff.user_id');
        if ($approval_setting_id === FALSE) {
            $this->db->where('approving_staff.approval_setting_id', $this->input->post('approval_setting_id'));
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($approval_setting_id)) {
                $this->db->where('approving_staff.approval_setting_id', $approval_setting_id);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($approval_setting_id);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function approval_staff_list($requested_amount,$client_loan_id) {
        $this->db->select("staff_id,salutation,firstname,lastname, othernames,gender,rank,
            CASE 
                WHEN staff_id IN (SELECT staff_id FROM `fms_loan_approval` JOIN `fms_staff` ON fms_staff.id=fms_loan_approval.staff_id JOIN `fms_user` ON fms_user.id=fms_staff.user_id WHERE fms_loan_approval.client_loan_id=$client_loan_id) THEN 1
                ELSE 0
            END AS approved_or_not");
        $this->db->from('approving_staff')->join('staff', 'staff.id=approving_staff.staff_id')->join('user', 'user.id=staff.user_id');
        $this->db->join('loan_approval_setting', 'approving_staff.approval_setting_id=loan_approval_setting.id ');

        $this->db->where('approving_staff.status_id', 1);
        if ($requested_amount === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($requested_amount)) {                
                $this->db->where("loan_approval_setting.min_amount <=",$requested_amount);
                $this->db->where("loan_approval_setting.max_amount >=",$requested_amount);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->where($requested_amount);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
    
    public function already_added_staff($approval_setting_id = false, $staff_id = false){
        $this->db->select('staff_id');
        $query = $this->db->from('approving_staff');
        if ($approval_setting_id !== false) {
            $this->db->where('approving_staff.approval_setting_id', $approval_setting_id);
        }
        if ($staff_id === false) {
            $this->db->where('approving_staff.staff_id', $this->input->post('staff_id'));
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($staff_id)) {
                $this->db->where('approving_staff.staff_id', $staff_id);
                $query = $this->db->get();
                return $this->db->affected_rows();
            } else {
                $this->db->where($staff_id);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function deactivate() {
        $data = array(
            'status_id' => $this->input->post('status_id'),            
            'modified_by' =>$_SESSION['id']
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('approving_staff', $data);
    }

}
