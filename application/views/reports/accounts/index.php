<?php
    $start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
    $end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));
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
                        <li><a class="nav-link active" data-bind="click: display_table" data-toggle="tab" href="#tab-savings_report"><i class="fa fa-line-chart"></i>Saving Accounts</a>
                        </li>
                        <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-shares_report"><i class="fa fa-line-chart"></i> Share Accounts </a>
                        </li>
                        <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-loans_report"><i class="fa fa-line-chart"></i>Loan Accounts </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <?php $this->view('reports/accounts/savings_account/tab_view'); ?>
                        <?php $this->view('reports/accounts/shares_account/tab_view'); ?>
                        <?php $this->view('reports/accounts/loans_account/tab_view'); ?>
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
    var accountsModel = {};
    var start_date, end_date;
    var to_date, status_id;
   


    $(document).ready(function() {
        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY");
        end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");
        var groupColumn = 0;


        var AccountsModel = function() {
            var self = this;
            self.display_table = function(data, click_event) {
                displayed_tab = $(click_event.target).prop("hash").toString().replace("#", "");
                TableManageButtons.init(displayed_tab);
            };
            self.end_date = ko.observable();
            self.start_date = ko.observable();
            
        };

        accountsModel = new AccountsModel();
        ko.applyBindings(accountsModel);

        var handleDataTableButtons = function(tabClicked) {
            <?php $this->view('reports/accounts/savings_account/tab_view_js'); ?>
            <?php $this->view('reports/accounts/shares_account/tab_view_js'); ?>
            <?php $this->view('reports/accounts/loans_account/tab_view_js'); ?>  
        };
        TableManageButtons = function() {
            "use strict";
            return {
                init: function(tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();
        TableManageButtons.init("tab-savings_report");



    });

    


   

    function filter_shares_reports_data() {
        dTable['tbl_shares_report'].ajax.reload(null, true);

    }
    function filter_savings_reports_data() {
        dTable['tbl_savings_report'].ajax.reload(null, true);

    }

    function filter_loans_reports() {
        dTable['tbl_loans_report'].ajax.reload(null, true);
    }


</script>