<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/pdf.css">

<table border="0" cellspacing="0" cellpadding="3"><tr><td class="center"><b>LOAN FEES PAYMENT RECIEPT</b></td></tr></table>
<table border="0" cellspacing="0" cellpadding="2">
    <tr><td></td>
        <td>Date : <?php echo date('n M Y', $single_receipt_items[0]['date_created']); ?><br />
            Receipt # : <span style="color:red"><?php echo $single_receipt_items[0]['transaction_no']; ?></span><br />
            Customer ID : <?php echo $loan_detail['client_no']; ?><br />
            Branch : <?php echo $loan_detail['branch_name']; ?><hr>
        </td></tr>

</table>
<br><br>
<table border="0" cellspacing="0" cellpadding="2">
    <tr><td>RECEIVED FROM:</td><td></td><td><?php echo $loan_detail['member_name']; ?></td></tr>
</table><hr><br ><br>

<table border="0" cellspacing="0" cellpadding="2">
    <tr>	
        <td>Item description</td>
        <td>Quantity</td>
        <td>Amount (UGX)</td>
    </tr>
    <?php foreach ($single_receipt_items as $receipt_item) { ?>
        <tr>	
            <td colspan="1"><?php
                if (isset($receipt_item['feename'])) {
                    echo $receipt_item['feename'];
                } else {
                    echo '';
                }
                ?></td>
            <td>1</td>
            <td><?php
                if (isset($receipt_item['amount'])) {
                    echo number_format(round($receipt_item['amount'], 1));
                } else {
                    echo '';
                }
                ?></td>
        </tr>
            <?php } ?>
</table><br ><hr>
<table border="0" cellspacing="0" cellpadding="2">
    <tr><td colspan="2">TOTAL</td><td colspan="1"><?php
            if (isset($receipt_item_sum['total'])) {
                echo number_format(round($receipt_item_sum['total'], 1));
            } else {
                echo '';
            }
            ?></td></tr>
</table><br ><br>
<table><b>FOR: <?php echo strtoupper($loan_detail['name']); ?></b></table>