                        <div role="tabpanel" id="tab-loan_installment_rate" class="tab-pane">
                            <div class="hr-line-dashed"></div>
                            <?php if(in_array('1', $privileges)){ ?>
                            <div><a data-toggle="modal" href="#add_loan_installment_rate-modal" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus-circle"></i> New Loan product fee</a></div>
                            <?php } ?>
                            <h3><center>Loan installment</center></h3>
                            <div class="hr-line-dashed"></div>
                            <div class="table-responsive">
                                <table id="tblLoan_installment_rate" class="table table-striped table-bordered table-hover small m-t-md" width="100%">
                                    <thead>
                                        <tr>
                                              <th>loan installment rate</th>
                                              <th>loan installment unit</th>
                                              <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive-->
                        </div>

