<div class="modal inmodal fade" id="attach_member_fees-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="post" class="formValidate" action="<?php echo site_url();?>applied_member_fees/create2" id="formApplied_member_fees1">
<div class="modal-header">
 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
 <h4 class="modal-title">
   Membership fee(s)</h4>
 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
</div>
                <div class="modal-body">
                

                 <div  class="row form-group">
                    <label class="col-lg-2 col-form-label">Member/Client</label>
                     <div class="col-lg-5">
                      <select class="form-control" id="membership_selects" style="width: 100%" name="member_id" data-bind="options: members, optionsText:function(data){ return ((data.client_no==0)?'':data.client_no+' - ') +data.client_name; }, optionsAfterRender: setOptionValue('id'), optionsCaption: '--select--', value: member">
                      </select>
                       
                </div>
                </div>
                <div class="row form-group">
                    <label class="col-lg-2 col-form-label">Transaction Date</label> 
                    <div class="col-lg-4">
                        <div class="input-group date" data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>" >
                            <input type="text" onkeydown="return false" name="transaction_date"  class="form-control m-b" autocomplete="off" required="required"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                <label class="col-lg-2 col-form-label">Paid ?<span class="text-danger">*</span></label>
                <div class="col-lg-4 form-group">
                       <br>
                      <label><input  value="0" name="fee_paid" type="radio"  data-bind="checked: fee_paid" required> No</label>
                      <label> <input value="1" name="fee_paid" type="radio"  data-bind="checked: fee_paid"  required> Yes</label>
                 </div>
                </div>
             <!-- ko if: parseInt(fee_paid())===1 -->
                <div class="row form-group">
                    <label class="col-lg-2 col-form-label">Payment Method</label>
                    <div class="col-lg-4">
                        <select  data-bind='options: payment_modes, optionsText: "payment_mode", optionsCaption: "-- select --" ,optionsAfterRender: setOptionValue("id"),value: payment_mode, attr:{name:"payment_id"}' class="form-control"  style="width: 170px;"> </select>
                    </div>
                    <!-- ko with: payment_mode --> 
                    <label class="col-lg-2 col-form-label"  data-bind="visible: id ==5">Savings Account</label>
                    <div class="col-lg-4"  data-bind="visible: id ==5">
                    <select class="form-control"  data-bind='
                        options: $root.available_saving_accounts, 
                        optionsText: function(data_item){return data_item.account_no + " | " + data_item.member_name}, 
                        optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"),attr:{name:"account_no_id"},
                        value: $root.selected_ac' style="width: 100%">
                    </select>
                </div>
                 <label class="col-lg-2 col-form-label"  data-bind="visible: id <5">Transaction Channel</label>
                    <div class="col-lg-4"  data-bind="visible: id <5">
                        <select  class="form-control" data-bind='options: $root.transaction_channels, optionsText:function(data_item){return data_item.channel_name}, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), attr:{name:"transaction_channel_id"}, value:$root.trans_channel' data-msg-required="Transaction channel must be selected" style="width: 100%"  >
                        </select>
                    </div>
                <!--/ko-->
                </div>
                 <!--/ko-->
                <!-- ko if: $root.attach_member_fees().length >0 -->
                <div class="row col-lg-12">
                    <div class="table-responsive">
                        <table  class="table table-striped table-condensed table-hover m-t-md">
                            <thead>
                                <tr>
                                    <th>Fee</th>
                                    <th>Amount</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody data-bind='foreach: attach_member_fee'>
                                <tr>
                                <td> <select data-bind='options: $root.attach_member_fees, optionsText: function(item){return item.feename}, value: selected_fee1, optionsCaption: "-- select --"' class="form-control"  style="width: 250px"> </select>
                                    </td>
                                    <td data-bind="with: selected_fee1">
                                        <label data-bind="text: ($root.attach_member_fees().length > 0)?curr_format(amount):''"></label>
                                        <input type="hidden" data-bind='attr:{name:"memberFees1["+$index()+"][amount]"}, value: amount'/>
                                        <input type="hidden" data-bind='attr:{name:"memberFees1["+$index()+"][member_fee_id]"}, value: id'/>
                                    </td>
                                    <td>
                                        <span title="Remove item" class="btn text-danger" data-bind='click: $root.removeMemberFee1'><i class="fa fa-minus"></i></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    <button data-bind='click: $root.addMemberFee1, enable:$root.attach_member_fees().length > 0' class="btn-white btn-sm pull-right"><i class="fa fa-plus"></i> Apply another fee</button>
                    <br>
                    </div>
                </div>
                <!--/ko-->
                <div class="row form-group">
                    <label class="col-lg-4 col-form-label">Narrative</label>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <textarea name="narrative" id="narrative" class="form-control m-b" required="required" placeholder="Transaction Narrative"></textarea>
                        </div>
                    </div>
                </div>

                </div>
                 <!-- ko if: $root.attach_member_fees().length >0 -->
                <div class="modal-footer">
                    <?php if((in_array('1', $member_privilege))||(in_array('3', $member_privilege))){ ?>
                        <button class="btn btn-primary btn-flat" data-bind="enable:$root.attach_member_fees().length > 0"  type="submit">Save Transaction</button>

                    <?php } ?>
                </div>
                <!--/ko-->

</form>
</div>
</div>
</div>
