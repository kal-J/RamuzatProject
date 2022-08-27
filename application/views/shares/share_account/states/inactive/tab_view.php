<div role="tabpanel" id="tab-share_inactive_accounts" class="tab-pane">
    <div class="row d-flex mt-3 flex-row-reverse mr-2">
        <?php if(isset($client_type) && $client_type == 2) { ?>
        <?php } else { ?>
        <button id="btn_print_inactive_shares" onclick="handlePrint_inactive_shares()"
            class="ml-2 btn btn-sm btn-secondary"><i class="fa fa-print fa-2x"></i></button>
        <button id="btn_printing_inactive_shares" class="btn btn-primary ml-2" type="button" disabled>
            <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
            Printing...
        </button>
        <div class="">
            <a target="_blank" href="shares/export_excel/19/1">
                <button class="btn btn-sm btn-secondary">
                    <i class="fa fa-file-excel-o fa-2x"></i>
                </button>
            </a>
        </div>
        <?php } ?>
    </div>
    <br>
    <div class="col-lg-12">
        <div class="pull-right add-record-btn">
            <div class="panel-title">

            </div>
        </div>
        <div class="table-responsive">
            <table class="table  table-bordered table-hover" id="tblShares_Inactive_Account" width="100%">
                <thead>
                    <tr>
                        <th>Share A/C NO</th>
                        <th>Account Name</th>
                        <th>Price Per Share (UGX)</th>
                        <th>No of Shares</th>
                        <th>Total Amount (UGX)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">Totals</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>