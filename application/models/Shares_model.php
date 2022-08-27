<?php

/**
 * Description of Shares_model
 *
 * @author Reagan
 */
class Shares_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database(); 
        $this->acc_sums = "(SELECT share_account_id,transaction_date, (SUM(IFNULL(credit,0))-SUM(IFNULL(debit,0))) as total_amount FROM fms_share_transactions WHERE status_id =1  GROUP BY share_account_id) sht";
        if (isset($_POST['end_date']) && !empty($_POST['end_date'])) {
            $end_date = date('Y-m-d', strtotime($_POST['end_date']));
            $end_date = str_replace('-', '', $end_date);
           
            $this->acc_sums = "(SELECT share_account_id,transaction_date, (SUM(IFNULL(credit,0))-SUM(IFNULL(debit,0))) as total_amount FROM fms_share_transactions WHERE status_id =1 AND DATE(transaction_date) <= $end_date  GROUP BY share_account_id) sht";
        }
        $this->app_sums = "(SELECT application_id, (SUM(IFNULL(credit,0))-SUM(IFNULL(debit,0))) as paid_amount FROM fms_share_transactions  GROUP BY application_id) sht_app";
        $this->call_sums = "(SELECT share_call_id, (SUM(IFNULL(credit,0))-SUM(IFNULL(debit,0))) as amount_paid FROM fms_share_transactions  GROUP BY share_call_id) sht_call";

        $this->current_state = "(SELECT share_account_id,state_id,narrative,action_date FROM fms_share_state
                WHERE id in (
                    SELECT MAX(id) from fms_share_state GROUP BY share_account_id
                )
            )";
        $this->table = "share_account";
    }

    public function set($share_no)
    {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['state_id'], $data['narrative']);
        $data['date_opened'] = $this->helpers->yr_transformer($data['date_opened']);

        $data['share_account_no'] = $share_no;
        $data['status_id'] = '1';
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $this->db->insert('share_account', $data);
        return $this->db->insert_id();
    }

    public function set_application($application_no)
    {

        $data = array(
            'share_application_no' => $application_no,
            'share_issuance_id' => $this->input->post('share_issuance_id'),
            'share_account_id' => $this->input->post('share_issuance_id'),
            'issuance_category_id' => $this->input->post('category_id'),
            'shares_requested' => $this->input->post('shares_requested'),
            'share_price' => $this->input->post('share_price'),
            'date_created' => time(),
            'created_by' => $_SESSION['id'],
            'application_date' => $this->helpers->yr_transformer($this->input->post('application_date'))
        );

        $this->db->insert('share_applications', $data);
        return $this->db->insert_id();
    }

    public function update_application($id)
    {
        $data = array(
            'share_issuance_id' => $this->input->post('share_issuance_id'),
            'share_account_id' => $this->input->post('share_issuance_id'),
            'issuance_category_id' => $this->input->post('category_id'),
            'shares_requested' => $this->input->post('shares_requested'),
            'share_price' => $this->input->post('share_price'),
            'modified_by' => $_SESSION['id'],
            'application_date' => $this->helpers->yr_transformer($this->input->post('application_date'))
        );
        $this->db->where('share_account.id', $id);
        return $this->db->update('share_applications', $data);
    }

    //setting alert 
    public function set_alert_setting()
    {


        $data = array(
            'alert_method' => $this->input->post('alert_method'),
            'alert_type' => $this->input->post('alert_type'),
            'number_of_reminder' => $this->input->post('number_of_reminder'),
            'type_of_reminder' => $this->input->post('type_of_reminder'),
            'date_created' => time(),
            'created_by' => $_SESSION['id'],

        );

        $this->db->insert('fms_alert_setting', $data);
        return $this->db->insert_id();
    }

    public function update_alert_setting($id)
    {
        $data = array(
            'alert_method' => $this->input->post('alert_method'),
            'alert_type' => $this->input->post('alert_type'),
            'number_of_reminder' => $this->input->post('number_of_reminder'),
            'type_of_reminder' => $this->input->post('type_of_reminder'),
            'modified_by' => $_SESSION['id'],
        );
        $this->db->where('share_account.id', $id);
        return $this->db->update('share_applications', $data);
    }

    //end of the function

    public function update()
    {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        //Submission date
        $data['date_opened'] = $this->helpers->yr_transformer($data['date_opened']);
        //$submission_date = explode('-', $data['submission_date'], 3);
        //$data['submission_date'] = count($submission_date) === 3 ? ($submission_date[2] . "-" . $submission_date[1] . "-" . $submission_date[0]) : null;
        //Application date
        //$data['application_date'] = $this->helpers->yr_transformer($data['application_date']);

        // $application_date = explode('-', $data['application_date'], 3);
        // $data['application_date'] = count($application_date) === 3 ? ($application_date[2] . "-" . $application_date[1] . "-" . $application_date[0]) : null;
        unset($data['id'], $data['state_id'], $data['narrative']);
        //$data['total_price'] = $this->input->post('shares') * $this->input->post('share_price');

        $data['status_id'] = '1';
        $data['modified_by'] = $_SESSION['id'];

        $this->db->where('share_account.id', $id);
        $query = $this->db->update('share_account', $data);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    //end of the function

    public function approve_share_application()
    {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        //Submission date
        $data['approval_date'] = $this->helpers->yr_transformer($data['approval_date']);
        unset($data['id'], $data['member_id']);
        $data['total_amount'] = $this->input->post('approved_shares') * $this->input->post('share_price');

        $data['approved_shares'] = $this->input->post('approved_shares');
        $data['modified_by'] = $_SESSION['id'];
        $this->db->where('share_applications.id', $id);
        return $this->db->update('share_applications', $data);
    }
    public function create_payments($call_list)
    {
        $total_amount = $this->input->post('approved_shares') * $this->input->post('share_price');
        foreach ($call_list as $key => $call) {
            $data[] = array(
                'share_call_id' => $call['id'],
                'share_application_id' => $this->input->post('id'),
                'share_call_percentage' => $call['percentage'],
                'total_call_amount' => round((($call['percentage'] / 100) * $total_amount), 2),
                'date_created' => time(),
                'created_by' => $_SESSION['id']
            );
        }
        return $this->db->insert_batch('share_call_payments', $data);
    }


    public function get_payment_calls($filter = FALSE)
    {
        $this->db->select('share_call_payments.*,share_applications.share_application_no,share_applications.share_account_id,share_applications.share_issuance_id,sht_call.share_call_id,IFNULL(sht_call.amount_paid,0) as amount_paid,call_name');
        $query = $this->db->from('share_call_payments');
        $this->db->join('share_applications', 'share_applications.id = share_call_payments.share_application_id', 'left');
        $this->db->join('share_calls', 'share_calls.id = share_call_payments.share_call_id', 'left');
        $this->db->join("$this->call_sums", "share_call_payments.id=sht_call.share_call_id", "left");

        if (isset($_POST['share_application_id'])) {
            $this->db->where('share_call_payments.share_application_id', $this->input->post('share_application_id'));
        }
        $query = $this->db->get();
        return $query->result_array();
    }
    public function get_all()
    {
        $m_id = $this->session->userdata('id');
        $this->db->select('share_account_no,member_id');
        $this->db->from('share_account');
        $this->db->where('member_id =', $m_id);
        $this->db->where('status_id =', 1);

        //$this->db->group_by('saving_account_id');
        $query = $this->db->get();

        return ['data' => $query->result_array(), 'member' =>  $m_id];
    }
    public function get2($filter = FALSE, $type = false)
    {
        $status_id = $this->input->post('status_id') == 2 ? 1 : 1;
        $this->db->select('share_account.*,group_name,savings_account.account_no,user.salutation, user.firstname,user.lastname,user.othernames, IFNULL(group_name, "")  member_name,share_state.narrative,share_state.state_id,sht.share_account_id,(total_amount/price_per_share) as num_of_shares,
        IFNULL(sht.total_amount,0) as total_amount,price_per_share,share_issuance.max_shares as issuance_max_shares,share_issuance_id,issuance_name,share_issuance.min_shares as issuance_min_shares');
        $query = $this->db->from('share_account');
        $this->db->join('savings_account', 'savings_account.id = share_account.default_savings_account_id', 'left');
        $this->db->join('group', 'group.id =share_account.member_id AND share_account.client_type=2', 'left');
        $this->db->join('member', 'member.id =share_account.member_id', 'left');
        $this->db->join('user', 'user.id= member.user_id', 'left');
        $this->db->join('share_issuance', 'share_account.share_issuance_id= share_issuance.id', 'left');

        $this->db->join("$this->current_state share_state", 'share_state.share_account_id=share_account.id', 'left');

        $this->db->join("$this->acc_sums", "share_account.id=sht.share_account_id", "left");


        if (isset($_POST['status_id'])) {
            $this->db->where('share_account.status_id=' . $status_id);
        }

        if (isset($_POST['state_id']) && $type == false) {
            $this->db->where('share_state.state_id', $this->input->post('state_id'));
        }
        if (isset($_POST['end_date']) && !empty($_POST['end_date'])) {

            $end_date = date('Y-m-d', strtotime($_POST['end_date']));
            $end_date = str_replace('-', '', $end_date);

            $this->db->where('DATE(fms_share_account.date_modified)<=', $_POST['end_date']);
            $this->db->or_where('DATE(fms_share_account.date_opened)<=', $end_date);
        }

        if (isset($_POST['group_id'])) {
            $this->db->where('share_account.member_id', $this->input->post('group_id'));
            $this->db->where('share_account.client_type', 2);
        }

        if (isset($_POST['client_id'])) {
            $this->db->where('share_account.member_id', $this->input->post('client_id'));
        }

        if ($filter === FALSE) {
            // $this->db->where('YEAR(fms_share_account.date_opened)',date('Y'));
            $this->db->where('share_account.status_id !=', '0');
            $this->db->where('share_state.state_id', $this->input->post('state_id'));
            $query = $this->db->get();

            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('share_account.id=' . $filter);
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

    public function get($filter = FALSE, $type = false)
    {

        $status_id = $this->input->post('status_id') == 2 ? 1 : 1;
        $this->db->select('share_account.*,group_name,savings_account.account_no,user.salutation, user.firstname,user.lastname,user.othernames, IFNULL(group_name, "")  member_name,share_state.narrative,share_state.state_id,sht.share_account_id,(total_amount/price_per_share) as num_of_shares,
        IFNULL(sht.total_amount,0) as total_amount,price_per_share,share_issuance.max_shares as issuance_max_shares,share_issuance_id,issuance_name,share_issuance.min_shares as issuance_min_shares');
        $query = $this->db->from('share_account');
        $this->db->join('savings_account', 'savings_account.id = share_account.default_savings_account_id', 'left');
        $this->db->join('group', 'group.id =share_account.member_id AND share_account.client_type=2', 'left');
        //$this->db->join('group', 'group.id=share_account.member_id', 'left');
        // $this->db->join('group', 'group.id=share_account.member_id', 'left');
        $this->db->join('member', 'member.id =share_account.member_id', 'left');
        $this->db->join('user', 'user.id= member.user_id', 'left');
        $this->db->join('share_issuance', 'share_account.share_issuance_id= share_issuance.id', 'left');

        $this->db->join("$this->current_state share_state", 'share_state.share_account_id=share_account.id', 'left');

        $this->db->join("$this->acc_sums", "share_account.id=sht.share_account_id", "left");

        if (isset($_POST['status_id'])) {
            $this->db->where('share_account.status_id=' . $status_id);
        }

        if (isset($_POST['state_id']) && $type == false) {
            $this->db->where('share_state.state_id', $this->input->post('state_id'));
        }
        
         if (isset($_POST['end_date']) && !empty($_POST['end_date'])) {
            $end_date = date('Y-m-d', strtotime($_POST['end_date']));
            $end_date = str_replace('-', '', $end_date);
           
            $this->db->where('DATE(fms_share_account.date_modified)<=', $_POST['end_date']);
            $this->db->or_where('DATE(fms_share_account.date_opened)<=', $end_date);
        } 

        if (isset($_POST['group_id'])) {
            $this->db->where('share_account.member_id=', $this->input->post('group_id'));
            $this->db->where('share_account.client_type', 2);
        }

        if (isset($_POST['client_id'])) {
            $this->db->where('share_account.member_id', $this->input->post('client_id'));
        }

        if ($filter === FALSE) {
            // $this->db->where('YEAR(fms_share_account.date_opened)',date('Y'));
            $this->db->where('share_account.status_id !=', '0');
            $this->db->where('share_state.state_id', $this->input->post('state_id'));
            $this->db->order_by('share_account.id', 'DESC');
            $query = $this->db->get();

            return $query->result_array();
            //print_r( $this->db->last_query()); die;
        } else {
            if (is_numeric($filter)) {
                $this->db->where('share_account.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();

                return $query->result_array();
            }
        }
    }

    public function get_shares($filter = FALSE, $type = false)
    {

        $status_id = $this->input->post('status_id') == 2 ? 1 : 1;
        $this->db->select('share_account.*,group_name,savings_account.account_no,user.salutation, user.firstname,user.lastname,user.othernames, IFNULL(group_name, "")  member_name,share_state.narrative,share_state.state_id,sht.share_account_id,(total_amount/price_per_share) as num_of_shares,
        IFNULL(sht.total_amount,0) as total_amount,price_per_share,share_issuance.max_shares as issuance_max_shares,share_issuance_id,issuance_name,share_issuance.min_shares as issuance_min_shares');
        $query = $this->db->from('share_account');
        $this->db->join('savings_account', 'savings_account.id = share_account.default_savings_account_id', 'left');
        $this->db->join('group', 'group.id =share_account.member_id AND share_account.client_type=2', 'left');
        //$this->db->join('group', 'group.id=share_account.member_id', 'left');
        // $this->db->join('group', 'group.id=share_account.member_id', 'left');
        $this->db->join('member', 'member.id =share_account.member_id', 'left');
        $this->db->join('user', 'user.id= member.user_id', 'left');
        $this->db->join('share_issuance', 'share_account.share_issuance_id= share_issuance.id', 'left');

        $this->db->join("$this->current_state share_state", 'share_state.share_account_id=share_account.id', 'left');

        $this->db->join("$this->acc_sums", "share_account.id=sht.share_account_id", "left");

        if (isset($_POST['status_id'])) {
            $this->db->where('share_account.status_id=' . $status_id);
        }

        if (isset($_POST['state_id']) && $type == false) {
            $this->db->where('share_state.state_id', $this->input->post('state_id'));
        }
        if (isset($_POST['end_date']) && !empty($_POST['end_date'])) {
            $end_date = date('Y-m-d', strtotime($_POST['end_date']));
            $end_date = str_replace('-', '', $end_date);
            
            
                $this->db->where('DATE(fms_share_account.date_modified)<=', $_POST['end_date']);
                $this->db->or_where('DATE(fms_share_account.date_opened)<=', $end_date);
            
        }
        $start_date = date('Y-m-d', strtotime($_POST['start_date']));
        $start_date = str_replace('-', '', $start_date);

        if (isset($_POST['group_id'])) {
            $this->db->where('share_account.member_id=', $this->input->post('group_id'));
            $this->db->where('share_account.client_type', 2);
        }

        if (isset($_POST['client_id'])) {
            $this->db->where('share_account.member_id', $this->input->post('client_id'));
        }

        if ($filter === FALSE) {
            // $this->db->where('YEAR(fms_share_account.date_opened)',date('Y'));
            $this->db->where('share_account.status_id !=', '0');
            $this->db->where('share_state.state_id', $this->input->post('state_id'));
            $this->db->where('DATE(fms_share_account.date_opened)>=', $start_date);
            $this->db->order_by('share_account.id', 'DESC');
            $query = $this->db->get();

            return $query->result_array();
            //print_r( $this->db->last_query()); die;
        } else {
            if (is_numeric($filter)) {
                $this->db->where('share_account.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();

                return $query->result_array();
            }
        }
    }
    public function get_share_guarantor($filter = FALSE, $type = false)
    {
        $this->db->select('share_account_no,user.salutation, user.firstname,user.lastname,user.othernames, concat( concat(salutation,".")," ",firstname," ", lastname," ", othernames)  member_name,share_state.narrative,share_state.state_id,sht.share_account_id,IFNULL(sht.total_amount,0) as total_amount,price_per_share,share_issuance.max_shares as issuance_max_shares,share_issuance_id,issuance_name,share_issuance.min_shares as issuance_min_shares');
        $query = $this->db->from('share_account');
        $this->db->join('member', 'member.id =share_account.member_id', 'left');
        $this->db->join('user', 'user.id= member.user_id', 'left');
        $this->db->join('share_issuance', 'share_account.share_issuance_id= share_issuance.id', 'left');

        $this->db->join("$this->current_state share_state", 'share_state.share_account_id=share_account.id', 'left');

        $this->db->join("$this->acc_sums", "share_account.id=sht.share_account_id", "left");

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('share_account.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                //print_r( $this->db->last_query()); die;
                return $query->result_array();
            }
        }
    }

    // GET APPLICATIONS 

    public function get_applications($filter = FALSE)
    {
        $this->db->select('share_applications.*,share_account.share_account_no,user.salutation, user.firstname,user.lastname,user.othernames, concat( concat(salutation,".")," ",firstname," ", lastname," ", othernames) AS member_name,share_state.state_id,sht_app.application_id,sht_app.paid_amount,category,issuance_category_id');
        $query = $this->db->from('share_applications');
        $this->db->join('share_account', 'share_account.id = share_applications.share_account_id', 'left');
        $this->db->join('member', 'member.id =share_account.member_id', 'left');
        $this->db->join('user', 'user.id= member.user_id', 'left');
        $this->db->join('share_issuance_category', 'share_issuance_category.id= share_applications.issuance_category_id', 'left');
        $this->db->join('share_category', 'share_category.id= share_issuance_category.category_id', 'left');
        $this->db->join("$this->current_state share_state", 'share_state.share_account_id=share_account.id', 'left');
        $this->db->join("$this->app_sums", "share_applications.id=sht_app.application_id", "left");

        $this->db->where('share_applications.status_id', $this->input->post('app_status_id'));
        if (isset($_POST['status_id'])) {
            $this->db->where('share_account.status_id', $this->input->post('status_id'));
        }
        if (isset($_POST['share_account_id'])) {
            $this->db->where('share_account.id', $this->input->post('share_account_id'));
        }
        if (isset($_POST['state_id'])) {
            $this->db->where('share_state.state_id', $this->input->post('state_id'));
        }
        if (isset($_POST['acc_id'])) {
            $this->db->where('share_account.default_savings_account_id', $this->input->post('acc_id'));
        }

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('share_applications.id=' . $filter); //fetch according to a share_account
                $query = $this->db->get();
                return $query->row_array();
            } elseif (is_array($filter)) {
                $this->db->where($filter);  //gets detail of a share account expects array with id
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_payments($filter = FALSE)
    {
        $this->db->select('dd.id decl_id, declaration_date, payment_date');
        $this->db->from('share_account');
        $this->db->join('dividend_declaration dd', '1=1');
        $this->db->join("$this->current_state share_state", 'share_state.share_account_id=share_account.id', 'left');

        if (is_numeric($this->input->post('state_id'))) {
            $this->db->select('(IFNULL(approved_shares,0) * IFNULL(dividend_per_share,0)) as amount_paid');
            /* $this->db->select('share_account.id, share_application_no, share_state.narrative, approved_shares,savings_account.account_no,concat( concat(salutation,".")," ",firstname," ", lastname," ", othernames) AS member_name');
            $this->db->join('savings_account', 'savings_account.id = share_account.default_savings_account_id', 'left');
            $this->db->join('member', 'member.id =share_account.member_id', 'left');
            $this->db->join('user', 'user.id= member.user_id', 'left');*/
            $this->db->where('share_state.state_id', $this->input->post('state_id'));
        }
        if (is_numeric($this->input->post('member_id'))) {
            $this->db->select('SUM(IFNULL(approved_shares,0) * IFNULL(dividend_per_share,0)) as amount_paid');
            $this->db->group_by("dd.id");
            $this->db->where('share_account.member_id', $this->input->post('member_id'));
        }
        if (is_numeric($this->input->post('acc_id'))) {
            $this->db->where('share_account.default_savings_account_id', $this->input->post('acc_id'));
        }
        if (is_numeric($this->input->post('id'))) {
            $this->db->select('SUM(IFNULL(approved_shares,0) * IFNULL(dividend_per_share,0)) as amount_paid');
            $this->db->group_by("dd.id");
            $this->db->where('share_account.id', $this->input->post('id'));
        } else {
        }

        $this->db->where('share_account.status_id', 1);
        $this->db->where('dd.status_id', 2);
        $this->db->order_by("declaration_date", "DESC");

        if ($filter !== FALSE) {
            if (is_numeric($filter)) {
                $this->db->where('dd.id=' . $filter); //fetch according to dividend declarations
            } else {
                $this->db->where($filter);
            }
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_by_id($share_id)
    {
        $this->db->select('share_account.*,user.salutation, user.firstname,user.lastname,user.othernames ,share_state.narrative,group_name');
        $this->db->from('share_account');
        $this->db->join('group', 'group.id=share_account.member_id AND share_account.client_type=2', 'left');
        $this->db->join('member', 'member.id =share_account.member_id', 'left');
        $this->db->join('user', 'user.id= member.user_id', 'left');
        $this->db->join("$this->current_state share_state", 'share_state.share_account_id=share_account.id', 'left');
        $this->db->where('share_account.id', $share_id);
        $query = $this->db->get();
        // print_r( $query->row_array()); die;
        return $query->row_array();
    }

    /**
     * This method deletes share application data from the database
     */

    // share 
    public function get_share_fee($share_id = FALSE)
    {

        $this->db->select('id,feename, amount, amountcalculatedas_id');
        $this->db->from('share_fees');
        $this->db->where('id', $share_id);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function get_total_share_capital()
    {
        $record_date = explode('-', $this->input->post('record_date'), 3);
        $data_record_date = count($record_date) === 3 ? ($record_date[2] . "-" . $record_date[1] . "-" . $record_date[0]) : date('Y-m-d');
        $this->db->select('(SUM(IFNULL(credit,0))-SUM(IFNULL(debit,0))) as total_amount ');
        $this->db->from('share_transactions');
        $this->db->where('share_transactions.status_id', 1);
        $this->db->where('share_transactions.transaction_date<=', $data_record_date);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * This method deactivates share application data from the database, though the user know he has deleted
     */
    public function change_status_by_id($id = false)
    {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            $query = $this->db->update($this->table, $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $data = array('status_id' => '0');
            $this->db->where('id', $id);
            $query = $this->db->update($this->table, $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function insert_into_jt()
    {
        $data = [];
        $data['transaction_date'] = $this->helpers->yr_transformer($this->input->post('approval_date'));
        $data['date_created'] = time();
        $data['journal_type_id'] = 24;
        $data['ref_id'] = $this->input->post('id');
        $data['description'] = $this->input->post('narrative');
        $data['modified_by'] = $_SESSION['id'];
        $data['created_by'] = $data['modified_by'];
        $this->db->insert('journal_transaction', $data);
        return $this->db->insert_id();
    }

    public function insert_into_jt_line($journal_transaction_id, $total_paid, $share_issuance_details)
    {
        $approved_shares = $this->input->post('shares');
        $share_price = $this->input->post('share_price');

        if ($total_paid['amount'] < ($approved_shares * $share_price)) {
            $amount = $total_paid['amount'];
        } else {
            $amount = ($approved_shares * $share_price);
        }

        $data = $data2 = [];
        $data['journal_transaction_id'] = $journal_transaction_id;
        $data['account_id'] = $share_issuance_details['share_application_account_id'];
        $data['debit_amount'] = $amount;
        $data['credit_amount'] = NULL;
        $data['narrative'] = $this->input->post('narrative');
        $data['modified_by'] = $_SESSION['id'];
        $data['date_created'] = time();
        $data['created_by'] = $data['modified_by'];

        $data2['journal_transaction_id'] = $journal_transaction_id;
        $data2['account_id'] = $share_issuance_details['share_capital_account_id'];
        $data2['debit_amount'] = NULL;
        $data2['credit_amount'] = $amount;
        $data2['narrative'] = $this->input->post('narrative');
        $data2['modified_by'] = $_SESSION['id'];
        $data2['date_created'] = time();
        $data2['created_by'] = $data2['modified_by'];

        $this->db->insert('journal_transaction_line', $data);
        return $this->db->insert('journal_transaction_line', $data2);
    }

    public function dividend_accounts($filter = false, $date_record)
    {
        $this->acc_sums1 = "(SELECT share_account_id, (SUM(IFNULL(credit,0))-SUM(IFNULL(debit,0))) as total_amount FROM fms_share_transactions WHERE transaction_date<='$date_record' GROUP BY share_account_id) shtd";

        $this->db->select('share_account.*,savings_account.account_no,user.salutation, user.firstname,user.lastname,user.othernames, concat( concat(salutation,".")," ",firstname," ", lastname," ", othernames) AS member_name,share_state.narrative,share_state.state_id,shtd.share_account_id,IFNULL(shtd.total_amount,0) as total_amount,shic.price_per_share');
        $query = $this->db->from('share_account');
        $this->db->join('savings_account', 'savings_account.id = share_account.default_savings_account_id', 'left');
        $this->db->join('member', 'member.id =share_account.member_id', 'left');
        $this->db->join('user', 'user.id= member.user_id', 'left');
        $this->db->join('share_issuance', 'share_issuance.status_id= 1', 'left');
        $this->db->join('share_issuance_category shic', 'shic.status_id =1', 'left');
        $this->db->join("$this->current_state share_state", 'share_state.share_account_id=share_account.id', 'left');

        $this->db->join("$this->acc_sums1", "share_account.id=shtd.share_account_id", "left");
        $this->db->where('shtd.total_amount>0');
        if (isset($_POST['status_id'])) {
            $this->db->where('share_account.status_id', $this->input->post('status_id'));
        }
        if (isset($_POST['state_id'])) {
            $this->db->where('share_state.state_id', $this->input->post('state_id'));
        }
        if (isset($_POST['acc_id'])) {
            $this->db->where('share_account.default_savings_account_id', $this->input->post('acc_id'));
        }

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('share_account.id=' . $filter); //fetch according to a share account
                $query = $this->db->get();
                return $query->row_array();
            } elseif (is_array($filter)) {
                $this->db->where($filter);  //gets detail of a share account expects array with id
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    //get share excel 
    public function get_excel_data($filter = false)
    {
        $this->db->select('sa.id,sa.share_account_no,client_no,sa.member_id,concat(concat(salutation,".")," ",firstname," ", lastname," ", othernames) AS member_name,sa.share_issuance_id');
        $this->db->from('fms_share_account sa');
        $this->db->join("member", "member.id = sa.member_id", 'left');
        $this->db->join("user", "member.user_id = user.id");
        $this->db->where('sa.share_account_no !=', '');
        $this->db->order_by('sa.id', 'ASC');

        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('sa.share_issuance_id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    // Deactivate or delete the account 
    public function change_state($data = FALSE)
    {

        $id = $this->input->post('share_account_id');
        if (is_numeric($id)) {
            $data['status_id'] = 0;
            $data['modified_by'] = $_SESSION['id'];

            $this->db->where('id', $id);
            return  $this->db->update('fms_share_account', $data);
        } else {
            $state_id = $data['saving_state_id'] == 7 ? 17 : 7;
            $this->db->where('share_account_id', $data['share_account_id']);
            return $this->db->update('fms_share_state', ['state_id' => $state_id]);
        }
    }

    public function get_group_shares($filter = FALSE)
    {
        //echo json_encode($filter);die;
        $status_id = $this->input->post('status_id') == 2 ? 1 : 1;
        $this->db->select('share_account.*,group_name,savings_account.account_no,user.salutation, user.firstname,user.lastname,user.othernames, IFNULL(group_name, "")  member_name,share_state.narrative,share_state.state_id,sht.share_account_id,(total_amount/price_per_share) as num_of_shares,
        IFNULL(sht.total_amount,0) as total_amount,price_per_share,share_issuance.max_shares as issuance_max_shares,share_issuance_id,issuance_name,share_issuance.min_shares as issuance_min_shares');
        $query = $this->db->from('share_account');
        $this->db->join('savings_account', 'savings_account.id = share_account.default_savings_account_id', 'left');
        $this->db->join('group', 'group.id =share_account.member_id AND share_account.client_type=2', 'left');
        $this->db->join('member', 'member.id =share_account.member_id', 'left');
        $this->db->join('user', 'user.id= member.user_id', 'left');
        $this->db->join('share_issuance', 'share_account.share_issuance_id= share_issuance.id', 'left');

        $this->db->join("$this->current_state share_state", 'share_state.share_account_id=share_account.id', 'left');

        $this->db->join("$this->acc_sums", "share_account.id=sht.share_account_id", "left");




        // if (isset($_POST['group_id'])) {
        //     $this->db->where('share_account.member_id=', $this->input->post('group_id'));
        //     $this->db->where('share_account.client_type', 2);
        // }


        if ($filter != FALSE && is_numeric($filter)) {
            if (isset($_POST['group_id'])) {
                $this->db->where('share_account.member_id=', $this->input->post('group_id'));
                $this->db->where('share_account.client_type', $filter);
                $this->db->where('share_account.status_id', 1);
                $query = $this->db->get();
                //echo json_encode($query); die;
                return $query->result_array();
            }
        } else {

            echo json_encode('Value isFALSE & Not numeric');
        }
    }
    public function get_member_shares($filter = FALSE)
    {
        //echo json_encode($filter);die;
        $status_id = $this->input->post('status_id') == 2 ? 1 : 1;
        $this->db->select('share_account.*,group_name,savings_account.account_no,user.salutation, user.firstname,user.lastname,user.othernames, IFNULL(group_name, "")  member_name,share_state.narrative,share_state.state_id,sht.share_account_id,(total_amount/price_per_share) as num_of_shares,
            IFNULL(sht.total_amount,0) as total_amount,price_per_share,share_issuance.max_shares as issuance_max_shares,share_issuance_id,issuance_name,share_issuance.min_shares as issuance_min_shares');
        $query = $this->db->from('share_account');
        $this->db->join('savings_account', 'savings_account.id = share_account.default_savings_account_id', 'left');
        $this->db->join('group', 'group.id =share_account.member_id AND share_account.client_type=2', 'left');
        $this->db->join('member', 'member.id =share_account.member_id', 'left');
        $this->db->join('user', 'user.id= member.user_id', 'left');
        $this->db->join('share_issuance', 'share_account.share_issuance_id= share_issuance.id', 'left');

        $this->db->join("$this->current_state share_state", 'share_state.share_account_id=share_account.id', 'left');

        $this->db->join("$this->acc_sums", "share_account.id=sht.share_account_id", "left");



        if ($filter != FALSE && is_numeric($filter)) {
            if (isset($_POST['client_id'])) {
                $this->db->where('share_account.member_id=', $this->input->post('client_id'));
                $this->db->where('share_account.client_type', $filter);
                $this->db->where('share_account.status_id', 1);
                $query = $this->db->get();
                //echo json_encode($query); die;
                return $query->result_array();
            }
        } else {

            echo json_encode('Value isFALSE & Not numeric');
        }
    }
}
