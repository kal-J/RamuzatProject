<?php
    $start_date1 = date('d-M-Y', strtotime($fiscal_year['start_date']));
    $end_date1 = date('d-M-Y', strtotime($fiscal_year['end_date']));
?>
<div role="tabpanel" id="tab-transactions" class="tab-pane<?php if(!isset($account_types)){echo " active";} ?>">
                            <?php if(isset($ledger_account['id'])){?>
                            <?php $this->load->view('accounts/account_details'); ?>
                            <?php } ?>
    <div class="hr-line-dashed"></div>
   <h3 id="tab_title">
   <center>Journal Transactions  { <?php echo $start_date1; ?> - <?php echo $end_date1; ?> }</center>
   </h3>
    <div class="hr-line-dashed"></div>
    <div class="panel-body"><br>
        <div class="col-lg-12">
            <div class="table-responsive">
                 
                <table class="table-bordered display compact nowrap table-hover" id="tblJournal_transaction_line" width="100%" >
                    <thead>
                        <tr>
                            <th>Transaction No.</th>
                            <th>Date</th>
                            <th>Ref. ID</th>
                            <th>Ref. NO</th>
                            <th>Journal Type</th>
                            <th>Narrative</th>
                            <?php if(!isset($ledger_account['id'])){?>
                            <th>Account</th>
                            <?php } ?>
                            <th>Debit </th>
                            <th>Credit </th>
                            <th>#</th> 
                            <th>#</th> 
                            <th>#</th> 
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="<?php if(isset($ledger_account['id'])){echo 6;}else{echo 7;} ?>">Totals</th>
                            <th>Debit </th>
                            <th>Credit </th>
                            <th>&nbsp;</th> 
                            <th>&nbsp;</th> 
                            <th>&nbsp;</th> 
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
    <?php $this->load->view('accounts/transaction/post_entry.php'); ?>

