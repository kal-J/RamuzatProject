                        <div role="tabpanel" id="tab-loan_fee" class="tab-pane">
                            <?php if(in_array('1', $loan_product_privilege)){ ?>
                            <div><h3><center>Loan fees<a data-toggle="modal" href="#add_loan_fee-modal" class="btn btn-sm btn-default pull-right"><i class="fa fa-plus-circle"></i> New Loan Fee</a></center></h3></div>
                            <?php } ?>
                            <div class="hr-line-dashed"></div>
                            <div class="table-responsive">
                                <table id="tblLoan_fee" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                                    <thead>
                                        <tr>
                                              <th>Fee Name</th>
                                              <th>Fee Type</th>
                                              <th>Amount Calculated As</th>
                                              <th>Amount</th>
                                              <th title="Method for calculating when the fee will be applied">Charge Trigger</th>
                                              <th>Linked Income A/C</th>
                                              <th>Linked Income Receivable A/C</th>
                                              <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive-->
                        </div>

