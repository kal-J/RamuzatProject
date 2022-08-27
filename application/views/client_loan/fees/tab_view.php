<div role="tabpanel" id="tab-loan_fee" class="tab-pane">
    <div class="panel-body">
        <div><h3>Applied fees</h3><?php if (isset($loan_detail)) { if(in_array('1', $client_loan_privilege)){ ?><a data-toggle="modal" href="#apply_loan_fee-modal" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus-circle"></i> Attach Fee(s)</a><?php } } ?></div>
<?php if (!isset($loan_detail)) { ?>
        <div class="row col-10 d-flex justify-content-center m-3">
      <div class="input-group date col-4">
        <label class="my-auto" for="start_date">From :&nbsp;</label>
        <input class="col-6" autocomplete="off" placeholder="DD-MM-YYYY" value="" type="text" onkeydown="return false" name="start_date" id="loan_fees_start_date" required />
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
      </div>
      <div class="input-group date col-4">
        <label class="my-auto" for="end_date">To :&nbsp;</label>
        <input class="col-6" autocomplete="off" placeholder="DD-MM-YYYY" value="<?php echo date('d-m-Y'); ?>" type="text" onkeydown="return false" name="end_date" id="loan_fees_end_date" required />
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
      </div>
      <div class="col-1 mx-0">
      <button onclick="filter_loan_fees_by_date()" class="btn btn-sm btn-primary">
        <i class="fa fa-filter fa-2x"></i>
      </button>
      </div>
    </div>
  <?php } ?>
        <br>
        <div class="pull-left add-record-btn">
          <div class="panel-title">
          </div>
        </div>
        <div class="table-responsive">
                <table id="tblApplied_loan_fee" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                    <thead>
                        <tr>
                            <?php if (!isset($loan_detail)) { ?>
                                <th>Ref #</th>
                                <th>Client Name</th>
                            <?php } ?>
                            <th>Fee</th>
                            <th>Date Applied</th>
                            <th>Amount</th>
                            <th>Date Paid</th>
                            <th>Status</th>
                            <?php if (isset($loan_detail)) { ?>
                                <th>Action</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- /.table-responsive-->
    </div>
</div><!--End of fees section-->
