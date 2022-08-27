<style type="text/css">
    .blueText {
        color: blue;
        font-size: 10px;
    }
    .box {
        display: none;
    }
</style>
<div class="modal inmodal fade" id="add_transaction" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data" class="formValidate" action="<?php echo base_url(); ?>Transaction/Create" id="formDeposit">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>

                    <h4 class="modal-title">
                        <?php if (isset($access_side)) { ?>
                            <span>New Deposit</span>

                        <?php } else if (!isset($selected_account)) { ?>
                            <!-- ko with: selected_account -->
                            <span data-bind="text:(client_type==1)?'Quick Individual Deposit':'Quick Group Deposit'"></span>
                            <input type="hidden" data-bind="value:(client_type==1)?1:2" name="client_type">
                            <!-- /ko -->
                        <?php } elseif (isset($access_side)) { ?>
                            <span>New Individual Deposit</span>

                        <?php } else { ?>
                            <span data-bind="text:($root.selected_account().client_type==1)?' New Individual Deposit':' New Group Deposit'"></span>
                            <input type="hidden" data-bind="value:($root.selected_account().client_type==1)?1:2" name="client_type">
                        <?php }
                        ?>
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <div class="">
                        <input type="hidden" name="id">
                        <input type="hidden" name="transaction_type_id" id="transaction_type_id" value="2">
                        <input type="hidden" name="charge_id" id="charge_id" value="7">
                        <!--deposit-->

                        <?php if (isset($access_side)) { ?>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label"><span class="text-danger">*</span>Account No.</label>
                                <select class="col-lg-4 form-control" id="member_account" data-bind='options: saving_accounts, optionsText: function(item){ return item.account_no+"-"+item.member_name;}, optionsCaption: "---select---", value: selected_account' data-msg-required="Account number is required" style="width: 30%">
                                </select>

                            <?php  } elseif (!isset($selected_account)) { ?>
                                <div class="form-group row">
                                    <!-- ko with: selected_account -->
                                    <input name="cash_bal" data-bind="value: (cash_bal)?cash_bal :''" type="hidden" />
                                    <input name="account_no_id" data-bind="value: (id)?id :''" type="hidden" />
                                    <input name="opening_balance" data-bind="value: (opening_balance)?opening_balance :0" type="hidden" />
                                    <input name="state_id" data-bind="value: (state_id)?state_id :''" type="hidden" />
                                    <!-- /ko -->
                                    <label class="col-lg-2 col-form-label"><span class="text-danger">*</span>Account No.</label>

                                <?php } else { ?>
                                    <div class="form-group row" data-bind="with: selected_account">
                                        <input name="cash_bal" data-bind="value: (cash_bal)?cash_bal :''" type="hidden" />
                                        <input name="account_no_id" data-bind="value: (id)?id :''" type="hidden" />
                                        <input name="opening_balance" data-bind="value: (opening_balance)?opening_balance :0" type="hidden" />
                                        <input name="state_id" data-bind="value: (state_id)?state_id :''" type="hidden" />
                                        <input name="account_details" value="1" type="hidden" />
                                        <label class="col-lg-2 col-form-label"><span class="text-danger">*</span>Account No.</label>
                                    <?php } ?>

                                    <?php if (!isset($selected_account)) { ?>
                                        <!-- ko with: selected_account -->
                                    <?php } ?>
                                    <input type="hidden" name="account_no" data-bind="attr:{value: (account_no)?account_no:''}">
                                    <input name="mandatory_saving" data-bind="attr:{value: (mandatory_saving)?mandatory_saving:''}" type="hidden" />
                                    <?php if (!isset($access_side)) { ?>
                                        <div class="col-lg-4">
                                            <input disabled class="form-control" id="account_no" type="text" data-bind="value: (account_no)?account_no:''">
                                        </div>
                                    <?php } elseif (isset($access_side)) { ?>
                                        <input name="cash_bal" data-bind="value: (cash_bal)?cash_bal :''" type="hidden" />
                                        <input name="account_no_id" data-bind="value: (id)?id :''" type="hidden" />
                                        <input name="opening_balance" data-bind="value: (opening_balance)?opening_balance :0" type="hidden" />
                                        <input name="state_id" data-bind="value: (state_id)?state_id :''" type="hidden" />
                                        <input name="account_details" value="1" type="hidden" />

                                    <?php }
                                    if (!isset($selected_account)) { ?>
                                        <!-- /ko-->
                                    <?php } ?>

                                    <label class="col-lg-2 col-form-label">Account Holder<span class="text-danger">*</span></label>
                                    <div class="col-lg-4">
                                        <?php if (!isset($selected_account)) { ?>
                                            <!-- ko with: selected_account -->
                                            <input type='text' class="form-control" data-bind="value:(member_name)?member_name:'None'" disabled />
                                            <!--/ko-->
                                            <!-- ko ifnot: selected_account -->
                                            <input type='text' class="form-control" value="Select Account No. first..." disabled />
                                            <!--/ko-->
                                        <?php } else { ?>
                                            <input class="form-control" data-bind="value:(member_name)?member_name :'None'" disabled />
                                        <?php } ?>
                                    </div>
                                    </div>

                                    <fieldset class="col-lg-12 form-group">
                                        <legend>Depositor Account</legend>
                                        <div class="form-group row">
                                            <div class="col-lg-5 d-flex align-items-center">
                                                <label class="col-form-label">Attach member depositing? <span class="text-danger">*</span></label>&nbsp;&nbsp;
                                                <span class="mt-2">
                                                    <center>
                                                        <label> <input checked value="0" name="depositor_account_attached" type="radio" > No </label>
                                                        <label> <input value="1" type="radio" name="depositor_account_attached" > Yes</label>
                                                    </center>
                                                </span>
                                            </div>

                                            

                                        </div>

                                    <div class="0 box"></div>
                                    <div class="1 box">
                                         <?php
                                    if (!isset($selected_account)) {
                                    ?>
                                        <!--ko with: selected_account -->
                                        <div class="form-group" data-bind="if: client_type==2">
                                            <div class="row">
                                                
                                                <label class="col-lg-8 col-form-label">Please, Select the group Member depositing on this account</label>
                                                <div class="col-lg-4 form-group">
                                                    <select class="form-control " style="width: 100%" name="group_member_id" data-bind="options: $root.group_members, optionsText: 'member_name', optionsAfterRender: setOptionValue('id'), optionsCaption: '--select--', value: $root.member_nm">
                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- /ko -->
                                    <?php } else { ?>
                                        <!--ko if: ($root.selected_account().client_type==2) -->
                                        <div class="form-group">
                                            <div class="row">
                                                
                                                <label class="col-lg-8 col-form-label">Please, Select the group Member depositing on this account</label>
                                                <div class="col-lg-4 form-group">
                                                    <select class="form-control select2able" style="width: 100%" name="group_member_id" data-bind="options: group_members  , optionsText: 'member_name', optionsAfterRender: setOptionValue('id'), optionsCaption: '--select--', value: id">
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                        <!--/ko -->
                                    <?php } ?>
                                        
                                    </div>

                                    </fieldset>



                                    
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label">Date <span class="text-danger">*</span></label></label>
                                        <div class="col-lg-4">
                                            <div class="input-group date" data-date-start-date="<?php echo isset($active_month) ? date('d-m-Y', strtotime($active_month['month_start'])) : date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month) ? ((strtotime(date('d-m-Y')) < (strtotime($active_month['month_end']))) ? date('d-m-Y') : date('d-m-Y', strtotime($active_month['month_end']))) : ((strtotime(date('d-m-Y')) < (strtotime($fiscal_active['end_date']))) ? date('d-m-Y') : date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>">

                                                <input type="text" autocomplete="off" placeholder="DD-MM-YYYY" class="form-control" onkeydown="return false" name="transaction_date" data-bind="datepicker: $root.transaction_date_de" value="<?php echo mdate("%d-%m-%Y"); ?>" required />

                                                <span class="input-group-addon" data-bind="datepicker: $root.transaction_date_de"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                        <label class="col-lg-2 col-form-label">Deposit Mode <span class="text-danger">*</span></label></label>
                                        <div class="col-lg-4">
                                            <select data-bind='options: $root.payment_modes, optionsText: "payment_mode", optionsCaption: "-- select --" ,optionsAfterRender: setOptionValue("id"),value:$root.selected_mode_d, attr:{name:"payment_id"}' class="form-control" required> </select>
                                        </div>

                                    </div>
                                    <!--ko with: selected_account-->
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label">Amount <small>(UGX)</small> <span class="text-danger">*</span></label></label>
                                        <div class="col-lg-4">
                                            <input class="form-control" id="money_status" data-bind="textInput: $parent.deposit_amount,valueUpdate:'afterkeydown', 
                                    attr: {'data-rule-min':((parseFloat(mindepositamount)>0)?mindepositamount:null),'data-msg-min':'Amount is less than '+curr_format(parseInt(mindepositamount))}
                                           " type="text" required>
                                            <span data-bind="visible:(parseFloat(mindepositamount)>=$parent.deposit_amount())" class="blueText">Should be above <b data-bind="text:parseFloat(mindepositamount)*1+ parseFloat($parent.totaldepositCharges())"></b> (minimum deposit).</span>
                                        </div>
                                        <label class="col-lg-2 col-form-label">Transaction channel <span class="text-danger">*</span></label></label>
                                        <div class="col-lg-4">
                                            <select class="form-control" id="transaction_channel_id" name="transaction_channel_id" data-bind='options: $parent.transaction_channel, optionsText: "channel_name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:$parent.tchannels' data-msg-required="Transaction channel  must be selected" style="width: 100%" required>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label">Narrative</label>
                                        <div class="col-lg-8">
                                            <textarea placeholder="" required class="form-control" id="narrative" name="narrative"></textarea>
                                        </div>
                                    </div>
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
                                                    <tbody data-bind="foreach: $parent.deposit_fees">
                                                        <tr>
                                                            <td data-bind="text:$index()+1" class="border-right"></td>
                                                            <td>
                                                                <span class="input-xs" required data-bind="text:(feename)?feename:'None'"></span>
                                                                <input class="form-control input-xs" required data-bind="value:(id)?id:'none', attr: {name:'charges['+$index()+'][charge_id]'}" type="hidden">
                                                            </td>
                                                            <td>
                                                                <span class="input-xs" required data-bind="text:curr_format(
                                                        (parseInt(cal_method_id)==3)?$root.compute_fee_amount(savings_fees_id,$root.deposit_amount()):
                                                        ( (parseInt(cal_method_id) == 1)?(parseFloat(amount)*1*parseFloat($root.deposit_amount()))/100:amount*1) )"></span>
                                                                <input class="input-xs" required class="form-control" data-bind="value:
                                                        (parseInt(cal_method_id)==3)?$root.compute_fee_amount(savings_fees_id,$root.deposit_amount()):
                                                        ( (parseInt(cal_method_id) == 1)?(parseFloat(amount)*1*parseFloat($root.deposit_amount()))/100:amount*1), attr: {name:'charges['+$index()+'][charge_amount]'}" type="hidden">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <td><span class="pull-right ">Total charges :</span></td>
                                                            <input type="hidden" name="total_charges" data-bind="value: ($parent.totaldepositCharges())?$parent.totaldepositCharges():'0'">
                                                            <th data-bind=" text: ($parent.totaldepositCharges())?$parent.totaldepositCharges():'0'"></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </fieldset>
                                        </div>
                                        <div class="col-lg-5">
                                            <fieldset class="">
                                                <legend style=" text-align: right;"> Deposit Amount</legend>
                                                <small class="pull-right text-muted">Charges included</small>
                                                <input class="form-control" id="amount" data-bind="value:$parent.deposit_amount()" name="amount" type="text" required hidden>
                                                <h2 class="pull-right" data-bind="text:curr_format($parent.deposit_amount())"></h2>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <!--/ko-->

                                    <div class="form-group row">
                                        <div class="col-lg-8">
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input " name="print" id="print">
                                                <label class="form-check-label" for="print"><span class="text-success"><b>Print Receipt</b></span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">
                                    <?php
                                    if (isset($saveButton)) {
                                        echo $saveButton;
                                    } else {
                                        echo "Save";
                                    }
                                    ?>
                                </button>
                            </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function(){
    $('input[type="radio"]').click(function(){
        var inputValue = $(this).attr("value");
        var targetBox = $("." + inputValue);
        $(".box").not(targetBox).hide();
        $(targetBox).show()
    });
});
</script>