<style type="text/css">
.blueText {
    color: blue;
    font-size: 10px;
}
</style>
<div class="modal inmodal fade" id="share_transfer" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" enctype="multipart/form-data"
                action="<?php echo base_url(); ?>Share_transaction/Create3" id="formTransfer_share">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                            class="sr-only">Close</span></button>
                    <h4 class="modal-title">

                        <span>Sell/Transfer Shares</span>

                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span
                            class="text-danger">*</span></small>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <fieldset class="col-lg-12">
                            <legend>Account info</legend>
                            <div class="col-lg-12 row no-gutter">
                                <input type="hidden" name="payment_id" id="payment_id" value="1">
                                <!-- ko with: account_trans -->
                                <input type="hidden" name="share_acc_id" data-bind="value:id">
                                <input type="hidden" name="transaction_type_id" value="9">
                                <input type="hidden" name="share_issuance_id" data-bind="value:share_issuance_id">
                                <input type="hidden" name="transaction_charge_type_id" id="transaction_charge_type_id"
                                    value="11">
                                 

                                <!-- /ko -->

                                <div class="form-group col-lg-5">
                                    <h2>
                                        <center>From</center>
                                    </h2>
                                    <hr>
                                    <label class="col-lg-12"><span class="text-danger">*</span>Account No.</label>
                                    <div class="col-lg-12">
                                        <!-- ko with: account_trans -->
                                        <input disabled class="form-control" id="share_account_no" type="text"
                                            data-bind="value: (share_account_no)?share_account_no:'None'">
                                        <input class="form-control" id="share_account_no" type="hidden" name="share_account_no" data-bind="value: (share_account_no)?share_account_no:'None'">
                                        <!--/ko-->
                                    </div>
                                    <label class="col-lg-12 ">Account Holder<span class="text-danger">*</span></label>
                                    <div class="col-lg-12">
                                        <!-- ko with: account_trans -->
                                        <input type='text' class="form-control"
                                            data-bind="value: group_name ? group_name : ( 
                                                member_name ? member_name: (
                                                    firstname ? (firstname+' '+lastname) : 'None'
                                                )
                                            ) " disabled />
                                            <input type="hidden" name="member_id" data-bind="value:member_id">
                                        <!--/ko-->
                                        <!-- ko ifnot: account_trans -->
                                        <input type='text' class="form-control" value="Select Account No. first..."
                                            disabled />
                                        <!--/ko-->

                                    </div>
                                </div>
                                <div class="col-lg-7 border-left">
                                    <h2>
                                        <center>To</center>
                                    </h2>
                                    <hr>
                                    <div class="form-group row">
                                        <label class="col-lg-12 col-form-label">Account Number<span
                                                class="text-danger">*</span></label>
                                        <div class="col-lg-12 form-group">
                                           
                                             <select class="form-control select2able" required style="width: 100%" name="shares_account_id"
                                                id="transfer_to_select"
                                                data-bind="options: share_accounts, optionsText: function(data){ 
                                                    let name = data.group_name ? data.group_name : (data.firstname +' '+data.lastname);
                                                    return data.share_account_no +' - '+ name}, optionsAfterRender: setOptionValue('id'), optionsCaption: '--select--', value: share_account2">
                                            </select>

                                        </div>
                                        <div data-bind="with:share_account2">
                                            <input type="hidden" name="transfer_share_issuance_id"
                                                data-bind="value:share_issuance_id">
                                        </div>
                                    </div>
                                    <center>
                                        <span class="text-muted">Amount available for Transfer</span>
                                        <!-- ko with: account_trans -->
                                        <h2 data-bind="text:curr_format(parseFloat(total_amount))"></h2>
                                        <!--/ko-->

                                    </center>

                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <!--ko with: account_trans-->
                    <div class="form-group">
                        <div class="row d-flex">
                            <label class="col-lg-2 col-form-label"> Transfer Amount</label>
                            <div class="col-lg-4">
                                <input class="form-control" id="amount" name="amount" data-bind="textInput:$parent.transfer_amount,valueUpdate:'afterkeydown',attr: {'data-rule-min': (($parent.totaltransferCharges())?$parent.totaltransferCharges(): 0),'data-rule-max':total_amount}
                                    " type="text" required>
                                <span class="blueText"><b data-bind="text: curr_format( ($parent.totaltransferCharges())?$parent.totaltransferCharges(): 0 )"></b>(min )&nbsp;&nbsp;&nbsp;<b
                                        data-bind="text:total_amount"></b> (max ).</span>
                            </div>

                            <label class="col-lg-2 col-form-label">Payment Mode <span
                                    class="text-danger">*</span></label></label>
                            <div class="col-lg-4">
                                <select
                                    data-bind='options: $root.payment_modes, optionsText: "payment_mode", optionsCaption: "-- select --" ,optionsAfterRender: setOptionValue("id"),value: $root.payment_mode, attr:{name:"payment_id"}'
                                    class="form-control" required> </select>
                            </div>

                        </div>
                    </div>
                    <!--/ko-->


                    <div class="form-group row">
                        <!-- ko with: payment_mode -->
                        <label class="col-lg-2 col-form-label" data-bind="visible: id ==5">Savings A/C<span
                                class="text-danger">*</span></label>
                        <div class="col-lg-4 form-group" data-bind="visible: id ==5">
                           <select id='default_savings_account_id' class="form-control" name="account_no_id" required
                                data-bind='options: $root.member_accounts, optionsText:function(item) { return `${item.account_no} ${item.member_name}`;}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")'
                                required data-msg-required="Savings Account is required">
                            </select>
                           <!-- <select id='default_savings_account_id' class="form-control" name="account_no_id" required
                                data-bind='options: $root.member_accounts, optionsText: function(item) { return `${item.account_no} ${item.member_name}`;}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")'
                                required data-msg-required="Savings Account is required">
                            </select>-->
                        </div>
                        <label class="col-lg-2 col-form-label" data-bind="visible: id !=5">Transaction channel</label>
                        <div class="col-lg-4" data-bind="visible: id !=5">
                            <select class="form-control" id="transaction_channel_id" name="transaction_channel_id"
                                data-bind='options:$root.transaction_channel, optionsText: "channel_name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:$root.tchannels'
                                data-msg-required="Transaction channel  must be selected" style="width: 100%" required>
                            </select>
                        </div>

                        <label class="col-lg-2 col-form-label">Date</label>
                        <div class="col-lg-4">
                            <div class="input-group date">
                                <input autocomplete="off" placeholder="DD-MM-YYYY"
                                    data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>"
                                    data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y')); ?>"
                                    type="text" class="form-control" onkeydown="return false" name="transaction_date"
                                    data-bind="datepicker: $root.transaction_date_wi" required />
                                <span
                                    data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>"
                                    data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y')); ?>"
                                    data-bind="datepicker: $root.transaction_date_wi" class="input-group-addon"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <!--/ko-->

                    </div>
                    <div class="form-group row">

                        <label class="col-lg-2 col-form-label">Narrative</label>
                        <div class="col-lg-10">
                            <textarea placeholder="" required class="form-control" id="narrative"
                                name="narrative"></textarea>
                        </div>
                    </div>




                    <!--ko with: account_trans-->
                    <div class="form-group row">
                        <div class="col-lg-7">
                            <fieldset>
                                <legend style="min-width:250px;">Transaction Charges</legend>
                                <table class='table table-hover'>
                                    <thead>
                                        <tr>
                                            <th class="border-right">#</th>
                                            <th>Name</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody data-bind="foreach: $parent.transfer_fees">
                                        <tr>
                                            <td data-bind="text:$index()+1" class="border-right"></td>
                                            <td>
                                                <span class="input-xs" required
                                                    data-bind="text:(feename)?feename:'None'"></span>
                                                <input class="form-control input-xs" required
                                                    data-bind="value:(id)?id:'none', attr: {name:'charges['+$index()+'][id]'}"
                                                    type="hidden">
                                            </td>
                                            <td>
                                                <span class="input-xs" required
                                                    data-bind="text:curr_format(
                                                        (parseInt(amountcalculatedas_id)==2)?parseFloat(amount):
                                                        ( (parseInt(amountcalculatedas_id) == 1)?(parseFloat(amount)*1*parseFloat($root.transfer_amount()))/100:amount*1) )"></span>
                                                <input class="input-xs" required class="form-control"
                                                    data-bind="value:
                                                        (parseInt(amountcalculatedas_id)==2)?parseFloat(amount):
                                                        ( (parseInt(amountcalculatedas_id) == 1)?(parseFloat(amount)*1*parseFloat($root.transfer_amount()))/100:amount*1), attr: {name:'charges['+$index()+'][charge_amount]'}"
                                                    type="hidden">
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td><span class="pull-right ">Total charges :</span></td>
                                            <input type="hidden" name="total_charges"
                                                data-bind="value: ($parent.totaltransferCharges())?$parent.totaltransferCharges():0">
                                            <th
                                                data-bind=" text: ($parent.totaltransferCharges())?$parent.totaltransferCharges():'0' ">
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </fieldset>
                        </div>
                        <div class="col-lg-5">
                            <fieldset class="">
                                <legend style=" text-align: right;"> Total Amount (Charges Included)</legend>
                                <input class="form-control" id="amount" data-bind="value:$parent.transfer_amount()"
                                    name="amount" type="text" required hidden>
                                <h2 class="pull-right" data-bind="text:curr_format($parent.transfer_amount())" )></h2>
                            </fieldset>
                        </div>
                    </div>

                    <!--/ko-->

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <!--ko if:(parseInt($root.account_balance()>0)-->
                    <button type="submit" class="btn btn-primary">
                        <?php
                        if (isset($saveButton)) {
                            echo $saveButton;
                        } else {
                            echo "Save";
                        }
                        ?>
                    </button>
                    <!--/ko-->
                </div>
            </form>
        </div>
    </div>
</div>