<div role="tabpanel" id="tab-collateral" class="tab-pane">
    <div class="panel-body">
        <div>
            <h3>Collateral</h3><?php if (in_array('1', $client_loan_privilege)) { ?>
            <a data-toggle="modal" href="#add_collateral-modal" class="btn btn-success btn-sm pull-right mx-2 mb-1"><i class="fa fa-plus-circle"></i> Add New Collateral</a> 
            <button onclick="fetch_collateral_data()" class="btn btn-primary btn-sm pull-right mb-1"><i class="fa fa-plus-circle"></i> Attach Existing Collateral</button> 
            <?php } ?>
        </div>
        <div class="table-responsive">
            <table id="tblLoan_collateral" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Item value</th>
                        <th>File name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div><!-- /.table-responsive-->
    </div>
</div>
<!--End of fees section-->