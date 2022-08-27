<div class="modal inmodal fade" id="post_entry" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url(); ?>Ledger/Create" id="formLedger">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        Journal Entry
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <div class="">
                        <input type="hidden" name="id">
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Date<span class="text-danger">*</span></label>
                            <div class="form-group col-lg-4">
                                <div class="input-group date">
                                    <input  type="text" class="form-control" name="transaction_date" value="<?php echo mdate("%d-%m-%Y"); ?>" placeholder="Transaction date" required/>
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>     
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Amount<span class="text-danger">*</span></label>
                            <div class="form-group col-lg-4">
                                <input placeholder="Amount" required class="form-control" name="amount" type="number">
                            </div>
                        </div>     
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Debit<span class="text-danger">*</span></label>
                            <div class="form-group col-lg-4">
                            <select class="form-control m-b" id="account_from_id" name="debit_account_id" data-bind='options: accountFromList,  optionsText: function(data_item){return "["+data_item.account_code+"]  " + data_item.account_name;}, optionsCaption: "Select parent Group...", optionsAfterRender: setOptionValue("id"), value:account_name' style="width: 100%">
                            </select>
                            </div>
                            <label class="col-lg-2 col-form-label">Credit<span class="text-danger">*</span></label>
                            <div class="form-group col-lg-4">
                            <select class="form-control m-b" id="account_to_id" name="credit_account_id" data-bind='options:  filteredAccountToList(),  optionsText: function(data_item){return "["+data_item.account_code+"]  " + data_item.account_name;}, optionsCaption: "Select parent Group...", optionsAfterRender: setOptionValue("id"), value:account_name2' data-msg-required="Account must be selected" style="width: 100%">
                            </select>
                              
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Narrative<span class="text-danger">*</span></label>
                            <div class="form-group col-lg-10">
                                <textarea class="form-control" rows="2" required name="narrative" id="description"></textarea>
                            </div>
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
            </form>
        </div>
    </div>
</div>
