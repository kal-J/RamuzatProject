<div id="tab-transaction-date-control" class="tab-pane">
    <div class="pull-right add-record-btn">
    <?php if(in_array('1', $privileges)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_transaction_date_control"><i class="fa fa-plus-circle"></i> Add New Control</button>
    <?php } ?>
    </div>
    <h3 class="mt-2 d-flex justify-content-center">Transaction Date Controls</h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table class="table  table-bordered table-hover" id="tblTransactionDateControl" width="100%" >
            <thead>
                <tr>
                    <th>Date Control</th>
                    <th>Staff</th>
                    <th>Description</th> 
                    <th>Action</th> 
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
     <?php $this->load->view('setting/transaction/date_controls/add_control'); ?>
</div>

