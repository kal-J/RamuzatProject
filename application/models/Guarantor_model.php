<?php

class Guarantor_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();

        $this->max_loan_state_id = "(SELECT client_loan_id,state_id,comment,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state GROUP BY client_loan_id ) )";


        $this->single_contact = "
                (SELECT `user_id`, `mobile_number` FROM `fms_contact`
                WHERE `id` in (
                    SELECT MAX(`id`) from `fms_contact` WHERE `contact_type_id`=1 GROUP BY `user_id` 
                )
            ) as c";
    }

    var $table = 'guarantor';

    public function add($loan_id = false)
    { //For a set by step form
        $query = false;
        if ($loan_id !== false) {
            $client_loan_id = $loan_id;
        } else {
            $client_loan_id = $this->input->post('client_loan_id');
        }
        $guarantor_type = $this->input->post('guarantor_type');
        if (is_numeric($guarantor_type) && $guarantor_type == 1) {
            $guarantors = $this->input->post('member_guarantors');

            foreach ($guarantors as $key => $value) { //it is a new entry, so we insert afresh
                $value['date_created'] = time();
                $value['client_loan_id'] = $client_loan_id;
                $value['status_id'] = 1;
                $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
                $value['guarantor_type_id'] = 1;
                $query = $this->db->insert($this->table, $value);
            }
        } elseif ($guarantor_type == 2) {

            $guarantors = $this->input->post('member_guarantors2');
            $document_name = $this->data['document_name'];
            //print_r($this->input->post('file_name'));die();

            foreach ($guarantors as $key => $value) {
                $value['attachment'] = $document_name;
                $value['member_id'] = '0';
                $value['firstname'] = $value['firstname'];
                $value['lastname'] = $value['lastname'];
                $value['mobile_number'] = $value['mobile_number'];
                $value['gender'] = $value['gender'];
                $value['email'] = $value['email'];
                $value['nin'] = $value['nin'];
                $value['comment'] = $value['comment'];
                $value['relationship_type_id'] = $value['relationship_type_id'];
                $value['date_created'] = time();
                $value['client_loan_id'] = $client_loan_id;
                $value['status_id'] = 1;
                $value['guarantor_type_id'] = 2;
                $value['created_by'] = $value['modified_by'] = $_SESSION['id'];
                $query = $this->db->insert($this->table, $value);
            }
        }


        return $query;
    }

    public function get_member_guaranteed_active_loans($member_id)
    {
        $this->db->select('g.client_loan_id, g.member_id');
        $this->db->where("l.state_id IN(1,5,6,7,11,12,13,14)");
        $this->db->where('g.member_id', $member_id);
        $this->db->where('g.status_id', 1);
        $this->db->from('fms_guarantor g');
        $this->db->join($this->max_loan_state_id . ' l', 'l.client_loan_id=g.client_loan_id', 'LEFT');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get($filter = false, $gender = false)
    {
        if ($gender) {
            $_POST['gender'] = $gender;
        }

        $this->db->distinct('member_id');
        $this->db->select(
            'a.*, 
            (CASE WHEN a.member_id !=0 THEN concat(u.firstname," ", u.lastname," ", u.othernames)  else concat(a.firstname," ", a.lastname) end) AS member_name,(CASE WHEN member_id !=0 THEN u.gender else a.gender end) as gender,(CASE WHEN member_id!=0 THEN c.mobile_number ELSE a.mobile_number end) as mobile_number, relationship_type,(CASE when member_id !=0 THEN u.nid_card_no ELSE  a.nin end)as nid_card_no,
            (CASE when member_id!=0 THEN u.comment ELSE a.comment end) as comment, (CASE when member_id!=0 THEN m.id ELSE 0  end)type,m.id as m_id'
        );
        $this->db->from('guarantor a');
        $this->db->join('relationship_type r', 'r.id = a.relationship_type_id', 'left');
        $this->db->join('member m', 'a.member_id = m.id', 'left');
        $this->db->join('user u', 'u.id = m.user_id', 'left');
        $this->db->join($this->single_contact, 'c.user_id=u.id', 'left');
        if (!empty($this->input->post('client_loan_id'))) {
            $this->db->where('client_loan_id', $this->input->post('client_loan_id'));
        }
        if ($this->input->post('gender') == 0 || $this->input->post('gender') == 1) {
            $this->db->where('u.gender', $this->input->post('gender'));
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

    public function delete_by_id()
    {
        if (isset($_POST['id']) === true) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $data = array('status_id' => 0);
            return $this->db->update($this->table, $data);
        } else {
            return false;
        }
    }
}
