l<div class="modal inmodal fade" id="add_share_call-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
<div class="modal-dialog modal-md">
<div class="modal-content">

<form method="post" class="formValidate" action="<?php echo base_url();?>share_call/Create" id="formShare_call">

<div class="modal-header">
 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
 <h4 class="modal-title">Add Share Call</h4>
 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
</div>

        <div class="modal-body"><!-- Start of the modal body -->
            <input type="hidden" name="id" id="id" >
            <input type="hidden" name="issuance_id" value="<?php echo $share_issuance['id'] ?>" >
                        <!--input type="hidden" name="tbl" id="tbl" value="tblBranch" -->
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">Call&nbsp;Name<span class="text-danger">*</span></label>
                            <div class="col-lg-8 form-group">
                                <input placeholder="Call Name" required class="form-control" name="call_name" id="call_name" type="text">
                            </div> 
                        </div><!--/row -->  
                    
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">Percentage<span class="text-danger">*</span></label>
                            <div class="col-lg-8 form-group">
                                <input placeholder="" required class="form-control" name="percentage" id="percentage" min="0" max="100" type="number">
                            </div>   
                        </div><!--/row -->
                        
                        
                </div><!-- End of the modal body -->
                    <div class="modal-footer"><!-- start of the modal footer -->
                    <?php if((in_array('1', $share_issuance_privilege))||(in_array('3', $share_issuance_privilege))){ ?>
                        <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                            <i class="fa fa-check"></i> Save
                        </button>
                    <?php } ?>
                        <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                            <i class="fa fa-times"></i> Cancel</button>
                    </div><!-- End of the modal footer -->
            </form>
        </div>
    </div>
</div>
