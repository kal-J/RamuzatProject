<?php
/**
 * Description of Fiscal_model
 *
 * @author Reagan
 */
class Fiscal_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select('id, start_date,end_date,close_status,status_id');
        $this->db->from('fiscal_year');
        $this->db->order_by("id DESC");
        if(is_numeric($this->input->post('organisation_id'))){
            $this->db->where('fiscal_year.organisation_id=' . $this->input->post('organisation_id'));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('fiscal_year.id=',$filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function current_fiscal_year() {
        $organisation_id=isset($_SESSION['organisation_id'])?$_SESSION['organisation_id']:1;
        $this->db->select('id, start_date, end_date');
        $this->db->from('fiscal_year');
        $where_clause = "organisation_id=" .$organisation_id. " AND `status_id`=1";
        $this->db->where($where_clause);
        $query = $this->db->get();
        $financial_year = $query->row_array();
        return !empty($financial_year) ? $financial_year : false;
    }

    public function set($end_date,$new_date=FALSE) {
        $data = $this->input->post(NULL, TRUE);
        if(isset($_POST['start_date'])){
            $start_date1=$this->input->post('start_date');
        }else{
            $start_date1=$new_date;
        }
        unset($data['id'],$data['hidden'],$data['check_me'], $data['tbl'],$data['btn_submit'],$data['fiscal_id'],$data['end_date']);
        $start_date = explode('-', $start_date1, 3);
        $data['start_date'] = count($start_date) === 3 ? ($start_date[2] . "-" . $start_date[1] . "-" . $start_date[0]) : null;

        $end_date = explode('-', $end_date, 3);
        $data['end_date'] = count($end_date) === 3 ? ($end_date[2] . "-" . $end_date[1] . "-" . $end_date[0]) : null;
        if(empty($this->input->post('organisation_id'))){
        $data['organisation_id'] = $_SESSION['organisation_id'];
        }
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] =$_SESSION['id'];
        $inser = $this->db->insert('fiscal_year', $data);
        $here = $this->db->insert_id();

        if ($inser === true) {
            return $here;
        } else {
            return false;
        }
    }

    public function update($end_date) {
        $data = $this->input->post(NULL, TRUE);
        unset($data['hidden'], $data['btn_submit']);
        $start_date = explode('-', $data['start_date'], 3);
        $data['start_date'] = count($start_date) === 3 ? ($start_date[2] . "-" . $start_date[1] . "-" . $start_date[0]) : null;

        $end_date = explode('-',$end_date, 3);
        $data['end_date'] = count($end_date) === 3 ? ($end_date[2] . "-" . $end_date[1] . "-" . $end_date[0]) : null;

        $data['date_modified'] = time();
        $data['modified_by'] = $_SESSION['id'];

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fiscal_year', $data);
    }

    public function update_close_status($id=false,$status=false) {
        $data = $this->input->post(NULL, TRUE);
        unset($data['hidden'], $data['organisation_id'],$data['end_date'],$data['check_me'],$data['fiscal_id'],$data['btn_submit'],$data['fisc_date_to'],$data['new_year_start_date']);
        $data['close_status'] = $status;
        $data['date_modified'] = time();
        $data['modified_by'] = $_SESSION['id'];

        $this->db->where('id', $id);
        return $this->db->update('fiscal_year', $data);
    }


    public function get_fiscal_id() {
        $this->db->select('ref_no');
        $this->db->from('fms_journal_transaction');
        $this->db->where("transaction_date >= ", $this->input->post("fisc_date_to"));
        $this->db->where("transaction_date <= ", $this->input->post("new_year_start_date"));
        $this->db->where("journal_type_id =18");
        $query = $this->db->get();
        return $query->row_array();
    }
    public function delete($fy_id=false) {
        if (empty($this->input->post('id'))) {
            $id=$fy_id;
        }else{
            $id=$this->input->post('id');
        }
        $this->db->where('id', $id);
        return $this->db->delete('fiscal_year');
    }

    public function change_status() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fiscal_year', ['status_id' => $this->input->post('status_id')]);
    }

    public function activate() {
        $this->db->where('organisation_id', $_SESSION['organisation_id']);
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fiscal_year', ['status_id' => $this->input->post('status')]);
    }

}
