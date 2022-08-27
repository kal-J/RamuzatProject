<h6 class="text-success"><center><u>PERFORMANCE REPORT</u></center></h6>
<hr>
<div class="col-lg-12">
    <div  class="table-responsive">
         <table class="table table-sm table-bordered" width="100%">
            <tbody>
                <tr style="background-color: #1c84c6;">
                    <td colspan="2" > <span style="font-size: 13px;color: #fff;font-weight: bold;">Details </span> </td>
                    <?php if($period==1) { ?>
                    <td > <span style="font-size: 13px;color: #fff;font-weight: bold;"><center> <b > As At &nbsp;&nbsp;</b> <?php echo date("d-M-Y",strtotime($end_date)); ?> </center></span> </td>
                    <?php } else{ ?>
                    <td > <span style="font-size: 13px;color: #fff;font-weight: bold;"><center><b style="color: #000;"> From &nbsp;&nbsp;</b> &nbsp;<?php echo date("D-M-Y",strtotime($start_date)); ?>  &nbsp;<b style="color: #000;"> To &nbsp;&nbsp;</b> &nbsp; <?php echo date("d-M-Y",strtotime($end_date)); ?> </center></span> </td>
                <?php } ?>
                </td>
                <?php if($period_savings==1) { ?>
                <tr >
                    <td rowspan="4" > <div><span style="font-weight: bold;">Savings</span></div> </td>
                    <td style="font-size:13px;" >Savings Accounts</td>
                    <td><div class="no-margins"><span  class="text-default" style="font-size:13px;"><?php echo $general_data['savings_count']; ?></span></div> 
                </td>
               </tr> 
                <tr >
                    <td style="font-size:13px;" >Total Deposits</td>
                    <td><div class="no-margins"><span  class="text-default" style="font-size:13px;"><?php echo number_format(round($general_data['savings']['deposits'],2)); ?></span></div> 
                </td>
               </tr>
                <tr >
                <td style="font-size:13px;" >Total Withdraws  </td>
                   <td><div class="no-margins"><span class="text-default" style="font-size:13px;" ><?php echo number_format(round($general_data['savings']['withdraws'],2)); ?></span></div>  
                </tr>
                 <tr >
                <td style="font-size:13px;">Total Savings </td>
                   <td><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($general_data['savings']['cash_bal'],2)); ?></span></div>  
                </td>
               </tr>
              <?php } if($loans==1) { ?>
                <tr >
                    <td rowspan="6" > <div><span style="font-weight: bold;">Loans</span> </div> </td>
                    <td style="font-size:13px;">Principal Disbursed</td>
                    <td><div class="no-margins"><span  class="text-default" style="font-size:13px;"><?php echo number_format(round($loan_data['principal_disbursed'],2)); ?></span></div> 
                </td>
                <tr>
                    <td style="font-size:13px;">Principal Collected</td>
                    <td ><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data['amount_paid']['already_principal_amount'],2)); ?></span></div> 
                </td>
                </tr>
                <tr>
                    <td style="font-size:13px;">Gross loan portfolio</td>
                    <td><div class="no-margins"><span  class="text-default" style="font-size:13px;"><?php echo number_format(round($loan_data['gross_loan_portfolio'],2)); ?></span></div> 
                </td>
                </tr>
                <tr >
                    <td style="font-size:13px;">Projected Loan Interest</td>
                    <td><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data['projected_intrest_earnings'],2)); ?></span></div> 
                </td>
                </tr>
                <tr >
                    <td style="font-size:13px;">Interest in suspense</td>
                    <td><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($loan_data['intrest_in_suspense'],2)); ?></span></div> 
                </td>
                </tr>
                <tr >
                    <td style="font-size:13px;">Unpaid Penalty</td>
                    <td  ><div class="no-margins"><span  class="text-default" style="font-size:13px;"><?php echo number_format(round($loan_data['penalty_total']['penalty_total'],2)); ?></span></div> 
                </td>
                </tr>
               <?php } if($shares==1) { ?>
                <tr>
                    <td rowspan="<?php echo $general_data['rowSpan_value']; ?>" ><div><span style="font-weight: bold;">Share Capital</span> </div> </td>
                    <td style="font-size:13px;">Number of Shareholders</td>
                    <td><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format($general_data['share_accounts']); ?></span></div> 
                </td>
               </tr>
               <?php foreach ($general_data['share_report'] as $key => $value) {
                 ?>
                <tr>
                <td style="font-size:13px;">Number of <?php echo $value['issuance_name']; ?> Shares @ <b ><?php echo number_format(round($value['price_per_share'],2)); ?></b> each </td>
                   <td><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round(($value['amount']/$value['price_per_share']),2)); ?></span></div>  
                </tr>
                <tr >
                    <td style="font-size:13px;">Total Amount</td>
                    <td><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format(round($value['amount'],2)); ?></span></div> 
                </td>
                </tr>

                <?php } } if($membership==1) { ?>
                <tr >
                    <td rowspan="2" ><div> <span style="font-weight: bold;">Membership</span> </div></td>
                    <td style="font-size:13px;">Male</td>
                    <td><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format($general_data['male_members']); ?></span></div> 
                </td>
                </tr>
                <tr >
                <td style="font-size:13px;"> Female</td>
                   <td><div class="no-margins"><span  class="text-default" style="font-size:13px;" ><?php echo number_format($general_data['female_members']); ?></span></div>  
                </tr>
                <?php } ?>
           
                </tbody>
        </table>
      </div>
</div>
