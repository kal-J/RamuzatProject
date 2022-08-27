<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="box box-solid">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <img src="<?php echo isset($this->pdf) ? "./assets/images/logo.png" : base_url("assets/images/logo.png"); ?>" width="auto" height="50px" class="img-rounded"/>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <?php if($receipt['status']==1): ?>
                            <div class="well-sm">
                                <h3>Thank you for making your payment. It is currently being processed. We will notify you once it has completed</h3>
                            </div>
                            <?php else: ?>
                            <div class="col-md-12">
                                <p class="text-center text-capitalize"><strong> <u>Subscription Payment Receipt</u></strong></p>
                                <div class="table-responsive">
                                    <table class="table table-condensed no-border">
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td class="text-right">TIN:<strong>1001213095</strong></td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td class="text-right">Receipt No: <strong><?php echo $receipt['invoice_no']; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <th><?php $ret_val = mdate("%l, %j %F %Y",$receipt['date_paid']); echo $ret_val; ?></th>
                                            <th>&nbsp;</th>
                                        </tr>
                                        <tr>
                                            <th><?php echo $bond['bond_name']; ?></th>
                                            <th>&nbsp;</th>
                                        </tr>
                                        <tr>
                                            <td>BOND NO. <?php echo $bond['bond_no']; ?></td>
                                            <th>&nbsp;</th>
                                        </tr>
                                        <tr>
                                            <td><?php echo $bond['physical_address']; ?></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <p><strong>BOND STOCK INFORMATION MANAGEMENT SYSTEM SUBSCRIPTION</strong></p>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-condensed table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Cost Item</th>
                                                    <th>Qty</th>
                                                    <th class="text-right">Amount (UG.SHS)</th>
                                                    <td class="text-right text-danger no-border">Plot 112 Bukoto Street</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        Subscription <?php echo mdate("%Y", ($bond['date_created'])); ?> - <?php echo mdate("%Y", ($bond['date_created'] + 86400 * 365)); ?>
                                                    </td>
                                                    <td>1</td>
                                                    <td class="text-right"><?php echo number_format($bond['annual_fee']); ?></td>
                                                    <td class="text-right no-border" rowspan="5">
                                                        <p class="text-danger">
                                                            P.O. Box 36211,<br/>
                                                            Kampala, Uganda
                                                        </p>
                                                        <p class="text-danger">
                                                            T: +256 (0) 701 478 636<br/>
                                                            +256 (0) 701 108 262<br/>
                                                            +256 (0) 702 771 124<br/>
                                                            +256 (0) 772 369 624<br/>
                                                        </p>
                                                        <p class="text-danger">
                                                            E: <a href="mailto:gmt@gmtconsults.com" title="Send email">gmt@gmtconsults.com</a>
                                                        </p>
                                                        <p class="text-danger">
                                                            W: <a href="http://www.gmtconsults.com" title="Website">www.gmtconsults.com</a>
                                                        </p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>VAT @18%</td><td>&nbsp;</td><td class="text-right no-border"><?php echo number_format(0.18 * $bond['annual_fee']); ?></td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Total</th><th>&nbsp;</th><th class="text-right"><?php echo number_format((0.18 * $bond['annual_fee']) + $bond['annual_fee']); ?></th><td class="text-right no-border">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <th>Note:</th><th colspan="2">Annual subscription</th><td class="text-right no-border">&nbsp;</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div><!-- /.col -->
                                </div>
                            </div><!-- /.col-md-9 -->
                            <!--div class="col-md-3 text-right"-->
                            <!--/div>< /.col-md-3 -->
<?php if (!isset($this->pdf)): ?>
                                <div class="col-md-10">
                                </div><!-- /.col-md-10 -->
                                <div class="col-md-2">
                                    <button id="bond_invoice_id"class="btn btn-sm btn-default" bond_id="<?php echo $bond['id']; ?>">Email Receipt</button>
                                </div><!-- /.col-md-2 -->
<?php endif; ?>
<?php endif; ?>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div><!-- /.panel-body -->
            </div><!-- /.panel -->
        </div><!-- /.col-lg-12 -->
    </div>
</div><!-- /.row -->