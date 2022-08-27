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
        <td colspan="3" style="color:#333;font-size:9; text-align:left;">Account No: <b><?php echo $selected_account['account_no']; ?> </b><br /><br />Account Name: <b><?php echo $selected_account['member_name']; ?></b> <br /><br />
            <b><?php echo date('d-M-Y', strtotime( $start_date ))?></b>  through  <b><?php echo date('d-M-Y', strtotime( $end_date ))?></b>
        </td>
        
    </tr>

</table>
<table border="0" cellspacing="0">
    <tr>
        <td></td><td></td><td ></td><td></td>
        <td colspan="6"><b>ACCOUNT STATEMENT</b></td>
        <td></td>
    </tr><hr />
</table>
<p></p>
<table border="1" style="border: 1px;" cellpadding="2">    
        <tr >
            <th><b>Trans&nbsp;No</b></th>
            <th><b>Account No</b></th>
            <th><b>Date</b></th>
            <th><b>Type</b></th>
            <th><b>Narrative</b></th>
            <th><b>Mode</b></th>
            <th><b>Debit</b></th>
            <th><b>Credit</b></th>
            <th><b>Balance</b></th>
        </tr>
        <hr />
        <?php 
        $balance =0;
        foreach( $transactions as $trans ){  ?>    
            <tr bgcolor="grey" cellpadding="2">
               <td><?php echo $trans['transaction_no']; ?></td>
                <td><?php echo $trans['account_no']; ?></td>
                <td><?php echo date('d-M-Y', strtotime( $trans['transaction_date'] )); ?></td>
                <td><?php echo $trans['type_name']; ?></td>
                <td><small><?php echo $trans['narrative']; ?></small></td>
                <td><?php echo $trans['payment_mode']; ?></td>
                <td><?PHP echo number_format(round($trans['debit'],2)); ?></td>
                <td><?php echo number_format(round($trans['credit'],2)); ?></td>
                <td><b><?php  echo number_format(round($trans['end_balance'],2)); ?></b></td>

            </tr>
        <?php } ?>
    <tr>
</table><p></p>

<table border="0" cellspacing="2">
    <tr>
        <td></td>
         <td ><b>Account Summary </b><hr> </td>
        <td></td>
    </tr>
    
    <tr>
        <td></td>
        <td >Account Balance</td>
        <td ><?php echo number_format($selected_account['real_bal']); ?></td>
    </tr><tr>
        <td></td>
        <td >Amount Locked</td>
        <td ><u><?php echo number_format($selected_account['real_bal']-$selected_account['cash_bal']); ?></u></td>
    </tr>

    <tr>
        <td></td>
         <td >Amount Available for withdraw </td>
        <td ><b><?php echo number_format($selected_account['cash_bal']); ?></b></td>
    </tr>
       
    
</table>
