<div role="tabpanel" id="tab-active_accounts" class="tab-pane active savings">
    <br>
    <div class="row align-items-center">
        <div class="col-lg-2 my-1">
            <a class="btn btn-sm btn-secondary text-white" href="<?php // echo base_url();?>transaction/export_excel"><i
                    class="fa fa-download text-white"></i> Download Template </a>
        </div>

        
        <div class="col-lg-7 my-1 d-flex justify-content-left align-items-left">

        <select class="" id="gender" name="gender" style="width:110px; height:30px;">
            <option value="All">--select --</option>
            <option value="1">--Male --</option>
            <option value="0">--Female --</option>
        </select>&nbsp;&nbsp;

           <select class="" id="producttype" name="producttype" style="width:110px; height:30px;">
               <option value="All">--select --</option>
               <?php foreach($products as $product){?>
               <option value="<?php  echo $product['id'];?>"><?php  echo $product['productname'];?></option>"
              <?php }?>
           </select>&nbsp;&nbsp;
        
            <div>
                <div class="input-group date"
                    data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>"
                    data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>">
                    <input autocomplete="off" placeholder="DD-MM-YYYY" value="<?php echo date('d-m-Y'); ?>" type="text"
                        onkeydown="return false" name="balance_end_date" id="balance_end_date" required />
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
            <span>
                <button onclick="balance_end_date_preview(event)" class="btn btn-primary">preview</button>
            </span>
        </div>
        <div class="col-lg-3 my-1 d-flex flex-row-reverse">
            <a class="btn btn-sm btn-primary text-white" href="#" data-toggle="modal"
                data-target="#bulk_deposit-modal"><i class="fa fa-file-excel-o text-white"></i> Bulk Deposit </a>
        </div>
    </div>

    <div class="mt-2 d-flex flex-row-reverse">

    <!-- <form action="<?php echo site_url("savings_account/active_savings_print_out"); ?>" method="post">
    <input type="hidden" name="state_id" value="7">

    <button type="submit" class="btn btn-sm btn-secondary">
    </form> -->
         <button data-bind="visible: !isPrinting_active()"  onclick="handlePrint_active_savings()" class="btn btn-sm btn-secondary">
        <i class="fa fa-print fa-2x"></i></button>
        <button data-bind="visible: isPrinting_active()" class="btn btn-primary" type="button" disabled>
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Printing...
        </button>
        <div class="d-flex flex-row-reverse mx-2">
            <a target="_blank" id='active-savings-excel-link'>
                <button class="btn btn-sm btn-secondary">
                <i class="fa fa-file-excel-o fa-2x"></i>
                </button>
            </a>
        </div>
    </div>
  <br>
    <div class="col-lg-12">
        <div class="table-responsive">
                <table class="table  table-bordered table-hover" id="tblSavings_account" width="100%" >
                <thead>
                    <tr>
                        <th>Account No</th>
                        <th>Account Holder</th>
                        <th>Product</th>
                        <th>Account Type</th>
                        <th>Account Balance</th>
                        <th>Available Balance</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
            <tr>
                <th colspan="3">Totals</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
            </tr>
        </tfoot>
            </table>
        </div>
    </div>
</div>
                        
                        
                        
             