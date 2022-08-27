<section>
    <div class="modal fade" id="general_loan_book_modal" tabindex="-1" role="dialog" aria-labelledby="printLayoutTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 65%; width: 65%;">
            <div class="modal-content">
                <div class="d-flex flex-row-reverse mt-4 ml-4 mr-4">
                    <button
                        onclick="(() => {
                            document.title = 'General-Loan-Book';
                            printJS({printable: 'printable_general_loan_book', type: 'html', targetStyles: ['*'], documentTitle: 'General-Loan-Book'})})()"
                        type="button" class="btn btn-primary mx-1">Print</button>
                    <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Close</button>
                </div>
                <div class="modal-body">
                    <div id="printable_general_loan_book">
                        <div class="d-flex flex-column align-items-center mx-auto w-100">
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
                            <h3 class="text-center text-success mb-4">
                                General Loan Book
                            </h3>
                        </div>

                        <div>
                            <div class="row">
                            <div class="col-lg-6">
                                <div class="ibox ">
                                    <div class="ibox-title">
                                        <h5>Loan Apps</h5>
                                        <span class="label label-primary ">Applications</span>
                                        
                                    </div>
                                    <div class="ibox-content" data-bind="with: loan_count_partial">
                                        <h1 class="no-margins" data-bind="text: loan_count">0</h1>
                                        <div class="stat-percent font-bold" data-bind="css:{'text-success':(parseFloat($root.app_percentage()) >=parseFloat(0)),'text-danger': (parseFloat($root.app_percentage()) < parseFloat(0))}"><span data-bind="text:$root.app_percentage() +'%'">0% </span><i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.app_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.app_percentage()) < parseFloat(0))}"></i></div>
                                        <small>From a month ago</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="ibox ">
                                    <div class="ibox-title">
                                        <h5>Loans Disbursed</h5>
                                        <span class="label label-info">Active Ones</span>
                                    </div>
                                    <div class="ibox-content" data-bind="with: loan_count_active">
                                        <h1 class="no-margins" data-bind="text: loan_count">0</h1>
                                        <div class="stat-percent font-bold text-info" data-bind="css:{'text-success':(parseFloat($root.active_percentage()) >=parseFloat(0)),'text-danger':(parseFloat($root.active_percentage()) < parseFloat(0))}"><span data-bind="text:$root.active_percentage() +'%'">0% </span><i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.active_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.active_percentage()) < parseFloat(0))}"></i></div>
                                        <small>From a month ago</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="ibox ">
                                    <div class="ibox-title">
                                        <h5>Principal Disbursed</h5>
                                        <span class="label label-success">Cash Out</span>
                                    </div>
                                    <div class="ibox-content">
                                        <h1 class="no-margins" data-bind="text:principal_disbursed()?curr_format(round(principal_disbursed(),2)*1):0">0</h1>
                                        <div class="stat-percent font-bold text-navy" data-bind="css:{'text-success':(parseFloat($root.principal_disbursed_percentage()) >=parseFloat(0)),'text-danger': (parseFloat($root.principal_disbursed_percentage()) < parseFloat(0))}"><span data-bind="text:$root.principal_disbursed_percentage() +'%'">0% </span> <i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.principal_disbursed_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.principal_disbursed_percentage()) < parseFloat(0))}"></i></div>
                                        <small>From a month ago</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="ibox " data-bind="with: amount_paid">
                                    <div class="ibox-title">
                                        <h5>Principal Collected</h5>
                                        <span class="label label-success">Recoveries</span>
                                    </div>
                                    <div class="ibox-content">
                                        <h1 class="no-margins" data-bind="text:already_principal_amount?curr_format(round(already_principal_amount,2)*1):0">0</h1>
                                        <div class="stat-percent font-bold text-navy" data-bind="css:{'text-success':(parseFloat($root.principal_collected_percentage()) >=parseFloat(0)),'text-danger':(parseFloat($root.principal_collected_percentage()) < parseFloat(0))}"><span data-bind="text:$root.principal_collected_percentage() +'%'">0%</span> <i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.principal_collected_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.principal_collected_percentage()) < parseFloat(0))}"></i></div>
                                        <small>From a month ago</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts and other graphs -->
                        <div class="m-2">
                            <h5>
                                Graphical Comparison & Other Statistics
                            </h5>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                            
                                <div class="ibox ">
                                    
                                    <div class="ibox-content">
                                        <div class="row">
                                            <div class="col-lg-9">
                                                <div class="flot-chart">
                                                <div class="flot-chart-content" id="pie_chart_modal"></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <ul class="stat-list">
                                                    <li>
                                                        <h2 class="no-margins " data-bind="text:extraordinary_writeoff()?curr_format(round(extraordinary_writeoff(),2)*1):0">0</h2>
                                                        <small>Extraordinary Write off</small>
                                                        <div class="stat-percent" data-bind="css:{'text-success':(parseFloat($root.writeoff_percentage()) >=parseFloat(0)),'text-danger':(parseFloat($root.writeoff_percentage()) < parseFloat(0))}"><span data-bind="text:$root.writeoff_percentage() +'%'">0%</span><i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.writeoff_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.writeoff_percentage()) < parseFloat(0))}"></i></div>
                                                        <div class="progress progress-mini">
                                                            <div data-bind="style:{ width: $root.writeoff_percentage()+'%' }" class="progress-bar"></div>
                                                        </div>
                                                    </li>
                                                    <li data-bind="with:amount_paid">
                                                        <h2 class="no-margins" data-bind="text:already_interest_amount?curr_format(round(already_interest_amount,2)*1):0">0</h2>
                                                        <small>Interest from loans</small>
                                                        <div class="stat-percent" data-bind="css:{'text-success':(parseFloat($root.loan_interest_percentage()) >=parseFloat(0)),'text-danger':(parseFloat($root.loan_interest_percentage()) < parseFloat(0))}"><span data-bind="text:$root.loan_interest_percentage() +'%'">0%</span> <i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.loan_interest_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.loan_interest_percentage()) < parseFloat(0))}"></i></div>
                                                        <div class="progress progress-mini">
                                                            <div data-bind="style:{ width: $root.loan_interest_percentage()+'%' }" class="progress-bar"></div>
                                                        </div>
                                                    </li>
                                                    <li data-bind="with:amount_paid">
                                                        <h2 class="no-margins " data-bind="text:already_paid_penalty?curr_format(round(already_paid_penalty,2)*1):0">0</h2>
                                                        <small>Paid Loan Penalties</small>
                                                        <div class="stat-percent" data-bind="css:{'text-success': (parseFloat($root.paid_penalty_percentage()) >=parseFloat(0)),'text-danger':(parseFloat($root.paid_penalty_percentage()) < parseFloat(0))}"><span data-bind="text:$root.paid_penalty_percentage() +'%'">0%</span> <i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.paid_penalty_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.paid_penalty_percentage()) < parseFloat(0))}"></i></div>
                                                        <div class="progress progress-mini">
                                                            <div data-bind="style:{ width: $root.paid_penalty_percentage()+'%' }" class="progress-bar"></div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Loan portfolio -->

                        <div class="row">

                            <div class="col-lg-12 mt-2 mb-5">
                                <div class="ibox ">
                                    <div>
                                        <!-- <span class="label label-success float-right">Complete Application</span> -->
                                        <h5>Gross loan portfolio</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <h1 class="no-margins" data-bind="text:gross_loan_portfolio()?curr_format(round(gross_loan_portfolio(),2)*1):0">0</h1>
                                        <div class="stat-percent font-bold" data-bind="css:{'text-success':(parseFloat($root.gross_loan_portfolio_percentage()) >=parseFloat(0)),'text-danger':(parseFloat($root.gross_loan_portfolio_percentage()) < parseFloat(0))}"><span data-bind="text:$root.gross_loan_portfolio_percentage() +'%'">0% </span> <i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.gross_loan_portfolio_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.gross_loan_portfolio_percentage()) < parseFloat(0))}"></i></div>
                                        <small>From a month ago</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-5">
                                <div class="ibox ">
                                    <div>
                                        <!-- <span class="label label-success float-right">Complete Application</span> -->
                                        <h5>Portfolio pending approval</h5>
                                    </div>
                                    <div class="ibox-content" data-bind="with:loan_count_pend_approval">
                                        <h1 class="no-margins" data-bind="text:requested_amount?curr_format(round(requested_amount,2)*1):0">0</h1>
                                        <div class="stat-percent font-bold" data-bind="css:{'text-success':(parseFloat($root.portfolio_pending_percentage()) >=parseFloat(0)),'text-danger':(parseFloat($root.portfolio_pending_percentage()) < parseFloat(0))}"><span data-bind="text:$root.portfolio_pending_percentage() +'%'">0% </span> <i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.portfolio_pending_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.portfolio_pending_percentage()) < parseFloat(0))}"></i></div>
                                        <small>From a month ago</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-2">
                                <div class="ibox ">
                                    <div>
                                        <h5>Average loan balance</h5>
                                        <span class="label label-info">Loan Stand</span>
                                    </div>
                                    <div class="ibox-content">
                                        <h1 class="no-margins" data-bind="text:totalamount()?curr_format(round(totalamount(),2)):0">0</h1>
                                        <div class="stat-percent font-bold" data-bind="css:{'text-success':(parseFloat($root.average_loan_balance_percentage()) >=parseFloat(0)),'text-danger': (parseFloat($root.average_loan_balance_percentage()) < parseFloat(0))}"><span data-bind="text:$root.average_loan_balance_percentage() +'%'">0% </span> <i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.average_loan_balance_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.average_loan_balance_percentage()) < parseFloat(0))}"></i></div>
                                        <small>From a month ago</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-2">
                                <div class="ibox ">
                                    <div>
                                        <h5>Projected Loan Interest</h5>
                                        <span class="label label-primary">Forecast</span>
                                    </div>
                                    <div class="ibox-content">
                                        <h1 class="no-margins" data-bind="text:projected_intrest_earnings()?curr_format(round(projected_intrest_earnings(),2)*1):0">0</h1>
                                        <div class="stat-percent font-bold" data-bind="css:{'text-success':(parseFloat($root.projected_intrest_earnings_percentage()) >=parseFloat(0)),'text-danger': (parseFloat($root.projected_intrest_earnings_percentage()) < parseFloat(0))}"><span data-bind="text:$root.projected_intrest_earnings_percentage() +'%'">0% </span> <i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.projected_intrest_earnings_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.projected_intrest_earnings_percentage()) < parseFloat(0))}"></i></div>
                                        <small>From a month ago</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 mt-2">
                                <div class="ibox ">
                                    <div>
                                        <h5>Unpaid Penalty</h5>
                                        <span class="label label-danger">For all </span>
                                    </div>
                                    <div class="ibox-content" data-bind="with:unpaid_penalty">
                                        <h1 class="no-margins" data-bind="text:penalty_total?curr_format(round(penalty_total,2)):0">0</h1>
                                        <div class="stat-percent font-bold" data-bind="css:{'text-danger':(parseFloat($root.penalty_percentage()) >=parseFloat(0)),'text-success': (parseFloat($root.penalty_percentage()) < parseFloat(0))}"><span data-bind="text:$root.penalty_percentage() +'%'"></span> <i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.penalty_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.penalty_percentage()) < parseFloat(0))}"></i></div>
                                        <small>From a month ago</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-2 mb-5 pb-5">
                                <div class="ibox ">
                                    <div>
                                        <h5>Value at Risk</h5>
                                        <span class="label label-danger">In arrear-Principal</span>
                                        
                                    </div>
                                    <div class="ibox-content">
                                        <h1 class="no-margins" data-bind="text:value_at_risk()?curr_format(round(value_at_risk(),2)*1):0">0</h1>
                                        <div class="stat-percent font-bold" data-bind="css:{'text-danger':(parseFloat($root.value_at_risk_percentage()) >=parseFloat(0)),'text-success': (parseFloat($root.value_at_risk_percentage()) < parseFloat(0))}"><span data-bind="text:$root.value_at_risk_percentage() +'%'">0% </span> <i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.value_at_risk_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.value_at_risk_percentage()) < parseFloat(0))}"></i></div>
                                        <small>From a month ago</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-5 pt-5">
                                <div class="ibox ">
                                    <div>
                                        <h5>Portifolio at Risk</h5>
                                        <span class="label label-danger">In arrears-Principal%</span>
                                    </div>
                                    <div class="ibox-content">
                                        <h1 class="no-margins" data-bind="text:portfolio_at_risk()?round(portfolio_at_risk(),2)+'%':0+'%'">0</h1>
                                        <div class="stat-percent font-bold" data-bind="css:{'text-danger':(parseFloat($root.portfolio_at_risk_percentage()) >=parseFloat(0)),'text-success': (parseFloat($root.portfolio_at_risk_percentage()) < parseFloat(0))}"><span data-bind="text:$root.portfolio_at_risk_percentage() +'%'">0% </span> <i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.portfolio_at_risk_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.portfolio_at_risk_percentage()) < parseFloat(0))}"></i></div>
                                        <small>From a month ago</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-2">
                                <div class="ibox ">
                                    <div>
                                        <h5>Interest in suspense</h5>
                                        <span class="label label-danger">In arrear-Interest</span>
                                    </div>
                                    <div class="ibox-content">
                                        <h1 class="no-margins" data-bind="text:intrest_in_suspense()?curr_format(round(intrest_in_suspense(),2)*1):0">0</h1>
                                        <div class="stat-percent font-bold" data-bind="css:{'text-danger':(parseFloat($root.intrest_in_suspense_percentage()) >=parseFloat(0)),'text-success': (parseFloat($root.intrest_in_suspense_percentage()) < parseFloat(0))}"><span data-bind="text:$root.intrest_in_suspense_percentage() +'%'">0% </span> <i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.intrest_in_suspense_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.intrest_in_suspense_percentage()) < parseFloat(0))}"></i></div>
                                        <small>From a month ago</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button onclick="(() => {
                            document.title = 'General-Loan-Book';
                            printJS({printable: 'printable_general_loan_book', type: 'html', targetStyles: ['*'], documentTitle: 'General-Loan-Book'})})()" type="button" class="btn btn-primary">Print</button>
                    </div>
                </div>
            </div>
        </div>
</section>