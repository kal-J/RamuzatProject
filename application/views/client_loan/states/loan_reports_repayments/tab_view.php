<div role="tabpanel" id="tab-loan_reports_repayments" class="tab-pane loans">
  <div class="panel-title">
    <center>
      <h3 style="font-weight: bold;">Loan Repayments Report</h3>
    </center>
  </div>

  <div class="mt-4 py-2 d-flex justify-content-between">

    <div class="d-flex py-2">
      <div class="input-group date col-4">
        <label class="my-auto" for="start_date">From :&nbsp;</label>
        <input id="start_date_filter" class="col-6" autocomplete="off" placeholder="DD-MM-YYYY" value="" type="text" onkeydown="return false" name="start_date" required />
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
      </div>
      <div class="input-group date col-4">
        <label class="my-auto" for="end_date">To :&nbsp;</label>
        <input id="end_date_filter" class="col-6" autocomplete="off" placeholder="DD-MM-YYYY" value="<?php echo date('d-m-Y'); ?>" type="text" onkeydown="return false" name="end_date" required />
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
      </div>

      <div>
        <select id="selected_credit_officer" name="credit_officer_id" class="form-control">
          <option value="">--select credit officer--</option>
          <!-- ko foreach: staffs -->
          <option data-bind="value: $data.id, text: $data.firstname + ' ' + $data.lastname"></option>
          <!-- /ko -->
        </select>
      </div>


      <div class="col-1 mx-0">
        <button onclick="filter_loan_reports_repayments()" class="btn btn-sm btn-primary">
          <i class="fa fa-filter fa-2x"></i>
        </button>
      </div>
    </div>




    <!-- <a target="_blank" href="client_loan/export_excel/disbursed_loans">
      <button class="btn btn-sm btn-primary">
        <i class="fa fa-file-excel-o fa-2x"></i>
      </button>
    </a> -->


  </div>
  <br>
  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover dataTables-example" id="tblloan_reports_repayments" style="width: 100%">
      <thead style="text-transform: uppercase; font-size: smaller;">
        <tr>
          <th>Ref #</th>
          <th>Credit Officer</th>
          <th>Client Name</th>
          <th>Product Name</th>
          <th>Disbursed Amount (UGX)</th>
          <th>Expected Interest (UGX)</th>
          <th>Total Paid (UGX)</th>
          <th>Principal Paid (UGX)</th>
          <th>Interest Paid (UGX)</th>
          
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
          <th></th>
          
          
        </tr>
      </tfoot>
    </table>
  </div>
</div><!-- ==END TAB-PENDING APPROVAL =====-->

<script>
  const filter_loan_reports_repayments = () => {
    if (dTable && dTable['tblloan_reports_repayments']) {
      dTable['tblloan_reports_repayments'].ajax.reload(null, true);
    }

  }
</script>