<?php
class Optimised_model extends CI_Model {
    private $columns = ["jtr.id", "jtr.ref_no", "jtr.ref_id", "transaction_date", "jty.type_name", "jtr.description","jtr.date_created", "jtr.status_id", "jtr.reverse_msg", "jtr.reversed_date"];
    private $alias_only_pattern = '/(\s+(as[\s]+)?)((`)?[a-zA-Z0-9_]+(`)?)$/';

    public function __construct() {

        date_default_timezone_set("Africa/Kampala");
        $this->load->database();
    }

    public function get2($filter = FALSE) {
        $tt_amount_subquery = "(SELECT `journal_transaction_id` `jt_id`, SUM(`debit_amount`) `tt_amount`, SUM(`credit_amount`) `c_amount` FROM `fms_journal_transaction_line` GROUP BY `journal_transaction_id`) tt_as";

        $this->db->select("jtr.id, journal_type_id, transaction_date, ref_no, ref_id, description,jtr.status_id,reverse_msg,reversed_date");
        $this->db->select("jty.type_name,tt_as.tt_amount,tt_as.c_amount");
        $this->db->select("IF(tt_amount=0, IFNULL(tt_as.tt_amount,tt_as.c_amount), tt_as.c_amount) t_amount", FALSE);

        $this->db->from("journal_transaction jtr");
        $this->db->join("journal_type jty", "jtr.journal_type_id=jty.id");
        $this->db->join("$tt_amount_subquery", "jt_id=jtr.id");
        $this->db->where("jtr.status_id IN(1,3)");

        if ($this->input->post("id") != NULL) {
            $this->db->where("id", $this->input->post("id"));
        }

        if ($this->input->post("start_date") != NULL && $this->input->post("end_date") != NULL) {
            $start_date = $this->input->post("start_date");
            $end_date = $this->input->post("end_date");
            $where_clause = "(transaction_date BETWEEN '$start_date' AND '$end_date')";
            $this->db->where($where_clause);
        }

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('jtr.id', $filter);
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
        $this->db->from("client_loan c");
        $this->db->where("jtr.status_id =".$this->input->post('status_id'));
        if ($this->input->post("id") != NULL) {
            $this->db->where("jtr.id", $this->input->post("id"));
        }
        if ($this->input->post("start_date") != NULL && $this->input->post("end_date") != NULL) {
            $start_date = $this->input->post("start_date");
            $end_date = $this->input->post("end_date");
            $where_clause = "(transaction_date BETWEEN '$start_date' AND '$end_date')";
            $this->db->where($where_clause);
        }
    }

    public function get($filter = FALSE) {
        $this->db->select('count(jtr.id) no_rows');
        $this->get_select();
        $query = $this->db->get();
        $result = $query->row_array();
        return isset($result['no_rows'])?$result['no_rows']:0;
    }


    public function get_dTable() {
        $all_columns = $this->input->post('columns');
        $tt_amount_subquery = "(SELECT `journal_transaction_id` `jt_id`, SUM(`debit_amount`) `tt_amount`, SUM(`credit_amount`) `c_amount` FROM `fms_journal_transaction_line` GROUP BY `journal_transaction_id`) tt_as";

        $this->db->select("SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $this->columns)), FALSE);
        $this->db->select("jtr.id, journal_type_id, transaction_date, ref_no, ref_id, description");
        $this->db->select("jty.type_name,tt_as.tt_amount,tt_as.c_amount");
        $this->db->select("IF(tt_amount=0, IFNULL(tt_as.tt_amount,tt_as.c_amount), tt_as.c_amount) t_amount", FALSE);
        $this->get_select();
        $this->db->join("journal_type jty", "jtr.journal_type_id = jty.id");
        $this->db->join("$tt_amount_subquery", "jt_id=jtr.id");
        $this->set_filters($all_columns);
        $this->db->order_by("jtr.id DESC");


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


    public function set($data = []) {

        if (is_array($data) && empty($data)) {
            $data = $this->input->post(NULL, TRUE);
        }
        //Transaction Date
        $data['transaction_date'] = isset($data['modified_by']) ? $data['transaction_date'] : $this->helpers->yr_transformer($data['transaction_date']);
        $data['modified_by'] = isset($data['modified_by']) ? $data['modified_by'] : (isset($_SESSION['id']) ? $_SESSION['id'] : 1);

        if (isset($data['id']) && is_numeric($data['id']) && !isset($data['date_created'])) { //when updating
            $this->db->where('id', $data['id']);
            unset($data['id'], $data['tbl'], $data['journal_transaction_line']);
            return $this->db->update('journal_transaction', $data);
        } else { //when saving for the first time
            if (!isset($data['date_created'])) {
                $data['date_created'] = time();
                $data['created_by'] = $data['modified_by'];
                unset($data['id'], $data['tbl'], $data['journal_transaction_line']);
            }
            $this->db->insert('journal_transaction', $data);
            return $this->db->insert_id();
        }
    }
    public function set_auto($data){
        return $this->db->insert_batch('journal_transaction', $data);
    }

    public function get_op_bal_trans($account_details) {
        $this->db->select("jtr.id, transaction_date");
        $this->db->from("journal_transaction jtr");
        $this->db->where($account_details['id'] . " IN (SELECT account_id FROM fms_journal_transaction_line)");
        $this->db->where("jtr.journal_type_id=", 18);
        $this->db->where("jtr.transaction_date", $account_details['opening_balance_date']);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function set_open_balance($new_posting = FALSE) {
        $data['transaction_date'] = $this->helpers->yr_transformer($this->input->post('opening_balance_date'));
        $data['description'] = "Opening Balance";
        $data['journal_type_id'] = 18;
        $data['date_created'] = time();
        $data['modified_by'] = $_SESSION['id'];

        if (is_numeric($this->input->post('id')) && $new_posting === FALSE) { //when updating
            $this->db->where('id', $this->input->post('id'));
            return $this->db->update('journal_transaction', $data);
        } else { //when saving for the first time
            $data['created_by'] = $data['modified_by'];
            $this->db->insert('journal_transaction', $data);
            return $this->db->insert_id();
        }
    }

    //deletes members given a particular where clause
    public function delete() {
        if ($this->delete_jtrl()) {
            $this->db->where('id', $this->input->post('id'));
            return $this->db->update('journal_transaction', ['status_id' => 0]);
        } else {
            return false;
        }
    }

    public function delete_jtrl() {
        $this->db->where('journal_transaction_id', $this->input->post('id'));
        return $this->db->update('journal_transaction_line', ['status_id' => 0]);
    }

    public function reverse($ref_id=false,$ref_no=false,$journal_type_id=false,$id=false) {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['journal_type_id'],$data['transaction_no'],$data['tbl'],$data['payment_id'],$data['transaction_type_id']);
        $data['reversed_by'] = $_SESSION['id'];
        $data['reversed_date'] = date("Y-m-d H:i:s");
        $data['reverse_msg'] = $this->input->post('reverse_msg');
        $data['status_id'] = 3;
        if ($ref_no!=false) {
            $this->db->where('ref_no', $ref_no);
        }
        if ($ref_id!=false) {
            $this->db->where('ref_id', $ref_id);
        }
        if ($journal_type_id!=false) {
            $this->db->where('journal_type_id IN'.$journal_type_id);
            return $this->db->update('journal_transaction', $data);
        } else {
            return false;
        }
    }
     public function reverse_main($data,$id) {
        $this->db->where('id='.$id);
        return $this->db->update('journal_transaction', $data);
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

        return $this->db->update('journal_transaction', $data);
    }

}
