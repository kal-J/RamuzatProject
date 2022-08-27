<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/pdf.css">

<table border="0" cellspacing="0" cellpadding="3"><tr><td class="center"><b>LOANS APPROVAL / OFFER FORM</b></td></tr></table>
<table border="0" cellspacing="0" cellpadding="2">
    <tr><td></td>

    
		<td>Date : <?php echo date('n M Y', $loan_detail['date_created']); ?><br />            
            Customer ID : <?php echo $loan_detail['client_no']; ?><br />
            Branch : <?php echo $loan_detail['branch_name']; ?><br />
            Organisation:<?php echo $loan_detail['name']; ?><br />
            Loan reference # : <span style="color:red"><?php echo $loan_detail['loan_approved_id']; ?></span><br />
            <?php if( $loan_detail['group_loan_id'] ){ ?>Group loan id:<?php echo $loan_detail['group_loan_id']; ?><br /><?php } ?>
            Loan approval date:<?php echo $loan_detail['approval_date']; ?><hr>
        </td></tr>

</table>
<br><br>
<table border="0" cellspacing="0" cellpadding="4">
    <tr><td>CLIENT LOAN DETAILS</td><td></td><td></td><td></td></tr><hr>
    <tr>
        <td><b>Client:</b></td><td><?php echo $loan_detail['member_name']; ?></td>
        <td><b>Loan&nbsp;type:</b></td><td><?php echo $loan_detail['type_name']; ?></td>    
    </tr>
    <tr>        
        <td><b>Min&nbsp;guarantor:</b></td><td><?php echo $loan_detail['min_guarantor']; ?></td>
        <td><b>Min&nbsp;collateral:</b></td><td><?php echo $loan_detail['min_collateral']; ?></td>
    </tr>
    <tr>        
        <td><b>Product&nbsp;name:</b></td><td><?php echo $loan_detail['product_name']; ?></td>
        <td><b>Requested&nbsp;amount:</b></td><td><?php echo number_format( $loan_detail['requested_amount'] ); ?></td>
    </tr>
    <tr>        
        <td><b>Application&nbsp;date:</b></td><td><?php echo $loan_detail['application_date']; ?></td>
        <td><b>Suggested&nbsp;disbursement&nbsp;date:</b></td><td><?php echo $loan_detail['suggested_disbursement_date']; ?></td>
    </tr>
    <tr>        
        <td><b>Amount&nbsp;approved:</b></td><td><?php echo number_format( $loan_detail['amount_approved'] ); ?></td>   
        <td><b>Credit&nbsp;officer:</b></td><td><?php echo $loan_detail['credit_officer_name']; ?></td> 
    </tr>
    <tr>        
        <td><b>Method&nbsp;description:</b></td><td colspan="2"><?php echo $loan_detail['method_description']; ?></td>
    </tr>
    <tr>
        <td><b>Installments:</b></td><td><?php echo $loan_detail['approved_installments']; ?></td>
        <td><b>Repayment&nbsp;frequency:</b></td><td><?php echo $loan_detail['approved_repayment_frequency']; ?></td>
    </tr>
    <tr>
        <td><b>Repayment&nbsp;made&nbsp;every:</b></td><td><?php echo $loan_detail['approved_repayment_made_every']; ?></td>
    </tr>
<br><br>
    <tr><td>DISBURSEMENT DETAILS</td><td></td><td></td><td></td></tr><hr>
    <tr>
        <td><b>Disbursement&nbsp;date:</b></td><td><?php echo $loan_detail['disbursement_date']; ?></td>
        <td><b>Disbursement&nbsp;note:</b></td><td><?php echo $loan_detail['disbursement_note']; ?></td>
    </tr>
    <tr>
        <td><b>Interest&nbsp;rate:</b></td><td><?php echo $loan_detail['interest_rate']; ?></td>
        <td><b>Offset&nbsp;made&nbsp;every:</b></td><td><?php echo $loan_detail['offset_made_every']; ?></td>
    </tr>
    <tr>
        <td><b>Offset&nbsp;period:</b></td><td><?php echo $loan_detail['offset_period']; ?></td>
        <td><b>Offset&nbsp;every:</b></td><td><?php echo $loan_detail['offset_every']; ?></td>
    </tr>
    <tr>
        <td><b>Grace&nbsp;period:</b></td><td><?php echo $loan_detail['grace_period']; ?></td>
        <td><b>Repayment&nbsp;frequency:</b></td><td><?php echo $loan_detail['repayment_frequency']; ?></td>
    </tr>
    <tr>
        <td><b>Made&nbsp;every&nbsp;name:</b></td><td><?php echo $loan_detail['made_every_name']; ?></td>
        <td><b>Repayment&nbsp;made&nbsp;every:</b></td><td><?php echo $loan_detail['repayment_made_every']; ?></td>
    </tr>
    <tr>
        <td><b>Installments:</b></td><td><?php echo $loan_detail['installments']; ?></td>
        <td><b>Penalty&nbsp;calculation&nbsp;method&nbsp;id:</b></td><td><?php echo $loan_detail['penalty_calculation_method_id']; ?></td>
    </tr>
    <tr>
        <td><b>penalty&nbsp;tolerance&nbsp;period:</b></td><td><?php echo $loan_detail['penalty_tolerance_period']; ?></td>
        <td><b>penalty&nbsp;rate&nbsp;charged&nbsp;per:</b></td><td><?php echo $loan_detail['penalty_rate_charged_per']; ?></td>
    </tr>
    <tr>
        <td><b>Penalty&nbsp;rate:</b></td><td><?php echo $loan_detail['penalty_rate']; ?></td>
        <td><b>Link&nbsp;to&nbsp;deposit&nbsp;account:</b></td><td><?php echo $loan_detail['link_to_deposit_account']; ?></td>
    </tr>
    <tr>        
        <td><b>Approved&nbsp;by:</b></td><td><?php echo $loan_detail['approved_by']; ?></td>
        <td><b>Approval&nbsp;note:</b></td><td><?php echo $loan_detail['approval_note']; ?></td>
    </tr>
    <tr>
        <td><b>comment:</b></td><td><?php echo $loan_detail['comment']; ?></td>
        <td><b>Loan&nbsp;state&nbsp;comment:</b></td><td><?php echo $loan_detail['loan_state_comment']; ?></td>
    </tr>
</table><br ><hr>
