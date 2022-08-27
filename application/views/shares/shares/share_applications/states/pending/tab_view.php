<div role="tabpanel" id="tab-pending_application" class="tab-pane">
  <br>
    <div class="col-lg-12">
        <div class="pull-right add-record-btn">
        <div class="panel-title">
        <?php if(in_array('1', $share_privilege)){ ?>
            <a href = "#add_share_application-modal" data-toggle="modal"  class="btn btn-default btn-sm">
                <i class="fa fa-plus-circle"></i> New Application</a>
        <?php } ?>
        </div>
    </div>
        <div class="table-responsive">
             <table class="table  table-bordered table-hover" id="tblShares_Pending_application" width="100%" >
                <thead>
                    <tr> 
                        <th>Share App NO</th>
                        <th>Applicant </th>
                        <th>Share Category </th>
                        <th>Application Date</th>
                        <th>Shares Requested</th>
                        <th>Total Amount (UGX)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
 </div>