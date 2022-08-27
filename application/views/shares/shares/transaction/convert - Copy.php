<style type="text/css">
    .blueText{color: blue;font-size: 10px;}
</style>
<div class="modal inmodal fade" id="convert_shares" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data" class="formValidate" action="<?php echo base_url(); ?>Share_transaction/Refund" id="formConvert_shares">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <span data-bind="text:'Sell Shares Back to Organisation'"></span>
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <div class="">
                        <!--ko with: call_payment-->
                        <input type="hidden" name="share_acc_id" data-bind="value:id">
                        <input type="hidden" name="share_issuance_id" data-bind="value:share_issuance_id">
                        <input type="hidden" name="transaction_type_id" value="9">
                        <input type="hidden" name="transaction_charge_type_id" id="transaction_charge_type_id" value="11">
                         
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Account Name</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control"  data-bind="value: group_name ? group_name : (firstname+' '+lastname) " disabled>
                            </div>
                            <label class="col-lg-2 col-form-label">Share Account No</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control"  data-bind="value:share_account_no" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Amount</label>
                            <div class="col-lg-4">
                                <input class="form-control" data-bind="textInput: $parent.deposit_amount,valueUpdate:'afterkeydown',
                            attr: {'data-rule-max':(total_amount),'data-msg-min':'Amount must be less or equal to '+curr_format(total_amount)}" name="amount" type="text" required>
                                <span class="blueText">Amount must be less than  <b data-bind="text:curr_format(total_amount)"></b> (Maximum amount).</span>
                            </div>
                            <label class="col-lg-2 col-form-label">Payment Mode <span class="text-danger">*</span></label></label>
                            <div class="col-lg-4">
                                <select  data-bind='options: $root.payment_modes, optionsText: "payment_mode", optionsCaption: "-- select --" ,optionsAfterRender: setOptionValue("id"),value: $root.payment_mode, attr:{name:"payment_id"}' class="form-control"  required > </select>
                            </div>
                        </div>
                        <!--/ko-->
                        <div class="form-group row">
                            <!-- ko with: payment_mode -->
                            <label class="col-lg-2 col-form-label" data-bind="visible: id ==5">Savings A/C<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group" data-bind="visible: id ==5">
                                <select id='default_savings_account_id' class="form-control" name="account_no_id" required data-bind='options: $root.member_accounts, optionsText: "account_no", optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' required data-msg-required="Savings Account is required">
                                </select>
                            </div>
                            <label class="col-lg-2 col-form-label" data-bind="visible: id !=5">Transaction channel</label>
                            <div class="col-lg-4" data-bind="visible: id !=5">
                                <select  class="form-control" id="transaction_channel_id" name="transaction_channel_id" data-bind='options:$root.transaction_channel, optionsText: "channel_name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:$root.tchannels' data-msg-required="Transaction channel  must be selected" style="width: 100%" required >
                                </select>
                            </div>

                            <label class="col-lg-2 col-form-label">Date</label>
                            <div class="col-lg-4">
                                <div class="input-group date" >
                                    <input autocomplete="off" placeholder="DD-MM-YYYY" data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y')); ?>" type="text" class="form-control" onkeydown="return false" name="transaction_date" data-bind="datepicker: $root.transaction_date_wi" required/>
                                    <span  data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y')); ?>" data-bind="datepicker: $root.transaction_date_wi" class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                            <!--/ko-->

                        </div>
                        <div class="form-group row">

                            <label class="col-lg-2 col-form-label">Narrative</label>
                            <div class="col-lg-10">
                                <textarea placeholder="" required class="form-control" id="narrative" name="narrative"></textarea>
                            </div>
                        </div>
                        <!--ko with: call_payment-->
                        <div class="form-group row">
                            <div class="col-lg-7">
                                <fieldset >
                                    <legend style="min-width:250px;">Transaction Charges</legend>
                                    <table class='table table-hover'>
                                        <thead>
                                        <tr>
                                            <th class="border-right">#</th>
                                            <th>Name</th>
                                            <th>Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody data-bind="foreach: $parent.deposit_fees">
                                        <tr>
                                            <td data-bind="text:$index()+1" class="border-right"></td>
                                            <td >
                                                <span class="input-xs" required  data-bind="text:(feename)?feename:'None'"></span>
                                                <input  class="form-control input-xs" required data-bind="value:(id)?id:'none', attr: {name:'charges['+$index()+'][id]'}" type="hidden">
                                            </td>
                                            <td >
                                                        <span class="input-xs" required data-bind="text:curr_format(
                                                        (parseInt(amountcalculatedas_id)==2)?parseFloat(amount):
                                                        ( (parseInt(amountcalculatedas_id) == 1)?(parseFloat(amount)*1*parseFloat($root.deposit_amount()))/100:amount*1) )" ></span>
                                                <input class="input-xs" required class="form-control"  data-bind="value:
                                                        (parseInt(amountcalculatedas_id)==2)?parseFloat(amount):
                                                        ( (parseInt(amountcalculatedas_id) == 1)?(parseFloat(amount)*1*parseFloat($root.deposit_amount()))/100:amount*1), attr: {name:'charges['+$index()+'][charge_amount]'}" type="hidden">
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td><span class="pull-right ">Total charges :</span></td>
                                            <input type="hidden" name="total_charges" data-bind="value: ($parent.totaldepositCharges())?$parent.totaldepositCharges():0" >
                                            <th data-bind=" text: ($parent.totaldepositCharges())?$parent.totaldepositCharges():'0'"></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </fieldset>
                            </div>
                            <div class="col-lg-5">
                                <fieldset class="">
                                    <legend style=" text-align: right;"> Total Amount ( Charges Included)</legend>
                                    <input class="form-control" id="amount" data-bind="value:$parent.deposit_amount()" name="amount" type="text" required hidden>
                                    <h2 class="pull-right" data-bind="text:curr_format(parseFloat($parent.deposit_amount()))")></h2>
                                </fieldset>
                            </div>
                        </div>
                        <!--/ko-->
                        <br>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <?php
                        if (isset($saveButton)) {
                            echo $saveButton;
                        } else {
                            echo "Save Transaction";
                        }
                        ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
