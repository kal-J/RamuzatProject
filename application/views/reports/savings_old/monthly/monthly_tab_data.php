<div role="tabpanel" id="tab-monthly_savings" class="tab-pane active">
        <div class="panel-body"><br>
            <div class="col-lg-12">
             <!-- sub tabs -->
             <ul class="nav nav-tabs" role="tablist">
                <li><a class="nav-link active"  data-bind="click: display_table" data-toggle="tab" href="#tab-general_tab"><i class="fa fa-money"></i> General </a></li> 
               <!--  <li><a class="nav-link"  data-bind="click: display_table" data-toggle="tab" href="#tab-account_based"><i class="fa fa-plus-square"></i> Savings Accounts</a></li>  -->
             </ul>
             
             <!-- end sub tabs  and start of sub tab contents-->

            <div class="tab-content">
                <?php  $this->view('reports/savings/monthly/general_tab'); ?>
                <?php $this->view('reports/savings/monthly/account_based'); ?>
            </div>

            </div>
        </div>
        <?php //$this->view('reports/balance_sheet/bs_printable'); ?>
    </div>