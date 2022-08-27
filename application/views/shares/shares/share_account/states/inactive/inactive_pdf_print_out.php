<section id="printable_in_active_shares">

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/pdf.css">
    <div class="row d-flex flex-column justify-content-center align-items-center" style="font-size: small;">
        <img style="width: 200px;height: 40px;" src="<?php echo base_url('uploads/organisation_' . $_SESSION['organisation_id'] . '/logo/' . $org['organisation_logo']);  ?>" />


        <span class="my-1" style="font-size: 16px; text-align:left;"><?php echo $org['name']; ?>, <?php echo $branch['physical_address']; ?>, <?php echo $branch['branch_name']; ?>
        </span>
        <span class="my-1">
            <?php echo $branch['postal_address']; ?>, <b>Tel:</b> <?php echo $branch['office_phone']; ?>
        </span>
        <span class="my-2"><b>Date</b> : <?php echo date('d-m-Y'); ?></span>

    </div>

    <br>
    <br>
    <div>
        <h5 class="text-success my-2">
            <center><b>IN-ACTIVE SHARE ACCOUNTS</b></center>
        </h5>
    </div>


    <table cellspacing="2" cellpadding="2" class="table table-sm table-bordered">
        <tr>
            <th><b>Share A/C NO</b></th>
            <th><b>Account Name</b></th>
            <th><b>Total Amount (UGX)</b></th>
        </tr>
        <hr />
        <?php
        if (empty($data)) {
        ?>
            <tr>
                <td colspan="3">There are no In-Active share accounts.</td>
            </tr>
        <?php

        } else {
        ?>
            <?php
            $total_amout = 0;
            foreach ($data as $share) {
                $total_amout += $share['total_amount'];
            ?>
                <tr cellpadding="2">
                    <td><?php echo $share['share_account_no']; ?></td>
                    <td><?php echo $share['salutation'] . ' ' . $share['firstname'] . ' ' . $share['lastname']; ?></td>
                    <td><?php echo number_format($share['total_amount']); ?></td>
                </tr>
            <?php } ?>

        <?php } ?>


    </table>

    <?php if (!empty($data)) { ?>
        <br>
        <div>
            <h5>In-active Share Accounts Summary</h5>
        </div>
        <br>

        <div>
            <table class="table table-sm table-bordered">
                <tr>
                    <td>In-active Total share Amount</td>
                    <td>
                        <b><?php echo 'UGX ' . number_format($total_amout);  ?></b>
                    </td>
                </tr>
                <tr>


            </table>
        </div>

        <br>

    <?php } ?>

</section>