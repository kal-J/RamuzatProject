<div role="tabpanel" id="tab-figures" class="tab-pane active loans">
    <div class="panel-body" style="background: #F3F3F4"><br>
        <div class="row">
            <div class="col-lg-3">
                <h2>Loans Performance</h2>
            </div>
            <div class="float-right col-lg-9">
                <div class="row">
                    <div class="col-lg-4"></div>
                    <label class="col-lg-1 col-form-label">From<span class="text-danger">*</span></label>
                    <div class="col-lg-3 form-group">
                        <div class="input-group date">
                            <input class="form-control" autocomplete="off" required name="start_date" data-bind="datepicker: $root.start_date,textInput:$root.start_date, event:{ change: $root.updateData}" type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>

                    <label class="col-lg-1 col-form-label">To<span class="text-danger">*</span></label>
                    <div class="col-lg-3 form-group">
                        <div class="input-group date">
                            <input class="form-control" autocomplete="off" required name="end_date" data-bind="datepicker: $root.end_date,textInput:$root.end_date, event:{ change: $root.updateData}" type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <?php if (in_array('6', $report_privilege)) { ?>
            <div class="row d-flex flex-row-reverse mx-1">
                <a href="#general_loan_book_modal" data-toggle="modal" class="btn btn-primary btn-sm"> <i class="fa fa-print fa-2x"></i> </a>
            </div>

        <?php } ?>
        <br>
        <div class="row">

            <div class="col-lg-4">
                <div class="ibox ">
                    <div class="ibox-title">
                        <span class="label label-info float-right">Active Ones</span>
                        <h5>Active Loans</h5>
                    </div>
                    <div class="ibox-content" data-bind="with: loan_count_active">
                        <h1 class="no-margins" data-bind="text: loan_count">0</h1>

                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox ">
                    <div class="ibox-title">
                        <span class="label label-success float-right">Cash Out</span>
                        <h5>Principal Disbursed</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins" data-bind="text:principal_disbursed()?curr_format(round(principal_disbursed(),2)*1):0">0</h1>

                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox " data-bind="with: amount_paid">
                    <div class="ibox-title">
                        <span class="label label-success float-right">Recoveries</span>
                        <h5>Principal Collected</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins" data-bind="text:already_principal_amount?curr_format(round(already_principal_amount,2)*1):0">0</h1>

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <div class="ibox " data-bind="with: amount_paid">
                    <div class="ibox-title">
                        <span class="label label-success float-right">Recoveries</span>
                        <h5>Interest Collected</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins" data-bind="text:already_interest_amount?curr_format(round(already_interest_amount,2)*1):0">0</h1>

                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox " data-bind="with: amount_paid">
                    <div class="ibox-title">
                        <span class="label label-success float-right">Recoveries</span>
                        <h5>Penalties Collected</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins" data-bind="text:already_paid_penalty?curr_format(round(already_paid_penalty,2)*1):0">0</h1>

                    </div>
                </div>
            </div>

        </div>

        <!-- Charts and other graphs -->
        <!-- <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Graphical Comparison</h5>
                        <div class="float-right">
                            <div class="btn-group">
                                <h3>Other Statistics</h3>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-9">
                                <div class="flot-chart">
                                    <div class="flot-chart-content" id="pie_chart"></div>
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
        </div>  -->

        <!-- Loan portfolio -->

        <div class="row">

            <div class="col-lg-4">
                <div class="ibox ">
                    <div class="ibox-title">
                        <!-- <span class="label label-success float-right">Complete Application</span> -->
                        <h5>Gross loan portfolio</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins" data-bind="text:gross_loan_portfolio()?curr_format(round(gross_loan_portfolio(),2)*1):0">0</h1>

                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox ">
                    <div class="ibox-title">
                        <!-- <span class="label label-success float-right">Complete Application</span> -->
                        <h5>Portfolio pending approval</h5>
                    </div>
                    <div class="ibox-content" data-bind="with:loan_count_pend_approval">
                        <h1 class="no-margins" data-bind="text:requested_amount?curr_format(round(requested_amount,2)*1):0">0</h1>

                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox ">
                    <div class="ibox-title">
                        <span class="label label-info float-right">Loan Stand</span>
                        <h5>Average loan balance</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins" data-bind="text:totalamount()?curr_format(round(totalamount(),2)):0">0</h1>

                    </div>
                </div>
            </div>
            <!-- <div class="col-lg-3">
                <div class="ibox ">
                    <div class="ibox-title">
                        <span class="label label-primary float-right">Forecast</span>
                        <h5>Projected Loan Interest</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins" data-bind="text:projected_intrest_earnings()?curr_format(round(projected_intrest_earnings(),2)*1):0">0</h1>
                        <div class="stat-percent font-bold" data-bind="css:{'text-success':(parseFloat($root.projected_intrest_earnings_percentage()) >=parseFloat(0)),'text-danger': (parseFloat($root.projected_intrest_earnings_percentage()) < parseFloat(0))}"><span data-bind="text:$root.projected_intrest_earnings_percentage() +'%'">0% </span> <i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.projected_intrest_earnings_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.projected_intrest_earnings_percentage()) < parseFloat(0))}"></i></div>
                        <small>From a month ago</small>
                    </div>
                </div>
            </div> -->
        </div>

        <div class="row">
            <!-- <div class="col-lg-3">
                <div class="ibox ">
                    <div class="ibox-title">
                        <span class="label label-danger float-right">For all </span>
                        <h5>Unpaid Penalty</h5>
                    </div>
                    <div class="ibox-content" data-bind="with:unpaid_penalty">
                        <h1 class="no-margins" data-bind="text:penalty_total?curr_format(round(penalty_total,2)):0">0</h1>
                        <div class="stat-percent font-bold" data-bind="css:{'text-danger':(parseFloat($root.penalty_percentage()) >=parseFloat(0)),'text-success': (parseFloat($root.penalty_percentage()) < parseFloat(0))}"><span data-bind="text:$root.penalty_percentage() +'%'"></span> <i class="fa" data-bind="css:{'fa-level-up': (parseFloat($root.penalty_percentage()) >=parseFloat(0)),'fa-level-down': (parseFloat($root.penalty_percentage()) < parseFloat(0))}"></i></div>
                        <small>From a month ago</small>
                    </div>
                </div>
            </div> -->
            <div class="col-lg-4">
                <div class="ibox ">
                    <div class="ibox-title">
                        <span class="label label-danger float-right">In arrear-Principal</span>
                        <h5>Value at Risk</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins" data-bind="text:value_at_risk()?curr_format(round(value_at_risk(),2)*1):0">0</h1>

                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ibox ">
                    <div class="ibox-title">
                        <span class="label label-danger float-right">In arrears-Principal%</span>
                        <h5>Portifolio at Risk</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins" data-bind="text:portfolio_at_risk()?round(portfolio_at_risk(),2)+'%':0+'%'">0</h1>

                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox ">
                    <div class="ibox-title">
                        <span class="label label-danger float-right">In arrear-Interest</span>
                        <h5>Interest in suspense</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins" data-bind="text:intrest_in_suspense()?curr_format(round(intrest_in_suspense(),2)*1):0">0</h1>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php $this->load->view('reports/loans/general_loan_book_modal'); ?>