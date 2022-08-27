
    <div class="col-lg-12">
      <div class="table-responsive">
         <table class="table table-sm table-bordered" width="100%">
            <tbody >
                <tr style="background-color: #1c84c6; color: #fff;">
                     <td colspan="2">
                          <span style="font-size: 18px;color: #fff;font-weight: bold;"><center>Income Statement  &nbsp;&nbsp;&nbsp;<?php echo date('d-M-Y',strtotime($end_date));?></center></span>
                    </td>
                </tr>
                <tr><td style="font-size: 16px;font-weight: bold;">
                    INCOME
                    </td>
                </tr>
           <?php foreach ($income as $key => $value) { ?>
            <?php if (($value['amount']!=0)&&($value['cat']==1)) {  ?>
               <tr  style="background-color: #fafafc;">
                    <td style ="padding-left:40px; font-weight:bold; font-size: 14px;" ><?php echo $value['account_name']; ?></td>
                    <td><div class="no-margins"><span  style ="font-weight:bold; font-size: 14px;" ><?php echo number_format(round($value['amount'],2)); ?></span></div> 
                </td>
                </tr>
           <?php } if (($value['amount']!=0)&&($value['cat']==0)) {  ?>
               <tr  >
                    <td style ="padding-left:80px; font-size: 14px;"><?php echo $value['account_name']; ?></td>
                    <td><span class="no-margins"><span style="font-size: 14px;" class="text-primary"><?php echo number_format(round($value['amount'],2)); ?></span></span> 
                </td>
                </tr>
            <?php } } ?>

                <tr style="background-color: #fafcdc;">
                <th>Gross Profit </th>
                    <th >  <?php echo number_format(round($profitloss_sums['total_income'],2)); ?></span>  </th> 
                </tr>
           
            <tr><td style="font-size: 16px;font-weight: bold;">
        EXPENSE
            </td></tr>
             <?php foreach ($expenses as $key => $value) { ?>
            <?php if (($value['amount']!=0)&&($value['cat']==1)) {  ?>
               <tr  style="background-color: #fafafc;">
                    <td style ="padding-left:40px; font-weight:bold; font-size: 14px;" ><?php echo $value['account_name']; ?></td>
                    <td><div class="no-margins"><span  style ="font-weight:bold; font-size: 14px;" ><?php echo number_format(round($value['amount'],2)); ?></span></div> 
                </td>
                </tr>
           <?php } if (($value['amount']!=0)&&($value['cat']==0)) {  ?>
               <tr  >
                    <td style ="padding-left:80px; font-size: 14px;"><?php echo $value['account_name']; ?></td>
                    <td><span class="no-margins"><span style="font-size: 14px;" class="text-primary"><?php echo number_format(round($value['amount'],2)); ?></span></span> 
                </td>
                </tr>
            <?php } } ?>
            
              <tr style="background-color: #fafcdc;">
                <th>Total expense </th>
                    <th> <span > <?php echo number_format(round($profitloss_sums['total_expense'],2)); ?></span>  </th> 
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr class="table-primary" style ="padding-left:40px;font-weight: bold;" > 
                 <td >Profit/Loss</td>
                    <th>
                    <span ><?php echo number_format(round($profitloss_sums['net_profit_loss'],2)); ?></span>
                    </th>
                </tr>
                
                </tbody>
        </table>
      </div>
</div>
