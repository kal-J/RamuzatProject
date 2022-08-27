<div role="tabpanel" id="tab-active_application" class="tab-pane active">
  <br>
    <div class="col-lg-12">
        <div class="pull-right add-record-btn">
        <div class="panel-title">
        <?php if(in_array('1', $share_privilege)){ ?>
            <a href="#add_account-modal" data-toggle="modal" id="active_share_issuance" class="btn btn-default btn-sm"> <i class="fa fa-plus-circle"></i> New Application</a>
        <?php } ?>
        </div>
    </div>
        <div class="table-responsive">
             <table class="table  table-bordered table-hover" id="tblShares_Active_Application" width="100%" >
                <thead>
                    <tr> 
                        <th>Share App NO</th>
                        <th>Applicant</th>
                        <th>Shares Requested</th>
                        <th>Shares Approved</th>
                        <th>Approval Date</th>
                        <th>Total Amount (UGX)</th> 
                        <th>Amount Paid (UGX)</th> 
                        <th>Amount Due (UGX)</th> 
                       <!--  <th></th> -->
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
 </div>