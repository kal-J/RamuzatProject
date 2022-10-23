<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>
<style>
    .dataTable>thead>tr>th[class*="sort"]:after {
        content: "" !important;
    }

    @keyframes spinner-border {
        to {
            transform: rotate(360deg);
        }
    }

    .spinner-border {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        vertical-align: text-bottom;
        border: .25em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        -webkit-animation: spinner-border .75s linear infinite;
        animation: spinner-border .75s linear infinite;
    }

    .spinner-border-sm {
        height: 1rem;
        border-width: .2em;
    }

    .printing {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 10;
        background-color: #000;
        opacity: 0.5;
        height: 100vh;
        width: 100vw;
        visibility: hidden;
        display: none;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
                <div class="tabs-container">
                    <div class="pull-right add-record-btn">
                        <div id="reportrange" class="reportrange">
                            <i class="fa fa-calendar"></i>
                            <span>December 30, 2018 - Feb 11, 2019</span> <b class="caret"></b>
                        </div>
                    </div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-bind="click: display_table" data-toggle="tab" href="#tab-figures"><i class="fa"></i> General Loan Book </a>
                        </li>




                    </ul>
                    <div class="tab-content">
                        
                        <?php $this->view('loan_reports/loan_book/figures_in_loans'); ?>
                        
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section>
        <?php $this->view('reports/loans/printer_modal'); ?>
    </section>


</div>
<script>
    var dTable = {};
    var TableManageButtons = {};
    var displayed_tab = '';
    var active_condition = 0;
    var credit_officer_id = 0;
    var reportsModel = {};
    var start_date, end_date;
    var active_max_amount = active_min_amount = active_product_id = active_loan_type = active_due_days = next_due_month = next_due_year = "All";
    var closed_max_amount = closed_min_amount = closed_product_id = closed_loan_type = "All";
    var inarrear_max_amount = inarrear_min_amount = inarrear_min_days = inarrear_max_days = inarrear_product_id = inarrear_loan_type = "All";
    $(document).ready(function() {
        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY");
        end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");
        var groupColumn = 0;

        var ReportsModel = function() {
            var self = this;
            self.display_table = function(data, click_event) {
                displayed_tab = $(click_event.target).prop("hash").toString().replace("#", "");
                TableManageButtons.init(displayed_tab);
            };

            //due days filtter
            self.period_condition = ko.observable([{
                "id": 1,
                "condition_name": "More Than"
            }, {
                "id": 2,
                "condition_name": "Less Than"
            }]);
            self.aging_bills = ko.observable();

            self.periods = ko.observable();

            self.end_date = ko.observable("<?php echo date('d-m-Y') ?>");
            self.start_date = ko.observable();

            self.credit_officers = ko.observable(<?php echo (isset($credit_officers)) ? json_encode($credit_officers) : ''; ?>);
            self.credit_officer = ko.observable();
            self.projected_intrest_earnings = ko.observable();
            self.change_in_Portfolio = ko.observable();
            self.gross_loan_portfolio = ko.observable();
            self.principal_disbursed = ko.observable();
            self.amount_paid = ko.observable();
            self.unpaid_penalty = ko.observable();
            self.extraordinary_writeoff = ko.observable();
            self.loan_count_pend_approval = ko.observable();
            self.loan_count_active = ko.observable();
            self.loan_count_locked = ko.observable();
            self.loan_count_arrias = ko.observable();
            self.loan_count_partial = ko.observable();
            self.portfolio_at_risk = ko.observable();
            self.value_at_risk = ko.observable();
            self.intrest_in_suspense = ko.observable();

            self.app_percentage = ko.observable();
            self.active_percentage = ko.observable();
            self.principal_disbursed_percentage = ko.observable();
            self.principal_collected_percentage = ko.observable();
            self.portfolio_pending_percentage = ko.observable();
            self.writeoff_percentage = ko.observable();
            self.loan_interest_percentage = ko.observable();
            self.paid_penalty_percentage = ko.observable();
            self.projected_intrest_earnings_percentage = ko.observable();
            self.average_loan_balance_percentage = ko.observable();
            self.gross_loan_portfolio_percentage = ko.observable();
            self.intrest_in_suspense_percentage = ko.observable();
            self.portfolio_at_risk_percentage = ko.observable();
            self.value_at_risk_percentage = ko.observable();
            self.penalty_percentage = ko.observable();

            self.totalamount = ko.computed(function() {
                total = 0;
                if ((typeof self.loan_count_active() !== 'undefined') && (typeof self.loan_count_locked() !== 'undefined') && (typeof self.loan_count_arrias() !== 'undefined')) {
                    total_count = parseInt(self.loan_count_active().loan_count) + parseInt(self.loan_count_locked().loan_count) + parseInt(self.loan_count_arrias().loan_count);
                    total = parseFloat(self.gross_loan_portfolio()) / parseInt(total_count);
                }

                return total;
            });


            self.updateData = function() {
                var credit_officer_id = '',
                    start_date1 = '2001-01-01',
                    end_date1 = '';
                if (typeof self.credit_officer() != 'undefined') {
                    credit_officer_id = self.credit_officer().id;
                }
                if (typeof self.start_date() != 'undefined') {
                    start_date1 = self.start_date();
                }
                if (typeof self.end_date() != 'undefined') {
                    end_date1 = self.end_date();
                }

                $.ajax({
                    type: "post",
                    dataType: "json",
                    data: {
                        start_date: moment(start_date1, 'DD-MM-YYYY').format('YYYY-MM-DD'),
                        end_date: moment(end_date1, 'DD-MM-YYYY').format('YYYY-MM-DD'),
                        credit_officer_id: credit_officer_id,
                        origin: "reports"
                    },
                    url: "<?php echo site_url('reports/get_loan_indicators_data') ?>",
                    success: function(response) {
                        /* pie_chart("pie_chart", parseFloat(response.principal_disbursed), parseFloat(response.amount_paid['already_principal_amount']));

                        pie_chart("pie_chart_modal", parseFloat(response.principal_disbursed), parseFloat(response.amount_paid['already_principal_amount'])); */

                        self.projected_intrest_earnings(response.projected_intrest_earnings);
                        self.change_in_Portfolio(response.change_in_Portfolio);
                        self.gross_loan_portfolio(response.gross_loan_portfolio);
                        self.principal_disbursed(response.principal_disbursed);
                        self.amount_paid(response.amount_paid);
                        self.extraordinary_writeoff(response.extraordinary_writeoff);
                        self.unpaid_penalty(response.penalty_total);
                        self.loan_count_pend_approval(response.loan_count_pend_approval);
                        self.loan_count_active(response.loan_count_active);
                        self.loan_count_locked(response.loan_count_locked);
                        self.loan_count_arrias(response.loan_count_arrias);
                        self.loan_count_partial(response.loan_count_partial);
                        self.portfolio_at_risk(response.portfolio_at_risk);
                        self.value_at_risk(response.value_at_risk);
                        self.intrest_in_suspense(response.intrest_in_suspense);

                        self.app_percentage(response.app_percentage);
                        self.active_percentage(response.active_percentage);
                        self.principal_disbursed_percentage(response.principal_disbursed_percentage);
                        self.principal_collected_percentage(response.principal_collected_percentage);
                        self.portfolio_pending_percentage(response.portfolio_pending_percentage);
                        self.writeoff_percentage(response.writeoff_percentage);
                        self.loan_interest_percentage(response.loan_interest_percentage);
                        self.paid_penalty_percentage(response.paid_penalty_percentage);
                        self.projected_intrest_earnings_percentage(response.projected_intrest_earnings_percentage);
                        self.average_loan_balance_percentage(response.average_loan_balance_percentage);
                        self.gross_loan_portfolio_percentage(response.gross_loan_portfolio_percentage);
                        self.intrest_in_suspense_percentage(response.intrest_in_suspense_percentage);
                        self.value_at_risk_percentage(response.value_at_risk_percentage);
                        self.portfolio_at_risk_percentage(response.portfolio_at_risk_percentage);
                        self.penalty_percentage(response.penalty_percentage);

                    }
                })
            };

            //pie chart data
            self.pie_chart_data = ko.observable();
            //column chart data
            self.column_chart_data = ko.observable();
            //line graph
            self.loan_state_category_totals = ko.observable();

            //Loan product list
            self.loan_product_data = ko.observableArray(<?php echo json_encode($loan_product_data); ?>);

            self.prepare_products = function(data) {
                var product_names = [];
                if (typeof data != 'undefined') {
                    for (var p = 0; p < data.length; ++p) {
                        product_names[p] = data[p].product_name;
                    }
                }
                return product_names;
            }

            self.prepare_state_totals = function(data, average_call = false, $four_column = false) {
                var state_totals = [];
                if (typeof data != 'undefined') {
                    for (var p = 0; p < data.length; ++p) {
                        if (average_call) {
                            if ($four_column) {
                                state_totals[p] = Math.round((parseFloat(data[p].total) / parseFloat(4)) * 100) / 100;
                            } else {
                                state_totals[p] = Math.round((parseFloat(data[p].total) / parseFloat(3)) * 100) / 100;
                            }
                        } else {
                            state_totals[p] = parseInt(data[p].total);
                        }
                    }
                }
                return state_totals;
            }

            self.draw_graph = function() {
                var pie_chart_data = [],
                    line_graph_data = [],
                    column_chart_data = [],
                    product_list = [],
                    chart_labells = [];
                var chart_title;
                product_list[0] = self.prepare_products(self.loan_product_data());

                //Application graphs
                chart_labells[1] = 'Partial';
                chart_labells[2] = 'Pending';
                chart_labells[3] = 'Approved';

                pie_chart_data[1] = self.pie_chart_data().partial;
                pie_chart_data[2] = self.pie_chart_data().pending;
                pie_chart_data[3] = self.pie_chart_data().approved;

                column_chart_data[1] = self.prepare_state_totals(self.column_chart_data().partial);
                column_chart_data[2] = self.prepare_state_totals(self.column_chart_data().pending);
                column_chart_data[3] = self.prepare_state_totals(self.column_chart_data().approved);

                line_graph_data[0] = self.prepare_state_totals(self.loan_state_category_totals().application, true);

                chart_title = "Combination Chart of Application Stage For Loan Product";
                combined_charts("applications_graph", chart_title, product_list, pie_chart_data, column_chart_data, line_graph_data, chart_labells);

                //Active graph
                chart_labells[1] = 'Active';
                chart_labells[2] = 'Locked';
                chart_labells[3] = 'In_arrears';

                pie_chart_data[1] = self.pie_chart_data().active;
                pie_chart_data[2] = self.pie_chart_data().locked;
                pie_chart_data[3] = self.pie_chart_data().in_arrears;

                column_chart_data[1] = self.prepare_state_totals(self.column_chart_data().active);
                column_chart_data[2] = self.prepare_state_totals(self.column_chart_data().locked);
                column_chart_data[3] = self.prepare_state_totals(self.column_chart_data().in_arrears);

                line_graph_data[0] = self.prepare_state_totals(self.loan_state_category_totals().active, true);

                chart_title = "Pie, Line and Bar graph of Active Loan For the Loan Products";
                combined_charts("active_graph", chart_title, product_list, pie_chart_data, column_chart_data, line_graph_data, chart_labells);

                //Terminated graph
                chart_labells[1] = 'Writhdrawn';
                chart_labells[2] = 'Cancelled';
                chart_labells[3] = 'Rejected';

                pie_chart_data[1] = self.pie_chart_data().writhdrawn;
                pie_chart_data[2] = self.pie_chart_data().cancelled;
                pie_chart_data[3] = self.pie_chart_data().rejected;

                column_chart_data[1] = self.prepare_state_totals(self.column_chart_data().writhdrawn);
                column_chart_data[2] = self.prepare_state_totals(self.column_chart_data().cancelled);
                column_chart_data[3] = self.prepare_state_totals(self.column_chart_data().rejected);

                line_graph_data[0] = self.prepare_state_totals(self.loan_state_category_totals().terminated, true);

                chart_title = "Combined Charts For Terminated Loans Per Product";
                combined_charts("terminated_graph", chart_title, product_list, pie_chart_data, column_chart_data, line_graph_data, chart_labells);

                //Closed graph
                chart_labells[1] = 'Paid_off';
                chart_labells[2] = 'Refinanced';
                chart_labells[3] = 'Obligation_met';
                chart_labells[4] = 'Written_off';

                pie_chart_data[1] = self.pie_chart_data().paid_off;
                pie_chart_data[2] = self.pie_chart_data().refinanced;
                pie_chart_data[3] = self.pie_chart_data().obligation_met;
                pie_chart_data[4] = self.pie_chart_data().written_off;

                column_chart_data[1] = self.prepare_state_totals(self.column_chart_data().paid_off);
                column_chart_data[2] = self.prepare_state_totals(self.column_chart_data().refinanced);
                column_chart_data[3] = self.prepare_state_totals(self.column_chart_data().obligation_met);
                column_chart_data[4] = self.prepare_state_totals(self.column_chart_data().written_off);

                line_graph_data[0] = self.prepare_state_totals(self.loan_state_category_totals().closed, true, true);

                chart_title = "Closed Loans Analysis For the Loan Products";
                combined_charts2("closed_graph", chart_title, product_list, pie_chart_data, column_chart_data, line_graph_data, chart_labells);
            }
            get_new_graphs('', '');

            //observables for printout report.
            self.Applications = ko.observableArray();
            self.Active_loans = ko.observableArray();
            self.Closed_loans = ko.observableArray();
            self.In_arrear_loans = ko.observableArray();
            self.written_off_loans = ko.observableArray();

            self.Applications_total = ko.observable();
            self.Active_loans_total = ko.observable();
            self.Closed_loans_total = ko.observable();
            self.In_arrear_loans_total = ko.observable();
            self.written_off_loans_total = ko.observable();

            self.rowSpan_value = ko.observable();
            self.loan_amounts = ko.observable();
            self.loan_portfolio = ko.observable();
            self.risk_indicators = ko.observable();

            self.loan_amounts_totals = ko.observable();
            self.loan_portfolio_totals = ko.observable();
            self.risk_indicators_totals = ko.observable();

            self.statuses = ko.observable(false);
            self.amounts = ko.observable(false);
            self.portfolio = ko.observable(true);
            self.indicators = ko.observable(true)
            self.computed_total = function(data) {
                let total = parseInt(0);
                $.each(data, function(key, val) {
                    total += parseInt(val.total)
                });
                return total;
            }

            self.computed_total_2 = function(data) {
                let totals = [];
                $.each(data, function(key1, val) {
                    totals[key1] = parseFloat(0);
                    $.each(val, function(key, val) {
                        totals[key1] += parseFloat(val.amount);
                    })
                });
                return totals;
            }


            self.get_loan_report_data = function() {
                var credit_officer_id = '',
                    start_date1 = '',
                    end_date1 = '';
                if (typeof self.credit_officer() != 'undefined') {
                    credit_officer_id = self.credit_officer().id;
                }
                if (typeof self.start_date() != 'undefined') {
                    start_date1 = self.start_date();
                }
                if (typeof self.end_date() != 'undefined') {
                    end_date1 = self.end_date();
                }
                $.ajax({
                    url: "<?php echo site_url('reports/loan_print_data') ?>",
                    dataType: 'json',
                    type: 'post',
                    data: {
                        start_date: (start_date1 != '') ? moment(start_date1, 'DD-MM-YYYY').format('YYYY-MM-DD') : null,
                        end_date: moment(end_date1, 'DD-MM-YYYY').format('YYYY-MM-DD'),
                        credit_officer_id: credit_officer_id
                    },
                    success: function(response) {
                        self.Applications(response.statuses.application);
                        self.Active_loans(response.statuses.active);
                        self.Closed_loans(response.statuses.closed);
                        self.In_arrear_loans(response.statuses.inarrears);
                        self.written_off_loans(response.statuses.written_off);

                        self.rowSpan_value(parseInt(response.statuses.application.length) + parseInt(2));
                        self.loan_amounts(response.loan_amount);
                        self.loan_portfolio(response.loan_portfolio);
                        self.risk_indicators(response.risk_indicators);

                        self.loan_amounts_totals(self.computed_total_2(response.loan_amount));

                        self.loan_portfolio_totals(self.computed_total_2(response.loan_portfolio));
                        self.risk_indicators_totals(self.computed_total_2(response.risk_indicators));

                        self.Applications_total(self.computed_total(response.statuses.application));
                        self.Active_loans_total(self.computed_total(response.statuses.active));
                        self.Closed_loans_total(self.computed_total(response.statuses.closed));
                        self.In_arrear_loans_total(self.computed_total(response.statuses.inarrears));
                        self.written_off_loans_total(self.computed_total(response.statuses.written_off));
                    }
                })
            }

            self.members = ko.observableArray(<?php echo isset($members) ? json_encode($members) : '' ?>);
            self.member = ko.observable();
        };

        reportsModel = new ReportsModel();
        ko.applyBindings(reportsModel);
        reportsModel.updateData();
        reportsModel.get_loan_report_data();

        var handleDataTableButtons = function(tabClicked) {
            <?php $this->view('reports/loans/active_table_js'); ?>
            <?php $this->view('reports/loans/in_arrears_table_js'); ?>
            <?php $this->view('reports/loans/closed_table_js'); ?>
            <?php $this->view('reports/loans/history/table_js'); ?>
            <?php $this->view('reports/loans/disbursed_loans_table_js'); ?>
        };
        TableManageButtons = function() {
            "use strict";
            return {
                init: function(tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();
        TableManageButtons.init("tab-charts");
         
        daterangepicker_initializer(false, "<?php echo $start_date; ?>", "<?php echo $end_date; ?>");

        // start of range function

        $(function() {

            $("#active_amount-range").slider({
                range: true,
                min: 100000,
                step: 100000,
                max: 100000000,
                slide: function(event, ui) {
                    $("#active_min_amount").val(ui.values[0]);
                    $("#active_max_amount").val(ui.values[1]);
                },
                change: function(event, ui) {
                    active_min_amount = $("#active_min_amount").val();
                    active_max_amount = $("#active_max_amount").val();
                    dTable['tblActive_client_loan'].ajax.reload(null, true);
                }
            });

            $("#closed_amount-range").slider({
                range: true,
                min: 100000,
                step: 100000,
                max: 100000000,
                slide: function(event, ui) {
                    $("#closed_min_amount").val(ui.values[0]);
                    $("#closed_max_amount").val(ui.values[1]);
                },
                change: function(event, ui) {
                    closed_min_amount = $("#closed_min_amount").val();
                    closed_max_amount = $("#closed_max_amount").val();
                    dTable['tblClosed_client_loan'].ajax.reload(null, true);
                }
            });

            $("#inarrear_amount-range").slider({
                range: true,
                min: 100000,
                step: 100000,
                max: 100000000,
                slide: function(event, ui) {
                    $("#inarrear_min_amount").val(ui.values[0]);
                    $("#inarrear_max_amount").val(ui.values[1]);
                },
                change: function(event, ui) {
                    inarrear_min_amount = $("#inarrear_min_amount").val();
                    inarrear_max_amount = $("#inarrear_max_amount").val();
                    dTable['tblInarrears_client_loan'].ajax.reload(null, true);
                }
            });


            $("#days-range").slider({
                range: true,
                min: 1,
                step: 1,
                max: 1830,
                slide: function(event, ui) {
                    $("#inarrear_min_days").val(ui.values[0]);
                    $("#inarrear_max_days").val(ui.values[1]);
                },
                change: function(event, ui) {
                    inarrear_min_days = $("#inarrear_min_days").val();
                    inarrear_max_days = $("#inarrear_max_days").val();
                    dTable['tblInarrears_client_loan'].ajax.reload(null, true);

                }
            });
            //console.log(product_id,loan_type,min_amount,max_amount,min_days,max_days);

        });
        // end of range function


    });

    function set_active_select_value() {
        active_product_id = $("#active_product_id").val();
        active_loan_type = $("#active_loan_type").val();
        active_condition = $("#active_condition").val();
        active_due_days = $("#active_due_days").val();
        credit_officer_id = $("#credit_officer_id").val();
        next_due_month = $("#next_due_month").val();
        next_due_year = $("#next_due_year").val();
        if (typeof dTable['tblActive_client_loan'] != 'undefined') {
            dTable['tblActive_client_loan'].ajax.reload(null, true);
        }
    }
    function set_disbursed_filter_dates_value() {
        start_date_at = $("#start_date_at").val();
        end_date_at = $("#end_date_at").val();
        
        if (typeof dTable['tblActive_client_loan'] != 'undefined') {
            dTable['tblDisbursed_client_loan'].ajax.reload(null, true);
        }
    }
 

    function set_closed_select_value() {
        closed_product_id = $("#closed_product_id").val();
        closed_loan_type = $("#closed_loan_type").val();
        if (typeof dTable['tblClosed_client_loan'] != 'undefined') {
            dTable['tblClosed_client_loan'].ajax.reload(null, true);
        }
    }

    function set_inarrear_select_value() {
        inarrear_product_id = $("#inarrear_product_id").val();
        inarrear_loan_type = $("#inarrear_loan_type").val();
        if (typeof dTable['tblInarrears_client_loan'] != 'undefined') {
            dTable['tblInarrears_client_loan'].ajax.reload(null, true);
        }
    }

    function display_footer_sum(api, columns) {
        $.each(columns, function(key, col) {
            //var page_total = api.column(col, {page: 'current'}).data().sum();
            var overall_total = api.column(col).data().sum();
            $(api.column(col).footer()).html('Shs ' + curr_format(overall_total));
            //viewModel.income_total(overall_total);
            //viewModel.expens_total(overall_total);
            //$(api.column(col).footer()).html(curr_format(page_total) + "(" + curr_format(overall_total) + ") ");
        });
    }

    function handleDateRangePicker(startDate, endDate) {
        start_date = moment(startDate, 'X').format('YYYY-MM-DD');
        end_date = moment(endDate, 'X').format('YYYY-MM-DD');
        get_new_graphs(start_date, end_date);

    }

    function pie_chart(chart_area_id, disbursed, collected) {

        Highcharts.chart(chart_area_id, {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Principal Disbursed Against Principal Collected'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                }
            },
            series: [{
                name: 'Princple',
                colorByPoint: true,
                data: [{
                    name: 'Disbursed',
                    y: disbursed
                }, {
                    name: 'Collected',
                    y: collected
                }]
            }]
        });
    }

    function combined_charts(chart_area_id, chart_title, product_list, pie_chart_data, column_chart_data, line_graph_data, chart_labells) {
        Highcharts.chart(chart_area_id, {
            chart: {
                // height: 600
            },
            title: {
                text: chart_title
            },
            xAxis: {
                categories: product_list[0] //['Land Loans', 'Main Loans', 'Emergency Loans', 'Quick Loans', 'Salary Loan']
            },
            yAxis: {
                // tickInterval: 20,
            },
            labels: {
                items: [{
                    html: 'Total loans per state',
                    style: {
                        left: '300px',
                        top: '10px',
                        color: (
                            Highcharts.defaultOptions.title.style &&
                            Highcharts.defaultOptions.title.style.color
                        ) || 'black'
                    }
                }]
            },
            series: [{
                    type: 'column',
                    name: chart_labells[1],
                    data: column_chart_data[1], //[3, 2, 1, 3]
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.1f}'
                    }
                },
                {
                    type: 'column',
                    name: chart_labells[2],
                    data: column_chart_data[2], //[4, 3, 3, 9]
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.1f}'
                    }
                },
                {
                    type: 'column',
                    name: chart_labells[3],
                    data: column_chart_data[3], //[2, 3, 5, 7]
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.1f}'
                    }
                },
                {
                    type: 'spline',
                    name: 'Average',
                    data: line_graph_data[0], //[3, 2.67, 3, 6.33]
                    marker: {
                        lineWidth: 2,
                        lineColor: (column_chart_data[4]) ? Highcharts.getOptions().colors[4] : Highcharts.getOptions().colors[3],
                        fillColor: 'white'
                    }
                },
                {
                    type: 'pie',
                    name: 'Total state Loans',
                    plotOptions: [{
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                            }
                        }
                    }],
                    data: [{
                            name: chart_labells[1],
                            y: pie_chart_data[1], //13
                            color: Highcharts.getOptions().colors[0] // Loan Applications's color
                        }, {
                            name: chart_labells[2],
                            y: pie_chart_data[2], //23
                            color: Highcharts.getOptions().colors[1] // Active Loan's color
                        }, {
                            name: chart_labells[3],
                            y: pie_chart_data[3], //19
                            color: Highcharts.getOptions().colors[2] // Closed Loans's color
                        }

                    ],
                    center: [500, 10],
                    size: 80,
                    showInLegend: false,
                    dataLabels: {
                        enabled: false
                    }
                }
            ]
        });
    }

    // four column graph
    function combined_charts2(chart_area_id, chart_title, product_list, pie_chart_data, column_chart_data, line_graph_data, chart_labells) {
        Highcharts.chart(chart_area_id, {
            chart: {
                // height: 600
            },
            title: {
                text: chart_title
            },
            xAxis: {
                categories: product_list[0] //['Land Loans', 'Main Loans', 'Emergency Loans', 'Quick Loans', 'Salary Loan']
            },
            yAxis: {
                // tickInterval: 20,
            },
            labels: {
                items: [{
                    html: 'Total loans per state',
                    style: {
                        left: '300px',
                        top: '10px',
                        color: (
                            Highcharts.defaultOptions.title.style &&
                            Highcharts.defaultOptions.title.style.color
                        ) || 'black'
                    }
                }]
            },

            series: [{
                    type: 'column',
                    name: chart_labells[1],
                    data: column_chart_data[1], //[3, 2, 1, 3]
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.1f}'
                    }
                },
                {
                    type: 'column',
                    name: chart_labells[2],
                    data: column_chart_data[2], //[4, 3, 3, 9]
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.1f}'
                    }
                },
                {
                    type: 'column',
                    name: chart_labells[3],
                    data: column_chart_data[3], //[2, 3, 5, 7]
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.1f}'
                    }
                },
                {
                    type: 'column',
                    name: chart_labells[4],
                    data: column_chart_data[4], //[2, 3, 5, 7]
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.1f}'
                    }
                },
                {
                    type: 'spline',
                    name: 'Average',
                    data: line_graph_data[0], //[3, 2.67, 3, 6.33]
                    marker: {
                        lineWidth: 2,
                        lineColor: (column_chart_data[4]) ? Highcharts.getOptions().colors[4] : Highcharts.getOptions().colors[3],
                        fillColor: 'white'
                    }
                },
                {
                    type: 'pie',
                    name: 'Total state Loans',
                    plotOptions: [{
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                            }
                        }
                    }],
                    data: [{
                            name: chart_labells[1],
                            y: pie_chart_data[1], //13
                            color: Highcharts.getOptions().colors[0] // Loan Applications's color
                        }, {
                            name: chart_labells[2],
                            y: pie_chart_data[2], //23
                            color: Highcharts.getOptions().colors[1] // Active Loan's color
                        }, {
                            name: chart_labells[3],
                            y: pie_chart_data[3], //19
                            color: Highcharts.getOptions().colors[2] // Closed Loans's color
                        }, {
                            name: chart_labells[4],
                            y: pie_chart_data[4], //19
                            color: Highcharts.getOptions().colors[3] // Closed Loans's color
                        }

                    ],
                    center: [500, 10],
                    size: 80,
                    showInLegend: false,
                    dataLabels: {
                        enabled: false
                    }
                }
            ]
        });
    }


    //getting new schedule
    function get_new_graphs(start_date, end_date) {
        var new_data = {
            date_from: start_date,
            date_to: end_date
        };
        var url = "<?php echo site_url("reports/loan_graph_data"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //emptying the observables
                reportsModel.pie_chart_data = ko.observable(null);
                reportsModel.column_chart_data = ko.observable(null);
                reportsModel.loan_state_category_totals = ko.observable(null);

                //pie chart data
                reportsModel.pie_chart_data = ko.observable(response.pie_chart);
                //column chart data
                reportsModel.column_chart_data = ko.observable(response.column_chart);
                //line graph
                reportsModel.loan_state_category_totals = ko.observable(response.totals);

                //Draw the graphs again
                // reportsModel.draw_graph();
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    let handlePrint_active = () => {
        let client_id;
        let group_id;
        <?php if (isset($user['id'])) { ?>
            client_id = <?php echo $user['id'] ?>;
        <?php } ?>

        <?php if (isset($group_id)) { ?>
            group_id = <?php echo $group_id ?>;
        <?php } ?>

        $.ajax({
            url: '<?php echo site_url("reports/active_loans_pdf_print_out"); ?>',
            data: {
                end_date: moment(end_date).format('YYYY-MM-DD'),
                start_date: moment(start_date).format('YYYY-MM-DD'),
                state_id: 7,
                min_amount: active_min_amount,
                max_amount: active_max_amount,
                product_id: active_product_id,
                loan_type: active_loan_type,
                report: true,
                client_id: client_id,
                group_id: group_id

            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log(response);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });

    }

    let handlePrint_closed = () => {
        $('#btn_print_closed_loans_report').css('display', 'none');
        $('#btn_printing_closed_loans_report').css('display', 'flex');

        let client_id;
        let group_id;
        <?php if (isset($user['id'])) { ?>
            client_id = <?php echo $user['id'] ?>;
        <?php } ?>

        <?php if (isset($group_id)) { ?>
            group_id = <?php echo $group_id ?>;
        <?php } ?>

        $.ajax({
            url: '<?php echo site_url("reports/closed_loans_pdf_print_out"); ?>',
            data: {
                end_date: moment(end_date).format('YYYY-MM-DD'),
                start_date: moment(start_date).format('YYYY-MM-DD'),
                state_ids: [8, 9, 10, 14],
                min_amount: closed_min_amount,
                max_amount: closed_max_amount,
                product_id: closed_product_id,
                loan_type: closed_loan_type,
                report: true,
                client_id: client_id,
                group_id: group_id

            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //console.log(response);
                $('#btn_print_closed_loans_report').css('display', 'flex');
                $('#btn_printing_closed_loans_report').css('display', 'none');
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                $('#btn_print_closed_loans_report').css('display', 'flex');
                $('#btn_printing_closed_loans_report').css('display', 'none');
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            },
            error: function(err) {
                $('#btn_print_closed_loans_report').css('display', 'flex');
                $('#btn_printing_closed_loans_report').css('display', 'none');
            },

        });

    }

    let handlePrint_in_arrears = () => {
        $('#btn_print_in_arrears_loans_report').css('display', 'none');
        $('#btn_printing_in_arrears_loans_report').css('display', 'flex');

        let client_id;
        let group_id;
        <?php if (isset($user['id'])) { ?>
            client_id = <?php echo $user['id'] ?>;
        <?php } ?>

        <?php if (isset($group_id)) { ?>
            group_id = <?php echo $group_id ?>;
        <?php } ?>

        $.ajax({
            url: '<?php echo site_url("reports/in_arrears_loans_pdf_print_out"); ?>',
            data: {
                end_date: moment(end_date).format('YYYY-MM-DD'),
                start_date: moment(start_date).format('YYYY-MM-DD'),
                state_id: 13,
                min_amount: inarrear_min_amount,
                max_amount: inarrear_max_amount,
                min_days_in_arrears: inarrear_min_days,
                max_days_in_arrears: inarrear_max_days,
                product_id: inarrear_product_id,
                loan_type: inarrear_loan_type,
                report: true,
                client_id: client_id,
                group_id: group_id

            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('#btn_print_in_arrears_loans_report').css('display', 'flex');
                $('#btn_printing_in_arrears_loans_report').css('display', 'none');
                //console.log(response);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                $('#btn_print_in_arrears_loans_report').css('display', 'flex');
                $('#btn_printing_in_arrears_loans_report').css('display', 'none');
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            },
            error: function(err) {
                $('#btn_print_in_arrears_loans_report').css('display', 'flex');
                $('#btn_printing_in_arrears_loans_report').css('display', 'none');
            }
        });

    }

    let handlePrint_loan_installment_payments = (client_loan_id, status_id) => {
        $.ajax({
            url: '<?php echo site_url("client_loan/loan_installment_payments_statement"); ?>',
            data: {
                client_loan_id: client_loan_id,
                status_id: status_id
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //console.log(response);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });

    }

    const preview_loan_history = () => {
        $.ajax({
            url: '<?php echo site_url("reports/member_loan_history"); ?>',
            data: {
                member_id: $('#loan_history_member_select').val(),
                limit: $('#loan_limit').val()
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('#loan_history').html(response);
                console.log(response);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }



    (() => {
        let client_id;
        let group_id;
        <?php if (isset($user['id'])) { ?>
            client_id = <?php echo $user['id'] ?>;
        <?php } ?>

        <?php if (isset($group_id)) { ?>
            group_id = <?php echo $group_id ?>;
        <?php } ?>

        $(document).ready(() => {
            $('#export_excel_closed_loans').on('click', () => {
                console.log('\n\n #=> closed loan', closed_product_id, '\n\n');
                $('#export_excel_closed_loans').attr('href', '<?php echo site_url('reports'); ?>' + `/closed_loans_export_excel/${moment(end_date).format('YYYY-MM-DD')}/${moment(start_date).format('YYYY-MM-DD')}/${closed_min_amount ? closed_min_amount : '0'}/${closed_max_amount ? closed_max_amount: '0'}/${closed_product_id ? closed_product_id : '0'}/${parseInt(closed_loan_type) === 0 || parseInt(closed_loan_type) === 1 ? closed_loan_type: 'null'}/${client_id ? client_id : '0'}/${group_id ? group_id : '0'}
        `);
            });

            $('#export_excel_in_arrears_loans').on('click', () => {
                $('#export_excel_in_arrears_loans').attr('href', '<?php echo site_url('reports'); ?>' + `/in_arrears_loans_export_excel/${inarrear_min_amount ? inarrear_min_amount : 'null'}/${inarrear_max_amount ? inarrear_max_amount: 'null'}/${inarrear_min_days ? inarrear_min_days: 'null'}/${inarrear_max_days ? inarrear_max_days: 'null'}/${inarrear_product_id ? inarrear_product_id: 'null'}/${inarrear_loan_type ? inarrear_loan_type: 'null'}/${client_id ? client_id : 'null'}/${group_id ? group_id: 'null'}`);
            });

            $('#print_active_loans_excel').on('click', () => {
                $('#print_active_loans_excel').attr('href', '<?php echo site_url('reports'); ?>' + `/active_loans_export_excel/${active_min_amount ? active_min_amount : 'null'}/${active_max_amount ? active_max_amount: 'null'}/${active_product_id ? active_product_id : 'null'}/${active_loan_type ? active_loan_type : 'null'}/${active_condition ? active_condition : 'null'}/${active_due_days ? active_due_days : 'null'}/${credit_officer_id ? credit_officer_id : 'null'}/${next_due_month ? next_due_month : 'null'}/${next_due_year ? next_due_year : 'null'}/${client_id ? client_id : 'null'}/${group_id ? group_id: 'null'}`);
            });

            $('#btn_printing_closed_loans_report').css('display', 'none');
            $('#btn_printing_in_arrears_loans_report').css('display', 'none');

        });
    })();


    // function filter_transaction_by_date(){
    //     start_date_at = $("#start_date_at").val();
    //     end_date_at   = $("#end_date_at").val();
    //     state_ids = [7,8,9,10,11,12,13,14,15];
    //      $.ajax({
    //          type: "POST",
    //          method: "POST",
    //          dataType: "json",
    //          url: '<?php echo site_url("client_loan/jsonList"); ?>',
    //          data:{start_date_at:start_date_at,end_date_at:end_date_at,state_ids:state_id},
    //          success:function(){
    //             if (typeof dTable['tblDisbursed_client_loan'] != 'undefined') {
    //            dTable['tblDisbursed_client_loan'].ajax.reload(null, true);
    //           }
    //          }
    //      });

       
    // }
</script>