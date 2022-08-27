<?php
/**
 * Description of Loan provision portfolio aging  Model
 * @author AMBROSE OGWANG 
 */

class Portfolio_aging_model extends CI_Model
{

    private $single_contact;
    public function __construct()
    {
        $this->load->database();
    }

    public $table = 'fms_loan_provision_portfolio_setting';

    public function get($filter = false)
    {
        
        $this->db->select("*")
            ->from('fms_loan_provision_portfolio_setting');
            $this->db->where('status_id',1);
            $this->db->order_by('date_created','DESC');
        if ($filter === false) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            $this->db->where($filter);
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function set(){
        $data = array(
             'start_range_in_days' =>$this->input->post('start_range_in_days'), 
             'end_range_in_days' =>$this->input->post('end_range_in_days'), 
             'name' => trim($this->input->post('name')), 
             'description' => trim($this->input->post('description')), 
             'provision_percentage' => $this->input->post('provision_percentage'), 
             'provision_loan_loss_account_id' => $this->input->post('provision_loan_loss_account_id'), 
             'asset_account_id'=>$this->input->post('asset_account_id'),
             'provision_method_id'=>$this->input->post('provision_method_id'),
             'date_created' => time(), 
             'created_by' => $_SESSION['id'] 
         );

       $this->db->insert($this->table, $data);
       return $this->db->insert_id();
 }
 public function update($id){
    $data = array(
        'start_range_in_days' =>$this->input->post('start_range_in_days'), 
        'end_range_in_days' =>$this->input->post('end_range_in_days'), 
        'name' => $this->input->post('name'), 
        'description' => $this->input->post('description'), 
        'provision_percentage' => $this->input->post('provision_percentage'), 
        'provision_loan_loss_account_id' => $this->input->post('provision_loan_loss_account_id'),  
        'asset_account_id'=>$this->input->post('asset_account_id'),
        'provision_method_id'=>$this->input->post('provision_method_id'),
         'modified_by' => $_SESSION['id'],
        );
    $this->db->where("$this->table.id", $id);
    return $this->db->update($this->table, $data);

}
public function change_status($data){
    $this->db->set(array("status_id"=>$data['status_id'],"modified_by"=>$_SESSION['id']));
   
    $this->db->where("id",$data['id']);

    $query=$this->db->update($this->table);
    if ($query) {
        return true;
    } else {
        return false;
    }
}

public function delete($data){
    if(isset($data['id']) && $data['id'] !=""){
    $this->db->set(array("status_id"=>0,"modified_by"=>$_SESSION['id']));
    $this->db->where('id',$data['id']);
    $query= $this->db->update($this->table);
   
    if ($query) {
        return true;
    } else {
        return false;
    }
}
}
 
}
