<?php
/**
 * Description of Share insuance model
 *
 * @author Reagan
 */
class Share_issuance_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select("share_issuance.*,made_every_name,concat(ac.account_code,' ', ac.account_name) share_account");
        $this->db->from('share_issuance')->join('repayment_made_every', 'repayment_made_every.id=share_issuance.lock_in_period_id','left');
        $this->db->join("accounts_chart ac", "share_issuance.share_capital_account_id=ac.id", "LEFT");
        if ($filter === FALSE) {
            $this->db->where('share_issuance.status_id IN (1,2)');
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)){ 
                $this->db->where('share_issuance.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['tbl']);

        $closing_date = explode('-', $data['closing_date'], 3);
        $data['closing_date'] = count($closing_date) === 3 ? ($closing_date[2] . "-" . $closing_date[1] . "-" . $closing_date[0]) : null;
        $date_of_issue = explode('-', $data['date_of_issue'], 3);
        $data['date_of_issue'] = count($date_of_issue) === 3 ? ($date_of_issue[2] . "-" . $date_of_issue[1] . "-" . $date_of_issue[0]) : null;

        $data['date_created'] = time();
        $data['status_id'] = 1;
        $data['created_by'] =$_SESSION['id'];

        $this->db->insert('share_issuance', $data);
        $insert_id= $this->db->insert_id();
       // $this->set_firstcall($insert_id);
        return $insert_id;
    }
	
    public function update() {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id']);
        $closing_date = explode('-', $data['closing_date'], 3);
        $data['closing_date'] = count($closing_date) === 3 ? ($closing_date[2] . "-" . $closing_date[1] . "-" . $closing_date[0]) : null;
        $date_of_issue = explode('-', $data['date_of_issue'], 3);
        $data['date_of_issue'] = count($date_of_issue) === 3 ? ($date_of_issue[2] . "-" . $date_of_issue[1] . "-" . $date_of_issue[0]) : null;

        $data['modified_by'] = $_SESSION['id'];
       //$this->update_firstcall($this->input->post('id'));
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('share_issuance', $data);
    }

     public function set_firstcall($insert_id) {
        $data['call_name'] = 'First Call';
        $data['percentage'] = $this->input->post('first_call_percent');
        $data['issuance_id'] = $insert_id;
        $data['status_id'] = '1';
        $data['first_call'] = '1';
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['staff_id'];
        $this->db->insert('share_calls', $data);
        return $this->db->insert_id();
    }

    public function update_firstcall() {
        $data['modified_by'] = $_SESSION['id'];
        $data['percentage'] = $this->input->post('first_call_percent');
        $data['status_id'] = '1';
        $data['first_call'] = '1';
        $this->db->where('first_call', 1);
        $this->db->where('issuance_id', $this->input->post('id'));
        return $this->db->update('share_calls', $data);
    }

    public function validate_percentage($pecentage) {
            $this->db->select('SUM(IFNULL(percentage,0)) as total_percentage');
            $this->db->from('share_calls');
            $this->db->where('share_calls.status_id', 1);
            $this->db->where('share_calls.issuance_id', $this->input->post('id'));
            $this->db->where_not_in('share_calls.first_call', 1);
            $query = $this->db->get();
            return $query->row_array();
        }
    /**
     * This method Deactivates share_issuance data from the database
     */

      public function get_accounts($filter = FALSE){
        $this->db->select('*');
        $this->db->from('share_issuance');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("share_issuance.id", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
    public function delete_by_id($id = false) {

         $id = $this->input->post('id');
        $data = array('status_id' =>0);
        $this->db->where('id', $id);
        $query = $this->db->update('share_issuance',$data);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    public function change_status_by_id($id = false) {

            $id = $this->input->post('id');
            $data = array('status_id' =>$this->input->post('status_id'));
            $this->db->where('id', $id);
            $query = $this->db->update('share_issuance',$data);
            if ($query) {
                return true;
            } else {
                return false;
            }
    }
}
