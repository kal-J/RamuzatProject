<?php
/**
 * Description of Loan_approval_model
 *
 * @author Eric, Melchisedec
 */
class Loan_approval_model extends CI_Model {

    public function __construct() {
        parent :: __construct();
        $this->load->database();
    }
    public function set( $client_loan_id=false, $unique_id=false ) {
        //Action date conversation
        $sent_date = explode('-', $this->input->post('action_date'),3);
        $action_date=($this->input->post('application_date') != NULL)?$this->helpers->yr_transformer($this->input->post('application_date')):(($this->input->post('action_date')  != NULL)?$this->helpers->yr_transformer($this->input->post('action_date')):$this->helpers->yr_transformer(date('Y-m-d')) );
        if ($client_loan_id==false) {
            $client_loan_id=$this->input->post('client_loan_id');
        }
        $data = array(
                'client_loan_id' => $client_loan_id,
                'staff_id' =>$_SESSION['staff_id'], 
                'date_created' =>time(),
                'status_id' =>1,
                'action_date' => $action_date,
                'comment' =>$this->input->post('comment'),
                'created_by' =>$_SESSION['id'],
                'unique_id' => $unique_id             
            );
        
        $this->db->insert('loan_approval', $data);
        return $this->db->insert_id();
    }

    //getting loan approvals
    public function get($filter = FALSE) {
        $this->db->select("loan_approval.*,suggested_disbursement_date,amount_approved,concat(firstname,' ', lastname,' ', othernames) staff_name,amount_approved, a.rank");
        $query = $this->db->from('loan_approval')
                ->join('client_loan','client_loan.id=loan_approval.client_loan_id');
        $this->db->join('staff','staff.id = loan_approval.staff_id');
        $this->db->join('fms_approving_staff a','staff.id = a.staff_id')
                ->join('user','user.id=staff.user_id');
        $this->db->order_by("action_date", "DESC");
        if ($this->input->post('status_id') !='') {
            $this->db->where('loan_approval.status_id',$this->input->post('status_id'));
        }
        if ($filter === FALSE) {
            $this->db->where('loan_approval.client_loan_id',$this->input->post('client_loan_id'));
            $query = $this->db->get();
            // print_r( $this->db->last_query($query) ); die;
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('loan_approval.client_loan_id=' . $filter);
                $query = $this->db->get();
                // print_r( $this->db->last_query($query) ); die;
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                // print_r( $this->db->last_query($query) ); die;
                return $query->result_array();
            }
        }
    }

    public function sum_approvals($filter = FALSE) {
        $this->db->select('COUNT(client_loan_id) AS approvals');
        $query = $this->db->from('loan_approval')->limit(1, 0);
         $this->db->where('loan_approval.status_id=1');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('loan_approval.client_loan_id=' . $filter);
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

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' =>0);
            $this->db->where('id', $id);
            $query = $this->db->update('loan_approval',$data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $data = array('status_id' =>0);
            $query = $this->db->update('loan_approval',$data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function deactivate_loan_approvals($id = false) {

        if ($id === false) {
            $id = $this->input->post('client_loan_id');
            $data = array('status_id' =>2);
            $this->db->where('client_loan_id', $id);
            $query = $this->db->update('loan_approval',$data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('client_loan_id', $id);
            $data = array('status_id' =>2);
            $query = $this->db->update('loan_approval',$data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

}
