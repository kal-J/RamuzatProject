<div role="tabpanel" id="tab-savings_account_pending" class="tab-pane savings">
    <div class="d-flex flex-row-reverse mt-2">
        <div class="mr-4 d-flex flex-row-reverse">
            <button data-bind="visible: !isPrinting_pending()" onclick="handlePrint_pending_savings()" class="btn btn-sm btn-secondary"><i class="fa fa-print fa-2x"></i></button>
            <button data-bind="visible: isPrinting_pending()" class="btn btn-primary" type="button" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Printing...
            </button>
        </div>
        <div class="d-flex flex-row-reverse mx-2">
            <a target="_blank" href="savings_account/export_excel/5">
                <button class="btn btn-sm btn-secondary">
                    <i class="fa fa-file-excel-o fa-2x"></i>
                </button>
            </a>
        </div>
    </div>


    <br>
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table  table-bordered table-hover" id="tblSavings_account_pending" width="100%">
                <thead>
                    <tr>
                        <th>Account No</th>
                        <th>Account Holder</th>
                        <th>Product</th>
                        <th>Client Type</th>
                        <th>Account Balance</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>