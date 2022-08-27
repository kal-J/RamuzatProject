<style type="text/css">
    .blueText{color: blue;font-size: 10px;}
</style>
<div class="modal inmodal fade" id="add_transaction" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <?php if(isset($payment_engine['payment_id']) && $payment_engine['payment_id'] ==3){?>
                <form method="post" enctype="multipart/form-data" class="formValidate"  action="<?php echo base_url('u/beyonic_payment/deposit'); ?>" id="formBeyonicDeposit" >

            <?php  } elseif(isset($payment_engine['payment_id']) && $payment_engine['payment_id'] ==2 ){?>
             <form method="post" enctype="multipart/form-data" action="pesapal/make_payment" id="formPesapalDeposit" >
                <!-- onsubmit="return false;" -->

            <?php } elseif(isset($payment_engine['payment_id']) && $payment_engine['payment_id'] ==1 ){?>
             <form method="post" enctype="multipart/form-data" class="formValidate"  action="<?php echo base_url('u/sentepay/deposit'); ?>" id="formSentePayDeposit" >
                <!-- onsubmit="return false;" -->

            <?php }?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">  
                        <?php if (!isset($selected_account)) { ?>
                            <!-- ko with: selected_account -->
                            <span data-bind="text:(client_type==1)?'Quick  Deposit':'Quick Group Deposit'"></span>
                            <input type="hidden" data-bind="value:(client_type==1)?1:2" name="client_type">
                            <!-- /ko -->
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
                        <input type="hidden" name="accessKey" value="$2a$08$oDgKu9jLJ5LE/J1IwkCiC.ueDOs2uT9BI7GhHrIjKaw1PpSZi96Ca">
                        <input type="hidden" name="countryCode" value="UG">
                        <input type="hidden" name="transaction_type_id" id="transaction_type_id" value="2">
                        <input type="hidden" name="charge_id" id="charge_id" value="7">
                        <!--deposit-->

                        <?php if (!isset($selected_account)) { ?> 
                            <div class="form-group row">
                                <!-- ko with: selected_account -->
                                <input name="cash_bal" data-bind="value: (cash_bal)?cash_bal :''" type="hidden"/>
                                <input id="account_no_id" name="account_no_id" data-bind="value: (id)?id :''" type="hidden"/>
                                <input name="opening_balance" data-bind="value: (opening_balance)?opening_balance :0" type="hidden"/>
                                <input name="state_id" data-bind="value: (state_id)?state_id :''" type="hidden"/>
                                <input id="member_id" name="member_id" data-bind="value: (member_id)?member_id :''" type="hidden"/>
                                <input id="client_type" data-bind="value: (client_type)?client_type :''" type="hidden"/>
                                <!-- /ko -->
                            <?php } else { ?>
                                <div class="form-group row" data-bind="with: selected_account">
                                    <input name="cash_bal" data-bind="value: (cash_bal)?cash_bal :''" type="hidden"/>
                                    <input name="account_no_id" data-bind="value: (id)?id :''" type="hidden"/>
                                    <input name="opening_balance" data-bind="value: (opening_balance)?opening_balance :0" type="hidden"/>
                                    <input name="state_id" data-bind="value: (state_id)?state_id :''" type="hidden"/>
                                    <input name="account_details" value="1" type="hidden"/>
                                <?php } ?>
                                <label class="col-lg-2 col-form-label"><span class="text-danger">*</span>Account No.</label>
                                <div class="col-lg-4"> 
                                    <?php if (!isset($selected_account)) { ?> 
                                        <!-- ko with: selected_account -->
                                    <?php } ?>
                                    <input placeholder="" disabled class="form-control" id="account_no" type="text" data-bind="value: (account_no)?account_no:'None'">
                                    <?php if (!isset($selected_account)) { ?> 
                                        <!-- /ko-->
                                    <?php } ?>
                                </div>
                                <label class="col-lg-2 col-form-label">Account Holder<span class="text-danger">*</span></label>
                                <div class="col-lg-4"> 
                                    <?php if (!isset($selected_account)) { ?>  
                                        <!-- ko with: selected_account -->
                                        <input type='text' id="client_name" class="form-control" data-bind="value:(member_name)?member_name:''" disabled />
                                        <!--/ko-->
                                        <!-- ko ifnot: selected_account -->
                                        <input type='text' class="form-control" value="Select Account No. first..." disabled />
                                        <!--/ko-->
                                    <?php } else { ?>
                                        <input class="form-control" data-bind="value:(member_name)?member_name :''" disabled />
                                    <?php } ?>
                                </div> 
                            </div>
                            <?php
                            if (!isset($selected_account)) {
                                ?>
                                <!--ko with: selected_account -->
                                <div class="form-group border-bottom" data-bind="if: client_type==2"> 
                                    <div class="row">
                                        <b class="col-lg-12"><u>
                                                Depositor Section</u></b>
                                        <label class="col-lg-8 col-form-label">Please, Select the group Member depositing on this account</label>
                                        <div class="col-lg-4 form-group"> 
                                            <select required class="form-control " style="width: 100%" id="group_member_id" name="group_member_id" data-bind="options: $root.group_members, optionsText: 'member_name', optionsAfterRender: setOptionValue('id'), optionsCaption: '--select--', value: $root.member_nm"> 
                                            </select> 

                                        </div>
                                    </div>
                                </div> 
                                <!-- /ko -->
                            <?php } else { ?> 
                                <!--ko if: ($root.selected_account().client_type==2) -->
                                <div class="form-group border-bottom ">
                                    <div class="row">
                                        <b class="col-lg-12"><u>
                                                Depositor Section</u></b>
                                        <label class="col-lg-8 col-form-label">Please, Select the group Member depositing on this account</label>
                                        <div class="col-lg-4 form-group"> 
                                            <select required class="form-control select2able" style="width: 100%" name="group_member_id" data-bind="options: group_members  , optionsText: 'member_name', optionsAfterRender: setOptionValue('id'), optionsCaption: '--select--', value: id"> 
                                            </select>  
                                        </div>

                                    </div>
                                </div>   
                                <!--/ko -->
                            <?php } ?>

                            <!--ko with: selected_account-->
                            <div class="form-group row">  
                                <label class="col-lg-2 col-form-label">Amount</label>
                                <div class="col-lg-4">
                                    <input class="form-control" data-bind="textInput: $parent.deposit_amount,valueUpdate:'afterkeydown', 
                                    attr: {'data-rule-min':((parseFloat(mindepositamount)>0)?mindepositamount:null),'data-msg-min':'Amount is less than '+curr_format(parseInt(mindepositamount))}
                                           " type="number" required >
                                    <span data-bind="visible:(parseFloat(mindepositamount)>=$parent.deposit_amount())" class="blueText">Should be above <b data-bind="text:parseFloat(mindepositamount)*1+ parseFloat($parent.totaldepositCharges())"></b> (minimum deposit).</span>
                                </div>
                                <label class="col-lg-2 col-form-label">Phone Number</label>
                                <div class="col-lg-4">
                                    <input type="number"id="client_contact" name="client_contact" class="form-control" pattern="^(0|\+256)[2347]([0-9]{8})" data-pattern-error="Wrong number format, start with + 0" required  >
                                </div>
                            </div>  
                            <div class="form-group row">  
                                 <label class="col-lg-2 col-form-label">Narrative</label>
                                <div class="col-lg-8">
                                    <textarea rows="5" required class="form-control" id="narrative" name="narrative"></textarea>
                                </div>
                            </div>
                            <br>
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
                                                        <input  class="form-control input-xs" required data-bind="value:(id)?id:'none', attr: {name:'charges['+$index()+'][charge_id]'}" type="hidden">
                                                    </td> 
                                                    <td >
                                                        <span class="input-xs" required data-bind="text:curr_format((parseInt(cal_method_id) == 1)?((parseFloat(amount)*1*parseFloat($root.deposit_amount()))/100):amount*1)" ></span>
                                                        <input class="input-xs" required class="form-control"  data-bind="value:(parseInt(cal_method_id) == 1)?(parseFloat(amount)*1*parseFloat($root.deposit_amount()))/100:amount*1, attr: {name:'charges['+$index()+'][charge_amount]'}" type="hidden">
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td><span class="pull-right ">Total charges :</span></td>
                                                    <input type="hidden" name="total_charges" data-bind="value: ($parent.totaldepositCharges())?$parent.totaldepositCharges():'0'" >
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
                             <!-- <div class="form-group row">   
                                <div class="col-lg-8">

                                </div>
                                <div class="col-lg-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input " name="print" id="print">
                                        <label class="form-check-label" for="print"><span class="text-success"><b>Print Receipt</b></span></label>
                                    </div>
                                </div> 
                            </div>  -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                            <button id="btn-submit" type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-check"></i> Deposit</button>
                    </div>
            </form>
        </div>
    </div>
</div>
