<div role="tabpanel" id="tab-inarrears_loans" class="tab-pane loans">
  <div class="panel-title">
    <center>
      <h3 style="font-weight: bold;">In-arrears Loan Reports</h3>
    </center>
  </div>
  <div class="row">
    <div class="col-lg-3">
      <label for="amount"><strong>Amount: </strong><input type="text" id="inarrear_min_amount" name="min_amount" style="border: 0; color: #f6931f; font-weight: bold; " size="8" />&nbsp;To&nbsp;
        <input type="text" id="inarrear_max_amount" name="max_amount" style="border: 0; color: #f6931f; font-weight: bold; " size="9" /></label>
      <div id="inarrear_amount-range"></div>
    </div>
    <div class="col-lg-3 row">
      <label class="col-form-label  col-lg-6"><strong>Loan Type</strong></label>
      <select onChange="set_inarrear_select_value()" class="col-lg-6" style="max-height:30px;margin-top:8px;" name="loan_type" id="inarrear_loan_type">
        <option value="">--Select--</option>
        <option value="0">Unsecured</option>
        <option value="1">Secured</option>
      </select>
    </div>
    <div class="col-lg-3 row">
      <label class="col-form-label col-lg-6">Loan Product</label>
      <select onChange="set_inarrear_select_value()" class="col-lg-6" style="max-height:30px;margin-top:8px;" data-bind='options:loan_product_data, optionsText: function(item){return item.product_name}, optionsAfterRender: setOptionValue("id"), optionsCaption: "-- select --"' class="col-lg-7" name="product_id" id="inarrear_product_id"> </select>
    </div>
    <div class="col-lg-3">
      <label class="col-lg-12" for="days"><strong>Days <br><small>in Arreas</small>: </strong><input type="text" id="inarrear_min_days" name="min_days" style="border: 0; color: #f6931f; font-weight: bold; " size="2" />&nbsp;To&nbsp;
        <input type="text" id="inarrear_max_days" name="max_days" style="border: 0; color: #f6931f; font-weight: bold; " size="2" /></label>
      <div id="days-range"></div>
    </div>
  </div>
  <div class="row mt-3 w-100 d-flex align-items-center flex-row-reverse">
    <div>
      <button id="btn_print_in_arrears_loans_report" onclick="handlePrint_in_arrears()" class="btn btn-primary btn-sm"><i class="fa fa-print fa-2x"></i></button>
      <button id="btn_printing_in_arrears_loans_report" class="btn btn-primary" type="button" disabled>
        <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
        Printing...
      </button>

    </div>
    <div class="mr-2">
      <a id="export_excel_in_arrears_loans">
        <button class="btn btn-sm btn-primary">
          <i class="fa fa-file-excel-o fa-2x"></i>
        </button>
      </a>
    </div>
  </div>
  <br>
  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover dataTables-example" id="tblInarrears_client_loan" style="width: 100%">
      <thead>
        <tr>
          <th>Ref #</th>
          <th>Client Name</th>
          <th>Installments</th>
          <th>Paid Installments</th>
          <th>Upaid Installments</th>
          <th>Requested Amount (UGX)</th>
          <th>Disbursed Amount (UGX)</th>
          <th>Expected Interest (UGX)</th>
          <th>Paid Amount (UGX)</th>
          <th>Remaining bal (UGX)</th>
          <th>Loan Due Date</th>
          <th>Days<small>In arrears</small></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="2">Totals</th>
          <th>0</th>
          <th>0</th>
          <th>0</th>
          <th>0</th>
          <th>0</th>
          <th colspan="2">&nbsp;</th>
        </tr>
      </tfoot>
    </table>
  </div>
</div>