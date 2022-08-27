<div class="modal inmodal fade" id="add_approving_staff-modal" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post" class="formValidate" action="<?php echo base_url(); ?>approving_staff/Create" id="formApproving_staffs">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "Assign New Staff ";
                        }
                        ?></h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>

                <div class="modal-body"><!-- Start of the modal body -->
                    <input type="hidden" name="id">
                    <input type="hidden" name="approval_setting_id" id="approval_setting_id" data-bind="value: $root.approval_setting().id" >
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Staff Name<span class="text-danger">*</span></label>
                        <div class="col-lg-4 form-group">
                        <select class="form-control select2able" id="staff_id" name="staff_id" data-bind='options: staffs, optionsText: function(item){ return item.salutation+" "+item.firstname+" "+item.lastname+" "+item.othernames +"-"+item.staff_no;}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: staff' required data-msg-required="Staff is required" style="width: 100%">
                        </select>
                          </div> 
                        <label class="col-lg-2 col-form-label">Rank<span class="text-danger">*</span></label>
                        <div class="col-lg-4 form-group">
                            <center required>
                                <label> <input required value="1" name="rank" type="radio" > Chair</label>
                                <label> <input required value="0" name="rank" type="radio"> Member</label>
                            </center>
                       </div>  

                    </div><!--/row -->

                </div><!-- End of the modal body -->
                <div class="modal-footer"><!-- start of the modal footer -->
                    <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                        <i class="fa fa-check"></i> 
                        <?php
                        if (isset($saveButton)) {
                            echo $saveButton;
                        } else {
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
