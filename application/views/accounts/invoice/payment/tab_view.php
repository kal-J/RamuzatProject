<div role="tabpanel" id="tab-income_line" class="tab-pane ">
    <h3><center>Payments<?php if(isset($invoice_payment_detail)): ?> items<?php endif; ?></center></h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table id="tblInvoice_payment"  border="0" class="table-bordered display compact nowrap" style="width:100%">
            <thead class="thead-light">
                <tr>
                        <?php if(isset($invoice_payment_detail)): ?>
                    <th>Invoice Ref#</th>
                    <th>Income Receivable Account</th>
                    <th>Invoice Date</th>
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
                    <th <?php if(isset($invoice_payment_detail)): ?>colspan="2"<?php endif; ?>>&nbsp;</th>
                    <th colspan="2">Totals (UGX)</th>
                    <th>0</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
        </table>
    </div><!-- /.table-responsive-->
</div>
