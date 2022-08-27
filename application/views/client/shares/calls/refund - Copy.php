<style type="text/css">
.blueText{color: blue;font-size: 10px;}
</style>
<div class="modal inmodal fade" id="make_refund" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form method="post" enctype="multipart/form-data" class="formValidate" action="<?php echo base_url(); ?>Share_transaction/Create" id="formRefund">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">  
                        <span data-bind="text:'Refund'"></span>
                </h4>
                <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
                <div class="">
                    <input type="hidden" name="id" >
                    <input type="hidden" name="transaction_type_id" id="transaction_type_id" value="13">
                    <input type="hidden" name="state_id" id="state_id" value="3">
                    <input type="hidden" name="share_call_id" id="share_call_id" value="0">

                     <input hidden type="text" class="form-control" name="submission_date" value="<?php echo mdate("%d-%m-%Y"); ?>"/>
                    <!--deposit-->
                    <?php if (!isset($selected_account)) { ?> 
                        <div class="form-group row">
                            <!-- ko with: selected_account -->
                            <input name="application_id" data-bind="value: (id)?id :''" type="hidden"/>
                            <!-- /ko -->
                        <?php } else { ?>
                            <div class="form-group row" data-bind="with: selected_account">
                                <input name="application_id" data-bind="value: (id)?id :''" type="hidden"/>
                                <input name="account_details" value="1" type="hidden"/>
                            <?php } ?>
                            <label class="col-lg-2 col-form-label">Share Account No:<span class="text-danger">*</span></label>
                            <div class="col-lg-4">
                                <?php if (!isset($selected_account)){ ?> 
                                    <!-- ko with: selected_account -->
                                <?php } ?>
                                <input type="hidden" name="share_issuance_id" data-bind="attr:{value: (share_issuance_id)?share_issuance_id:''}">
                                <input disabled class="form-control" id="share_account_no" type="text" data-bind="value: (share_account_no)?share_account_no:''">
                                <?php if (!isset($selected_account)) { ?> 
                                    <!-- /ko-->
                                <?php } ?>
                            </div>
                            <label class="col-lg-2 col-form-label">Shareholder<span class="text-danger">*</span></label>
                            <div class="col-lg-4"> 
                                <?php if (!isset($selected_account)) { ?>  
                                    <!-- ko with: selected_account -->
                                    <input type='text' class="form-control" data-bind="value:(member_name)?member_name:'None'" disabled />
                                    <!--/ko-->
                                    <!-- ko ifnot: selected_account -->
                                    <input type='text' class="form-control" value="Select Application No. first..." disabled />
                                    <!--/ko-->
                                <?php } else { ?>
                                    <input class="form-control" data-bind="value:(member_name)?member_name :'None'" disabled />
                                <?php } ?>
                            </div> 
                    </div>
                    <!--ko with: selected_account -->
                    <div class="form-group row">  
                        <label class="col-lg-2 col-form-label">Amount</label>
                            <div class="col-lg-4">
                                <input class="form-control" data-bind="value:(total_amount)?total_amount:''" name="amount" id="amount" type="text" required readonly />
                                <span class="blueText">Refund full amount <b data-bind="text:curr_format(total_amount)"></b> ( Balance ).</span>
                            </div>
                        </div>  
                        <div class="form-group row">  
                             <label class="col-lg-2 col-form-label">Transaction channel</label>
                            <div class="col-lg-4">
                                <select  class="form-control" id="transaction_channel_id" name="transaction_channel_id" data-bind='options: $parent.transaction_channel, optionsText: "channel_name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:$parent.tchannels' data-msg-required="Transaction channel  must be selected" style="width: 100%" required >
                                </select>
                            </div>
                            <label class="col-lg-2 col-form-label">Refund Date</label>
                            <div class="col-lg-4">
                                <div class="input-group date">
                                <input  type="text" class="form-control" name="transaction_date" value="<?php echo mdate("%d-%m-%Y"); ?>" placeholder="Transaction date" required/>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                               </div>
                            </div>
                        </div>
                        <div class="form-group row">  
                        <label class="col-lg-2 col-form-label">Narrative</label>
                            <div class="col-lg-10">
                                <textarea placeholder="" required class="form-control" id="narrative" name="narrative"></textarea>
                            </div>
                        </div>
                        <br>
                        <!--/ko-->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <?php
                        if (isset($saveButton)) {
                            echo $saveButton;
                        } else {
                            echo "Refund";
                        }
                        ?>
                    </button>
                </div>
        </form>
    </div>
</div>
</div>
