<div class="modal inmodal fade" id="add_invoice-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="post" class="formValidate" action="<?php echo site_url("invoice/create"); ?>" id="formInvoice">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "New Invoice";
                        }
                        ?>
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id"/>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="client_id">Client<span class="text-danger">*</span></label>
                                <div class="form-group col-lg-8">
                                    <select class="form-control invoice_selects m-b" name="client_id" data-bind='select2:select2options("#add_invoice-modal")' style="width: 100%">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Debtor's Account</label>
                                <div class="form-group col-lg-8">
                                    <select class="form-control m-b invoice_selects" name="receivable_account_id" data-bind='options: select2accounts(1), optionsText: formatAccount2, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")' data-msg-required="Account must be selected" style="width: 100%" required></select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Discount Allowed</label>
                                <div class="form-group col-lg-8">
                                    <input type="number" name="discount" class="form-control" placeholder="Discount" min="0" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Discount Account</label>
                                <div class="form-group col-lg-8">
                                    <select class="form-control invoice_selects m-b" name="discount_account_id" data-bind='options: select2accounts([14,15,16]), optionsText: formatAccount2, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")' data-msg-required="Discount account must be selected" style="width: 100%"></select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="ref_no">Ref No.</label>
                                <div class="form-group col-lg-8">
                                    <input type="text" name="ref_no" class="form-control" placeholder="Reference No."/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="invoice_date">Date Invoiced<span class="text-danger">*</span></label>
                                <div class="form-group col-lg-8">
                                    <div class="input-group date" data-date-start-date="<?php echo $fiscal_year['start_date2']; ?>" data-date-end-date="+0d">
                                        <input type="text" id="invoiceing_date" name="invoice_date" class="form-control" value="<?php echo date('d-m-Y') ?>" required><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="due_date">Due date<span class="text-danger">*</span></label>
                                <div class="form-group col-lg-8">
                                    <div class="input-group date" data-date-start-date="+0d">
                                        <input type="text" id="due_date" name="due_date" class="form-control" value="<?php echo date('d-m-Y') ?>" required><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Terms</label>
                                <div class="form-group col-lg-8">
                                    <select  class="form-control invoice_selects" name="applied_tax_id" data-bind='options: tax_list, optionsText: "tax_name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:applied_tax, optionsDisableDefault: true' style="width: 100%" >
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label class="col-form-label">Narrative</label>
                            <textarea class="form-control" rows="2" name="description"></textarea>
                        </div>
                        <div class="col-lg-6">
                            <label class="col-form-label">Attachment</label>
                            <input type="file" name="file_attachment" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <h3>Income Accounts<span class="text-danger">*</span></h3>
                        <div class="table-responsive">
                            <table class="table table-condensed table-borderless table-striped">
                                <thead>
                                    <tr>
                                        <th>Category/Account</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <!--th>Tax</th-->
                                        <th><button data-bind='click: addInvoiceLine' class="btn btn-white btn-sm" title="Add Item"><i class="fa fa-plus"></i></button></th>
                                    </tr>
                                </thead>
                                <tbody data-bind="foreach: invoice_lines">
                                    <tr>
                                        <td>
                                            <select class="form-control m-b detail_accounts" data-bind='options: $parent.select2accounts([12,13]), optionsText: $parent.formatAccount2, value:selected_account, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), attr:{name:"invoice_line_item["+$index()+"][account_id]"}' data-msg-required="Account must be selected" style="width: 100%" required></select>
                                            <input type="hidden"  class="form-control m-b" data-bind='attr:{name:"invoice_line_item["+$index()+"][id]",value:id}'/>
                                        </td>
                                        <td data-bind="with: selected_account">
                                            <input type="number" class="form-control" min="0" data-bind='textInput: $parent.amount, attr:{name:"invoice_line_item["+$index()+"][amount]"}' required/>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="Description" data-bind='value:narrative, attr:{name:"invoice_line_item["+$index()+"][narrative]"}' required/>
                                        </td>
                                        <td>
                                            <span title="Remove item" class="btn text-danger" data-bind='click: $parent.removeInvoiceLineItem,visible:$index() > 0'><i class="fa fa-minus"></i></span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total (UGX)</th>
                                        <th data-bind="text: curr_format(invoice_sum())">0</th>
                                        <th colspan="2">&nbsp;</th>
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