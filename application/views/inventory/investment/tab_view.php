 <div role="tabpanel" id="tab-investment" class="tab-pane">
     <div class="pull-right add-record-btn">
     <?php if(in_array('1', $accounts_privilege)){ ?>
        <button class="btn btn-primary btn-sm gain_or_loss" type="button" data-toggle="modal" data-target="#add_investment_modal"><i class="fa fa-plus-circle"></i> Add Investment </button>
    <?php } ?>
    </div>
    <h3><center>Investment Register</center></h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table id="tblInvestment"  border="0" class="table-bordered display compact nowrap" style="width:100%">
            <thead class="thead-light" >
                <tr>
                    <th>Type</th>
                    <th title="The length of tenure">Investment Account</th>
                    <th>Date Created</th>
                     <th>Balance</th>
                     <th>Gain</th>
                    <th>Loss</th>
                    <!--<th>Withdraw</th>-->
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
              <tfoot>
                    <tr>
                        <th colspan="3">Totals</th>
                        <th>0 </th>
                          <th>0 </th>
                           <th>&nbsp;</th>
                            
                             
                               
                       

                        <th colspan="2">&nbsp;</th> 
                    </tr>
                </tfoot>
        </table>
    </div><!-- /.table-responsive-->
</div>

<?php $this->load->view('inventory/investment/add_modal'); ?>
<?php $this->load->view('inventory/investment/transactions/add_gain_loss_modal'); ?>



