<div role="tabpanel" id="tab-bs_accounts" class="tab-pane active">
    <div class="panel-body">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table tblParent_table" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <!-- ========= START INNER TABLE ASSETS======= -->
                                <table class="table " id="tblAssets" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Assets</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                <table class="table" width="100%">
                                    <tbody>
                                        <!-- ko with: all_totals -->
                                        <tr class="text-danger" data-bind="visible: parseFloat(net_profit_loss)<0">
                                            <th>Net Loss</th>
                                            <th>
                                                <span> <span data-bind="text:curr_format(round(net_profit_loss,2))">0</span> </span>
                                            </th>
                                        </tr>
                                        <!-- /ko -->
                                        <tr class="table-secondary">
                                            <th>Totals</th>
                                            <th> <span data-bind="with:debt_assets"> <span data-bind="text:curr_format(round(slice2.amount,2))">0</span> </span></th>
                                    </tbody>
                                </table>
                                <!-- ==========END ASSETS====== -->
                            </td>
                            <td>
                                <!-- ========= START INNER TABLE lIABILITIES AND EQUITY======= -->
                                <table class="table" id="tblEquityandLiability" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Liabilities and Stockholder's Equity</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                <table class="table" width="100%">
                                    <tbody>
                                        <!-- ko with: all_totals -->
                                        <tr class="text-success" data-bind="visible: parseFloat(net_profit_loss)>0">
                                            <th>Net Profit</th>
                                            <th>
                                                <span> <span data-bind="text:curr_format(round(net_profit_loss,2))">0</span> </span>
                                            </th>
                                        </tr>
                                        <!-- /ko -->
                                        <tr class="table-secondary">
                                            <th>Totals</th>
                                            <th> <span data-bind="with:all_totals"> <span data-bind="text:curr_format(round(parseFloat(equity_side)+parseFloat(net_profit_loss),2))">0</span> </th>
                                        </tr>
                                    </tbody>
                                </table>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>