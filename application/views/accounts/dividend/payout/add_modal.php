<div class="modal inmodal fade" id="pay_dividend-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" class="formValidate" action="<?php echo site_url("dividend_declaration/pay_dividend"); ?>" id="formDividend_payment">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Dividends Payment</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id"/>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="total_dividends">Total Declared Dividends</label>
                        <div class="form-group col-lg-8">
                            <input type="number" id="total_dividends" name="total_dividends" class="form-control" value="" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="payment_date">Payment date<span class="text-danger">*</span></label>
                        <div class="form-group col-lg-8">
                            <div class="input-group date" data-date-start-date="<?php echo $fiscal_year['start_date2']; ?>">
                                <input type="text" id="payment_date" name="payment_date" class="form-control" value="" required><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Authorized By</label>
                        <div class="form-group col-lg-8">
                            <select class="form-control dividend_payment_selects m-b" name="authorizer_id" data-bind='options: authorizer_list,  optionsText: function(item){return item.salutation + " " + item.firstname + " " +item.lastname + " ";}, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                            </select>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Notes</label>
                        <div class="col-lg-8">
                            <textarea class="form-control" rows="2" name="payment_notes"></textarea>
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