<div role="tabpanel" id="tab-loan_payable_today" class="tab-pane loans">
    <div class="panel-title">
        <center>
            <h3 style="font-weight: bold;">Loans Payable Today (<?php echo date('d-M-Y'); ?>)</h3>
        </center>
    </div>

    <div class="row d-flex justify-content-between my-2">
        <div class="row col-10 d-flex justify-content-center m-3">
           
            <div class="input-group date col-4">
                <label class="my-auto" for="current_date">Current Date:&nbsp;</label>
                <input class="col-6" autocomplete="off" placeholder="DD-MM-YYYY" value="<?php echo date('d-m-Y'); ?>" type="text" onkeydown="return false" name="current_date" id="current_date" required />
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            </div>
            <div class="col-1 mx-0">
                <button onclick="filter_pyables_by_date()" class="btn btn-sm btn-primary">
                    <i class="fa fa-filter fa-2x"></i>
                </button>
            </div>

        </div>
        <div class="col-lg-1 d-flex flex-row-reverse align-items-center pull-right">
            <button id="btn_print_loan_payable_today" onclick="handlePrint_loan_payable_today()" class="btn btn-sm btn-primary"><i class="fa fa-print fa-2x"></i></button>
            <button id="btn_printing_loan_payable_today" class="btn btn-primary" type="button" disabled>
                <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                Printing...
            </button>

        </div>
    </div>
    <br>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover dataTables-example" id="tblPayable_today" style="width: 100%">
            <thead>
                <tr>
                    <th>Ref #</th>
                    <th>Client Name</th>
                    <th>Product Name</th>
                    <th>Requested Amount (UGX)</th>
                    <th>Disbursed Amount (UGX)</th>
                    <th>Expected Interest (UGX)</th>
                    <th>Paid Amount (UGX)</th>
                    <th>Amount Demanded (UGX)</th>
                    <th>Remaining bal (UGX)</th>
                    <th>Disbursement Date</th>
                    <th>Next Pay Date</th>
                    <th>Loan Due Date</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Totals</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div><!-- ==END TAB-PENDING APPROVAL =====-->