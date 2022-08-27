<div role="tabpanel" id="tab-repayment_schedule" class="tab-pane">
    <div class="panel-body">
        <div class="pull-left add-record-btn">
          <div class="panel-title">
           <h3>Loan Repayment Schedule</h3>
          </div>
      </div>
            <div data-bind="visible: (parseInt($root.loan_detail().state_id) ==6)" class="pull-right add-record-btn">
              <div class="panel-title">
                <a href="#disburse-modal" data-toggle="modal" data-bind="click: disburse"  class="btn btn-default btn-sm">
                <i class="fa fa-plus-circle"></i> Disburse</a>
                </div>
            </div>
            <div data-bind="visible: (parseInt($root.loan_detail().state_id) ==7)" class="pull-right add-record-btn">
              <div class="panel-title">
                 <?php if(in_array('6', $client_loan_privilege)){ ?>
                    <button onclick="print_schedule()" class="btn btn-success btn-sm pull-right ml-2" style="color:#fff" > <i class="fa fa-print mr-1"></i>Current Loan Schedule</button>
              <button onclick="print_disbursement_sheet()" class="btn btn-primary btn-sm pull-right" style="color:#fff" > <i class="fa fa-print "></i> Disbursement sheet</button>
              
               <?php } ?>
                </div>
            </div>
            <div data-bind="visible: (parseInt($root.loan_detail().state_id) ==7)" class="pull-right add-record-btn">
              <!-- <div class="panel-title">
                <a href="#re_faince-modal" data-toggle="modal" data-bind="click: disburse"  class="btn btn-default btn-sm">
                <i class="fa fa-plus-circle"></i> Re-finance</a>
                </div> -->
            </div>
        <div class="table-responsive">
                <table id="tblRepayment_schedule" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                    <thead>
                        <tr>
                            <th>Installment #</th>
                            <th>Repayment Date</th>
                            <th>Interest Payable (UGX)</th>
                            <th>Principal Payable (UGX)</th>
                            <th>Penalty (UGX)</th>
                            <th>Total Amount (UGX)</th>
                            <th>Action Date</th>
                            <th>Payment Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>                    
                  <tfoot>
                      <tr>
                          <th>Totals</th>
                          <th></th>
                          <th>0</th>
                          <th>0</th>
                          <th>0</th>
                          <th>0</th>
                          <th colspan="3">&nbsp;</th>
                      </tr>
                  </tfoot>
                </table>
            </div><!-- /.table-responsive-->
    </div>
</div><!--End of payments section-->
