<div class="modal inmodal fade" id="change_states_modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="formValidate" action="<?php echo site_url("Shares/change_state"); ?>" id="formChange_state" method="post" name="formChange_state" data-toggle ='validator'>
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" data-bind="text:'You are about to '+action_msg()+' this account'"></h4>
                    <small class="font-bold">Note: You are required to add a comment</small>
                </div>
                <div class="modal-body">
                    <div class="row">
                    <div  data-bind="with: share_details">
                        <input type="hidden" name="share_account_id" data-bind="value:id" id="share_account_id" />
                    </div>
                    <input type="hidden" name="state_id" data-bind="value:account_state()" id="state_id" >
                              <div class="col-lg-12">
							  <div class="row">
							   <label class="col-lg-6 col-form-label">Select Action Date<span class="text-danger">*</span></label>
								<div class="col-lg-6 form-group">
								  <div class="input-group date">
									<input class="form-control" required name="action_date" type="text" value="<?php echo date('d-m-Y');?>" ><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								  </div>
								</div> 
							</div> 
                                    <label class="col-form-label">comment<span class="text-danger">*</span></label>
                                <div class="form-group">
                                    <textarea class="form-control col-lg-12 " name="comment" id="comment" ></textarea>
                                    <span class="help-block with-errors text-danger" aria-hidden="true"></span>
                                </div>
                            </div>
                      </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-flat" >Save </button>
                </div>
            </form>
        </div>
    </div>
</div>
