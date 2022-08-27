<?php $enable_or_disable_editting = isset($org['edit_account_nos']) && $org['edit_account_nos'] ? null : "readonly"; ?>
<!-- bootstrap modal -->
<style type="text/css">
.greenText {
    color: green;
    font-size: 12px;
}
</style>

<div class="modal inmodal fade" id="add_share_account-modal" tabindex="-1" privilege="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url();?>shares/create" id="formShares">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">X</span></button>
                    <h3 class="modal-title">
                        New Shares Account</h3>
                    <small class="font-bold">Note: Required fields are marked with <span
                            class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="state_id" value="7">

                     <div class="form-group row">
                            <label class="col-lg-4 col-form-label"><span class="text-danger">*</span>Account No.</label>
                            <div class="col-lg-8">
                                <input <?php echo $enable_or_disable_editting; ?> type="text" class="form-control"  id="share_account_no" name="share_account_no" data-bind="attr: {value:new_account_no}" required/>
                            </div>

                        </div>
                     <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Issuance/Category<span
                                class="text-danger">*</span></label>
                        <div class="col-lg-8 form-group">
                            <select class="form-control select2able" style="width: 100%" name="share_issuance_id"
                                id="share_issuance_id"
                                data-bind="options: share_issuance, optionsText: 'issuance_name', optionsAfterRender: setOptionValue('id'), optionsCaption: '--select--',value: issuance">
                            </select>
                        </div>
                    </div>

                    <?php if(isset($client_type) && $client_type == 2) { ?>
                        <input type="hidden" name="client_type" type="text" value="2">

                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Group Name<span class="text-danger">*</span></label>
                        <div class="col-lg-8 form-group">
                            
                            
                            <input type="text" class="form-control" value="<?php echo $group['group_name']; ?>" disabled>
                            <input type="hidden" name="member_id" value="<?php echo $group['id'] ?>">
                            
                        </div>
                    </div>

                    <?php } else { ?>

                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Client Name<span class="text-danger">*</span></label>
                        <div class="col-lg-8 form-group">
                            <?php if (!isset($modalTitle)) { ?>
                            <select class="form-control select2able" required style="width: 100%" name="member_id"
                                id="member_id"
                                data-bind="options: members, optionsText: function(data){ return data.client_name}, optionsAfterRender: setOptionValue('id'), optionsCaption: '--select--', value: member">
                            </select>
                            <input type="hidden" name="client_type" data-bind="value: $root.member() ? parseInt($root.member().client_type) : 1 ">


                            <?php }else{?>
                            <!--ko with: share_details-->
                            <input type="text" class="form-control"
                                data-bind="value: salutation+' '+firstname+' '+lastname+' '+othernames" disabled>
                            <input type="hidden" name="member_id" data-bind="value:member_id">
                            <input type="hidden" name="client_type" data-bind="value: client_type">
                            <!-- /ko -->
                            <?php } ?>
                        </div>
                    </div>


                    <?php } ?>

                    <!-- ko with: issuance  -->
                    <div class="form-group row" data-bind="visible: parseInt(link_to_savings)===1">
                        <label class="col-lg-4 col-form-label">Client Savings A/C<span
                                class="text-danger">*</span></label>
                        <div class="col-lg-8 form-group">
                            <?php if (!isset($modalTitle)) { ?>
                            
                            <select id='default_savings_account_id' class="form-control"
                                name="default_savings_account_id" required
                                data-bind='options: $root.member_accounts, optionsText: function(item) { return `${item.account_no} ${item.member_name}`;}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")'
                                required data-msg-required="Applicant is required">
                            </select>
                            <?php } else { ?>
                            <!--ko with: share_details-->
                            <input type="text" class="form-control" data-bind="value:account_no" disabled>
                            <input type="hidden" name="default_savings_account_id"
                                data-bind="value:default_savings_account_id">
                            <!-- /ko -->
                            <?php } ?>
                        </div>
                    </div>
                    <!-- /ko -->
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Opening Date</label>
                        <div class="col-lg-8">
                            <div class="input-group date">
                                <input type="text" class="form-control" name="date_opened" placeholder="Opening Date"
                                    required />
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Comment<span class="text-danger">*</span></label>
                        <div class="col-lg-8 form-group">
                            <textarea required class="form-control" rows="4" name="narrative" id="narrative"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-check"></i> Save </button>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel"
                        class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>