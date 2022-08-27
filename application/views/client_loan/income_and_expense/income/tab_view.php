<div role="tabpanel" id="tab-monthly_income" class="tab-pane">
    <div class="panel-body">
    <div><h3>Monthly Income</h3><?php if(in_array('1', $client_loan_privilege)){ ?><a data-toggle="modal" href="#add_monthly_income-modal" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus-circle"></i> Add income item</a><?php  } ?></div>
        <div class="table-responsive">
                <table id="tblClient_loan_monthly_income" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                    <thead>
                        <tr>
                            <th>Income item</th>
                            <th>Amount (UGX)</th>
                            <th>Description</th>
                            <th>Action</th> 
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- /.table-responsive-->
    </div>
</div><!--End of fees section-->
