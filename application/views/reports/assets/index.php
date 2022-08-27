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
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table"  href="#tab-asset_reports"><i class="fa fa-graph-chart"></i>Asset Report</a></li>
                     <!-- <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"  href="#tab-login_log"><i class="fa fa-toggle-on"></i>Disposal Account Report</a></li>-->
                    </ul>                    
                    <div class="hr-line-dashed"></div>
                    <div class="tab-content">
                        <?php $this->view('reports/assets/tab_view'); ?>
                       <?php $this->view('reports/assets/disposal/tab_view'); ?>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<script>
    var dTable = {};
    var assetsModel = {};
    var TableManageButtons = {};
    var displayed_tab = '';
    var start_date, end_date,drp;
    $(document).ready(function () {

        var AssetsModel = function () {
            var self = this;
            self.display_table = function (data, click_event) {
                displayed_tab = $(click_event.target).prop("hash").toString().replace("#", "");
                TableManageButtons.init(displayed_tab);
            };
         
        };

        assetsModel = new AssetsModel();

        ko.applyBindings(assetsModel);

        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY"); end_date = moment('<?php echo ($end_date>date("d-m-Y"))?date("d-m-Y"):$end_date; ?>', "DD-MM-YYYY");

        daterangepicker_initializer(false, "<?php echo '01-01-2012'; ?>", "<?php echo ($end_date>date("d-m-Y"))?date("d-m-Y"):$end_date; ?>");
        var handleDataTableButtons = function (tabClicked) {
          <?php $this->view('reports/assets/table_js'); ?>
          <?php $this->view('reports/assets/disposal/table_js'); ?>
        };

        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();

        TableManageButtons.init("tab-asset_reports");

    });

     function display_footer_sum(api, columns) {
        $.each(columns, function (key, col) {
            //var page_total = api.column(col, {page: 'current'}).data().sum();
            var overall_total = api.column(col).data().sum();
            $(api.column(col).footer()).html('Shs ' + curr_format(dpr_amount));
            //viewModel.income_total(overall_total);
            //viewModel.expens_total(overall_total);
            //$(api.column(col).footer()).html(curr_format(page_total) + "(" + curr_format(overall_total) + ") ");
        });
    }

    function reload_data(formId, reponse_data) {
        switch (formId) {
           
        }
    }

    function consumeDtableData(dTableData) {
        var theData = dTableData.data;
        if (theData.length > 0) {
            
            
            
        }
    }

    
   
    
    function set_selects(data) {
       
    }

    function handleDateRangePicker(startDate, endDate) {
        start_date = startDate;
        end_date = endDate;
        if(typeof displayed_tab !== 'undefined'){
            TableManageButtons.init(displayed_tab);
        }
    }

</script>
