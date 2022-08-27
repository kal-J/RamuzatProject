<section id="printable_loan_payable_today" style="width: 100%;">
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
            <center><b>LOAN PAYABLE TODAY (<?php echo date('d-M-Y') ?>)</b>
            </center>
        </h5>
    </div>

    <div style="font-size: small; width: 100%;">
        <table style="width: 100%;" cellspacing="2" cellpadding="2" class="table table-sm table-bordered">
            <tr>
                <th><b>Loan</b></th>
                <th><b>Name</b></th>
                <th><b>Product Name</b></th>
                <th><b>Principal in demand</b></th>
                <th><b>Interest in demand</b></th>
                <th><b>Total Balance </b></th>
            </tr>
            <hr />
            <?php
            if(!empty($data)){
                $totals = 0;
            foreach ($data as $loan_detail) {  ?>
                <tr bgcolor="grey" cellpadding="2">
                    <td><?php echo $loan_detail['loan_no']; ?></td>
                    <td><?php echo $loan_detail['member_name'] ?></td>
                    <td><?php echo $loan_detail['product_name']; ?></td>
                    <td><?php echo number_format($loan_detail['principal_in_demand']); ?></td>
                    <td><?php echo number_format($loan_detail['expected_interest']-$loan_detail['paid_interest']); ?></td>
                    <td><?php echo number_format($loan_detail['principal_in_demand']+($loan_detail['expected_interest']-$loan_detail['paid_interest'])); ?></td>

                    

                </tr>
            <?php
            
         } ?>
            <tr>
            <td></td>
            </tr>
            <?php
        }else{ ?>
                <td col-span="5">No loans payable today (<?php echo date('d-m-y'); ?>)</td>

           <?php } ?>
            <tr>
        </table>
    </div>


</section>