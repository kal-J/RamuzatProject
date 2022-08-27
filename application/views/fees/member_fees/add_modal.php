<div class="modal inmodal fade" id="add_member_fees-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="post" class="formValidate" action="<?php echo site_url();?>applied_member_fees/create" id="formApplied_member_fees">
<div class="modal-header">
 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
 <h4 class="modal-title">
    Pay fees
    </h4>
</div>

<input class="form-control" name="id" id="id" type="hidden">

<input class="form-control" name="member_fee_id" id="member_fee_id"  type="hidden">
<input class="form-control" name="member_id" id="member_id"  type="hidden">
<input class="form-control" name="transaction_no" id="transaction_no" value="1" type="hidden">
                <div class="modal-body">
                 <div class="row form-group" >
                    <label class="col-lg-2 col-form-label">Fee</label>
                    <div class="col-lg-4">
                    <input class="form-control" name="feename" id="feename" disabled type="text">
                    </div>
                     <label class="col-lg-2 col-form-label">Amount</label>
                    <div class="col-lg-4">
                    <input class="form-control" name="amount" id="amount" disabled type="text">
                    <input class="form-control" name="amount" id="amount"  type="hidden">
                        
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-lg-2 col-form-label">Payment Date</label> 
                    <div class="col-lg-4">
                        <div class="input-group date" data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>" >
                            <input type="text" onkeydown="return false" name="transaction_date" class="form-control m-b" autocomplete="off" required="required"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                    <label class="col-lg-2 col-form-label">Payment Method</label>
                    <div class="col-lg-4">
                        <select required="required" data-bind='options: $root.payment_modes, optionsText: "payment_mode", optionsCaption: "-- select --" ,optionsAfterRender: setOptionValue("id"),value: payment_mode, attr:{name:"payment_id"}' class="form-control"  style="width: 170px;"> </select>
                    </div>
                </div>
                <!-- ko with: payment_mode --> 
                <div class="row form-group" >
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

                    <label class="col-lg-2 col-form-label">Narrative</label>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <textarea name="narrative" id="narrative" class="form-control m-b" required="required" placeholder="Subscription Narrative"></textarea>
                        </div>
                    </div>
                </div>
                <!--/ko-->
               
                </div>
                <div class="modal-footer">
                    <?php if((in_array('1', $member_privilege))||(in_array('3', $member_privilege))){ ?>
                    <button class="btn btn-primary btn-flat" data-bind="enable:$root.available_member_fees().length > 0"  type="submit">Apply Fees</button>
                    <?php } ?>
                </div>
</form>
</div>
</div>
</div>
