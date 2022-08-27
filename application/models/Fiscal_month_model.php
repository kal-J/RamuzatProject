<?php

/**
 * Description of Fiscal_month_model
 *
 * @author Reagan
 */
class Fiscal_month_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
       
        $this->db->select('fm.id,month_id,month_name,month_start, month_end,fm.status_id');
        $this->db->from('financial_month fm');
        $this->db->join("fms_months m", "m.id=fm.month_id");
        if(is_numeric($this->input->post('organisation_id'))){
            $this->db->where('fm.organisation_id=' . $this->input->post('organisation_id'));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
            //$this->db->where('financial_month.id=',$filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_active_month() {
        $where_clause = "organisation_id=" . $_SESSION['organisation_id'] . " AND fm.status_id=1";
        $this->db->select('fm.id,month_name,month_start, month_end,fm.status_id');
        $this->db->from('financial_month fm');
        $this->db->join("fms_months m", "m.id=fm.month_id");
        $this->db->where($where_clause);
        $query = $this->db->get();
        $financial_month = $query->row_array();
        return !empty($financial_month) ? $financial_month : false;
    }

    public function get_latest_month() {
        $this->db->select('fm.id,month_name,month_start, month_end');
        $this->db->from('financial_month fm');
        $this->db->join("fms_months m", "m.id=fm.month_id");
        $where_clause = "organisation_id=" . $_SESSION['organisation_id'];
        $this->db->where($where_clause);
        $this->db->order_by('month_end DESC');
        $query = $this->db->get();
        $financial_month = $query->row_array();
        return !empty($financial_month) ? $financial_month : false;
    }

    public function check_if_month_exist() {
        $this->db->select('*');
        $this->db->from('financial_month');
        $where_clause = "organisation_id=" . $_SESSION['organisation_id'] ." AND YEAR(month_start) =".$_POST['year']." AND month_id =".$_POST['month_id'];
        $this->db->where($where_clause);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function set($dates_array,$month_id=false) {
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['hidden'],$data['tbl'],$data['year']);
        if(empty($this->input->post('organisation_id'))){
        $data['organisation_id'] = $_SESSION['organisation_id'];
        }
        if(empty($this->input->post('month_id'))){
        $data['month_id'] = $month_id;
        }
        $data['month_start'] = $dates_array['month_start'];
        $data['month_end'] = $dates_array['month_end'];
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        if(is_numeric($this->input->post('month_id'))){
        $data['status_id'] = 2;
        } else {
        $data['status_id'] = 1;
        }
        $data['modified_by'] =$_SESSION['id'];
        $inser = $this->db->insert('financial_month', $data);
        $here = $this->db->insert_id();

        if ($inser === true) {
            return $here;
        } else {
            return false;
        }
    }


    public function update_close_status($id=false,$status=false) {
        $data = $this->input->post(NULL, TRUE);
        unset($data['hidden'], $data['organisation_id'],$data['month_end'],$data['check_me'],$data['fiscal_id'],$data['btn_submit'],$data['fisc_date_to'],$data['new_year_month_start']);
        $data['close_status'] = $status;
        $data['date_modified'] = time();
        $data['modified_by'] = $_SESSION['id'];

        $this->db->where('id', $id);
        return $this->db->update('financial_month', $data);
    }

    public function delete($fy_id=false) {
        if (empty($this->input->post('id'))) {
            $id=$fy_id;
        }else{
            $id=$this->input->post('id');
        }
        $this->db->where('id', $id);
        return $this->db->delete('financial_month');
    }

    public function change_status() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('financial_month', ['status_id' => $this->input->post('status_id')]);
    }

    public function deactivate_all_first() {
        $this->db->where('organisation_id', $_SESSION['organisation_id']);
        $this->db->where('status_id', 1);
        return $this->db->update('financial_month', ['status_id' => 2]);
    }

}
