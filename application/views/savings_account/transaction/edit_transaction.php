<div class="modal inmodal fade" id="edit_transaction-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <form method="post" class="formValidate" action="<?php echo site_url("transaction/edit_transaction"); ?>" id="formTransaction">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "Edit Transaction";
                        }
                        ?></h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                <input type="hidden" name="id">
                <input type="hidden" name="transaction_no">
                     <div class="form-group row">
                             <label class="col-lg-4 col-form-label">Transaction Date<span class="text-danger">*</span></label>
                            <div class="col-lg-8 form-group" >
                                <div class="input-group date" data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>">
                                    <input  type="text" class="form-control" onkeydown="return false" name="transaction_date"  required/><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>

                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">Comment</label>
                            <div class="col-lg-8 form-group">
                                <textarea class="form-control" rows="2" name="narrative" id="narrative"></textarea>
                            </div>
                        </div>
                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                        <?php if (in_array('26', $savings_privilege)){ ?>
                        <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data"><i class="fa fa-check"></i><?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            } else {
                                echo "Update";
                            }
                            ?></button>
                   <?php } ?>
                        
                </div>
            </form>
        </div>
    </div>
</div>
