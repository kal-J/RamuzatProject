<?php

class Revenue_performance_model extends CI_Model
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

  public function get_category_subcategories($category)
  {
    if (is_numeric($category)) {
      $this->db->select('id, sub_cat_name');
      $this->db->from("fms_account_sub_categories");
      $this->db->where("category_id", $category);
      $query = $this->db->get();
      return $query->result_array();
    }
  }



  public function get_category_sums($category = FALSE, $sub_category = FALSE, $where = FALSE)
  {

    $this->db->select('ABS(SUM(IFNULL(credit_amount,0)-IFNULL(debit_amount,0))) amount, SUM(IFNULL(credit_amount,0)) as credit_sum,SUM(IFNULL(debit_amount,0)) as debit_sum,acc_sub.*,acc_cat.normal_balance_side');
    $this->db->from("fms_journal_transaction_line jtl");
    $this->db->join("fms_journal_transaction jt", "jt.id=jtl.journal_transaction_id");
    $this->db->join('accounts_chart ac', "ac.id=jtl.account_id");
    $this->db->join("fms_account_sub_categories acc_sub", "ac.sub_category_id=acc_sub.id");
    $this->db->join("fms_account_categories acc_cat", "acc_sub.category_id=acc_cat.id");
    $this->db->where("jtl.status_id =", 1);
    $this->db->where("jt.status_id =", 1);
    $this->db->where("ac.organisation_id = ", $_SESSION["organisation_id"]);

    if ($this->input->post('print') != 1) {
      $this->db->where("jt.journal_type_id !=26");
    }
    if ($this->input->post("fisc_date_from") !== NULL) {
      $this->db->where("jtl.transaction_date >= ", $this->input->post("fisc_date_from"));
      $this->db->where("jtl.transaction_date <= ", $this->input->post("fisc_date_to"));
    } else {
      if ($where != FALSE) {
        $this->db->where($where);
      }
    }
    if ($category === FALSE) {
      $this->db->where($sub_category);
      $this->db->group_by("acc_sub.id");
      $query = $this->db->get();
      return $query->result_array();
    } else {
      if (is_numeric($category)) {
        $this->db->where("acc_cat.id = ", $category);
        $query = $this->db->get();
        return $query->row_array();
      } else if (is_numeric($sub_category)) {
        $this->db->where("acc_sub.id = ", $sub_category);
        $query = $this->db->get();
        //print_r($this->db->last_query());die();
        return $query->row_array();
      } else {
        $this->db->where_in("acc_cat.id", $category);
        $query = $this->db->get();
        return $query->result_array();
      }
    }
  }

  public function get_accounts_sums($filter, $start_date, $end_date)
  {
    $date_range_filter = "1";
    if ($start_date !== NULL) {
      $date_range_filter = "(jtl.transaction_date >= '" . $start_date . "' AND jtl.transaction_date <= '" . $end_date . "')";
    }
    $account_summations = "(SELECT `account_id`,SUM(IFNULL(debit_amount,0)) `debit_sum`, SUM(IFNULL(credit_amount,0)) `credit_sum` "
      . "FROM `fms_journal_transaction_line` `jtl` JOIN `fms_journal_transaction` `jt` ON `jt`.`id`=`journal_transaction_id` WHERE `jtl`.`status_id`=1 AND `jt`.`status_id`=1 AND $date_range_filter GROUP BY `account_id`) acc_sums";

    $this->db->select('ac.id,ac.account_code,ac.account_name, ac.description, ac.opening_balance, ac.opening_balance_date, '
      . 'pac.account_name p_account_name, normal_balance_side,debit_sum, credit_sum, (debit_sum-credit_sum) as debit_balance,(credit_sum-debit_sum) as credit_balance');
    $this->db->from('accounts_chart ac');
    $this->db->join("accounts_chart pac", "pac.id=ac.parent_account_id", "LEFT");
    $this->db->join("$account_summations", "ac.id=acc_sums.account_id", "LEFT");
    $this->db->join("account_sub_categories sc", "sc.id=ac.sub_category_id");
    $this->db->join("account_categories acat", "acat.id=sc.category_id");
    $this->db->order_by("ac.account_code", "asc");
    if ($this->input->post("organisation_id") !== NULL) {
      $this->db->where("organisation_id = ", $this->input->post("organisation_id"));
    }
    if ($filter === FALSE) {
      $query = $this->db->get();
      return $query->result_array();
    } else {
      if (is_numeric($filter)) {
        $this->db->where("ac.id", $filter);
        $query = $this->db->get();
        return $query->row_array();
      } else {
        $this->db->where($filter);
        $query = $this->db->get();
        return $query->result_array();
      }
    }
  }


  public function get_category_sums_data($category)
  {
    if ($this->input->post('print') != 1) {
      $journal_type_id = " jt.journal_type_id !=26 ";
    } else {
      $journal_type_id = " 1 ";
    }
    if (is_numeric($category)) {
      $sub_categories = $this->get_category_subcategories($category);

      if ($this->input->post("month") !== null && $this->input->post("month") != "All") {
        //get the selected year
        $year = $this->input->post("year");
        // $year = date('Y', strtotime($this->active['end_date']));
        $new_today = date('Y-m-d', strtotime($year . "-" . $this->input->post("month") . "-01"));
        $first_day_of_month = date('Y-m-01', strtotime($new_today));
        $today = date('Y-m-t', strtotime($new_today));
      } else {
        $today = date('Y-m-d');
        $first_day_of_month = date('Y-m-01', strtotime($today));
      }

      if ($this->input->post("month") == "All") {
        $response =  $this->format_data_for_all($sub_categories, $category);
        $resp = $this->format_data_for_all_ui_usage($response['data']);
        //  "table_months" => $months_to_display
        return ["data" => $resp['response'], "months" => $response['months'], "total" => $resp['total'], "table_months" => $response["table_months"], "formatted_total" => $resp['formatted_total']];
      }


      $response = array();
      $total = 0;

      if ($category == 4) {
        $sub_categories[] = ['id' => 2]; // For computing actual interest colected from the credits on the receivable
      }

      foreach ($sub_categories as $value) {
        $sub_category_id = $value['id'];
        $data = $this->get_accounts_sums("ac.sub_category_id=$sub_category_id", $first_day_of_month, $today);

        foreach ($data as $val) {

          if ($val['id'] != 7) { // Not receivable interest account
            if ($category == 5) {
              $income =  abs($val['debit_sum'] - $val['credit_sum']);
            } else {
              $income = abs($val['credit_sum'] - $val['debit_sum']);
            }
            $total += $income;
            array_push($response, ["name" => $val['account_name'], "income" => $income]);
          } elseif ($val['id'] == 7) { // Receivable Interest on loans
            // $income = abs($val['credit_sum']); // Actual interest income on loans is the credit sum on the receivable
            // $total += $income;
            // array_push($response, ["name" => "Interest on Loans", "income" => $income]);
          }
        }
      }

      //get the months passed year
      //TODO: Move this functionality to the helper utils
      $months = array();
      // if the selected year is not the current year return 12 months
      if ($this->input->post("year") != null && $this->input->post("year") != date("Y")) {
        for ($m = 1; $m <= 12; $m++) {
          $month = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
          array_push($months, $month);
        }
      } else {
        for ($m = 1; $m <= date('n'); $m++) {
          $month = date('F', mktime(0, 0, 0, $m, 1, date('Y', strtotime($this->active['start_date']))));
          array_push($months, $month);
        }
      }
      array_push($months, 'All');

      $current_month = date('F');

      // append the total to the response
      return ["data" => $response, "total" => $total, "months" => $months, "current_month" => $current_month];
    }
  }

  public function format_data_for_all($sub_categories, $category)
  {
    $response = array();
    $months = array();
    $total = 0;
    if ($category == 4) {
      $sub_categories[] = ['id' => 2]; // For computing actual interest colected from the credits on the receivable
    }

    // get all months passed including the current one
    if ($this->input->post("year") != null && $this->input->post("year") != date("Y")) {
      for ($m = 1; $m <= 12; $m++) {
        $month = date('F', mktime(0, 0, 0, $m, 1, $this->input->post("year")));
        $monthly_data = array();
        array_push($months, $month);
        // compute stats by month

        // $year = date('Y', strtotime($this->active['end_date']));
        $year = $this->input->post("year");
        $new_today = date('Y-m-d', strtotime($year . "-" . $month . "-01"));

        $first_day_of_month = date('Y-m-01', strtotime($new_today));

        $today = date('Y-m-t', strtotime($new_today));
        
        //get the values
        foreach ($sub_categories as $value) {
          $sub_category_id = $value['id'];
          $data = $this->get_accounts_sums("ac.sub_category_id=$sub_category_id", $first_day_of_month, $today);

          foreach ($data as $val) {
            if ($val['id'] != 7) { //  Not receivable interest account
              if ($category == 5) {
                $income =  abs($val['debit_sum'] - $val['credit_sum']);
              } else {
                $income = abs($val['credit_sum'] - $val['debit_sum']);
              }
              $total += $income;
              array_push($monthly_data, ["month" => $month, "name" => $val['account_name'], "income" => $income]);
            } elseif ($val['id'] == 7) { // Receivable Interest on loans
              // $income = abs($val['credit_sum']); // Actual interest income on loans is the credit sum on the receivable
              // $total += $income;
              // array_push($monthly_data, ["month" => $month, "name" => "Interest on Loans", "income" => $income]);
            }
          }
        }
        array_push($monthly_data, ["total" => $total]);

        array_push($response, $monthly_data);
      }
    } else {


      for ($m = 1; $m <= date('n'); $m++) {
        $month = date('F', mktime(0, 0, 0, $m, 1, date('Y', strtotime($this->active['start_date']))));
        $monthly_data = array();
        array_push($months, $month);
        // compute stats by month

        $year = date('Y', strtotime($this->active['end_date']));
        $new_today = date('Y-m-d', strtotime($year . "-" . $month . "-01"));

        $first_day_of_month = date('Y-m-01', strtotime($new_today));

        $today = date('Y-m-t', strtotime($new_today));

        //get the values
        foreach ($sub_categories as $value) {
          $sub_category_id = $value['id'];
          $data = $this->get_accounts_sums("ac.sub_category_id=$sub_category_id", $first_day_of_month, $today);

          foreach ($data as $val) {
            if ( $val['id'] != 7) { // Not receivable interest account
              if ($category == 5) {
                $income =  abs($val['debit_sum'] - $val['credit_sum']);
              } else {
                $income = abs($val['credit_sum'] - $val['debit_sum']);
              }
              $total += $income;
              array_push($monthly_data, ["month" => $month, "name" => $val['account_name'], "income" => $income]);
            } elseif ($val['id'] == 7) { // Receivable Interest on loans
              // $income = abs($val['credit_sum']); // Actual interest income on loans is the credit sum on the receivable
              // $total += $income;
              // array_push($monthly_data, ["month" => $month, "name" => "Interest on Loans", "income" => $income]);
            }
          }
        }
        array_push($monthly_data, ["total" => $total]);

        array_push($response, $monthly_data);
      }
    }
    $months_to_display = array_merge($months, ['Totals']);
    array_push($months, 'All');

    return ["data" => $response, "months" => $months, "table_months" => $months_to_display];
  }

  public function format_data_for_all_ui_usage($data)
  {
    $response = array();
    $names = array();
    $names_with_data = array();
    $total = array();
    $formated_total = array();

    foreach ($data as $key => $value) {
      $monthly_total = 0;
      foreach ($value as $k => $v) {
        if (isset($v['name'])) {
          if (in_array($v['name'], $names)) {
            (function () use (&$names_with_data, $v) {
              $i = 0;
              foreach ($names_with_data as $ke => $data) {
                if ($data['name'] == $v['name']) {
                  array_push($names_with_data[$ke]['data'], number_format($v['income']));
                  array_push($names_with_data[$ke]['unformated_data'], $v['income']);
                }
              }
            })();
          } else {
            array_push($names, $v['name']);
            array_push($names_with_data, ["name" => $v['name'], "data" => [number_format($v['income'])], "unformated_data" => [$v['income']]]);
          }
          //calculate the total
          $monthly_total += $v['income'];
        }
      }
      // append the montly_totals to the total array
      array_push($total, $monthly_total);
      array_push($formated_total, number_format($monthly_total));
    }

    return ["total" => $total, "formatted_total" => $formated_total, "response" => $names_with_data];
  }


  public function get_years_gone_on_fiscal_year()
  {
    $years = range(date("Y"), date("Y", strtotime($this->active['start_date'])));

    return $years;
  }
}
