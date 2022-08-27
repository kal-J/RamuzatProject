<section id="printable_pending_savings_account">
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
            <center><b>PENDING SAVINGS ACCOUNTS BALANCE AS AT <?php echo $end_date ?></b>
            </center>
        </h5>
    </div>

    <table cellspacing="2" cellpadding="2" class="table table-sm table-bordered">
        <tr>
            <th><b>Account&nbsp;no.</b></th>
            <th><b>Account Holder</b></th>
            <th><b>Product</b></th>
            <th><b>Account Balance</b></th>
        </tr>
        <hr />
        <?php
        $total_savings_balance = 0;
        foreach ($data as $saving) {
            $total_savings_balance += $saving['real_bal'];
        ?>
            <tr cellpadding="2">
                <td><?php echo $saving['account_no']; ?></td>
                <td><?php echo $saving['child_name'] ? $saving['child_name'] . ' - ' . '[ ' . $saving['member_name'] . ' ]' : $saving['member_name']; ?></td>
                <td><?php echo $saving['productname']; ?></td>
                <td><?php echo number_format($saving['real_bal']); ?></td>
            </tr>
        <?php } ?>
        <hr style="margin: 2em inherit;">
        <tr>
            <td colspan="3"><b>Total</b></td>
            <td>
                <b><?php echo number_format($total_savings_balance) ?></b>
            </td>
        </tr>
    </table>
    <p></p>
</section>