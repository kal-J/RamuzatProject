<div class="modal fade" id="bal_sheet_modal" tabindex="-1" role="dialog" aria-labelledby="printLayoutTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 80vw; width: 80vw">
        <div class="modal-content">
            <div class="d-flex flex-row-reverse my-4 mx-5">
                    <button onclick="printJS({printable: 'printable_bal_sheet', type: 'html', targetStyles: ['*'], documentTitle: 'Balance-Sheet'})" type="button" class="btn btn-primary">Print</button>
                        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Close</button>
            </div>
            <div class="modal-body">
                <div id="printable_bal_sheet">
                    <div class="row d-flex flex-column align-items-center mx-auto w-100">
                        <img style="height: 50px;" src="<?php echo base_url("uploads/organisation_" . $_SESSION['organisation_id'] . "/logo/" . $org['organisation_logo']);  ?>" alt="logo">

                        <div class="mx-auto text-center mb-2">
                            <span>
                                <?php echo $org['name']; ?> ,
                            </span>
                            <span>
                                <?php echo $branch['physical_address']; ?>, <?php echo $branch['branch_name']; ?>
                            </span><br>
                            <span>
                                <?php echo $branch['postal_address']; ?> ,
                            </span>
                            <span>
                                <b>Tel:</b> <?php echo $branch['office_phone']; ?>
                            </span>
                            <br><br>
                        </div>
                    </div>

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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button onclick="printJS({printable: 'printable_bal_sheet', type: 'html', targetStyles: ['*'], documentTitle: 'Balance-Sheet'})" type="button" class="btn btn-primary">Print</button>
                </div>
            </div>
        </div>
    </div>