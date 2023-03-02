<?php

/**
 * Journal Transaction Line Model
 *  */
class Journal_transaction_line_model extends CI_Model {
  private $columns = ["jtl.id", "jtl.transaction_date", "jt.ref_no", "jt.ref_id","jtp.type_name","jtl.reference_key", "jt.description","credit_amount", "debit_amount","jtl.date_created", "jt.status_id", "jt.reverse_msg", "jt.reversed_date"];
    private $alias_only_pattern = '/(\s+(as[\s]+)?)((`)?[a-zA-Z0-9_]+(`)?)$/';

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select("jtl.id, account_id,jtl.transaction_date,credit_amount, debit_amount,narrative, journal_transaction_id");
        $this->db->select("ac.account_name,ac.account_code,acat.normal_balance_side");
        $this->db->from("journal_transaction_line jtl");
        $this->db->join("accounts_chart ac", "ac.id=jtl.account_id", 'left');
        $this->db->join("account_sub_categories sc", "sc.id=ac.sub_category_id", "LEFT");
        $this->db->join("account_categories acat", "acat.id=sc.category_id", "LEFT");
        $this->db->join("journal_transaction jt", "jt.id=jtl.journal_transaction_id", 'left');

        if ($this->input->post("journal_transaction_id") != NULL) {
            $this->db->where("journal_transaction_id", $this->input->post("journal_transaction_id"));
        }
         
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('`id`', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
     private function get_select() {
        $this->db->select("ac.account_name,ac.account_code,concat(u.lastname, ' ', u.firstname, ' ', u.othernames) member_name,concat(su.lastname, ' ', su.firstname, ' ', su.othernames) staff_name");
        $this->db->from("journal_transaction_line jtl");
        $this->db->join("accounts_chart ac", "ac.id=jtl.account_id", 'left');
        $this->db->join("member", "member.id = jtl.member_id","left");
        $this->db->join("user u", "member.user_id = u.id","left");
        $this->db->join("user su", "jtl.created_by = su.id","left");
        $this->db->join("journal_transaction jt", "jt.id=jtl.journal_transaction_id", 'left');
        $this->db->where("jt.status_id IN(1)");
        $this->db->where("jtl.status_id IN(1)");

        if ($this->input->post("account_id") != NULL) {
            $this->db->where("account_id", $this->input->post("account_id"));
        }
        if (is_numeric($this->input->post("created_by")) && $this->input->post("all")==1) {
            $this->db->where("jtl.created_by", $this->input->post("created_by"));
        }
        if ($this->input->post("journal_transaction_id") != NULL) {
            $this->db->where("journal_transaction_id", $this->input->post("journal_transaction_id"));
        }
        if ($this->input->post("start_date") != NULL && $this->input->post("end_date") != NULL) {
            $start_date = date('Y-m-d',strtotime($this->input->post("start_date")));
            $end_date = date('Y-m-d',strtotime($this->input->post("end_date")));
            $where_clause = "(jtl.transaction_date BETWEEN '$start_date' AND '$end_date')";
            $this->db->where($where_clause);
        }
    }
    public function get2() {
        $this->db->select('count(jtl.id) no_rows');
        $this->get_select();
        $query = $this->db->get();
        $result = $query->row_array();
        return isset($result['no_rows'])?$result['no_rows']:0;
    }
  

    public function get_dTable() {
        $all_columns = $this->input->post('columns');
        $this->db->select("SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $this->columns)), FALSE);
        $this->db->select("jtl.id, account_id,credit_amount, debit_amount,narrative, journal_transaction_id");
        $this->db->select("jtl.transaction_date, ref_no, ref_id,journal_type_id, jtp.type_name");
        $this->get_select();
        $this->db->join("journal_type jtp", "jtp.id=jt.journal_type_id", 'left');
        $this->set_filters($all_columns);

        if ($this->input->post('order') !== NULL && $this->input->post('order') !== '') {
            $order_columns = $this->input->post('order');
            
            foreach ($order_columns as $order_column) {
                if (isset($order_column['column']) && $all_columns[$order_column['column']]['orderable'] == "true") {
                    $this->db->order_by(preg_replace($this->alias_only_pattern, '', $this->columns[$order_column['column']]), $order_column['dir']);
                }
            }/*print_r($order_columns);*/
        }
        if ($this->input->post('start') !== NULL && is_numeric($this->input->post('start')) && $this->input->post('length') != '-1') {
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        }
        $this->db->where('jtl.status_id', 1);
        $query = $this->db->get();
        //print_r($this->db->last_query()); die;
        return $query->result_array();
    }
    public function get_expenses($filter = 1) {
        
        
        $this->db->select("jtl.*, ach.account_name, asub.sub_cat_name");
        $this->db->from("fms_journal_transaction_line jtl");
        $this->db->join("fms_journal_transaction jt", "jt.id=jtl.journal_transaction_id", "LEFT");
        $this->db->join("fms_accounts_chart ach", "ach.id=jtl.account_id", "LEFT");
        $this->db->join("fms_account_sub_categories asub", "asub.id=ach.sub_category_id", "LEFT");
        $this->db->where(" asub.category_id = 5 AND jtl.status_id=1 AND jtl.debit_amount>0 AND jt.status_id=1 ");
        $this->db->where($filter);
                
        $query = $this->db->get();
        //print_r($this->db->last_query()); die;
        return $query->result_array();
    }


    private function set_filters($all_columns) {
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
        /* if (isset($_SESSION) && isset($_SESSION['branch_id']) && is_numeric($_SESSION['branch_id'])) {
          $this->db->where("branch_id =", (int) $_SESSION['branch_id']);
          } */
    }
  public function get_found_rows() {
        $this->db->select("FOUND_ROWS()", FALSE);
        $q = $this->db->get();
        return $q->row_array();
    }

    public function get_income_account($filter = FALSE) {
        $this->db->select("jtl.*, ref_no, ref_id,journal_type_id, jtp.type_name");
        $this->db->from("journal_transaction_line jtl");
        $this->db->join("journal_transaction jt", "jt.id=jtl.journal_transaction_id", 'left');
        $this->db->join("journal_type jtp", "jtp.id=jt.journal_type_id", "LEFT");
        
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('`jtl`.`id`', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->row_array();
            }
        }
    }
    public function set($journal_transaction_id, $data = []) {

        $ids = $ids2 = [];
        if (is_array($data) && empty($data)) {
            $data = $this->input->post('journal_transaction_line');
        }
        foreach ($data as $key => $value) {//it is a new entry, so we insert afresh
            if (isset($value['account_id']) && is_numeric($value['account_id']) && ((isset($value['debit_amount']) && is_numeric($value['debit_amount'])) || (isset($value['credit_amount']) && is_numeric($value['credit_amount'])))) {
                $value['journal_transaction_id'] = $journal_transaction_id;
                $value['modified_by'] = isset($_SESSION['id'])?$_SESSION['id']:1;
                if (isset($value['credit_amount']) && is_numeric($value['credit_amount'])) {
                    $value['credit_amount'] = $value['credit_amount'];
                    $value['debit_amount'] = NULL;
                } else {
                    $value['debit_amount'] = $value['debit_amount'];
                    $value['credit_amount'] = NULL;
                }
                $value['transaction_date'] = $this->helpers->yr_transformer($value['transaction_date']);
                if (isset($value['id']) && is_numeric($value['id']) && $value['id'] !== '') {
                    $ids[] = $value['id'];
                    $this->db->where('id', $value['id']);
                    unset($value['id']);
                    $this->db->update('journal_transaction_line', $value);
                } else {
                    unset($value['id']);
                    $value['created_by'] = $value['modified_by'];
                    $value['date_created'] = time();
                    $this->db->insert('journal_transaction_line', $value);
                    $ids2[] = $this->db->insert_id();
                }
            }
        }
        return $this->update_delete($ids, $ids2);
    }
    private function get_jtrlids($journal_transaction_id = FALSE) {
        $this->db->select('id');
        $this->db->from('journal_transaction_line');
        $this->db->where("journal_transaction_id", $journal_transaction_id);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function set2($journal_transaction_id,$value) {
        $data = $this->input->post(NULL, TRUE);
            $value['journal_transaction_id'] = $journal_transaction_id;
            $value['modified_by'] = $_SESSION['id'];
            $value['transaction_date'] = $this->helpers->yr_transformer($data['transaction_date']);
            $value['reference_no'] = $data['ref_no'];

            if (isset($value['credit_amount']) && is_numeric($value['credit_amount'])) {
                $value['credit_amount'] = $value['credit_amount'];
                $value['debit_amount'] = NULL;
            } else {
                $value['debit_amount'] = $value['debit_amount'];
                $value['credit_amount'] = NULL;
            }

            if (isset($value['id']) && is_numeric($value['id']) && $value['id'] !== '') {
                $ids[] = $value['id'];
                $this->db->where('id', $value['id']);
                unset($value['id']);
                return $this->db->update('journal_transaction_line', $value);
            } else {
                unset($value['id']);
                $value['created_by'] = $value['modified_by'];
                $value['date_created'] = time();
                return $this->db->insert('journal_transaction_line', $value);
            }

       // return $this->update_delete($ids, $ids2);
    }
    public function delete_lines($journal_transaction_id){
         $data = $this->input->post('journal_transaction_line');
         $db_ids = array_column($this->get_jtrlids($journal_transaction_id), 'id');
         $form_ids = array_column($data, 'id');
         $diff_result = array_diff($db_ids, $form_ids);
         if (!empty($diff_result)) { //DELETED LINES FIRST
            $this->db->where_in('id', $diff_result);
            return $this->db->delete('journal_transaction_line');
         } else{
            return true;
         }
    }

    public function set_auto($data){
        return $this->db->insert_batch('journal_transaction_line', $data);
    }

    public function set_open_balance_line($account_id, $journal_transaction_id, $new_opbal = false) {
        $data = [];
        $data['journal_transaction_id'] = $journal_transaction_id;
        $data['narrative'] = "Opening Balance";
        $data['account_id'] = $account_id;
        $data['transaction_date'] = $this->helpers->yr_transformer($this->input->post('opening_balance_date'));
        if ($this->input->post('normal_balance_side') == 1) {
            $data['debit_amount'] = $this->input->post('opening_balance');
            $data['credit_amount'] = NULL;
        } else {
            $data['credit_amount'] = $this->input->post('opening_balance');
            $data['debit_amount'] = NULL;
        }
        $data['date_created'] = time();
        $data['modified_by'] = $_SESSION['id'];

        if (is_numeric($this->input->post('id')) && $new_opbal === FALSE) { //when updating
            $this->db->where('account_id', $this->input->post('id'));
            $this->db->where('narrative', "Opening Balance");
            unset($data['journal_transaction_id'], $data['account_id'], $data['narrative'], $data['tbl']);
            return $this->db->update('journal_transaction_line', $data);
        } else { //when saving for the first time
            $data['created_by'] = $data['modified_by'];
            $this->db->insert('journal_transaction_line', $data);
            return $this->db->insert_id();
        }
    }

    //deletes entries given a particular where clause
    private function update_delete($ids = false, $ids2 = false) {
        if ($ids !== false && !empty($ids) && is_numeric($this->input->post('id'))) {
            $this->db->where_not_in('id', $ids);
            if ($ids2 !== false && !empty($ids2)) {
                $this->db->where_not_in('id', $ids2);
            }
            $this->db->where('journal_transaction_id', $this->input->post('id'));
            return $this->db->delete('journal_transaction_line');
        }
        return true;
    }

    //deletes entries given a particular where clause
    public function delete() {
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        if ($this->input->post('group_id') !== NULL && is_numeric($this->input->post('group_id'))) {
            $this->db->where('group_id', $this->input->post('group_id'));
        }
        return $this->db->delete('journal_transaction_line');
    }

    public function change_status() {
        $data = array(
            'status_id' => 0
        );
        if ($this->input->post('id') !== NULL && is_numeric($this->input->post('id'))) {
            $this->db->where('id', $this->input->post('id'));
        }
        if ($this->input->post('group_id') !== NULL && is_numeric($this->input->post('group_id'))) {
            $this->db->where('group_id', $this->input->post('group_id'));
        }

        return $this->db->update('journal_transaction_line', $data);
    }

    public function update_status($data,$where_clause) {
        $this->db->where($where_clause);
        return $this->db->update('journal_transaction_line', $data);
    }
    public function update_status_topup($data,$ref_no,$array,$date) {
        $this->db->where('reference_no',$ref_no);
        $this->db->where('reference_id',$array);
        $this->db->where('transaction_date',$date);
        return $this->db->update('journal_transaction_line', $data);
    }

    public function fix_interest()
    {
        $today = date('Y-m-d');
        $this->db->select("*");
        $this->db->from("fms_journal_transaction_line");
        $this->db->where("status_id", 1);
        $this->db->where("account_id IN(7,6)");
        $this->db->where("transaction_date > '{$today}'");
        $this->db->limit(500);
        $query = $this->db->get();
        $lines = $query->result_array();
        if(empty($lines)) return [];
        $data = [];

        foreach ($lines as $key => $line) {            
            $loan_active_date = $this->get_loan_disbursement_date_using_journal_transaction_id($line["journal_transaction_id"]);

            $this->db->trans_start();
            $this->db->where('id', $line['id']);
            $this->db->update("fms_journal_transaction_line", [
                "transaction_date" => $loan_active_date
            ]);
            $this->db->trans_complete();
            array_push($data, $line);
        }
        return $data;
    }

    public function get_loan_disbursement_date_using_journal_transaction_id($id)
    {
        $this->db->select('transaction_date');
        $this->db->from("fms_journal_transaction");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $journal = $query->row_array();
        return $journal["transaction_date"];

    }
}
