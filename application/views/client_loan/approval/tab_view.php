<div role="tabpanel" id="tab-loan_approvals" class="tab-pane">
    <div class="panel-body">
        <div class="pull-left add-record-btn">
          <div class="panel-title">
           <center><h3 style="font-weight: bold;">Loan Approvals</h3></center>
          </div>
      </div>
      <?php if((in_array('14', $client_loan_privilege))&&($loan_detail['state_id']==5)){ ?>
            <div class="pull-right add-record-btn">
              <div class="panel-title">
                <a  data-toggle="modal" data-bind="click: approve_loan" data-target="#approve-modal" class="btn btn-sm" ><i class='fa fa-check-square-o' style='font-size:16px'></i> Approve Application</a>
                </div>
            </div>
          <?php } ?>
        <div class="table-responsive">
                <table id="tblLoan_approvals" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                    <thead>
                        <tr>
                            <!--th>Loan approved id</th-->
                            <th>Date</th>
                            <th>Approved By</th>
                            <th>Suggested Disbursement Date</th>
                            <th>Amount Approved (UGX)</th>
                            <th>Comment</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- /.table-responsive-->
    </div>
</div><!--End of payments section-->