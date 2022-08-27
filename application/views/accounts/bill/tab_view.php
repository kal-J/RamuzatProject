<div role="tabpanel" id="tab-bill" class="tab-pane ">
     <div class="pull-right add-record-btn">
     <?php if(in_array('1', $accounts_privilege)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_bill-modal"><i class="fa fa-plus-circle"></i> New Bill </button>
    <?php } ?>
    </div>
    <h3><center>Bills</center></h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table id="tblBill"  border="0" class="table-bordered display compact nowrap" style="width:100%">
            <thead class="thead-light" >
                <tr>
                    <th>Receipt #</th>
                    <th>Supplier/Vendor</th>
                    <th>Bill Date</th>
                    <th>Due Date</th>
                    <th>Applied Tax</th>
                    <th>Narrative</th>
                    <th>Total Amount</th>
                    <th>Discount</th>
                    <th>Amount Paid</th>
                    <th>Amount Due</th>
                    <th>Status</th>
                    <th>Attachment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4">&nbsp;</th>
                    <th colspan="2">Totals (UGX)</th>
                    <th>0</th>
                    <th>0</th>
                    <th>0</th>
                    <th colspan="3">&nbsp;</th>
                </tr>
            </tfoot>
        </table>
    </div><!-- /.table-responsive-->
</div>
<?php 
$this->load->view('accounts/bill/add_modal'); 
$this->load->view('accounts/bill/payment/add_modal'); 

