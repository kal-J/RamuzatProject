<div role="tabpanel" id="tab-expense_line" class="tab-pane ">
    <h3><center>Payments<?php if(isset($bill_payment_detail)): ?> items<?php endif; ?></center></h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table id="tblBill_payment"  border="0" class="table-bordered display compact nowrap" style="width:100%">
            <thead class="thead-light">
                <tr>
                        <?php if(isset($bill_payment_detail)): ?>
                    <th>Bill Ref#</th>
                    <th>Liability Account</th>
                    <th>Billing Date</th>
                        <?php else: ?>
                    <th>Payment Ref#</th>
                    <th>Payment Date</th>
                        <?php endif; ?>
                    <th>Narrative</th>
                    <th>Amount</th>
                    <!--th>Discount</th-->
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th <?php if(isset($bill_payment_detail)): ?>colspan="2"<?php endif; ?>>&nbsp;</th>
                    <th colspan="2">Totals (UGX)</th>
                    <th>0</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
        </table>
    </div><!-- /.table-responsive-->
</div>
