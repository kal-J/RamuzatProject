<div role="tabpanel" id="tab-solidarity_loan" class="tab-pane loans active">
  <br>
    <div class="col-lg-12">
        <div class="pull-right add-record-btn">
        <div class="panel-title">
        <?php if(in_array('1', $group_loan_privilege)){ ?>
            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#add_pending_approval-modal"><i class="fa fa-plus-circle"></i> New Group Loan</button>
        <?php } ?>
        </div>
    </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover dataTables-example" id="tblGroup_loan" style="width: 100%">
                <thead>
                    <tr>
                        <th>Ref #</th>
                        <th>Group Name</th>
                        <th>Requested Amount (UGX)</th>
                        <th>Approved Amount (UGX)</th>
                        <th>Credit Officer</th>
                        <th>Comment</th>
                        <th></th>
                    </tr>
                </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
    </div>
 </div>
