<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
                <ul class="breadcrumb">
                    <li><a href="<?php echo site_url("SummaryReports"); ?>">Summary Reports</a></li>
                    <li><span style="font-weight:bold; color:gray;  font-size:14px;">
                            <?php echo $title; ?></span></li>
                </ul>
            </div>

            <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" href="#tab-fixed_asset"><i class="fa fa-money"></i> <?php echo $title; ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <!-- date range picker section -->
                        <div class="row">
                            <div class="col-sm-8">


                            </div>
                            <div class="col-sm-4">
                                <div id="reportrange" class="reportrange pull-right">
                                    <i class="fa fa-calendar"></i>
                                    <span>January 01, 2019 - December 31, 2020</span> <b class="caret"></b>
                                </div>
                            </div>
                        </div>
                        <!-- end of date range picker section -->
                        <h2 style="text-align: center; padding-top:  20px; font-weight:bold">Transactions - <span style="text-align: center;" class="badge badge-secondary">
                                <?php echo $start_date1 . " - " . $end_date1; ?> 
                            </span></h2>
                        <div class="row tp-7">

                            <div class="col-lg-12 tp-5">
                                <div class="table-responsive tp-5">
                                    <table class="table-bordered display compact nowrap table-hover" id="tblDetail_transaction" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Transaction ID.</th>
                                                <th>Ref. N0</th>
                                                <th>Ref. ID</th>
                                                <th>Date</th>
                                                <th>Type </th>
                                                <th>Amount</th>
                                                <th>Narrative</th>
                                                <th>Staff Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <th>Totals</th>
                                            <th>&nbsp;</th>
                                            <th>&nbsp;</th>
                                            <th>&nbsp;</th>
                                            <th>&nbsp;</th>
                                            <th>&nbsp;</th>
                                            <th>&nbsp;</th>
                                            <th>&nbsp;</th>
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

<script>
    var dTable = {};
    var TableManageButtons = {};
    var handleDateRangePicker;
    var start_date;
    var end_date;

    $(document).ready(function() {

        start_date = '<?php echo $start_date1; ?>'; 
        end_date = '<?php echo $end_date1; ?>';

        daterangepicker_initializer();

        var handleDataTableButtons = function() {
            <?php $this->view("summary_reports/transactions/table_js"); ?>
        };

        TableManageButtons = function() {
            "use strict";
            return {
                init: function() {
                    handleDataTableButtons();
                }
            };
        }();
        TableManageButtons.init();        

    });

    let updateData = function(start, end) {
        start_date = moment(start, 'X').format('YYYY-MM-DD');
        end_date = moment(end, 'X').format('YYYY-MM-DD');

        dTable['tblDetail_transaction'].ajax.reload(null, true);

    };

    function handleDateRangePicker(startDate, endDate) {
        start_date = startDate;
        end_date = endDate;
        //TableManageButtons.init(displayed_tab);
        updateData(startDate, endDate);
    }
</script>