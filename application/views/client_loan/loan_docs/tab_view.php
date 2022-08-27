    <div role="tabpanel" id="tab-loan_docs" class="tab-pane">
        <div class="panel-body">
            <div><h3>Loan documents</h3><?php if(in_array('1', $client_loan_privilege)){ ?><a data-toggle="modal" href="#add_loan_docs-modal" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus-circle"></i> Add Document</a><?php } ?></div>
            <div class="table-responsive">
                    <table id="tblClient_loan_doc" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                        <thead>
                            <tr>
                                <th>File Type</th>
                                <th>Description</th>
                                <th>Action</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div><!-- /.table-responsive-->
        </div>
    </div><!--End of loan_docs section-->
