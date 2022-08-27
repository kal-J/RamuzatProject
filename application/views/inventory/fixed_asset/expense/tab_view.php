<div class="panel-body"><br>
    <div class="col-lg-12">
        <p>
            <center><strong>Expense Transactions</strong></center>
            <?php if (in_array('3', $accounts_privilege)) { ?>
                <a  data-toggle="modal" href="#add_expense-modal" class="btn btn-sm btn-primary pull-right"><i class="fa fa-edit"></i> Add Expense</a>
            <?php } ?>
        </p>
        <div class="hr-line-dashed"></div>
        <div class="table-responsive">
            <table class="table-bordered  display compact nowrap table-hover" id="tblAsset_expense" width="100%" >
                <thead>
                    <tr>
                        <th>Transaction No</th>
                        <th>Expense Type</th>
                        <th>Transn. Date</th>
                        <th>Type</th>
                        <th>Amount </th>
                        <th>Payment Mode </th>
                        <th>Fund Source A/C</th>
                        <th>Expense Account </th>
                        <th>Narrative</th>
                        <th>Action</th> 
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4">Totals</th>
                         
                        <th>0 </th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th> 
                        <th>&nbsp;</th> 
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php
$this->load->view('inventory/fixed_asset/expense/add_modal'); 
$this->load->view('inventory/fixed_asset/expense/edit_expense_transaction'); 
?>
