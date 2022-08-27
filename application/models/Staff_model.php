<?php

/**
 * This class helps to create the mode for the database operations
 *@Eric
 */
class Staff_model extends CI_Model {

    var $subQuery ='';
    private $single_contact;

    public function __construct() {
        parent :: __construct();
        $this->load->database();
        $this->single_contact = "
                (SELECT `user_id`, `mobile_number` FROM `fms_contact`
                WHERE `id` in (SELECT MAX(`id`) from `fms_contact` WHERE `contact_type_id`=1 GROUP BY `user_id`)
            )";
    }
    /**
     * This method helps to add staff into the sacco
     */
    public function add_staff($user_id,$staff_no) {
        $data = array(
            'user_id' => $user_id,
            'staff_no' => $staff_no,
            'branch_id' => $_SESSION['branch_id'],
            'position_id' => $this->input->post('position_id'),
            'status_id' => '1',
            'date_created' => time(),
            'created_by' => $_SESSION['id'],
            'comment' => $this->input->post('comment')
        );
        $this->db->insert('staff', $data);
        return $this->db->insert_id();
    }

    /**
     * This method displays staff data from the database
     */
    public function get_staff($id = false) {

        if ($id !== false) {
            $this->db->select('s.id,s.staff_no,u.salutation,u.firstname,u.login_attempt,u.lastname,u.othernames, p.position, c.mobile_number, u.email,u.password, u.gender, '
                    . 'u.marital_status_id, ms.marital_status_name, u.date_of_birth ,u.disability, u.children_no,u.photograph, u.dependants_no, u.crb_card_no, s.position_id,'
                    . 'u.date_created,u.created_by, u.comment,s.user_id,d.salutation AS created_by_salutation,d.firstname AS created_by_firstname,'
                    . 'd.lastname AS created_by_lastname, d.othernames AS created_by_othernames,s.status_id');
            $this->db->from('staff s');
            $this->db->join('user u', 'u.id=s.user_id', 'left');
            $this->db->join('user d', 'd.id=s.created_by', 'left');
            $this->db->join('branch b', 'b.id = s.branch_id', 'left');
            $this->db->join('position p', 'p.id = s.position_id', 'left');
            $this->db->join('marital_status ms', 'u.marital_status_id=ms.id','left');
            $this->db->join("$this->single_contact c", "c.user_id = u.id", "left");
            $this->db->where('s.id', $id);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->distinct('s.staff_no', false);
            $this->db->select('s.staff_no,s.id,s.user_id,concat(s.staff_no," | ",u.lastname," ",u.firstname," ", u.othernames) staff_name,u.salutation,u.firstname,u.login_attempt,u.lastname,u.othernames,b.branch_name, p.position, c.mobile_number,u.email,s.status_id,client_no');
            $this->db->from('staff s');
            $this->db->join('user u', 'u.id=s.user_id', 'left');
            $this->db->join('member m', 'u.id=m.user_id', 'left');
            $this->db->join('branch b', 'b.id = s.branch_id', 'left');
            $this->db->join('position p', 'p.id = s.position_id', 'left');
            $this->db->join("$this->single_contact c", "c.user_id = u.id", "left");
            if($this->input->post('status_id') !=NULL){
                $this->db->where('s.status_id',$this->input->post('status_id'));
            }else{
                $this->db->where('s.status_id',1);
            }
            
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function get_count($filter = 1) {
        $this->db->select(" count(`id`) `staff_count`")
                ->where($filter);
        $q = $this->db->get("staff");
        return $q->row_array();
    }
    /**
     * This method deactivate staff data from the database
     */
    public function change_status_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array(
                'status_id' =>$this->input->post('status_id'),
                'modified_by' =>$_SESSION['id']
            );
            $this->db->where('id', $id);
            $query = $this->db->update('staff',$data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
             $data = array(
                'status_id' =>$this->input->post('status_id'),
                'modified_by' =>$_SESSION['id']
             );
            $this->db->where('id', $id);
            $query = $this->db->update('staff',$data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function delete_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->delete('staff');
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete('staff');
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }
    /**
     * This method updates staff data in the database
     */
    public function update_staff() {
        $id = $this->input->post('id');
        $data = array(
            'branch_id' => $_SESSION['branch_id'],
            'position_id' => $this->input->post('position_id'),
            'modified_by' => $_SESSION['id'],
            'comment' => $this->input->post('comment'),
        );
        $this->db->where('id', $id);
        $query = $this->db->update('staff', $data);
        if ($query) {
            return 1;
        } else {
            return false;
        }
    }

    //Get registered by
    public function get_registeredby($filter = FALSE) {
        $this->db->select('staff.id,firstname,lastname,othernames,salutation,staff_no');
        $this->db->from('staff');
        $this->db->join('user', 'user.id=staff.user_id');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('staff.id=' . $filter);
                $query = $this->db->get('', 1);
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
  
   
}
