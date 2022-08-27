<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/pdf.css">
<style>
    .blueText{ color:blue; }
</style>
<table border="0" cellspacing="0" cellpadding="0"><tr><td></td><td colspan="2"><b>LOANS APPROVAL / OFFER FORM</b></td></tr></table>
<table border="0" cellspacing="0" cellpadding="2">
    <tr>
        <td>Date : <?php echo date('d M Y', strtotime($loan_detail['application_date'])); ?><br />  
            Ref: <span style="color:red"><?php echo $loan_detail['loan_no']; ?></span><br />
            To:<?php echo $loan_detail['member_name']; ?><br />
            <?php if( $loan_detail['group_loan_id'] ){ ?>Group loan id:<?php echo $loan_detail['group_loan_id']; ?><br /><?php } ?>
        </td><td></td>
    </tr>
</table><br><br>

<table border="0" cellspacing="0" cellpadding="4"><tr><td>SUBJECT: <b>LOAN APPROVED IN ...........................(SECTOR)</b></td></tr></table><br /><br />

<table border="0" cellspacing="0" cellpadding="4">
    <tr>        
        <td colspan="4">Considering the Application dated <span class="blueText"><b><?php echo date('d-M-Y', strtotime($loan_detail['application_date'])); ?></b></span>, we gladly inform you that your Application has periodically been accepted.
            The terms and conditions of the loan have been communicated to you both verbally and in writing. If you accept these
            terms and conditions, please [not clear] and its copy and send the letter to us and preserve the copy by your own.
        </td><br>        
    </tr>
    <tr>
        <td  colspan="4">Management: Terms and conditions of loan approval.</td>
    </tr>
</table>

<table border="0" cellspacing="0" cellpadding="4">
    <tr>
        <td><b>Officer:</b></td><td><?php echo $loan_detail['credit_officer_name']; ?></td>
        <td><b>Branch Manager:</b></td><td><?php echo $loan_detail['disbursement_note']; ?>..........</td>
    </tr>
    <tr>
        <td><b>[not clear] (xx):</b></td><td><?php echo $loan_detail['credit_officer_name']; ?> </td>
        <td><b>Signature (witness):</b></td><td><?php // echo $loan_detail['disbursement_note']; ?>..............</td>
    </tr>
</table>



<table border="0" cellspacing="0" cellpadding="4">
    
</table><br >
