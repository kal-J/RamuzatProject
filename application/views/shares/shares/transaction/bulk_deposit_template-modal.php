<!-- bootstrap modal -->
<div class="modal inmodal fade" id="bulk_deposit_template-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form method="post" class="formValidate" enctype="multipart/form-data" action="<?php echo base_url();?>share_transaction/export_excel" id="formBulk_deposit_template">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">X</span></button>
                <h3 class="modal-title" style="font-size: 15px;">Shares Template</h3>
                 <small class="font-bold">Note: Required field is marked with<span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
                
                  
                    <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Category <span class="text-danger">*</span></label></label>
                        <div class="col-lg-8">
                              <select class="form-control select2able2" style="width: 100%" name="share_issuance_id" id="share_issuance_id" data-bind="options: share_issuance, optionsText: 'issuance_name', optionsAfterRender: setOptionValue('id'), optionsCaption: '--select--',value: issuance">
                    </select>
                        </div>
                    </div>
                     
                  </div>             
               
                <div class="modal-footer">
                <?php if(in_array('3', $share_privilege)){ ?>
                     <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                        <i class="fa fa-check"></i> Download Template 
                    </button>
                    <?php } ?>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                    </div>
        </form>
        </div>
    </div>
</div>
