<!-- bootstrap modal -->
<div class="modal inmodal fade" id="add_client_subscription-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <?php echo form_open_multipart("client_subscription/create", array('id' => 'formClient_subscription1', 'class' => 'formValidate', 'name' => 'formClient_subscription1', 'method' => 'post', 'role' => 'form')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title"><?php echo $this->lang->line('cont_subscription');  ?></h3>
                <small class="font-bold">Please Make sure you enter all the required fields correctly</small>
            </div>
            <div class="modal-body">
                <input type="text" hidden name="id" id="id">
                <input type="text" hidden name="client_id" id="client_id"  value="<?php echo $user['id']; ?>">
                <div class="row form-group">
                    <label class="col-lg-2 col-form-label">Expected Date</label>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <input class="form-control m-b"  data-bind="attr: {value:next_payment_date}" disabled>
                            <input type="text" name="subscription_date" id="subscription_date" class="form-control m-b" required="required" data-bind="attr: {value:next_payment_date}" readonly>
                        </div>
                    </div>
                    <label class="col-lg-2 col-form-label">Paid ?<span class="text-danger">*</span></label>
                    <div class="col-lg-4 form-group">
                           <br>
                          <label><input  value="0" name="sub_fee_paid" type="radio"  data-bind="checked: sub_fee_paid" required> No</label>
                          <label> <input value="1" name="sub_fee_paid" type="radio"  data-bind="checked: sub_fee_paid"  required> Yes</label>
                     </div>
                </div>
                <div class="row form-group">
                    <label class="col-lg-2 col-form-label">Payment Date</label> 
                    <div class="col-lg-4">
                        <div class="input-group date" data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>">
                            <input type="text" onkeydown="return false" name="transaction_date" id="transaction_date" value="<?php echo date("d-m-Y"); ?>" class="form-control m-b" autocomplete="off" required="required"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                     <label class="col-lg-2 col-form-label">Amount</label>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <input type="text" name="amount" id="amount" class="form-control m-b" data-bind="attr:{value:amount_payable}" required="required" readonly>
                        </div>
                    </div>
                </div>
                <!-- ko if: parseInt(sub_fee_paid())===1 -->
                <div class="row form-group">
                    <label class="col-lg-2 col-form-label">Payment Method</label>
                    <div class="col-lg-4">
                        <select  data-bind='options: $root.payment_modes, optionsText: "payment_mode", optionsCaption: "-- select --" ,optionsAfterRender: setOptionValue("id"),value: payment_mode, attr:{name:"payment_id"}' class="form-control"  style="width: 170px;"> </select>
                    </div>
                <!-- ko with: payment_mode --> 
                    <label class="col-lg-2 col-form-label" data-bind="visible: id ==5">Savings Account</label>
                    <div class="col-lg-4" data-bind="visible: id ==5">
                    <select class="form-control"  data-bind='
                        options: $root.available_saving_accounts, 
                        optionsText: function(data_item){return data_item.account_no + " | " + data_item.member_name}, 
                        optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"),attr:{name:"account_no_id"},
                        value: $root.selected_ac' style="width: 100%">
                    </select>
                </div>
                    <label class="col-lg-2 col-form-label" data-bind="visible: id !=5">Transaction Channel</label>
                    <div class="col-lg-4" data-bind="visible: id !=5">
                        <select  class="form-control" data-bind='options: $root.transaction_channels, optionsText:function(data_item){return data_item.channel_name}, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), attr:{name:"transaction_channel_id"}, value:$root.trans_channel' data-msg-required="Transaction channel must be selected" style="width: 100%"  >
                        </select>
                    </div>
                <!--/ko-->
                </div>
                <!--/ko-->
                <div class="row form-group">
                    <label class="col-lg-2 col-form-label">Narrative</label>
                    <div class="col-lg-10">
                        <div class="input-group">
                            <textarea name="narrative" id="narrative" class="form-control m-b" required="required" placeholder=" Narrative"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php if ((in_array('1', $subscription_privilege)) || (in_array('3', $subscription_privilege))) { ?>
                        <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                            <i class="fa fa-check"></i> Save</button>
                    <?php } ?>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm cancel">
                        <i class="fa fa-times"></i> Cancel</button>
                </div>
              </div>
            </form>
        </div>
    </div>
</div>
<!-- bootstrap modal ends -->
