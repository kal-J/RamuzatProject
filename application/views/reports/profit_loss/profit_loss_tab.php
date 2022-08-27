<div role="tabpanel" id="tab-profit_and_loss" class="tab-pane">
       <!--  <div class="panel-body"><br>
            <div class="col-lg-12"> -->
             <!-- sub tabs -->
             <!-- <ul class="nav nav-tabs" role="tablist">
                <li><a class="nav-link active"  data-bind="click: display_table" data-toggle="tab" href="#tab-pl_tabular"><i class="fa fa-line-chart"></i> Tabular View  </a></li> 
                <li><a class="nav-link"  data-bind="click: display_table" data-toggle="tab" href="#tab-pl_vertical"><i class="fa fa-balance-scale"></i> Vertical View</a></li> 
             </ul>
             
            <div class="tab-content">
                <?php //$this->view('reports/profit_loss/tabular_view'); ?>
                <?php //$this->view('reports/profit_loss/vertical_view'); ?>
            </div>

            </div>
        </div> -->
        <?php $this->view('reports/profit_loss/vertical_view'); ?>
    </div>
