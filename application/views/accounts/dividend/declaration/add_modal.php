<div class="modal inmodal fade" id="add_dividend_declaration-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="post" class="formValidate" action="<?php echo site_url(); ?>dividend_declaration/create"
              id="formDividend_declaration">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                                class="sr-only">Close</span></button>
                    <h4 class="modal-title">Dividend Declaration Form</h4>
                    <small class="font-bold">Note: Required fields are marked with <span
                                class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id"/>
                    <div class="form-group row">
                            <label class="col-sm-1 col-form-label">Fiscal Year<span class="text-danger">*</span></label>
                            <div class="col-md-4">
                                <select class="form-control" id="fiscal_year_id" name="fiscal_year_id"
                                        data-bind='options:fiscal_years, optionsText:function(data_item){return data_item.start_date +" - " + data_item.end_date;}, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:start_date, optionsDisableDefault: true'
                                        data-msg-required="Fiscal Year must be selected" style="width: 100%" required>
                                </select>

                            </div>

                            <label class="col-sm-2 col-form-label">Issuance/ Category<span
                                        class="text-danger">*</span></label>
                            <div class="col-md-4">
                                <select class="form-control" name="share_issuance_id" id="share_issuance_id"
                                        data-bind="options: share_issuance, optionsText: 'issuance_name', optionsAfterRender: setOptionValue('id'), optionsCaption: '--select--',value: issuance"
                                        data-msg-required="Issuance/Category must be selected" style="width: 100%" required>
                                </select>
                            </div>
                    </div>
                    <div class="form-group row">

                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="col-form-label" for="dividend_declaration_date">Declaration Date<span
                                        class="text-danger">*</span></label>
                            <div class="input-group date"
                                 data-date-start-date="<?php echo $fiscal_year['start_date2']; ?>"
                                 data-date-end-date="+0d">
                                <input type="text" id="dividend_declaration_date" name="declaration_date"
                                       class="form-control" value="<?php echo date('d-m-Y') ?>" required>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="col-form-label" for="cash_stock">Cash or Stock? <span
                                        class="text-danger">*</span></label>
                            <div>
                                <label><input type="radio" name="cash_stock" class="radio-inline" checked="checked"
                                              value="1"/> Cash</label>
                                <label><input type="radio" name="cash_stock" class="radio-inline" value="2"/>
                                    Stock</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="col-form-label" for="dividend_record_date">Date of Record<span
                                        class="text-danger">*</span> <small>Date by which a shareholder must own stock
                                    in order to qualify for the dividend</small></label>
                            <div class="input-group date"
                                 data-date-start-date="<?php echo $fiscal_year['start_date2']; ?>"
                                 data-date-end-date="+0d">
                                <input type="text" id="dividend_record_date" name="record_date" class="form-control"
                                       data-bind="textInput:dividend_record_date" required>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="col-form-label" for="dividend_payment_date">Date of Payment<span
                                        class="text-danger">*</span></label>
                            <div class="input-group date" data-date-start-date="+0d">
                                <input type="text" id="dividend_payment_date" name="payment_date" class="form-control"
                                       required>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="col-form-label" for="paying_preference_sh"><input type="checkbox"
                                                                                            name="paying_preference_sh"
                                                                                            id="paying_preference_sh"
                                                                                            class="radio-inline"
                                                                                            checked="checked"
                                                                                            value="1"/> Paying
                                Cumulative Preference Shareholders</label>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="col-form-label" for="paying_ordinary_sh"><input type="checkbox"
                                                                                          name="paying_ordinary_sh"
                                                                                          id="paying_ordinary_sh"
                                                                                          class="radio-inline"
                                                                                          checked="checked" value="1"/>
                                Paying Ordinary Shareholders</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4" id="select22">
                            <label class="col-form-label" for="retained_earnings_acc_id">Retained Earnings A/C</label>
                            <select class=" form-control dividend_selects" id="retained_earnings_acc_id"
                                    name="retained_earnings_acc_id"
                                    data-bind='options: select2accounts(19), optionsText: formatAccount2, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")'
                                    style="width: 100%" required>
                                <option value="">--select--</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <label class="col-form-label" for="dividends_payable_acc_id">Dividends Payable A/C</label>
                            <select class=" form-control dividend_selects" id="dividends_payable_acc_id"
                                    name="dividends_payable_acc_id"
                                    data-bind='options: select2accounts(8), optionsText: formatAccount2, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")'
                                    style="width: 100%" required>
                                <option value="">--select--</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <label class="col-form-label" for="dividends_cash_acc_id">Cash A/C</label>
                            <select class=" form-control dividend_selects" id="dividends_cash_acc_id"
                                    name="dividends_cash_acc_id"
                                    data-bind='options: select2accounts([3,4,5]), optionsText: formatAccount2, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")'
                                    style="width: 100%" required>
                                <option value="">--select--</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="col-form-label" for="total_dividends">Total Dividends Declared <small>(Profit/Loss:
                                    <span data-bind="text:curr_format(Math.trunc(profit_loss()))"></span>)</small></label>
                            <input type="number" name="total_dividends" id="total_dividends" class="form-control"
                                   min="0" data-bind="textInput: total_computed_share"/>
                        </div>
                        <div class="form-group col-lg-6">
                            <input type="hidden" name="no_share" data-bind="value:curr_format(no_shares())">
                            <label class="col-form-label" for="dividend_per_share">Dividend Per Share <small>(Total
                                    Shares: <span data-bind="text:curr_format(no_shares())"></span>)</small></label>
                            <input type="number" name="dividend_per_share" id="dividend_per_share" class="form-control"
                                   min="0"
                                   data-bind="textInput: dividend_per_share,attr: {'data-rule-max':round(profit_loss()/no_shares(),2),max:round(profit_loss()/no_shares(),2), value:Math.trunc((total_computed_share()/no_shares()),2)}"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-form-label">Notes</label>
                            <textarea class="form-control" rows="2" name="notes"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-form-label">Attachment</label>
                            <input type="file" name="file_attachment" class="form-control"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <?php if ((in_array('1', $accounts_privilege)) || (in_array('3', $accounts_privilege))) { ?>
                        <button type="submit" class="btn btn-primary">Save</button>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>
</div>