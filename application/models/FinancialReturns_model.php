<?php

class FinancialReturns_model extends CI_Model
{
  public function __construct()
  {
    $this->load->database();
    $this->active = $this->get_current_fiscal_year(1);
  }

  public function get_current_fiscal_year($status_id)
  {
    $this->db->select('id, start_date, end_date');
    $this->db->from('fiscal_year');
    if ($status_id === FALSE) {
      //$this->db->where("fiscal_year.organisation_id=", $org_id);
      $query = $this->db->get();
      return $query->result_array();
    } else {
      //$this->db->where("fiscal_year.organisation_id=", $org_id);
      $this->db->where("fiscal_year.status_id=", $status_id);
      $query = $this->db->get();
      return $query->row_array();
    }
  }

  public function get_category_subcategories($category=FALSE)
  {
    
      $this->db->select('id, sub_cat_name');
      $this->db->from("fms_account_sub_categories");
      if (is_numeric($category)) {
        $this->db->where("category_id", $category);
      }
      $query = $this->db->get();
      return $query->result_array();
    
  }

  public function get_accounts_details($id) {
    $this->db->select("id,account_name");
    $this->db->from("accounts_chart");
    $this->db->where("sub_category_id = ", $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function current_year_quarter_data() {
    $current_quarter = $this->determine_current_quarter();
    $quarterly = $this->get_quarters_in_year($current_quarter);
    $end_date = $quarterly['end_date'];
    $start_date = $quarterly['start_date'];
    $previous_quarter =  $this->accounts_sum_array($start_date, $end_date);
    return $this->getTotals($previous_quarter);
  }

  public function previous_quarter_data() {
    $current_quarter = $this->determine_current_quarter();
    if($current_quarter == 1){
      $prev_year = date("Y",strtotime("-1 year"));
      $quarterly = $this->get_quarters_in_year(4,$prev_year);
    }else {
      $quarterly = $this->get_quarters_in_year($current_quarter - 1);
    }
    $end_date = $quarterly['end_date'];
    $start_date = $quarterly['start_date'];
    $previous_quarter =  $this->accounts_sum_array($start_date, $end_date);
    return $this->get_totals_only($this->getTotals($previous_quarter));
  }

  public function last_year_correspondig_quarter_data() {
    $current_quarter = $this->determine_current_quarter();
    $prev_year = date("Y",strtotime("-1 year"));
    $quarterly = $this->get_quarters_in_year($current_quarter,$prev_year);
    $end_date = $quarterly['end_date'];
    $start_date = $quarterly['start_date'];
    $previous_quarter =  $this->accounts_sum_array($start_date, $end_date);
    return $this->get_totals_only($this->getTotals($previous_quarter));
    // return $this->getTotals($previous_quarter);
  }

  public function combined_data() {
    $response = [];
    $names = [];
    $dataArray1 = $this->previous_quarter_data();
    $dataArray2 = $this->last_year_correspondig_quarter_data();

    foreach ($dataArray1 as $key => $value) {
      foreach ($value as $ke => $val) {
        if(in_array($ke,$names)){
          foreach($response as $k => $v){
            foreach ($v as $k1 => $v1) {
              if($ke == $k1){
                array_push($response[$k][$k1],$val);
              }
            }
          }
        }else {
          array_push($response,[$ke=>[$val]]);
          array_push($names, $ke);
        }
        
      }
    }

    foreach ($dataArray2 as $key2 => $value2) {
      foreach ($value2 as $ke2 => $val2) {
        if(in_array($ke,$names)){
          foreach($response as $k2 => $v2){
            (function () use (&$response, $v2,$ke2,$k2,$val2) {
              foreach ($v2 as $k12 => $v12) {
                if($ke2 == $k12){
                  array_push($response[$k2][$k12],$val2);
                }
              }

            })();
          }
        }else {
          array_push($response,[$ke2=>[$val2]]);
          array_push($names, $ke2);
        }
        
      }
    }
    return $response; 

  }

  public function getTotals($quarterly_data) {
    $response = [];
    $sub_categories = [];
    if($quarterly_data){
      foreach ($quarterly_data as $value) {
        foreach ($value as $key => $val) {
          $single_category_array = [];
          foreach ($val as $v) {
            foreach ($v as $k => $v2) {
              if (in_array($key, $sub_categories)) {
                (function () use (&$single_category_array, $v2,$k, $key) {
                  $i = 0;
                  foreach ($single_category_array as $ke => $data) {
                    $append_data = [$k => abs($v2[0]['debit_sum'] - $v2[0]['credit_sum'])]; //TODO: fix
                    array_push($single_category_array[$ke][$key], $append_data);
                  }
                })();
              } else {
                array_push($single_category_array, [$key => [[$k =>  abs($v2[0]['debit_sum'] - $v2[0]['credit_sum'])]]]); //TODO: fix
                array_push($sub_categories, $key);

              }
            }
          }
          array_push($response, $single_category_array);
        }
      }
    }
    return ["response"=>$response,"titles" => $sub_categories];
  }

  public function accounts_sum_array($start_date,$end_date) {
    $sub_categories = $this->get_category_subcategories();
    $response = [];
    foreach ($sub_categories as $key => $value) {
      $resp = [];
      $data = $this->get_accounts_details($value['id']);
      foreach ($data as $val) {
        $id = $this->get_accounts_sums($val['id'], $start_date,$end_date);
        array_push($resp, [$val['account_name'] => $id]);
      }

      array_push($response, [$value['sub_cat_name'] => $resp]);
    }
    return $response;
  }

  public function get_accounts_sums($account_id,$start_date=FALSE, $end_date=FALSE) {
    if ($start_date) {
      $date_range_filter = "(jtl.transaction_date >= '" . $start_date . "' AND jtl.transaction_date <= '" . $end_date . "')";
      $this->db->where($date_range_filter);
    }

    $this->db->select('IFNULL(ABS(SUM(IFNULL(credit_amount,0)-IFNULL(debit_amount,0))),0) amount, IFNULL(SUM(IFNULL(credit_amount,0)),0) as credit_sum,IFNULL(SUM(IFNULL(debit_amount,0)),0) as debit_sum');
    $this->db->from("fms_journal_transaction_line jtl");
    $this->db->join("fms_journal_transaction jt", "jt.id=jtl.journal_transaction_id");
    $this->db->join('accounts_chart ac', "ac.id=jtl.account_id");
    $this->db->join("fms_account_sub_categories acc_sub", "ac.sub_category_id=acc_sub.id");
    $this->db->join("fms_account_categories acc_cat", "acc_sub.category_id=acc_cat.id");
    $this->db->where("jtl.status_id =", 1);
    $this->db->where("jt.status_id =", 1);
    $this->db->where("ac.organisation_id = ", $_SESSION["organisation_id"]);
    $this->db->where("ac.id = ", $account_id);

    if ($this->input->post('print') != 1) {
      $this->db->where("jt.journal_type_id !=26");
    }
    $query = $this->db->get();
    return $query->result_array();

  }


  public function get_quarters_in_year($no,$year=FALSE) {

    $y = $year ? $year: "Y";

    switch($no){
      case 1 :
        $start_date = date($y."-00-01");
        break;
      case 2 :
        $start_date = date($y."-03-01");
        break;
      case 3 :
        $start_date = date($y."-06-01");
        break;
      case 4 :
        $start_date = date($y."-09-01");
        break;
    }
    $end_date = date($y."-m-t",strtotime("+ 3 months",strtotime($start_date)));
    return ["start_date" => $start_date, "end_date" => $end_date];
  }



  public function get_totals_only($dataArray) {
    $response = [];
    $names = [];
    foreach ($dataArray['response'] as $key => $value) {
      foreach ($value as $ke => $val) {
        $total_parent = [];
        foreach ($val as $k => $v) {
          foreach ($v as $data) {
            if(count($data)>0){
              array_push($response,$data);
            }
          }
        }
        if(count($total_parent) > 0){
          array_push($response, $total_parent);
        }
      }
    
    }
    return $response;
  }

  public function determine_current_quarter(){
    $months = $this->months_names_in_a_year();
    $current_month = date("F");
    $quarters_in_year = array_chunk($months,3);
    foreach ($quarters_in_year as $key => $value) {

      if(in_array($current_month,$value)){
        return $key+ 1; 
      }
    }
  }

  private function months_names_in_a_year() {
    $months = [];
    for ($m=1; $m<=12; $m++) {
      array_push($months,date('F', mktime(0,0,0,$m, 1, date('Y'))));
    }
    return $months;
  }

  public function get_years_gone_on_fiscal_year()
  {
    $years = range(date("Y"), date("Y", strtotime($this->active['start_date'])));

    return $years;
  }
}
