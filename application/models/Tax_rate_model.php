<?php
class Tax_rate_model extends CI_Model {

    Public function __construct()
    {
      parent :: __construct();
    }

    public function get($filter = FALSE) {
        $query = $this->db->from('tax_rate');
        if ($this->input->post('tax_rate_source_id') !== NULL) {
             $this->db->where("tax_rate_source_id = " . $this->input->post('tax_rate_source_id'));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('tax_rate.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set() {
        $data = $this->input->post(NULL, TRUE);
        //Start date
        $data['start_date'] = $this->helpers->yr_transformer($data['start_date']);
        $data['created_by'] =$_SESSION['id'];

        if(isset($data['id'])&& is_numeric($data['id'])){
            $this->db->where('id', $data['id']);
            unset($data['id'], $data['tbl'], $data['tbl']);
            return $this->db->update('tax_rate', $data);
        }else{
            unset($data['id'], $data['tbl'], $data['tbl']);
            $data['date_created'] = time();
            //modfied_by throwing a post error
            $data['created_by'] = $data['created_by'];
            $this->db->insert('tax_rate', $data);
            return $this->db->insert_id();
        }
    }
    public function deactivate($id) {
        //Start date
        $start_date = explode('-', $this->input->post('start_date'), 3);
        //End date
        $data['end_date'] = ($start_date[2] . "-" . $start_date[1] . "-" . $start_date[0]);
        $data['modified_by'] = $_SESSION['id'];

        $this->db->where('id', $id);
        $this->db->update('tax_rate', $data);
        return $this->db->insert_id();
    }
    public function delete() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->delete('tax_rate');
    }

}
