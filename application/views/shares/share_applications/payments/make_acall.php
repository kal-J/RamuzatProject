<style type="text/css">
.blueText{color: blue;font-size: 10px;}
</style>
<div class="modal inmodal fade" id="make_call" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form method="post" enctype="multipart/form-data" class="formValidate" action="<?php echo base_url(); ?>Share_transaction/Create" id="formMake_acall">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">  
                        <span data-bind="text:'Make Payment'"></span>
                </h4>
                <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
                <div class="">
                        <!--ko with: call_payment2-->
                    <input type="hidden" name="share_call_id" data-bind="value:id">
                    <input type="hidden" name="share_issuance_id" data-bind="value:share_issuance_id">
                    <input type="hidden" name="share_account_id" id="share_account_id" data-bind="value:share_account_id">
                    <input type="hidden" name="application_id" id="application_id" data-bind="value:share_application_id">
                        <div class="form-group row">  
                        <label class="col-lg-2 col-form-label">Share Call</label>
                        <div class="col-lg-4">
                        <input type="text" class="form-control"  data-bind="value:call_name" disabled>
                        </div>
                        <label class="col-lg-2 col-form-label">Share Application No</label>
                        <div class="col-lg-4">
                        <input type="text" class="form-control"  data-bind="value:share_application_no" disabled>
                        </div>
                        </div>
                        <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Call Amount</label>
                        <div class="col-lg-4">
                            <input class="form-control" data-bind="textInput: parseFloat(total_call_amount)-parseFloat(amount_paid),valueUpdate:'afterkeydown', 
                            attr: {'data-rule-min':1,'data-msg-min':'Amount must be greater or equal to 1','data-rule-max':parseFloat(total_call_amount)-parseFloat(amount_paid),'data-msg-max':'Amount is greater than '+curr_format(parseFloat(total_call_amount)-parseFloat(amount_paid))}" name="amount" type="text" required>
                            <span class="blueText">Call amount must be equal or less than <b data-bind="text:curr_format(parseFloat(total_call_amount)-parseFloat(amount_paid))"></b> (call balance).</span>
                            </div>
                           <label class="col-lg-2 col-form-label">Payment Mode <span class="text-danger">*</span></label></label> 
                            <div class="col-lg-4">
                                <select  data-bind='options: $root.payment_modes, optionsText: "payment_mode", optionsCaption: "-- select --" ,optionsAfterRender: setOptionValue("id"),value: $root.payment_mode, attr:{name:"payment_id"}' class="form-control"  required > </select>
                            </div>
                        </div>  
                        <!--/ko-->

                        <div class="form-group row">  
                             <label class="col-lg-2 col-form-label">Transaction channel</label>
                            <div class="col-lg-4">
                                <select  class="form-control" id="transaction_channel_id" name="transaction_channel_id" data-bind='options:transaction_channel, optionsText: "channel_name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:tchannels' data-msg-required="Transaction channel  must be selected" style="width: 100%" required >
                                </select>
                            </div>
                            <label class="col-lg-2 col-form-label">Date</label>
                            <div class="col-lg-4">
                                <div class="input-group date">
                                <input  type="text" class="form-control" name="transaction_date"  placeholder="Transaction date" required/>
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
