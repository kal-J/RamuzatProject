<div role="tabpanel" id="tab-share_pending_accounts" class="tab-pane">
    <div class="row d-flex flex-row-reverse my-3 mr-4">
        <?php if(isset($client_type) && $client_type == 2) { ?>
        <?php } else { ?>
        <div class="ml-2">
            <button id="btn_print_pending_shares" onclick="handlePrint_pending_shares()"
                class="btn btn-sm btn-secondary"><i class="fa fa-print fa-2x"></i></button>
            <button id="btn_printing_pending_shares" class="btn btn-primary" type="button" disabled>
                <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                Printing...
            </button>
        </div>
        <div class="ml-2">
            <a target="_blank" href="shares/export_excel/5/1">
                <button class="btn btn-sm btn-secondary">
                    <i class="fa fa-file-excel-o fa-2x"></i>
                </button>
            </a>
        </div>
        <?php } ?>

        <div class="">
            <div class="panel-title mx-1">
                <?php if (in_array('1', $share_privilege)) { ?>
                <a href="#add_share_account-modal" data-toggle="modal" class="btn btn-primary btn-lg"> <i
                        class="fa fa-plus-circle"></i> New Share Account</a>
                <?php } ?>
            </div>
        </div>

    </div>

    <div class="table-responsive">
        <table class="table  table-bordered table-hover" id="tblShares_Pending_Account" width="100%">
            <thead>
                <tr>
                    <th>Share A/C NO</th>
                    <th>Applicant </th>
                    <th>Total Amount (UGX)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>