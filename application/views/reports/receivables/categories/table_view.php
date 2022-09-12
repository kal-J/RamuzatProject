
<div class="container-fluid" style="background: #fff;">
  <div class="d-flex flex-row-reverse mx-2 pt-1">
  </div>
  <div class="pull-left add-record-btn">
  </div>
  <div class="panel-title">
    <center>
      <?php
      if ($all_portfolio_details) {
        foreach ($all_portfolio_details as $key => $value) {
      ?>

          <h3 style="font-weight: bold;">Aging Portfolio - <?php echo $key; ?> Days</h3>
        <?php }
      } else {
        foreach ($all_portfolio_details_2 as $key => $value) {
        ?>
          <h3 style="font-weight: bold;">Rescheduled/Reclassified Loans - <?php echo $key; ?> Days</h3>
      <?php
        }
      } ?>
    </center>
    <center>
      <h4 style="font-weight: bold;">As of - <?php echo date("d-M-Y"); ?> </h4>
    </center>
  </div>
  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover dataTables-example" id="tblProvision_loans" width="100%">
      <thead>
        <tr>
          <th>Client Loan No.</th>
          <th>Client Name</th>
          <th>Loan Product</th>
          <th>Disbursed Amount (UGX)</th>
          <th>Paid Principal (UGX)</th>
          <th>Paid Interest (UGX)</th>
          <th>Days Demanded</th>
          <th>Amount Demanded</th>
          <th>Portfolio at Risk</th>
        </tr>
      </thead>
      <tbody>
        <?php
        //Portfolio aging
        if ($all_portfolio_details) {
          foreach ($all_portfolio_details as $key => $value) {
            //echo json_encode($value[0]['number_of_accounts']);die;

            foreach ($value as $key1 => $value1) {
              //echo json_encode($key1);
              foreach ($value1 as $key2 => $value2) {
        ?>
                <tr>
                  <td><a href='<?php echo site_url('client_loan/view/');
                                echo  $value2['id']; ?>' title='View this Loan details'><?php echo $value2['loan_no']; ?></a></td>
                  <td><?php echo $value2['member_name']; ?></td>
                  <td><?php echo $value2['product_name']; ?></td>
                  <td><?php echo number_format($value2['amount_approved']); ?></td>
                  <td><?php echo is_null($value2['paid_principal']) ? (0) : number_format($value2['paid_principal']); ?></td>
                  <td><?php echo is_null($value2['paid_interest']) ? (0) : number_format($value2['paid_interest']); ?></td>
                  <td><?php echo is_null($value2['days_in_demand']) ? 0 : $value2['days_in_demand']; ?></td>
                  <td><?php echo number_format($value2['amount_in_demand']); ?></td>
                  <td><?php echo number_format($value2['principal_in_demand']); ?></td>
                </tr>




              <?php
              }
            }
          }
        // Rescheduled/Reclassified Loans
        } else {
          foreach ($all_portfolio_details_2 as $key => $value) {
            //echo json_encode($value[0]['number_of_accounts']);die;

            foreach ($value as $key1 => $value1) {
              //echo json_encode($key1);
              foreach ($value1 as $key2 => $value2) {
              ?>
                <tr>
                  <td><a href='<?php echo site_url('client_loan/view/');
                                echo  $value2['id']; ?>' title='View this Loan details'><?php echo $value2['loan_no']; ?></a></td>
                  <td><?php echo $value2['member_name']; ?></td>
                  <td><?php echo $value2['product_name']; ?></td>
                  <td><?php echo number_format($value2['amount_approved']); ?></td>
                  <td><?php echo is_null($value2['paid_principal']) ? (0) : number_format($value2['paid_principal']); ?></td>
                  <td><?php echo is_null($value2['paid_interest']) ? (0) : number_format($value2['paid_interest']); ?></td>
                  <td><?php echo is_null($value2['days_in_demand']) ? 0 : $value2['days_in_demand']; ?></td>
                  <td><?php echo number_format($value2['amount_in_demand']); ?></td>
                  <td><?php echo is_null($value2['principal_in_demand'])?0:number_format($value2['principal_in_demand']); ?></td>
                </tr>




        <?php
              }
            }
          }
        }

        ?>
      </tbody>
    </table>
  </div>
</div><!-- ==END TAB =====-->
<script>
  $(document).ready(function() {
    $('#tblProvision_loans').DataTable();
  });
</script>