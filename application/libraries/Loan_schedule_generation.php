<?php

/**
 * Description of Loan_schedule_generation
 *
 * @author Eric
 */
if (!defined('BASEPATH')){ exit("No direct script access allowed"); }
class Loan_schedule_generation {
	protected $CI;
	public function __construct() {
		// Assign the CodeIgniter super-object
		$this->CI = & get_instance();
		$this->CI->load->model('non_working_days_model', '', TRUE);
		$this->CI->load->model('holiday_model', '', TRUE);

        $this->response['payment_summation']['interest_amount']=0;
        $this->response['payment_summation']['principal_amount']=0;
        $this->response['payment_summation']['paid_principal']=0;
        $this->response['payment_summation']['payment_date']=0;
        date_default_timezone_set('Africa/Kampala');
	}

	public function generate($required_data){
		if ($required_data['product_type_id'] ==2) {//for dynamic Term loan
            $this->response=$this->reducing_balance_calculation($required_data);
        } elseif($required_data['product_type_id'] ==3) { // for Declining balance calculation
            $this->response=$this->declining_balance_calculation($required_data);
        }else{//for Fixed Term loan
            $this->response=$this->flat_rate_calculation($required_data);
        }

        return $this->response;
	}
	

    private function declining_balance_calculation($support_data){
        $x=0;$y= 0;
        $p=$support_data['p']; $n=$support_data['n']; 
        $i=$support_data['i'];  $r=$support_data['r'];
        $installment_counter=$support_data['installment_counter'];
        $current_installment=$support_data['current_installment'];
        $payment_date=$support_data['payment_date']; $payment_date1=$support_data['payment_date1'];
        $schedule_date=$support_data['schedule_date'];
        $n -=$installment_counter;
        //paid installment, this includes both the interest+paid principal
        //$EMI =($i*$p)/ (1- pow((1+$i),-$n)); 
        $declining_loan_amount = $p;
        $principal_amount_per_installment = $n ? ($p/$n) : 0;

        for ($y; $y <$n; $y++) { 
            if ($current_installment !=NULL && $installment_counter ==($current_installment-1) ) {
                $payment_date2 = $payment_date1;
                $this->response['payment_schedule'][$x]['payment_date']=$payment_date;
            }elseif ($this->CI->input->post('amount') !=NULL && $y ==0 ) {
                $payment_date2 = $payment_date1;
                $this->response['payment_schedule'][$x]['payment_date']=$payment_date;
            }else{
                $payment_date = strtotime($schedule_date, strtotime(date('Y-m-d', $payment_date)));
               if ($this->isWeekend($payment_date)) {
                    $payment_date=date('Y-m-d',$payment_date);
                    $payment_date= strtotime('+1 day', strtotime($payment_date));
                    while($this->isWeekend($payment_date)) {
                        $payment_date=date('Y-m-d',$payment_date);
                        $payment_date= strtotime('+1 day', strtotime($payment_date));
                    }
                    $this->response['payment_schedule'][$x]['payment_date']=$payment_date;
                    $payment_date2 = date('Y-m-d', $payment_date);
                }else{
                    $this->response['payment_schedule'][$x]['payment_date']=$payment_date;
                    $payment_date2  = date('Y-m-d', $payment_date);
                }
            }
            
            //installment number
            $this->response['payment_schedule'][$x]['installment_number']=$installment_counter+1;
            //interest amount paid per installment alone
            $this->response['payment_schedule'][$x]['interest_amount']=$interest_amount_per_installment=ceil( ($i*$declining_loan_amount) / 100 ) * 100;
            $this->response['payment_summation']['interest_amount']+=$interest_amount_per_installment;
            //principal amount payable
            $this->response['payment_schedule'][$x]['principal_amount']=$principal_amount=ceil( ($principal_amount_per_installment) / 100 ) * 100;
            $this->response['payment_summation']['principal_amount']+=$principal_amount;
            //total principal amount paid 
            $this->response['payment_schedule'][$x]['paid_principal']=$paid_principal=($principal_amount_per_installment + $interest_amount_per_installment);
            $this->response['payment_summation']['paid_principal']+=$paid_principal;
            //outstanding_balance
            $declining_loan_amount=$declining_loan_amount-$principal_amount;                            
            $x++;
            $installment_counter++;
        }

        $this->response['payment_date1']=$payment_date1;
        $this->response['payment_date2']= isset($payment_date2) ? $payment_date2 : '';
        return $this->response;
    }
    private function reducing_balance_calculation($support_data){
        $x=0;$y= 0;
        $p=$support_data['p']; $n=$support_data['n']; 
        $i=$support_data['i'];  $r=$support_data['r'];
        $installment_counter=$support_data['installment_counter'];
        $current_installment=$support_data['current_installment'];
        $payment_date=$support_data['payment_date']; $payment_date1=$support_data['payment_date1'];
        $schedule_date=$support_data['schedule_date'];
        $n -=$installment_counter;
        //paid installment, this includes both the interest+paid principal
        $EMI =($i*$p)/ (1- pow((1+$i),-$n));   
        for ($y; $y <$n; $y++) { 
            if ($current_installment !=NULL && $installment_counter ==($current_installment-1) ) {
                $payment_date2 = $payment_date1;
                $this->response['payment_schedule'][$x]['payment_date']=$payment_date;
            }elseif ($this->CI->input->post('amount') !=NULL && $y ==0 ) {
                $payment_date2 = $payment_date1;
                $this->response['payment_schedule'][$x]['payment_date']=$payment_date;
            }else{
                $payment_date = strtotime($schedule_date, strtotime(date('Y-m-d', $payment_date)));
               if ($this->isWeekend($payment_date)) {
                    $payment_date=date('Y-m-d',$payment_date);
                    $payment_date= strtotime('+1 day', strtotime($payment_date));
                    while($this->isWeekend($payment_date)) {
                        $payment_date=date('Y-m-d',$payment_date);
                        $payment_date= strtotime('+1 day', strtotime($payment_date));
                    }
                    $this->response['payment_schedule'][$x]['payment_date']=$payment_date;
                    $payment_date2 = date('Y-m-d', $payment_date);
                }else{
                    $this->response['payment_schedule'][$x]['payment_date']=$payment_date;
                    $payment_date2  = date('Y-m-d', $payment_date);
                }
            }
            
            //installment number
            $this->response['payment_schedule'][$x]['installment_number']=$installment_counter+1;
            //interest amount paid per installment alone
            $this->response['payment_schedule'][$x]['interest_amount']=$interest_amount_per_installment = ceil( ($i*$p) / 100 ) * 100;
            $this->response['payment_summation']['interest_amount']+=$interest_amount_per_installment;
            //principal amount payable
            $this->response['payment_schedule'][$x]['principal_amount']=$principal_amount = ceil( ($EMI-$interest_amount_per_installment) / 100 ) * 100;
            $this->response['payment_summation']['principal_amount']+=$principal_amount;
            //total principal amount paid 
            $this->response['payment_schedule'][$x]['paid_principal']=$paid_principal=$EMI;
            $this->response['payment_summation']['paid_principal']+=$paid_principal;
            //outstanding_balance
            $p=$p-$principal_amount;                            
            $x++;
            $installment_counter++;
        }

        $this->response['payment_date1']=$payment_date1;
        $this->response['payment_date2']=$payment_date2;
        return $this->response;
    }

    private function flat_rate_calculation($support_data){
        $x=0;$y= 0;
        $p=$support_data['p']; $n=$support_data['n'];
        $number_of_years=$support_data['number_of_years']; $r=$support_data['r'];
        $installment_counter=$support_data['installment_counter'];
        $current_installment=$support_data['current_installment'];
        $payment_date=$support_data['payment_date']; $payment_date1=$support_data['payment_date1'];
        $schedule_date=$support_data['schedule_date'];
        $n -=$installment_counter;

        for ($y; $y <$n; $y++) {
            if ($current_installment !=NULL && $installment_counter ==($current_installment-1) ) {
                $payment_date2 = $payment_date1;
                $this->response['payment_schedule'][$x]['payment_date']=$payment_date;
            }elseif ($this->CI->input->post('amount') !=NULL && $y ==0 ) {
                $payment_date2 = $payment_date1;
                $this->response['payment_schedule'][$x]['payment_date']=$payment_date;
            }else{
                $payment_date = strtotime($schedule_date, strtotime(date('Y-m-d', $payment_date)));
                if ($this->isWeekend($payment_date)) {
                    $payment_date = date('Y-m-d', $payment_date);
                    $payment_date = strtotime('+1 day', strtotime($payment_date));
                    while ($this->isWeekend($payment_date)) {
                        $payment_date = date('Y-m-d', $payment_date);
                        $payment_date = strtotime('+1 day', strtotime($payment_date));
                    }
                    $this->response['payment_schedule'][$x]['payment_date'] = $payment_date;
                    $payment_date2 = date('Y-m-d', $payment_date);
                } else {
                    $this->response['payment_schedule'][$x]['payment_date'] = $payment_date;
                    $payment_date2 = date('Y-m-d', $payment_date);
                }
            }
            //installment number
            $this->response['payment_schedule'][$x]['installment_number']=$installment_counter+1;
            $this->response['payment_schedule'][$x]['interest_amount']=$interest_amount_per_installment= ceil( (($p*$number_of_years*$r)/$n) / 100 ) * 100;
            $this->response['payment_summation']['interest_amount']+=$interest_amount_per_installment;
            $this->response['payment_schedule'][$x]['principal_amount']=$principal_amount= ceil( ($p/$n) / 100 ) * 100;
            $this->response['payment_summation']['principal_amount']+=$principal_amount;
            $this->response['payment_schedule'][$x]['paid_principal']=$paid_principal=($interest_amount_per_installment+$principal_amount);
             $this->response['payment_summation']['paid_principal']+=$paid_principal;
             $x++;
             $installment_counter++;
        }
        $extra = $this->response['payment_summation']['principal_amount'] -  $support_data['p'];
        $this->response['payment_schedule'][$x-1]['principal_amount'] = $this->response['payment_schedule'][$x-1]['principal_amount'] - $extra;
        $this->response['payment_schedule'][$x-1]['paid_principal'] = $this->response['payment_schedule'][$x-1]['paid_principal'] - $extra;
        $this->response['payment_summation']['principal_amount'] = $this->response['payment_summation']['principal_amount'] -$extra;
        $this->response['payment_summation']['paid_principal'] = $this->response['payment_summation']['paid_principal'] - $extra;
        $this->response['payment_date1']=$payment_date1;
        $this->response['payment_date2']=$payment_date2;
        return $this->response;
    }

    //checking whether the date is a weekend;
    private function isWeekend($date) {
        $non_working_days_data = $this->CI->non_working_days_model->get();
        $non_working_days=array();
        foreach ($non_working_days_data as $key => $value) {
            if ($value['sunday']==1) {
                $non_working_days[0]=0;
            }
            if ($value['monday']==1) {
                $non_working_days[1]=1;
            }
            if ($value['tuesday']==1) {
                $non_working_days[2]=2;
            }
            if ($value['wednesday']==1) {
               $non_working_days[3]=3;
            }
            if ($value['thursday']==1) {
               $non_working_days[4]=4;
            }
            if ($value['friday']==1) {
                $non_working_days[5]=5;
            }
            if ($value['saturday']==1) {
                $non_working_days[6]=6;
            }
        }
        $received_date = date('Y-m-d', $date);
        $weekDay = date('w', strtotime($received_date));
        if (!empty($non_working_days) && $non_working_days !='') {
           if (in_array($weekDay, $non_working_days)) {
                return true;
            }else {
                if ($this->isHoliday($date)) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            if ($this->isHoliday($date)) {
                return true;
            } else {
                return false;
            }
        }
    }

    //checking whether the date is a public holiday;
    private function isHoliday($date) {
        $holiday_data = $this->CI->holiday_model->get();
        if (empty($holiday_data)) {
            return false;
        } else {
            foreach ($holiday_data as $key => $value) {
                if ($value['every'] == 'Constant') {
                    $holidays[] = $value['holiday'];
                } else if ($value['every'] == 'Good_Friday') {
                    $the_year = date('Y', $date);
                    $the_easter_sunday = date('Y-m-d', easter_date($the_year));
                    $holidays[] = date("m-d", strtotime("-2 day", strtotime($the_easter_sunday)));
                } else if ($value['every'] == 'Easter_Sunday') {
                    $the_year = date('Y', $date);
                    $the_easter_sunday = date('Y-m-d', easter_date($the_year));
                    $holidays[] = date("m-d", strtotime($the_easter_sunday));
                } else if ($value['every'] == 'Easter_Monday') {
                    $the_year = date('Y', $date);
                    $the_easter_sunday = date('Y-m-d', easter_date($the_year));
                    $holidays[] = date("m-d", strtotime("+1 day", strtotime($the_easter_sunday)));
                } else {
                    $year = date('Y', $date);
                    $month = strtolower($value['month']);
                    $day = strtolower($value['day']);
                    $every = strtolower($value['every']);
                    $holidays[] = date('m-d', strtotime("$every $day of $month $year"));
                }
            }
            $date = date('m-d', $date);
            if (in_array($date, $holidays)) {
                return true;
            } else {
                return false;
            }
        }
    }

}
