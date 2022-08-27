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
        <td colspan="3" style="color:#333;font-size:9; text-align:left;"><b>Active Loans</b> <br /><br />
            <b><?php echo date('d-M-Y', strtotime( $start_date ))?></b>  through  <b><?php echo date('d-M-Y', strtotime( $end_date ))?></b>
        </td>
        
    </tr>

</table>
<table border="0" cellspacing="0">
    <tr>
        <td></td><td></td><td ></td><td></td>
        <td colspan="6"><b>ACTIVE LOAN REPORTS</b></td>
        <td></td>
    </tr><hr />
</table>
<p></p>
<table border="1" cellpadding="2">    
        <tr>
            <th><b>Ref&nbsp;#</b></th>
            <th><b>Client Name</b></th>
            <th><b>Requested Amount (UGX)</b></th>
            <th><b>Disbursed Amount (UGX)</b></th>
            <th><b>Expected Interest (UGX)</b></th>
            <th><b>Paid Amount (UGX)</b></th>
            <th><b>Remaining bal (UGX)</b></th>
            <th><b>Disbursement Date</b></th>
            <th><b>Next Pay Date</b></th>
            <th><b>Loan Due Date</b></th>
        </tr>
        <hr />
        <?php 
        foreach( $data as $loan ){  ?>    
            <tr bgcolor="grey" cellpadding="2">
               <td><?php echo $loan['loan_no']; ?></td>
                <td><?php echo $loan['member_name']; ?></td>
                <td><?php echo number_format($loan['requested_amount']); ?></td>
                <td><?php echo number_format($loan['expected_principal']); ?></td>
                <td><?php echo number_format($loan['expected_interest']); ?></td>
                <td><?php echo number_format($loan['paid_amount']); ?></td>
                <td><?php 
                    if($loan['expected_principal'] && $loan['expected_interest']){
                        echo $loan['paid_amount'] ? number_format(($loan['expected_principal'] + $loan['expected_interest']) - $loan['paid_amount']) : number_format($loan['expected_principal'] + $loan['expected_interest']);
                      }else{
                        echo '';
                      }
                 ?></td>
                <td><?php echo ($loan['action_date'])? date('d-m-Y', strtotime($loan['action_date'])) : 'None'; ?></td>
                <td><?php echo ($loan['next_pay_date'])? date('d-m-Y', strtotime($loan['next_pay_date'])) : 'None'; ?></td>
                <td><?php echo ($loan['last_pay_date'])? date('d-m-Y', strtotime($loan['last_pay_date'])) : 'None'; ?></td>
            </tr><hr>
        <?php } ?>
    <tr>
</table><p></p>

<?php 
    $total_requested = 0;
    $total_disbursed = 0;
    $total_expected_interest = 0;
    $total_paid_amount = 0;
    $total_remaining_bal = 0;
    foreach ($data as $loan) {
        $total_requested += $loan['requested_amount'];
        $total_disbursed += $loan['expected_principal'];
        $total_expected_interest += $loan['expected_interest'];
        $total_paid_amount += $loan['paid_amount'];

        if(($loan['expected_principal']) && $loan['expected_interest']){
            $total_remaining_bal += ($loan['paid_amount'])? ($loan['expected_principal'] + $loan['expected_interest']) - ($loan['paid_amount']) : ($loan['expected_principal'] + $loan['expected_interest']);
        }
    }

?>

<table cellpadding="2">
    <tr>
        <td colspan="3"><b>ACTIVE LOAN SUMMARY</b></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr><hr>
    
    <tr>
        <td colspan="2">Requested Amount Total</td>
        <td ><b><?php echo number_format($total_requested); ?></b></td>
    </tr>
    <tr>
        <td colspan="2">Disbursed Amount Total</td>
        <td ><b><?php echo number_format($total_disbursed); ?></b></td>
    </tr>
    <tr>
        <td colspan="2">Expected Interest Total</td>
        <td ><b><?php echo number_format($total_expected_interest); ?></b></td>
    </tr>
    <tr>
        <td colspan="2">Paid Amount Total</td>
        <td ><b><?php echo number_format($total_paid_amount); ?></b></td>
    </tr>
    <tr>
        <td colspan="2">Paid Principal Total</td>
        <td ><b><?php echo $total_paid_amount >= $total_disbursed ? number_format($total_disbursed) : number_format($total_paid_amount); ?></b></td>
    </tr>
    <tr>
        <td colspan="2">Paid Interest Total</td>
        <td ><b><?php echo $total_paid_amount > $total_disbursed ? number_format($total_paid_amount - $total_disbursed) : 0; ?></b></td>
    </tr>
    <tr>
        <td colspan="2">Unpaid Balance Total</td>
        <td ><b><?php echo number_format($total_remaining_bal); ?></b></td>
    </tr>
    <tr>
        <td colspan="2">UnPaid Principal Total</td>
        <td ><b><?php echo $total_paid_amount >= $total_disbursed ? 0 : number_format($total_disbursed - $total_paid_amount); ?></b></td>
    </tr>
    <tr>
        <td colspan="2">Unpaid Interest Total</td>
        <td ><b><?php echo $total_paid_amount > $total_disbursed ? number_format($total_expected_interest - ($total_paid_amount - $total_disbursed)) : number_format($total_expected_interest); ?></b></td>
    </tr>
</table>
