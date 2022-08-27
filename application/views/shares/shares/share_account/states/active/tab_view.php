<div role="tabpanel" id="tab-share_active_accounts" class="tab-pane active">
    <div class="row d-flex flex-row-reverse mt-3 mr-4">

        <?php if(isset($client_type) && $client_type == 2) { ?>
        <?php } else { ?>

        <div class="ml-2">
            <button id="btn_print_active_shares" onclick="handlePrint_active_shares()" class="btn btn-sm btn-secondary">
                <i class="fa fa-print fa-2x"></i>
            </button>
            <button id="btn_printing_active_shares" class="btn btn-primary" type="button" disabled>
                <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                Printing...
            </button>
        </div>

        <div class="ml-2">
            <a target="_blank" href="shares/export_excel/7/1">
                <button class="btn btn-sm btn-secondary">
                    <i class="fa fa-file-excel-o fa-2x"></i>
                </button>
            </a>
        </div>
        
        <?php } ?>

        <div class="">
            <div class="panel-title">
                <?php if (in_array('1', $share_privilege)) { ?>
                <a href="#add_share_account-modal" data-toggle="modal" id="active_share_issuance"
                    class="btn btn-primary btn-lg text-white"> <i class="fa fa-plus-circle"></i> New Share Account</a>
                <?php } ?>
            </div>

        </div>

         <div class="col-lg-4 my-1 d-flex" style="margin-right: 80px;">
            <div>
                <div class="input-group date"
                    data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>"
                    data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>">
                    <input autocomplete="off" placeholder="DD-MM-YYYY" value="<?php echo date('d-m-Y'); ?>" type="text"
                        onkeydown="return false" name="end_date1" id="end_date1" required />
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
            <span>
                <button onclick="transaction_end_date_preview(event)" class="btn btn-primary">preview</button>
            </span>
        </div>
    </div>



    <br>
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table  table-bordered table-hover" id="tblShares_Active_Account" width="100%">
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