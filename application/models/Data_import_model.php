<?php

/**
 * Description of Data Import model
 * For only data imports
 *
 * @author reagan
 */
class Data_import_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    public function add_savings_account($data)
    {
        $this->db->insert('fms_savings_account', $data);
        $last_id = $this->db->insert_id();
        $inserter = $this->db->insert('fms_account_states', [
            'account_id' => $last_id,
            'comment' => 'Current Account Status at the time of data migration',
            'state_id' => 7,
            'created_by' => 1,
            'date_created' => time()
        ]);
        return $last_id;
    }

    public function set_share_state($data)
    {
        $this->db->insert('fms_share_account', $data);
        $last_id = $this->db->insert_id();
        $inserter = $this->db->insert('fms_share_state', [
            'share_account_id' => $last_id,
            'narrative' => 'Current Account Status at the time of data migration',
            'state_id' => 7,
            'created_by' => 1,
            'date_created' => time()
        ]);
        return $last_id;
    }



    public function add_transaction_batch($data)
    {
        return $this->db->insert_batch('fms_transaction', $data);
    }


    public function add_share_transaction_batch($data)
    {
        return $this->db->insert_batch('fms_share_transactions', $data);
    }

    public function add_transaction($data)
    {
        //echo json_encode($data); die;


        $this->db->insert('fms_transaction', $data);
        $last_id = $this->db->insert_id();

        if (is_numeric($last_id)) {
            $response['transaction_no'] = $data['transaction_no'];
            $response['transaction_id'] = $last_id;
            return $response;
        } else {
            return false;
        }


        /*  MODIFICATION For KCAASP SACCO SAVINGS TRANSACTION DATA IMPORT


    $this->db->trans_start();

     $this->db->where('member_id', $data['member_id']);
    $this->db->update('fms_savings_account',['account_no' => $data['account_no'], 'deposit_product_id' => $data['deposit_product_id']]);

     
    $this->db->select("*")->from('fms_savings_account');
    $query = $this->db->get();
    $savings_data =$query->result_array();
    $this->db->trans_complete();
    


    foreach ($savings_data as $key => $value) {
        if($value['account_no'] == $data['account_no']) {
            //$this->db->trans_start();
            $data['account_no_id'] = $value['id'];
            unset($data['member_id']);
            unset($data['account_no']);
            unset($data['deposit_product_id']);
            
            //echo json_encode($data); die;

            $this->db->insert('fms_transaction', $data);
            $last_id = $this->db->insert_id();
   
            if (is_numeric($last_id)) {
                $response['transaction_no']=$data['transaction_no'];
                $response['transaction_id']=$last_id;
                return $response;
            }else{
                return false;
            }
            //$this->db->trans_complete();
            //break;
        }
    }

     */

     
    }


    public function add_transaction_shares($data)
    {
        $this->db->insert('fms_share_transactions', $data);
        $last_id = $this->db->insert_id();
        if (is_numeric($last_id)) {
            $response['transaction_no'] = $data['transaction_no'];
            $response['transaction_id'] = $last_id;
            return $response;
        } else {
            return false;
        }
    }

    public function get_journal_ids()
    {
        $this->db->select("id");
        $this->db->from('journal_transaction');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_savings_accounts($account_no = false)
    {
        $this->db->select("*");
        $this->db->from('savings_account');
        $this->db->where('account_no', $account_no);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function get_savings_accounts2($member_id = false)
    {
        $this->db->select("*");
        $this->db->from('savings_account');
        $this->db->where('member_id', $member_id);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function get_shares_accounts($account_no = false)
    {
        $this->db->select("*");
        $this->db->from('share_account');
        $this->db->where('share_account_no', $account_no);
        $query = $this->db->get();
        return $query->row_array();
    }


    public function add_journal_tr($data)
    {
        $this->db->insert('journal_transaction', $data);
        return $this->db->insert_id();
    }

    public function add_journal_tr_line($id, $data)
    {

        foreach ($data as $key => $value) {
            $value['journal_transaction_id'] = $id;
            $value['created_by'] = 4;
            $value['date_created'] = time();

            $this->db->insert('journal_transaction_line', $value);
        }
    }

    public function update_members($member_id, $branch_id)
    {
        $arrayName = array('branch_id' => $branch_id);
        $this->db->where('id', $member_id);
        return $this->db->update('member', $arrayName);
    }

    public function update_user_password($member_id, $data)
    {
        $user_date = $this->get_user_id($member_id);
        $this->db->where('id', $user_date['user_id']);
        return $this->db->update('user', $data);
    }
    private function get_user_id($member_id)
    {
        $this->db->select("user_id");
        $this->db->from('member');
        $this->db->where('id', $member_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_member_id($member_id)
    {
        $this->db->select("id");
        $this->db->from('member');
        $this->db->where('comment', $member_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function add_membership_fees($data)
    {
        $this->db->insert('applied_member_fees', $data);
        $last_id = $this->db->insert_id();
        if (is_numeric($last_id)) {
            $response['transaction_no'] = $data['transaction_no'];
            $response['transaction_id'] = $last_id;
            return $response;
        } else {
            return false;
        }
    }

    public function get_loan($filter)
    {
        $this->db->select("*");
        $this->db->from('client_loan');
        $this->db->where($filter);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function add_subscription_fees($data)
    {
        $this->db->insert('client_subscription', $data);
        $last_id = $this->db->insert_id();
        if (is_numeric($last_id)) {
            $response['transaction_no'] = $data['transaction_no'];
            $response['transaction_id'] = $last_id;
            return $response;
        } else {
            return false;
        }
   }

   public function member_collateral($insert_data) {
        $this->db->insert("member_collateral", $insert_data);
        return $this->db->insert_id();
    }
   public function user_collateral($insert_data) {
        $this->db->insert("loan_collateral", $insert_data);
        return $this->db->insert_id();
    }
   
    public function get_reschedule_id($loan_id)
    {
        $this->db->select('id, client_loan_id');
        $this->db->from('fms_repayment_schedule');
        $this->db->where('client_loan_id', $loan_id);

        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_member_id_from_member_no($member_no)
    {
        $this->db->select('id');
        $this->db->from('fms_member');
        $this->db->where("client_no = $member_no");
        $query = $this->db->get();
        return $query->row_array();
    }
}
