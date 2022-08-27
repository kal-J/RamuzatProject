<div class="modal fade" id="summary_report_modal" role="dialog" aria-labelledby="printLayoutTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 80vw; width: 80vw">
        <div class="modal-content">
            <div class="d-flex flex-row-reverse my-4 mx-5">
                <button onclick="printJS({printable: 'printable_summary_report', type: 'html', targetStyles: ['*'], documentTitle: 'Summary-Report'})" type="button" class="btn btn-primary">Print</button>
                <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Close</button>
            </div>
            <div class="modal-body">
                <div id="printable_summary_report" class="w-100">

                    <div class="row d-flex flex-column align-items-center mx-auto w-100 mb-4">
                        <img style="height: 50px;" src="<?php echo base_url("uploads/organisation_" . $_SESSION['organisation_id'] . "/logo/" . $org['organisation_logo']);  ?>" alt="logo">

                        <div class="mx-auto text-center w-100 mt-2">
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

                    <div class="row d-flex justify-content-center align-items-center w-100 mx-2">
                        <h3 class="w-100 d-flex justify-content-center text-success" data-bind="text: 'Summary Report From ' + moment($root.start_datev(),'MMM Do YYYY').format('MMMM Do YYYY') + '&nbsp;  to  &nbsp;' + moment($root.end_datev(),'MMM Do YYYY').format('MMMM Do YYYY')"></h3>
                    </div>

                    <div class="row my-3">
                        <?php if (in_array('4', $modules)) { ?>
                            <button type="button" class="btn btn-info my-2 mr-2">
                                Active Loans <span class="badge badge-light" data-bind="text: $root.active_loans_count()"></span>
                            </button>
                            <button type="button" class="btn btn-info my-2 mr-1">
                                Closed Loans <span class="badge badge-light" data-bind="text: $root.closed_loans_count()"></span>
                            </button>
                        <?php } ?>

                        <table class="table table-sm table-bordered" id="balancesheet" width="100%">
                            <tbody>
                                <?php if (in_array('4', $modules)) { ?>
                                    <tr>
                                        <td>
                                            <h3>Expected Amounts</h3>
                                        </td>
                                    </tr>
                                    <tr style="background-color: #fafafc;">
                                        <td style="padding-left:40px; font-weight:bold;">
                                            Loan Principal
                                        </td>
                                        <td>
                                            <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text:curr_format(round($root.expected_principal() , 2) )">0</span></h4>
                                        </td>
                                    </tr>
                                    <tr style="background-color: #fafafc;">
                                        <td style="padding-left:40px; font-weight:bold;">
                                            Loan Interest
                                        </td>
                                        <td>
                                            <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: curr_format( round($root.expected_interest(), 2))">0</span></h4>
                                        </td>
                                    </tr>

                                    <tr class="table-primary">
                                        <th>Total Expected Amounts </th>
                                        <th> <span> <span data-bind="text:curr_format(round(parseFloat($root.expected_interest()) + parseFloat($root.expected_principal()) ,2))">0</span> </span> </th>
                                    </tr>

                                <?php } ?>

                                <tr>
                                    <td>
                                        <h3>Collected Amounts</h3>
                                    </td>
                                </tr>

                                <?php if (in_array('4', $modules)) { ?>
                                    <tr style="background-color: #fafafc;">
                                        <td style="padding-left:40px; font-weight:bold;">
                                            Loan Principal
                                        </td>
                                        <td>
                                            <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: curr_format(round($root.collected_principal() , 2) )">0</span></h4>
                                        </td>
                                    </tr>
                                    <tr style="background-color: #fafafc;">
                                        <td style="padding-left:40px; font-weight:bold;">
                                            Loan Interest
                                        </td>
                                        <td>
                                            <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: curr_format(round($root.collected_interest() , 2) )">0</span></h4>
                                        </td>
                                    </tr>
                                    <tr style="background-color: #fafafc;">
                                        <td style="padding-left:40px; font-weight:bold;">
                                            Loan Penalty
                                        </td>
                                        <td>
                                            <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: curr_format(round($root.collected_penalty() , 2) )">0</span></h4>
                                        </td>
                                    </tr>
                                    <tr style="background-color: #fafafc;">
                                        <td style="padding-left:40px; font-weight:bold;">
                                            Loan Fees
                                        </td>
                                        <td>
                                            <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: $root.loan_charges() ? curr_format( round($root.loan_charges(), 2) ) : 0">0</span></h4>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <?php if (in_array('6', $modules)) { ?>

                                    <tr style="background-color: #fafafc;">
                                        <td style="padding-left:40px; font-weight:bold;">
                                            Savings Deposits
                                        </td>
                                        <td>
                                            <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: curr_format( round($root.savings_deposits(), 2) )">0</span></h4>
                                        </td>
                                    </tr>
                                    <tr style="background-color: #fafafc;">
                                        <td style="padding-left:40px; font-weight:bold;">
                                            Savings Fees
                                        </td>
                                        <td>
                                            <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: $root.savings_charges() ? curr_format(round($root.savings_charges(),2)) : 0">0</span></h4>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr class="table-primary">
                                    <th>Total Collected Amounts <span class="text-xs">(Excluding any other incomes not listed above)</span> </th>
                                    <th> <span> <span data-bind="text: (() => {
                                    let loans = '<?php echo in_array('4', $modules); ?>';
                                    let savings = '<?php echo in_array('6', $modules); ?>';
                                    let savings_sum = 0;
                                    let loans_sum = 0;
                                    if(savings) savings_sum = (parseFloat($root.savings_deposits()) + parseFloat($root.savings_charges() ? $root.savings_charges() : 0));
                                    if(loans) loans_sum = (parseFloat($root.collected_principal()) + parseFloat($root.collected_interest()) + parseFloat($root.collected_penalty()) + parseFloat($root.loan_charges() ? $root.loan_charges() : 0));

                                    return curr_format(round(savings_sum + loans_sum, 2));

                                })()">0</span> </span> </th>

                                </tr>

                                <?php if (in_array('4', $modules)) { ?>

                                    <tr>
                                        <td>
                                            <h3>Disbursed Amounts</h3>
                                        </td>
                                    </tr>
                                    <tr style="background-color: #fafafc;">
                                        <td style="padding-left:40px; font-weight:bold;">
                                            Loan Principal
                                        </td>
                                        <td>
                                            <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: curr_format(round($root.principal_disbursed(),2))">0</span></h4>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <tr>
                                    <td>
                                        <h3>Withdrawn Amounts</h3>
                                    </td>
                                </tr>
                                <?php if (in_array('6', $modules)) { ?>
                                    <tr style="background-color: #fafafc;">
                                        <td style="padding-left:40px; font-weight:bold;">
                                            Savings Withdrawn
                                        </td>
                                        <td>
                                            <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: curr_format(round($root.savings_withdraws(),2))">0</span></h4>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if (in_array('12', $modules)) { ?>
                                    <tr style="background-color: #fafafc;">
                                        <td style="padding-left:40px; font-weight:bold;">
                                            Shares Withdrawn
                                        </td>
                                        <td>
                                            <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: curr_format(round($root.share_withdraws(),2))">0</span></h4>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <tr class="table-primary">
                                    <th>Total Withdraws</th>
                                    <th> <span> <span data-bind="text: (() => {
                                    let shares = '<?php echo in_array('12', $modules); ?>';
                                    let savings = '<?php echo in_array('6', $modules); ?>';
                                    let shares_w = 0;
                                    let savings_w = 0;
                                    if(shares) shares_w = parseFloat($root.share_withdraws());
                                    if(savings) savings_w = parseFloat($root.savings_withdraws());

                                    return curr_format(round(shares_w+savings_w, 2));

                                })()">0</span> </span> </th>
                                </tr>

                                <tr>
                                    <td>
                                        <h3></h3>
                                    </td>
                                </tr>

                                <tr class="table-primary">
                                    <th>Total Expenses</th>
                                    <th> <span> <span data-bind="text:curr_format(round(
                                    parseFloat($root.expenses())
                                    ,2))">0</span> </span> </th>
                                </tr>

                            </tbody>
                        </table>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button onclick="printJS({printable: 'printable_summary_report', type: 'html', targetStyles: ['*'], documentTitle: 'Summary-Report'})" type="button" class="btn btn-primary">Print</button>
                </div>
            </div>
        </div>
    </div>
</div>