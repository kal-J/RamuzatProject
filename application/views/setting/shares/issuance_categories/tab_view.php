<div role="tabpanel" id="tab-share_issuance_category" class="tab-pane">
    <div class="panel-body">
        <div>
            <h3 class="text-center">
                Share Prices
                    <?php if (in_array('1', $share_issuance_privilege)) { ?>
                        <a data-toggle="modal" href="#add_share_issuance_category-modal" class="btn btn-sm btn-primary pull-right add-record-btn"><i class="fa fa-plus-circle"></i> New Share Issuance</a>
                    <?php } ?>
            </h3>
        </div>
        <div class="hr-line-dashed"></div>
        <div class="table-responsive">
            <table id="tblShare_issuance_category" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                <thead>
                    <tr>
                        <th>Price Per Share</th>
                        <th>Comment</th>
                        <th>Status</th>
                        <th>&nbsp; </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div><!-- /.table-responsive-->
    </div>
    </div>