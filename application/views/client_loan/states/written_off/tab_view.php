<div id="tab-written_off" class="tab-pane loans">
    <div class="pull-left add-record-btn">
    </div>
      <div class="panel-title">
       <center><h3 style="font-weight: bold;">Written Off Loans</h3></center>
      </div>
      <div class="my-2 d-flex flex-row-reverse">
        <a target="_blank" href="client_loan/export_excel5/8">
          <button class="btn btn-sm btn-primary">
            <i class="fa fa-file-excel-o fa-2x"></i>
          </button>
        </a>
        
        <!-- <button data-bind="visible: isPrinting()" class="btn btn-primary" type="button" disabled>
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Printing...
        </button> -->
      </div>
      <br>

      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover dataTables-example" id="tblWritten_off_loan" width="100%">
            <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Client Name</th>
                  <th>Loan Product</th>
                  <th>Disbursed Amount (UGX)</th>
                  <th>Expected Total (UGX)</th>
                  <th>Paid Amount (UGX)</th>
                  <th>Writtenoff Amount (UGX)</th>
                  <th>Date Written off</th>
                  <th>Reason</th>
                  <!-- <th></th> -->
                </tr>
            </thead>
          <tbody>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="2">Totals</th>
            <th>&nbsp;</th>
            <th>0</th>
            <th>0</th>
            <th>0</th>
            <th>0</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
        </tfoot>
      </table>
    </div>
   </div><!-- ==END TAB-PENDING APPROVAL =====-->

