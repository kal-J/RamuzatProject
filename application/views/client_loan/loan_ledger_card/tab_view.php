<div role="tabpanel" id="tab-loan_ledger_card" class="tab-pane">
    <div class="panel-body">
        <div class="pull-left add-record-btn">
          <div class="panel-title">
           <h3>Loan Ledger Card</h3>
          </div>
      </div>
            <div data-bind="visible: (parseInt($root.loan_detail().state_id) ==6)" class="pull-right add-record-btn">
              <div class="panel-title">
                <a href="#disburse-modal" data-toggle="modal" data-bind="click: disburse"  class="btn btn-default btn-sm">
                <i class="fa fa-plus-circle"></i> Disburse</a>
                </div>
            </div>
            
            
        <div class="table-responsive">
                <table id="tblLoan_ledger_card" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                    <thead>
                        <tr>
                            <th>Installment #</th>
                            <th>Repayment Date</th>
                            <th>Interest Payable</th>
                            <th>Principal Payable</th>
                            <th>Penalty</th>
                            <th>Total Amount</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                            <th>Action Date</th>
                            <th>Installment Status</th>
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
                          <th>0</th>
                          <th>0</th>
                          <th colspan="3">&nbsp;</th>
                      </tr>
                  </tfoot>
                </table>
            </div><!-- /.table-responsive-->
    </div>
</div><!--End of payments section-->
