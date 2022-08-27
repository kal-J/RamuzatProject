<!-- Deposit Product Fees -->
<div class="modal inmodal fade" id="add_deposit_product_fee-modal" tabindex="-1" role="dialog" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                            class="sr-only">Close</span></button>
                <h4 class="modal-title">Savings fee</h4>
                <small class="font-bold">Note: Required fields are marked with <span
                            class="text-danger">*</span></small>
            </div>
            <form class="formValidate" id="formSaving_fees" name="formSaving_fees" action="Saving_fees/create"
                  class="form-horizontal">
                <div class="modal-body ">
                    <div class="row">
                        <input type="hidden" name="tbl" value="tblDepositProductFee">
                        <input type="hidden" name="id" id="id">

                        <label class="col-lg-4 control-label">Fee Name</label>
                        <div class="col-lg-8 form-group">
                            <input type="text" name="feename" id="feename" placeholder="example: deposit fee"
                                   class="form-control" data-msg-required="Fee name  is required" required/>
                        </div>
                        <label class="col-lg-4 pull-right">Amount calculated as?<span
                                    class="text-danger">*</span></label>
                        <div class="col-lg-8 form-group">
                            <select class="form-control" id="cal_method_id" name="cal_method_id"
                                    data-bind="options: amountCalOptionsOther, optionsText: 'amountcalculatedas', optionsAfterRender: setOptionValue('amountcalculatedas_id'), optionsCaption: '--select--', value:$root.Amountcal">
                            </select>
                        </div>
                        <label class="col-lg-4 col-form-label">Fee type<span class="text-danger">*</span></label>
                        <div class="col-lg-8 form-group">
                            <center>
                                <label><input checked value="M" name="fee_type" type="radio" required> Mandatory</label>
                                <label> <input value="O" name="fee_type" type="radio" required> Optional</label>
                            </center>
                        </div>
                        <!-- ko with: Amountcal -->
                        <label class="col-lg-4 control-label"
                               data-bind="visible:  amountcalculatedas_id !=3,text: (amountcalculatedas_id==1)?'Percentage':'Amount'"> </label>
                        <div class="col-lg-8 form-group" data-bind="visible:  amountcalculatedas_id !=3">
                            <input type="number" name="amount" id="amount" class="form-control"
                                   data-msg-required="Amount is required" data-rule-min="0"
                                   data-bind='attr:{"data-rule-max": (amountcalculatedas_id==1)?100:null, "data-msg-min":"Percentage should be more than 0", "data-msg-max":"Percentage should be less than 100","placeholder":(amountcalculatedas_id==1)?"Example in percentage: 15":"example:10000"}'
                                   required/>

                        </div>
                        <div class="table-responsive" data-bind="visible:  amountcalculatedas_id ==3">

                            <table class="table table-striped table-condensed table-hover m-t-md">
                                <thead>
                                <tr>
                                    <th>Min</th>
                                    <th>Max</th>
                                    <th>Fee type</th>
                                    <th>Rate/Amount</th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody data-bind='foreach:$root.saving_range_fees'>
                                <tr>
                                    <td>
                                        <input type="text"
                                               data-bind='attr:{name:"rangeFees["+$index()+"][min_range]"},value:min_range'
                                               class="form-control"/>
                                        <input type="hidden"
                                               data-bind='attr:{name:"rangeFees["+$index()+"][id]"},value:id'
                                               class="form-control"/>
                                    </td>
                                    <td>
                                        <input type="text"
                                               data-bind='attr:{name:"rangeFees["+$index()+"][max_range]"},value:max_range'
                                               class="form-control"/>
                                    </td>
                                    <td>
                                        <select data-bind='options: $root.amountCalOptions, optionsText: function(item){return item.amountcalculatedas},attr:{name:"rangeFees["+$index()+"][calculatedas_id]"}, optionsAfterRender: setOptionValue("amountcalculatedas_id"), optionsCaption: "-- select --",value:calculatedas_id'
                                                class="form-control" style="width: 170px;"> </select>
                                    </td>
                                    <td>
                                        <input type="text"
                                               data-bind='attr:{name:"rangeFees["+$index()+"][range_amount]"},value:range_amount'
                                               class="form-control"/>
                                    </td>
                                    <td>
                                        <span title="Remove item" class="btn text-danger"
                                              data-bind='click: $root.removeRangeFee'><i class="fa fa-minus"></i></span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <button data-bind='click: $root.addRangeFee' class="btn-white btn-sm pull-right"><i
                                        class="fa fa-plus"></i> Add Another Range
                            </button>

                        </div>

                        <!--/ko-->
                    </div>

                    <div class="row">
                        <label class="col-lg-4 control-label">Trigger</label>
                        <div class="col-lg-8 form-group">
                            <select class="form-control" id="chargetrigger_id" name="chargetrigger_id"
                                    data-bind="options: chargeTriggerOptions, optionsText: 'charge_trigger_name', optionsAfterRender: setOptionValue('id'), optionsCaption: '--select--', value: chargeTrigger">
                            </select>
                        </div>
                        <!-- ko with: chargeTrigger -->
                        <label class="col-lg-4 control-label " data-bind='visible: id==2'>Frequency of Payment </label>
                        <div class="col-lg-4 form-group" data-bind='visible: id==2'>
                            <input placeholder="" required class="form-control" name="repayment_frequency"
                                   type="number" id="repayment_frequency">
                        </div>
                        <div class="col-lg-4 form-group" data-bind='visible: id==2'>
                            <select class="form-control" name="repayment_made_every" id="repayment_made_every"
                                    data-bind='options:$root.repayment_made_every_options, optionsText: "made_every_name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")'
                                    required data-msg-required="Select an option">
                            </select>
                        </div>

                        <label class="col-lg-4 control-label " data-bind='visible: id==2'>Date Application Method <sup
                                    data-toggle="tooltip"
                                    title="Method for calculating the day when the fee will be applied"
                                    data-placement="right"><i class="fa fa-question-circle"></i></sup></label>
                        <div class="col-lg-8 form-group" data-bind='visible: id==2'>
                            <select class="form-control" id="dateapplicationmethod_id" name="dateapplicationmethod_id"
                                    data-bind="options: $parent.dateApplicationMethodOptions(), optionsText: 'date_method',optionsAfterRender: setOptionValue('id'), optionsCaption: '--Select--', value:$parent.dateApplicationMtd()">
                            </select>
                        </div>
                        <!--/ko-->

                        <!-- <div class="col-lg-4 form-group">&nbsp;
                        </div>

                        <div class="col-lg-8 form-group">
                                <span class="pull-right col-lg-4 no-padding" id="taxables">
                                    <span class="pull-right">Taxable?<input name="taxable" id="taxable" type="checkbox"></span>
                                </span>
                                <span class="col-lg-4" class="hidetax row" id="tabbox" style="display:none;">
                                    Tax(%) <input  placeholder="0.0" class="form-control" name="tax" id="tax" type="number" max="100">
                                </span>
                        </div> -->
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <?php if ((in_array('1', $deposit_product_privilege)) || (in_array('3', $deposit_product_privilege))) { ?>
                            <button class="btn btn-sm btn-primary save" type="submit">Submit</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

