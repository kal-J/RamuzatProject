<?php

class Example_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->from('project');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('project.id=' . $filter);
                $query = $this->db->get();
                //echo $this->db->last_query();
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
        $birth_date = explode('-', $data['birth_date'],3);
         $data['birth_date'] = count($birth_date)===3?($birth_date[2] . "-" . $birth_date[1] . "-" . $birth_date[0]):null;
        $data['created_by'] = isset($_SESSION)?$_SESSION['staff_id']:1;
        $data['modified_by'] = isset($_SESSION)?$_SESSION['staff_id']:1;

        $this->db->insert('project', $data);
        return $this->db->insert_id();
    }

    public function update() {
        $data = $this->input->post(NULL, TRUE);
        $data['modified_by'] = $_SESSION['user_id'];
        /*$data = array(
            'project_title' => strtoupper($this->input->post('project_title')),
            'project_reference' => strtoupper($this->input->post('project_reference')),
            'description' => $this->input->post('description'),
            'modified_by' => $_SESSION['user_id']
        );*/

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('project', $data);
    }

    public function delete() {
        $this->db->where('id', $this->input->post('id'));
        return $this->db->delete('project');
    }

}
