<?php
/**
 * Description of non_working_days_model
 *
 * @author Eric
 */
class Non_working_days_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select('*');
        $this->db->from('non_working_days');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)){
                $this->db->where('non_working_days.id',$filter);
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
        $data = array(
            'monday' => ($this->input->post('monday')=='on')?'1':'0',
            'tuesday' => ($this->input->post('tuesday')=='on')?'1':'0',
            'wednesday' => ($this->input->post('wednesday')=='on')?'1':'0',
            'thursday' => ($this->input->post('thursday')=='on')?'1':'0',
            'friday' => ($this->input->post('friday')=='on')?'1':'0',
            'saturday' => ($this->input->post('saturday')=='on')?'1':'0', 
            'sunday' => ($this->input->post('sunday')=='on')?'1':'0'
        );
        $data['date_created']=time();
        $data['created_by']=$_SESSION['id'];
        $this->db->insert('non_working_days', $data);
        return $this->db->insert_id();
    }
	
    public function update() {
        $data = array(
            'monday' => ($this->input->post('monday')=='on')?'1':'0',
            'tuesday' => ($this->input->post('tuesday')=='on')?'1':'0',
            'wednesday' => ($this->input->post('wednesday')=='on')?'1':'0',
            'thursday' => ($this->input->post('thursday')=='on')?'1':'0',
            'friday' => ($this->input->post('friday')=='on')?'1':'0',
            'saturday' => ($this->input->post('saturday')=='on')?'1':'0', 
            'sunday' => ($this->input->post('sunday')=='on')?'1':'0'
        );
        $data['modified_by']=$_SESSION['id'];
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('non_working_days', $data);
    }
}
