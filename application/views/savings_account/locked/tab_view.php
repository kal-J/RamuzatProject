<div role="tabpanel" id="tab-locked" class="tab-pane">
            <div class="panel-body">
            <?php if(in_array('18', $savings_privilege)){ ?>
             <button class="btn btn-primary btn-sm pull-right" type="button" data-toggle="modal" data-target="#add_locked_amount"><i class="fa fa-edit"></i> Lock Amount </button>
          <?php } ?>
          <br>
            <div class="col-lg-12">
                <div class="table-responsive">
                        <table class="table  table-bordered table-hover" id="tblLock_savings" width="100%" >
                        <thead>
                        <tr>
                            <th>Account No</th>
                            <th>Calculated As</th>
                            <th>Locked Amount</th>
                            <th>Date Locked</th>
                            <th>Action</th> 
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</div>