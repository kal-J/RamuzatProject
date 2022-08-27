<?php

class Org_modules_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        //$this->db->from('fms_org_modules om');
        if ($filter === FALSE) {
         /*  $this->db->select('m.module_name,om.module_id,om.id,om.status_id as status,m.description');
            $this->db->join('modules m', 'om.module_id=m.id', 'left');
            $this->db->where("organisation_id",$filter);
            $query = $this->db->get();
            return $query->result_array();  */
        } else {
            if (is_numeric($filter)) {
                $query = $this->db->query("SELECT fms_org_modules.id, module_id,module_name,fms_org_modules.status_id as module_status, 1 as yesno from fms_org_modules  JOIN fms_modules ON module_id=fms_modules.id WHERE `organisation_id`=$filter AND fms_org_modules.status_id=0 UNION SELECT null as id, id as module_id,module_name,null as module_status, 0 as yesno from fms_modules WHERE `id` NOT IN (SELECT module_id FROM fms_org_modules WHERE `organisation_id`=$filter) AND fms_modules.status_id=1");
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
    
    public function set() {
        $organisation_id = $this->input->post('organisation_id');
        $modules_lists = $this->input->post('modules_list');
        $data = [];
        foreach ($modules_lists as $key => $modules_list) {
            if ($modules_list['id'] == null && isset($modules_list['module_id']) && is_numeric($modules_list['module_id'])) { //new entry
                $data[] = array(
                    'organisation_id' => $organisation_id,
                    'module_id' => $modules_list['module_id']
                );
            }
            if (!isset($modules_list['module_id']) && is_numeric($modules_list['id'])) { //unchecked the privilege
                $this->db->where('id', $modules_list['id']);
                $query = $this->db->delete('org_modules');
            }
        }
        if (!empty($data)) {
            return $this->db->insert_batch('org_modules', $data);
        }
        return true;
    } 

    public function set_module_settings($organisation_id) {
        $this->set_defaults(['module_id' => 1, 'organisation_id' => $organisation_id, 'status_id' => "1"]);
        $this->set_defaults(['module_id' => 2, 'organisation_id' => $organisation_id, 'status_id' => "1"]);
        $this->set_defaults(['module_id' => 3, 'organisation_id' => $organisation_id, 'status_id' => "1"]);
        $this->set_defaults(['module_id' => 4, 'organisation_id' => $organisation_id, 'status_id' => "1"]);
        $this->set_defaults(['module_id' => 7, 'organisation_id' => $organisation_id, 'status_id' => "1"]);
        $this->set_defaults(['module_id' => 11, 'organisation_id' => $organisation_id, 'status_id' => "1"]);
        $this->set_defaults(['module_id' => 16, 'organisation_id' => $organisation_id, 'status_id' => "1"]);
        $this->set_defaults(['module_id' => 18, 'organisation_id' => $organisation_id, 'status_id' => "1"]);
        $this->set_defaults(['module_id' => 19, 'organisation_id' => $organisation_id, 'status_id' => "1"]);
        $this->set_defaults(['module_id' => 20, 'organisation_id' => $organisation_id, 'status_id' => "1"]);
    }

    public function set_defaults($data = []){
        $this->db->insert('fms_org_modules', $data);
    }
}
?>