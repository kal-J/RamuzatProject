<div role="tabpanel" id="tab-share_fee" class="tab-pane">
                            <div class="hr-line-dashed"></div>
                            <?php if(in_array('1', $share_issuance_privilege)){ ?>
                            <div><a data-toggle="modal" href="#add_share_fee-modal" class="btn btn-sm btn-default pull-right"><i class="fa fa-plus-circle"></i> New Share Fee</a></div>
                            <?php } ?>
                            <h3><center>Share Fees</center></h3>
                            <div class="hr-line-dashed"></div>
                            <div class="table-responsive">
                                <table id="tblShare_fee" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                                    <thead>
                                        <tr>
                                              <th>Share Name</th>
                                              <th>Amount Calculated As</th>
                                              <th>Amount</th>
                                              <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive-->
                        </div>

