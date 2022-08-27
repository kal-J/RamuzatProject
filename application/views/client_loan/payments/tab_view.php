<div id="tab-loan_payments" class="tab-pane loans">
  <div class="panel-title d-flex justify-content-center my-2">
    <h3 class="d-flex justify-content-center">Loan Installment Payments</h3>
  </div>

  <div class="row d-flex justify-content-between my-2">
    <div class="row col-10 d-flex justify-content-center m-3">
      <div class="input-group date col-4">
        <label class="my-auto" for="start_date">From :&nbsp;</label>
        <input class="col-6" autocomplete="off" placeholder="DD-MM-YYYY" value="" type="text" onkeydown="return false" name="start_date" id="start_date" required />
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
      </div>
      <div class="input-group date col-4">
        <label class="my-auto" for="end_date">To :&nbsp;</label>
        <input class="col-6" autocomplete="off" placeholder="DD-MM-YYYY" value="<?php echo date('d-m-Y'); ?>" type="text" onkeydown="return false" name="end_date" id="end_date" required />
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
      </div>
      <div class="col-1 mx-0">
      <button onclick="filter_by_date()" class="btn btn-sm btn-primary">
        <i class="fa fa-filter fa-2x"></i>
      </button>
      </div>
      
    </div>
    <div class="col-lg-1 d-flex flex-row-reverse align-items-center pull-right">
      <button id="btn_print_loan_payments" onclick="handlePrint_loan_payments()" class="btn btn-sm btn-primary"><i class="fa fa-print fa-2x"></i></button>
      <button id="btn_printing_loan_payments" class="btn btn-primary" type="button" disabled>
        <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
        Printing...
      </button>

    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover dataTables-example" id="tblLoan_payments" style="width: 100%">
      <thead>
        <tr>
          <th>Loan Ref #</th>
          <th>Client</th>
          <th>Installment Number</th>
          <th>Interest Paid</th>
          <th>Principal Paid (UGX)</th>
          <th>Penalty Paid (UGX)</th>
          <th>Written Off Interest (UGX)</th>
          <th>Total Payment</th>
          <th>Balance</th>
          <th>Date Paid</th>
          <th>Received By</th>
          <th>Comment</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th colspan="3">Total</th>
          <th>0</th>
          <th>0</th>
          <th>0</th>
          <th>0</th>
          <th>0</th>
          <th></th>
          <th colspan="2">&nbsp;</th>
        </tr>
      </tfoot>
      <tbody>
      </tbody>
    </table>
  </div>
</div><!-- ==END TAB-PENDING APPROVAL =====-->