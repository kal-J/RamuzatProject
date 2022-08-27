
<h6 class="text-success"><center><u> GENERAL LOAN REPORT
<?php if ($start_date !='' && $start_date != 'Invalid date') {?>
    FROM <?php echo $start_date; ?> TO <?php echo $end_date; ?> 
<?php }else{?>
AS OF <?php echo $end_date; ?> 

<?php } ?>
</u></center></h6>
<style type="text/css">
    table {
        border-collapse: collapse;
    }
    tbody {
        font-size: 14px;
        font-weight: normal
    }
</style>
<!-- style="background-color: #3057d6; color: #fff; font-size: 14px; font-weight: bold;" -->
<div  class="table-responsive">
 <table class="table table-sm table-bordered" width="100%">
    <tbody>
        <tr>
            <td>PARTICULARS</td>
            <td>LOAN PRODUCT</td>
            <td>NUMBERS/AMOUNT</td>
            <td>PERCENTAGE(%)</td>
        </tr>
        <?php if($loan_statuses=='true'){ ?>
        <tr>
            <td colspan="4"><h6 style="color: #1C84C9;font-weight: normal; font-size: 14px;">LOAN STATUSES</h6></td>
        </tr>
        
        <tr>
            <td rowspan="<?php echo $span_size+1?>">Applications<br> (Partial, Pending & Approved)</td>
            
        <?php $count=0; foreach ($statuses['application'] as $key => $value) { if($count ==1){break;}?>
            <td><?php echo $value['product_name']; ?></td>  
            <td><?php echo number_format($value['total'],2);  ?></td>  
            <td><?php echo round(($value['total']/( ($totals['statuses']['application'] >0)?$totals['statuses']['application']:1 ))*100,2).'%' ?></td>  
        <?php $count++; } ?>
        </tr> 
        <?php foreach ($statuses['application'] as $key => $value) { if($count ==1){$count++; continue;}?>
        <tr>
            <td><?php echo $value['product_name']; ?></td>  
            <td><?php echo number_format($value['total'],2);  ?></td>  
            <td><?php echo round(($value['total']/( ($totals['statuses']['application'] >0)?$totals['statuses']['application']:1 ))*100,2).'%' ?></td>  
        </tr>
        <?php $count++; } ?>
        <tr style="border: 1px solid black;">
            <td style="border: 1px solid black;"><h6>Total</h6></td>  
            <td style="border: 1px solid black;"><h6><?php echo number_format($totals['statuses']['application'],2)?></h6></td>  
            <td style="border: 1px solid black;"><h6><?php echo ($totals['statuses']['application'] >0)?'100%':'0%' ?></h6></td>  
        </tr>

        <tr>
            <td rowspan="<?php echo $span_size+1?>">Active Loans<br> (Active & Locked)</td>
        <?php $count=0; foreach ($statuses['active'] as $key => $value) { if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['total'],2) ?></td>  
            <td><?php echo round(($value['total']/( ($totals['statuses']['active'] >0)?$totals['statuses']['active']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>
        </tr>
        <?php foreach ($statuses['active'] as $key => $value) { if($count ==1){$count++; continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['total'],2) ?></td>  
            <td><?php echo round(($value['total']/( ($totals['statuses']['active'] >0)?$totals['statuses']['active']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr style="border: 1px solid black; ">
            <td style="border: 1px solid black;"><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['statuses']['active'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['statuses']['active'] >0)?'100%':'0%' ?></h6></td>  
        </tr>

    
        <tr>
            <td rowspan="<?php echo $span_size+1?>">Closed Loans<br> (Paid-off, Refinaced, Obligations-met)</td>
        <?php $count=0; foreach ($statuses['closed'] as $key => $value) { if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['total'],2) ?></td>  
            <td><?php echo round(($value['total']/( ($totals['statuses']['closed'] >0)?$totals['statuses']['closed']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>
        </tr>
        <?php foreach ($statuses['closed'] as $key => $value) { if($count ==1){$count++; continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['total'],2) ?></td>  
            <td><?php echo round(($value['total']/( ($totals['statuses']['closed'] >0)?$totals['statuses']['closed']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr style="border: 1px solid black; ">
            <td style="border: 1px solid black;"><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['statuses']['closed'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['statuses']['closed'] >0)?'100%':'0%' ?></h6></td>  
        </tr>

        <tr>
            <td rowspan="<?php echo $span_size+1?>">Loans In Arrears <br>(Past Lona Tenure)</td>
        <?php $count=0; foreach ($statuses['inarrears'] as $key => $value) { if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['total'],2) ?></td>  
            <td><?php echo round(($value['total']/( ($totals['statuses']['inarrears'] >0)?$totals['statuses']['inarrears']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>
            
        </tr>
        <?php foreach ($statuses['inarrears'] as $key => $value) { if($count ==1){$count++; continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['total'],2) ?></td>  
            <td><?php echo round(($value['total']/( ($totals['statuses']['inarrears'] >0)?$totals['statuses']['inarrears']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr style="border: 1px solid black; ">
            <td style="border: 1px solid black;"><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['statuses']['inarrears'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['statuses']['inarrears'] >0)?'100%':'0%' ?></h6></td>  
        </tr>


        <tr>
            <td rowspan="<?php echo $span_size+1?>">Written off Loans</td>
        <?php $count=0; foreach ($statuses['written_off'] as $key => $value) { if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['total'],2) ?></td>  
            <td><?php echo round(($value['total']/( ($totals['statuses']['written_off'] >0)?$totals['statuses']['written_off']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>
            
        </tr>
        <?php foreach ($statuses['written_off'] as $key => $value) { if($count ==1){$count++; continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['total'],2) ?></td>  
            <td><?php echo round(($value['total']/( ($totals['statuses']['written_off'] >0)?$totals['statuses']['written_off']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr style="border: 1px solid black; ">
            <td style="border: 1px solid black;"><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['statuses']['written_off'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['statuses']['written_off'] >0)?'100%':'0%' ?></h6></td>  
        </tr>
        

        <?php } if($amounts=='true'){ ?>
        <tr>
            <td colspan="4"> <h6 style="color: #1C84C9;font-weight: bold; font-size: 14px;"> LOAN AMOUNTS</h6></td>
        </tr>
        <tr>
            <td rowspan="<?php echo $span_size+1?>">Disbursed Amount</td>  
        <?php $count=0; foreach ($loan_amount['disbursed'] as $key => $value) { if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_amount']['disbursed'] >0)?$totals['loan_amount']['disbursed']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>                              
        </tr>
        <?php foreach ($loan_amount['disbursed'] as $key => $value) { if($count ==1){$count++; continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_amount']['disbursed'] >0)?$totals['loan_amount']['disbursed']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr style="border: 1px solid black; ">
            <td style="border: 1px solid black;"><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['loan_amount']['disbursed'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['loan_amount']['disbursed'] >0)?'100%':'0%' ?></h6></td>  
        </tr>

        <tr>
            <td rowspan="<?php echo $span_size+1?>">Collected Amount</td>
        <?php $count=0; foreach ($loan_amount['collected'] as $key => $value) { if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_amount']['collected'] >0)?$totals['loan_amount']['collected']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>
            
        </tr>
        <?php foreach ($loan_amount['collected'] as $key => $value) { if($count ==1){$count++; continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_amount']['collected'] >0)?$totals['loan_amount']['collected']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr style="border: 1px solid black; ">
            <td style="border: 1px solid black;"><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['loan_amount']['collected'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['loan_amount']['collected'] >0)?'100%':'0%' ?></h6></td>  
        </tr>
        <tr>
            <td rowspan="<?php echo $span_size+1?>">Outstanding Amount</td>
        <?php $count=0; foreach ($loan_amount['outstanding'] as $key => $value) { if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_amount']['outstanding'] >0)?$totals['loan_amount']['outstanding']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>
            
        </tr>
        <?php foreach ($loan_amount['outstanding'] as $key => $value) { if($count ==1){$count++; continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_amount']['outstanding'] >0)?$totals['loan_amount']['outstanding']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr style="border: 1px solid black; ">
            <td style="border: 1px solid black;"><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['loan_amount']['outstanding'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['loan_amount']['outstanding'] >0)?'100%':'0%' ?></h6></td>  
        </tr>
        <tr>
            <td rowspan="<?php echo $span_size+1?>">Loan Interest</td>
        <?php $count=0; foreach ($loan_amount['interest'] as $key => $value) { if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_amount']['interest'] >0)?$totals['loan_amount']['interest']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>
            
        </tr>
        <?php foreach ($loan_amount['interest'] as $key => $value) { if($count ==1){$count++; continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_amount']['interest'] >0)?$totals['loan_amount']['interest']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr style="border: 1px solid black; ">
            <td style="border: 1px solid black;"><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['loan_amount']['interest'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['loan_amount']['interest'] >0)?'100%':'0%' ?></h6></td>  
        </tr>
        <tr>
            <td rowspan="<?php echo $span_size+1?>">Loan Penalty</td>
        <?php $count=0; foreach ($loan_amount['penalty'] as $key => $value) { if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_amount']['penalty'] >0)?$totals['loan_amount']['penalty']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>
            
        </tr>
        <?php foreach ($loan_amount['penalty'] as $key => $value) { if($count ==1){$count++; continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_amount']['penalty'] >0)?$totals['loan_amount']['penalty']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr style="border: 1px solid black; ">
            <td style="border: 1px solid black;"><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['loan_amount']['penalty'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['loan_amount']['penalty'] >0)?'100%':'0%' ?></h6></td>  
        </tr>
        <tr>
            <td rowspan="<?php echo $span_size+1?>">Written off Amount</td>
        <?php $count=0; foreach ($loan_amount['written_off'] as $key => $value) { if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_amount']['written_off'] >0)?$totals['loan_amount']['written_off']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>
            
        </tr>
        <?php foreach ($loan_amount['written_off'] as $key => $value) { if($count ==1){$count++; continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_amount']['written_off'] >0)?$totals['loan_amount']['written_off']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr style="border: 1px solid black; ">
            <td style="border: 1px solid black;"><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['loan_amount']['written_off'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['loan_amount']['written_off'] >0)?'100%':'0%' ?></h6></td>  
        </tr>
        <tr>
            <td rowspan="<?php echo $span_size+1?>">Average loan balance</td>
        <?php $count=0; foreach ($loan_amount['average_loan_balance'] as $key => $value) { if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_amount']['average_loan_balance'] >0)?$totals['loan_amount']['average_loan_balance']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>
            
        </tr> 
        <?php foreach ($loan_amount['average_loan_balance'] as $key => $value) { if($count ==1){$count++; continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_amount']['average_loan_balance'] >0)?$totals['loan_amount']['average_loan_balance']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr style="border: 1px solid black; ">
            <td style="border: 1px solid black;"><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['loan_amount']['average_loan_balance'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['loan_amount']['average_loan_balance'] >0)?'100%':'0%' ?></h6></td>  
        </tr>
        <tr>
            <td rowspan="<?php echo $span_size+1?>">Projected Loan Interest</td>
        <?php $count=0; foreach ($loan_amount['projected_interest_amount'] as $key => $value) { if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_amount']['projected_interest_amount'] >0)?$totals['loan_amount']['projected_interest_amount']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>
            
        </tr>
        <?php foreach ($loan_amount['projected_interest_amount'] as $key => $value) { if($count ==1){$count++; continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_amount']['projected_interest_amount'] >0)?$totals['loan_amount']['projected_interest_amount']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr style="border: 1px solid black; ">
            <td style="border: 1px solid black; "><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['loan_amount']['projected_interest_amount'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['loan_amount']['projected_interest_amount'] >0)?'100%':'0%' ?></h6></td>  
        </tr>
        
        <?php } if($portfolio=='true'){ ?>
        <tr>
            <td colspan="4"> <h6 style="color: #1C84C9;font-weight: bold; font-size: 14px;">LOAN PORTFOLIO</h6></td>
        </tr>
        <tr>
            <td rowspan="<?php echo $span_size+1?>">Portfolio pending approval</td>            
        <?php $count=0; foreach ($loan_portfolio['portfolio_pending_approval'] as $key => $value) { if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_portfolio']['portfolio_pending_approval'] >0)?$totals['loan_portfolio']['portfolio_pending_approval']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>
        </tr>
        <?php foreach ($loan_portfolio['portfolio_pending_approval'] as $key => $value) { if($count ==1){$count++; continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_portfolio']['portfolio_pending_approval'] >0)?$totals['loan_portfolio']['portfolio_pending_approval']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr style="border: 1px solid black; ">
            <td style="border: 1px solid black;"><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['loan_portfolio']['portfolio_pending_approval'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['loan_portfolio']['portfolio_pending_approval'] >0)?'100%':'0%' ?></h6></td>  
        </tr>

        <tr>
            <td rowspan="<?php echo $span_size+1?>">Gross loan portfolio</td>
        <?php $count=0; foreach ($loan_portfolio['gross_loan_portfolio'] as $key => $value) { if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_portfolio']['gross_loan_portfolio'] >0)?$totals['loan_portfolio']['gross_loan_portfolio']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>
            
        </tr>
        <?php foreach ($loan_portfolio['gross_loan_portfolio'] as $key => $value) { if($count ==1){$count++; continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['loan_portfolio']['gross_loan_portfolio'] >0)?$totals['loan_portfolio']['gross_loan_portfolio']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr style="border: 1px solid black; ">
            <td style="border: 1px solid black;"><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['loan_portfolio']['gross_loan_portfolio'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['loan_portfolio']['gross_loan_portfolio'] >0)?'100%':'0%' ?></h6></td>  
        </tr>
        

        <?php } if($indicators=='true'){ ?>

        <tr>
            <td colspan="4"><h6 style="color: #1C84C9;font-weight: bold; font-size: 14px;">RISK INDICATORS</h6></td>
        </tr>
        <tr>
            <td rowspan="<?php echo $span_size+1?>">Unpaid Penalty</td>             
        <?php $count=0; foreach ($risk_indicators['unpaid_penalty'] as $key => $value) { if($count ==1){break;} ?>
            
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['risk_indicators']['unpaid_penalty'] >0)?$totals['risk_indicators']['unpaid_penalty']:1 ))*100,2).'%'?></td>  
        
        <?php $count++;} ?>          
        </tr>
        <?php foreach ($risk_indicators['unpaid_penalty'] as $key => $value) { if($count ==1){$count++; continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['risk_indicators']['unpaid_penalty'] >0)?$totals['risk_indicators']['unpaid_penalty']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count ++;} ?>
        <tr>
            <td style="border: 1px solid black; "><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['risk_indicators']['unpaid_penalty'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['risk_indicators']['unpaid_penalty'] >0)?'100%':'0%' ?></h6></td>  
        </tr>


        <tr>
            <td rowspan="<?php echo $span_size+1?>">Value at Risk</td>

        <?php $count=0; foreach ($risk_indicators['value_at_risk'] as $key => $value) {if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['risk_indicators']['value_at_risk'] >0)?$totals['risk_indicators']['value_at_risk']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>
            
        </tr>
        <?php foreach ($risk_indicators['value_at_risk'] as $key => $value) {if($count ==1){$count++;continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['risk_indicators']['value_at_risk'] >0)?$totals['risk_indicators']['value_at_risk']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr>
            <td style="border: 1px solid black; "><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['risk_indicators']['value_at_risk'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['risk_indicators']['value_at_risk'] >0)?'100%':'0%' ?></h6></td>  
        </tr>


        <tr>
            <td rowspan="<?php echo $span_size+1?>">Portifolio at Risk</td>
            <?php $count=0; foreach ($risk_indicators['portfolio_at_risk'] as $key => $value) {if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['risk_indicators']['portfolio_at_risk'] >0)?$totals['risk_indicators']['portfolio_at_risk']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>
        </tr>
        <?php foreach ($risk_indicators['portfolio_at_risk'] as $key => $value) {if($count ==1){$count++;continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['risk_indicators']['portfolio_at_risk'] >0)?$totals['risk_indicators']['portfolio_at_risk']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr >
            <td style="border: 1px solid black; "><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['risk_indicators']['portfolio_at_risk'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['risk_indicators']['portfolio_at_risk'] >0)?'100%':'0%' ?></h6></td>  
        </tr>

        <tr>
            <td rowspan="<?php echo $span_size+1?>">Interest in Suspense </td>
        <?php $count=0; foreach ($risk_indicators['intrest_in_suspense'] as $key => $value) {if($count ==1){break;}?>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['risk_indicators']['intrest_in_suspense'] >0)?$totals['risk_indicators']['intrest_in_suspense']:1 ))*100,2).'%'?></td>  
        <?php $count++; } ?>
            
        </tr>
        <?php foreach ($risk_indicators['intrest_in_suspense'] as $key => $value) {if($count ==1){$count++;continue;}?>
        <tr>
            <td><?php echo $value['product_name']?></td>  
            <td><?php echo number_format($value['amount'],2) ?></td>  
            <td><?php echo round(($value['amount']/( ($totals['risk_indicators']['intrest_in_suspense'] >0)?$totals['risk_indicators']['intrest_in_suspense']:1 ))*100,2).'%'?></td>  
        </tr>
        <?php $count++; } ?>
        <tr style="border: 1px solid black; ">
            <td style="border: 1px solid black; "><h6>Total</h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo number_format($totals['risk_indicators']['intrest_in_suspense'],2)?></h6></td>  
            <td style="border: 1px solid black; "><h6><?php echo ($totals['risk_indicators']['intrest_in_suspense'] >0)?'100%':'0%' ?></h6></td>  
        </tr>
        <?php } ?>

    </tbody>
</table>
</div>
