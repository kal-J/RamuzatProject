<?php

/**
 * Description of Client_subscription_model
 *
 * @author Allan Jes
 */
class Client_subscription_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    public function get($filter = FALSE, $date = false)
    {
        $this->db->select('cs.id,MAX(cs.subscription_date) AS subscription_date ,transaction_no,reversed_date,reverse_msg,MAX(cs.payment_date) as transaction_date,SUM(cs.amount) as amount,concat(u.firstname," ", u.lastname," ", u.othernames) AS member_name,cs.narrative,cs.sub_fee_paid,sp.plan_name,payment_id,payment_mode,cs.client_id,cs.status_id,mf.feename');
        $this->db->from('client_subscription cs');
        $this->db->join('member m', 'm.id=cs.client_id', 'left');
        $this->db->join('user u', 'm.user_id=u.id', 'left');
        $this->db->join('subscription_plan sp', 'sp.id=m.subscription_plan_id', 'left');
        $this->db->join('member_fees mf','mf.id=cs.feeid','left');
        $this->db->join('payment_mode pm', 'pm.id=cs.payment_id', 'left');
        if ($this->input->post('client_id') !== null && is_numeric($this->input->post('client_id'))) {
            $this->db->where('cs.client_id', $this->input->post('client_id'));
        }
        if ($this->input->post('status_id') !== null && is_numeric($this->input->post('status_id'))) {
            $this->db->where('cs.status_id', $this->input->post('status_id'));
        }

        if ($date != FALSE) {
            $this->db->where('cs.subscription_date >=', $date);
        }
        $this->db->group_by('cs.client_id');
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

     public function get3($filter = FALSE, $date = false)
    {
        $this->db->select('cs.id,cs.subscription_date,transaction_no,reversed_date,reverse_msg,cs.payment_date as transaction_date,cs.amount,concat(u.firstname," ", u.lastname," ", u.othernames) AS member_name,cs.narrative,cs.sub_fee_paid,sp.plan_name,payment_id,payment_mode,cs.client_id,cs.status_id');
        $this->db->from('client_subscription cs');
        $this->db->join('member m', 'm.id=cs.client_id', 'left');
        $this->db->join('user u', 'm.user_id=u.id', 'left');
        $this->db->join('subscription_plan sp', 'sp.id=m.subscription_plan_id', 'left');
        $this->db->join('payment_mode pm', 'pm.id=cs.payment_id', 'left');
        if ($this->input->post('client_id') !== null && is_numeric($this->input->post('client_id'))) {
            $this->db->where('cs.client_id', $this->input->post('client_id'));
        }
        if ($this->input->post('status_id') !== null && is_numeric($this->input->post('status_id'))) {
            $this->db->where('cs.status_id', $this->input->post('status_id'));
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


    public function get2($filter = FALSE, $date = false)
    {
        $this->db->select('cs.id,cs.subscription_date,transaction_no,reversed_date,reverse_msg,cs.payment_date as transaction_date,cs.amount,concat(u.firstname," ", u.lastname," ", u.othernames) AS member_name,cs.narrative,payment_id,cs.member_id,cs.status_id');
        $this->db->from('applied_member_fees cs');
        $this->db->join('member m', 'm.id=cs.member_id', 'left');
        $this->db->join('user u', 'm.user_id=u.id', 'left');
        $this->db->join('member_fees sp', 'sp.id=m.subscription_plan_id', 'left');

        if ($this->input->post('client_id') !== null && is_numeric($this->input->post('client_id'))) {
            $this->db->where('cs.client_id', $this->input->post('client_id'));
        }
        if ($this->input->post('status_id') !== null && is_numeric($this->input->post('status_id'))) {
            $this->db->where('cs.status_id', $this->input->post('status_id'));
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


    public function get_max($filter = FALSE)
    {
        $this->db->select('cs.id,cs.subscription_date,transaction_no,reversed_date,reverse_msg,cs.payment_date as transaction_date,cs.amount,concat(u.firstname," ", u.lastname," ", u.othernames) AS member_name,cs.narrative,cs.sub_fee_paid,sp.plan_name,payment_id,payment_mode,cs.client_id,cs.status_id');
        $this->db->from('client_subscription cs');
        $this->db->join('member m', 'm.id=cs.client_id');
        $this->db->join('user u', 'm.user_id=u.id', 'left');
        $this->db->join('subscription_plan sp', 'sp.id=m.subscription_plan_id');
        $this->db->join('payment_mode pm', 'pm.id=cs.payment_id', 'left');
        if ($this->input->post('client_id') !== null && is_numeric($this->input->post('client_id'))) {
            $this->db->where('cs.client_id', $this->input->post('client_id'));
        }
        if ($this->input->post('status_id') !== null && is_numeric($this->input->post('status_id'))) {
            $this->db->where('cs.status_id', $this->input->post('status_id'));
        }
        $this->db->order_by('cs.subscription_date', 'DESC');
        $this->db->limit(1);

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->row_array();
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


    public function client_recent_payment($filter)
    {
        if (is_numeric($filter)) {
            $sql = "(SELECT max(id) id, `payment_date` FROM `fms_client_subscription` WHERE `client_id`=$filter)";
        } else {
            $sql = "(SELECT max(id) id, `payment_date` FROM `fms_client_subscription` WHERE $filter)";
        }
        $query = $this->db->query($sql);
        return $query->row_array();
    }
    public function set()
    {
        $data = $this->input->post(NULL, TRUE);
        unset($data['transaction_date'], $data['account_no_id']);
        $data['subscription_date'] = $this->helpers->yr_transformer($data['subscription_date']);
        $data['payment_date'] = $this->helpers->yr_transformer($this->input->post('transaction_date'));
        $data['transaction_no'] = date('ymdhms') . mt_rand(10, 99);
        unset($data['id'], $data['tbl']);
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] = $_SESSION['id'];

        $this->db->insert("client_subscription", $data);
        return $this->db->insert_id();
    }
    public function set2($sent_data)
    { //For cron job subscriptions
        $sub_fee_paid = $sent_data['sub_fee_paid'] !="" ? $sent_data['sub_fee_paid'] :0;
        $data['subscription_date'] = $sent_data['subscription_date'];
        $data['payment_date'] = $sent_data['payment_date'];
        $data['payment_id'] = $sent_data['payment_id'];
        $data['transaction_no'] = date('ymdhms') . mt_rand(10, 99);
        $data['narrative'] = $sent_data['narrative'];
        $data['amount'] = $sent_data['amount'];
        $data['client_id'] = $sent_data['client_id'];
        $data['status_id'] = 1;
        $data['sub_fee_paid'] =$sub_fee_paid;
        $data['date_created'] = time();
        $data['created_by'] = 1;
        $data['modified_by'] = 1;
        $data['feeid'] = $sent_data['feeid'];
        $this->db->insert("client_subscription", $data);
        return $this->db->insert_id();
    }

    public function set3($sent_data)
    { //For cron job memberships
        $data['subscription_date'] = $sent_data['subscription_date'];
        $data['payment_date'] = $sent_data['payment_date'];
        $data['transaction_no'] = date('ymdhms') . mt_rand(10, 99);
        $data['narrative'] = $sent_data['narrative'];
        $data['amount'] = $sent_data['amount'];
        $data['client_id'] = $sent_data['client_id'];
        $data['status_id'] = 1;
        $data['date_created'] = time();
        $data['created_by'] = 1;
        $data['modified_by'] = 1;
        $this->db->insert("client_membership", $data);
        return $this->db->insert_id();
    }

    public function auto_update($sent_data)
    {
        $id = $sent_data['client_id'];
        $start_date = explode('-', $this->input->post('transaction_date'), 3);
        $start_date_prepared = count($start_date) === 3 ? ($start_date[2] . "-" . $start_date[1] . "-" . $start_date[0]) : null;

        $data = array(
            'sub_fee_paid' => 1,
            'payment_date' => $sent_data['payment_date'],
            'payment_id' => $sent_data['payment_id'],
            'date_modified' => time(),
            'modified_by' => $sent_data['modified_by']
        );

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('fms_client_subscription', $data);
        } else {
            return false;
        }
    }


    public function update()
    {
        $id = $this->input->post('id');
        $start_date = explode('-', $this->input->post('transaction_date'), 3);
        $start_date_prepared = count($start_date) === 3 ? ($start_date[2] . "-" . $start_date[1] . "-" . $start_date[0]) : null;

        $data = array(
            'sub_fee_paid' => 1,
            'payment_date' => $start_date_prepared,
            'payment_id' => $this->input->post('payment_id'),
            'date_modified' => time(),
            'modified_by' => $_SESSION['id']
        );

        $this->update_state($id);

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('client_subscription', $data);
        } else if (!is_numeric($id)) {
            $this->db->insert("client_subscription", $data);
            return $this->db->insert_id();
        } else {

            return false;
        }
    }

    public function update_state($id){
        $data = array('state' => '9');
        $this->db->where('id', $id);
        return $this->db->update('membership_schedule', $data);
    }

    public function reverse()
    {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['transaction_no']);
        $data['reversed_by'] = $_SESSION['id'];
        $data['reversed_date'] = date("Y-m-d H:i:s");
        $data['reverse_msg'] = $this->input->post('reverse_msg');
        $data['status_id'] = 3;

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update("client_subscription", $data);
        } else {
            return false;
        }
    }


    public function change_status()
    {

        $data = array(
            'status_id' => $this->input->post('status') !== NULL ? $this->input->post('status') : 0,
            'modified_by' => $_SESSION['id']
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('client_subscription', $data);
    }

    //deletes members given a particular where clause
    public function delete()
    {
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        return $this->db->delete('client_subscription');
    }

    public function get_max_subscription_date($filter= false){

         $this->db->select('MAX(payment_date) as max_transaction_date');
         $this->db->from('client_subscription');
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
}
