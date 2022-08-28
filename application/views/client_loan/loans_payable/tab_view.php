<div role="tabpanel" id="tab-loan_payable_today" class="tab-pane loans">
    <div class="panel-title">
        <center>
            <h3 style="font-weight: bold;">Expected Loan Repayments</h3>
        </center>
    </div>

    <div class="mt-4 py-2 d-flex justify-content-center">

    <div class="d-flex py-2">
      <div class="input-group date col-4">
        <label class="my-auto" for="start_date">From :&nbsp;</label>
        <input id="repayment_expected_start_date" class="col-6" autocomplete="off" placeholder="DD-MM-YYYY" value="" type="text" onkeydown="return false" name="start_date" required />
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
      </div>
      <div class="input-group date col-4">
        <label class="my-auto" for="end_date">To :&nbsp;</label>
        <input id="repayment_expected_end_date" class="col-6" autocomplete="off" placeholder="DD-MM-YYYY" value="<?php echo date('d-m-Y'); ?>" type="text" onkeydown="return false" name="end_date" required />
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
      </div>

      <div class="col-1 mx-0">
        <button onclick="get_expected_payments()" class="btn btn-sm btn-primary">
          <i class="fa fa-filter fa-2x"></i>
        </button>
      </div>
    </div>



  </div>

    <br>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover dataTables-example" id="tblPayable_today" style="width: 100%">
            <thead>
                <tr>
                    <th>Ref #</th>
                    <th>Credit Officer</th>
                    <th>Client Name</th>
                    <th>Product Name</th>
                    <th>Requested Amount (UGX)</th>
                    <th>Disbursed Amount (UGX)</th>
                    <th>Expected Interest (UGX)</th>
                    <th>Paid Amount (UGX)</th>
                    <th>Remaining bal (UGX)</th>
                    <th>Disbursement Date</th>
                    <th>Next Pay Date</th>
                    <th>Loan Due Date</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Totals</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div><!-- ==END TAB-PENDING APPROVAL =====-->

<script>
    const get_expected_payments = () => {
        dTable['tblPayable_today'].ajax.reload(null,true);
    }
</script>