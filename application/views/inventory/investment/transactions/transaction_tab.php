<div role="tabpanel" id="tab-investmet-transaction" class="tab-pane">
    <div class="panel-body">
<?php if(!empty($acc_id)&&(is_numeric($acc_id))){ ?>
<div class="row">
      <div class="col-lg-4">
            </div>         

   <div class="col-lg-6">
    <div id="reportrange" class="reportrange pull-right">
        <i class="fa fa-calendar"></i>
        <span>December 30, 2018 - Feb 11, 2019</span> <b class="caret"></b>
    </div>
    </div>
    <div class="col-lg-2">
     <button data-bind="click: printStatement" class="btn btn-sm btn-primary btn-flat"> Print Statement </button>
    </div>  

</div>
<?php  } ?>
<br>
    <div class="col-lg-12">
        <div class="table-responsive">
                <table class="table-bordered display compact nowrap table-hover" id="tblTransaction" width="100%" >
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Transaction No.</th>
                        <th>Transaction Date</th>
                        <th>Investment Type</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>BALANCE</th>
                        <th>Type</th>
                        <th>Payment Method</th>
                        <th>Narrative</th>
                        <th>Action</th> 
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
