<?php

class Member_collateral_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->max_state_id = "(SELECT client_loan_id,state_id,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state GROUP BY client_loan_id ) )";
        $this->max_loan_collateral_id = "(SELECT client_loan_id,member_collateral_id FROM fms_loan_collateral
                WHERE status_id=1 AND id in ( SELECT MAX(id) from fms_loan_collateral GROUP BY member_collateral_id ) )";
    }

    var $table = 'member_collateral';

    public function add($file_name = false, $insert_data) {
        if ($file_name) {
            $insert_data['file_name'] = $file_name;
        }

        if (!isset($insert_data['file_name'])) {
            $insert_data['file_name'] = "";
        }
        $insert_data['status_id'] = 1;
        $insert_data['date_created'] = time();
        $insert_data['created_by'] = $_SESSION['id'];

        $this->db->insert($this->table, $insert_data);
        return $this->db->insert_id();
    }

    public function update_document($update_data) {
        $id = $this->input->post('id');
        //get the document by id
        $fectch_link['document_to_del'] = $this->get($id);
        $path = "./uploads/organisation_" . $_SESSION['organisation_id'] . "/loan_docs/collateral/" . $fectch_link['document_to_del']['file_name'];

        if (!empty($fectch_link['document_to_del']['file_name'])) {
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $update_data['modified_by'] = $_SESSION['id'];
        $this->db->where('id=', $this->input->post('id'));
        return $this->db->update($this->table, $update_data);
    }

    public function set($member_id = false, $files) {
        $query = false;
        if ($member_id === false) {
            $member_id = $this->input->post('member_id');
        }
        $collaterals = $this->input->post('collaterals');
        foreach ($collaterals as $key => $value) { //it is a new entry, so we insert afresh
            $value['date_created'] = time();
            $value['member_id'] = $member_id;
            $value['status_id'] = 1;
            if (count($files)) {
                $value['file_name'] = $files[$key];
            }
            $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
            $query = $this->db->insert('member_collateral', $value);
        }
        return $query;
    }

    public function check_if_has_attached_active_loan($id) {
        $this->db->where('a.id=' . $id);
        $this->db->select('a.*');
        $this->db->from('member_collateral a');
        $this->db->join('loan_collateral l', 'l.member_collateral_id = a.id', 'left');
        $this->db->join($this->max_state_id . ' ls', 'ls.client_loan_id = l.client_loan_id', 'left');

        $this->db->where('l.status_id=1 AND ls.state_id IN(1,5,6,7,12)');

        $query = $this->db->get();
        return !empty($query->row_array());
    }

    public function get_not_attached_to_active_loan($filter = false) {
        if($filter !== false) {
            if(is_numeric($filter)) {
                $filter = 'id='.$filter;
            }
        } else {
            $filter = '';
        }
        
        $query = $this->db->query('SELECT mc.*, (SELECT collateral_type_name FROM fms_collateral_type b WHERE mc.collateral_type_id=b.id) collateral_type_name, (SELECT client_no FROM fms_member m WHERE mc.member_id = m.id) client_no, (SELECT user_id FROM fms_member m WHERE mc.member_id = m.id) user_id, (SELECT concat(firstname," ", lastname," ", othernames) FROM fms_user u WHERE u.id=user_id) member_name FROM fms_member_collateral mc WHERE '. $filter .' AND id NOT IN(SELECT member_collateral_id as id FROM (SELECT * FROM fms_loan_collateral WHERE status_id NOT IN(0,2)) lc LEFT JOIN fms_client_loan a ON lc.client_loan_id=a.id LEFT JOIN (SELECT client_loan_id,state_id FROM fms_loan_state WHERE id in ( SELECT MAX(id) from fms_loan_state GROUP BY client_loan_id ) ) loan_state ON loan_state.client_loan_id =a.id WHERE loan_state.state_id IN(1,5,6,7,12))');
        
        return $query->result_array();
        
    }

    public function get($filter = false) {
        $this->db->select('a.*,collateral_type_name, concat(firstname," ", lastname," ", othernames) AS member_name, m.client_no AS client_no, ls.state_id AS loan_state, loan_no');
        $this->db->from('member_collateral a');
        $this->db->join('collateral_type b', 'a.collateral_type_id = b.id', 'left');
        $this->db->join('member m', 'a.member_id = m.id', 'left');
        $this->db->join('user u', 'u.id = m.user_id', 'left');
        $this->db->join($this->max_loan_collateral_id . ' l', 'l.member_collateral_id = a.id', 'left');
        $this->db->join($this->max_state_id . ' ls', 'ls.client_loan_id = l.client_loan_id', 'left');
        $this->db->join('client_loan cl', 'cl.id = l.client_loan_id', 'left');
        
        if (!empty($this->input->post('member_id'))) {
            $this->db->where('member_id', $this->input->post('member_id'));
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
            $data = array('status_id' => $this->input->post('status_id'));
            $this->db->where('id', $id);
            $query = $this->db->update($this->table, $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $data = array('status_id' => $this->input->post('status_id'));
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
            
            $data = array(
                'status_id' => 0
        );

            $this->db->where('id', $id);

            return $this->db->update($this->table, $data);
        } else {
            return false;
        }
    }
}
