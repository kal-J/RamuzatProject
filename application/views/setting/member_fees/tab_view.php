    <div role="tabpanel" id="tab-member_fees" class="tab-pane">
        <div class="panel-body">
        <?php if(in_array('1', $membership_privilege)){ ?>
            <div><h3><center>Member fees</strong><a data-toggle="modal" href="#add_member_fees-modal" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus-circle"></i>Member fees</a></center></h3></div>
        <?php } ?>
            <div class="hr-line-dashed"></div>
            <div class="table-responsive">
                    <table id="tblMember_fees" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                        <thead>
                            <tr>
                                <th>Fee name</th>
                                <th>Amount</th>
                                <th>Required fee</th>
                                <th>Income A/C</th>
                                <th>Receivable A/C</th>
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
