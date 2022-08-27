<div role="tabpanel" id="tab-balance_sheet" class="tab-pane">
        <!-- <div class="panel-body"><br>
            <div class="col-lg-12"> -->
             <!-- sub tabs -->
            <!--  <ul class="nav nav-tabs" role="tablist">
                <li><a class="nav-link active"  data-bind="click: display_table" data-toggle="tab" href="#tab-bs_accounts"><i class="fa fa-line-chart"></i> Balance sheet (Accounts) </a></li> 
                <li><a class="nav-link"  data-bind="click: display_table" data-toggle="tab" href="#tab-bs_printable"><i class="fa fa-balance-scale"></i> Printable Statement</a></li> 
             </ul> -->
             
             <!-- end sub tabs  and start of sub tab contents-->

           <!--  <div class="tab-content">
                <?php  //$this->view('reports/balance_sheet/bs_accounts'); ?>
                <?php ///$this->view('reports/balance_sheet/bs_printable'); ?>
            </div>

            </div>
        </div> -->
        <?php $this->view('reports/balance_sheet/bs_printable'); ?>
    </div>