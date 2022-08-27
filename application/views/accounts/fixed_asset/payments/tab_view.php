<div class="panel-body"><br>
    <div class="col-lg-12">
        <p>
            <strong>Payments</strong>
            <?php if (in_array('3', $accounts_privilege)) { ?>
            <!--ko with:fixed_asset_detail-->
                <a data-bind="visible: (parseFloat(purchase_cost)-parseFloat($parent.asset_paid_amount()))>0" data-toggle="modal" href="#add_asset_payment-modal" class="btn btn-sm btn-primary pull-right"><i class="fa fa-edit"></i> Add Payment</a>
            <!--/ko-->
            <?php } ?>
        </p>
        <p data-bind="with: fixed_asset_detail"><strong>Purchase Cost (UGX):</strong> <span data-bind="text: curr_format(purchase_cost*1)"></span>.
            <strong>Total Payments (UGX):</strong> <span data-bind="text: curr_format($parent.asset_paid_amount()*1)"></span>
            <strong>Balance (UGX):</strong> <span data-bind="text: curr_format(parseFloat(purchase_cost)-parseFloat($parent.asset_paid_amount()))"></span>
        </p>
        <div class="hr-line-dashed"></div>
        <div class="table-responsive">
            <table class="table  table-bordered table-hover" id="tblAsset_payment" width="100%" >
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Transn. Date</th>
                        <th>Amount </th>
                        <th>Narrative</th>
                        <th>Action</th> 
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Totals</th>
                        <th>&nbsp;</th> 
                        <th>0 </th>
                        <th colspan="2">&nbsp;</th> 
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php
$this->load->view('accounts/fixed_asset/payments/add_modal');
