<section id="printable_active_shares">

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
            <center><b>SHARE ACCOUNT REPORT <?php echo $filters['transaction_status']==1?'(Periodic Payment Made)':'(No Periodic Payment Made)'?></b><br>
            <b><h5><b>Date From: </b><?php echo $filters['start_date'] ?><b> Through: </b><?php echo $filters['end_date']!=''?$filters['end_date']:date('d-m-Y')?></h5></b>
        
        </center>
        </h5>
       
    </div>

    
    <table cellspacing="2" cellpadding="2" class="table table-sm table-bordered">
        <tr>
            <?php $postfix= ucwords('(UGX)');?>
            <th><b>Share A/C NO</b></th>
            <th><b>Account Name</b></th>
            <th><b>Number of Shares</b></th>
            <th><b>Price Per Share (UGX)</b></th>
            <th><b>Bought <?php echo $postfix ?></b></th>
            <th><b>Refunded <?php echo $postfix ?></b></th>
            <th><b>Transfered <?php echo $postfix ?></b></th>
            <th><b>Charges <?php echo $postfix ?></b> </th>
            <th><b>Total Amount (UGX)</b></th>
        </tr>
        <hr />
        <?php
        $total_amout = 0;
        foreach ($data as $share) {
            $total_amout += $share['total_amount'];
        ?>
            <tr cellpadding="2">
                <td><?php echo $share['share_account_no']; ?></td>
                <td><?php echo $share['member_name']; ?></td>
                <td><?php echo number_format($share['total_amount'] / $share['price_per_share']); ?></td>
                <td><?php echo number_format($share['price_per_share']); ?></td>
                <td><?php echo number_format($share['shares_bought']); ?></td>
                <td><?php echo number_format($share['shares_refund']); ?></td>
                <td><?php echo number_format($share['shares_transfer']); ?></td>
                <td><?php echo number_format($share['charges']); ?></td>
                <td><?php echo number_format($share['total_amount']); ?></td>
            </tr>
        <?php } ?>

       
    </table>
 
    <?php if (!empty($data)) {
       
        ?>
        <br>
        <div>
           
            <h5>Share Account  Report Summary</b></h5>
            
        </div>
        <br>

        <div>
            <table class="table table-sm table-bordered">
                <tr>
                    <td> Total share Amount</td>
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