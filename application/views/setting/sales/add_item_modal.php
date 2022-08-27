<style type="text/css">
    section {
        overflow-y: auto;
    }

    @media (min-width: 992px) {

        .modal-lg,
        .modal-xl {
            max-width: 700px !important;
        }
    }

    @media (min-width: 1200px) {
        .modal-xl {
            max-width: 840px !important;
        }
    }
</style>
<div class="modal inmodal fade" id="new_item-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo site_url("sales/create_item"); ?>" id="formItemsForSale">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title">
                        Add Item For Sale
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="form-group col-lg-6">
                            <label class="col-form-label">Item Name</label>
                            <input type="hidden" name="id" id="id"/>
                            <input type="text" class="form-control" name="name" placeholder="Item Name" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="form-group col-lg-12">
                            <label class="col-form-label">Description</label>
                            <textarea class="form-control" name="narrative" placeholder="Description"></textarea>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <?php if ((in_array('1', $deposit_product_privilege)) || (in_array('3', $deposit_product_privilege))) { ?>
                            <button type="submit" class="btn btn-primary">
                                <?php if (isset($saveButton)) {
                                    echo $saveButton;
                                } else {
                                    echo "Save";
                                } ?></button>
                        <?php } ?>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>