<div class="modal inmodal fade" id="add_service_category-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <form method="post" class="formValidate" action="<?php echo site_url("service_category/create"); ?>" id="formService_category">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "Add Service Category";
                        }
                        ?>
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with </small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id"/>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Category Name<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="text" id="service_category_name" name="service_category_name" placeholder="" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="service_category_code">Category Code<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="text" id="service_category_code" name="service_category_code" placeholder="" required class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Linked Account<span class="text-danger">*</span></label>
                        <div class="col-lg-8">  
                            <select class="form-control m-b service_category_modal" name="linked_account_id" data-bind='options: select2accounts([8,9,10,12,13]), optionsText: formatAccount2, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")' data-msg-required="Account must be selected" style="width: 100%" required>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Description<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <textarea class="form-control" rows="2" required name="description" id="description">
                            </textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <?php if ((in_array('1', $accounts_privilege)) || (in_array('3', $accounts_privilege))) { ?>
                        <button type="submit" class="btn btn-primary"><?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            } else {
                                echo "Save";
                            }
                            ?></button>
                    <?php } ?>
                </div>
            </div>  
        </form>
    </div>
</div>