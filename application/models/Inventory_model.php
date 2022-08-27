<?php
/**
 * Description of Inventory_model
 * @author reagan
 */
class Inventory_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    
   public function get($filter = FALSE) {
       
        $this->db->select("a.id,a.asset_name,a.asset_account_id,IFNULL(cumm_dep,0) cumm_dep,IFNULL(cumm_app,0) cumm_app, a.identity_no, a.purchase_date, a.purchase_cost, a.date_when, "
                . "a.depreciation_account_id, a.expense_account_id, a.depreciation_method_id, a.depreciation_rate,a.appreciation_rate,a.appreciation_account_id,a.income_account_id,depre_appre_id,depre_appre, a.description, a.status_id,a.salvage_value,a.appreciation_loss_account_id,a.appreciation_gain_account_id,a.depreciation_loss_account_id,a.depreciation_gain_account_id, "
                . "a.expected_age, at.account_name, at.account_code, dm.method_name, "
                . "expac.account_name expense_account_name, expac.account_code expense_account_code,"
                . "depac.account_name depreciation_account_name, depac.account_code depreciation_account_code,appac.account_name appreciation_account_name, appac.account_code appreciation_account_code,inpac.account_name income_account_name, inpac.account_code income_account_code");
        $this->db->from('fixed_assets a');
        $this->db->join("accounts_chart at", "at.id=a.asset_account_id","LEFT");
        $this->db->join("accounts_chart depac", "`depac`.`id`=a.depreciation_account_id","LEFT");
        $this->db->join("accounts_chart expac", "`expac`.`id`=a.expense_account_id","LEFT");

        $this->db->join("accounts_chart appac", "`appac`.`id`=a.appreciation_account_id","LEFT");
        $this->db->join("accounts_chart inpac", "`inpac`.`id`=a.income_account_id","LEFT");

        $this->db->join("depreciation_method dm", "`dm`.`id`=a.depreciation_method_id","LEFT");
        $this->db->join("depre_appre_option da", "`da`.`id`=a.depre_appre_id","LEFT");
        $this->db->join("(SELECT SUM(`amount`) `cumm_dep`, `fixed_asset_id` FROM `fms_depreciation` GROUP BY `fixed_asset_id`) cum_dep", "`a`.`id`=cum_dep.fixed_asset_id","LEFT",FALSE);

         $this->db->join("(SELECT SUM(`amount`) `cumm_app`, `fixed_asset_id` FROM `fms_appreciation` GROUP BY `fixed_asset_id`) cumm_app", "`a`.`id`=cumm_app.fixed_asset_id","LEFT",FALSE);
         
        if(is_numeric($this->input->post("status_id"))){
            $this->db->where("a.status_id=" . $this->input->post("status_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("a.id=" . $filter);
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
        //Application date
        $purchase_date = explode('-', $data['purchase_date'], 3);
        $data['purchase_date'] = count($purchase_date) === 3 ? ($purchase_date[2] . "-" . $purchase_date[1] . "-" . $purchase_date[0]) : null;
        $date_when = explode('-', $data['date_when'], 3);
        $data['date_when'] = count($date_when) === 3 ? ($date_when[2] . "-" . $date_when[1] . "-" . $date_when[0]) : null;
        unset($data['id'], $data['tbl']);
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] =$_SESSION['id'];

        $this->db->insert('fixed_assets', $data);
        return $this->db->insert_id();
    }

    public function update() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'], $data['tbl']);
        $purchase_date = explode('-', $data['purchase_date'], 3);
        $data['purchase_date'] = count($purchase_date) === 3 ? ($purchase_date[2] . "-" . $purchase_date[1] . "-" . $purchase_date[0]) : null;
        $date_when = explode('-', $data['date_when'], 3);
        $data['date_when'] = count($date_when) === 3 ? ($date_when[2] . "-" . $date_when[1] . "-" . $date_when[0]) : null;
        $data['modified_by'] = $_SESSION['id'];

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('fixed_assets', $data);
        } else {
            return false;
        }
    }

    public function deactivate() {
        $data = array(
            'status_id' => $this->input->post('status_id'),
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fixed_assets', $data);
    }

    public function delete() {
        $data = array(
            'status_id' => 0,
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('fixed_assets', $data);
    }


    public function get_journal_transaction() {
        $this->db->from('journal_transaction');
        $this->db->where('journal_type_id', $this->input->post('journal_type_id'));
        $this->db->where('ref_no', $this->input->post('transaction_no'));
        $query = $this->db->get();
        return $query->row_array();
    }

    /////////////////================INCOME FROM ASSET =====================================

    public function get_asset_income($filter = FALSE) {
        $this->db->select("as.*,fa.asset_name,ac.account_code,ac.account_name,tc.channel_name,it.income_type");
        $this->db->from('asset_income as');
        $this->db->join("accounts_chart ac", "ac.id=as.income_account_id", "LEFT");
        $this->db->join("transaction_channel tc", "tc.id=as.transaction_channel_id","LEFT");
        $this->db->join("user_income_type it", "it.id=as.income_type_id","LEFT");
        $this->db->join("fixed_assets fa", "fa.id=as.asset_id","LEFT");
        if (is_numeric($this->input->post("status_id"))) {
            $this->db->where("as.status_id=" . $this->input->post("status_id"));
        }
        if (is_numeric($this->input->post("asset_id"))) {
            $this->db->where("as.asset_id=" . $this->input->post("asset_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("as.id=" . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set_income($data = array()) {
        if (empty($data)) {
            $data = $this->input->post(NULL, TRUE);
            unset($data['id'], $data['tbl']);
        }
        $transaction_date = explode('-', $data['transaction_date'], 3);
        $data['transaction_date'] = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
        $data['transaction_no'] = date('yws').mt_rand(100, 999);
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] = $_SESSION['id'];
        $this->db->insert('asset_income', $data);
        $last_id = $this->db->insert_id();
        if(is_numeric($last_id)){
            $response['transaction_no']=$data['transaction_no'];
            $response['transaction_id']=$last_id;
            return $response;
        }else{
            return false;
        }
    }

    public function update_income() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['journal_type_id'],$data['transaction_no'],$data['tbl']);
        $data['modified_by'] = $_SESSION['id'];
        $transaction_date = explode('-', $data['transaction_date'], 3);
        $data['transaction_date'] = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('asset_income', $data);
        } else {
            return false;
        }
    }
   
    ///////////////==================END OF INCOME FROM ASSET =============================== 
   /*                                                                                     */
    /////////////////================ START EXPENSE FROM ASSET =====================================

    public function get_asset_expense($filter = FALSE) {
        $this->db->select("as.*,fa.asset_name,ac.account_code,payment_mode,ac.account_name,ac2.account_code as exp_account_code,ac2.account_name as exp_account_name, et.expense_type");
        $this->db->from('asset_expense as');
        $this->db->join("accounts_chart ac", "ac.id=as.fund_source_account_id", "LEFT");
        $this->db->join("accounts_chart ac2", "ac2.id=as.expense_account_id", "LEFT");
        $this->db->join("user_expense_type et", "et.id=as.expense_type_id","LEFT");
        $this->db->join("payment_mode p", "p.id=as.payment_id","LEFT");
        $this->db->join("fixed_assets fa", "fa.id=as.asset_id ","LEFT");
        if (is_numeric($this->input->post("status_id"))) {
            $this->db->where("as.status_id=" . $this->input->post("status_id"));
        }
        if (is_numeric($this->input->post("asset_id"))) {
            $this->db->where("as.asset_id=" . $this->input->post("asset_id"));
        }
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("as.id=" . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function set_expense($data = array()) {
        if (empty($data)) {
            $data = $this->input->post(NULL, TRUE);
            unset($data['id'], $data['tbl']);
        }
        $transaction_date = explode('-', $data['transaction_date'], 3);
        $data['transaction_date'] = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
        $data['transaction_no'] = date('yws').mt_rand(100, 999);
        $data['date_created'] = time();
        $data['created_by'] = $_SESSION['id'];
        $data['modified_by'] = $_SESSION['id'];

        $this->db->insert('asset_expense', $data);
        $last_id = $this->db->insert_id();
        if(is_numeric($last_id)){
            $response['transaction_no']=$data['transaction_no'];
            $response['transaction_id']=$last_id;
            return $response;
        }else{
            return false;
        }
    }

    public function update_expense() {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        unset($data['id'],$data['journal_type_id'],$data['transaction_no'],$data['tbl']);
        $data['modified_by'] = $_SESSION['id'];
        $transaction_date = explode('-', $data['transaction_date'], 3);
        $data['transaction_date'] = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->update('asset_expense', $data);
        } else {
            return false;
        }
    }

    ///////////////==================END OF EXPENSE FROM ASSET =============================== 

 public function edit_journal_transaction($filter =FALSE) {
          $transaction_date = explode('-', $this->input->post('transaction_date'), 3);
        $transaction_date_final = count($transaction_date) === 3 ? ($transaction_date[2] . "-" . $transaction_date[1] . "-" . $transaction_date[0]) : null;
        $data = array(
            'transaction_date' => $transaction_date_final,
            'modified_by' => $_SESSION['id'],
            'description' => $this->input->post('narrative')
        );
        $this->db->where('id', $filter);
        $this->db->update('journal_transaction', $data);
        return $this->edit_journal_transaction_line($filter,$transaction_date_final);
    }
    private function edit_journal_transaction_line($filter,$transaction_date_final) {
        $data = array(
            'modified_by' => $_SESSION['id'],
            'transaction_date' => $transaction_date_final,
            'narrative' => $transaction_date_final." ".$this->input->post('narrative')
        );
        $this->db->where('journal_transaction_id', $filter);
        return $this->db->update('journal_transaction_line', $data);
    }

    public function get_last_depre_appre_tnx_date($table,$asset_id){
        $query= $this->db->select("MAX(transaction_date) as transaction_date");
        $this->db->from($table);
        $this->db->where($table.".fixed_asset_id=",$asset_id);
        $query=$this->db->get();
        return $query->result_array();
    }

}
?>