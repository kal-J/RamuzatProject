<div role="tabpanel" id="tab-sales" class="tab-pane">
    <br>
    <div class="pull-right add-record-btn">
        <?php if (in_array('1', $accounts_privilege)) { ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#new_sales-modal" ><i class="fa fa-shopping-cart"></i> &nbsp; New Sale</button>
        <?php } ?>
    </div>
    <?php if (in_array('1', $accounts_privilege)) { ?>
       <!--  <button class="btn btn-success btn-sm" type="button" data-toggle="modal" data-target="#post_loans-modal" ><i class="fa fa-plus-circle"></i> Post Loans</button> -->
        <?php } ?>
    <h3><center>Sales Transactions</center></h3>
    <div class="hr-line-dashed"></div>
    <div class="panel-body">
        
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table-bordered display compact nowrap table-hover" id="tblSales_transaction" width="100%" >
                    <thead>
                        <tr>
                            <th>Item Sold</th>
                            <th>Ref. N0</th>
                            <th>Buyer</th>
                            <th>Date</th>
                            <th>Narrative</th>
                            <th>Amount </th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5">Total</th>
                            <th>0 </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
