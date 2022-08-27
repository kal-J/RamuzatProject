<section id="printable_savings_transaction2">


    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/pdf.css">
    <table border="0" cellspacing="0">
        <tr>
            <td colspan="3"><img src="<?php echo base_url("uploads/organisation_" . $_SESSION['organisation_id'] . "/logo/" . $org['organisation_logo']);  ?>" /><br />
                <span colspan="3" style="color:#333;font-size:8; text-align:left;"><?php echo $org['name']; ?><br />
                    <?php echo $branch['physical_address']; ?>, <?php echo $branch['branch_name']; ?><br />
                    <?php echo $branch['postal_address']; ?><br />
                    <b>Tel:</b> <?php echo $branch['office_phone']; ?>
                </span>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="3" style="color:#333;font-size:9; text-align:left;">Account No: <b><?php echo $selected_account['account_no']; ?> </b><br /><br />Account Name: <b><?php echo $selected_account['member_name']; ?></b> <br /><br />
                <b>Date: <?php 
                $format ="%d-%M-%Y %h:%i %A";
                echo $balance_end_date ? $balance_end_date : (mdate($format)) ?></b>
            </td>

        </tr>

    </table>
    <table border="0" cellspacing="0">
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="6"><b>ACCOUNT TRANSACTIONS</b></td>
            <td></td>
        </tr>
        
    </table>


    <table cellspacing="2" cellpadding="2" class="table table-sm table-bordered">
        <tr>
            <th><b>Transaction&nbsp;no.</b></th>
            <th><b>Transaction&nbsp;date</b></th>
            <th><b>A/C&nbsp;no.</b></th>
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
                <td><?php echo $transaction['debit']; ?></td>
                <td><?php echo $transaction['credit']; ?></td>
                <td><?php echo number_format($transaction['end_balance']); ?></td>
            </tr>
        <?php } ?>
        <hr style="margin: 2em inherit;">


    </table>
    <p></p>

</section>