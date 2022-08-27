                        <div role="tabpanel" id="tab-loan_product" class="tab-pane">
                            <?php if(in_array('1', $loan_product_privilege)){ ?>
                            <div><h3><center>Loan products<a data-toggle="modal" href="#add_loan_product-modal" class="btn btn-sm btn-default pull-right"><i class="fa fa-plus-circle"></i> New Loan Product</a></center></h3></div>
                            <?php } ?>
                            <div class="hr-line-dashed"></div>
                            <div class="table-responsive">
                                <table id="tblLoan_product" class="table table-striped table-bordered table-hover" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Interest Calculated</th>
                                            <th>Source Fund</th>
                                            <th>Available To</th>
                                            <th>Status</th>
                                            <th>&nbsp; </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive-->
                        </div>
