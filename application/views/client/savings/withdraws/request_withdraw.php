<style type="text/css">
    .blueText{color: blue;font-size: 10px;}
</style>
<div class="modal inmodal fade" id="request_withdraw" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post" enctype="multipart/form-data" class="formValidate"  action="<?php echo base_url('u/savings/withdraw_request'); ?>" id="requestWithdrawForm" >
                <!-- onsubmit="return false;" -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">  
                        <?php if (!isset($selected_account)) { ?>
                            <!-- ko with: selected_account -->
                            <span data-bind="text:(client_type==1)?'Withdraw Request':'Quick Group Deposit'"></span>
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

                            <div class="form-group row">
                                <!-- ko with: selected_account -->
                                <input name="cash_bal" data-bind="value: (cash_bal)?cash_bal :''" type="hidden"/>
                                <input id="account_no_id" name="account_no_id" data-bind="value: (id)?id :''" type="hidden"/>
                                <input name="opening_balance" data-bind="value: (opening_balance)?opening_balance :0" type="hidden"/>
                                <input name="state_id" data-bind="value: (state_id)?state_id :''" type="hidden"/>
                                <input id="member_id" name="member_id" data-bind="value: (member_id)?member_id :''" type="hidden"/>
                                <input id="client_type" data-bind="value: (client_type)?client_type :''" type="hidden"/>
                                <!-- /ko -->
                            
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
                           

                            <!--ko with: selected_account-->
                            <!-- <pre data-bind="text: JSON.stringify(ko.toJS($root.accountw), null, 2)"></pre> -->
                            <div class="form-group row">  
                                <label class="col-lg-2 col-form-label">Amount</label>
                                <div class="col-lg-4">
                                    <input min="0" class="form-control" name="amount" data-bind="textInput: $parent.deposit_amount" type="number" required >
                                    <!-- <span data-bind="visible:(parseFloat(cash_bal)< $parent.deposit_amount())" class="blueText">Should be less than <b data-bind="text:parseFloat(cash_bal)*1+ parseFloat($parent.totaldepositCharges())"></b> (maximum withdraw).</span> -->

                                    <input type="hidden" id="max_withdraw" data-bind="textInput:parseFloat(cash_bal)*1+ parseFloat($parent.totaldepositCharges()" />
                                </div>

                             <input type="hidden" value="1" name="transaction_channel_id" />

                                <!-- <label class="col-lg-2 col-form-label">Phone Number</label>
                                <div class="col-lg-4">
                                    <input type="number"id="client_contact" name="client_contact" class="form-control" pattern="^(0|\+256)[2347]([0-9]{8})" data-pattern-error="Wrong number format, start with + 0" required  >
                                </div> -->
                            </div>  
                            <div class="form-row">
                              
                            </div>
                            <div class="form-group row">  
                                 <label class="col-lg-2 col-form-label">Reason</label>
                                <div class="col-lg-8">
                                    <textarea rows="5" required class="form-control" id="narrative" name="reason"></textarea>
                                </div>
                            </div>
                            <br>
                           
                            <!--/ko-->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                            <button id="btn-submit" type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-check"></i> Request</button>
                    </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("document").ready(function(){

        $.validator.addMethod('lessThanEqual', function(value,  param) {
            var i = parseInt(value);
            var j = parseInt($(param).val());
            return i <= j;
        }, "The value {0} must be less than {1}");

        console.log(ko.selected_account);
        $("#requestWithdrawForm").validate({
            rules: {
                amount: {
                    required: true,
                    number: true,
                    // max: $("#max_withdraw").val(),
                    // lessThanEqual: 
                    // max: function() {
                    //     return parseFloat($("#max_withdraw").val());
                    // }
                },
                reason: {
                    required: true,
                    minlength: 5,
                }
            },
            messages: {
                // amount: {
                //     max : "Should be less than "+$("#max_withdraw").val()+" (maximum withdraw)."
                // }
            },
            submitHandler: function (form) {
                $.ajax({
                    type: $(form).attr('method'),
                    url: $(form).attr('action'),
                    data: $(form).serialize(),
                    dataType : 'json'
                })
                .done(function (response) {
                    if (response.success == true) {    
                        toastr.success(response.message, "Success");  
                        $("#request_withdraw").modal('hide');    
                    } else {
                        toastr.warning(response.message, "Failure!");
                    }
                });
                return false; 
            }
        });
    })
</script>