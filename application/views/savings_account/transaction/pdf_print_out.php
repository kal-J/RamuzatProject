<section id="printable_savings_transaction">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/pdf.css">


    <div class="row d-flex flex-column justify-content-center align-items-center" style="font-size: small;">
        <img style="width: 200px;height: 40px;" src="<?php echo base_url('uploads/organisation_' . $_SESSION['organisation_id'] . '/logo/' . $org['organisation_logo']);  ?>" />


        <span class="my-1" style="font-size: 16px; text-align:left;"><?php echo $org['name']; ?>, <?php echo $branch['physical_address']; ?>, <?php echo $branch['branch_name']; ?>
        </span>
        <span class="my-1">
            <?php echo $branch['postal_address']; ?>, <b>Tel:</b> <?php echo $branch['office_phone']; ?>
        </span>

    </div>

    <br>
    <div>
        <h5 class="text-success my-2">
            <center><b>SAVINGS ACCOUNT TRANSACTIONS AS AT <?php echo $balance_end_date ?></b>
            </center>
        </h5>
    </div>


    <table cellspacing="2" cellpadding="2" class="table table-sm table-bordered">
        <tr>
            <th><b>Transaction&nbsp;no.</b></th>
            <th><b>Transaction&nbsp;date</b></th>
            <th><b>A/C&nbsp;no.</b></th>
            <th><b>A/C&nbsp;Name</b></th>
            <th><b>Debit</b></th>
            <th><b>Credit</b></th>
            <th><b>A/C&nbsp;Balance</b></th>
        </tr>
        <hr />
        <?php
        $total_savings_balance = 0;
        foreach ($data as $transaction) {
        ?>
            <tr cellpadding="2">
                <td><?php echo $transaction['transaction_no']; ?></td>
                <td><?php echo $transaction['transaction_date']; ?></td>
                <td><?php echo $transaction['account_no']; ?></td>
                <td><?php echo $transaction['member_name']; ?></td>
                <td><?php echo $transaction['debit']; ?></td>
                <td><?php echo $transaction['credit']; ?></td>
                <td><?php echo number_format($transaction['end_balance']); ?></td>
            </tr>
        <?php } ?>
        <hr style="margin: 2em inherit;">
        

    </table>
    <p></p>

</section>