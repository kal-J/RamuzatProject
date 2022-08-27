<?php
/**
 * Description of holiday_model
 *
 * @author Eric
 */
class Holiday_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select('holiday.*,date_format(holiday_date,"%m-%d")AS holiday,month,day,every');
        $this->db->from('holiday')->join('holiday_frequency_every','holiday_frequency_every.id=holiday.frequency_every_id')->join('holiday_frequency_day','holiday_frequency_day.id=holiday.frequency_day_id')->join('holiday_frequency_of','holiday_frequency_of.id=holiday.frequency_of_id');
        $this->db->order_by("holiday_date", "ASC");
        if(is_numeric($this->input->post("organisation_id"))){
            $this->db->where("holiday.organisation_id=" .$this->input->post("organisation_id"));
        }else{
            $organisation_id=isset($_SESSION['organisation_id'])?$_SESSION['organisation_id']:1;
            $this->db->where("holiday.organisation_id=" .$organisation_id);
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)){
                $this->db->where('holiday.id',$filter);
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
        unset($data['id'],$data['tbl']);
        //holiday_date conversation
        $holiday_date = explode('-', $data['holiday_date'], 3);
        $data['holiday_date'] = count($holiday_date) === 3 ? ($holiday_date[2] . "-" . $holiday_date[1] . "-" . $holiday_date[0]) : null;
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['organisation_id'] = $_SESSION['organisation_id'];
        $this->db->insert('holiday', $data);
        return $this->db->insert_id();
    }
	
    public function update() {
        $data = $this->input->post(NULL, TRUE);
        //holiday_date conversation
        $holiday_date = explode('-', $data['holiday_date'], 3);
        $data['holiday_date'] = count($holiday_date) === 3 ? ($holiday_date[2] . "-" . $holiday_date[1] . "-" . $holiday_date[0]) : null;
        $data['modified_by'] = $_SESSION['id'];
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('holiday', $data);
    }
    
    /**
     * This method Deactivates holiday data from the database
     */
    public function delete_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->delete('holiday');
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete('holiday');
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }
}
