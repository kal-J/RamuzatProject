<div role="tabpanel" id="tab-role" class="tab-pane">
    <div class="panel-body">
        <div>
            <h3 class="text-center">
                Roles and Privileges
                    <?php if (in_array('1', $role_privilege)) { ?>
                        <a data-toggle="modal" href="#add_role-modal" class="btn btn-sm btn-primary pull-right add-record-btn"><i class="fa fa-plus-circle"></i> Add New Role</a>
                    <?php } ?>
            </h3>
        </div>
        <div class="hr-line-dashed"></div>
        <div class="table-responsive">
            <table id="tblRole" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                <thead>
                    <tr>
                        <th>Role Name</th>
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
