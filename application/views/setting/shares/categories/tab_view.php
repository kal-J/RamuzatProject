<div role="tabpanel" id="tab-share_category" class="tab-pane">
    <div class="panel-body">
        <div>
            <h3 class="text-center">
                Share Categories
                    <?php if (in_array('1', $share_issuance_privilege)) { ?>
                        <a data-toggle="modal" href="#add_share_category-modal" class="btn btn-sm btn-primary pull-right add-record-btn"><i class="fa fa-plus-circle"></i> Add Share Category</a>
                    <?php } ?>
            </h3>
        </div>
        <div class="hr-line-dashed"></div>
        <div class="table-responsive">
            <table id="tblShare_category" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Description</th>
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
