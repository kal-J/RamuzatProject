<div class="modal inmodal fade" id="add_revenue-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="post" class="formValidate" action="<?php echo site_url(); ?>revenue/create" id="formRevenue">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "Add Revenue Stream";
                        }
                        ?>
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id"/>
                    <!--ko with:revenue_source-->
                    <input type="hidden" name="revenue_source_account_id" data-bind="value: linked_account_id"/>
                    <!--/ko-->
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="service_category_id">Source<span class="text-danger">*</span></label>
                        <div class="form-group col-lg-4">
                            <select class="form-control m-b" id="service_category_id" name="service_category_id" data-bind='options: incomeCategoryList,  optionsText: function(data_item){return "[" +data_item.service_category_code+"] " + data_item.service_category_name;}, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"),value: revenue_source' style="width: 100%">
                            </select>
                        </div>
                        <label class="col-lg-2 col-form-label" for="receipt_date">Date<span class="text-danger">*</span></label>
                        <div class="form-group col-lg-4">
                            <div class="input-group date" data-date-end-date="+0d">
                                <input type="text" id="receipt_date" name="receipt_date" class="form-control" value="<?php echo date('d-m-Y') ?>" required><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Amount<span class="text-danger">*</span></label>
                        <div class="form-group col-lg-4">
                            <input type="number" min="0" name="amount" placeholder="" class="form-control" required="required">
                        </div>
                        <label class="col-lg-2 col-form-label" for="receipt_no">Receipt No.</label>
                        <div class="form-group col-lg-4">
                            <input type="text" name="receipt_no" id="receipt_no" placeholder="" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Transaction Channel<span class="text-danger">*</span></label>
                        <div class="form-group col-lg-3">
                            <select  class="form-control" id="transaction_channel_id" name="transaction_channel_id" data-bind='options: transaction_channels, optionsText: "channel_name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:transaction_channel, optionsDisableDefault: true' data-msg-required="Transaction channel must be selected" style="width: 100%" required >
                            </select>
                            <!-- ko with: transaction_channel-->
                            <!-- <input type="hidden" name="cash_or_bank_account_id" id="cash_or_bank_account_id" data-bind="value: linked_account_id"/> -->
                            <!--/ko-->
                        </div>
                        <label class="col-lg-2 col-form-label">Attachment</label>
                        <div class="form-group col-lg-4">
                            <input type="file" name="file_attachment" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Narrative<span class="text-danger">*</span></label>
                        <div class="col-lg-10">
                            <textarea class="form-control" rows="2" required name="description" id="description"></textarea>
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