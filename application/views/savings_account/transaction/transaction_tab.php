<div role="tabpanel" id="tab-transaction" class="tab-pane">
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
<div class="row col-10 d-flex justify-content-center m-3">
      <div class="input-group date col-4">
        <label class="my-auto" for="start_date">From :&nbsp;</label>
        <input class="col-6" autocomplete="off" placeholder="DD-MM-YYYY" value="" type="text" onkeydown="return false" name="start_date" id="start_date" required />
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
      </div>
      <div class="input-group date col-4">
        <label class="my-auto" for="end_date">To :&nbsp;</label>
        <input class="col-6" autocomplete="off" placeholder="DD-MM-YYYY" value="<?php echo date('d-m-Y'); ?>" type="text" onkeydown="return false" name="end_date" id="end_date" required />
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
      </div>
      <div class="col-1 mx-0">
      <button onclick="filter_by_date()" class="btn btn-sm btn-primary">
        <i class="fa fa-filter fa-2x"></i>
      </button>
      </div>
      
    </div>
    <?php if(!empty($acc_id)&&(is_numeric($acc_id))){ ?>
        <div class="mt-2 d-flex flex-row-reverse"data-bind="click: printTransaction" >
         <button  onclick="handlePrint_individual_savings_transaction()" class="btn btn-sm btn-secondary">
        <i class="fa fa-print fa-2x"></i></button>
        <button data-bind="visible: isPrinting()" class="btn btn-primary" type="button" disabled>
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Printing...
        </button>
    </div>
    
    <?php } else { ?>

        <div class="mt-2 d-flex flex-row-reverse">
         <button data-bind="visible: !isPrinting_active()"  onclick="handlePrint_savings_transaction()" class="btn btn-sm btn-secondary">
        <i class="fa fa-print fa-2x"></i></button>
        <button data-bind="visible: isPrinting_active()" class="btn btn-primary" type="button" disabled>
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Printing...
        </button>
        <div class="d-flex flex-row-reverse mx-2">
            <a target="_blank" id='savings-transaction-excel-link'>
                <button class="btn btn-sm btn-secondary">
                <i class="fa fa-file-excel-o fa-2x"></i>
                </button>
            </a>
        </div>
    </div>
        
        
    <?php } ?>
<br>
    <div class="col-lg-12">
        <div class="table-responsive">
                <table class="table-bordered display compact nowrap table-hover" width="100%" id="tblTransaction">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Transaction No.</th>
                        <th>Transaction Date</th>
                        <th>Account No</th>
                        <th>Account Name</th>
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
