<?php

/**
 * Description of Num_format_helper
 *
 * @author allan_jes modified by Ambrose
 */
class Num_format_helper {

    protected $CI;

    public function __construct() {
        // Assign the CodeIgniter super-object
        $this->CI = & get_instance();
        $this->CI->load->model("misc/num_format_model");
        $this->CI->load->model("DepositProduct_model",'',TRUE);
        $this->CI->load->model("loan_product_model",'',TRUE);
        $this->CI->load->model("share_issuance_model",'',TRUE);
        $this->CI->load->model('organisation_format_model', '', TRUE);
        $this->fields = ["account_format", "loan_format", "client_format", "staff_format", "group_format", "share_format", "partner_format"];
    }

    public function new_savings_acc_no() {

          $product_code= "";
          $product_id = $this->CI->input->post('deposit_Product_id');
         
         if(isset($product_id) && is_numeric($product_id)){
             $saving_product_code= $this->CI->DepositProduct_model->get('sp.id='.$product_id.' AND sp.status_id=1');
             $product_code = trim($saving_product_code[0]['product_code']);
             
         }

        $org_acc_num_format = $this->CI->organisation_format_model->get_format_types(FALSE, ['account_format','account_format_initials']);
          if (empty($org_acc_num_format['account_format'])) {
            return FALSE;
        } 
        else{
             $account_initials=strtoupper($org_acc_num_format['account_format_initials']);
             
             //echo json_encode($account_initials);die();
             $ac_initials_len=strlen($account_initials);
             if($ac_initials_len==1){
              $account_format=substr($org_acc_num_format['account_format'],0, $ac_initials_len);
              $account_initials2=$account_initials.$account_format; 
              }
            else{
               $account_initials2=substr($account_initials,0,$ac_initials_len);
              }
              // echo json_encode($product_code);die();
        $last_account_no = $this->CI->num_format_model->get_last_no2("savings_account", "account_no",$account_initials2,$product_code,$product_id);

        // echo json_encode($last_account_no);die();
         
        //if there isn't any account, we just start from the current format
        if (!empty($last_account_no)) {
             if(is_numeric($last_account_no[0])){
                    $saving_product_code_len = strlen($product_code);
                    $last_saving_acc_no_generated =substr($last_account_no,$saving_product_code_len);
                    ++$last_saving_acc_no_generated;

                    return $product_code.$last_saving_acc_no_generated;
                    
                }
                return ++$last_account_no;
             
        } else {
            //if organisation format not set, we just return 1
            return !empty($org_acc_num_format['account_format']) ?  $product_code.$account_initials2.$org_acc_num_format['account_format'] : FALSE;
        }
    }
}
    public function new_loan_acc_no() {

          $product_code = "";
          $product_id = $this->CI->input->post('loan_product_id');
      
            if(isset($product_id ) && is_numeric($product_id)){
              
                $client_loan_product_code= $this->CI->loan_product_model->get("loan_product.id=".$product_id );
                $product_code = $client_loan_product_code[0]['product_code'];
                //echo json_encode($client_loan_product_code);die();
              
            }
        $org_loan_num_format = $this->CI->organisation_format_model->get_format_types(FALSE, ['loan_format','loan_format_initials']); 
        if (empty($org_loan_num_format)) {
            return FALSE;
        } 
        else {
              $account_initials=strtoupper($org_loan_num_format['loan_format_initials']);
                $ac_initials_len=strlen($account_initials);
                 if($ac_initials_len==1){
                  $account_format=substr($org_loan_num_format['loan_format'],0, $ac_initials_len);
                  $account_initials2=$account_initials.$account_format; 
                  }
                else{
                   $account_initials2=substr($account_initials,0,$ac_initials_len);
                  }
            
            $last_loan_acc_no = $this->CI->num_format_model->get_last_no2("client_loan", "loan_no",$account_initials2,$product_code,$product_id);
            
            if(!empty($last_loan_acc_no)){
                   if(is_numeric($last_loan_acc_no[0])){
                    $client_loan_product_code_len = strlen($product_code);
                    $last_client_loan_acc_no_generated =substr($last_loan_acc_no,$client_loan_product_code_len);
                    
                     ++$last_client_loan_acc_no_generated;
                    return $product_code.$last_client_loan_acc_no_generated;     
                   }
                  return ++$last_loan_acc_no;
                 
              } 
            else {
               return !empty($org_loan_num_format['loan_format']) ?  $product_code.$account_initials2.$org_loan_num_format['loan_format'] : FALSE;
            }
         }
    }
    public function new_group_loan_no() {
        $org_loan_num_format = $this->CI->organisation_format_model->get_format_types(FALSE, ['group_loan_format','group_loan_format_initials']);
        if (empty($org_loan_num_format['group_loan_format'])) {
            return FALSE;
        } else {
            $account_initials=strtoupper($org_loan_num_format['group_loan_format_initials']);
            $ac_initials_len=strlen($account_initials);
             if($ac_initials_len==1){
            $group_loan_format=substr($org_loan_num_format['group_loan_format'],0,$ac_initials_len);
            $account_initials2=$account_initials.$group_loan_format;      
            }
            else{
           
            $account_initials2=substr($account_initials,0,$ac_initials_len);
              }
            $last_group_loan_no = $this->CI->num_format_model->get_last_no2("group_loan", "group_loan_no",$account_initials2);
            if (!empty($last_group_loan_no)) {
                return ++$last_group_loan_no;
            } else {
                return  $account_initials2.$org_loan_num_format['group_loan_format'];
            }
        }
    }

    public function new_share_acc_no() {

          $product_code = "";
          $product_id = $this->CI->input->post('share_issuance_id');

            if(isset($product_id) && is_numeric($product_id)){
          
            $share_product_code= $this->CI->share_issuance_model->get('share_issuance.id='.$product_id);
            $product_code = $share_product_code[0]['issuance_code'];
          
        }

        $org_share_acc_num_format = $this->CI->organisation_format_model->get_format_types(FALSE, ['share_format','share_format_initials']);
        if (empty($org_share_acc_num_format['share_format'])) {
            return FALSE;
        } else {
                $account_initials=strtoupper($org_share_acc_num_format['share_format_initials']);
                $ac_initials_len=strlen($account_initials);
                if($ac_initials_len==1){
                $share_format=substr($org_share_acc_num_format['share_format'],0,$ac_initials_len);
                $account_initials2=$account_initials.$share_format;      
                }
            else{
                $account_initials2=substr($account_initials,0,$ac_initials_len);
              }
            $last_share_acc_no = $this->CI->num_format_model->get_last_no2("share_account", "share_account_no",$account_initials2,$product_code,$product_id);
            if (!empty($last_share_acc_no)) {
                // checking if the first index of the code is an integer
                if(is_numeric($last_share_acc_no[0])){
                $issuance_code_len = strlen($product_code);
                $last_share_acc_no_generated =substr($last_share_acc_no,$issuance_code_len);
                ++$last_share_acc_no_generated;
                 
                return $product_code.$last_share_acc_no_generated;
              }
              return ++$last_share_acc_no;
            }
              else {
                return $product_code.$account_initials2.$org_share_acc_num_format['share_format'];
            }
        }
    }

    public function new_share_app_no() {
        $org_share_app_num_format = $this->CI->organisation_format_model->get_format_types(FALSE, ['share_application_no']);
        if (empty($org_share_app_num_format['share_application_no'])) {
            return FALSE;
        } else {
            $last_share_app_no = $this->CI->num_format_model->get_last_no("share_applications", "share_application_no");
            if (!empty($last_share_app_no)) {
                return ++$last_share_app_no;
            } else {
                return $org_share_app_num_format['share_application_no'];
            }
        }
    }

    public function new_client_no(){
        $org_member_num_format = $this->CI->organisation_format_model->get_format_types(FALSE, ['client_format','client_format_initials']);
        if (empty($org_member_num_format['client_format'])) {
            return FALSE;
        } else {

            $account_initials=strtoupper($org_member_num_format['client_format_initials']);
            $ac_initials_len=strlen($account_initials);
             if($ac_initials_len==1){
              $client_format=substr($org_member_num_format['client_format'],0,$ac_initials_len);
               $account_initials2=$account_initials.$client_format;      
            }
            else{
           
            $account_initials2=substr($account_initials,0,$ac_initials_len);
              }
            
               $last_member_no = $this->CI->num_format_model->get_last_no2("member", "client_no",$account_initials2);
            if(!empty($last_member_no)){
                return ++$last_member_no;
            } else {
                return  $account_initials2.$org_member_num_format['client_format'];
            }
        }
    }

    public function new_group_no() {
        $org_group_no_format = $this->CI->organisation_format_model->get_format_types(FALSE, ['group_format']);
        if (empty($org_group_no_format['group_format'])) {
            return FALSE;
        } else {
            $last_group_no = $this->CI->num_format_model->get_last_no("group", "group_no");
            if (!empty($last_group_no)) {
                return ++$last_group_no;
            } else {
                return $org_group_no_format['group_format'];
            }
        }
    }

    public function new_staff_no() {
        $org_staff_no_format = $this->CI->organisation_format_model->get_format_types(FALSE, ['staff_format','staff_format_initials']);
        if (empty($org_staff_no_format['staff_format'])) {
            return FALSE;
        } else {

            $account_initials=strtoupper($org_staff_no_format['staff_format_initials']);
            $ac_initials_len=strlen($account_initials);
             if($ac_initials_len==1){
              $staff_format=substr($org_staff_no_format['staff_format'],0,$ac_initials_len);
               $account_initials2=$account_initials.$staff_format;      
            }
            else{
           
            $account_initials2=substr($account_initials,0,$ac_initials_len);
              }
            
            $last_staff_no = $this->CI->num_format_model->get_last_no2("staff", "staff_no",$account_initials2);
            if (!empty($last_staff_no)) {
                return ++$last_staff_no;
            } else {
                return $account_initials2.$org_staff_no_format['staff_format'];
            }
        }
    }

}
