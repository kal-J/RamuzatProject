<div role="tabpanel" id="tab-locked_month" class="tab-pane">
    <div class="hr-line-dashed"></div>
   <?php if (in_array('1', $fiscal_privilege)) { ?>
        <a data-toggle="modal" href="#add_month-modal" class="btn btn-sm btn-primary pull-right "><i class="fa fa-plus-circle"></i> Add New Month</a>
    <?php } ?>
    <div class="table-responsive">
    <br>
    <table id="tblFiscal_month" class="table table-striped table-bordered table-hover m-t-md" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Month</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        </table>
    </div><!-- /.table-responsive-->
</div>

