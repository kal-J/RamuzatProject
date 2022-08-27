<?php

/**
 * Description of Shares_application_model
 *
 * @author Reagan
 */
class Shares_application_model extends CI_Model {

    public function __construct() {
        $this->load->database();
        $this->acc_sums = "(SELECT application_id, (SUM(IFNULL(credit,0))-SUM(IFNULL(debit,0))) as total_amount FROM fms_share_transactions  GROUP BY application_id) sht";

        $this->current_state = "(SELECT share_account_id,state_id,narrative,action_date FROM fms_share_state
                WHERE id in (
                    SELECT MAX(id) from fms_share_state GROUP BY share_account_id
                )
            )";
        $this->table = "share_applications";
    }
    
    public function set($share_no){
        $data = $this->input->post(NULL, TRUE);
        //Submission date

        $data['submission_date'] = $this->helpers->yr_transformer($data['submission_date']);
        // $submission_date = explode('-', $data['submission_date'], 3);
        // $data['submission_date'] = count($submission_date) === 3 ? ($submission_date[2] . "-" . $submission_date[1] . "-" . $submission_date[0]) : null;
        //Application date
        $data['application_date'] = $this->helpers->yr_transformer($data['application_date']);

        // $application_date = explode('-', $data['application_date'], 3);
        // $data['application_date'] = count($application_date) === 3 ? ($application_date[2] . "-" . $application_date[1] . "-" . $application_date[0]) : null;
        unset($data['id'],$data['state_id'],$data['payment_id'],$data['transaction_channel_id'],$data['transaction_date'],$data['narrative'],$data['amount'],$data['category_id']);
        $data['total_price'] = $this->input->post('shares') * $this->input->post('share_price');

        $data['share_account_no'] = $share_no;


        $data['status_id'] = '1';
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];

        $this->db->insert('share_account', $data);
        return $this->db->insert_id();
    }

      public function set_application($application_no){
           $data = array(
                'share_application_no' => $application_no, 
                'share_issuance_id' => $this->input->post('share_issuance_id'), 
                'share_account_id' => $this->input->post('share_issuance_id'), 
                'shares' => $this->input->post('shares'),
                'share_price' => $this->input->post('share_price'), 
                'total_price' => $this->input->post('total_price'), 
                'application_date' => $this->helpers->yr_transformer($this->input->post('application_date'))
            );
          $this->db->insert('share_applications', $data);
          return $this->db->insert_id();
    }

//end of the function

    public function update() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        //Submission date
        $data['submission_date'] = $this->helpers->yr_transformer($data['submission_date']);
        //$submission_date = explode('-', $data['submission_date'], 3);
        //$data['submission_date'] = count($submission_date) === 3 ? ($submission_date[2] . "-" . $submission_date[1] . "-" . $submission_date[0]) : null;
        //Application date
        $data['application_date'] = $this->helpers->yr_transformer($data['application_date']);

        // $application_date = explode('-', $data['application_date'], 3);
        // $data['application_date'] = count($application_date) === 3 ? ($application_date[2] . "-" . $application_date[1] . "-" . $application_date[0]) : null;
        unset($data['id'], $data['state_id'], $data['narrative']);
        $data['total_price'] = $this->input->post('shares') * $this->input->post('share_price');

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

    public function change_share_state() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        //Submission date
        $data['approval_date'] = $this->helpers->yr_transformer($data['approval_date']);
        unset($data['id'], $data['state_id'], $data['narrative'], $data['shares']);
        $data['total_price'] = $this->input->post('shares') * $this->input->post('share_price');

        $data['approved_shares'] = $this->input->post('shares');

        $data['status_id'] = '1';
        $data['modified_by'] = $_SESSION['id'];

        $this->db->where('share_account.id', $id);
        $query = $this->db->update('share_account', $data);
        if ($query) {
            $this->db->insert(
                    'fms_share_state',
                    [
                        'share_account_id' => $this->input->post('id'),
                        'action_date' => $this->input->post('approval_date'),
                        'state_id' => 6,
                        'narrative' => $this->input->post('narrative'),
                        'created_by' => $_SESSION['id'],
                        'date_created' => time()
                    ]
            );

            return true;
        } else {
            return false;
        }
    }

//end of the function

    public function get($filter = FALSE) {
        $this->db->select('share_account.*,savings_account.account_no,user.salutation, user.firstname,user.lastname,user.othernames, concat( concat(salutation,".")," ",firstname," ", lastname," ", othernames) AS member_name,share_state.narrative,share_state.state_id,sht.application_id,sht.total_amount,(IFNULL(total_price,0) - IFNULL(sht.total_amount,0)) as rem_balance');
        $query = $this->db->from('share_account');
        $query = $this->db->join('share_issuance', 'share_account.share_issuance_id=share_issuance.id', 'left');
        $this->db->join('savings_account', 'savings_account.id = share_account.default_savings_account_id', 'left');
        $this->db->join('member', 'member.id =share_account.member_id', 'left');
        $this->db->join('user', 'user.id= member.user_id', 'left');
        $this->db->join("$this->current_state share_state", 'share_state.share_account_id=share_account.id', 'left');

        $this->db->join("$this->acc_sums", "share_account.id=sht.application_id", "left");

        $this->db->order_by("submission_date", "DESC");
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
                $this->db->where('share_account.member_id=' . $filter); //fetch according to a member
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

    public function get_payments($filter = FALSE) {
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

    public function get_by_id($share_id) {
        $this->db->select('share_account.*,user.salutation, user.firstname,user.lastname,user.othernames ,share_state.narrative');
        $this->db->from('share_account')->join('share_issuance', 'share_account.share_issuance_id=share_issuance.id', 'left');
        $this->db->join('member', 'member.id =share_account.member_id ');
        $this->db->join('user', 'user.id= member.user_id');
        $this->db->join("$this->current_state share_state", 'share_state.share_account_id=share_account.id');
        $this->db->order_by("submission_date", "DESC");
        $this->db->where('share_account.id', $share_id);
        $query = $this->db->get();
        //print_r( $this->db->last_query()); die;
        return $query->row_array();
    }

    /**
     * This method deletes share application data from the database
     */
    public function delete_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->delete($this->table);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete($this->table);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    // share 
    public function get_share_fee($share_id = FALSE) {

        $this->db->select('id,feename, amount, amountcalculatedas_id');
        $this->db->from('share_fees');
        $this->db->where('id', $share_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_approved_shares($filter = 1) {
        $this->db->select('SUM(IFNULL(approved_shares,0)) as total_approved_shares');
        $this->db->from('share_account');
        $this->db->where('share_account.status_id', 1);
        //$this->db->where('share_account.share_issuance_id', $filter);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * This method deactivates share application data from the database, though the user know he has deleted
     */
    public function change_status_by_id($id = false) {

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

    public function insert_into_jt() {
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

    public function insert_into_jt_line($journal_transaction_id, $total_paid, $share_issuance_details) {
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

}
