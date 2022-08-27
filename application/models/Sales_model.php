<?php

/**
 * Description of Sales_model
 *
 * @author Joshua
 */
class Sales_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    public function get($filter = FALSE)
    {
        $this->db->select('st.*,ifs.name AS item_name,sa.account_no,concat( concat(salutation,".")," ",firstname," ", lastname," ", othernames) AS member_name');
        $this->db->from('sales_transactions st');
        $this->db->join('fms_items_for_sale ifs', 'ifs.id=st.item_id', 'left');
        $this->db->join('fms_savings_account sa', 'sa.id=st.savings_account_id', 'left');
        $this->db->join("member", "member.id = sa.member_id");
        $this->db->join("user", "member.user_id = user.id");
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where('st.id=' . $filter);
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

    public function get_items($filter = FALSE){
        $this->db->from('items_for_sale');
        $this->db->where('status_id',1);
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)){
                $this->db->where('id=' . $filter);
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

    public function set()
    {
        $data = $this->input->post(NULL, TRUE);

        unset($data['id'], $data['tbl']);
        $data['date_created'] = time();
        $data['transaction_date'] = date("Y-m-d", strtotime($data['transaction_date']));
        $data['created_by'] = $data['modified_by'] = $_SESSION['id'];
        $data['ref_no'] = date('ymdhms').mt_rand(100, 999);

        // echo json_encode($data); die();

        $this->db->insert('sales_transactions', $data);
        $return['transaction_no'] = $this->db->insert_id();
        $return['ref_no'] = $data['ref_no'];
        return $return;
    }

    public function set_for_items(){
        $data = $this->input->post(NULL, TRUE);

        if(isset($data['id']) && is_numeric($data['id'])){

            $data['modified_by'] = $_SESSION['id'];
            $this->db->where('id', $this->input->post('id'));
            return $this->db->update('items_for_sale', $data);
            
        }else{
            $data['date_created'] = time();
            $data['created_by'] = $_SESSION['id'];
            $data['status_id'] = 1;
            $this->db->insert('items_for_sale', $data);
            $return['transaction_no'] = $this->db->insert_id();
            return $return;
        }
        

    }

    public function deactivate_item($id)
    {
        $data['modified_by'] = $_SESSION['id'];
        $data['status_id'] = 0;
        $this->db->where('id', $id);
        return $this->db->update('items_for_sale', $data);
    }

    public function deduct_savings($data)
    {
        $this->db->insert('fms_transaction', $data);
        $return['transaction_no'] = $this->db->insert_id();
        return $return;
    }

    
}
