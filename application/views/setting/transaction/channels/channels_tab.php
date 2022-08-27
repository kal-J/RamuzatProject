<div id="tab-transaction_channel" class="tab-pane">
    <div class="pull-right add-record-btn">
    <?php if(in_array('1', $privileges)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_transaction_channel"><i class="fa fa-plus-circle"></i> Add Transaction Channel </button>
    <?php } ?>
    </div>
    <h3><center>Transaction channels</center></h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table class="table  table-bordered table-hover" id="tblTransactionChannel" width="100%" >
            <thead>
                <tr>
                    <th>Transaction Channel (Till)</th>
                    <th>Staff</th>
                    <th>Linked Account</th> 
                    <th>Description</th> 
                    <th>Action</th> 
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
     <?php $this->load->view('setting/transaction/channels/add_channel'); ?>
</div>

