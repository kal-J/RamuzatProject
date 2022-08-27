<div class="modal inmodal fade" id="add_income-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="post" class="formValidate" action="<?php echo site_url(); ?>income/create" id="formIncome">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "New Sale";
                        }
                        ?>
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id"/>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="client_id">Client<span class="text-danger">*</span></label>
                        <div class="form-group col-lg-4">
                            <select class="form-control income_selects m-b" id="client_id" name="client_id" data-bind='select2:select2options("#add_income-modal")' style="width: 100%">
                            </select>
                        </div>
                        <label class="col-lg-2 col-form-label" for="receipt_date">Receipt date<span class="text-danger">*</span></label>
                        <div class="form-group col-lg-4">
                            <div class="input-group date" data-date-start-date="<?php echo $fiscal_year['start_date2']; ?>" data-date-end-date="+0d">
                                <input type="text" id="payment_date" name="receipt_date" class="form-control" value="<?php echo date('d-m-Y') ?>" required>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Cash account<span class="text-danger">*</span></label>
                        <div class="form-group col-lg-4">
                            <select class=" form-control income_selects" id="cash_account_id" name="cash_account_id" data-bind='options: select2accounts([3,4,5]), optionsText: formatAccount2, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")' style="width: 100%" required >
                                <option value="">--select--</option>
                            </select>
                        </div>
                        <label class="col-lg-2 col-form-label" for="receipt_no">Receipt No.</label>
                        <div class="form-group col-lg-4">
                            <input type="text" name="receipt_no" id="receipt_no" placeholder="" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Applied Tax</label>
                        <div class="form-group col-lg-4">
                            <select  class="form-control income_selects" id="tax_rate_source_id" name="tax_rate_source_id" data-bind='options: tax_list, optionsText: "tax_name", optionsCaption: "--no tax--", optionsAfterRender: setOptionValue("id"), value:applied_tax, optionsDisableDefault: true' style="width: 100%" >
                            </select>
                        </div>
                        <label class="col-lg-2 col-form-label">Received By</label>
                        <div class="form-group col-lg-4">
                            <select class="form-control income_selects m-b" id="authorizer_id" name="receiver_id" data-bind='options: authorizer_list,  optionsText: function(item){return item.salutation + " " + item.firstname + " " +item.lastname + " ";}, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Narrative</label>
                        <div class="col-lg-10">
                            <textarea class="form-control" rows="2" name="description"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Attachment</label>
                        <div class="form-group col-lg-10">
                            <input type="file" name="file_attachment" class="form-control">
                        </div>
                    </div> 
                    <div class="row">
                        <h3>Accounts<span class="text-danger">*</span></h3>
                        <div class="table-responsive">
                            <table class="table table-condensed table-borderless table-striped">
                                <thead>
                                    <tr>
                                        <th>Category/Account</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <!--th>Tax</th-->
                                        <th><button data-bind='click: addIncomeLine' class="btn btn-white btn-sm" title="Add Row"><i class="fa fa-plus"></i></button></th>
                                    </tr>
                                </thead>
                                <tbody data-bind="foreach: income_lines">
                                    <tr>
                                        <td>
                                            <select class="form-control m-b detail_accounts" data-bind='options: $parent.select2accounts([12,13]), optionsText: $parent.formatAccount2, value:selected_account, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), attr:{name:"income_line_item["+$index()+"][account_id]"}' data-msg-required="Account must be selected" style="width: 100%" required></select>
                                            <input type="hidden"  class="form-control m-b" data-bind='attr:{name:"income_line_item["+$index()+"][id]",value:id}'/>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="Description" data-bind='value:narrative, attr:{name:"income_line_item["+$index()+"][narrative]"}' required/>
                                        </td>
                                        <td data-bind="with: selected_account">
                                            <input type="number" class="form-control" min="0" data-bind='textInput: $parent.amount, attr:{name:"income_line_item["+$index()+"][amount]"}' required/>
                                        </td>
                                        <td>
                                            <span title="Remove item" class="btn text-danger" data-bind='click: $parent.removeIncomeLine,visible:$index() > 0'><i class="fa fa-minus"></i></span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th data-bind="text: curr_format(income_sum())">0</th>
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