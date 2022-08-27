<div class="panel-body">

  <div id="enable_print" class="row">
    <div class="col-lg-4">
    </div>

    <div class="col-lg-6">
      <div id="reportrange" class="reportrange pull-right">
        <i class="fa fa-calendar"></i>
        <span>December 30, 2018 - Feb 11, 2019</span> <b class="caret"></b>
      </div>
    </div>
    <div class="col-lg-2">
     <button onclick="handlePrint()"  class="btn btn-sm btn-primary btn-flat"> Print Statement </button>
    </div> 



</div>
 <div class="row col-10 d-flex justify-content-center m-3">
      <div  id="btn3" class="input-group date col-4">
        <label class="my-auto" for="start_date">From :&nbsp;</label>
        <input class="col-6" autocomplete="off" placeholder="DD-MM-YYYY" value="" type="text" onkeydown="return false" name="start_date" id="start_date" required />
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
      </div>
       <div id="btn4" class="input-group date col-4"
                    data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>"
                    data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>">
                    <input autocomplete="off" placeholder="DD-MM-YYYY" value="<?php echo date('d-m-Y'); ?>" type="text"
                        onkeydown="return false" name="end_date" id="end_date" required />
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
      <div class="col-1 mx-0">
      <button id="btn5" onclick="filter_transaction_by_date()" class="btn btn-sm btn-primary">
      Filter
      </button>
      </div>
      
    </div>
  <div class="panel-list pull-right" id="template_btns">
          
              <a class="btn btn-sm btn-secondary text-white" href="#" data-toggle="modal" data-target="#bulk_deposit_template-modal" id="btn1"><i
                    class="fa fa-download text-white"></i> Download Template </a>
                   
            <a class="btn btn-sm btn-primary text-white" href="#" data-toggle="modal"
                data-target="#bulk_transaction-modal" id="btn2"><i class="fa fa-file-excel-o text-white"></i> Bulk Transaction </a>
</div><br>
<br>
    <div class="col-lg-12">
        <div class="table-responsive">
                <table class="table-bordered display compact nowrap table-hover" id="tblShare_transaction" width="100%" >
                <thead>
                    <tr>
                        <th>Transaction No.</th>
                        <th>Account Name</th>
                        <th>Transaction Date</th>
                        <th>Payment Mode</th>
                        <th>Type </th>
                        <th>Account No</th>
                        <th>Debit</th>
                        <th>Credit</th>
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
  <br>
 