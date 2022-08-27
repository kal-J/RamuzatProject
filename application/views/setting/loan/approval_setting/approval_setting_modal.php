<div class="modal inmodal fade" id="add_approval_setting-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form method="post" class="formValidate" action="<?php echo base_url();?>loan_approval_setting/Create" id="formApproval_setting">

<div class="modal-header">
 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
 <h4 class="modal-title">
    <?php
    if (isset($modalTitle)) {
        echo $modalTitle;
    }else{
        echo "Add/Edit Approval Setting";
    }
 ?></h4>
 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
</div>

        <div class="modal-body"><!-- Start of the modal body -->
            <input type="hidden" name="id" id="id" >
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">Min.Amount<span class="text-danger">*</span></label>
                    <div class="col-lg-3 form-group">
                        <input min="0" required class="form-control" name="min_amount" id="min_amount" type="number">
                    </div> 
                    <label class="col-lg-2 col-form-label">Max.Amount<span class="text-danger">*</span></label>
                    <div class="col-lg-3 form-group">
                        <input min="0" required class="form-control" name="max_amount" id="max_amount" type="number">
                    </div> 
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">Min.Approvals<span class="text-danger">*</span></label>
                    <div class="col-lg-3 form-group">
                        <input min="1" required class="form-control" name="min_approvals" id="min_approvals" type="number">
                    </div>        
                </div><!--/row -->
                        
                </div><!-- End of the modal body -->
                    <div class="modal-footer"><!-- start of the modal footer -->
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-check"></i> 
                            <?php
                                if (isset($saveButton)) {
                                    echo $saveButton;
                                }else{
                                    echo "Save";
                                }
                             ?>
                        </button>
                        <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                            <i class="fa fa-times"></i> Cancel</button>
                    </div><!-- End of the modal footer -->
            </form>
        </div>
    </div>
</div>
