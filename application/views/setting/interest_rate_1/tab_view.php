<div class="tab-pane" style="margin-top:10px;" id="tab_interest_payment_point">
    <div class="ibox-content">
        <div class="pull-right">
        <?php if(in_array('1', $privileges)){ ?>
            <a data-toggle="modal" class="btn btn-primary  btn-sm" href="#add_interest_payment_method_modal"><i class="fa fa-plus"></i> New Tax Rate</a>
        <?php } ?>
        </div>
        <h3><center>Interest rates</center></h3>
        <div class="hr-line-dashed"></div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="tblInterest_Payment_points">
                <thead>
                    <tr>
                        <th>Rate</th>
                        <th>Tax Rate Source</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Note</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
