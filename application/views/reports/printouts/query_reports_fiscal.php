<h6 class="text-success"><center><u>PERFORMANCE REPORT</u></center></h6>
<hr>
<?php if(($fiscal_2==0)&&($fiscal_3==0)){
        $colspan=0;
        $colspan1=3;
    }elseif (($fiscal_2!=0)&&($fiscal_3==0)) {
        $colspan=2;
        $colspan1=4;
    }elseif (($fiscal_2==0)&&($fiscal_3!=0)) {
        $colspan=2;
        $colspan1=4;
    }else {
       $colspan1=5;
       $colspan=3;
    }
 ?>
<div class="col-lg-12">
  <div class="table-responsive">
         <table class="table table-sm table-bordered" width="100%">
            <tbody>
                
               <tr style="background-color: #1c84c6;">
                    <td colspan="<?php echo $colspan1; ?>"> <span style="font-size: 14px;color: #fff;font-weight: bold;"><center>Performance Comparision</center></span> </td>
                </tr>
                <tr style="background-color: #1c84c6;">
                    <td colspan="2" rowspan="2" > <span style="font-size: 16px;color: #fff;font-weight: bold;"><center>Details</center></span> </td>
                    <td colspan="<?php echo $colspan; ?>"><span style="font-size: 12px;color: #fff;font-weight: bold;"><center>Financial Year</center></span></td>
                </tr>
                <tr style="background-color: #1c84c6;" >
                    <td><span style="font-size: 13px;color: #fff;font-weight: bold;"><center ><?php echo date("M-Y",strtotime($start_date)); ?> / <?php echo date("M-Y",strtotime($end_date)); ?></center></span ></td>
                    <?php if($fiscal_2!=0){ ?>
                    <td ><span style="font-size: 13px;color: #fff;font-weight: bold;"><center><?php echo date("M-Y",strtotime($start_date1)); ?> / <?php echo date("M-Y",strtotime($end_date1)); ?><center></span></td>
                    <?php } if($fiscal_3!=0){ ?>
                    <td ><span style="font-size: 13px;color: #fff;font-weight: bold;"><center ><?php echo date("M-Y",strtotime($start_date2)); ?> / <?php echo date("M-Y",strtotime($end_date2)); ?></center></span></td>
                    <?php } ?>
               </tr> 
               <?php if($period_savings==1){ ?>
                <tr  >
                    <td rowspan="4" ><div> <span style="font-weight: bold;">Savings</span> </div> </td>
                    <td style="font-size:13px;" >Savings Accounts</td>
                    <td><div class="no-margins"><span  class="text-default" style="font-size:13px;"><?php echo $general_data['savings_count']; ?></span></div> </td>
                    <?php if($fiscal_2!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo $general_data1['savings_count']; ?></span></div> </td>
                    <?php } if($fiscal_3!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo $general_data2['savings_count']; ?></span></div> </td>
                    <?php } ?>
               </tr> 
                <tr >
                    <td style="font-size:13px;">Total Deposits</td>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($general_data['savings']['deposits'],2)); ?></span></div> </td>
                    <?php if($fiscal_2!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;"><?php echo number_format(round($general_data1['savings']['deposits'],2)); ?></span></div> </td>
                <?php } if($fiscal_3!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;"><?php echo number_format(round($general_data2['savings']['deposits'],2)); ?></span></div> </td>
                <?php } ?>
               </tr>
                <tr >
                <td style="font-size:13px;" >Total Withdraws  </td>
                   <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($general_data['savings']['withdraws'],2)); ?></span></div></td>
                   <?php if($fiscal_2!=0){ ?>
                   <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($general_data1['savings']['withdraws'],2)); ?></span></div></td>
               <?php } if($fiscal_3!=0){ ?>
                   <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($general_data2['savings']['withdraws'],2)); ?></span></div></td>
               <?php } ?>
                </tr>
                 <tr >
                <td style="font-size:13px;">Total Savings </td>
                   <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($general_data['savings']['cash_bal'],2)); ?></span></div></td>
                   <?php if($fiscal_2!=0){ ?>
                   <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($general_data1['savings']['cash_bal'],2)); ?></span></div></td>
                   <?php } if($fiscal_3!=0){ ?>
                   <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($general_data2['savings']['cash_bal'],2)); ?></span></div></td>
               <?php } ?>
               </tr>
              <?php } if($loans==1){ ?>
                <tr >
                    <td rowspan="6" > <div> <span style="font-weight: bold;">Loans</span> </div> </td>
                    <td style="font-size:13px;">Principal Disbursed</td>
                    <td><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data['principal_disbursed'],2)); ?></span></div></td>
                    <?php if($fiscal_2!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data1['principal_disbursed'],2)); ?></span></div></td>
                <?php } if($fiscal_3!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data2['principal_disbursed'],2)); ?></span></div></td>
                <?php } ?>
                <tr >
                    <td style="font-size:13px;">Principal Collected</td>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;"><?php echo number_format(round($loan_data['amount_paid']['already_principal_amount'],2)); ?></span></div> </td>
                    <?php if($fiscal_2!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;"><?php echo number_format(round($loan_data1['amount_paid']['already_principal_amount'],2)); ?></span></div> </td>
                <?php } if($fiscal_3!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data2['amount_paid']['already_principal_amount'],2)); ?></span></div> </td>
                <?php } ?>
                </tr>
                <tr >
                    <td style="font-size:13px;">Gross loan portfolio</td>
                    <td><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data['gross_loan_portfolio'],2)); ?></span></div> </td>
                    <?php if($fiscal_2!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data1['gross_loan_portfolio'],2)); ?></span></div> </td>
                <?php } if($fiscal_3!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data2['gross_loan_portfolio'],2)); ?></span></div> </td>
                    <?php } ?>
                </tr>
                <tr>
                    <td style="font-size:13px;">Projected Loan Interest</td>
                    <td><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data['projected_intrest_earnings'],2)); ?></span></div> </td>
                    <?php if($fiscal_2!=0){ ?>
                    <td d><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data1['projected_intrest_earnings'],2)); ?></span></div> </td>
                    <?php } if($fiscal_3!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data2['projected_intrest_earnings'],2)); ?></span></div> </td>
                <?php } ?>
                </tr>
                <tr>
                    <td style="font-size:13px;">Interest in suspense</td>
                    <td><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data['intrest_in_suspense'],2)); ?></span></div> </td>
                    <?php if($fiscal_2!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data1['intrest_in_suspense'],2)); ?></span></div> </td>
                    <?php } if($fiscal_3!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data2['intrest_in_suspense'],2)); ?></span></div> </td>
                <?php } ?>
                </tr>
                <tr>
                    <td style="font-size:13px;">Unpaid Penalty</td>
                    <td  ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data['penalty_total']['penalty_total'],2)); ?></span></div></td> 
                    <?php if($fiscal_2!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data1['penalty_total']['penalty_total'],2)); ?></span></div></td> 
                <?php } if($fiscal_3!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data2['penalty_total']['penalty_total'],2)); ?></span></div></td> 
                <?php } ?>
                </tr>
                <?php } if($shares==1){ ?>
                <tr  >
                    <td rowspan="3" ><div> <span style="font-weight: bold;">Share Capital</span> </div> </td>
                    <td style="font-size:13px;">Number of Shareholders</td>
                    <td><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format($general_data['share_accounts']); ?></span></div> </td>
                    <?php if($fiscal_2!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format($general_data1['share_accounts']); ?></span></div> </td>
                <?php } if($fiscal_3!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format($general_data2['share_accounts']); ?></span></div> </td>
                <?php } ?>
               </tr>
                <tr  >
                <td style="font-size:13px;">Total Number of Shares @ <b ><?php echo number_format(round($general_data['price_per_share'],2)); ?></b> each </td>
                   <td><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($general_data['total_shares'],2)); ?></span></div></td>
                   <?php if($fiscal_2!=0){ ?>
                   <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;"><?php echo number_format(round($general_data1['total_shares'],2)); ?></span></div></td>
                   <?php } if($fiscal_3!=0){ ?>
                   <td><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($general_data2['total_shares'],2)); ?></span></div></td>
               <?php } ?>
                </tr>
            
                <tr >
                <td style="font-size:13px;">Total Share Capital</td>
                <td style="font-size:13px;"><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($general_data['share_report']['amount'],2)); ?></span></div></td> 
                    <?php if($fiscal_2!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($general_data1['share_report']['amount'],2)); ?></span></div></td> 
                <?php  } if($fiscal_3!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($general_data2['share_report']['amount'],2)); ?></span></div></td> 
                <?php } ?>
                </tr>
            <?php } if($membership==1){ ?>
                <tr >
                    <td rowspan="2" ><div> <span style="font-weight: bold;">Membership</span> </div></td>
                    <td style="font-size:13px;">Male</td>
                    <td><div class="no-margins"><span class="text-default" style="font-size:13px;"><?php echo number_format($general_data['male_members']); ?></span></div></td>
                    <?php if($fiscal_2!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format($general_data1['male_members']); ?></span></div></td> 
                    <?php } if($fiscal_3!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format($general_data2['male_members']); ?></span></div></td>  
                    <?php } ?>
                </tr>
                <tr >
                <td style="font-size:13px;">Female</td>
                   <td><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format($general_data['female_members']); ?></span></div></td> 
                   <?php if($fiscal_2!=0){ ?>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format($general_data1['female_members']); ?></span></div></td> 
                    <?php } if($fiscal_3!=0){ ?>
                     <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format($general_data2['male_members']); ?></span></div></td>
                     <?php } ?>  
                </tr>
                <?php } ?> 
                </tbody>
        </table>
      </div>
</div>
