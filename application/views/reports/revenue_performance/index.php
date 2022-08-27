<?php
    $start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
    $end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));
    

?>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
             <ul class="breadcrumb">
                <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
            </ul>
            <div class="pull-right" style="padding-left: 2%">
                <div id="reportrange" class="reportrange">
                    <i class="fa fa-calendar"></i>
                    <span>December 30, 2018 - Feb 11, 2019</span> <b class="caret"></b>
                </div>
            </div>
        </div>
            
            <div class="ibox-content">
                <div class="tabs-container">  
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table"  href="#tab-revenue-performance"><i class="fa fa-lock"></i>Revenue Performance</a></li>
                    </ul>                    
                    <div class="hr-line-dashed"></div>
                    <div class="tab-content">
                        <?php $this->view('reports/revenue_performance/revenue_performance_view'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<script>
    var dTable = {};
    var revenuePerformanceModel = {};
    var TableManageButtons = {};
    var displayed_tab = '';
    var start_date, end_date,drp;
    var Revenue = function(name, income) {
        this.name = name;
        this.income = ko.observable(income);
    }

    var RevenueExpenses = function(name, income) {
        this.name = name;
        this.income = ko.observable(income);
    }

    var RevenueAll = function(name, numbers,totals) {
        this.name = name;
        this.numbers = ko.observableArray(numbers);
        this.revenueRowTotal = ko.computed((function(){
            var totalCalculated = 0;
            totals.forEach((function(item){ totalCalculated+= item }));
            return Number(totalCalculated).toLocaleString();
        }))
    }
    var RevenueAllExpenses = function(name, numbers, totals) {
        this.name = name;
        this.numbers = ko.observableArray(numbers);
        this.revenueRowTotal = ko.computed((function(){
            var totalCalculated = 0;
            totals.forEach((function(item){ totalCalculated+= item }));
            return Number(totalCalculated).toLocaleString();
        }))
    }
    $(document).ready(function () {
    
        var RevenuePerformanceModel = function () {
            var self = this;
            self.display_table = function (data, click_event) {
                displayed_tab = $(click_event.target).prop("hash").toString().replace("#", "");
                TableManageButtons.init(displayed_tab);
            };

            self.show = ko.observable(true);
            self.showAll = ko.observable(false);

            self.months = ko.observableArray([]);
            self.fiscal_years = ko.observableArray([]);
            self.view_all_months = ko.observableArray([]);
            self.currentMonth = ko.observable();
            self.currentYear = ko.observable();
            self.revenue_data_income = ko.observableArray([]);
            self.revenue_data_income_all = ko.observableArray([]);
            self.revenue_data_income_all_expenses = ko.observableArray([]);
            self.revenue_data_expenses = ko.observableArray([]);
            self.total = ko.observable(0);
            self.all_total = ko.observableArray([]);
            self.all_total_formatted = ko.observableArray([]);
            self.all_total_expenses = ko.observableArray([]);
            self.all_total_expenses_formatted = ko.observableArray([]);
            self.totalExpenses = ko.observable(0);
            self.netProfit= ko.observable(0);
            self.netProfitAllUnformatted = ko.observableArray([]);
            self.netProfitAll = ko.computed(function() {
                var allNetProfitAll = [];
                self.netProfitAllUnformatted.removeAll();
                for(var i = 0; i<self.all_total().length; i++ ){
                    allNetProfitAll.push(Number(self.all_total()[i] -  self.all_total_expenses()[i]).toLocaleString());
                    self.netProfitAllUnformatted.push(self.all_total()[i] -  self.all_total_expenses()[i]);
                }
                return allNetProfitAll;
            });

            self.accmIncome = ko.computed(function() {
                var allAccIncome = [];
                var prevTotal = 0;
                for(var i = 0; i<self.netProfitAllUnformatted().length; i++ ){
                    allAccIncome.push(Number(prevTotal + self.netProfitAllUnformatted()[i]).toLocaleString());
                    prevTotal = prevTotal + self.netProfitAllUnformatted()[i];
                }
                return allAccIncome;
            });

            self.exportLink = ko.computed(function() {
                var currentYear = self.currentYear();
                var currentMonth = self.currentMonth();
                return "<?php echo site_url("RevenuePerformance/print_revenue_performance"); ?>/"+currentYear+"/"+currentMonth;
            });


        }
        revenuePerformanceModel = new RevenuePerformanceModel();

        ko.applyBindings(revenuePerformanceModel);

        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY"); 
        end_date = moment('<?php echo ($end_date>date("d-m-Y"))?date("d-m-Y"):$end_date; ?>', "DD-MM-YYYY");

        daterangepicker_initializer(false, "<?php echo '01-01-2012'; ?>", "<?php echo ($end_date>date("d-m-Y"))?date("d-m-Y"):$end_date; ?>");
        var handleDataTableButtons = function (tabClicked) {
          <?php $this->view('reports/revenue_performance/revenue_performance_table_js'); ?>
        };

        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();

        TableManageButtons.init("tab-revenue-performance");

        fetchInitialRevenuePerformanceData();
    });

    function set_selects(data) {
       
    }

    function handleDateRangePicker(startDate, endDate) {
        start_date = startDate;
        end_date = endDate;
        if(typeof displayed_tab !== 'undefined'){
            TableManageButtons.init(displayed_tab);
        }
    }

    const fetchInitialRevenuePerformanceData = () => {
        $.ajax({
                url: "<?php echo site_url('RevenuePerformance/jsonList') ?>",
                dataType: "json",
                type: "POST",
                success: function(response) {
                    revenuePerformanceModel.revenue_data_income.removeAll();
                    revenuePerformanceModel.revenue_data_expenses.removeAll();
                    revenuePerformanceModel.total(Number(response.income.total).toLocaleString());
                    revenuePerformanceModel.totalExpenses(Number(response.expenses.total).toLocaleString());
                    revenuePerformanceModel.netProfit(Number(response.income.total - response.expenses.total).toLocaleString());
                    //set the months
                    revenuePerformanceModel.months(response.income.months);
                    //set current month
                    revenuePerformanceModel.currentMonth(response.income.current_month);
                    //fiscal_years
                    revenuePerformanceModel.currentYear(response.current_year);

                    // Add subscribers to month & year
                    if(revenuePerformanceModel.currentMonth()) {
                        revenuePerformanceModel.currentMonth.subscribe(function(newValue) {
                            if (newValue) asyncFetchRevenuePerformance({month: newValue, year: revenuePerformanceModel.currentYear()});
                        });
                    }
                    if(revenuePerformanceModel.currentYear()) {
                        revenuePerformanceModel.currentYear.subscribe(function(newValue){
                            if (newValue) asyncFetchRevenuePerformance({month: revenuePerformanceModel.currentMonth(), year: newValue});
                        });
                    }
                    
                    //current year
                    revenuePerformanceModel.fiscal_years(response.fiscal_years);
                    for(var i =0; i<response.income.data.length; i++){
                       var formattedIncome = response.income.data[i].income ? Number(response.income.data[i].income).toLocaleString() : 0;
                        if(response.expenses.data[i]) {
                            var formattedExpense = response.expenses.data[i].income ? Number(response.expenses.data[i].income).toLocaleString() : 0;
                        }
                        
                        revenuePerformanceModel.revenue_data_income.push(new Revenue(response.income.data[i]?.name, formattedIncome));
                        revenuePerformanceModel.revenue_data_expenses.push(new RevenueExpenses(response.expenses.data[i]?.name, formattedExpense));
                    }

                }
            });
    }

    const asyncFetchRevenuePerformance = (data) => {
        $.ajax({
            url: "<?php echo site_url('RevenuePerformance/jsonList') ?>",
            dataType: "json",
            type: "POST",
            data: data,
            success: function(response) {
                if(data.month === "All"){
                    revenuePerformanceModel.show(false);
                    revenuePerformanceModel.showAll(true);
                    revenuePerformanceModel.view_all_months(response.income.table_months);
                    revenuePerformanceModel.all_total(response.income?.total);
                    revenuePerformanceModel.all_total_formatted(response.income?.formatted_total);
                    revenuePerformanceModel.all_total_expenses(response.expenses?.total);
                    revenuePerformanceModel.all_total_expenses_formatted(response.expenses?.formatted_total);
                }else {
                    revenuePerformanceModel.show(true);
                    revenuePerformanceModel.showAll(false);
                    revenuePerformanceModel.netProfit(Number(response.income.total - response.expenses.total).toLocaleString());
                    revenuePerformanceModel.total(Number(response.income.total).toLocaleString());
                    revenuePerformanceModel.totalExpenses(Number(response.expenses.total).toLocaleString());
                }
                //set the months
                revenuePerformanceModel.months(response.income.months);
                
                revenuePerformanceModel.revenue_data_income.removeAll();
                revenuePerformanceModel.revenue_data_expenses.removeAll();

                revenuePerformanceModel.revenue_data_income_all.removeAll();
                revenuePerformanceModel.revenue_data_income_all_expenses.removeAll();

                revenuePerformanceModel.total(Number(response.income.total).toLocaleString());
                revenuePerformanceModel.totalExpenses(Number(response.expenses.total).toLocaleString());
                for(var i =0; i<response.income.data.length; i++){

                    if(data.month === "All"){
                        revenuePerformanceModel.revenue_data_income_all.push(new RevenueAll(response.income.data[i]?.name, response.income.data[i]?.data,response.income.data[i]?.unformated_data ));
                        if(response.expenses.data[i]?.name){
                            revenuePerformanceModel.revenue_data_income_all_expenses.push(new RevenueAllExpenses(response.expenses.data[i]?.name, response.expenses.data[i]?.data,response.expenses.data[i]?.unformated_data ));

                        }
                    }else {
                        var formattedIncome = response.income.data[i].income ? Number(response.income.data[i].income).toLocaleString() : 0;
                        if(response.expenses.data[i]) {
                            var formattedExpense = response.expenses.data[i].income ? Number(response.expenses.data[i].income).toLocaleString() : 0;

                        }
                        
                        revenuePerformanceModel.revenue_data_income.push(new Revenue(response.income.data[i]?.name, formattedIncome));
                        revenuePerformanceModel.revenue_data_expenses.push(new RevenueExpenses(response.expenses.data[i]?.name, formattedExpense));
                        
                    }
                }

            }
        });
    } 

</script>
