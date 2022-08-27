<?php

class Loan_collateral_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->max_state_id = "(SELECT client_loan_id,state_id,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state GROUP BY client_loan_id ) )";
    }

    var $table = 'loan_collateral';

    public function add($insert_data) {
        $insert_data['status_id'] = 1;
        $insert_data['date_created'] = time();
        $insert_data['created_by'] = $_SESSION['id'];
        $this->db->insert($this->table, $insert_data);
        return $this->db->insert_id();
    }

    public function update_document() {
        $insert_data['member_collateral_id'] = $this->input->post('member_collateral_id');
        $update_data['client_loan_id'] = $this->input->post('client_loan_id');
        //$update_data['collateral_type_id'] = $this->input->post('collateral_type_id');
        //$update_data['description'] = $this->input->post('description');
        $update_data['item_value'] = $this->input->post('item_value');
        //$update_data['file_name'] = $file_name;
        $update_data['status_id'] = 1;
        $update_data['date_created'] = time();
        $update_data['created_by'] = $_SESSION['id'];
        $this->db->where('id=', $this->input->post('id'));
        return $this->db->update($this->table, $update_data);
    }

    public function set($member_collaterals = []) {
        $query = false;

        foreach ($member_collaterals as $value) { //it is a new entry, so we insert afresh
            $value['date_created'] = time();
            $value['status_id'] = 1;
            $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
            $query = $this->db->insert('loan_collateral', $value);
        }
        return $query;
    }

    public function duplicate_entry($new_loan_id) {
        $sql_query = "INSERT INTO fms_loan_collateral(client_loan_id, item_value, status_id,date_created,created_by,modified_by) SELECT " . $new_loan_id . ", item_value,  status_id, UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP('1970-01-01 03:00:00')," . $_SESSION['id'] . "," . $_SESSION['id'] . " FROM fms_loan_collateral WHERE status_id=1 AND client_loan_id =" . $this->input->post('linked_loan_id');
        $query = $this->db->query($sql_query);
        return $this->db->insert_id();
        // print_r($this->db->last_query()); die;
    }
    public function get($filter = false) {
        if (!empty($this->input->post('client_loan_id'))) {
            $this->db->select('a.*, collateral_type_name, collateral_type_id, mc.description, file_name');
            $this->db->from('loan_collateral a');
            $this->db->join('member_collateral mc', 'a.member_collateral_id = mc.id', 'left');
            $this->db->join('collateral_type b', 'mc.collateral_type_id = b.id', 'left');
        } else {

            $this->db->select('a.*, collateral_type_name, concat(firstname," ", lastname," ", othernames) AS member_name, loan_no, ls.state_id AS loan_state, collateral_type_id, mc.description, file_name, l.id AS loan_id');
            $this->db->from('loan_collateral a');
            $this->db->join('member_collateral mc', 'a.member_collateral_id = mc.id', 'left');
            $this->db->join('member m', 'mc.member_id = m.id', 'left');
            $this->db->join('user u', 'u.id = m.user_id', 'left');
            $this->db->join('client_loan l', 'l.id = a.client_loan_id', 'left');
            $this->db->join("$this->max_state_id ls", 'ls.client_loan_id=a.client_loan_id', 'left');
            $this->db->join('collateral_type b', 'mc.collateral_type_id = b.id', 'left');

            $this->db->where('m.id != "null"');
            $this->db->where('ls.state_id IN(1,5,6,7,12,13,14,17)');
        }

        if (!empty($this->input->post('client_loan_id'))) {
            $this->db->where('client_loan_id', $this->input->post('client_loan_id'));
        }

        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('a.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function update_loan_doc_type() {

        $data = array(
            'loan_doc_type_id' => $this->input->post('loan_doc_type_id'),
            'description' => $this->input->post('description'),
            'modified_by' => $_SESSION['id']
        );

        $id = $this->input->post('id');
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        $this->db->affected_rows();
        return true;
    }

    public function get_collateral_type($filter = FALSE) {
        $response = array();
        $this->db->where('status_id', 1);
        $this->db->select('*');
        $q = $this->db->get('collateral_type');
        $response = $q->result_array();
        return $response;
    }

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

    public function delete_by_id() {
        if (isset($_POST['id']) === true) {
            $id = $this->input->post('id');
            $data = array('status_id' => 0);
            $this->db->where('id', $id);

            return $this->db->update($this->table, $data);
        } else {
            return false;
        }
    }

    public function sum_loan_collateral($filter = FALSE) {
        $this->db->select('SUM(item_value) AS loan_collateral_value');
        $query = $this->db->from('loan_collateral')->limit(1, 0);
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('loan_collateral.client_loan_id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
}
