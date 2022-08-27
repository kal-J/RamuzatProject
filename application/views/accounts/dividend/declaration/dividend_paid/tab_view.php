<br>
<div class="pull-right add-record-btn">
    <?php if(in_array('1', $accounts_privilege)){ ?>
        <a href="#pay_dividend-modal" data-toggle="modal" id="active_share_issuance" class="btn btn-success btn-sm"> <i class="fa fa-check"></i> Payout</a>
    <?php } ?>
</div>
<div class="pull-left add-record-btn">
    <?php if(in_array('6', $accounts_privilege)){ ?>
        <a href="#print-modal" data-toggle="modal" class="btn btn-default btn-sm"> <i class="fa fa-print fa-2x"></i> </a>
    <?php } ?>
</div>
<h3 data-bind="with: dividend_declaration"><center><span data-bind="text:'Dividends Paid'"></span></center></h3>
<div class="table-responsive">
     <table class="table  table-bordered table-hover" id="tblDividend_paid" width="100%" >
        <thead>
            <tr> 
                <th>Share A/C NO</th>
                <th>Account Name</th>
                <th>Dividend Per Share (UGX)</th> 
                <th>No for Shares</th> 
                <th>Dividend Paid (UGX)</th> 
                <th>Date Paid</th> 
                <th>Status</th> 
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

