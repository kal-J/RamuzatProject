<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = $fiscal_year['end_date'] <= date('Y-m-d') ? date('d-m-Y', strtotime($fiscal_year['end_date'])) : date('d-m-Y');
?>
<style>
    .dataTable>thead>tr>th[class*="sort"]:after {
        content: "" !important;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
                <ul class="breadcrumb">
                    <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                    <li><span style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
                </ul>
                <div class="pull-right add-record-btn">
                    <div id="reportrange" class="reportrange ">
                        <i class="fa fa-calendar"></i>
                        <span>December 30, 2018 - Feb 11, 2019</span> <b class="caret"></b>
                    </div>
                </div>
            </div>
            <div class="ibox-content">
                <div class="tabs-container">

                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-bind="click: display_table" data-toggle="tab" href="#tab-trial_balance"><i class="fa fa-balance-scale"></i> Trial Balance</a></li>
                        <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-profit_and_loss"><i class="fa fa-line-chart"></i> Income Statement</a></li>
                        <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-balance_sheet"><i class="fa fa-balance-scale"></i> Balance Sheet</a></li>
                        <!-- <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-cash_flow"><i class="fa fa-line-chart"></i> Cash Flow </a></li> -->
                        <li><a class="nav-link" data-toggle="tab" href="#tab-query_reports"><i class="fa fa-balance-scale"></i>Performance Reports</a></li>
                        <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-financial"><i class="fa fa-line-chart"></i> Indicators </a></li>

                        <!-- <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-change_in_equity"><i class="fa fa-line-chart"></i> Change In Equity </a></li> -->
                    </ul>
                    <div class="tab-content">
                        <?php $this->view('reports/trialbalance_tab'); ?>
                        <?php $this->view('reports/profit_loss/profit_loss_tab'); ?>
                        <?php $this->view('reports/balance_sheet/balance_sheet_tab'); ?>
                        <?php $this->view('reports/cashflow/cashflow_tab_data'); ?>
                        <?php $this->view('reports/printouts/query_reports'); ?>
                        <?php $this->view('reports/printouts/change_in_equity'); ?>
                        <?php $this->view('reports/financial_tab'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var dTable = {};
    var TableManageButtons = {};
    var displayed_tab = '';
    var reportsModel = {};
    var start_date, end_date;
    $(document).ready(function() {
        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY");
        end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");
        var groupColumn = 0;
        $('form#reports_query').validate({
            submitHandler: saveData2
        });
        var ReportsModel = function() {
            var self = this;
            self.display_table = function(data, click_event) {
                displayed_tab = $(click_event.target).prop("hash").toString().replace("#", "");
                TableManageButtons.init(displayed_tab);
            };
            self.period_types = ko.observable([{
                "id": 1,
                "period_name": "As At"
            }, {
                "id": 2,
                "period_name": "Date Range"
            }]);
            //{"id": 3, "period_name": "Fiscal Years"}
            self.fiscal_years = ko.observable(<?php echo json_encode($fiscal_years); ?>);
            self.period = ko.observable();
            self.liab_equity = ko.observableArray();
            self.assets = ko.observableArray();
            self.income = ko.observableArray();
            self.expenses = ko.observableArray();
            self.print_sums = ko.observable();
            self.profitloss_sums = ko.observable();
            self.report_type = ko.observable();

            self.cf_operations=ko.observableArray();


            //self.net_profit_coss = ko.observable('<?php //echo $net_profit_loss; 
                                                    ?>');
            self.debt_assets = ko.observable();
            self.debt_equity = ko.observable();
            self.current_ratio = ko.observable();
            self.all_totals = ko.observable();
            self.subaccounts_totals = ko.observable();
            self.membership = ko.observable();
            self.period_savings = ko.observable();
            self.loans = ko.observable();
            self.shares = ko.observable();
            self.fiscal_2 = ko.observable();
            self.fiscal_3 = ko.observable();
            self.fiscal_1 = ko.observable();
            self.start_date = ko.observable(start_date);
            self.end_date = ko.observable(end_date);
            self.selected_period = ko.observable(0);
            self.rowSpan_value = ko.observable(0);


            self.start_date1 = ko.observable();
            self.end_date1 = ko.observable();

            self.start_date2 = ko.observable();
            self.end_date2 = ko.observable();

            /* =========================QUERY REPORT============================ */
            self.share_report = ko.observable();
            self.no_of_shareholders = ko.observable();
            self.total_shares = ko.observable();
            self.price_per_share = ko.observable();
            self.savings = ko.observable();
            self.savings_count = ko.observable();
            self.male_members = ko.observable();
            self.female_members = ko.observable();

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

            self.share_report1 = ko.observable();
            self.no_of_shareholders1 = ko.observable();
            self.total_shares1 = ko.observable();
            self.price_per_share1 = ko.observable();
            self.savings1 = ko.observable();
            self.savings_count1 = ko.observable();
            self.male_members1 = ko.observable();
            self.female_members1 = ko.observable();

            self.projected_intrest_earnings1 = ko.observable();
            self.change_in_Portfolio1 = ko.observable();
            self.gross_loan_portfolio1 = ko.observable();
            self.principal_disbursed1 = ko.observable();
            self.amount_paid1 = ko.observable();
            self.unpaid_penalty1 = ko.observable();
            self.extraordinary_writeoff1 = ko.observable();
            self.loan_count_pend_approval1 = ko.observable();
            self.loan_count_active1 = ko.observable();
            self.loan_count_locked1 = ko.observable();
            self.loan_count_arrias1 = ko.observable();
            self.loan_count_partial1 = ko.observable();
            self.portfolio_at_risk1 = ko.observable();
            self.value_at_risk1 = ko.observable();
            self.intrest_in_suspense1 = ko.observable();


            self.share_report2 = ko.observable();
            self.no_of_shareholders2 = ko.observable();
            self.total_shares2 = ko.observable();
            self.price_per_share2 = ko.observable();
            self.savings2 = ko.observable();
            self.savings_count2 = ko.observable();
            self.male_members2 = ko.observable();
            self.female_members2 = ko.observable();

            self.projected_intrest_earnings2 = ko.observable();
            self.change_in_Portfolio2 = ko.observable();
            self.gross_loan_portfolio2 = ko.observable();
            self.principal_disbursed2 = ko.observable();
            self.amount_paid2 = ko.observable();
            self.unpaid_penalty2 = ko.observable();
            self.extraordinary_writeoff2 = ko.observable();
            self.loan_count_pend_approval2 = ko.observable();
            self.loan_count_active2 = ko.observable();
            self.loan_count_locked2 = ko.observable();
            self.loan_count_arrias2 = ko.observable();
            self.loan_count_partial2 = ko.observable();
            self.portfolio_at_risk2 = ko.observable();
            self.value_at_risk2 = ko.observable();
            self.intrest_in_suspense2 = ko.observable();

            self.updateData = function() {
                $.ajax({
                    type: "post",
                    dataType: "json",
                    data: {
                        fisc_date_from: moment(start_date, 'X').format('YYYY-MM-DD'),
                        fisc_date_to: moment(end_date, 'X').format('YYYY-MM-DD'),
                        origin: "reports"
                    },
                    url: "<?php echo site_url('reports/get_indicators_data') ?>",
                    success: function(response) {
                        self.all_totals(response.all_totals);
                        self.debt_equity(response.debt_equity);
                        draw_pie_chart(response.debt_equity, 'pieChart1', '<span style="font-size:12px;padding:0px;">Debt to Equity .</span>');
                        self.debt_assets(response.debt_assets);
                        draw_pie_chart(response.debt_assets, 'pieChart2', '<span style="font-size:12px;padding:0px;">Debt to Total Assets .</span>');
                        self.current_ratio(response.current_ratio);
                        draw_pie_chart(response.current_ratio, 'pieChart3', '<span style="font-size:12px;padding:0px;">Current Ratio</span>');
                        draw_line_chart("line_graph1", response.income_expense);
                        draw_bar_chart("bar_graph1", response.cashflow_investments);
                        draw_basic_line_graph("bar_graph2", response.cashflow_financing);

                        self.subaccounts_totals(response.subaccounts_totals);

                    }
                })
            };
        };

        reportsModel = new ReportsModel();
        reportsModel.updateData();
        ko.applyBindings(reportsModel);

        var handleDataTableButtons = function(tabClicked) {
            <?php //$this->view('reports/profit_loss/profit_loss_table_js'); 
            ?>
            <?php //$this->view('reports/profit_loss/table_js'); 
            ?>
            <?php //$this->view('reports/balance_sheet/balance_sheet_js'); 
            ?>
            <?php $this->view('reports/trialbalance_table_js'); ?>
        };
        TableManageButtons = function() {
            "use strict";
            return {
                init: function(tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();
        TableManageButtons.init("tab-trial_balance");
        daterangepicker_initializer();
        // Order by the grouping
        $('#tblProfit_loss tbody').on('click', 'tr.group', function() {
            var currentOrder = table.order()[0];
            if (currentOrder[0] === groupColumn && currentOrder[1] === 'asc') {
                table.order([groupColumn, 'desc']).draw();
            } else {
                table.order([groupColumn, 'asc']).draw();
            }
        });
        //=========================================balance sheet=====================
        get_balancesheet(start_date, end_date);
        get_income_statement(start_date, end_date);

        if ($('.tblParent_table').length) {
            if (typeof(dTable['tblParent_table']) !== 'undefined') {
                //$(".tab-pane").removeClass("active");
                //$("#tab-balance_sheet").addClass("active");
                //dTable['tblParent_table'].ajax.reload(null,true);
            } else {
                dTable['tblParent_table'] = $('.tblParent_table').DataTable({
                    "searching": false,
                    "paging": false,
                    "ordering": false,
                    "bInfo": false,
                    "responsive": true,
                    "dom": '<"html5buttons"B>lTfgitp',
                    "buttons": <?php if (in_array('6', $report_privilege)) { ?> getBtnConfig('End of year report'),
                <?php } else {
                                    echo "[],";
                                } ?>

                });
            }
        }

        $('#create_excel').click(function() {
            var excel_data = $('#balancesheet').html();
            $.post('<?php echo site_url('reports/excel'); ?>', {
                    excel_data: excel_data,
                    filename: 'balancesheet'
                },
                function(feedback) {
                    //swal("success");
                });
        });

        <?php $this->view('reports/highcharts_js'); ?>
    });

    function get_table_options(url, tbl_id) {
        //tbl_id is optional
        return {
            "searching": false,
            "paging": false,
            "ordering": false,
            "bInfo": false,
            "responsive": true,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],

            ajax: {
                "url": url,
                "dataType": "json",
                "type": "POST",
                "data": function(d) {
                    d.fisc_date_to = moment(end_date, 'X').format('YYYY-MM-DD');
                    d.fisc_date_from = moment(start_date, 'X').format('YYYY-MM-DD');
                    d.status_id = '1';
                }
            },
            "columnDefs": [{
                "targets": [1],
                "orderable": false,
                "searchable": false
            }],
            "footerCallback": function(tfoot, data, start, end, display) {
                var api = this.api();
                display_footer_sum(api, [1]);
            },
            columns: [{
                    data: "account_name",
                    render: function(data, type, full, meta) {
                        return display_account_info(data, type, full, meta);
                    }
                },
                {
                    data: 'amount',
                    render: function(data, type, full, meta) {
                        return data ? curr_format(round(data, 2)) : '0';
                    }
                }
            ],
            "drawCallback": function(settings) {
                var api = this.api();
                dTable['tblParent_table'].draw();
            }
        };
    }

    function display_footer_sum(api, columns) {
        $.each(columns, function(key, col) {
            //var page_total = api.column(col, {page: 'current'}).data().sum();
            var overall_total = api.column(col).data().sum();
            $(api.column(col).footer()).html('Shs ' + curr_format(round(overall_total, 2)));
            //viewModel.income_total(overall_total);
            //viewModel.expens_total(overall_total);
            //$(api.column(col).footer()).html(curr_format(page_total) + "(" + curr_format(overall_total) + ") ");
        });
    }

    function display_account_info(data, type, full, meta) {
        var level = (full.account_code).toString().split("-");
        if (level.length < 2) {
            var padding = 0;
        } else {
            var padding = (level.length) * 10;
        }
        if (full.account_id) {
            return "<a style='padding-left:" + padding + "px;' href='<?php echo site_url("accounts/view"); ?>/" + full.account_id + "' title='Click to view full details'>[" + full.account_code + "]  " + full.account_name + "</a>";
        } else {
            return "[" + full.account_code + "]  " + full.account_name;
        }
    }

    function handleDateRangePicker(startDate, endDate) {
        if (typeof displayed_tab !== 'undefined') {
            // if (displayed_tab === 'tab-risky_loans') {
            //     start_date = moment().add(6, 'days'); 
            //     end_date = moment();
            // }else{
            //     start_date = startDate;
            //     end_date = endDate;
            // }
        }
        start_date = startDate;
        end_date = endDate;
        TableManageButtons.init(displayed_tab);
        get_balancesheet(startDate, endDate);
        get_income_statement(startDate, endDate);

        reportsModel.updateData();
    }

    function get_balancesheet(start_date, end_date) {
        $('#gif').css('visibility', 'visible');
        $.ajax({
            url: "<?php echo site_url('reports/balancesheet') ?>",
            data: {
                print: 0,
                status_id: 1,
                fisc_date_to: moment(end_date, 'X').format('YYYY-MM-DD'),
                fisc_date_from: moment(start_date, 'X').format('YYYY-MM-DD')
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                reportsModel.assets(response.assets);
                reportsModel.print_sums(response.print_sums);
                reportsModel.liab_equity(response.liab_equity);
                reportsModel.report_type(response.report_type);
                reportsModel.start_date(start_date);
                reportsModel.end_date(end_date);
                $('#gif').css('visibility', 'hidden');
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    function get_income_statement(start_date, end_date) {
        $('#gif').css('visibility', 'visible');
        $.ajax({
            url: "<?php echo site_url('reports/income_statement') ?>",
            data: {
                print: 0,
                status_id: 1,
                fisc_date_to: moment(end_date, 'X').format('YYYY-MM-DD'),
                fisc_date_from: moment(start_date, 'X').format('YYYY-MM-DD')
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                reportsModel.profitloss_sums(response.profitloss_sums);
                reportsModel.income(response.income);
                reportsModel.expenses(response.expenses);
                reportsModel.report_type(response.report_type);
                reportsModel.start_date(start_date);
                reportsModel.end_date(end_date);
                $('#gif').css('visibility', 'hidden');
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

        function get_cash_flow(start_date, end_date) {
        $('#gif').css('visibility', 'visible');
        $.ajax({
            url: "<?php echo site_url('reports/cashflow') ?>",
            data: {
                print: 0,
                status_id: 1,
                fisc_date_to: moment(end_date, 'X').format('YYYY-MM-DD'),
                fisc_date_from: moment(start_date, 'X').format('YYYY-MM-DD')
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                reportsModel.cf_operations(response.cf_operations);
               
                reportsModel.start_date(start_date);
                reportsModel.end_date(end_date);
                $('#gif').css('visibility', 'hidden');
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    //getting report data
    function get_query_report_data(that) {
        var shares = $('#shares').val();
        var membership = $('#membership').val();
        var loans = $('#loans').val();
        var savings = $('#savings').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var period = $('#period').val();
        var fiscal_1 = $('#fiscal_one').val();
        var fiscal_2 = $('#fiscal_two').val();
        var fiscal_3 = $('#fiscal_three').val();
        var date_at = $('#date_at').val();
        var end_date_final = (parseInt(period) === 1) ? moment(date_at, 'DD-MM-YYYY').format('YYYY-MM-DD') : moment(end_date, 'DD-MM-YYYY').format('YYYY-MM-DD');
        var url = "<?php echo site_url("reports/query_reports"); ?>";
        $('#gif').css('visibility', 'visible');
        $.ajax({
            url: url,
            data: {
                savings: savings,
                membership: membership,
                shares: shares,
                loans: loans,
                end_date: end_date_final,
                start_date: moment(start_date, 'DD-MM-YYYY').format('YYYY-MM-DD'),
                period: period,
                fiscal_1: fiscal_1,
                fiscal_2: fiscal_2,
                fiscal_3: fiscal_3
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                reportsModel.selected_period(response.period);
                reportsModel.membership(response.membership);
                reportsModel.period_savings(response.period_savings);
                reportsModel.loans(response.loans);
                reportsModel.shares(response.shares);
                reportsModel.fiscal_1(response.fiscal_1);
                reportsModel.fiscal_2(response.fiscal_2);
                reportsModel.fiscal_3(response.fiscal_3);
                reportsModel.start_date(response.start_date);
                reportsModel.end_date(response.end_date);
                reportsModel.share_report(response.general_data.share_report);
                reportsModel.rowSpan_value(response.general_data.rowSpan_value);
                reportsModel.no_of_shareholders(response.general_data.share_accounts);
                reportsModel.savings(response.general_data.savings);
                reportsModel.savings_count(response.general_data.savings_count);
                reportsModel.male_members(response.general_data.male_members);
                reportsModel.female_members(response.general_data.female_members);

                reportsModel.projected_intrest_earnings(response.loan_data.projected_intrest_earnings);
                reportsModel.change_in_Portfolio(response.loan_data.change_in_Portfolio);
                reportsModel.gross_loan_portfolio(response.loan_data.gross_loan_portfolio);
                reportsModel.principal_disbursed(response.loan_data.principal_disbursed);
                reportsModel.amount_paid(response.loan_data.amount_paid);
                reportsModel.extraordinary_writeoff(response.loan_data.extraordinary_writeoff);
                reportsModel.unpaid_penalty(response.loan_data.penalty_total);
                reportsModel.loan_count_pend_approval(response.loan_data.loan_count_pend_approval);
                reportsModel.loan_count_active(response.loan_data.loan_count_active);
                reportsModel.loan_count_locked(response.loan_data.loan_count_locked);
                reportsModel.loan_count_arrias(response.loan_data.loan_count_arrias);
                reportsModel.loan_count_partial(response.loan_data.loan_count_partial);
                reportsModel.portfolio_at_risk(response.loan_data.portfolio_at_risk);
                reportsModel.value_at_risk(response.loan_data.value_at_risk);
                reportsModel.intrest_in_suspense(response.loan_data.intrest_in_suspense);
                // ============ FOR THE SECOND YEAR ==========================
                if (parseInt(response.fiscal_2) !== 0) {
                    reportsModel.start_date1(moment(response.start_date1, 'YYYY-MM-DD').format('MMM-YYYY'));
                    reportsModel.end_date1(moment(response.end_date1, 'YYYY-MM-DD').format('MMM-YYYY'));
                    reportsModel.share_report1(response.general_data1.share_report);
                    reportsModel.no_of_shareholders1(response.general_data1.share_accounts);
                    reportsModel.total_shares1(response.general_data1.total_shares);
                    reportsModel.price_per_share1(response.general_data1.price_per_share);
                    reportsModel.savings1(response.general_data1.savings);
                    reportsModel.savings_count1(response.general_data1.savings_count);
                    reportsModel.male_members1(response.general_data1.male_members);
                    reportsModel.female_members1(response.general_data1.female_members);

                    reportsModel.projected_intrest_earnings1(response.loan_data1.projected_intrest_earnings);
                    reportsModel.change_in_Portfolio1(response.loan_data1.change_in_Portfolio);
                    reportsModel.gross_loan_portfolio1(response.loan_data1.gross_loan_portfolio);
                    reportsModel.principal_disbursed1(response.loan_data1.principal_disbursed);
                    reportsModel.amount_paid1(response.loan_data1.amount_paid);
                    reportsModel.extraordinary_writeoff1(response.loan_data1.extraordinary_writeoff);
                    reportsModel.unpaid_penalty1(response.loan_data1.penalty_total);
                    reportsModel.loan_count_pend_approval1(response.loan_data1.loan_count_pend_approval);
                    reportsModel.loan_count_active1(response.loan_data1.loan_count_active);
                    reportsModel.loan_count_locked1(response.loan_data1.loan_count_locked);
                    reportsModel.loan_count_arrias1(response.loan_data1.loan_count_arrias);
                    reportsModel.loan_count_partial1(response.loan_data1.loan_count_partial);
                    reportsModel.portfolio_at_risk1(response.loan_data1.portfolio_at_risk);
                    reportsModel.value_at_risk1(response.loan_data1.value_at_risk);
                    reportsModel.intrest_in_suspense1(response.loan_data1.intrest_in_suspense);
                }
                if (parseInt(response.fiscal_3) !== 0) {
                    // ============ FOR THE THIRD YEAR ==========================
                    reportsModel.start_date2(moment(response.start_date2, 'YYYY-MM-DD').format('MMM-YYYY'));
                    reportsModel.end_date2(moment(response.end_date2, 'YYYY-MM-DD').format('MMM-YYYY'));
                    reportsModel.share_report2(response.general_data2.share_report);
                    reportsModel.no_of_shareholders2(response.general_data2.share_accounts);
                    reportsModel.total_shares2(response.general_data2.total_shares);
                    reportsModel.price_per_share2(response.general_data2.price_per_share);
                    reportsModel.savings2(response.general_data2.savings);
                    reportsModel.savings_count2(response.general_data2.savings_count);
                    reportsModel.male_members2(response.general_data2.male_members);
                    reportsModel.female_members2(response.general_data2.female_members);

                    reportsModel.projected_intrest_earnings2(response.loan_data2.projected_intrest_earnings);
                    reportsModel.change_in_Portfolio2(response.loan_data2.change_in_Portfolio);
                    reportsModel.gross_loan_portfolio2(response.loan_data2.gross_loan_portfolio);
                    reportsModel.principal_disbursed2(response.loan_data2.principal_disbursed);
                    reportsModel.amount_paid2(response.loan_data2.amount_paid);
                    reportsModel.extraordinary_writeoff2(response.loan_data2.extraordinary_writeoff);
                    reportsModel.unpaid_penalty2(response.loan_data2.penalty_total);
                    reportsModel.loan_count_pend_approval2(response.loan_data2.loan_count_pend_approval);
                    reportsModel.loan_count_active2(response.loan_data2.loan_count_active);
                    reportsModel.loan_count_locked2(response.loan_data2.loan_count_locked);
                    reportsModel.loan_count_arrias2(response.loan_data2.loan_count_arrias);
                    reportsModel.loan_count_partial2(response.loan_data2.loan_count_partial);
                    reportsModel.portfolio_at_risk2(response.loan_data2.portfolio_at_risk);
                    reportsModel.value_at_risk2(response.loan_data2.value_at_risk);
                    reportsModel.intrest_in_suspense2(response.loan_data2.intrest_in_suspense);
                }
                $('#gif').css('visibility', 'hidden');
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }


    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }

    $(document).ready(() => {
        reportsModel.start_date.subscribe(() => {
            $('#print_trial_balance_excel').attr('href', `reports/print_trial_balance_excel/${moment(reportsModel.start_date(),'X').format('YYYY-MM-DD')}/${moment(reportsModel.end_date(),'X').format('YYYY-MM-DD')}`);
        });

        reportsModel.end_date.subscribe(() => {
            $('#print_trial_balance_excel').attr('href', `reports/print_trial_balance_excel/${moment(reportsModel.start_date(),'X').format('YYYY-MM-DD')}/${moment(reportsModel.end_date(),'X').format('YYYY-MM-DD')}`);
        });

    });
</script>