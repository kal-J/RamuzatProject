<div role="tabpanel" id="tab-payment_engine" class="tab-pane">
    <div class="hr-line-dashed"></div>
    <?php if(in_array('1', $privileges)){ ?>
    <div><a data-toggle="modal" href="#payment_engine-modal" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus-circle"></i> New Payment</a></div>
    <?php } ?>
    <div class="table-responsive">
    <table id="tblPayment_engine" class="table table-striped table-hover" width="100%">
            <thead>
                <tr>
                    <th>Payment</th>
                    <th>Link</th>
                    <th>Status</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- /.table-responsive-->
</div>

