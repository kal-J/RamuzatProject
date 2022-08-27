<section id="printable_loan_installment_payments" style="width: 100%;">
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
    <div style="width: 100%;">
        <h5 class="text-success my-2">
            <center><b>LOAN PAYMENTS (<?php echo $start_date ? $start_date : 'UPTO' ?> - <?php echo $end_date ?>)</b>
            </center>
        </h5>
    </div>

    <div style="font-size: small; width: 100%;">
        <table style="width: 100%;" cellspacing="2" cellpadding="2" class="table table-sm table-bordered">
            <tr>
                <th><b>Loan</b></th>
                <th><b>Name</b></th>
                <th><b>Installment No</b></th>
                <th><b>Interest Paid</b></th>
                <th><b>Principal Paid</b></th>
                <th><b>Penalty Paid</b></th>
                <th><b>Total Payment</b></th>
                <th><b>Date Paid</b></th>
            </tr>
            <hr />
            <?php
            foreach ($data as $installment) {  ?>
                <tr bgcolor="grey" cellpadding="2">
                    <td><?php echo $installment['loan_no']; ?></td>
                    <td><?php echo $installment['lastname'] . ' ' . $installment['firstname'] ?></td>
                    <td><?php echo $installment['installment_number']; ?></td>
                    <td><?php echo number_format($installment['paid_interest']); ?></td>
                    <td><?php echo number_format($installment['paid_principal']); ?></td>
                    <td><?php echo number_format($installment['paid_penalty']); ?></td>
                    <td><?php echo number_format($installment['paid_penalty'] + $installment['paid_principal'] + $installment['paid_interest']); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($installment['payment_date'])); ?></td>

                </tr>
            <?php } ?>
            <tr>
        </table>
    </div>


</section>