<?php
/**
 * @Author Eric
 */
if (!defined('BASEPATH'))
    exit("No direct script access allowed");
class Payment_transactions{

  protected $CI;
  public function __construct(){
    // Assign the CodeIgniter super-object
    $this->CI = & get_instance();
    $this->CI->load->model('payment_model', '', TRUE);
  }
  public function uuid(){
      return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
          mt_rand(0, 0xffff), mt_rand(0, 0xffff),
          mt_rand(0, 0xffff),
          mt_rand(0, 0x0fff) | 0x4000,
          mt_rand(0, 0x3fff) | 0x8000,
          mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
      );
  }

 
  //for generating transaction ids
  public function merchantTransaction(){
        $final_string=$this->uuid();

        $post_data['merchant_transaction_id']=$final_string;
        if( $this->CI->input->post('transaction_type_id')==1){
             $post_data['payment_id']=2;
         } else if( $this->CI->input->post('transaction_type_id')==2){
            $post_data['payment_id']='1';
         }else if($this->CI->input->post('transaction_type_id')==4){
            $post_data['payment_id']=3;
         }else{
          $post_data['payment_id']=1;
         }
        $post_data['member_id']=$this->CI->input->post('member_id');
        
        $data['merchant_transaction_id'] = $final_string;

        return $this->CI->payment_model->set($post_data);

    }

  

}