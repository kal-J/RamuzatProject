<?php

/**
 * Description of Automated_fees_model
 *
 * @author Joshua Nabuka
 */
class Automated_fees_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE,$date=false,$table,$table2,$column) {
        $this->db->select('cs.id,cs.subscription_date,cs.'.$column.',IFNULL(am.amount,0) amount_paid,s.state_name,sp.*,ABS(SUM(IFNULL(cs.amount,0)-IFNULL(am.amount,0))) due_amount,cs.last_payment_date,SUM(cs.amount) amount,concat(u.firstname," ", u.lastname," ", u.othernames) AS member_name,cs.required_fee,cs.member_id,cs.state, COUNT(*) total_Rows');
        $this->db->from($table.' cs');
        $this->db->join('member m', 'm.id=cs.member_id','left');
        $this->db->join('user u', 'm.user_id=u.id','left');
        $this->db->join($table2.' sp', 'sp.id=cs.'.$column,'left');
        $this->db->join('applied_member_fees am', 'cs.member_id = am.member_id AND cs.'.$column.' = am.member_fee_id','left');
        $this->db->join('state s', 's.id=cs.state','left');
        $this->db->group_by('member_id');

        if ($this->input->post('member_id') !== null && is_numeric($this->input->post('member_id'))) {
            $this->db->where('cs.member_id', $this->input->post('member_id'));
        }

        if ($this->input->post('state_id') !== null && is_numeric($this->input->post('state_id'))) {
            if($this->input->post('state_id') == 1){
                $this->db->where('cs.state', 20);
            }else if($this->input->post('state_id') == 2){
                $this->db->where('cs.state', 21);
            }else if($this->input->post('state_id') == 3){
                $this->db->where('cs.state', 9);
            }
            
        }else{
            $this->db->where('cs.state', 20);
        }

        if ($date != FALSE) {
            $this->db->where('cs.subscription_date >=', $date);
        }

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $query = $this->db->get();
                $this->db->where('id', $filter);

                return $query->row_array();
            } else {
                $query = $this->db->get();
                $this->db->where($filter);
                return $query->result_array();
            }
        }
    }

    public function get_summary($state =FALSE)
    {
        $this->db->select('cs.*,am.*, COUNT(cs.id) as due_users, COUNT(am.id) as paid_users, SUM(IFNULL(cs.amount,0)),ABS(SUM(IFNULL(cs.amount,0)-IFNULL(am.amount,0))) total_due, SUM(IFNULL(am.amount,0)) as total_paid, COUNT(*) OVER () as total_Rows');
        $this->db->from('membership_schedule cs');
        $this->db->join('applied_member_fees am','am.member_id = cs.member_id AND fee_paid=1','left');

        if ($this->input->post('state_id') !== null && is_numeric($this->input->post('state_id'))) {
            $this->db->where('cs.state', $state);
        }

            $query = $this->db->get();
            return $query->result_array();
        
    }

    public function get_details($filter = FALSE,$table,$table2,$column) {
        $this->db->select("a.id,a.subscription_date,b.*,a.amount,IFNULL(am.amount,0) amount_paid,ABS(SUM(IFNULL(a.amount,0)-IFNULL(am.amount,0))) due_amount,a.date_created,a.".$column.", a.member_id,s.state_name,concat(u.firstname,' ', u.lastname,' ', u.othernames) AS member_name");
        $this->db->from($table.' a');
        $this->db->join('member m', 'm.id=a.member_id');
        $this->db->join('user u', 'm.user_id=u.id','left');
        $this->db->join($table2.' b', 'a.'.$column.' = b.id'); 
        $this->db->join('applied_member_fees am', 'a.member_id = am.member_id AND a.'.$column.' = am.member_fee_id','left');       
        $this->db->join('state s', 'a.state = s.id');
        if (is_numeric($this->input->post('member_id'))){
            $this->db->where('a.member_id', $this->input->post('member_id'));
        }
        if ($filter == false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                // print_r( $this->db->last_query()); die;
                return $query->result_array();
            }
        }
    }

    public function get_subscription($filter = FALSE,$date=false,$table,$table2,$column) {
        $this->db->select('cs.id,cs.subscription_date,cs.'.$column.',IFNULL(am.amount,0) amount_paid,s.state_name,sp.*,cs.amount,concat(u.firstname," ", u.lastname," ", u.othernames) AS member_name,cs.required_fee,cs.member_id,cs.member_id as client_id,cs.state');
        $this->db->from($table.' cs');
        $this->db->join('member m', 'm.id=cs.member_id','left');
        $this->db->join('user u', 'm.user_id=u.id','left');
        $this->db->join($table2.' sp', 'sp.id=cs.'.$column,'left');
        $this->db->join('client_subscription am', 'cs.member_id = am.client_id','left');
        $this->db->join('state s', 's.id=cs.state','left');

        if ($this->input->post('member_id') !== null && is_numeric($this->input->post('member_id'))) {
            $this->db->where('cs.member_id', $this->input->post('member_id'));       
         }
         
         if ($this->input->post('state_id') !== null && is_numeric($this->input->post('state_id'))) {
            if($this->input->post('state_id') == 1){
                $this->db->where('cs.state', 20);
            }else if($this->input->post('state_id') == 2){
                $this->db->where('cs.state', 21);
            }else if($this->input->post('state_id') == 3){
                $this->db->where('cs.state', 9);
            }
            
        }else{
            $this->db->where('cs.state', 20);
        }

        if ($date != FALSE) {
            $this->db->where('cs.subscription_date >=', $date);
        }

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $query = $this->db->get();
                $this->db->where('id', $filter);

                return $query->row_array();
            } else {
                $query = $this->db->get();
                $this->db->where($filter);
                return $query->result_array();
            }
        }
    }

    public function get_sub_summary($state =FALSE)
    {
        $this->db->select('cs.*,am.*, COUNT(cs.id) as due_users, COUNT(am.id) as paid_users, SUM(IFNULL(cs.amount,0)),ABS(SUM(IFNULL(cs.amount,0)-IFNULL(am.amount,0))) total_due, SUM(IFNULL(am.amount,0)) as total_paid, COUNT(*) OVER () as total_Rows');
        $this->db->from('subscription_schedule cs');
        $this->db->join('client_subscription am','am.client_id = cs.member_id AND sub_fee_paid=1','left');

        if ($this->input->post('state_id') !== null && is_numeric($this->input->post('state_id'))) {
            $this->db->where('cs.state', $state);
        }

            $query = $this->db->get();
            return $query->result_array();
        
    }


    public function set($data,$table) {
        $this->db->insert($table, $data);
        $last_id= $this->db->insert_id();
        if (is_numeric($last_id)) {
            $response['last_id']=$last_id;
            return $response;
        }else{
            return false;
        }
    }

    public function get_defaulters($table,$column){
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($column.' = 5');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function update_schedule($data)
    {
        $id = $data['member_id'];
        $info = array(
            'last_payment_date' => date('Y-m-d'),
            'state' => '9',
        );

        $this->db->where('id', $id);
        return $this->db->update('membership_schedule', $info);
    }
    
    public function auto_update($sent_data) {
        $id = $sent_data['member_id'];
        $start_date = explode('-', $this->input->post('transaction_date'), 3);
        $start_date_prepared = count($start_date) === 3 ? ($start_date[2] . "-" . $start_date[1] . "-" . $start_date[0]) : null;

        $data = array(
            'fee_paid' => 1,
            'payment_date' => $sent_data['payment_date'],
            'payment_id' => $sent_data['payment_id'],
            'date_modified' => time(),
            'modified_by' => $sent_data['modified_by']
        );

        $this->db->where('id', $id);
        return $this->db->update('applied_member_fees', $data);
    }

}