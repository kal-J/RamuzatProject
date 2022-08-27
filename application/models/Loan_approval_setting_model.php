<?php
/**
 * Description of Loan_approval_setting_model
 *
 * @author Eric
 */
class Loan_approval_setting_model extends CI_Model {

    public function __construct() {
        parent :: __construct();
        $this->load->database();
        $this->num_of_attached_staff="(select approval_setting_id, COUNT(staff_id) AS _num_of_attached_staff from fms_approving_staff GROUP BY approval_setting_id)";
    }
    public function set($organisation_id=false) {        
        if ($organisation_id==false) {
            $organisation_id=isset($_SESSION['organisation_id'])?$_SESSION['organisation_id']:1;
        }
        $data = array(
                'organisation_id' => $organisation_id,
                'status_id' =>'1',
                'min_amount' =>$this->input->post('min_amount'), 
                'max_amount' =>$this->input->post('max_amount'), 
                'min_approvals' =>$this->input->post('min_approvals'),
                'date_created' =>time(),
                'created_by' =>$_SESSION['id']                
            );

        $this->db->insert('loan_approval_setting', $data);
        return $this->db->insert_id();
    }
    public function update($settings_id=false) {        
        if ($settings_id==false) {
            $settings_id=$this->input->post('id');
        }
        $data = array(
                'min_amount' =>$this->input->post('min_amount'), 
                'max_amount' =>$this->input->post('max_amount'),
                'min_approvals' =>$this->input->post('min_approvals'),
                'modified_by' =>$_SESSION['id']                
            );

        if (is_numeric($settings_id)) {
            $this->db->where('id',$settings_id);
            $query=$this->db->update('loan_approval_setting', $data);
            return true;
        }else{
            return false;
        }

    }
     //getting loan approval setting
    public function get($filter = FALSE) {
        $this->db->select("loan_approval_setting.*,ifnull(_num_of_attached_staff,0) num_of_attached_staff");
        $query = $this->db->from('loan_approval_setting')->join("$this->num_of_attached_staff approving_staff",'approving_staff.approval_setting_id=loan_approval_setting.id','left');
        $this->db->where('loan_approval_setting.status_id',1);
        if ($filter === FALSE) {
            $this->db->where('loan_approval_setting.organisation_id',$_SESSION['organisation_id']);
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('loan_approval_setting.organisation_id',$_SESSION['organisation_id']);//
                $this->db->where('loan_approval_setting.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
    public function min_approvals($requested_amount) {
        $this->db->select('min_approvals AS required_approvals');
        $query = $this->db->from('loan_approval_setting');
        if (is_numeric($requested_amount)) {
            $this->db->where($requested_amount.' >=loan_approval_setting.min_amount');
            $this->db->where($requested_amount.' <=loan_approval_setting.max_amount');
            $this->db->where(' status_id=',1);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $query = $this->db->get();
            return $query->row_array();
        }
    }
    public function validate_range_value($amount) {
        $id=$this->input->post('id'); 
        if ($id === NULL || empty($id)) {
            $query_result = $this->db
                    ->limit(1)
                    ->where('organisation_id=', $_SESSION['organisation_id'])
                    ->where($amount . " BETWEEN ", "`min_amount` AND `max_amount`", FALSE)
                    ->get('loan_approval_setting');
            return ($query_result->num_rows() === 0);
        }else{
            $query_result = $this->db
                ->limit(1)
                ->where('organisation_id=', $_SESSION['organisation_id'])
                ->where('id=', $id)                
                ->where("min_amount=",$amount)
                ->or_where("max_amount=",$amount)
                ->get('loan_approval_setting');
            if ($query_result->num_rows() === 1) {
                return TRUE;
            }else{                
                $query_result = $this->db
                        ->limit(1)
                        ->where('organisation_id=', $_SESSION['organisation_id'])
                        ->where($amount . " BETWEEN ", "`min_amount` AND `max_amount` ", FALSE)
                        ->where("'{$amount}'" . " <> ", "`min_amount` OR ")
                        ->where("'{$amount}'" . " > ", "`max_amount` ")
                        ->get('loan_approval_setting');
                return ($query_result->num_rows() === 0);
            }
        }
    }

}
