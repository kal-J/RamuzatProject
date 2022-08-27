<?php

class Transaction_date_control_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->table = 'fms_transaction_date_control';
    }

    public function get($filter)
    {
        $this->db->select("c.*, concat(s.staff_no,' | ',u.lastname,' ',u.firstname,' ', u.othernames) staff_name");
        $this->db->from("$this->table c");
        $this->db->join('staff s', 's.id=c.staff_id', 'left');
        $this->db->join('user u', 'u.id=s.user_id', 'left');

        if(is_numeric($filter)) {
            $this->db->where('c.id', $filter);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            if($filter) $this->db->where($filter);
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function get_current_fiscal_year($org_id, $status_id) {
        if ($status_id === FALSE) {
            $this->db->where("fiscal_year.organisation_id=", $org_id);
            $query = $this->db->get("fiscal_year");
            return $query->result_array();
        } else {
            $this->db->where("fiscal_year.organisation_id=", $org_id);
            $this->db->where("fiscal_year.status_id=", $status_id);
            $query = $this->db->get("fiscal_year");
            return $query->row_array();
        }
    }

    public function generate_allowed_dates()
    {
        $staff_id = $_SESSION['staff_id'];
        $staff_date_control = $this->get("c.staff_id=$staff_id");
        $transaction_date_control = null;
        if(isset($staff_date_control[0])) {
            $transaction_date_control = $staff_date_control[0];
        }
        if(isset($transaction_date_control['id'])) {
            $past_days = $transaction_date_control['past_days'];
            $future_days = $transaction_date_control['future_days'];
            $start_date = date('Y-m-d', strtotime(date('Y-m-d') . " -" . $past_days . " days"));
            $end_date = date('Y-m-d', strtotime(date('Y-m-d') . " +" . $future_days . " days"));
            $allowed_dates = [
                date('d-m-Y', strtotime($start_date)), 
                date('d-m-Y', strtotime($end_date))
            ];

            while(strtotime($start_date) < strtotime($end_date)) {
                $allowed_date = date('d-m-Y', strtotime($start_date . " + 1 day"));
                $allowed_dates[] = $allowed_date;
                $start_date = $allowed_date;
            }

            return $allowed_dates;    
        }

        return [];
    }

    public function set()
    {
        $data = $this->input->post();
        unset($data['id']);

        $data['branch_id'] = isset($data['branch_id']) ? $data['branch_id'] : $_SESSION['branch_id'];
        $data['organisation_id'] = isset($data['branch_id']) ? $data['branch_id'] : $_SESSION['branch_id'];
        $data['status_id'] = 1;

        $insert_id = $this->db->insert($this->table, $data);

        return $insert_id;
    }

    public function update()
    {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['tbl']);
        $data['date_modified'] = time();
        $data['modified_by'] = $_SESSION['id'];

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update($this->table, $data);
        } else {
            return false;
        }
    }

    public function delete() {
        $data = array(
            'status_id' => 0,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update($this->table, $data);
    }

    public function deactivate() {
        $data = array(
            'status_id' => 2,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update($this->table, $data);
    }

}
