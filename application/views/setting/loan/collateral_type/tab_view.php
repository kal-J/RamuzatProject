    <div role="tabpanel" id="tab-collateral_docs_setup" class="tab-pane">
        <div class="panel-body">
            <div>
                <h3>
                    <center>Collateral type
                        
                    <?php if(in_array('1', $loan_product_privilege)){ ?>
                        <a data-toggle="modal" href="#add_collateral_docs_setup-modal" class="btn btn-primary btn-sm pull-right">
                            <i class="fa fa-plus-circle"></i> New Collateral Type
                        </a>
                        
                    <?php } ?>
                    </center>
                </h3>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="table-responsive">
                    <table id="tblCollateral_docs_setup" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                        <thead>
                            <tr>
                                <th>Collateral type name</th>
                                <th>Description</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div><!-- /.table-responsive-->
        </div>
    </div><!--End of collateral_docs section-->
