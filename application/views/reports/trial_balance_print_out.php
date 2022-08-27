<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/pdf.css">
<table border="0" cellspacing="0">
    <tr>
        <td colspan="3"><img src="<?php echo base_url("uploads/organisation_".$_SESSION['organisation_id']."/logo/".$org['organisation_logo']);  ?>"  />
<span colspan="3" style="color:#333;font-size:8; text-align:left;"><?php echo $org['name']; ?><br />
            <?php echo $branch['physical_address']; ?>, <?php echo $branch['branch_name']; ?><br />
            <?php echo $branch['postal_address']; ?><br />
            <b>Tel:</b> <?php echo $branch['office_phone']; ?>
        </span>
        </td>
        <td></td>
        <td></td>
        <td></td><td></td>
        <td colspan="3" style="color:#333;font-size:9; text-align:left;"><b>Trial Balance</b> <br /><br />
            <b><?php echo date('d-M-Y', strtotime( $start_date ))?></b>  through  <b><?php echo date('d-M-Y', strtotime( $end_date ))?></b>
        </td>
        
    </tr>

</table>
<table border="0" cellspacing="0">
    <tr>
        <td></td><td></td><td ></td><td></td>
        <td colspan="6"><b>TRIAL BALANCE</b></td>
        <td></td>
    </tr><hr />
</table>
<p></p>
<table border="1" cellpadding="2">    
        <tr>
            <th><b>Account Name</b></th>
            <th><b>Debit</b></th>
            <th><b>Credit</b></th>
        </tr>
        <hr />
        <?php 
        $total_debit = 0;
        $total_credit = 0;
        foreach( $trial_bal_data['data'] as $account ){
            $debit_sum_amount = 0;
            $credit_sum_amount = 0;
            $debit_sum = $account['debit_sum' ] ? $account['debit_sum' ] : 0;
            $credit_sum = $account['credit_sum'] ? $account['credit_sum'] : 0;
           if($account['normal_balance_side'] == 1){
            $debit_sum_amount = $debit_sum - $credit_sum;
           }
           if($account['normal_balance_side'] == 2){
            $credit_sum_amount = $credit_sum - $debit_sum;
           }
           $total_debit += $debit_sum_amount;
           $total_credit += $credit_sum_amount;

            ?>    
            <tr bgcolor="grey" cellpadding="2">
               <td><?php echo $account['account_name']; ?></td>
               <td><?php echo number_format(round($debit_sum_amount,2)); ?></td>
               <td><?php echo number_format(round($credit_sum_amount, 2)); ?></td>
            </tr>
            <hr>

        <?php } ?>
    <tr>
</table><p></p>

<table cellpadding="2">
    <tr>
        <td colspan="3"><b>TRIAL BALANCE SUMMARY</b></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr><hr>
    
    <tr>
        <td colspan="2">Total Debit</td>
        <td ><b><?php echo number_format(round($total_debit, 2)); ?></b></td>
    </tr>
    <tr>
        <td colspan="2">Total Credit</td>
        <td ><b><?php echo number_format(round($total_credit, 2)); ?></b></td>
    </tr>
</table>
