<?php
    $start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
    $end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>

<section id="print_js_printable" style="display: none;">
</section>

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
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table"  href="#tab-sms"><i class="fa fa-money"></i>SMS</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"  href="#tab-sms-monthly">SMS - Monthly Billing Report</a></li>
                    </ul>
                    <div class="hr-line-dashed"></div>
                    <div class="tab-content">
                        <?php $this->view('billing/tab_view'); ?>
                        <?php $this->view('billing/monthly/tab_view'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<script>
    var dTable = {};
    var billingModel = {};
    var TableManageButtons = {};
    var displayed_tab = '';
    var start_date, end_date,drp;
    $(document).ready(function () {

        var BillingModel = function () {
            var self = this;
            self.display_table = function (data, click_event) {
                displayed_tab = $(click_event.target).prop("hash").toString().replace("#", "");
                TableManageButtons.init(displayed_tab);
            };
         
        };

        billingModel = new BillingModel();

        ko.applyBindings(billingModel);

        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY"); end_date = moment('<?php echo ($end_date>date("d-m-Y"))?date("d-m-Y"):$end_date; ?>', "DD-MM-YYYY");

        daterangepicker_initializer(false, "<?php echo '01-01-2012'; ?>", "<?php echo ($end_date>date("d-m-Y"))?date("d-m-Y"):$end_date; ?>");
        var handleDataTableButtons = function (tabClicked) {
          <?php $this->view('billing/table_js'); ?>
          <?php $this->view('billing/monthly/table_js'); ?>
        };

        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();

        TableManageButtons.init("tab-sms");

    });

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

    const filter_by_month = () => {
        dTable['tblBilling_monthly_sms'].ajax.reload(null, true);
    }

    const print_sms_billing = (e) => {
        e.preventDefault();
        $.ajax({
            url: '<?php echo site_url("billing/pdf_print_out"); ?>',
            data: {},
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                console.log(response);
                $('#print_js_printable').html(response.the_page_data);
                
                printJS({printable: 'printable_sms_billing', type: 'html', targetStyles: ['*'], documentTitle: response.sub_title});
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }
    const print_sms_billing_monthly = (e) => {
        e.preventDefault();
        $.ajax({
            url: '<?php echo site_url("billing/pdf_print_out"); ?>',
            data: {
                month: $('#month_filter').val()
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                console.log(response);
                $('#print_js_printable').html(response.the_page_data);

                printJS({printable: 'printable_sms_billing', type: 'html', targetStyles: ['*'], documentTitle: response.sub_title});
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    $(document).ready(() => {
        $('#print_sms_billing').on('click', () => {
            $('#print_sms_billing').attr('href', '<?php echo site_url('billing/export_excel'); ?>');
        });

        $('#print_sms_billing_monthly').on('click', () => {

            $('#print_sms_billing_monthly').attr('href', '<?php echo site_url('billing'); ?>' + `/export_excel/${$('#month_filter').val()}`);

        });
   
    });

</script>
