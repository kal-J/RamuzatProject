<div role="tabpanel" id="tab-declarations" class="tab-pane">
    <div class="pull-right add-record-btn">
     <?php if(in_array('1', $accounts_privilege)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_dividend_declaration-modal"><i class="fa fa-plus-circle"></i> Add Dividend Declaration </button>
    <?php } ?>
    </div>
    <h3><center>Dividend Declarations</center></h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table id="tblDividend_declaration"  border="0" class="table-bordered display compact nowrap" style="width:100%">
            <thead class="thead-light" >
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Declaration Date</th>
                    <th>Date of Record</th>
                    <th>Date of Payment</th>
                    <th>Retained Earnings A/C</th>
                    <th>Dividends Liability A/C</th>
                    <th>Payable Dividends</th>
                    <th>Declared Dividends</th>
                    <th>Dividends Per Share</th>
                    <th>Paid To</th>
                    <th>Notes</th>
                    <th>Attachment</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7">Total</th>
                    <th>0</th>
                    <th colspan="6">&nbsp;</th>
                </tr>
            </tfoot>
        </table>
    </div><!-- /.table-responsive-->
</div>
<?php $this->load->view('accounts/dividend/declaration/add_modal');
$this->load->view('accounts/dividend/payout/add_modal');