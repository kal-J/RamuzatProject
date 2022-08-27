<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table" href="#tab-solidarity_loan"><i class="fa fa-line-chart"></i> Solidarity Loan</a></li>
                        <li><a class="nav-link " data-toggle="tab" data-bind="click: display_table" href="#tab-pure_loan"><i class="fa fa-hourglass-start"></i>Pure Loan</a></li>
                        
                    </ul>
                    <div class="tab-content">
                        <?php $this->load->view('client_loan/group_loan/types/pure/tab_view'); ?>
                        <?php $this->load->view('client_loan/group_loan/types/solidarity/tab_view'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

