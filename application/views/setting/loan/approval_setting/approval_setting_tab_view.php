<div role="tabpanel" id="tab-approval_setting" class="tab-pane">
    <div class="hr-line-dashed"></div>
    <?php if(in_array('1', $approval_privilege)){ ?>
    <div><a data-toggle="modal" href="#add_approval_setting-modal" class="btn btn-sm btn-default pull-right"><i class="fa fa-plus-circle"></i> New Approval Setting</a></div>
    <?php } ?>
    <h3><center>Approval setting</center></h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table id="tblApproval_setting" class="table table-striped table-bordered table-hover m-t-md" width="100%">
            <thead>
                <tr>
                      <th>Min. Amount (UGX)</th>
                      <th>Max. Amount (UGX)</th>
                      <th>Min. Approvals</th>
                      <th>No. of attached staffs </th>
                      <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- /.table-responsive-->
</div>

