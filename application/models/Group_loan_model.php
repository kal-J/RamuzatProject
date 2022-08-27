<?php
/**
 * This class helps to create the mode for the database operations
 *@author Eric
 */
class Group_loan_model extends CI_Model {

    private $requested_loan_amount;
    public function __construct() {
        parent :: __construct();
        $this->load->database();
        $this->load->library("loans_col_setup");
        $this->requested_loan_amount = "
                (SELECT group_loan_id,SUM(amount_approved) AS approved_amount,  SUM(ifnull(amount_approved, requested_amount)) borrowed_amount FROM fms_client_loan GROUP BY group_loan_id )";
        $this->max_state_id = "(SELECT client_loan_id,state_id,comment,action_date FROM fms_loan_state
        WHERE id in ( SELECT MAX(id) from fms_loan_state GROUP BY client_loan_id ) )";

        $this->paid_amount = "(SELECT client_loan_id,SUM(paid_interest+paid_principal) AS paid_amount,SUM(paid_principal) AS paid_principal,SUM(paid_interest) AS paid_interest FROM fms_loan_installment_payment WHERE fms_loan_installment_payment.status_id=1 GROUP BY client_loan_id)";
        $this->pay_day = "(SELECT MIN(`repayment_date`) next_pay_date,MAX(`repayment_date`) last_pay_date,`client_loan_id` FROM fms_repayment_schedule WHERE `status_id`=1 AND `payment_status` IN (4,2) GROUP BY client_loan_id)";
        $this->approvals = "(SELECT client_loan_id,COUNT(client_loan_id) AS approvals FROM fms_loan_approval WHERE status_id=1 GROUP BY client_loan_id)";
        $this->paid_installment_amount = "(SELECT client_loan_id,repayment_schedule_id,SUM(paid_interest+paid_principal) AS paid_amount,SUM(paid_principal) AS paid_principal,SUM(paid_interest) AS paid_interest FROM fms_loan_installment_payment WHERE fms_loan_installment_payment.status_id=1 GROUP BY repayment_schedule_id)";
        $this->disburse_data = "(SELECT client_loan_id, SUM(interest_amount) expected_interest, SUM(principal_amount) expected_principal FROM `fms_repayment_schedule` WHERE status_id <>2 GROUP BY client_loan_id)";
        $this->amount_in_demand = "(SELECT client_loan_id, SUM(interest_amount+principal_amount) amount_in_demand, SUM(principal_amount) principal_in_demand FROM `fms_repayment_schedule` WHERE status_id <>2 AND repayment_date <= CURDATE() GROUP BY client_loan_id)";

        $this->days_in_demand = "(SELECT *, DATEDIFF(CURDATE(),repayment_date) days_in_demand FROM fms_repayment_schedule
                WHERE id in ( SELECT MIN(id) from fms_repayment_schedule WHERE repayment_date <= CURDATE() AND payment_status <> 1 AND payment_status <> 3 AND status_id=1 GROUP BY client_loan_id ))";
    }

    /**
     * This method helps to add group_loan into the sacco
     */
    public function set( $loan_ref_no = false ) {
        
        $data = array(
            'group_loan_no' => $loan_ref_no,
            'group_id' => $this->input->post('group_id'),
            'loan_product_id' => $this->input->post('loan_product_id'),
            'loan_type_id' => $this->input->post('loan_type_id'),
            'requested_amount' => $this->input->post('requested_amount'),
            'credit_officer_id' => $this->input->post('credit_officer_id'),
            'comment' => $this->input->post('comment'),
            'credit_officer_id' => $this->input->post('credit_officer_id'),
            'status_id' => '1',
            'date_created' => time(),
            'created_by' => $_SESSION['id']
        );
        $this->db->insert('group_loan', $data);
        return $this->db->insert_id();
    }

    /**
     * This method updates group_loan data in the database
     */
    public function update() {
        $group_loan_id = $this->input->post('group_loan_id');
        if (isset($group_loan_id) && $group_loan_id != '') {
            $id = $this->input->post('group_loan_id');
        }else{
            $id = $this->input->post('id');
        }
        $data = array(
            'group_id' => $this->input->post('group_id'),
            'loan_type_id' => $this->input->post('loan_type_id'),
            'loan_product_id' => $this->input->post('loan_product_id'),
            'requested_amount' => $this->input->post('requested_amount'),
            'credit_officer_id' => $this->input->post('credit_officer_id'),
            'comment' => $this->input->post('comment'),
            'credit_officer_id' => $this->input->post('credit_officer_id'),
            'status_id' => '1',
            'modified_by' => $_SESSION['id']
        );
        $this->db->where('id', $id);
        $query = $this->db->update('group_loan', $data);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * This method displays group_loan data from the database
     */
    // public function get($filter = false) { 
    //     $this->columns = $this->loans_col_setup->get_fields();
    //     $this->db->select("SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $this->columns)), FALSE);
    //         $this->db->select('group_loan.*,group_name,type_name,approved_amount,borrowed_amount');
    //         $this->db->from('group_loan');
    //         $this->db->join('group','group.id=group_loan.group_id',"left");
    //         $this->db->join('client_loan a','a.group_loan_id=group_loan.id',"left");
    //         $this->db->join('group_loan_type', 'group_loan_type.id=group_loan.loan_type_id',"left");
    //         $this->db->join('staff s','s.id = group_loan.credit_officer_id',"left");
    //         $this->db->join('user c','c.id=s.user_id',"left");
    //         $this->db->join('user d','d.id=s.user_id',"left");
    //         $this->db->join('fms_member m','m.user_id=d.id',"left");
    //         $this->db->join('branch ','branch.id=m.branch_id',"left");
    //         $this->db->join("$this->requested_loan_amount client_loan", "client_loan.group_loan_id = group_loan.id","left");

    //     $this->db->join('loan_product e', 'e.id=a.loan_product_id',"left")
    //         ->join('repayment_made_every', 'repayment_made_every.id=a.repayment_made_every',"left")
    //         ->join('repayment_made_every g', 'g.id=a.approved_repayment_made_every', "left");
    //     $this->db->join('repayment_made_every f', 'f.id=a.offset_made_every', "left");
    //     $this->db->join('accounts_chart ac', 'ac.id=e.fund_source_account_id', "left");
    //     $this->db->join("$this->max_state_id loan_state", 'loan_state.client_loan_id=a.id', "left");
    //     $this->db->join("state", 'state.id=loan_state.state_id', 'left');
    //     $this->db->join("$this->paid_amount rsdl", 'rsdl.client_loan_id=a.id', 'left');
    //     $this->db->join("client_loan b", 'b.id=a.linked_loan_id AND a.topup_application =1', 'left');
    //     $this->db->join("$this->paid_amount rsdf", 'rsdf.client_loan_id=b.id', 'left');
    //     $this->db->join("$this->disburse_data c", 'c.client_loan_id=b.id', 'left');
    //     $this->db->join("$this->approvals fla", 'fla.client_loan_id=a.id', 'left');
    //     $this->db->join("$this->pay_day pay_day", 'pay_day.client_loan_id=a.id', 'left');
    //     $this->db->join("$this->disburse_data disburse_data", 'disburse_data.client_loan_id=a.id', 'left');
    //     $this->db->join("$this->amount_in_demand amount_in_demand", 'amount_in_demand.client_loan_id=a.id', 'left');
    //     $this->db->join("$this->days_in_demand days_in_demand", 'days_in_demand.client_loan_id=a.id', 'left');
    //     $this->db->join('payment_details', 'a.id =payment_details.client_loan_id AND payment_details.status_id =1', 'left');



    //     if (isset($_SESSION['role']) && ($_SESSION['role']=='Credit Officer' || $_SESSION['role_id'] ==4)) {
    //         $this->db->where('group_loan.credit_officer_id', $_SESSION['staff_id']);
    //     }
    //     if ($filter == false) {
    //         $this->db->where('group_loan.loan_type_id=', $this->input->post('loan_type_id'));
    //         $query = $this->db->get();
    //         return $query->result_array();
    //     } else {
    //         if (is_numeric($filter)){
    //             $this->db->where('group_loan.id='.$filter);
    //             $query = $this->db->get();
    //             return $query->row_array();
    //         } else {
    //             $this->db->where($filter);
    //             $query = $this->db->get();
    //             return $query->result_array();
    //         }
    //     }
    // }


      public function get($filter = false) { 
            $this->db->select('group_loan.*,group_name,type_name,approved_amount,borrowed_amount,salutation,firstname,lastname,othernames');
            $this->db->from('group_loan');
            $this->db->join('group','group.id=group_loan.group_id');
            $this->db->join('group_loan_type', 'group_loan_type.id=group_loan.loan_type_id');
            $this->db->join('staff s','s.id = group_loan.credit_officer_id');
            $this->db->join('user d','d.id=s.user_id');
            $this->db->join("$this->requested_loan_amount client_loan", "client_loan.group_loan_id = group_loan.id", "left");
        if (isset($_SESSION['role']) && ($_SESSION['role']=='Credit Officer' || $_SESSION['role_id'] ==4)) {
            $this->db->where('group_loan.credit_officer_id', $_SESSION['staff_id']);
        }
        if ($filter == false) {
            $this->db->where('group_loan.loan_type_id=', $this->input->post('loan_type_id'));
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)){
                $this->db->where('group_loan.id='.$filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    /**
     * This method deactivate group_loan data from the database
     */
    public function change_status_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' =>'0');
            $this->db->where('user_id', $id);
            $query = $this->db->update('group_loan',$data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $data = array('status_id' =>'0');
            $this->db->where('id', $id);
            $query = $this->db->update('group_loan',$data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function delete_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->delete('group_loan');
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete('group_loan');
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }
    public function get_id() {
        $this->db->select(" (case when count(id) = 0 then 1 else max(id) + 1 end) id");
        $q = $this->db->get('group_loan');
        return $q->row_array();
    }

}
