<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
                <ul class="breadcrumb">
                    <li><a href="<?php echo site_url("fees/index"); ?>">Fees</a></li>
                    <li><span style="font-weight:bold; color:gray;  font-size:14px;">
                            <?php echo $title; ?></span></li>
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
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table" href="#tab-fixed_asset"><i class="fa fa-money"></i> User Fees</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="table-responsive pt-5">
                            <table class="table table-striped table-condensed" id="tblDetail_member_fees" width="100%">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Fee name</th>
                                        <th>Amount</th>
                                        <th>Amount Due</th>
                                        <th>Amount Paid</th>
                                        <th>Due date</th>
                                        <th>Status ?</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
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
<?php $this->load->view('fees/reports/membership/pay_modal'); ?>

<script>
    var dTable = {};
    $(document).ready(function() {
        <?php $this->view("fees/reports/membership/details_js"); ?>
    });
</script>