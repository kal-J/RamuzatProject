<!-- <div role="tabpanel" id="tab-bs_printable" class="tab-pane"> -->
<div class="panel-body">
    <div class="col-lg-12">
        <img src="<?php echo base_url(); ?>images/loading.gif" id="gif" style="display: block; margin: 0 auto; width: 30px; visibility: hidden;">

        <?php if (in_array('6', $report_privilege)) { ?>
            <div class="pull-right">
                <a href="#bal_sheet_modal" data-toggle="modal" class="btn btn-primary btn-sm mb-1"> <i class="fa fa-print fa-2x"></i> </a>
            </div>
        <?php } ?>
        <div>
            <table class="table table-sm table-bordered" id="balancesheet" width="100%">
                <tbody>
                    <tr style="background-color: #1c84c6; color: #fff;">
                        <td colspan="2">
                            <h4>
                                <center>Statement of Financial Position &nbsp; &nbsp; &nbsp;- &nbsp;<span data-bind="text:moment(end_date(),'X').format('DD-MMMM-YYYY')"></span></center>
                            </h4>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h3>Assets</h3>
                        </td>
                    </tr>
                    <!-- ko foreach: assets -->
                    <!-- ko if: ((parseFloat(amount)!==parseInt(0)) &&(parseFloat(cat)===parseInt(1))) -->
                    <tr style="background-color: #fafafc;">
                        <td style="padding-left:40px; font-weight:bold;" data-bind="text: '['+account_code+'] '+account_name"></td>
                        <td>
                            <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text:curr_format(round(amount,2))">0</span></h4>
                        </td>
                    </tr>
                    <!-- /ko -->
                    <!-- ko if: ((parseFloat(amount)!==parseInt(0)) &&(parseFloat(cat)===parseInt(0))) -->
                    <tr>
                        <td style="padding-left:80px;"><a data-bind="attr: {href:'<?php echo site_url("accounts/view/"); ?>'+id}, text:'['+account_code+'] '+account_name"></a></td>
                        <td>
                            <h4 class="no-margins"><span class="text-success" data-bind="text:curr_format(round(amount,2))">0</span></h4>
                        </td>
                    </tr>
                    <!-- /ko -->
                    <!-- /ko -->

                    <tr class="table-primary">
                        <th>Total Assets </th>
                        <th> <span data-bind="with:print_sums"> <span data-bind="text:curr_format(round(total_assets,2))">0</span> </span> </th>
                    </tr>

                    <tr>
                        <td>
                            <h3>Liabilities and Stockholder's Equity</h3>
                        </td>
                    </tr>
                    <!-- ko foreach: liab_equity -->
                    <!-- ko if: ((parseFloat(amount)!==parseInt(0)) &&(parseFloat(cat)===parseInt(1))) -->
                    <tr style="background-color: #fafafc;">
                        <td style="padding-left:40px; font-weight:bold;" data-bind="text: '['+account_code+'] '+account_name"></td>
                        <td>
                            <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text:curr_format(round(amount,2))">0</span></h4>
                        </td>
                    </tr>
                    <!-- /ko -->
                    <!-- ko if: ((parseFloat(amount)!==parseInt(0)) &&(parseFloat(cat)===parseInt(0))) -->
                    <tr>
                        <td style="padding-left:80px;"><a data-bind="attr: {href:'<?php echo site_url("accounts/view/"); ?>'+id}, text:'['+account_code+'] '+account_name"></a></td>
                        <td>
                            <h4 class="no-margins"><span class="text-success" data-bind="text:curr_format(round(amount,2))">0</span></h4>
                        </td>
                    </tr>
                    <!-- /ko -->
                    <!-- /ko -->
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <!-- ko with: print_sums -->
                    <tr style="background-color: #fafcdc;" data-bind="visible: parseFloat(net_profit_loss)>0">
                        <th style="padding-left:40px;">Profit/Loss</th>
                        <th>
                            <span> <span data-bind="text:curr_format(round(net_profit_loss,2))">0</span> </span>
                        </th>
                    </tr>

                    <tr class="table-primary">
                        <th>Total Liabilities & Stockholder's Equity </th>
                        <th> <span> <span data-bind="text:curr_format(round(parseFloat(equity_side+net_profit_loss),2))">0</span> </th>
                    </tr>
                    <!-- /ko -->

                </tbody>
            </table>
        </div>

    </div>
</div>
<!-- </div> -->

<section>
    <?php $this->load->view('reports/balance_sheet/print_out_modal'); ?>
</section>