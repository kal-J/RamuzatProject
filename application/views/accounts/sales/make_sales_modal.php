<style type="text/css">
    section {
        overflow-y: auto;
    }

    @media (min-width: 992px) {

        .modal-lg,
        .modal-xl {
            max-width: 700px !important;
        }
    }

    @media (min-width: 1200px) {
        .modal-xl {
            max-width: 840px !important;
        }
    }
</style>
<div class="modal inmodal fade" id="new_sales-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo site_url("sales/create"); ?>" id="formSales">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title">
                        General Item Sales
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="form-group col-lg-6">
                            <label class="col-form-label">Date<span class="text-danger">*</span></label>

                            <div class="input-group date" data-date-start-date="<?php echo isset($active_month) ? date('d-m-Y', strtotime($active_month['month_start'])) : date('d-m-Y', strtotime($fiscal_year['start_date2'])); ?>" data-date-end-date="<?php echo isset($active_month) ? ((date('d-m-Y') < date('d-m-Y', strtotime($active_month['month_end']))) ? date('d-m-Y') : date('d-m-Y', strtotime($active_month['month_end']))) : ((date('d-m-Y') < date('d-m-Y', strtotime($fiscal_year['end_date2'])) ? date('d-m-Y') : date('d-m-Y', strtotime($fiscal_year['end_date2'])))); ?>">
                                <input type="text" onkeydown="return false" autocomplete="off" class="form-control" name="transaction_date" placeholder="Transaction date" required />
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="col-form-label">Item <span class="text-danger">*</span></label>
                            <!--<input type="text" class="form-control" name="item" placeholder="Item for sale" />-->
                            <select id='item_id' class="form-control select2able" name="item_id" required data-bind='options: items_list, optionsText: function(item) { return `${item.name}`;}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' required data-msg-required="Item is required" style="width: 100%;">
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="form-group col-lg-6">
                            <label class="col-form-label">Amount</label>
                            <input type="text" class="form-control" name="amount" required  placeholder="Cost Price / Total Cost" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="form-group col-lg-6">
                            <label class="col-form-label"><span class="text-danger">*</span>Savings Account No.</label>
                            <select id='default_savings_account_id' class="form-control select2able" name="savings_account_id" required data-bind='options: member_accounts, optionsText: function(item) { return `${item.account_no} ${item.member_name}`;}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' required data-msg-required="Applicant is required" style="width: 100%;">
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="col-form-label">Income Account<span class="text-danger">*</span></label>
                            <select class="form-control savings_product_fees_selects select2able" name="income_account_id" id="savings_liability_account_id" data-bind='options: select2accounts([13,12]), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' required data-msg-required="Select an option" style="width: 100%;">
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="form-group col-lg-12">
                            <label class="col-form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control"  name="narrative" required placeholder="Description"></textarea>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <?php if ((in_array('1', $accounts_privilege)) || (in_array('3', $accounts_privilege))) { ?>
                            <button type="submit" class="btn btn-primary">
                                <?php if (isset($saveButton)) {
                                    echo $saveButton;
                                } else {
                                    echo "Save";
                                } ?></button>
                        <?php } ?>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>