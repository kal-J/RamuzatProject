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
                <div class="pull-right add-record-btn">
                    <div id="reportrange" class="reportrange ">
                        <i class="fa fa-calendar"></i>
                        <span>December 30, 2018 - Feb 11, 2019</span> <b class="caret"></b>
                    </div>

                </div>
            </div>
            <div class="ibox-content">
                <div class="tabs-container">

                    <ul class="nav nav-tabs col-md-12" role="tablist">
                        <li><a class="nav-link active" data-bind="click: display_table" data-toggle="tab" href="#tab-monthly_savings"><i class="fa fa-line-chart"></i> Monthly Savings </a>
                        </li>
                        <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-savings_ending"><i class="fa fa-line-chart"></i>Savings Report </a>
                        </li>
                        <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-interest"><i class="fa fa-money"></i>Interest Payouts </a>
                        </li>
                        <!--   <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-savings_schedule"><i class="fa fa-line-chart"></i>Savings Schedules </a></li> -->

                        <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-report"><i class="fa fa-line-chart"></i>Accounts Report </a>
                        </li>
                        <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-periodic"><i class="fa fa-line-chart"></i>Periodic Reports </a>
                        </li>
                        <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-deposit-returns"><i class="fa fa-line-chart"></i>Deposit Returns </a>
                        </li>
                    </ul>
                    <div class="tab-content">

                        <?php $this->view('reports/savings/monthly/monthly_tab_data'); ?>
                        <?php $this->view('reports/savings/monthly_ending_balance_tab'); ?>
                        <?php //$this->view('reports/savings/savings_schedule_tab'); 
                        ?>
                        <?php $this->view('reports/savings/interest/interest_tab'); ?>
                        <?php $this->view('reports/savings/account_reports/report_tab'); ?>
                        <?php $this->view('reports/savings/periodic_reports/periodic_tab'); ?>
                        <?php $this->view('reports/savings/deposit_returns/deposit_returns_tab'); ?>

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
    var deposit = 0;
    var to_date, from_date, product_id, status_id;
    var statuses = [{
            'id': 1,
            'name': 'Pending'
        },
        {
            'id': 2,
            'name': 'Partial'
        },
        {
            'id': 3,
            'name': 'Paid'
        }
    ];

    function getJsonData(mydata, start_date, end_date) {
        $.ajax({
            url: "<?php echo site_url('reports/savings_end_balance') ?>",
            dataType: "json",
            type: "POST",
            data: {
                fisc_date_to: moment(end_date, 'X').format('YYYY-MM-DD'),
                fisc_date_from: moment(start_date, 'X').format('YYYY-MM-DD')
            },
            success: mydata
        });
    }

    function getJsonDataAccountBased(mydata, start_date, end_date) {
        $.ajax({
            url: "<?php echo site_url('reports/savings_end_balance') ?>",
            dataType: "json",
            type: "POST",
            data: {
                fisc_date_to: moment(end_date, 'X').format('YYYY-MM-DD'),
                fisc_date_from: moment(start_date, 'X').format('YYYY-MM-DD')
            },
            success: mydata
        });
    }

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
            self.end_date = ko.observable();
            self.start_date = ko.observable();
            self.products = ko.observable(<?php echo (isset($products)) ? json_encode($products) : ''; ?>);
            self.product = ko.observable();
            self.statuses = ko.observable(statuses);
            self.status = ko.observable();
            self.deposit = ko.observable();
            self.month_names = ko.observableArray(0);
            //Deposit Returns data
            self.deposit_returns_data = ko.observableArray([]);
            self.fixed_savings_total = ko.observableArray([]);
            self.savings_total = ko.observableArray([]);
            self.locked_savings_total = ko.observableArray([]);
            
            
        

            FetchDepositReturns();
            FixedTotalSavings();
            TotalSavings();
            LockedTotalSavings();

            self.updateData = function() {
                if (typeof self.product() != 'undefined') {
                    product_id = self.product().id;
                } else {
                    product_id = '';
                }
                if (typeof self.status() != 'undefined') {
                    status_id = self.status().id;
                } else {
                    status_id = '';
                }
                if (typeof self.deposit() != 'undefined') {
                    deposit = self.deposit().id;
                } else {
                    deposit = '2';
                }
                if (typeof self.start_date() != 'undefined') {
                    from_date = self.start_date();
                } else {
                    from_date = '';
                }
                if (typeof self.end_date() != 'undefined') {
                    to_date = self.end_date();
                } else {
                    to_date = '';
                }
                if (dTable['tblSavings_schedule']) {
                    dTable['tblSavings_schedule'].ajax.reload(null, true);
                }


            };
        };

        reportsModel = new ReportsModel();
        ko.applyBindings(reportsModel);

        var handleDataTableButtons = function(tabClicked) {
            <?php $this->view('reports/savings/monthly/general_table_js'); ?>
            <?php $this->view('reports/savings/ending_js'); ?>
            <?php //$this->view('reports/savings/schedule_js'); 
            ?>
            <?php $this->view('reports/savings/interest/interest_js'); ?>
            <?php $this->view('reports/savings/account_reports/report_js'); ?>
            <?php $this->view('reports/savings/periodic_reports/periodic_js'); ?>
            <?php $this->view('reports/savings/deposit_returns/deposit_returns_table_js'); ?>
        };
        TableManageButtons = function() {
            "use strict";
            return {
                init: function(tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();
        TableManageButtons.init("tab-general_tab");
        daterangepicker_initializer(false, "<?php echo '01-01-2012'; ?>", "<?php echo ($end_date > date("d-m-Y")) ? date("d-m-Y") : $end_date; ?>");



    });

    const FetchDepositReturns = () => {
        $.ajax({
            url: "<?php echo site_url('DepositReturns/deposit_returns_all') ?>",
            dataType: "json",
            type: "POST",
            success: function(response) {
                reportsModel.deposit_returns_data.removeAll();
                reportsModel.deposit_returns_data(response);

            }
        });
    }
    const FixedTotalSavings = () => {
        $.ajax({
            url: "<?php echo site_url('DepositReturns/fixed_savings_total') ?>",
            dataType: "json",
            type: "POST",
            success: function(response) {
                reportsModel.fixed_savings_total.removeAll();
                reportsModel.fixed_savings_total(response);

            }
        });
    }
    const TotalSavings = () => {
        $.ajax({
            url: "<?php echo site_url('DepositReturns/savings_total') ?>",
            dataType: "json",
            type: "POST",
            success: function(response) {
                reportsModel.savings_total.removeAll();
                reportsModel.savings_total(response);

            }
        });
    }
    const LockedTotalSavings = () => {
        $.ajax({
            url: "<?php echo site_url('DepositReturns/locked_savings_total') ?>",
            dataType: "json",
            type: "POST",
            success: function(response) {
                reportsModel.locked_savings_total.removeAll();
                reportsModel.locked_savings_total(response);

            }
        });
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

    function filter_reports_data() {
        dTable['tbl_saving_report'].ajax.reload(null, true);

    }

    function filter_periodic_data() {
        dTable['tbl_periodic_report'].ajax.reload(null, true);
    }

    function handleDateRangePicker(startDate, endDate) {
        if (typeof displayed_tab !== 'undefined') {

        }
        start_date = startDate;
        end_date = endDate;
        TableManageButtons.init(displayed_tab);

        getJsonData(function(data) {
            var columns = [];
            data = data;
            columnNames = data.month_name;
            for (var i in columnNames) {
                columns.push({
                    data: data[i],
                    title: columnNames[i]
                });
            }
            dTable['tblSavings_ending'] = $('#tblSavings_ending').DataTable().clear().destroy();
            dTable['tblSavings_ending'] = $('#tblSavings_ending').DataTable({
                "lengthMenu": [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                "order": [],
                "bInfo": true,
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": <?php if (in_array('6', $report_privilege)) { ?> getBtnConfig('Monthly Savings Report '),
            <?php } else {
                                echo "[],";
                            } ?> "data": data.data,
            "columns": columns,
            "responsive": true
            });
        }, start_date, end_date);
    }

    $(document).ready(() => {
        $('#btn_print_monthly_savings').on('click', () => {
            $('#btn_print_monthly_savings').attr('href', '<?php echo site_url('reports') ?>' + `/export_excel_savings_per_month/${start_date ? moment(start_date, 'X').format('YYYY-MM-DD') : 'null'}/${end_date ? moment(end_date, 'X').format('YYYY-MM-DD') : 'null'}`);
        });

        $('#btn_print_month_ending_bal').on('click', () => {
            $('#btn_print_month_ending_bal').attr('href', '<?php echo site_url('reports') ?>' + `/export_excel_savings_monthly_ending_bal/${start_date ? moment(start_date, 'X').format('YYYY-MM-DD') : 'null'}/${end_date ? moment(end_date, 'X').format('YYYY-MM-DD') : 'null'}`);
        });

        $('#btn_print_savings_interest_payouts').on('click', () => {
            $('#btn_print_savings_interest_payouts').attr('href', '<?php echo site_url('reports') ?>' + `/export_excel_savings_interest_payments/${start_date ? moment(start_date, 'X').format('YYYY-MM-DD') : 'null'}/${end_date ? moment(end_date, 'X').format('YYYY-MM-DD') : 'null'}`);
        });

        $('#btn_print_savings_accounts_report').on('click', () => {
            $('#btn_print_savings_accounts_report').attr('href', '<?php echo site_url('reports') ?>' + `/export_excel_savings_accounts_report/${$("#start").val() ? $("#start").val() : 'null'}/${$("#end").val() ? $("#end").val() : 'null'}/${$("#product_id").val() ? $("#product_id").val() : 'null'}`);
        });

        $('#btn_print_savings_periodic_reports').on('click', () => {
            $('#btn_print_savings_periodic_reports').attr('href', '<?php echo site_url('reports') ?>' + `/export_excel_savings_accounts_periodic_reports/${$("#min").val() ? $("#min").val() : 'null'}/${$("#max").val() ? $("#max").val() : 'null'}`);
        });

    });
</script>