<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
                <ul class="breadcrumb">
                    <li><a href="<?php echo site_url("loan_reversal"); ?>">Transaction Reversals</a></li>
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
                        <h2 style="text-align: center; padding-top:  20px; font-weight:bold">Transactions </h2>
                        <div class="row tp-7">

                            <div class="col-lg-12 tp-5">
                                <div class="table-responsive tp-5">
                                    <table class="table-bordered display compact nowrap table-hover" id="tblTrans_tracking" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Unique ID.</th>
                                                <th>Loan no.</th>
                                                <th>Action Date</th>
                                                <th>Action</th>
                                                <th>Reverse</th>
                                            </tr>
                                        </thead>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $this->load->view('client_loan/loan_reversal/reversal_modal.php'); ?>
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
    var start_datev;
    var end_date;
    var end_datev;
    $(document).ready(function() {
        start_date = '<?php echo $start_date; ?>';
        end_date = '<?php echo ($end_date > date("d-m-Y")) ? date("d-m-Y") : $end_date; ?>';

        $('form#formReverseTransaction').validator().on('submit', saveData);

        var handleDataTableButtons = function() {
            <?php $this->view("client_loan/loan_reversal/table_js"); ?>
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

        daterangepicker_initializer();
    });

    function reload_data(form_id, response) {
        switch (form_id) {
            case "formReverseTransaction":
            case "formReverseTransaction":
                dTable['tblTrans_tracking'].ajax.reload(null, true);
                break;
            default:
                //nothing really to do here
                break;
        }
    }

    let updateData = function(startDate, endDate) {
        start_date = moment(startDate, 'X').format('YYYY-MM-DD');
        end_date = moment(endDate, 'X').format('YYYY-MM-DD');

        dTable['tblTrans_tracking'].ajax.reload(null, true);

    };

    function handleDateRangePicker(startDate, endDate) {
        start_date = startDate;
        end_date = endDate;
        //TableManageButtons.init(displayed_tab);
        updateData(startDate, endDate);
    }
</script>