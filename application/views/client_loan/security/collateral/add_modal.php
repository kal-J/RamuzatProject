<!-- bootstrap modal -->
<div class="modal inmodal fade" id="add_collateral-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="<?php echo site_url('loan_collateral/create') ?>" id="formLoan_collateral" class="formValidate form-horizontal" method="post" enctype="multipart/form-data">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h3 class="modal-title">Collateral Information</h3>
                    <small class="font-bold">Please Make sure you enter all the required fields correctly</small>
                </div>
                <div class="modal-body">
                    <input type="hidden"  name="client_loan_id" value="<?php echo $loan_detail['id']; ?>">
                    <input type="hidden"  name="member_id" value="<?php echo isset($loan_detail['member_id']) ? $loan_detail['member_id'] : ''; ?>">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Collateral type<span class="text-danger">*</span></label>
                        <div class="col-lg-6 form-group">
                            <select id='collateral_type_id' class="form-control required" name="collateral_type_id" >
                                <option value="select_one">--Select one--</option>
                                <?php
                                foreach ($collateral_types as $collateral_type) {
                                    echo "<option value='" . $collateral_type['id'] . "'>" . $collateral_type['collateral_type_name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Description<span class="text-danger">*</span></label>
                        <div class="col-lg-6 form-group">
                            <textarea type="text" name="description" class="form-control m-b" required="required"></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Item value<span class="text-danger">*</span></label>
                        <div class="col-lg-6 form-group">
                            <input type="number" name="item_value" class="form-control m-b" required="required">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">File name<span class="text-danger"></span></label>
                        <div class="col-lg-6 form-group">
                        <input class="form-control" id="file_name" type="file" name="file_name">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"><?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            } else {
                                echo "Save";
                            }
                            ?></button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
