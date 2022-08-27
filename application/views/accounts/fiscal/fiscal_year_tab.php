<div role="tabpanel" id="tab-fiscal" class="tab-pane">
    <div class="hr-line-dashed"></div>
    <?php if(in_array('1', $fiscal_privilege)){ ?>
    <!-- <div><a data-toggle="modal" href="#fiscal-modal" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus-circle"></i> New Fiscal Year</a></div> -->
    <?php } ?>
    <div class="table-responsive">
    <br>
    <table id="tblFiscal_year" class="table table-striped table-hover" width="100%">
        <thead>
            <tr>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Close / Rollback</th>
                <th>Status</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        </table>
    </div><!-- /.table-responsive-->
</div>

