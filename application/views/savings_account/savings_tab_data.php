<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <?php if($type =="savings"){ ?>
            <div class="ibox-title">
                <ul class="breadcrumb">
                    <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                    <li><span style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
                </ul>
            </div>
            <?php } ?>
            <div class="ibox-content">
                <div class="tabs-container">
                    <div class="ibox-tools">
                        <?php if(in_array('1', $savings_privilege)){ ?>
                        <div class="pull-right add-record-btn">
                            <a class="btn btn-sm btn-primary text-white" href="#" data-toggle="modal"
                                data-target="#add_savings_account"><i class="fa fa-plus-circle text-white"></i> Create
                                Savings Account </a>
                        </div>
                        <?php } ?>
                    </div>

                    <ul class="nav nav-tabs" role="tablist">
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-line-chart"></i>
                                Active</a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                        href="#tab-active_accounts"><i class="fa fa-line-chart"></i> Active
                                        <!--ko foreach: $root.ac_state_totals -->
                                        <!--ko if: state_id == parseInt(7) -->
                                        <span class="badge bg-green" data-bind="text: number">0</span>
                                        <!--/ko-->
                                        <!--/ko-->
                                    </a></li>
                                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                        href="#tab-savings_account_pending"><i class="fa fa-hourglass-start "></i>
                                        Pending
                                        <!--ko foreach: $root.ac_state_totals -->
                                        <!--ko if: state_id == parseInt(5) -->
                                        <span class="badge bg-red" data-bind="text: number">0</span>
                                        <!--/ko-->
                                        <!--/ko-->
                                    </a></li>
                                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                        href="#tab-inactive_accounts"><i class="fa fa-bars "></i> Dormant
                                        <!--ko foreach: $root.ac_state_totals -->
                                        <!--ko if: state_id == parseInt(17) -->
                                        <span class="badge bg-red" data-bind="text: number">0</span>
                                        <!--/ko-->
                                        <!--/ko-->
                                    </a></li>
                                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                        href="#tab-savings_account_suspended"><i class="fa fa-lock "></i> Locked
                                        <!--ko foreach: $root.ac_state_totals -->
                                        <!--ko if: state_id == parseInt(12) -->
                                        <span class="badge bg-red" data-bind="text: number">0</span>
                                        <!--/ko-->
                                        <!--/ko-->
                                    </a></li>
                                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                        href="#tab-savings_account_deleted"><i class="fa fa-ban "></i> Deleted
                                        <!--ko foreach: $root.ac_state_totals -->
                                        <!--ko if: state_id == parseInt(18) -->
                                        <span class="badge bg-red" data-bind="text: number">0</span>
                                        <!--/ko-->
                                        <!--/ko-->
                                    </a></li>
                            </ul>
                        </li>
                        <?php if(isset($acc_id)) { ?>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                href="#tab-fixed-transaction"><i class="fa fa-bars"></i>Fixed Accounts</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                href="#tab-transaction"><i class="fa fa-bars"></i>Transactions</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                href="#tab-transaction_log"><i class="fa fa-bars"></i>Transaction logs</a></li>
                    

                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-money"></i>
                                Withdraw requests</a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                        href="#tab-withdraw_requests"><i class="fa fa-line-chart"></i> Active
                                        <span class="badge bg-green" data-bind="text: $root.pending_requests_totals">0</span>
                                    </a></li>
                                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                        href="#tab-accepted_withdraw_requests"><i class="fa fa-hourglass-start text-info"></i>
                                        Accepted
                                        
                                        <span class="badge bg-info" data-bind="text: $root.accepted_requests_totals">0</span>
                                    </a></li>

                                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                        href="#tab-declined_withdraw_requests"><i class="fa fa-ban text-danger "></i> Declined
                                        
                                        <span class="badge bg-red" data-bind="text: $root.declined_requests_totals">0</span>
                                       
                                    </a></li>
                            </ul>
                        </li>

                        <?php if($org['savings_shares']==1) { ?>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                href="#tab-savings_graph"><i class="fa fa-bars"></i>Graph</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"
                                href="#tab-member_shares"><i class="fa fa-bars"></i>Shares</a></li>

                        <?php } } ?>
                    </ul>
                    <div class="tab-content">
                        <!-- ================= START YOUR TAB CONTENT HERE =============== -->
                        <?php $this->load->view('savings_account/states/active/savings_account_tab'); ?>
                        <?php $this->load->view('savings_account/fixed/fixed_accounts_tab.php'); ?>
                        <?php $this->load->view('savings_account/transaction/transaction_tab.php'); ?>
                        <?php $this->load->view('savings_account/transaction/transaction_log_tab.php'); ?>
                        <?php $this->load->view('savings_account/transaction/edit_transaction.php'); ?>
                        <?php $this->load->view('savings_account/transaction/reverse_modal.php'); ?>
                        <?php $this->load->view('savings_account/states/pending/savings_account_pending_tab'); ?>
                        <?php $this->load->view('savings_account/states/suspended/savings_account_suspended_tab'); ?>
                        <?php $this->load->view('savings_account/states/deleted/savings_account_deleted_tab'); ?>
                        <?php $this->load->view('savings_account/states/inactive/savings_accounts_inactive_tab'); ?>
                        <?php $this->load->view('savings_account/add_savings_account'); ?>
                        <?php $this->load->view('savings_account/deposits/add_modal'); ?>
                        <?php $this->load->view('savings_account/deposits/bulk_deposit_modal'); ?>
                        <?php $this->load->view('savings_account/withdraws/add_modal'); ?>
                        <?php $this->load->view('savings_account/withdraws/transfer'); ?>
                        <?php $this->load->view('savings_account/savings_graph'); ?>
                        <?php $this->load->view('savings_account/shares/membershares'); ?>
                        <?php $this->load->view('savings_account/states/change_state_modal'); ?>
                        <?php $this->load->view('savings_account/requests/withdraw_requests_tab'); ?>
                        <?php $this->load->view('savings_account/requests/accepted/accepted_withdraw_requests_tab'); ?>
                        <?php $this->load->view('savings_account/requests/declined/declined_withdraw_requests_tab'); ?>
                        <?php $this->load->view('savings_account/requests/accept_request_modal'); ?>
                        <?php $this->load->view('savings_account/requests/add'); ?>
                        <?php $this->load->view('savings_account/requests/decline_request_modal'); ?>
                        

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>