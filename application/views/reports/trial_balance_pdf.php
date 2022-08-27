
    <div class="col-lg-12">
      <div class="table-responsive">
         <table class="table table-sm table-bordered" width="100%">
            <tbody >
                <tr style="background-color: #1c84c6; color: #fff;">
                     <td colspan="3">
                          <span style="font-size: 18px;color: #fff;font-weight: bold;"><center>Trial Balance  &nbsp;&nbsp;&nbsp;<?php echo date('d-M-Y',strtotime($end_date));?></center></span>
                    </td>
                </tr>
              <tr >
                    <th style ="font-size: 14px;" >Account Name</th>
                    <th><div class="no-margins"><span  style ="font-size: 14px;" >Debit</span></div> 
                    <th><div class="no-margins"><span  style ="font-size: 14px;" >Credit</span></div> 
                </th>
                </tr>
             <?php
             $total_credit =$total_debit=0;
              foreach ($data as $key => $value) { ?>
              <?php if (($value['normal_balance_side']==1)&&($value['debit_balance']!=0)) {  
                $total_debit+=$value['debit_balance'];
                ?>
               <tr >
                    <td style ="padding-left:40px;  font-size: 14px;" ><?php echo $value['account_name']; ?></td>
                    <td><div class="no-margins"><span  style =" font-size: 14px;" ><?php echo number_format(round($value['debit_balance'],2)); ?></span></div> 
                    <td><div class="no-margins"><span  style =" font-size: 14px;" >0</span></div> 
                </td>
                </tr>
           <?php } if (($value['normal_balance_side']==2)&&($value['credit_balance']!=0)) { 
           $total_credit+=$value['credit_balance'];
            ?>
              <tr >
                    <td style ="padding-left:40px;  font-size: 14px;" ><?php echo $value['account_name']; ?></td>
                    <td><div class="no-margins"><span  style =" font-size: 14px;" >0</span></div> 
                    <td><div class="no-margins"><span  style =" font-size: 14px;" ><?php echo number_format(round($value['credit_balance'],2)); ?></span></div> 
                </td>
                </tr>
            <?php } } ?>
            
              <tr style="background-color: #fafcdc;">
                <th>Totals </th>
                    <th> <span > <?php echo number_format(round($total_debit,2)); ?></span>  </th> 
                    <th> <span > <?php echo number_format(round($total_credit,2)); ?></span>  </th>
                </tr>
               
                
                </tbody>
        </table>
      </div>
</div>
