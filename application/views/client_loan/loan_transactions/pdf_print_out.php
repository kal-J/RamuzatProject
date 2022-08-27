<section id="printable_loan_payments">

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
    <br>

    <div>
        <h5 class="text-success my-2">
            <center><b>LOAN INSTALLMENT PAYMENTS</b></center>
        </h5>
    </div>
    <br>
    <br>

    <div>
        <h5>Client</h5>
    </div>
    <br>

    <div>
        <table class="table table-sm table-bordered">
            <tr>
                <td>Name</td>
                <td>
                    <strong>
                        <?php echo ucwords(strtolower($loan_detail['member_name'])); ?>
                    </strong>
                </td>
            </tr>
            <tr>
                <td>Membership Number</td>
                <td>
                    <strong><?php echo $loan_detail['client_no']; ?></strong>
                </td>
            </tr>
            <tr>
                <td>Contact</td>
                <td>
                    <strong>
                        <?php echo $loan_detail['mobile_number']; ?>
                    </strong>
                </td>
            </tr>
        </table>
    </div>
    <br>

    <table cellpadding="2" class="table table-sm table-bordered">
        <tr>
            <th><b>Loan&nbsp;Ref</b></th>
            <th><b>Installment No</b></th>
            <th><b>Interest Paid</b></th>
            <th><b>Principal Paid</b></th>
            <th><b>Penalty Paid</b></th>
            <th><b>Total Payment</b></th>
            <th><b>Date Paid</b></th>
            <th><b>Comment</b></th>
        </tr>
        <hr />
        <?php
        foreach ($data as $installment) {  ?>
            <tr  cellpadding="2">
                <td><?php echo $installment['loan_no']; ?></td>
                <td><?php echo $installment['installment_number']; ?></td>
                <td><?php echo number_format($installment['paid_interest']); ?></td>
                <td><?php echo number_format($installment['paid_principal']); ?></td>
                <td><?php echo number_format($installment['paid_penalty']); ?></td>
                <td><?php echo number_format($installment['paid_penalty'] + $installment['paid_principal'] + $installment['paid_interest']); ?></td>
                <td><?php echo date('d-M-Y', strtotime($installment['payment_date'])); ?></td>
                <td><small><?php echo $installment['comment']; ?></small></td>

            </tr>
        <?php } ?>
        <tr>
    </table>
    <p></p>

</section>