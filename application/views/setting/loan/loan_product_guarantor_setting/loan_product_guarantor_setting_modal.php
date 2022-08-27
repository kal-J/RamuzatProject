<div class="modal inmodal fade" id="add_loan_product_guarantor_setting-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form method="post" class="formValidate" action="<?php echo base_url();?>loan_product_guarantor_setting/Create" id="formLoan_product_guarantor_setting">

<div class="modal-header">
 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
 <h4 class="modal-title">
    <?php
    if (isset($modalTitle1)) {
        echo $modalTitle;
    }else{
        echo "Add New Guarantor setting";
    }
 ?></h4>
 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
</div>

        <div class="modal-body"><!-- Start of the modal body -->
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="loan_product_id" id="loan_product_id" value="<?php echo $loan_product['id']?>" >
                    <div class="form-group row">
                            <label class="col-lg-6 col-form-label">Guarantor&nbsp;Setting<span class="text-danger">*</span></label>
                            <div class="col-lg-6 form-group">
                            <select required class="form-control" id="guarantor_setting_id" name="guarantor_setting_id">
                                <option value="" selected>--Select--</option>
                                <?php
                                foreach ($guarantor_setting as $value) {
                                    echo "<option value='" . $value['id'] . "'>" . $value['setting'] . "</option>";
                                }
                                ?>
                            </select>
                            <div>
                          </div>                            
                          </div> 

                        </div><!--/row -->
                        
                </div><!-- End of the modal body -->
                    <div class="modal-footer"><!-- start of the modal footer -->
                    <?php if((in_array('1', $guarantor_privilege))||(in_array('3', $guarantor_privilege))){ ?>
                        <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                            <i class="fa fa-check"></i> 
                            <?php
                                if (isset($saveButton)) {
                                    echo $saveButton;
                                }else{
                                    echo "Save";
                                }
                             ?>
                        </button>
                        <?php } ?>
                        <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                            <i class="fa fa-times"></i> Cancel</button>
                    </div><!-- End of the modal footer -->
            </form>
        </div>
    </div>
</div>
