<div role="tabpanel" id="tab-loan_payable_today" class="tab-pane loans">
    <div class="panel-title">
        <center>
            <h3 style="font-weight: bold;">Expected Loan Repayments</h3>
        </center>
    </div>

    <div class="mt-4 py-2 d-flex justify-content-center">

    <div class="d-flex py-2">
      <div class="input-group date col-6">
        <label class="my-auto" for="start_date">From :&nbsp;</label>
        <input id="repayment_expected_start_date" class="col-6" autocomplete="off" placeholder="DD-MM-YYYY" value="" type="text" onkeydown="return false" name="start_date" required />
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        <span onclick="(() => {
                $('#repayment_expected_start_date').val('');
              })()" 
        role="button" style="font-size: 1.2rem; cursor: pointer;" class="d-flex align-items-center text-center px-2 text-danger border "><i class="fa fa-trash"></i></span>
      </div>
      
      <div class="input-group date col-6">
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
                    <th>Expected Principal</th>
                    <th>Expected Interest</th>
                    <th>Expected Penalty</th>
                    <th>Total Amount Expected</th>

                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Totals</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    
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