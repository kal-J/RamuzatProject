<div role="tabpanel" id="tab-loans_report" class="tab-pane">
    <div class="panel-body">
        <div style="text-align: center;" id="reports_title"><h3>Loans Account - Reports</h3></div>
        <div class="row">
        <div class="float-right col-lg-12">
            <div class="form-row col-md-12">
                <label class="col-form-label"> End Date<span class="text-danger">*</span></label>
                <div class="col-md-2 form-group">
                    <div class="input-group date">
                        <input class="form-control" id="end" autocomplete="off" name="end_date" data-bind="datepicker: $root.end_date,textInput:$root.end_date, event:{ change: $root.updateData}" type="text"/><span  class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
                <div class="col-md-1 form-group">
                    <button type="button" class="btn mb-1 btn-primary" onclick="filter_loans_reports()"><i class="fa fa-filter"></i> Filter</button>

                </div>
            </div>
        </div>
        <br>
        <br>
            
            <div class="col-lg-12">
            <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover dataTables-example" id="tbl_loans_report" style="width: 100%">
            <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Client Name</th>
                  <th>Requested Amount (UGX)</th>
                  <th>Disbursed Amount (UGX)</th>
                  <th>Expected Interest (UGX)</th>
                  <th>Paid Amount (UGX)</th>
                  <th>Days Due</th>
                  <th>Remaining bal (UGX)</th>
                  <th>Disbursement Date</th>
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
          </tr>
      </tfoot>
      </table>
    </div>
            </div>
        </div>
    </div>
</div>