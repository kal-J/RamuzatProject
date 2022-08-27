<style>
@keyframes spinner-border {
    to {
        transform: rotate(360deg);
    }
}

.spinner-border {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    vertical-align: text-bottom;
    border: .25em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    -webkit-animation: spinner-border .75s linear infinite;
    animation: spinner-border .75s linear infinite;
}

.spinner-border-sm {
    height: 1rem;
    border-width: .2em;
}
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <?php if(isset($client_type) && $client_type == 2) { ?>

            <?php } else { ?>
            <div class="ibox-title">
                <ul class="breadcrumb">
                    <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                    <li><span style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
                </ul>
                <div class="pull-right" style="padding-left: 2%">
                    <div id="reportrange" class="reportrange">
                        <i class="fa fa-calendar"></i>
                        <span>December 30, 2018 - Feb 11, 2019</span> <b class="caret"></b>
                    </div>
                </div>
            </div>

            <?php } ?>

            <div class="ibox-content">
                <div class="tabs-container">
                    <?php if(isset($client_type) && $client_type == 2) { ?>

                    <ul class="nav nav-tabs" role="tablist">
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-line-chart"></i>
                                Active</a>

                            <ul class="dropdown-menu">
                                <li>
                                    <a class="nav-link active" data-toggle="tab" data-bind="click: display_table"
                                        href="#tab-share_active_accounts"><i class="fa fa-line-chart"></i> Active</a>
                                </li>

                                <li>
                                    <a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                        href="#tab-share_pending_accounts"><i
                                            class="fa fa-hourglass-start"></i>Pending</a>
                                </li>
                                <li>
                                    <a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                        href="#tab-share_inactive_accounts"><i class="fa fa-bars"></i>Inactive</a>
                                </li>


                            </ul>
                        </li>
                    </ul>

                    <?php } else { ?>

                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table"
                                href="#tab-share_active_accounts"><i class="fa fa-line-chart"></i> Active</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                href="#tab-share_pending_accounts"><i class="fa fa-hourglass-start"></i>Pending</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                href="#tab-share_inactive_accounts"><i class="fa fa-bars"></i>Inactive</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                href="#tab-transaction"><i class="fa fa-bars"></i>Transactions</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                href="#tab-transaction_log"><i class="fa fa-bars"></i> Transaction logs</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                href="#tab-shares_report"><i class="fa fa-line-chart"></i> Report</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                href="#tab-shares_performance_report"><i class="fa fa-balance-scale"></i>Performance
                                Report</a>
                       </li>
                        <!--<li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-alert-setting"><i class="fa fa-envelope"></i>Alert Setting</a>
                        </li>-->
                    </ul>

                    <?php } ?>

                    <div class="tab-content">
                        <?php if(isset($client_type) && $client_type == 2) { ?>
                        <?php $this->load->view('shares/share_account/states/active/tab_view'); ?>
                        <?php $this->load->view('shares/share_account/states/pending/tab_view'); ?>
                        <?php $this->load->view('shares/share_account/states/inactive/tab_view'); ?>
                        <?php $this->load->view('shares/share_account/states/pending/add_modal'); ?>


                        <?php $this->load->view('shares/transaction/buy_shares'); ?>
                        <?php $this->load->view('shares/transaction/transfer'); ?>
                        <?php $this->load->view('shares/transaction/convert'); ?>
                        <?php $this->load->view('shares/transaction/bulk_transaction_modal'); ?>
                        <?php $this->load->view('shares/transaction/bulk_deposit_template-modal'); ?>
                        <?php } else { ?>

                        <?php $this->load->view('shares/share_account/states/active/tab_view'); ?>
                        <?php $this->load->view('shares/share_account/states/pending/tab_view'); ?>
                        <?php $this->load->view('shares/share_account/states/inactive/tab_view'); ?>
                        <?php $this->load->view('shares/share_account/states/pending/add_modal'); ?>
                        <div role="tabpanel" id="tab-transaction" class="tab-pane tabparent">
                            <?php $this->load->view('shares/transaction/transaction_tab'); ?>
                            <?php $this->load->view('shares/transaction/reverse_modal'); ?>

                        </div>
                        <div role="tabpanel" id="tab-transaction_log" class="tab-pane transaction_log">
                            <?php $this->load->view('shares/transaction/transaction_log_tab'); ?>
                        </div>
                       <div role="tabpanel" id="tab-shares_report" class="tab-pane shares_report">
                            <?php  $this->load->view('shares/transaction/report/shares_report_tab'); ?>
                        </div> 
                      
                        <?php $this->load->view('shares/transaction/report/shares_performance_report_tab'); ?>
                        
                        <?php $this->load->view('shares/transaction/buy_shares'); ?>
                        <?php $this->load->view('shares/transaction/transfer'); ?>
                        <?php $this->load->view('shares/transaction/convert'); ?>
                        <?php $this->load->view('shares/transaction/bulk_transaction_modal'); ?>
                        <?php $this->load->view('shares/transaction/bulk_deposit_template-modal'); ?>
                        
                        <?php } ?>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>