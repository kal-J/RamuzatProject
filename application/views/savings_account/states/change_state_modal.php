<div class="modal inmodal fade" id="change_states_modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="formValidate" action="<?php echo site_url("Savings_account/change_state"); ?>" id="formChange_state" method="post" name="formChange_state" data-toggle ='validator'>
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" data-bind="text:'You are about to '+action_msg()"></h4>
                    <small class="font-bold">Note: You are required to add a comment</small>
                </div>
                <div class="modal-body">
                <div  data-bind="with: selected_account()">
                    <input type="hidden" name="account_id" data-bind="value:id" id="account_id" />
                    </div>
                    <input type="hidden" name="state_id" data-bind="value:account_state()" id="state_id" >
                      <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Comment<span class="text-danger">*</span></label>
                            <textarea class="form-control col-lg-10 " name="comment" id="comment" ></textarea>
                            <span class="help-block with-errors text-danger" aria-hidden="true"></span>
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
