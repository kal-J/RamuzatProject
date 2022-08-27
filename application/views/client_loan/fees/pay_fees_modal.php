<div class="modal inmodal fade" id="pay_loan_fee-modal"  role="dialog" aria-hidden="true" style="display: none;">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="post" class="formValidate" action="<?php echo base_url();?>applied_loan_fee/Create" id="formLoan_fee_application">
<div class="modal-header">
 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
 <h4 class="modal-title">
  Pay Loan fee(s)</h4>
 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
</div>
<input class="form-control" name="id" id="id" type="hidden">
<input class="form-control" name="loan_id" value="<?php echo $loan_detail['id']; ?>" type="hidden">
<input class="form-control" name="loan_product_id" value="<?php echo $loan_detail['loan_product_id']; ?>" type="hidden">
                <div class="modal-body">
                    <div class="col-lg-12">

                        <div class="table-responsive">
                            <table  class="table table-striped table-condensed table-hover m-t-md">
                                <thead>
                                    <tr>
                                        <th>Fee</th>
                                        <th>Amount</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody data-bind='foreach: $root.pay_loan_fee'>
                                    <tr>
                                        <td>
                                            <select data-bind='
                                            options: $root.unpaid_loan_fees, 
                                            optionsText: function(data_item){return data_item.feename}, 
                                            optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"), 
                                            value: selected_fee' class="form-control"  style="width: 250px"> </select>
                                        </td>
                                        <td data-bind="with: selected_fee">
                                            <label data-bind="text: (parseInt(amountcalculatedas_id)==3)?curr_format( $root.compute_fee_amount(loanfee_id,$root.loan_detail().requested_amount)):( curr_format(parseInt(amountcalculatedas_id)==1?amount:amount) ) "></label>
                                            <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][amount]"}, value: (parseInt(amountcalculatedas_id)==3)?
                                             $root.compute_fee_amount(loanfee_id,$root.loan_detail().requested_amount):(
                                             parseInt(amountcalculatedas_id)==1?amount:amount )'/>
                                            <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][loan_product_fee_id]"}, value: id'/>  
                                        <input type="hidden"  value="1" data-bind='attr:{name:"loanFees["+$index()+"][paid_or_not]"}'>
                                        </td>
                                        <td>
                                            <span title="Remove item" class="btn text-danger" data-bind='click: $root.removeLoanPayFee'><i class="fa fa-minus"></i></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="col-lg-4 col-form-label">Payment Date</label>
                        <div class="col-lg-8">
                            <div class="input-group date" >
                                <input type="text" onkeydown="return false" name="action_date" class="form-control m-b" autocomplete="off" required="required"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                    <label class="col-lg-4 col-form-label">Payment Method</label> 
                    <div class="col-lg-8">
                        <select  data-bind='options: $root.payment_modes, optionsText: "payment_mode", optionsCaption: "-- select --" ,optionsAfterRender: setOptionValue("id"),value: payment_mode, attr:{name:"payment_id"}' class="form-control"  required style="width: 170px;"> </select>
                    </div>
                    </div>
                    <!-- ko with: payment_mode --> 
                        <div class="row form-group"  data-bind="visible: id ==5">
                            <label class="col-lg-4 col-form-label">Savings Account</label>
                            <div class="col-lg-8">
                            <select class="form-control" name="savings_account_id" style="width: 100%">
                                <option value="">--Select--</option>
                                <!-- ko foreach: $root.savings_accs_member -->
                                <option data-bind="value: id, text: account_no + ' | ' + member_name "></option>
                                <!-- /ko -->
                            </select>
                        </div>
                        </div>
                        <div class="row form-group" data-bind="visible: id !=5">
                            <label class="col-lg-4 col-form-label">Transaction Channel</label>
                            <div class="col-lg-8">
                                <select  class="form-control" data-bind='options: $root.transaction_channel, optionsText:function(data_item){return data_item.channel_name}, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), attr:{name:"transaction_channel_id"}, value:$root.trans_channel' data-msg-required="Transaction channel must be selected" style="width: 100%"  >
                                </select>
                            </div>
                        </div>
                <!--/ko-->
                </div>
                <div class="modal-footer">
                    <button data-bind='click: $root.addLoanPayFee, enable:$root.unpaid_loan_fees().length > 0' class="btn-white btn-sm"><i class="fa fa-plus"></i> Add another fee</button>
                    <?php if((in_array('1', $client_loan_privilege))||(in_array('3', $client_loan_privilege))){ ?>
                    <button class="btn btn-primary btn-flat" data-bind="enable:$root.unpaid_loan_fees().length > 0" type="submit">Pay Fee(s)</button>
                    <?php } ?>
                </div>
            </form>
            </div>
            </div>
            </div>
