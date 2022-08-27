<div class="modal inmodal fade" id="add_expense_category-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <form method="post" class="formValidate" action="<?php echo site_url("expense_category/create"); ?>" id="formExpense_category">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "Add Expense Category";
                        }
                        ?>
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id"/>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Expense category name<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="text" id="expense_category_name" name="expense_category_name" placeholder="" required class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="expense_category_code">Expense category code</label>
                        <div class="col-lg-8">
                            <input type="text" id="expense_category_name" name="expense_category_code" placeholder="" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Linked Account<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                                <select class="form-control m-b expense_category_modal" name="linked_account_id" data-bind='options: select2accounts(15), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                                </select>
                        </div>
                        <!--div class="col-lg-1">
                                <button type="button" class="input-group-addon btn btn-info" data-toggle="modal" data-target="#add_account-modal" title="Add another account"  data-bind="click: form_origin(3)"><i class="fa fa-plus"></i></button>
                        </div-->
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Description<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <textarea class="form-control" rows="2" required name="description" id="description"></textarea>
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