<?php

/**

 * Number Formats Model
 *  */
class Num_format_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->select("num_format.id, org_id,format_cat_id,section_format, section_seperator, section_start, section_length, status_id");
        $this->db->from("num_format");
        if ($this->input->post("format_cat") != NULL) {
            $this->db->where("format_cat_id", $this->input->post("format_cat"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } 
        else {
            if (is_numeric($filter)) {
                $this->db->where('id', $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set($org_id, $formats = []) {

        $ids = $ids2 = [];
        if (is_array($formats) && empty($formats)) {
            $formats = $this->input->post('formats');
        }
        foreach ($formats as $key => $format) {
            if (isset($format['format_cat_id']) && is_numeric($format['section_format'])) {
                $format['modified_by'] = (isset($_SESSION['id']))?$_SESSION['id']:1;
                //its an update operation
                if (isset($format['id']) && $format['id'] !== '' && is_numeric($format['id'])) {
                    $ids[] = $format['id'];
                    $this->db->where('id', $format['id']);
                    unset($format['id']);
                    $this->db->update('num_format', $format);
                } else {
                    //it is a new entry, so we insert afresh
                    $format['org_id'] = $org_id;
                    unset($format['id']);
                    $format['created_by'] = $format['modified_by'];
                    $format['date_created'] = time();
                    $this->db->insert('num_format', $format);
                    $ids2[] = $this->db->insert_id();
                }
            }
        }
        return $this->update_delete($org_id, $ids, $ids2);
    }

    //deletes entries given a particular where clause
    private function update_delete($org_id, $ids = false, $ids2 = false) {
        if ($ids !== false && !empty($ids) && is_numeric($this->input->post('id'))) {
            $this->db->where_not_in('id', $ids);
            if ($ids2 !== false && !empty($ids2)) {
                $this->db->where_not_in('id', $ids2);
            }
            $this->db->where('org_id', $org_id);
            return $this->db->delete('num_format');
        }
        return true;
    }

}
