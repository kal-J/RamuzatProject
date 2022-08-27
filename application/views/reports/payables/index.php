<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>
<style>
    .dataTable > thead > tr > th[class*="sort"]:after{
        content: "" !important;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
                <div class="tabs-container">
                    
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active"  data-bind="click: display_table" data-toggle="tab" href="#tab-receivables"><i class="fa fa-line-chart"></i> Aging Payables </a></li> 
                        <li><a class="nav-link"  data-bind="click: display_table" data-toggle="tab" href="#tab-tabular"><i class="fa fa-line-chart"></i> Creditors </a>
                        </li> 
                    </ul>
                    <div class="tab-content">
                    <div role="tabpanel" id="tab-receivables" class="tab-pane active">
                    <div class="panel-body"><br>
                    <div class="row">
                        <div class="col-lg-12">
                        <div class="table-responsive">
                                <table class="table-bordered display compact nowrap table-hover" id="tblTrialbalance" width="100%" >
                                    <thead>
                                        <tr>
                                            <th>Range (Days)</th>
                                            <th>0-30 Days</th>
                                            <th>31-60 Days</th>
                                            <th>61-90 Days</th>
                                            <th>91+ Days</th>
                                        </tr>
                                    </thead>
                                        <tr data-bind="with:aging_bills" >
                                            <td><B>Total Amount (UGX)</B></td>
                                            <td><span data-bind="text: curr_format(amount_0_30*1)"></span></td>
                                            <td><span data-bind="text: curr_format(amount_31_60*1)"></span></td>
                                            <td><span data-bind="text: curr_format(amount_61_90*1)"></span></td>
                                            <td><span data-bind="text: curr_format(amount_90_plus*1)"></span></td>
                                        </tr>
                                    <tbody>
                                    </tbody>
                                  
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                        <div id="bar_graph3" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                        </div>
                    </div>
                    </div>
                    </div>
                     <!--  end of graph tab -->
                     <div role="tabpanel" id="tab-tabular" class="tab-pane ">
                    <div class="panel-body"><br>
                    <div class="row">
                        <div class="col-lg-12">
                        <div class="table-responsive">
                                <table class="table-bordered display compact nowrap table-hover" id="tblAgingbills" width="100%" >
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Total Amount Due</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th >Totals</th>
                                        <th>Amount Due </th>
                                    </tr>
                                </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    </div>
                    </div>

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
    $(document).ready(function () {
        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY"); end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");
        var groupColumn = 0;

        var ReportsModel = function () {
            var self = this;
            self.display_table = function (data, click_event) {
                displayed_tab = $(click_event.target).prop("hash").toString().replace("#","");
                TableManageButtons.init(displayed_tab);
            };
            
            self.aging_bills = ko.observable();
            self.updateData = function () {
                $.ajax({
                    type: "post",
                    dataType: "json",
                    data: {fisc_date_from: moment(start_date,'X').format('YYYY-MM-DD'), fisc_date_to: moment(end_date,'X').format('YYYY-MM-DD'), origin: "reports"},
                    url: "<?php echo site_url('reports/aging_accounts') ?>",
                    success: function (response) {
                        
                      self.aging_bills(response.aging_bill_payables);
                       draw_basic_bar_graph("bar_graph3","Aging Accounts Payables","Amount Payable: <b>{point.y:.1f}</b>",response.aging_payables);
                    }
                })
            };
        };

        reportsModel = new ReportsModel();
        reportsModel.updateData();
        ko.applyBindings(reportsModel);

        var handleDataTableButtons = function (tabClicked) {
<?php $this->view('reports/payables/aging_bills_table_js'); ?>
        };
        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();
        daterangepicker_initializer(false, "<?php echo $start_date; ?>", "<?php echo $end_date; ?>");
      
<?php $this->view('reports/highcharts_js'); ?>
    });
    function display_footer_sum(api, columns) {
        $.each(columns, function (key, col) {
            //var page_total = api.column(col, {page: 'current'}).data().sum();
            var overall_total = api.column(col).data().sum();
            $(api.column(col).footer()).html('Shs ' + curr_format(overall_total));
            //viewModel.income_total(overall_total);
            //viewModel.expens_total(overall_total);
            //$(api.column(col).footer()).html(curr_format(page_total) + "(" + curr_format(overall_total) + ") ");
        });
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

        reportsModel.updateData();
    }
</script>