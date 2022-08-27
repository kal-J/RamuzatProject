<?php

class Member_model extends CI_Model
{

    private $single_contact;
    private $columns = ["m.client_no", "u.firstname", "u.lastname",  "u.othernames", "concat(u.firstname, ' ', u.lastname, ' ', u.othernames) member_name1", "concat(u.lastname, ' ', u.firstname, ' ', u.othernames) member_name2", "branch_name", "c.mobile_number", "u.email", "u.gender", "ms.marital_status_name", "m.date_registered", "m.id", "m.occupation", "m.subscription_plan_id", "sp.plan_name", "u.salutation", "u.marital_status_id", "u.date_of_birth", "u.disability", "u.children_no", "u.dependants_no", "u.crb_card_no", "u.date_created", "u.created_by", "u.comment", "m.user_id", "m.status_id"];
    private $alias_only_pattern = '/(\s+(as[\s]+)?)((`)?[a-zA-Z0-9_]+(`)?)$/';

    public function __construct()
    {
        $this->load->database();
        $this->single_contact = "
                (SELECT `user_id`, `mobile_number` FROM `fms_contact`
                WHERE `id` in (
                    SELECT MAX(`id`) from `fms_contact` WHERE `contact_type_id`=1 GROUP BY `user_id` 
                )
            )";

        $this->referred_by = "(SELECT u.id,mr.introduced_by_id,mr.user_id,CONCAT(u.firstname,' ',u.lastname,' ',u.othernames)as referred_by_name FROM fms_member mr
        LEFT JOIN fms_user u 
        ON(u.id=mr.introduced_by_id))";
    }

    public function get_member_contact($filter)
    {
        $this->db->select('firstname,lastname,othernames,email,mobile_number,branch_id');
        $this->db->from('member m');
        $this->db->join('user u', 'u.id = m.user_id', 'left');
        $this->db->join($this->single_contact . " c", "c.user_id = u.id", "left");
        if (is_numeric($filter)) {
            $this->db->where("m.id=", $filter);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->where($filter);
            $query = $this->db->get();
            return $query->row_array();
        }
    }

    public function get_due_birthdays($birthday)
    {
        $this->db->select('firstname,lastname,othernames,email,mobile_number,branch_id,o.name as organisation_name');
        $this->db->from('member m');
        $this->db->join('user u', 'u.id = m.user_id', 'left');
        $this->db->join('branch b', 'b.id = m.branch_id', 'left');
        $this->db->join('organisation o', 'o.id = b.organisation_id', 'left');
        $this->db->join($this->single_contact . " c", "c.user_id = u.id", "left");
        $this->db->where("u.date_of_birth=", $birthday);
        $query = $this->db->get();
        //print_r($this->db->last_query());die;
        return $query->result_array();
    }


    // public function get1(){
    //     $query = $this->db->query("SELECT a.client_no,a.firstname,a.lastname,a.othernames,a.gender,a.marital_status_id,a.email,date_registered,a.id FROM( SELECT fms_user.id,client_no,marital_status_id,email,firstname,lastname,othernames,gender,fms_user.date_registered FROM fms_user JOIN fms_member ON fms_user.id=fms_member.user_id WHERE fms_member.status_id=1 ) a JOIN( SELECT client_no,firstname,lastname,othernames,COUNT(firstname) number FROM fms_user JOIN fms_member ON fms_user.id=fms_member.user_id WHERE fms_member.status_id=1 GROUP BY firstname,lastname,othernames HAVING COUNT(firstname) > 1 ) b
    //         ON a.firstname=b.firstname AND a.lastname=b.lastname AND a.othernames=b.othernames ORDER BY a.firstname,a.lastname,a.othernames");
    //     return $query->result_array();
    // }
    public function get_member($filter = FALSE)
    {
        $this->db->select('m.id,o.`name`,m.branch_id,b.branch_name,m.occupation,m.client_no,u.spouse_names,u.spouse_contact,u.salutation,u.firstname,u.password, u.lastname,u.othernames,cr.firstname as created_firstname, '
            . 'cr.lastname as created_lastname,cr.othernames as created_othernames ,reg.firstname as registered_firstname, reg.lastname as registered_lastname,'
            . 'reg.othernames as registered_othernames,reg.salutation as registered_salutation,cr.salutation as created_salutation, u.email, u.gender, '
            . 'u.date_of_birth ,u.disability, u.children_no, u.dependants_no, u.crb_card_no, m.date_registered,u.date_created, m.registered_by, u.created_by,u.photograph, '
            . 'u.comment, m.user_id,m.subscription_plan_id, sp.plan_name,u.marital_status_id, ms.marital_status_name,m.status_id,b.organisation_id,u.nid_card_no,mr.referred_by_name,m.introduced_by_id');
        $this->db->from('member m');
        $this->db->join('user u', 'u.id = m.user_id', 'left');
        $this->db->join('staff s_reg', 's_reg.id=m.registered_by', 'left');
        $this->db->join('user reg', 'reg.id=s_reg.user_id', 'left');
        $this->db->join("$this->referred_by mr", "mr.user_id = u.id", "left");
        //$this->db->join('user reg', 'reg.id=m.registered_by', 'left');

        $this->db->join('user cr', 'cr.id=m.created_by', 'left');
        $this->db->join('marital_status ms', 'u.marital_status_id=ms.id', 'left');
        $this->db->join('subscription_plan sp', 'sp.id = m.subscription_plan_id', 'left');
        $this->db->join('branch b', 'b.id = m.branch_id', 'left');
        $this->db->join('organisation o', 'o.id = b.organisation_id', 'left');
        if ($this->input->post("no_accounts") != '1') {
            $this->db->select('sa.account_no');
            $this->db->join('savings_account sa', 'sa.member_id = m.id', 'left');
        }
        if ($this->input->post("term") !== null) { //when searching from the dropdown select2
            $this->db->or_like('u.firstname', $this->input->post('term'));
            $this->db->or_like('u.lastname', $this->input->post('term'));
            $this->db->or_like('u.email', $this->input->post('term'));
            $this->db->or_like("concat(u.firstname, ' ', u.lastname, ' ', u.othernames)", $this->input->post('term'));
            $this->db->or_like("concat(u.lastname, ' ', u.firstname, ' ', u.othernames)", $this->input->post('term'));
            $this->db->or_like('u.othernames', $this->input->post('term'));
            $this->db->or_like('m.client_no', $this->input->post('term'));
        }

        if (is_numeric($this->input->post("status_id"))) {
            $this->db->where('m.status_id=', $this->input->post('status_id'));
        }
        if (is_numeric($this->input->post("created_by"))) {
            $this->db->where('m.created_by=', $this->input->post('created_by'));
        }
        if ($this->input->post('page') !== NULL && is_numeric($this->input->post('page'))) {
            $this->db->limit(50, $this->input->post('page') - 1);
        } else {
            $this->db->limit(100);
        }
        if ($filter === FALSE) {
            $this->db->select('c.mobile_number');
            $this->db->join($this->single_contact . " c", "c.user_id = u.id", "left");
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("m.id=", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->select('c.mobile_number');
                $this->db->join("$this->single_contact c", "c.user_id = u.id", "left");
                $this->db->join("spouse s", "s.user_id = u.id", "left");
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
    public function get($filter = FALSE)
    {
        $this->db->select(implode(", ", $this->columns), FALSE);
        $this->get_select();
        if (isset($_SESSION) && isset($_SESSION['branch_id']) && is_numeric($_SESSION['branch_id'])) {
            $this->db->where("m.branch_id =", (int) $_SESSION['branch_id']);
        }
        if ($this->input->post('status_id') !== NULL && is_numeric($this->input->post('status_id'))) {
            $this->db->where('m.status_id =', $this->input->post('status_id'));
        }
        if (is_numeric($this->input->post("created_by"))) {
            $this->db->where('m.created_by=', $this->input->post('created_by'));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("m.id=", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get1($filter = FALSE)
    {
        $this->db->select('count(m.id) no_rows');
        $this->db->from('member m');
        if (isset($_SESSION) && isset($_SESSION['branch_id']) && is_numeric($_SESSION['branch_id'])) {
            $this->db->where("m.branch_id =", (int) $_SESSION['branch_id']);
        }
        if ($this->input->post('status_id') !== NULL && is_numeric($this->input->post('status_id'))) {
            $this->db->where('m.status_id =', $this->input->post('status_id'));
        }
        if (is_numeric($this->input->post("created_by"))) {
            $this->db->where('m.created_by=', $this->input->post('created_by'));
        }
        $query = $this->db->get();
        $result = $query->row_array();
        return isset($result['no_rows']) ? $result['no_rows'] : 0;
    }

    public function get_count($filter = 1)
    {
        $this->db->select(" count(`id`) `client_count`")
            ->where($filter);
        if ($this->input->post('staff_id')) {
            $this->db->where('created_by', $this->input->post('staff_id'));
        }
        $query = $this->db->get("member");
        return $query->row_array();
    }

    private function get_select()
    {
        $this->db->select('m.id,m.occupation,m.client_no,u.spouse_names,u.spouse_contact,u.salutation,u.firstname,u.password, u.lastname,u.othernames,m.branch_id,branch_name,u.email, u.gender, '
            . 'u.date_of_birth ,u.disability,s.staff_no,u.children_no, u.dependants_no, u.crb_card_no, m.date_registered,u.date_created, m.registered_by, u.created_by,u.photograph, '
            . 'u.comment, m.user_id,m.subscription_plan_id, sp.plan_name,u.marital_status_id, ms.marital_status_name,m.status_id');
        $this->db->from('member m');
        $this->db->join('user u', 'm.user_id=u.id', 'left');
        $this->db->join('staff s', 'u.id=s.user_id', 'left');
        $this->db->join('marital_status ms', 'u.marital_status_id=ms.id', 'left');
        $this->db->join('branch b', 'b.id = m.branch_id', 'left');
        $this->db->join('subscription_plan sp', 'm.subscription_plan_id=sp.id', 'left');
        $this->db->join($this->single_contact . " c", "c.user_id = u.id", "left");
        $this->db->join("$this->referred_by mr", "mr.user_id = u.id", "left");

        if (is_numeric($this->input->post('gender'))) {
            $this->db->where('u.gender=', $this->input->post('gender'));
        }
    }

    private function set_filters($all_columns)
    {
        if ($this->input->post("search") !== NULL) {
            $search = $this->input->post("search");
            if (isset($search['value']) && $search['value'] != "") {
                $this->db->group_start();
                for ($i = 0; $i < count($this->columns); $i++) {
                    if (isset($all_columns[$i]['searchable']) && $all_columns[$i]['searchable'] == "true") {
                        $column = preg_replace($this->alias_only_pattern, '', $this->columns[$i]);
                        $this->db->or_like($column, $search['value']);
                    }
                }
                // Individual column filtering
                foreach ($this->columns as $key) {
                    if (isset($all_columns[$key]['searchable']) && $all_columns[$key]['searchable'] == "true" && $all_columns[$key]['search']['value'] != '') {
                        $this->db->or_like(preg_replace($this->alias_only_pattern, '', $this->columns[$key]), $all_columns[$key]["search"]["value"]);
                    }
                }
                $this->db->group_end();
            }
        }
    }

    public function get_found_rows()
    {
        $this->db->select("FOUND_ROWS()", FALSE);
        $q = $this->db->get();
        return $q->row_array();
    }

    public function get_dtable_format()
    {
        $all_columns = $this->input->post('columns');
        $this->db->select("SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $this->columns)), FALSE);
        $this->get_select();
        $this->set_filters($all_columns);
        if ($this->input->post('status_id') !== NULL && is_numeric($this->input->post('status_id'))) {
            $this->db->where('m.status_id =', $this->input->post('status_id'));
        }
        if (is_numeric($this->input->post("created_by"))) {
            $this->db->where('m.created_by=', $this->input->post('created_by'));
        }
        if ($this->input->post('order') !== NULL) {
            $order_columns = $this->input->post('order');
            foreach ($order_columns as $order_column) {
                if (isset($order_column['column']) && $all_columns[$order_column['column']]['orderable'] == "true") {
                    $this->db->order_by(preg_replace($this->alias_only_pattern, '', $this->columns[$order_column['column']]), $order_column['dir']);
                }
            }
        }
        if ($this->input->post('start') !== NULL && is_numeric($this->input->post('start')) && $this->input->post('length') != '-1') {
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        }

        $query = $this->db->get();
        //print_r($this->db->last_query());
        return $query->result_array();
    }

    /**
     * This method helps to add member into the sacco
     */
    public function add_member($user_id, $client_no, $post_data = [])
    {


        if ($post_data === []) {
            $member_data =  $this->input->post(NULL, TRUE);
            $introduced_by_id = empty($_POST['introduced_by_id']) ? "NULL" : $this->input->post('introduced_by_id');

            $registration_date = explode('-', $member_data['date_registered'], 3);

            $registration_date2 = count($registration_date) === 3 ? ($registration_date[2] . "-" . $registration_date[1] . "-" . $registration_date[0]) : null;

            $data = array(
                'user_id' => $user_id,
                'client_no' => $client_no,
                'branch_id' => $_SESSION['branch_id'],
                'occupation' => $member_data['occupation'],
                // 'subscription_plan_id' => $member_data['subscription_plan_id']?$member_data['subscription_plan_id']:0,
                'registered_by' => $member_data['registered_by'],
                'date_created' => time(),
                'date_registered' => $registration_date2,
                'created_by' => $_SESSION['id'],
                'modified_by' => $_SESSION['id'],
                'comment' => $member_data['comment'],
                'introduced_by_id' => $introduced_by_id
            );
        }

        // saving the member referee and referred details 



        else {
            $data = $post_data;
        }
        /*if($this->input->post('member_referral_on_off') ==1){
            $this->db->insert('member', $data);
            $this->add_member_referral($data);
            return $this->db->insert_id();
            
        }*/
        // else{
        $this->db->insert('member', $data);
        return $this->db->insert_id();

        // }


    }

    /* public function add_member_referral($data){
        $member_data = $this->input->post(NULL,TRUE);

        //if($member_data['member_referral_on_off'] !=0 && $this->input->post('introduced_by_id') !=''){
            $data2  = array(
                
                'introduced_by_id'       => $member_data['introduced_by_id'],
                'introduced_id'          => $data['user_id'],
                'date_created'           => time(),
                'organisation_id'        => $_SESSION['organisation_id'],
                'created_by'            =>  $_SESSION['id']
            );
            //unset($data2['member_referral_on_off']);
            $this->db->insert('fms_member_referral',$data2);
            
           // }
    }*/


    //Get position
    public function get_few_details($filter = FALSE)
    {
        $this->db->select('m.id,u.salutation,u.firstname, u.lastname,u.othernames, client_no');
        $this->db->from('member m');
        $this->db->join('user u', 'm.user_id=u.id', 'left');
        if ($filter === FALSE) {
            $this->db->where('m.status_id=', $this->input->post('status'));
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("m.user_id=", $filter);
                $this->db->where("m.status_id=", 1);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where("m.status_id=", 1);
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    /**
     * This method deletes MEMBER data from the database
     */
    public function delete_by_id($id = false)
    {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->delete('member');
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete('member');
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * This method updates member data in the database
     */
    public function update_member()
    {
        $registration_date = explode('-', $this->input->post('date_registered'), 3);
        $data['registration_date'] = count($registration_date) === 3 ? ($registration_date[2] . "-" . $registration_date[1] . "-" . $registration_date[0]) : null;

        $id = $this->input->post('id');
        $data = array(
            'branch_id' => $_SESSION['branch_id'],
            'occupation' => $this->input->post('occupation'),
            'subscription_plan_id' => $this->input->post('subscription_plan_id'),
            'registered_by' => $this->input->post('registered_by'),
            'date_registered' => $data['registration_date'],
            'introduced_by_id' => (int) $this->input->post('introduced_by_id'),
            'modified_by' => $_SESSION['id'],
            'comment' => $this->input->post('comment'),
        );
        $this->db->where('id', $id);
        $query = $this->db->update('member', $data);
        //print_r($this->db->last_query());die();
        if ($query) {
            return '1';
        } else {
            return false;
        }
    }

    public function temporary_delete()
    {
        $data = array(
            'status_id' => 2,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('member', $data);
    }

    public function change_status_by_id($id = false)
    {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array(
                'status_id' => $this->input->post('status_id'),
                'modified_by' => $_SESSION['id']
            );
            $this->db->where('id', $id);
            $query = $this->db->update('member', $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $data = array(
                'status_id' => $this->input->post('status_id'),
                'modified_by' => $_SESSION['id']
            );
            $this->db->where('id', $id);
            $query = $this->db->update('member', $data);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function get_member_by_user_id($filter = false)
    {
        $this->db->select("member.id, concat(b.salutation,' ',b.firstname,' ', b.lastname,' ', b.othernames) member_name,member.client_no");
        $this->db->from('member');
        $this->db->join('fms_user b', 'user_id = b.id');
        if ($filter === false) {
        } else {
            if (is_numeric($filter)) {
                $this->db->where('member.id', $filter);
                return $this->db->get()->row_array();
            }

            $this->db->where($filter);
        }
        $q = $this->db->get();
        $response = $q->result_array();
        return $response;
    }

    public function get_member_info($filter = false)
    {
        $this->db->select("sa.id,sa.account_no,deposit_Product_id");
        $this->db->from('member m');
        $this->db->join('fms_savings_account sa', 'm.id=sa.member_id', 'left');
        $this->db->where('client_no', $filter);
        $this->db->where('deposit_Product_id', 1);
        $q = $this->db->get();
        $response = $q->row_array();
        return $response;
    }
    //share account details
    public function get_member_info2($filter = false)
    {
        $this->db->select("sa.id,sa.share_account_no,share_issuance_id");
        $this->db->from('member m');
        $this->db->join('fms_share_account sa', 'm.id=sa.member_id', 'left');
        $this->db->where('client_no', $filter);
        $this->db->where('share_issuance_id!=', '');
        $q = $this->db->get();
        $response = $q->row_array();
        return $response;
    }

    public function get_member_data($filter)
    {
        $this->db->select('m.id,salutation,firstname,lastname,othernames,email,mobile_number,client_no,m.date_registered,account_no');
        $this->db->from('member m');
        $this->db->join('user u', 'u.id = m.user_id', 'left');
        $this->db->join('savings_account sa', 'sa.member_id=m.id', 'left');
        $this->db->join($this->single_contact . " c", "c.user_id = u.id", "left");
        $this->db->where('m.status_id=1');
        $this->db->like('firstname', $filter);
        $this->db->or_like('lastname', $filter);
        $this->db->or_like('othernames', $filter);
        $this->db->or_like('client_no', $filter);
        $this->db->or_like('account_no', $filter);
        $this->db->or_like('mobile_number', $filter);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function member_birthday()
    {
        $birthday = date('m-d');/* 
        $now = new DateTime();
        echo DATE_FORMAT($now, `%m-%d`);
        die(); */

        $this->db->select('m.id,o.`name`,m.branch_id,b.branch_name,m.occupation,m.client_no,u.spouse_names,u.spouse_contact,u.salutation,u.firstname,u.password, u.lastname,u.othernames,'
            . 'u.email, u.gender, '
            . 'u.date_of_birth ,u.disability, u.children_no, u.dependants_no, u.crb_card_no, m.date_registered,u.date_created, m.registered_by, u.created_by,u.photograph, '
            . 'u.comment, m.user_id,m.subscription_plan_id,u.marital_status_id,m.status_id,b.organisation_id,u.nid_card_no');
        $this->db->from('member m');
        $this->db->join('user u', 'u.id = m.user_id', 'left');
        $this->db->join('branch b', 'b.id = m.branch_id', 'left');
        $this->db->join('organisation o', 'o.id = b.organisation_id', 'left');

        $this->db->where('m.status_id=', 1);
        $this->db->where("DATE_FORMAT(u.date_of_birth, '%m-%d') = ", $birthday);
        $this->db->select('c.mobile_number');
        $this->db->join($this->single_contact . " c", "c.user_id = u.id", "left");

        $query = $this->db->get();
        $return = $query->result_array();
        //echo json_encode($return); die();


    }
    public function get_member_referral_list1()
    {
        $this->db->distinct('m.introduced_by_id');
        $this->db->select("u.id,m.introduced_by_id,CONCAT(firstname,' ',lastname,' ',othernames)as member_name
        ");
        $this->db->from('member m');
        $this->db->join('user u', 'u.id=m.introduced_by_id', 'LEFT');
        $this->db->where('m.introduced_by_id !=', '');
        $query = $this->db->get();
        return $query->result_array();
    }

    // details of referrals 
    public function get_member_referral_list($filter = FALSE)
    {

        $this->db->select("m.introduced_by_id,CONCAT(firstname,' ',lastname,' ',othernames)as member_name,
      SUM(IFNULL(ft.credit,0)-IFNULL(ft.debit,0)) as saving_account_balance,COUNT(m.introduced_by_id) as number_of_referrals,sv.account_no,IFNULL(amf.amount,0) as fees_paid,(IFNULL(st.credit,0)-IFNULL(st.debit,0))/si.price_per_share as shares_bought
     ");

        $this->db->from('user u');
        //$this->db->join('member_referral mr','mr.introduced_id=u.id','LEFT');
        $this->db->join('member m', 'u.id=m.user_id', 'LEFT');
        $this->db->join('fms_savings_account sv', 'sv.member_id=m.id', 'LEFT');
        $this->db->join('fms_transaction ft', 'ft.account_no_id=sv.id', 'LEFT');
        $this->db->join('applied_member_fees amf', 'amf.member_id=m.id', 'LEFT');
        $this->db->join("share_account sa", "sa.member_id=m.id", "LEFT");
        $this->db->join("fms_share_transactions st", "sa.id=st.share_account_id", "LEFT");
        $this->db->join("fms_share_issuance si", "st.share_issuance_id=si.id", "LEFT");

        $this->db->group_by("u.id");

        if ($filter === FALSE) {
            $this->db->where('m.status_id=', 1);
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {

                $this->db->where("m.introduced_by_id", $filter);

                $query = $this->db->get();
                return $query->result_array();
            } else {

                $this->db->where("m.status_id=", 1);
                $this->db->where('m.introduced_by_id !=', '');
                //$this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }
}
