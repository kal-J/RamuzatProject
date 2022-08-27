<div role="tabpanel" id="tab-financial" class="tab-pane">
     <div class="panel-title" >
        <center>
        
        </center>
      </div>
    <div class="panel-body"><br>
    
   
    <div class="row">
        <div class="col-lg-12">
        <div id="line_graph1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        </div>
    </div>
     <hr>
    <div class="row">
        <div class="col-lg-4">
            <div id="pieChart1" style="max-height:360px; max-width:600px; margin: 0 auto" ></div>
            <span data-bind="with:debt_equity"> Liabilities: UGX <span data-bind="text: curr_format(slice1.amount*1)"></span> <br> Equity: UGX  <span data-bind="text: curr_format(slice2.amount*1)"></span></span>
        </div>
        <div class="col-lg-4">
            <div id="pieChart2" style="max-height:360px; max-width:600px; margin: 0 auto" ></div>
            <span data-bind="with:debt_assets"> Liabilities: UGX <span data-bind="text: curr_format(slice1.amount*1)"></span> <br> Assets: UGX  <span data-bind="text: curr_format(slice2.amount*1)"></span></span>
        </div>
        <div class="col-lg-4">
        <!-- <span data-bind="text: curr_format(net_profit_coss2()*1)"></span> -->
            <div id="pieChart3" style="max-height:360px; max-width:600px; margin: 0 auto" ></div>
            <span data-bind="with:current_ratio"> Current Liabilities: UGX <span data-bind="text: curr_format(slice2.amount*1)"></span> <br>Current Assets: UGX  <span data-bind="text: curr_format(slice1.amount*1)"></span></span>
        </div>
    </div>
    <hr>
     <div class="row">
        <div class="col-lg-6">
            <div class="card ">
                <div class="card-body">
                  <div id="bar_graph2" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card ">
                <div class="card-body">
                <div id="bar_graph1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                </div>
            </div>
        </div>
    </div>
   
    </div>
</div>