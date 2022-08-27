<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/pdf.css">
<table border="0" cellspacing="0">
    <tr>
        <td></td>
        <td></td>
        <td></td><td></td><td></td><td></td>
        <td colspan="1"><img src="<?php echo base_url(); ?>images/logo2.png" height="40" /></td>
        <td colspan="3" style="color:#333;font-size:8; text-align:right;"><?php echo $loan_detail['name']; ?><br />
            <?php echo $loan_detail['physical_address']; ?>, <?php echo $loan_detail['branch_name']; ?><br />
            <?php echo $loan_detail['postal_address']; ?><br />
            <?php echo $loan_detail['office_phone']; ?>, <?php echo $loan_detail['email_address']; ?>
        </td>        
    </tr>

</table>
<table border="0" cellspacing="0">
    <tr>
        <td></td><td></td><td colspan="1"></td>
        <td colspan="6"><b>LOAN FACT SHEET</b></td>
        <td></td>
    </tr><hr />
</table>

<table border="0" cellspacing="0">
<tr><td><b>Client's details</b></td><td></td><td></td><td></td></tr>
    <tr>
        <td>Client M/Ship No:</td><td><?php echo $loan_detail['loan_no']; ?></td>
        <td>Client's name:</td><td><?php echo $loan_detail['member_name']; ?></td>    
    </tr>
    <tr>
        <td>Date of Application:</td><td> <?php echo date('d-M-Y',strtotime($loan_detail['application_date'])); ?> </td>
        <td>Client's Residence Address:</td><td><?php echo $loan_detail['physical_address'] . ', ' .$loan_detail['postal_address'] ; ?></td>
    </tr>
    <tr>
        <td>Client Business Type:</td><td><?php echo $business['businessname']; ?></td>
        <td>Client Business Address:</td><td><?php echo $business['businesslocation'];; ?></td>
    </tr>
    <tr>
        <td>Amount Requested:</td><td><?php echo number_format(round( $loan_detail['requested_amount'] , 1)) . ' UGX'; ?></td>
        <td>Amount Recommended:</td><td><?php echo number_format(round($loan_detail['amount_approved'], 1)) . ' UGX'; ?></td>
    </tr>
    <tr>
        <td>Nature of business:</td><td><?php echo $business['natureofbusiness'];; ?></td>
        <td></td><td></td>
    </tr>
</table><p></p>
<table border="0" cellspacing="0">
    <tr><td><b>Terms and Conditions</b></td></tr><p></p>
</table><hr />
<table border="0" cellspacing="0">
        <tr><td>Interest rate: <?php echo $loan_detail['interest_rate']; ?></td></tr>
        <?php ( $repayment_period =  $loan_detail['approved_repayment_frequency'] * $loan_detail['installments'] );?>
        <tr><td>Period: <?php echo $repayment_period.' '. $loan_detail['approved_repayment_made_every']; ?></td></tr>
        <tr><td>Mode of Payment: <?php echo (!empty($repayment_schedule)?number_format(  $repayment_schedule['principal_amount'] + $repayment_schedule['interest_amount'] ):0) . ' per '. $loan_detail['made_every_name']; ?></td></tr>
<?php
    $item_value_total = 0; 
    foreach( $collaterals as $collateral ){ 
        $item_value_total +=$collateral['item_value'];
    }
?>
        <tr><td>Collateral: <?php  echo isset( $item_value_total )? number_format(round($item_value_total, 1)) . ' UGX' : '';  ?></td></tr>
    <p></p>
</table><p></p>
<table border="0" cellspacing="0">
    <tr><td><b>Guarantors:</b></td></tr><p></p>
</table><hr />
<table border="0" cellspacing="0">
    <tr>
        <td>Guarantor's Name</td>
        <td>Telephone</td>
    </tr>
    <?php foreach( $guarantors as $guarantor ){ ?>
    <tr>
        <td><?php echo $guarantor['guarantor_name']; ?></td>
        <td><?php echo $guarantor['mobile_number']; ?></td>
    </tr>
    <?php } ?>
</table><p></p><hr />
<table border="0" cellspacing="0">
    <tr>
        <td>Purpose of the Facility: <?php echo $loan_detail['comment']; ?></td>
    </tr> 
</table><p></p>

<table border="0" cellspacing="0">
    <tr><td><b>Sources of Repayment:</b></td></tr><p></p>
</table>
<table border="1" cellspacing="0">
    <tr>
        <td>Business type</td>
        <td>Monthly</td>
        <td>Remarks</td>    
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>    
    </tr>
    <tr>
        <td>Business Expenditure</td>
        <td></td>
        <td></td>    
    </tr>
    <tr>
        <td>Personal expenditure</td>
        <td></td>
        <td></td>    
    </tr>
    <tr>
        <td>Net Income</td>
        <td></td>
        <td></td>    
    </tr>
    <tr>
        <td>Installment per month</td>
        <td></td>
        <td></td>    
    </tr>
    <tr>
        <td>Installment per week</td>
        <td></td>
        <td></td>    
    </tr>
</table><p></p>
<table border="0" cellspacing="0">
    <tr><td><b>Credit history:</b></td></tr><p></p>
</table><hr />
<table border="0" cellspacing="0">
    <tr>
        <td>commentry here</td>
    </tr>
</table><p></p>

<table border="0" cellspacing="0">
    <tr><td><b>Loan Offcer's recommendation:</b></td></tr><p></p>
</table><hr />
<table border="0" cellspacing="0">
    <tr>
        <td>commentry here</td>
    </tr>
</table><p></p>

<table border="0" cellspacing="0">
    <tr><td><b>BM's Comment:</b></td></tr><p></p>
</table><hr />
<table border="0" cellspacing="0">
    <tr>
        <td>commentry here</td>
    </tr>
</table><p></p>


<?php
    function calc_period_from_date( $date ){
        $birthDate = date( 'm-d-Y', strtotime( $date ) );
        $birthDate = explode("-", $birthDate);
        $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
        ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));
        return $age;
    }  


    function calc_date_diff( $date1, $date2 ){
        $diff = abs(strtotime($date2) - strtotime($date1));
        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        return  $years . 'years ' . $months. 'months ' . $days . 'days';
    }
?>