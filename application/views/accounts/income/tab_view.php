<div role="tabpanel" id="tab-income" class="tab-pane">
     <div class="pull-right add-record-btn">
     <?php if(in_array('1', $accounts_privilege)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_income-modal"><i class="fa fa-plus-circle"></i> New Sale </button>
    <?php } ?>
    </div>
    <h3><center>Sales</center></h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table id="tblIncome"  border="0" class="table-bordered display compact nowrap" style="width:100%">
            <thead class="thead-light" >
                <tr>
                    <th>Invoice/Ref#</th>
                    <th>Client</th>
                    <th>Receipt Date</th>
                    <th>Revenue/Cash A/C</th>
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
                    <th colspan="4">&nbsp;</th>
                    <th colspan="2">Total</th>
                    <th>0</th>
                    <th colspan="3">&nbsp;</th>
                </tr>
            </tfoot>
        </table>
    </div><!-- /.table-responsive-->
</div>
<?php $this->load->view('accounts/income/add_modal');