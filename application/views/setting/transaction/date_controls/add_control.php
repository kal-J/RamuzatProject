<div class="modal inmodal fade" id="add_transaction_date_control" role="dialog" aria-hidden="true">
    <div class="modal-dialog col-lg-8">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url(); ?>Transaction_date_control/create" id="formTransactionDateControl">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "Add Transaction Date Control";
                        }
                        ?></h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <div class="">
                        <input type="hidden" name="id">
                        <div class="form-group row">
                            <label class="col-lg-5 col-form-label">Control Name<span class="text-danger">*</span></label>
                            <div class="col-lg-7">
                                <input placeholder="" required class="form-control" name="control_name" type="text">
                            </div>
                        </div>
                         <div class="form-group row">
                            <label class="col-lg-5 col-form-label">Staff<span class="text-danger">*</span></label>
                            <div class="col-lg-7">
                               <select class="form-control" style="width: 100%" name="staff_id" data-bind="options: $root.staffs, optionsText: 'staff_name', optionsAfterRender: setOptionValue('id'), optionsCaption: '--Assign staff--'">
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-5 col-form-label">Allowed Dates<span class="text-danger">*</span></label>
                            <div class="col-lg-7 d-flex">
                                <div class="d-flex flex-column mr-1">
                                    <label class="col-form-label">Days in the past</label>
                                    <input name="past_days" type="number" class="form-control">
                                </div>
                                <div class="d-flex flex-column">
                                    <label class="col-form-label">Days in the future</label>
                                    <input name="future_days" type="number" class="form-control">
                                </div>
                            </div>
                            <!-- Control Range input -->
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-5 col-form-label">Description<span class="text-danger">*</span></label>
                            <div class="col-lg-7">
                                <textarea class="form-control" rows="2" required name="description" id="description"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <?php if ((in_array('1', $privileges)) || (in_array('3', $privileges))) { ?>
                        <button type="submit" class="btn btn-primary"><?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            } else {
                                echo "Save";
                            }
                            ?></button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>
