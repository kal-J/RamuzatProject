    <div role="tabpanel" id="tab-user_doc_type" class="tab-pane">
        <div class="panel-body">
        <?php if(in_array('1', $privileges)){ ?>
            <div><h3><center>User Document type</strong><a data-toggle="modal" href="#add_user_doc_type-modal" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus-circle"></i>New document type</a></center></h3></div>
        <?php } ?>
            <div class="hr-line-dashed"></div>
            <div class="table-responsive">
                    <table id="tblUser_doc_type" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                        <thead>
                            <tr>
                                <th>Document type</th>
                                <th>Description</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div><!-- /.table-responsive-->
        </div>
    </div><!--End of loan_docs section-->
