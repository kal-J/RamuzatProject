<?php
$start_date = date('d-m-Y', strtotime($fiscal_active['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_active['end_date']));
?>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">

            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-4">
                    <h2> <?php echo $title; ?></h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?php echo base_url('u/home') ?>">Home</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <strong><?php echo $title; ?></strong>
                        </li>
                    </ol>
                </div>

            </div>
            <div class="ibox-content">
                <div class="tabs-container">

                    <ul class="nav nav-tabs col-md-12" role="tablist">
                        <li><a class="nav-link active" data-bind="click: display_table" data-toggle="tab" href="#tab-share_active_accounts"><i class="fa fa-line-chart"></i> Share Account </a>
                        </li>

                    </ul>
                    <div class="tab-content">

                        <?php $this->view('client/shares/share_account/states/active/tab_view'); ?>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var dTable = {};
    var TableManageButtons = {};
  
   
      $(document).ready(function() {
        
        var SharesModel = function() {
            var self = this;
            self.display_table = function(data, click_event) {
                displayed_tab = $(click_event.target).prop("hash").toString().replace("#", "");
                TableManageButtons.init(displayed_tab);
            };
            self.end_date = ko.observable();
            self.start_date = ko.observable();
            self.status = ko.observable();
            self.user_shares = ko.observableArray([]);
           

            

            
        };

        sharesModel = new SharesModel();
        ko.applyBindings(sharesModel);

        var handleDataTableButtons = function(tabClicked) {
            
            <?php $this->view('client/shares/share_account/states/active/active_js'); ?>
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
        



    });

    

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
        dTable['tblShares_Active_Account'].ajax.reload(null, true);

    }

    

    

    $(document).ready(() => {
       
        
        $('#btn_shares_report').on('click', () => {
            $('#btn_print_savings_periodic_reports').attr('href', '<?php echo site_url('reports') ?>' + `/export_excel_savings_accounts_periodic_reports/${$("#min").val() ? $("#min").val() : 'null'}/${$("#max").val() ? $("#max").val() : 'null'}`);
        });

    });
</script>