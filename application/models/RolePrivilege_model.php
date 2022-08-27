<?php

class RolePrivilege_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($filter = FALSE) {
        $this->db->from('module_privilege md');
        if ($filter === FALSE) {
            $this->db->select('m.module_name,md.module_id,md.id,md.status_id as status,p.privilege,p.description');
            $this->db->join('modules m', 'md.module_id=m.id', 'left');
            $this->db->join('privilege p', 'md.privilege_code=p.id', 'left');
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $query = $this->db->query("SELECT fms_role_privilege.id, privilege_id, description, 1 as yesno, module_id from fms_role_privilege  JOIN fms_module_privilege ON privilege_id=fms_module_privilege.id WHERE `role_id`=$filter UNION SELECT null as id, id as privilege_id, description, 0 as yesno, module_id from fms_module_privilege WHERE `id` NOT IN (SELECT privilege_id FROM fms_role_privilege WHERE `role_id`=$filter) AND status_id=1");
                return $query->result_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set() {
        $role_id = $this->input->post('role_id');
        $role_privileges = $this->input->post('role_privilege');
        $data = [];
        foreach ($role_privileges as $key => $role_privilege) {
            if ($role_privilege['id'] == null && isset($role_privilege['privilege_id']) && is_numeric($role_privilege['privilege_id'])) { //new entry
                $data[] = array(
                    'role_id' => $role_id,
                    'privilege_id' => $role_privilege['privilege_id'],
                    'date_created' => time(),
                    'created_by' => $_SESSION['id'],
                    'modified_by' => $_SESSION['id']
                );
            }
            if (!isset($role_privilege['privilege_id']) && is_numeric($role_privilege['id'])) { //unchecked the privilege
                $this->db->where('id', $role_privilege['id']);
                $query = $this->db->delete('role_privilege');
            }
        }
        if (!empty($data)) {
            return $this->db->insert_batch('role_privilege', $data);
        }
        return true;
    }

    public function get_user_privileges($module_id, $staff_id) {
        $data['roles'] = $this->get_user_roles($staff_id);
        if (empty($data['roles'])) {
            return false;
        } else {
            foreach ($data['roles'] as $key => $role) {
                $roles_list[] = ($role['role_id']);
            }
            $this->db->select("DISTINCT mp.privilege_code", false);
            $this->db->from("role_privilege rp");
            $this->db->join("module_privilege mp", "rp.privilege_id=mp.id", "left");
            $this->db->where_in("rp.role_id", $roles_list);
            $this->db->where("mp.module_id", $module_id);
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function get_user_modules($staff_id) {
        $data['roles'] = $this->get_user_roles($staff_id);
        if (empty($data['roles'])) {
            return [];
        } else {
            foreach ($data['roles'] as $key => $role) {
                $roles_list[] = ($role['role_id']);
            }
            
            $this->db->select('om.module_id');
            $this->db->from('org_modules om');
            $this->db->where('om.organisation_id', $_SESSION['organisation_id']);
            $sub_query = $this->db->get_compiled_select();
            $this->db->select('DISTINCT mp.module_id', false);
            $this->db->from('role_privilege rp');
            $this->db->join('module_privilege mp', 'rp.privilege_id=mp.id', 'left');
            $this->db->where_in('rp.role_id', $roles_list);
            $this->db->where("mp.module_id IN ($sub_query)");
            $query = $this->db->get();
            
            return $query->result_array();
        }
    }

    public function get_user_roles($staff_id) {
        $this->db->from('user_role');
        $this->db->select("role_id");
        $this->db->where('staff_id', $staff_id);
        $this->db->where('user_role.status_id', 1);
        $query = $this->db->get();
        
        return $query->result_array();
    }

}
