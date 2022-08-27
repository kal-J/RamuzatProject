<div role="tabpanel" id="tab-expense" class="tab-pane ">
     <div class="pull-right add-record-btn">
     <?php if(in_array('1', $accounts_privilege)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_expense-modal"><i class="fa fa-plus-circle"></i> Add an Expense </button>
    <?php } ?>
    </div>
    <h3><center>Expenses</center></h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table id="tblExpense"  border="0" class="table-bordered display compact nowrap" style="width:100%">
            <thead class="thead-light" >
                <tr>
                    <!--th>#</th-->
                    <th>Receipt/Ref#</th>
                    <th>Supplier/Vendor</th>
                    <th>Payment Date</th>
                    <th>Affected Cash Account</th>
                    <th>Applied Tax</th>
                    <th>Narrative</th>
                    <th>Total Amount</th>
                    <th>Attachment</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th colspan="4">&nbsp;</th>
                    <th>0</th>
                    <th colspan="4">&nbsp;</th>
                </tr>
            </tfoot>
        </table>
    </div><!-- /.table-responsive-->
</div>
<?php $this->load->view('accounts/expense/add_modal'); ?>
