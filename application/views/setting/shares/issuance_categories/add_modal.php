<!-- bootstrap modal -->
<div class="modal inmodal fade" id="add_share_issuance_category-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url();?>share_issuance_category/create" id="formShare_issuance_category">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">X</span></button>
                <h3 class="modal-title">New Issuance</h3>
                 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="share_issuance_id" value="<?php echo $share_issuance['id'];  ?>" >
                <div class="form-group row">
                <label class="col-lg-4 col-form-label">Price Per Share</label>
                <div class="col-lg-8 form-group">
                <input placeholder="" required min ="1" class="form-control" name="price_per_share" type="number" id="price_per_share">
                </div>
               </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label"><center>Comment</center></label>
                        <div class="col-lg-8 form-group">
                            <textarea class="form-control" rows="3" name="description" id="description"></textarea>
                        </div>                                                  
                    </div>                        
                </div>
                <div class="modal-footer">
                <?php if((in_array('1', $share_issuance_privilege))||(in_array('3', $share_issuance_privilege))){ ?>
                     <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                        <i class="fa fa-check"></i> Save </button>
                    <?php } ?>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                    </div>
        </form>
        </div>
    </div>
</div>
