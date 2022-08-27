
<div class="col-lg-12">
    <div  class="table-responsive">
         <table class="table table-sm table-bordered" width="100%">
            <tbody >
                <tr style="background-color: #1c84c6; color: #fff;">
                     <td colspan="2">
                         <span style="font-size: 16px;color: #fff;font-weight: bold;"><center>Statement of Financial Position &nbsp;&nbsp;&nbsp;-&nbsp;<?php echo date('d-M-Y',strtotime($end_date));?></center></span>
                    </td>
                </tr>
                <tr><td>
                    <span style="font-weight: bold;">Assets</span>
                    </td>
                </tr>
            
             <?php foreach ($assets as $key => $value) { ?>
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

                <tr class="table-primary">
                <th style="font-size: 15px;">Total Assets </th>
                    <th style="font-size: 15px;" > <span ><?php echo number_format(round($print_sums['total_assets'],2)); ?></span> </th> 
                </tr>
           
            <tr><td colspan="2">
        <span style="font-weight: bold;">Liabilities and Stockholder's Equity</span>
            </td></tr>
            <?php foreach ($liab_equity as $key => $value) { ?>
            <?php if (($value['amount']!=0)&&($value['cat']==1)) {  ?>
                <tr  style="background-color: #fafafc;">
                    <td style ="padding-left:40px; font-weight:bold; font-size: 14px;" ><?php echo $value['account_name']; ?></td>
                    <td><span class="no-margins"><span  style ="font-weight:bold; font-size: 14px;" ><?php echo number_format(round($value['amount'],2)); ?></span></span> 
                </td>
                </tr>
            <?php } if (($value['amount']!=0)&&($value['cat']==0)) {  ?>
               <tr  >
                    <td style ="padding-left:80px; font-size: 14px;"><?php echo $value['account_name']; ?></td>
                    <td><span class="no-margins"><span style="font-size: 14px;" class="text-primary"><?php echo number_format(round($value['amount'],2)); ?></span></span> 
                </td>
                </tr>
            <?php } } ?>

              <tr class="text-primary"  > 
              <td style ="padding-left:40px;">Profit/Loss</td>
                    <th>
                     <span ><?php echo  number_format(round($print_sums['net_profit_loss'],2)); ?></span> 
                    </th>
                </tr>
                
              <tr class="table-primary">
                <th style="font-size: 15px;">Total Liabilities & Stockholder's Equity </th>
                    <th style="font-size: 15px;"> <span > <span ><?php echo number_format(round(($print_sums['net_profit_loss']+$print_sums['equity_side']),2)); ?></span>  </th> 
                </tr>

                </tbody>
        </table>
      </div>
</div>
