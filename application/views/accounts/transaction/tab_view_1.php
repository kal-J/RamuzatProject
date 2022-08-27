
<div role="tabpanel" id="tab-transactions" class="tab-pane active">
    <br>
    <div class="pull-right add-record-btn">
        <?php if (in_array('1', $accounts_privilege)) { ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#post_entry-modal" ><i class="fa fa-plus-circle"></i> New Entry</button>
        <?php } ?>
    </div>
    <?php if (in_array('1', $accounts_privilege)) { ?>
       <!--  <button class="btn btn-success btn-sm" type="button" data-toggle="modal" data-target="#post_loans-modal" ><i class="fa fa-plus-circle"></i> Post Loans</button> -->
        <?php } ?>
    <h3><center>Journal Transactions</center></h3>
    <div class="hr-line-dashed"></div>
    <div class="panel-body">
        
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table-bordered display compact nowrap table-hover" id="tblJournal_transaction" width="100%" >
                    <thead>
                        <tr>
                            <th>Transaction ID.</th>
                            <th>Ref. N0</th>
                            <th>Ref. ID</th>
                            <th>Date</th>
                            <th>Type </th>
                            <th>Narrative</th>
                            <th>Amount </th>
                            <th>Action</th> 
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6">Total</th>
                            <th>0 </th>
                            <th>&nbsp;</th> 
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php $this->load->view('accounts/transaction/post_entry.php'); ?>
</div>

