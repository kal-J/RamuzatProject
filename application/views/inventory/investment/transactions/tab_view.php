 <div role="tabpanel" id="tab-investment_trans" class="tab-pane">
     <div class="pull-right add-record-btn">
    <!-- <?php if(in_array('1', $accounts_privilege)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_gain_loss_modal"><i class="fa fa-plus-circle"></i> New Entry </button>
    <?php } ?>-->
    </div>
   
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table id="tblInvestment_trans"  border="0" class="table-bordered display compact nowrap" style="width:100%">
            <thead class="thead-light" >
                <tr>
                    <th>Transaction Type</th>
                    <th>Transaction No</th>
                    <th>Transaction Date</th>
                    <th>Payment Mode</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Narative</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
             <tfoot>
                    <tr>
                        <th colspan="4">Totals</th>
                        <th>0 </th>
                          <th>0 </th>
                           <th></th>
                          

                        <th colspan="2">&nbsp;</th> 
                    </tr>
                </tfoot>
        </table>
    </div><!-- /.table-responsive-->
</div>

<?php $this->load->view('inventory/investment/transactions/add_gain_loss_modal'); ?>
<?php $this->load->view('inventory/investment/transactions/reverse_modal'); ?>
 

