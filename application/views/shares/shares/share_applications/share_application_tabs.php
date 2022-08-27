<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
                <div class="tabs-container">
                    <div class="pull-right add-record-btn">
                    </div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table" href="#tab-active_application"><i class="fa fa-line-chart"></i> Approved</a></li>
                        <li><a class="nav-link " data-toggle="tab" data-bind="click: display_table" href="#tab-pending_application"><i class="fa fa-hourglass-start"></i>Pending</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-inactive_application"><i class="fa fa-bars"></i>Rejected</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-payments"><i class="fa fa-bars"></i>Payments</a></li>
                    </ul>
                    <div class="tab-content">
                        <!-- =========== START YOUR TAB CONTENT HERE =============== -->
                        <?php $this->load->view('shares/share_applications/states/pending/tab_view'); ?>
                        <?php $this->load->view('shares/share_applications/states/pending/add_modal'); ?>
                        <?php $this->load->view('shares/share_applications/states/active/tab_view'); ?>
                        <?php $this->load->view('shares/share_applications/states/pending/approve_modal'); ?>
                        <?php $this->load->view('shares/share_applications/states/inactive/tab_view'); ?>
                        <?php $this->load->view('shares/share_applications/payments/payments'); ?>
                        <?php $this->load->view('shares/share_applications/payments/make_acall'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
