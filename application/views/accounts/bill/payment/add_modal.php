<div class="modal inmodal fade" id="pay_bill-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="post" class="formValidate" action="<?php echo site_url("bill_payment/create"); ?>" id="formBill_payment">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Bill Payment</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id"/>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="payment_date">Payment date<span class="text-danger">*</span></label>
                        <div class="form-group col-lg-4">
                            <div class="input-group date" data-date-start-date="<?php echo $fiscal_year['start_date2']; ?>" data-date-end-date="+0d">
                                <input type="text" id="payment_date" name="payment_date" class="form-control" value="<?php echo date('d-m-Y') ?>" required><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <label class="col-lg-2 col-form-label">Cash account<span class="text-danger">*</span></label>
                        <div class="form-group col-lg-4">
                            <select class=" form-control bill_payment_selects" id="cash_account_id" name="cash_account_id" data-bind='options: select2accounts([3,4,5]), optionsText: formatAccount2, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")' style="width: 100%" required ></select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="ref_no">Reference</label>
                        <div class="form-group col-lg-4">
                            <input type="text" name="ref_no" id="ref_no" placeholder="" class="form-control"/>
                        </div>
                        <label class="col-lg-2 col-form-label">Description</label>
                        <div class="col-lg-4">
                            <textarea class="form-control" rows="2" name="description"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Authorized By</label>
                        <div class="form-group col-lg-4">
                            <select class="form-control bill_payment_selects m-b" name="authorizer_id" data-bind='options: authorizer_list,  optionsText: function(item){return item.salutation + " " + item.firstname + " " +item.lastname + " ";}, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                            </select>
                        </div>
                        <label class="col-lg-2 col-form-label">Attachment</label>
                        <div class="form-group col-lg-4">
                            <input type="file" name="file_attachment" class="form-control">
                        </div>
                    </div> 
                    <div class="row">
                        <h3>Unpaid Bills<span class="text-danger">*</span></h3>
                        <div class="table-responsive">
                            <table class="table table-condensed table-borderless table-striped">
                                <thead>
                                    <tr>
                                        <th>Bill Ref#</th>
                                        <th>Bill Amount</th>
                                        <th>Amount Due</th>
                                        <th>Due date</th>
                                        <!--th>Discount</th>
                                        <th>Discount Account</th-->
                                        <th>Amount Paid</th>
                                        <th>Narrative</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody data-bind="foreach: bill_payment_lines">
                                    <tr>
                                        <td>
                                            <label data-bind="text: ref_no"></label>
                                            <input type="hidden"  class="form-control m-b" data-bind='attr:{name:"bill_payment_line["+$index()+"][id]",value:id}'/>
                                            <input type="hidden" data-bind='value:bill_id, attr:{name:"bill_payment_line["+$index()+"][bill_id]"}'/>
                                            <input type="hidden" data-bind='value:supplier_account_id, attr:{name:"bill_payment_line["+$index()+"][supplier_account_id]"}'/>
                                        </td>
                                        <td>
                                            <label data-bind="text: curr_format(total_amount())"></label>
                                        </td>
                                        <td>
                                            <label data-bind="text: curr_format(due_amount())"></label>
                                        </td>
                                        <td>
                                            <label data-bind="text: moment(due_date(),'YYYY-MM-DD').format('D/M/YYYY')"></label>
                                        </td>
                                        <!--td>
                                            <input type="number" class="form-control" min="0" data-bind='textInput: discount_amount, attr:{name:"bill_payment_line["+$index()+"][discount_amount]", required:typeof discount_account()!=="undefined" && discount_account!==null, "data-rule-max":amount_paid}' />
                                        </td>
                                        <td>
                                            <select class="form-control m-b" data-bind='options: $parent.select2accounts([12,13]), optionsText: $parent.formatAccount2, value:discount_account, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), attr:{name:"bill_payment_line["+$index()+"][discount_account_id]"}' data-msg-required="Discount account must be selected" style="width: 100%"></select>
                                        </td-->
                                        <td>
                                            <input type="number" class="form-control" min="0" data-bind='textInput: amount_paid, attr:{name:"bill_payment_line["+$index()+"][amount]"}' required/>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="Description" data-bind='value:narrative, attr:{name:"bill_payment_line["+$index()+"][narrative]"}' required/>
                                        </td>
                                        <td>
                                            <span title="Remove item" class="btn text-danger" data-bind='click: $parent.removeBillPaymentLine,visible:$index() > 0'><i class="fa fa-minus"></i></span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">&nbsp;</th>
                                        <th colspan="2">Total (UGX)</th>
                                        <th data-bind="text: curr_format(bill_payment_sum())">0</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <?php if ((in_array('1', $accounts_privilege)) || (in_array('3', $accounts_privilege))) { ?>
                        <button type="submit" class="btn btn-primary"><?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            } else {
                                echo "Save";
                            }
                            ?></button>
                    <?php } ?>
                </div>
            </div>  
        </form>
    </div>
</div>