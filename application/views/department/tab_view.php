<div role="tabpanel" id="tab-department" class="tab-pane">
    <div class="hr-line-dashed"></div>
    <?php if(in_array('1', $privileges)){ ?>
    <div><a data-toggle="modal" href="#add_department-modal" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus-circle"></i> New Department</a></div>
    <?php } ?>
    <div class="table-responsive">
        <table id="tblDepartment" class="table table-striped table-bordered table-hover small m-t-md" width="100%">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
