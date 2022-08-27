<div class="tab-pane" style="margin-top:10px;" id="tab_saving_fees">
    <div class="panel-body">
        <div>
            <h3>
                <center>Savings Product Fees
                    <?php if (in_array('1', $deposit_product_privilege)) { ?>
                        <a data-toggle="modal" class="btn btn-primary  btn-sm pull-right" href="#add_deposit_product_fee-modal"><i class="fa fa-plus"></i> Savings Product Fee</a>
                    <?php } ?>
                </center>
            </h3>
        </div>
        <div class="hr-line-dashed"></div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="tblSaving_fees">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Calculation Method</th>
                        <th>Amount</th>
                        <th>Fee type</th>
                        <th title="Method for calculating the day when the fee will be applied">Trigger</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
