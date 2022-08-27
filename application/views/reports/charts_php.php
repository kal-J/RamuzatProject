<?php

$member = new Member();
$dashboard = new Dashboard();
$loan_account_obj = new LoanAccount();
$expense = new Expenses();
$income = new Income();
$loan = new Loans();
$deposit_account = new DepositAccount();
$deposit_account_transaction_obj = new DepositAccountTransaction();
/* $share = new Shares(); */

//set the respective variables to be received from the calling page
$figures = $tables = $percents = array();
//No of members
//1 in this period
$figures['members'] = $member->noOfMembers(" (`dateAdded` BETWEEN " . $start_date . " AND " . $end_date . ") AND active=1"); //
//before this period
$members_b4 = $member->noOfMembers(" `dateAdded` < " . $start_date . " AND active=1"); //(
$percents['members'] = ($figures['members'] > 0 && $members_b4 > 0) ? round(($figures['members'] / $members_b4) * 100, 2) : ($figures['members'] > 0 ? 100 : 0);


//Total amount of paid subscriptions
//1 in this period
$figures['total_shares'] = $dashboard->getSumOfShares("(`datePaid` BETWEEN " . $start_date . " AND " . $end_date . ")");
//before this period
$total_shares_b4 = $dashboard->getSumOfShares("(`datePaid` < " . $start_date . ")");
//percentage increase/decrease
$percents['shares_percent'] = ($total_shares_b4 > 0 && $figures['total_shares'] > 0) ? round(($figures['total_shares'] / $total_shares_b4) * 100, 2) : ($figures['total_shares'] > 0 ? 100 : 0);

//Total amount of paid subscriptions
//1 in this period
$figures['total_scptions'] = $dashboard->getSumOfSubscriptions("(`datePaid` BETWEEN " . $start_date . " AND " . $end_date . ")");
//before this period
$total_scptions_b4 = $dashboard->getSumOfSubscriptions("(`datePaid` < " . $start_date . ")");
//percentage increase/decrease
$percents['scptions_percent'] = ($total_scptions_b4 > 0 && $figures['total_scptions'] > 0) ? round(($figures['total_scptions'] / $total_scptions_b4) * 100, 2) : ($figures['total_scptions'] > 0 ? 100 : 0);

//Total loan portfolio
//1 in this period
$figures['loan_portfolio'] = $dashboard->getSumOfLoans("`disbursementDate` BETWEEN " . $start_date . " AND " . $end_date);
//before this period
$loan_portfolio_b4 = $dashboard->getSumOfLoans("(`disbursementDate` < " . $start_date . ")");
//percentage increase/decrease
$percents['loan_portfolio'] = ($loan_portfolio_b4 > 0 && $figures['loan_portfolio'] > 0) ? round(($figures['loan_portfolio'] / $loan_portfolio_b4) * 100, 2) : ($figures['loan_portfolio'] > 0 ? 100 : 0);

//Total expected interest
//1 in this period
$figures['loan_interest'] = $dashboard->getSumOfInterest("`disbursementDate` BETWEEN " . $start_date . " AND " . $end_date);
//before this period
$loan_portfolio_b4 = $dashboard->getSumOfInterest("(`disbursementDate` < " . $start_date . ")");
//percentage increase/decrease
$percents['loan_interest'] = ($loan_portfolio_b4 > 0 && $figures['loan_interest'] > 0) ? round(($figures['loan_interest'] / $loan_portfolio_b4) * 100, 2) : ($figures['loan_interest'] > 0 ? 100 : 0);

//Total loan penalties
//1 in this period
$figures['loan_penalty'] = $dashboard->getSumOfLoans("`dateAdded` BETWEEN " . $start_date . " AND " . $end_date);
//before this period
$loan_penalty_b4 = $dashboard->getSumOfLoans("(`dateAdded` < " . $start_date . ")");
//percentage increase/decrease
$percents['loan_penalty'] = ($loan_penalty_b4 > 0 && $figures['loan_penalty'] > 0) ? round(($figures['loan_penalty'] / $loan_penalty_b4) * 100, 2) : ($figures['loan_penalty'] > 0 ? 100 : 0);

//Total loan payments
//1 in this period
$figures['loan_payments'] = $dashboard->getSumOfLoanRepayments("(`transactionDate` BETWEEN " . $start_date . " AND " . $end_date . ")"); // AND `status`=3
//before this period
$loan_payments_b4 = $dashboard->getSumOfLoanRepayments("(`transactionDate` < " . $start_date . ")"); // AND `status`=3
//percentage increase/decrease
$percents['loan_payments'] = ($loan_payments_b4 > 0 && $figures['loan_payments'] > 0) ? round(($figures['loan_payments'] / $loan_payments_b4) * 100, 2) : ($figures['loan_payments'] > 0 ? 100 : 0);

//Total pending loans
//1 in this period
$figures['pending_loans'] = $dashboard->getCountOfLoans("(`applicationDate` BETWEEN " . $start_date . " AND " . $end_date . ") AND `status`=3");
//before this period
$pending_loans_b4 = $dashboard->getCountOfLoans("(`applicationDate` BETWEEN " . $start_date . " AND " . $end_date . ") AND `status`=3");
//percentage increase/decrease
$percents['pending_loans'] = ($pending_loans_b4 > 0 && $figures['pending_loans'] > 0) ? round(($figures['pending_loans'] / $pending_loans_b4) * 100, 2) : ($figures['pending_loans'] > 0 ? 100 : 0);

//Total rejected loans
//1 in this period
$figures['rejected_loans'] = $dashboard->getCountOfLoans("(`approvalDate` BETWEEN " . $start_date . " AND " . $end_date . ") AND `status`=11");
//before this period
$rejected_loans_b4 = $dashboard->getCountOfLoans("(`approvalDate` BETWEEN " . $start_date . " AND " . $end_date . ") AND `status`=11");
//percentage increase/decrease
$percents['rejected_loans'] = ($rejected_loans_b4 > 0 && $figures['rejected_loans'] > 0) ? round(($figures['rejected_loans'] / $rejected_loans_b4) * 100, 2) : ($figures['rejected_loans'] > 0 ? 100 : 0);

//Total approveded loans
//1 in this period
$figures['approved_loans'] = $dashboard->getCountOfLoans("(`approvalDate` BETWEEN " . $start_date . " AND " . $end_date . ") AND `status`=4");
//before this period
$approved_loans_b4 = $dashboard->getCountOfLoans("(`approvalDate` BETWEEN " . $start_date . " AND " . $end_date . ") AND `status`=4");
//percentage increase/decrease
$percents['approved_loans'] = ($approved_loans_b4 > 0 && $figures['approved_loans'] > 0) ? round(($figures['approved_loans'] / $approved_loans_b4) * 100, 2) : ($figures['approved_loans'] > 0 ? 100 : 0);

//Total disbursed loans
//1 in this period
$figures['disbursed_loans'] = $dashboard->getCountOfLoans("(`disbursementDate` BETWEEN " . $start_date . " AND " . $end_date . ") AND `status`=5");
//before this period
$disbursed_loans_b4 = $dashboard->getCountOfLoans("(`disbursementDate` BETWEEN " . $start_date . " AND " . $end_date . ") AND `status`=5");
//percentage increase/decrease
$percents['disbursed_loans'] = ($disbursed_loans_b4 > 0 && $figures['disbursed_loans'] > 0) ? round(($figures['disbursed_loans'] / $disbursed_loans_b4) * 100, 2) : ($figures['disbursed_loans'] > 0 ? 100 : 0);

//Withdraws
//1 in this period
$withdraws = ($deposit_account_transaction_obj->getMoneySum("2 AND (`dateCreated` BETWEEN " . $start_date . " AND " . $end_date . ")"));
//Deposits
$deposits = $deposit_account_transaction_obj->getMoneySum("1 AND (`dateCreated` BETWEEN " . $start_date . " AND " . $end_date . ")");
//before this period 

$deposits_b4 = $deposit_account_transaction_obj->getMoneySum("1 AND `dateCreated` < " . $start_date);
//before this period  
$withdraws_b4 = ($deposit_account_transaction_obj->getMoneySum("2 AND `dateCreated` < " . $start_date));

//percentage increase/decrease
$figures['savings'] = ($deposits - $withdraws);
$b4 = $deposits_b4 - $withdraws_b4;
//percentage increase/decrease
//we divide the current savings by the previous in order to get the percentage inc/decrease
$percents['savings'] = ($figures['savings'] > 0 && $b4 > 0) ? round((($figures['savings']) / $b4) * 100, 2) : ($figures['savings'] > 0 ? 100 : 0);

//Income
$tables['income'] = $income->findOtherIncome("`dateAdded` BETWEEN " . $start_date . " AND " . $end_date, "amount DESC", 10);
//Savings
$tables['savings'] = $deposit_account->findRecentDeposits($start_date, $end_date, 10);
//$tables['savings'] = $depositAccount->findRecentDeposits($start_date, $end_date, 10);
//Expenses"
$tables['expenses'] = $expense->findAllExpenses("`expenseDate` BETWEEN " . $start_date . " AND " . $end_date, 10);

$tables['loan_products'] = $loan_account_obj->findLoans($start_date, $end_date, 10);

//line and barchart
$barchart = getGraphData($start_date, $end_date);
if ($barchart) {
    $_result['graph_data'] = $barchart;
}

//pie chart
$piechart = getPieChartData($start_date, $end_date);
if ($piechart) {
    $_result['pie_chart_data'] = $piechart['chart_data'];
    $figures['total_product_sales'] = $piechart['total_product_sales'];
}

$_result['figures'] = $figures;
$_result['percents'] = $percents;
$_result['tables'] = $tables; //

function getGraphData($start_date, $end_date) {
    $graph_data['title']['text'] = "Total product sales, " . date('j M, y', $start_date) . " - " . date('j M, y', $end_date);
    $graph_data['yAxis']['title']['text'] = "UGX";

    $period_dates = [];
    if (($graph_periods = get_graph_periods($end_date, $start_date)) === TRUE) {
        $graph_data['xAxis'] = $graph_periods['xAxis'];
        $period_dates = $graph_periods['period_dates'];
    }
    if (!empty($period_dates)) {
        $loan_products = $loanProduct->findAll();
        if (!empty($loan_products)) {
            foreach ($loan_products as $product) {
                $datasets = array();
                $datasets['name'] = $product['productName'];

                foreach ($period_dates as $period_date) {
                    $between = "BETWEEN " . $period_date['start'] . " AND " . $period_date['end'] . ")";
                    $datasets['data'][] = $dashboard->getSumOfLoans($between . " AND  `loanProductId`=" . $product['id']);
                }
                $graph_data['datasets'][] = $datasets;
            }
        }
    }
    if (!empty($graph_data)) {
        return $graph_data;
    } else
        return false;
}

function get_graph_periods($end_date, $start_date) {

    $begin = DateTime::createFromFormat('Y-m-d', $start_date);
    $end = DateTime::createFromFormat('Y-m-d', $end_date);

    //$interval = DateInterval::createFromDateString('1 day');
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($begin, $interval, $end);

    //arrays with data for the past i days/months
    $graph_period_dates = $categories = array("date_range"=>$begin->format("j M, y") . " - " . $end->format('j M, y'));

    $days = iterator_count($period);
    $period_dates = iterator_to_array($period);

    $period_dates1 = [];
    //if days are 7 or less
    if ($days == 0 || $days < 13) {
        $period_dates1 = $period_dates;
        foreach ($period_dates as $period_date) {
            $categories[] = $period_date->format("D, j/n");
        }
    } elseif ($days > 12 && $days < 85) {
        /* split the days into weeks
         * generate an array holding the start and end dates of the given period
         */
        $period_dates1 = [];
        $index = 0;
        $period_dates1[$index]['start'] = $start_date;
        if (date('N', $start_date) == 7) {
            $period_dates1[$index++]['end'] = $start_date;
        }
        for ($i = $index; $i < count($period_dates); $i++) {
            $period_date = $period_dates[$i];
            if ($period_date->format('N') == 1) {
                $period_dates1[$index]['start'] = $period_date->getTimestamp();
            }
            if ($period_date->format('N') == 7) {
                $period_dates1[$index++]['end'] = $period_date->getTimestamp();
            }
        }
        $period_dates1[$index]['end'] = $end_date;
        if (date('N', $end_date) == 1) {
            $period_dates1[$index]['start'] = $end_date;
        }
        foreach ($period_dates1 as $week) {
            $categories[] = date('j/M', $week['start']) . "-" . date('j/M', $week['end']);
        }
    } elseif ($days > 84) {
        /* split the days into months
         * generate an array holding the start and end dates of the given period */
        $period_dates1 = [];
        $index = 0;
        $period_dates1[$index]['start'] = $start_date;
        if (date('j', $start_date) == date('t', $start_date)) {
            $period_dates1[$index++]['end'] = $start_date;
        }
        for ($i = $index; $i < count($period_dates); $i++) {
            $period_date = $period_dates[$i];
            if ($period_date->format('j') == 1) {
                $period_dates1[$index]['start'] = $period_date->format('Y-m-d');
            }
            if ($period_date->format('j') == $period_date->format('t')) {
                $period_dates1[$index++]['end'] = $period_date->getTimestamp();
            }
        }
        $period_dates1[$index]['end'] = $end_date;
        if (date('j', $end_date) == date('t', $end_date)) {
            $period_dates1[$index]['start'] = $end_date;
            $period_dates1[$index]['end'] = $end_date;
        }
        foreach ($period_dates1 as $month) {
            $categories[] = date('M/Y', $month['end']);
        }
    }
    if (!empty($graph_period_dates)) {
        $graph_period_dates['period_dates'] = $period_dates1;
        $graph_period_dates['xAxis']['categories'] = $categories;
        return $graph_period_dates;
    } else
        return false;
}

function getPieChartData($start_date, $end_date) {
    $dashboard = new Dashboard();
    $loanProduct = new LoanProduct();
    $loan_products = $loanProduct->findAll();

    $pie_chart_data = array();
    $products_sum = 0;

    $between = "BETWEEN " . $start_date . " AND " . $end_date . ")";
    $pie_chart_data['series']['name'] = 'Loan Products';
    if ($loan_products) {
        foreach ($loan_products as $product) {
            $products_sum += $total_amount = $dashboard->getSumOfLoans("(`disbursementDate` " . $between . " AND `loanProductId`=" . $product['id']);
            $pie_chart_data['series']['data'][] = array('name' => $product['productName'], 'y' => $total_amount);
        }//

        $pie_chart_data['title']['text'] = "Total product sales " . date('j M, y', $start_date) . " - " . date('j M, y', $end_date);
    }
    if ($loan_products) {
        $pie_chart = array('total_product_sales' => $products_sum, 'chart_data' => $pie_chart_data);
        return $pie_chart;
    } else
        return false;
}

function getGraphProps() {
    $datasets = array();
    $datasets['backgroundColor'] = "rgba(" . rand(0, 255) . ", " . rand(0, 255) . ", " . rand(0, 255) . ", " . (rand(0, 100) / 100) . ")";
    $datasets['borderColor'] = "rgba(" . rand(0, 255) . ", " . rand(0, 255) . ", " . rand(0, 255) . ", " . (rand(0, 100) / 100) . ")";
    $datasets['pointBackgroundColor'] = "rgba(" . rand(0, 255) . ", " . rand(0, 255) . ", " . rand(0, 255) . ", " . (rand(0, 100) / 100) . ")";
    $datasets['pointHoverBackgroundColor'] = "#fff";
    $datasets['pointHoverBorderColor'] = "rgba(" . rand(0, 255) . ", " . rand(0, 255) . ", " . rand(0, 255) . ", " . (rand(0, 100) / 100) . ")";
    $datasets['pointBorderWidth'] = 1;
    return $datasets;
}
