<section id="printable_sms_billing">
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
            <center><?php echo $sub_title; ?></center>
            </center>
        </h5>
    </div>


    <br>

    <div>
        <h5>Bill</h5>
    </div>
    <br>

    <div>
        <table class="table table-sm table-bordered">
            <tbody>
                <tr style="font-weight: bold;">
                    <td># Client No.</td>
                    <td>Name</td>
                    <td>Mobile no.</td>
                    <td>No. of messages</td>
                    <td>Total cost (ugx)</td>
                </tr>

                <?php if (empty($billing)) { ?>
                    <tr>
                        <td colspan="6">There is no bill info.</td>
                    </tr>
                    <?php } else {
                    $total_sms = 0;
                    $total_cost = 0;
                    foreach ($billing as $bill) {  ?>
                        <tr>
                        <?php 
                            $total_sms += $bill['no_of_msgs'];
                            $total_cost += ($bill['no_of_msgs'] * $bill['cost']);
                         ?>
                           
                            <td><?php echo $bill['client_no']; ?></td>
                            <td><?php echo $bill['member_name']; ?></td>
                            <td><?php echo $bill['mobile_number']; ?></td>
                            <td><?php echo number_format($bill['no_of_msgs']); ?></td>
                            <td><?php echo number_format($bill['no_of_msgs'] * $bill['cost']); ?></td>

                        </tr>
                <?php }
                } ?>
                <tr>
            </tbody>
        </table>
    </div>

    <br>
    <div>
        <h5>Bill Summary</h5>
    </div>
    <br>
   
    <div>
        <table class="table table-sm table-bordered">
            <tr>
                <td>Total Number of SMS</td>
                <td>
                    <strong>
                        <?php echo !empty($total_sms) ? number_format($total_sms) : 0; ?>
                    </strong>
                </td>
            </tr>
            <tr>
                <td>Total Cost</td>
                <td>
                    <strong><?php echo !empty($total_cost) ? 'ugx ' . number_format($total_cost) : '0 ugx'; ?></strong>
                </td>
            </tr>
           
        </table>
    </div>
    <br>


</section>