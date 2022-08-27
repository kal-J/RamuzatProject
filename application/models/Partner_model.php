<?php

/**
 * This class helps to create the mode for the database operations
 *@Eric
 */
class Partner_model extends CI_Model {

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
     * This method helps to add partner into the sacco
     */
    public function add($user_id,$partner_no) {
        $data = array(
            'user_id' => $user_id,
            'partner_no' => $partner_no,
            'status_id' => '1',
            'date_created' => time(),
            'created_by' => $_SESSION['id'],
            'modified_by' => $_SESSION['id'],
            'comment' => $this->input->post('comment')
        );
        $this->db->insert('partner', $data);
        return $this->db->insert_id();
    }

    /**
     * This method displays partner data from the database
     */
    public function get($id = false) {

        if ($id !== false) {
            $this->db->select('p.id,p.partner_no,u.salutation,u.firstname, u.lastname,u.othernames,c.mobile_number, u.email,u.password, u.gender, '
                    . 'u.marital_status_id, ms.marital_status_name, u.date_of_birth ,u.disability, u.children_no,u.photograph, u.dependants_no, u.crb_card_no, s.position_id,u.date_registered,'
                    . 'u.date_created, u.registered_by,u.created_by, u.comment,p.user_id,d.salutation AS created_by_salutation,d.firstname AS created_by_firstname,'
                    . 'd.lastname AS created_by_lastname, d.othernames AS created_by_othernames');
            $this->db->where('p.id', $id);
            $this->db->from('partner p');
            $this->db->join('user u', 'u.id=p.user_id', 'left');
            $this->db->join('user d', 'd.id=p.created_by', 'left');
            $this->db->join('marital_status ms', 'u.marital_status_id=ms.id');
            $this->db->join("$this->single_contact c", "c.user_id = u.id", "left");
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->distinct('p.partner_no', false);
            $this->db->select('p.partner_no,p.id,p.user_id, u.salutation,u.firstname, u.lastname,u.othernames, c.mobile_number, u.email');
            $this->db->from('partner p');
            $this->db->join('user u', 'u.id=p.user_id', 'left');
            if ($this->input->post('status_id') !=NULL && $this->input->post('status_id') !='') {
              $this->db->where('p.status_id',$this->input->post('status_id'));
            } 
            $this->db->join("$this->single_contact c", "c.user_id = u.id", "left");
            $query = $this->db->get();
            return $query->result_array();
        }
    }
    /**
     * This method deactivate partner data from the database
     */
    public function change_status_by_id($id = false) {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' =>2);
            $this->db->where('id', $id);
            $query = $this->db->update('partner',$data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $data = array('status_id' =>'2');
            $this->db->where('id', $id);
            $query = $this->db->update('partner',$data);
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
            $query = $this->db->delete('partner');
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete('partner');
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }
    /**
     * This method updates partner data in the database
     */
    public function update() {
        $id = $this->input->post('id');
        $data = array(
            'status_id' => '1',
            'modified_by' => $_SESSION['id'],
            'comment' => $this->input->post('comment'),
        );
        $this->db->where('id', $id);
        $query = $this->db->update('partner', $data);
        if ($query) {
            return 1;
        } else {
            return false;
        }
    }

}
