
<?php $this->load->view('shares/group_shares_tab_js'); ?>

<div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table" href="#tab-active_accounts"><i class="fa fa-line-chart"></i> Active</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-pending_accounts"><i class="fa fa-hourglass-start"></i>Pending</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-inactive_accounts"><i class="fa fa-bars"></i>Inactive</a></li>
                    </ul>
                    </ul>

                    <div class="tab-content">
                        <?php $this->load->view('shares/share_account/states/active/tab_view'); ?>
                        <?php $this->load->view('shares/share_account/states/pending/tab_view'); ?>
                        <?php $this->load->view('shares/share_account/states/inactive/tab_view'); ?>
                        <?php $this->load->view('shares/share_account/states/pending/add_modal'); ?>
                       
                        
                        <?php $this->load->view('shares/transaction/buy_shares'); ?>
                        <?php $this->load->view('shares/transaction/transfer'); ?>
                        <?php $this->load->view('shares/transaction/convert'); ?>
                        <?php $this->load->view('shares/transaction/bulk_transaction_modal'); ?>
                        <?php $this->load->view('shares/transaction/bulk_deposit_template-modal'); ?>


                    </div>
                </div>
            </div>