<?php

class Transaction_model extends CI_Model
{

    private $alias_only_pattern = '/(\s+(as[\s]+)?)((`)?[a-zA-Z0-9_]+(`)?)$/';

    private $columns = ["tn.id", "tn.transaction_no", "concat(u.firstname,' ', u.lastname,' ', u.othernames) member_name", "tn.ref_no", "tn.status_id", "tn.reverse_msg", "tn.reversed_date", "sa.account_no", "tn.debit", "tn.credit", "tn.transaction_type_id", "tn.payment_id", "tn.narrative", "tn.transaction_date", "tt.type_name", "sa.client_type", "tc.payment_mode", "tn.date_modified", "+COALESCE((SELECT SUM(ifnull(credit,0)) - SUM(ifnull(debit,0))
                              FROM fms_transaction b
                              WHERE tn.transaction_date >= b.transaction_date 
                               AND b.account_no_id = tn.account_no_id
                               AND b.status_id = 1),0)
                                 AS end_balance"];
    public function __construct()
    {
        $this->load->database();
        $this->table = 'fms_transaction tn';
    }

    public function get($filter = false)
    {
        $this->db->select('tn.*,sa.account_no,sa.client_type,COALESCE((SELECT SUM(ifnull(credit,0)) - SUM(ifnull(debit,0))
                              FROM fms_transaction b
                              WHERE tn.transaction_date >= b.transaction_date 
                               AND b.account_no_id = tn.account_no_id
                               AND b.status_id = 1 ),0)
                                 AS end_balance,tc.payment_mode,tt.type_name');
        $this->db->from('fms_transaction tn');
        $this->db->join('fms_savings_account sa', 'tn.account_no_id=sa.id', 'left');
        $this->db->join('fms_transaction_type tt', 'tn.transaction_type_id=tt.id', 'left');
        $this->db->join('fms_payment_mode tc', 'tn.payment_id=tc.id', 'left');
        $this->db->order_by('tn.transaction_date DESC');
        $this->db->order_by('tn.transaction_type_id DESC');

        if (!empty($_POST['start_date'])) {
            $start_date = str_replace('-', '', $_POST['start_date']);
            $this->db->where('DATE(tn.transaction_date) >=' . $start_date);
        }

        if (!empty($_POST['end_date'])) {
            $end_date = str_replace('-', '', $_POST['end_date']);
            $this->db->where('DATE(tn.transaction_date) <= ' . $end_date);
        }

        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else if (is_numeric($filter)) { //when given the primary key
            $this->db->where('tn.id', $filter);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->where($filter);
            $query = $this->db->get();
            return $query->result_array();
        }
    }
    public function get_sums($filter = false)
    {
        $this->db->select('SUM(IFNULL(credit ,0))-SUM(IFNULL(debit ,0)) as cash_bal,SUM(IFNULL(credit ,0)) as deposits,SUM(IFNULL(debit ,0)) as withdraws');
        $this->db->from('fms_transaction tn');
        $this->db->where($filter);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_savings_accounts($filter = FALSE, $filter2 = false, $acc_id = FALSE)
    {

        $this->db->select('account_no_id,sum(credit) deposit');
        $this->db->from('transaction');
        $this->db->where('transaction_type_id', '2');
        $this->db->where('status_id=1');
        $this->db->where($filter);
        $this->db->group_by('account_no_id');
        $sub_query_1 = $this->db->get_compiled_select();


        $this->db->select('account_no_id,sum(debit) payments, transaction_date');
        $this->db->from('transaction');
        $this->db->where('transaction_type_id', '4');
        $this->db->where('status_id=1');
        $this->db->where($filter);
        $this->db->group_by('account_no_id');
        $sub_query_2 = $this->db->get_compiled_select();

        $this->sub_query_3 = "(SELECT account_id, state_id FROM fms_account_states
                WHERE id in (
                    SELECT MAX(id) from fms_account_states GROUP BY account_id))";

        $this->db->select("a.id, account_no,a.member_id, (ifnull( deposit ,0) ) - (ifnull( payments ,0) ) savings,a.deposit_Product_id, e.deposit, payments, transaction_date,"
            . "a.client_type,concat(salutation,' ', firstname, ' ',lastname,' '"
            . ", othernames) member_name,sp.mandatory_saving,date_opened, k.last_saving_penalty_pay_date");
        $this->db->from('savings_account a');
        $this->db->join('fms_savings_product sp', 'sp.id=a.deposit_Product_id', 'LEFT');
        $this->db->join('member b', 'a.member_id = b.id', 'LEFT');
        $this->db->join('user c', 'b.user_id = c.id', 'LEFT');
        $this->db->join('(' . $sub_query_1 . ') e', 'e.account_no_id = a.id', 'LEFT');
        $this->db->join('(' . $sub_query_2 . ') g', 'g.account_no_id = a.id', 'LEFT');
        $this->db->join("$this->sub_query_3 j", 'j.account_id=a.id', "LEFT");
        $this->db->join("fms_auto_savings k", 'k.savings_account_id=a.id', "LEFT");


        if ($filter === FALSE) {

            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($acc_id)) {
                $this->db->where('a.id=' . $acc_id);
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter2);
                $query = $this->db->get();
                //print_r( $this->db->last_query());
                return $query->result_array();
            }
        }
    }

    private function get_select_trans()
    {
        $this->db->from($this->table);
        $this->db->distinct();
        $this->db->select('fg.group_name');
        $this->db->join('fms_savings_account sa', 'tn.account_no_id=sa.id', 'left');
        $this->db->join('fms_group_member fgm', 'fgm.group_id=sa.member_id AND sa.client_type=2', 'left');
        $this->db->join('fms_group fg', 'fg.id=fgm.group_id', 'left');
        $this->db->join('fms_transaction_type tt', 'tn.transaction_type_id=tt.id', 'left');
        $this->db->join('fms_member m', 'sa.member_id=m.id', 'left');
        $this->db->join('fms_user u', 'm.user_id=u.id', 'left');
        $this->db->join('fms_payment_mode tc', 'tn.payment_id=tc.id', 'left');
        $this->db->order_by('transaction_date DESC');
        $this->db->order_by('transaction_type_id DESC');;
        if (is_numeric($this->input->post('acc_id'))) {
            $this->db->where('sa.id=', $this->input->post('acc_id'));
        }
        if (is_numeric($this->input->post('status_id'))) {
            $this->db->where('tn.status_id=', $this->input->post('status_id'));
        }
    }

    public function get_per_month($start_date, $end_date)
    {
        $query = $this->db->query("SELECT d1.month,d1.debit_sum,d1.credit_sum,d1.balance from(SELECT MONTH(transaction_date) as month, SUM(ifnull(debit ,0)) as debit_sum, SUM(ifnull(credit ,0)) as credit_sum, (SUM(ifnull(credit ,0))-SUM(ifnull(debit ,0))) as balance from fms_transaction WHERE transaction_date >='$start_date' AND transaction_date <= '$end_date' AND status_id=1 group by MONTH(transaction_date)) d1 ORDER BY d1.month ASC");
        //print_r($this->db->last_query()); die(); 
        return $query->result_array();
    }


    public function get_trans($filter = FALSE)
    {
        $this->db->select('count(tn.id) no_rows');
        $this->get_select_trans();
        $query = $this->db->get();
        $result = $query->row_array();
        return isset($result['no_rows']) ? $result['no_rows'] : 0;
    }

    public function get_dTable_trans()
    {
        $all_columns = $this->input->post('columns');
        $this->db->select("SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $this->columns)), FALSE);
        $this->get_select_trans();
        $this->set_filters($all_columns);

        if ($this->input->post('order') !== NULL && $this->input->post('order') !== '') {
            $order_columns = $this->input->post('order');

            foreach ($order_columns as $order_column) {
                if (isset($order_column['column']) && $all_columns[$order_column['column']]['orderable'] == "true") {
                    $this->db->order_by(preg_replace($this->alias_only_pattern, '', $this->columns[$order_column['column']]), $order_column['dir']);
                }
            }
        }
        if ($this->input->post('start') !== NULL && is_numeric($this->input->post('start')) && $this->input->post('length') != '-1') {
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        }

        if (!empty($_POST['start_date'])) {
            $start_date = str_replace('-', '', $_POST['start_date']);
            $this->db->where('DATE(tn.transaction_date) >=' . $start_date);
        }

        if (!empty($_POST['end_date'])) {
            $end_date = str_replace('-', '', $_POST['end_date']);
            $this->db->where('DATE(tn.transaction_date) <= ' . $end_date);
        }

        $query = $this->db->get();
        // print_r($this->db->last_query()); die;
        return $query->result_array();
    }
    public function get_dTable_trans2($filter = FALSE)
    {
        $this->db->select('tn.*,sa.account_no,sa.client_type,COALESCE((SELECT SUM(ifnull(credit,0)) - SUM(ifnull(debit,0))
                              FROM fms_transaction b
                              WHERE tn.transaction_date >= b.transaction_date 
                               AND b.account_no_id = tn.account_no_id
                               AND b.status_id = 1 ),0)
                                 AS end_balance,tc.payment_mode,tt.type_name');
        $this->db->from('fms_transaction tn');
        $this->db->join('fms_savings_account sa', 'tn.account_no_id=sa.id', 'left');
        $this->db->join('fms_transaction_type tt', 'tn.transaction_type_id=tt.id', 'left');
        $this->db->join('fms_payment_mode tc', 'tn.payment_id=tc.id', 'left');
        $this->db->order_by('tn.transaction_date DESC');
        $this->db->order_by('tn.transaction_type_id DESC');

        if (!empty($_POST['start_date'])) {
            $start_date = str_replace('-', '', $_POST['start_date']);
            $this->db->where('DATE(tn.transaction_date) >=' . $start_date);
        }

        if (!empty($_POST['end_date'])) {
            $end_date = str_replace('-', '', $_POST['end_date']);
            $this->db->where('DATE(tn.transaction_date) <= ' . $end_date);
        }

        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else if (is_numeric($filter)) { //when given the primary key
            $this->db->where('tn.account_no_id', $filter);
            $query = $this->db->get();
            return $query->result_array();
        } else {
            $this->db->where($filter);
            $query = $this->db->get();
            return $query->result_array();
        }
    }


    private function set_filters($all_columns)
    {
        if ($this->input->post("search") !== NULL) {
            $search = $this->input->post("search");
            if (isset($search['value']) && $search['value'] != "") {
                $this->db->group_start();
                for ($i = 0; $i < count($this->columns); $i++) {
                    if (isset($all_columns[$i]['searchable']) && $all_columns[$i]['searchable'] == "true") {
                        $column = preg_replace($this->alias_only_pattern, '', $this->columns[$i]);
                        $this->db->or_like($column, $search['value']);
                    }
                }
                // Individual column filtering
                foreach ($this->columns as $key) {
                    if (isset($all_columns[$key]['searchable']) && $all_columns[$key]['searchable'] == "true" && $all_columns[$key]['search']['value'] != '') {
                        $this->db->or_like(preg_replace($this->alias_only_pattern, '', $this->columns[$key]), $all_columns[$key]["search"]["value"]);
                    }
                }
                $this->db->group_end();
            }
        }
    }

    ////////////////////////////////////
    //==========================================================================

    public function get_found_rows()
    {
        $this->db->select("FOUND_ROWS()", FALSE);
        $q = $this->db->get();
        return $q->row_array();
    }

    ///////////////////////////////////////////

    public function set($account_no_id = FALSE, $transaction_type_id = FALSE, $amount = FALSE, $last_id = false, $narrative = false)
    {   //for both withdraws and deposits
        $data = $this->input->post(NULL, TRUE);

        if ($this->input->post('client_type') == 2 && $this->input->post('transaction_type_id') == 2) {
            $group_member_id = $this->input->post('group_member_id');
        } else {
            $group_member_id = NULL;
        }
        $charges = isset($data['charges']) ? $data['charges'] : [];

        unset(
            $data['id'],
            $data['client_type'],
            $data['account_details'],
            $data['memberFees'],
            $data['member_id'],
            $data['accept_note'],
            $data['tbl'],
            $data['charges'],
            $data['state_id'],
            $data['print'],
            $data['charge_id'],
            $data['savings_account_no'],
            $data['savings_account_id'],
            $data['total_charges'],
            $data['opening_balance'],
            $data['cash_bal'],
            $data['transaction_channel_id'],
            $data['share_acc_id'],
            $data['share_issuance_id'],
            $data['client_id'],
            $data['subscription_date'],
            $data['account_no'],
            $data['amount'],
            $data['total_dividends'],
            $data['dividend_per_share'],
            $data['dividends_payable_acc_id'],
            $data['dividends_cash_acc_id'],
            $data['dividend_declaration_id'],
            $data['declaration_date'],
            $data['record_date'],
            $data['excess_for'],
            $data['mandatory_saving'],
            $data['memberFees1'],
            $data['fee_paid'],
            $data['member_fee_id'],
            $data['save_more'],
            $data['ahead_or'],
            $data['sub_fee_paid'],
            $data['transaction_charge_type_id'],
            $data['shares_account_id'],
            $data['transfer_share_issuance_id'],
            $data['share_account_no']
        );
        if ($this->input->post('transaction_type_id') == 3) {
            $data['account_no_id'] = $account_no_id;
            if ($transaction_type_id != FALSE) {
                $data['narrative'] = "[ Transfer From " . $this->input->post('account_no') . " ] " . $this->input->post('narrative');
                $data['ref_no'] = $last_id;
            } else {
                $data['narrative'] = "[ Transfer To " . $this->input->post('savings_account_no') . " ] " . $this->input->post('narrative');
            }
        } else if ($transaction_type_id == 4) {
            if (is_numeric($account_no_id)) {
                $data['account_no_id'] = $account_no_id;
            } else {
                $data['account_no_id'] = $this->input->post('account_no_id');
            }
            $data['ref_no'] = $last_id;
            $data['narrative'] = $narrative . " " . $this->input->post('narrative');
        } else {
            if (is_numeric($account_no_id)) {
                $data['account_no_id'] = $account_no_id;
            } else {
                $data['account_no_id'] = $this->input->post('account_no_id');
            }
            $data['narrative'] = $this->input->post('narrative');
        }
        if (is_numeric($transaction_type_id)) {
            $data['transaction_type_id'] = $transaction_type_id;
        } else {
            $data['transaction_type_id'] = $this->input->post('transaction_type_id');
        }
        if (is_numeric($amount)) {
            if (($this->input->post('transaction_type_id') == 2) || ($transaction_type_id == 2) || ($transaction_type_id == 12)) {
                $data['credit'] = $amount;
            } else {
                $data['debit'] = $amount;
            }
        } else {
            if (($this->input->post('transaction_type_id') == 2) || ($transaction_type_id == 2)) {
                $data['credit'] = $this->input->post('amount');
            } else {
                $data['debit'] = $this->input->post('amount');
            }
        }
        $data['transaction_no'] = date('ymdhms') . mt_rand(100, 999);
        //$data['transaction_no'] = date('yws').random_int(100, 999);
        $transaction_date = explode('-', $this->input->post('transaction_date'), 3);
        $data_transaction_date = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;

        $data_transaction_date = $data_transaction_date ? $data_transaction_date : date('Y-m-d');
        $data['transaction_date'] = $this->get_date_time($data_transaction_date);

        $data['group_member_id'] = $group_member_id;
        $data['date_created'] = time();
        $data['status_id'] = 1;
        $data['created_by'] = $_SESSION['id'];

        $this->db->insert('fms_transaction', $data);
        $last_id = $this->db->insert_id();
        if (is_numeric($transaction_type_id)) {
        } else {
            if (($proper_entries = $this->insert_batch_charges($charges, $last_id, $data, $data_transaction_date)) != false) {
                $this->db->insert_batch('fms_transaction', $proper_entries);
            }
        }
        if (is_numeric($last_id)) {
            $response['transaction_no'] = $data['transaction_no'];
            $response['transaction_id'] = $last_id;
            return $response;
        } else {
            return false;
        }
        //end the transaction
    }



    public function set_interest_payout($account_no_id = FALSE, $transaction_type_id = FALSE, $amount = FALSE, $last_id = false, $narrative = false, $transaction_date)
    {

        $data['account_no_id'] = $account_no_id;
        $data['ref_no'] = $last_id;
        $data['narrative'] = $narrative;
        $data['payment_id'] = 5;
        $data['transaction_type_id'] = $transaction_type_id;
        if ($transaction_type_id == 2) {
            $data['credit'] = $amount;
        } else {
            $data['debit'] = $amount;
        }
        $data['transaction_no'] = date('ymdhms') . mt_rand(100, 999);
        $data['transaction_date'] = $this->get_date_time($transaction_date);
        $data['date_created'] = time();
        $data['status_id'] = 1;
        $data['created_by'] = 1;

        $this->db->insert('fms_transaction', $data);
        $last_id = $this->db->insert_id();
        if (is_numeric($last_id)) {
            $response['transaction_no'] = $data['transaction_no'];
            $response['transaction_id'] = $last_id;
            return $response;
        } else {
            return false;
        }
        //end the transaction
    }

    public function mm_set($data, $charges)
    {
        $insert_data['transaction_no'] = date('ymdhms') . mt_rand(100, 999);
        $insert_data['account_no_id'] = $data['account_no_id'];
        $insert_data['payment_id'] = $data['payment_id'];
        if ($data['transaction_type_id'] == 2) {
            $insert_data['credit'] = $data['amount'];
        } else {
            $insert_data['debit'] = $data['amount'];
        }
        $insert_data['narrative'] = $data['narrative'];
        $insert_data['transaction_type_id'] = $data['transaction_type_id'];
        $insert_data['transaction_date'] = $this->get_date_time(date('Y-m-d'));
        $insert_data['group_member_id'] = $data['group_member_id'];
        $insert_data['date_created'] = time();
        $insert_data['status_id'] = isset($data['status_id']) ? $data['status_id'] : 1;
        $insert_data['created_by'] = $_SESSION['id'];
        $this->db->insert('fms_transaction', $insert_data);
        $last_id = $this->db->insert_id();
        if (($proper_entries = $this->insert_batch_mm_charges($charges, $last_id, $data['amount'])) != false) {
            $this->db->insert_batch('fms_transaction_charges', $proper_entries);
        }
        if (is_numeric($last_id)) {
            if (isset($insert_data['transaction_no'])) {
                $response['transaction_no'] = $insert_data['transaction_no'];
            }
            $response['transaction_id'] = $last_id;
            return $response;
        } else {
            return false;
        }
    }
    public function bulk_set($data, $charges = false)
    {
        $this->db->insert('fms_transaction', $data);
        $last_id = $this->db->insert_id();
        // if (($proper_entries = $this->insert_batch_bulk_charges($charges, $last_id,$data)) != false) {
        //$this->db->insert_batch('fms_transaction', $proper_entries);
        //} 
        if (is_numeric($last_id)) {
            $response['amount'] = $data['credit'];
            $response['account_no_id'] = $data['account_no_id'];
            $response['transaction_no'] = $data['transaction_no'];
            $response['transaction_id'] = $last_id;
            return $response;
        } else {
            return false;
        }
    }
    public function get_transaction($filter = false)
    {
        $this->db->select('fms_transaction.*,staff_no,concat(u.firstname," ", u.lastname," ", u.othernames) AS member_name,concat(gu.firstname," ", gu.lastname," ", gu.othernames) AS gp_member_name,sa.client_type,sa.account_no,branch_name,physical_address,office_phone,postal_address,email_address');
        $this->db->from('fms_transaction');
        $this->db->join('fms_savings_account sa', 'fms_transaction.account_no_id=sa.id', 'left');
        $this->db->join('group_member group', 'fms_transaction.group_member_id=group.id', 'left');
        $this->db->join('member gm', 'group.member_id=gm.id', 'left');
        $this->db->join('member m', 'sa.member_id=m.id', 'left');
        $this->db->join('user gu', 'gm.user_id=gu.id', 'left');
        $this->db->join('user u', 'm.user_id=u.id', 'left');
        $this->db->join('staff', 'fms_transaction.created_by=staff.id', 'left');
        $this->db->join('fms_branch br', 'staff.branch_id=br.id', 'left');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("transaction.id=", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function insert_batch_charges($charges, $last_id, $data, $date)
    { //prepare the array
        $track = 0;
        $entries = array();
        foreach ($charges as $key => $value) {
            if ($value['charge_id'] == '' || $value['charge_amount'] == '') {
                $track += 1;
            } else {
                $charge['transaction_no'] = date('ymdhms') . mt_rand(100, 999);
                $charge['account_no_id'] = $data['account_no_id'];
                $charge['debit'] =  $value['charge_amount'];
                $charge['transaction_type_id'] = $this->input->post('charge_id');
                $charge['ref_no'] = $last_id;
                $charge['payment_id'] = $this->input->post('payment_id');
                $charge['transaction_date'] = $this->get_date_time($date);
                $charge['narrative'] = $data['narrative'];
                $charge['date_created'] = time();
                $charge['created_by'] = $_SESSION['id'];
                $entries[] = $charge;
            }
        }
        return ($track === 0) ? $entries : false;
    }
    public function insert_batch_mm_charges($charges, $last_id, $amount)
    { //prepare the array
        $track = 0;
        $entries = array();
        foreach ($charges as $key => $value) {
            if ($value['id'] == '' || $value['cal_method_id'] == 0) {
                $track += 1;
            } else {
                $charge['transaction_no'] = date('ymdhms') . mt_rand(000, 999);
                $charge['account_no_id'] = $data['account_no_id'];
                $charge['debit'] =  ($value['cal_method_id'] == 1) ? (($value['amount'] * $amount) / 100) : $value['amount'];
                $charge['transaction_type_id'] = $value['id'];
                $charge['ref_no'] = $last_id;
                $charge['payment_id'] = 4;
                $charge['transaction_date'] = $this->get_date_time(date('Y-m-d'));
                $charge['narrative'] = $value['narrative'];
                $charge['date_created'] = time();
                $charge['created_by'] = $_SESSION['id'];
                $entries[] = $charge;
            }
        }
        return ($track === 0) ? $entries : false;
    }

    public function insert_batch_bulk_charges($charges, $last_id, $data)
    { //prepare the array
        $track = 0;
        $entries = array();
        $transaction_date = explode('-', $this->input->post('transaction_date'), 3);
        $data_transaction_date = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;

        foreach ($charges as $key => $value) {
            if ($value['id'] == '' || $value['cal_method_id'] == 0) {
                $track += 1;
            } else {
                $charge['transaction_no'] = date('ymdhms') . mt_rand(000, 999);
                $charge['account_no_id'] = $data['account_no_id'];
                $charge['debit'] =  ($value['cal_method_id'] == 1) ? (($value['amount'] * $data['amount']) / 100) : $value['amount'];
                $charge['transaction_type_id'] = 7;
                $charge['ref_no'] = $last_id;
                $charge['payment_id'] = $this->input->post('payment_id');
                $charge['transaction_date'] = $this->get_date_time($data_transaction_date);
                $charge['narrative'] = "Deposit Charge [ " . $data['narrative'] . " ]";
                $charge['date_created'] = time();
                $charge['created_by'] = $_SESSION['id'];
                $entries[] = $charge;
            }
        }
        return ($track === 0) ? $entries : false;
    }

    public function bulk_error_log($failed_records)
    {
        $this->db->insert('fms_bulk_deposit_error_log', $failed_records);
    }

    public function update_transaction($data)
    {
        $data['date_modified'] = time();
        $data['modified_by'] = $_SESSION['id'];
        $this->db->where('id', $data['id']);
        $this->db->update('fms_transaction', $data);
    }

    public function update()
    {
        $data = $this->input->post(NULL, TRUE);
        $charges = $data['charges'];
        if ($this->input->post('client_type') == 2) {
            $group_member_id = $this->input->post('group_member_id');
        } else {
            $group_member_id = '';
        }
        unset($data['id'], $data['client_type'], $data['tbl'], $data['charges'], $data['state_id'], $data['opening_balance'], $data['cash_bal']);
        $transaction_date = explode('-', $this->input->post('transaction_date'), 3);
        $data['transaction_date'] = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
        $group_member_id = NULL;
        $data['date_modified'] = time();
        $data['modified_by'] = $_SESSION['id'];

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('fms_transaction', $data);
        $last_id = $this->input->post('id');
        $complete = $this->db->update_batch('fms_transaction', $this->insert_batch_charges($charge, $last_id), array('transaction_id' => $last_id));
        return $complete;
        //end the transaction
    }

    //  public function get_savingsm_per_month($filter = FALSE) {  //prop per user

    //  $query = $this->db->query('SELECT d1.month,d1.count from(SELECT MONTH(DATE_OF_VAL) as month, SUM(amount) as count from property WHERE YEAR(DATE_OF_VAL)='.$filter.' group by MONTH(DATE_OF_VAL)) d1');

    //      return $query->result_array();
    // }

    public function edit_transaction($filter = FALSE)
    {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['tbl']);
        $transaction_date = explode('-', $this->input->post('transaction_date'), 3);
        $data_transaction_date = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;

        $data['transaction_date'] = $this->get_date_time($data_transaction_date);
        $data['modified_by'] = $this->session->userdata('id');
        $this->db->where('id', $this->input->post('id'));
        $this->db->or_where('ref_no', $this->input->post('id'));
        return $this->db->update('transaction', $data);
    }

    public function edit_journal_transaction($filter = FALSE)
    {
        $transaction_date = explode('-', $this->input->post('transaction_date'), 3);
        $transaction_date_final = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
        $data = array(
            'transaction_date' => $transaction_date_final,
            'modified_by' => $_SESSION['id'],
            'description' => $this->input->post('narrative')
        );
        $data2 = array(
            'transaction_date' => $transaction_date_final,
            'modified_by' => $_SESSION['id'],
            'narrative' => $this->input->post('narrative')
        );

        $this->db->where('ref_no', $this->input->post('transaction_no'));
        $this->db->where('ref_id', $this->input->post('id'));
        if ($this->db->update('journal_transaction', $data)) {
            $this->db->where('reference_no', $this->input->post('transaction_no'));
            $this->db->where('reference_id', $this->input->post('id'));
            return $this->db->update('journal_transaction_line', $data2);
        } else {
            return false;
        }
    }

    public function should_reverse($id)
    {
        $fiscal_year = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
        $end_date = $fiscal_year['end_date'];
        $start_date = $fiscal_year['start_date'];
        $this->db->select("*");
        $this->db->where('id=' . $id);
        $this->db->from("journal_transaction");
        $query = $this->db->get();
        $results = $query->row_array();
        $trans_date = $results['transaction_date'];

        if (($trans_date >= $start_date) && ($trans_date <= $end_date)) {
            return true;
        }
        return false;
    }

    public function reverse($external_ref_no = false)
    {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['transaction_no'], $data['transaction_type_id']);
        $data['reversed_by'] = $_SESSION['id'];
        $data['reversed_date'] = date("Y-m-d H:i:s");
        $data['reverse_msg'] = $this->input->post('reverse_msg');
        $data['status_id'] = 3;

        if (is_numeric($id)) {

            if ($this->should_reverse($id)) {
                if ($external_ref_no != false) {
                    $this->db->where('ref_no', $external_ref_no);
                    return $this->db->update('transaction', $data);
                } else {
                    // $this->db->where('ref_no', $id);
                    // $this->db->update('transaction', $data);
                    $this->db->where('id', $id);
                    return $this->db->update('transaction', $data);
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function total_transactions($filter = FALSE)
    {  //get total transaction amount per account
        $this->db->select('SUM(amount) as amount', FALSE);
        $this->db->from('fms_transaction');
        $this->db->where('status_id', 1);

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if (is_numeric($filter) && !is_array($filter)) {
                $this->db->where('account_no_id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->row_array();
            }
        }
    }

    public function deduct_savings($sent_data, $unique_id = false)
    {
        if (isset($sent_data['transaction_date'])) {
            $transaction_date = explode('-', $sent_data['transaction_date'], 3);
            $data_transaction_date = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
            $data['transaction_date'] = $this->get_date_time($data_transaction_date);
        } else {
            $data['transaction_date'] = $this->get_date_time(date('Y-m-d'));
        }
        $data['transaction_no'] = date('ymdhms') . mt_rand(100000, 999999);
        $data['account_no_id'] = $sent_data['account_no_id'];
        if (array_key_exists('transaction_type_id', $sent_data) && $sent_data['transaction_type_id'] == 2) {
            $data['credit'] = $sent_data['amount'];
        } else {
            $data['debit'] = $sent_data['amount'];
        }

        if (array_key_exists('ref_no', $sent_data)) {
            $data['ref_no'] = $sent_data['ref_no'];
        } else {
            $data['ref_no'] = NULL;
        }
        $data['transaction_type_id'] = (isset($sent_data['transaction_type_id'])) ? $sent_data['transaction_type_id'] : 4;
        $data['narrative'] = $sent_data['narrative'];
        $data['status_id'] = 1;
        $data['payment_id'] = 5; #uncomment after adding this column in the transaction table
        $data['date_created'] = time();
        $data['unique_id'] = $unique_id;
        $data['created_by'] = (isset($_SESSION) && isset($_SESSION['id'])) ? $_SESSION['id'] : 1; #system user id
        $this->db->insert('fms_transaction', $data);
        $last_id = $this->db->insert_id();
        $response['transaction_no'] = $data['transaction_no'];
        $response['transaction_id'] = $last_id;
        return $response;
    }

    public function delete()
    {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fms_transaction', ['status_id' => 0]);
    }


    public function get_date_time($date)
    {
        if ($date != null) {
            $t = microtime(true);
            $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
            $d2 = new DateTime($date . " " . date('H:i:s.' . $micro, $t));
            return $d2->format("Y-m-d H:i:s.u");
        } else {
            return null;
        }
    }

    /*
==============================================================================================================================
ASSETS REPORTS
==============================================================================================================================
    */
    /*public function get_asset_purchase_per_month($start_date, $end_date) { 
        $query = $this->db->query("SELECT DISTINCT d1.month,d1.debit_sum,d1.credit_sum,d1.balance from(SELECT DISTINCT MONTH(transaction_date) as month,(CASE WHEN transaction_type_id=2 then SUM(amount) else '0' end) as debit_sum,(CASE WHEN transaction_type_id=1 then  SUM(amount) else '0' end) as credit_sum, (SUM(ifnull(amount ,0))-SUM(ifnull(amount ,0))) as balance from fms_asset_payment WHERE transaction_date >='$start_date' AND transaction_date <= '$end_date' AND status_id=1 group by MONTH(transaction_date),transaction_type_id) d1  ORDER BY d1.month ASC");
       //print_r($this->db->last_query()); die(); 
         return $query->result_array();
    }*/

    public function asset_full_list()
    {
        $query = $this->db->query("SELECT DISTINCT id,asset_name,purchase_cost,purchase_date,dpr_amount,apr_amount,expected_age,dpr_apr_rate,
(CASE WHEN loss_or_gain=1 AND depre_appre_id=1  THEN (purchase_cost-dpr_amount)-cash     
WHEN loss_or_gain=1 AND depre_appre_id=2 THEN(purchase_cost+apr_amount)-cash ELSE 0 end)as loss,
(CASE WHEN loss_or_gain=2 AND depre_appre_id=1 THEN cash-(purchase_cost-dpr_amount) 
     WHEN loss_or_gain=2 AND depre_appre_id=2 THEN cash-(purchase_cost+apr_amount) ELSE 0 end)as gain,
IFNULL(cash,0)as cash,
(CASE WHEN depre_appre_id=1 THEN purchase_cost-dpr_amount
 ELSE purchase_cost+apr_amount end )as asset_value, disposal_status
 FROM 
(SELECT fa.id,asset_name,purchase_cost,purchase_date,SUM(IFNULL(dpr.amount,0))as dpr_amount,
SUM(IFNULL(apr.amount,0)) as apr_amount,fa.expected_age,(CASE WHEN depre_appre_id=1 then fa.depreciation_rate else fa.appreciation_rate 
end)dpr_apr_rate,depre_appre_id,
(CASE  WHEN  IFNULL(apr.amount,0) THEN (purchase_cost+apr.amount)WHEN  IFNULL(dpr.amount,0) THEN (purchase_cost-dpr.amount) ELSE purchase_cost end) as asset_value,
(CASE WHEN fa.status_id=4 THEN 'disposed off' ELSE 'active' end) as disposal_status
FROM fms_fixed_assets fa
LEFT JOIN fms_depreciation dpr
ON(fa.id=dpr.fixed_asset_id)
LEFT JOIN fms_appreciation apr
on(fa.id=apr.fixed_asset_id)  
GROUP BY fa.id
 )q
 LEFT JOIN 
 (SELECT SUM(IFNULL(amount,0)) as cash,asset_id,loss_or_gain FROM fms_asset_payment ap
 WHERE ap.transaction_type_id=1 
 GROUP BY ap.asset_id)r
 on(q.id=r.asset_id)");

        return $query->result_array();
    }


    public function sum_savings_amounts($filter)
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $this->db->select("SUM(ifnull(credit,0)) as credit_amount, SUM(ifnull(debit,0)) as debit_amount");
        $this->db->from("fms_transaction");
        $this->db->where("status_id=1");
        $this->db->where($filter);

        if ($start_date) {
            $this->db->where("DATE(transaction_date) >= '{$start_date}'");
        }
        if ($end_date) {
            $this->db->where("DATE(transaction_date) <= '{$end_date}'");
        }

        $query = $this->db->get();
        $result = $query->row_array();
        if (!is_numeric($result['credit_amount'])) $result['credit_amount'] = 0;
        if (!is_numeric($result['debit_amount'])) $result['debit_amount'] = 0;

        return $result;
    }
}
