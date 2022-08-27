<div class="modal inmodal fade" id="add_pending_approval-modal" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post" class="formValidate" action="<?php if (isset($case2) && $case2 == 'client_loan') {
                                                                    $controller = 'client_loan';
                                                                } else if ($type == 'group_loan') {
                                                                    $controller = 'group_loan';
                                                                } else {
                                                                    $controller = 'client_loan';
                                                                }
                                                                echo base_url($controller); ?>/Create" id="<?php if (isset($case2) && $case2 == 'client_loan') {
                                                                                                                $id = 'formClient_loan';
                                                                                                            } else if ($type == 'group_loan') {
                                                                                                                $id = 'formGroup_loan';
                                                                                                            } else {
                                                                                                                $id = 'formClient_loan';
                                                                                                            }
                                                                                                            echo $id; ?>">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modal_title)) {
                            echo $modal_title;
                        } else {
                            echo "New Loan";
                        }
                        ?> Application Form</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>

                <div class="modal-body">
                    <input name="id" id="id" type="hidden">
                    <input name="group_loan_id" id="group_loan_id" type="hidden">
                    <input type="hidden" name="state_id" value="1">

                    <?php if ($type == 'group_loan' || (isset($case2) && $case2 == 'group_loan')) : ?>
                        <fieldset class="col-lg-12">
                            <legend>Group Details</legend>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label">Group<span class="text-danger">*</span></label>
                                <div class="col-lg-4 form-group">
                                    <?php if (isset($case2) && $case2 == 'client_loan') : ?>
                                        <input type="hidden" name="group_loan_id" id="group_loan_id" value="<?php echo $group_loan_details['id']; ?>">
                                        <input type="text" class="form-control" value="<?php echo $group_loan_details['group_name']; ?>" disabled>

                                    <?php elseif (isset($group)) : ?>
                                        <label><input type="hidden" name="group_id" id="group_id" value="<?php echo $group['id']; ?>">
                                            <input type="text" value="<?php echo $group['group_name']; ?>" class="form-control" disabled>
                                        </label>
                                    <?php else : ?>
                                        <select class="form-control required" name="group_id" id="group_id" data-bind="enable:(parseInt($root.loan_detail().state_id) < 6)" style="width: 100%">
                                            <option value="">--select--</option>
                                            <?php
                                            foreach ($groups as $group) {
                                                echo "<option value='" . $group['id'] . "'>" . $group['group_name'] . "</option>";
                                            } ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                                <label class="col-lg-2 col-form-label">Loan Type<span class="text-danger">*</span></label>
                                <?php if (isset($case2) && $case2 == 'client_loan') : ?>
                                    <div class="col-lg-4 form-group">
                                        <span class="form-control"> <?php echo $group_loan_details['type_name']; ?></span>
                                    </div>
                                <?php else : ?>
                                    <div class="col-lg-4 form-group">
                                        <select class="form-control required" name="loan_type_id" data-bind="value:loan_type,enable:(parseInt($root.loan_detail().state_id) < 6)">
                                            <option value="">--select--</option>
                                            <?php
                                            foreach ($loan_type as $value) {
                                                echo "<option value='" . $value['id'] . "'>" . $value['type_name'] . "</option>";
                                            } ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </fieldset>
                    <?php endif; ?>

                    <fieldset class="col-lg-12">
                        <legend>Application Details</legend>

                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Loan No<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <input class="form-control" required name="loan_no" type="text" data-bind="attr: {value:loan_ref_no(), readonly:loan_ref_no()!==false?'readonly':''}" />
                            </div>
                            <?php if ((isset($case2) && $case2 != 'group_loan') && ($type == 'client_loan' || (isset($case2) && $case2 == 'client_loan'))) : ?>
                                <label class="col-lg-2 col-form-label">Client<span class="text-danger">*</span></label>
                                <div class="col-lg-4 form-group">
                                    <select class="form-control" id="member_id" name="member_id" data-bind='options: member_names, optionsText: function(item){ return item.member_name+"-"+item.client_no;}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: member_name,enable:(parseInt($root.loan_detail().state_id) < 6)' required data-msg-required="Member is required" style="width: 100%">
                                    </select>
                                </div>
                            <?php elseif (isset($memberloan) && $memberloan == 'member_loan') : ?>
                                <label class="col-lg-2 col-form-label">Client<span class="text-danger">*</span></label>
                                <div class="col-lg-4 form-group">
                                    <input type="hidden" name="member_id" value="<?php echo (isset($user['id']) ? $user['id'] : ''); ?>">
                                    <input type="text" class="form-control" readonly value="<?php echo (isset($user['firstname']) ? $user['firstname'] . ' ' . $user['lastname'] . ' ' . $user['othernames'] . '- ' . $user['client_no'] : ''); ?>">
                                </div>
                            <?php endif; ?>
                            <label class="col-lg-2 col-form-label">Application date<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group" data-bind="visible:(parseInt($root.loan_detail().state_id) < 6)">
                                <div class="input-group date" data-date-start-date="<?php echo isset($active_month) ? date('d-m-Y', strtotime($active_month['month_start'])) : date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month) ? ((strtotime(date('d-m-Y')) < (strtotime($active_month['month_end']))) ? date('d-m-Y') : date('d-m-Y', strtotime($active_month['month_end']))) : ((strtotime(date('d-m-Y')) < (strtotime($fiscal_active['end_date']))) ? date('d-m-Y') : date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>">
                                    <input class="form-control" onkeydown="return false" autocomplete="off" required name="action_date" data-bind="datepicker: $root.application_date,attr:{value:$root.application_date,readonly:(parseInt($root.loan_detail().state_id) < 6)?'readonly':''}" type="text"><span data-bind="datepicker: $root.application_date" class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Loan Product<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <select class="form-control" id="loan_product_id" name="loan_product_id" data-bind='options: product_names, optionsText: "product_name", optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: product_name,enable:(parseInt($root.loan_detail().state_id) < 6)' required data-msg-required="Loan Product is required" style="width: 100%">
                                </select>
                            </div>
                            <label class="col-lg-2 col-form-label">Credit officer<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <select required class="form-control" name="credit_officer_id" id="credit_officer_id" data-bind="enable:(parseInt($root.loan_detail().state_id) < 6)" style="width: 100%">
                                    <option value="">--select--</option>
                                    <?php
                                    foreach ($staffs as $staff) {
                                        echo "<option value='" . $staff['id'] . "'>" . $staff['salutation'] . ' ' . $staff['firstname'] . ' ' . $staff['lastname'] . ' ' . $staff['othernames'] . '-' . $staff['staff_no'] . "</option>";
                                    }
                                    ?>
                                </select>

                            </div>
                        </div>
                        <!--/row -->
                    </fieldset>
                    <style type="text/css">
                        .blueText {
                            color: blue;
                            font-size: 10px;
                        }
                    </style>
                    <fieldset class="col-lg-12">
                        <legend>Preferred Payment Option</legend>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Payment Option<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <select class="form-control" name="preferred_payment_id" data-bind='options: payment_modes, optionsText: "payment_mode", optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: payment_mode' required data-msg-required="Payment mode is required" style="width: 100%">
                                </select>
                            </div>
                        </div>
                        <!-- ko with: payment_mode -->
                        <!-- ko if: parseInt(id)===2 -->
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">A/C Name<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <input class="form-control" name="ac_name" type="text" required />
                            </div>
                            <label class="col-lg-2 col-form-label">A/C Number<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <input class="form-control" name="ac_number" type="text" required />
                            </div>
                            <label class="col-lg-2 col-form-label">Bank Branch<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <input class="form-control" name="bank_branch" type="text" required />
                            </div>
                            <label class="col-lg-2 col-form-label">Bank Name<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <input class="form-control" name="bank_name" type="text" required />
                            </div>
                        </div>
                        <!-- /ko -->
                        <!-- ko if: parseInt(id)===4 -->
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Phone Number<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <input class="form-control" name="phone_number" type="text" required />
                            </div>
                        </div>
                        <!-- /ko -->
                        <!-- /ko -->
                    </fieldset>
                    <!-- ko with: product_name -->

                    <fieldset class="col-lg-12">
                        <legend>Loan Amount details</legend>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Total Requested amount<span class="text-danger">*</span></label>
                            <?php if (isset($case2) && $case2 == 'client_loan' && $type == 'group_loan') : ?>
                                <div class="col-lg-3 form-group">
                                    <input required class="form-control" name="requested_amount" type="number" data-bind='textInput: (parseFloat($root.group_loan_details().borrowed_amount)>0) ? (parseFloat($root.group_loan_details().requested_amount)-parseFloat($root.group_loan_details().borrowed_amount)) : parseFloat($root.group_loan_details().requested_amount), 
                                        attr: {"data-rule-min":1, 
                                        "data-rule-max": (parseFloat($root.group_loan_details().borrowed_amount)>0)? (parseFloat($root.group_loan_details().requested_amount) - parseFloat($root.group_loan_details().borrowed_amount)) : parseFloat($root.group_loan_details().requested_amount), "data-msg-min":"Loan amount is less than "+curr_format(parseInt(0)), "data-msg-max":"Loan amount is more than "+curr_format(parseInt($root.group_loan_details().borrowed_amount)),readonly:(parseInt($root.loan_detail().state_id) < 6)?"readonly":""}' required />
                                    <div class="blueText">
                                        <p>
                                            <span data-bind="visible: (parseFloat($root.group_loan_details().requested_amount)>0)">Min: </span>
                                            <span data-bind="visible: (parseFloat($root.group_loan_details().requested_amount)>0), text: curr_format(parseInt(0))"></span>&nbsp;
                                            <span data-bind='visible: (parseFloat($root.group_loan_details().requested_amount)>0)'>Max: </span>
                                            <span data-bind="visible: (parseFloat($root.group_loan_details().requested_amount)>0), text: ((parseFloat($root.group_loan_details().borrowed_amount)>0)? ( parseFloat($root.group_loan_details().requested_amount) - parseFloat($root.group_loan_details().borrowed_amount)) :parseFloat($root.group_loan_details().requested_amount))"></span>
                                        </p>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="col-lg-3 form-group">
                                    <input required class="form-control" name="requested_amount" type="number" data-bind='textInput: def_amount, attr: {"data-rule-min":((parseFloat(min_amount)>0)?min_amount:null), "data-rule-max": ((parseFloat(max_amount)>0)?max_amount:null), "data-msg-min":"Loan amount is less than "+curr_format(parseInt(min_amount)), "data-msg-max":"Loan amount is more than "+curr_format(parseInt(max_amount)),readonly:(parseInt($root.loan_detail().state_id) < 6)?"readonly":""}' required />
                                    <div class="blueText">
                                        <p>
                                            <span data-bind="visible: (parseFloat(min_amount)>0)">Min: </span>
                                            <span data-bind="visible: (parseFloat(min_amount)>0), text: curr_format(parseInt(min_amount))"></span>&nbsp;
                                            <span data-bind='visible: (parseFloat(max_amount)>0)'>Max: </span>
                                            <span data-bind="visible: (parseFloat(max_amount)>0), text: curr_format(parseInt(max_amount))"></span>
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <!--ko if: typeof $parent.loan_type()!=='undefined'&&parseInt($parent.loan_type())===1-->
                            <label class="col-lg-3 col-form-label">Interest rate<span class="text-danger">*</span><br><small><em>All rates are in per annum</em></small></label>
                            <div class="col-lg-3 form-group">
                                <input min="0" step="0.01" required class="form-control" name="interest_rate" type="number" data-bind='textInput: def_interest, attr: {"data-rule-min":((parseFloat(min_interest)>0)?min_interest:null), "data-rule-max": ((parseFloat(max_interest)>0)?max_interest:null), "data-msg-min":"Interest rate is less than "+parseFloat(min_interest), "data-msg-max":"Interest rate is more than "+parseFloat(max_interest)},enable:(parseInt($root.loan_detail().state_id) < 6)' required />
                                <div class="blueText">
                                    <p>
                                        <span data-bind="visible: (parseFloat(min_interest)>0)">Min: </span>
                                        <span data-bind="visible: (parseFloat(min_interest)>0), text: parseFloat(min_interest)"></span>&nbsp;
                                        <span data-bind='visible: (parseFloat(max_interest)>0)'>Max: </span>
                                        <span data-bind="visible: (parseFloat(max_interest)>0), text: parseFloat(max_interest)"></span>
                                    </p>
                                </div>
                            </div>
                            <!--/ko-->
                        </div>
                        <!--/row -->
                        <!--ko if: typeof $parent.loan_type()!=='undefined'&&parseInt($parent.loan_type())===1-->
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Grace period<span class="text-danger">*</span></label>
                            <div class="col-lg-2 form-group">
                                <input required class="form-control" name="grace_period" type="number" data-bind='textInput: def_grace_period, attr: {"data-rule-min":((parseFloat(min_grace_period)>0)?min_grace_period:null), "data-rule-max": ((parseFloat(max_grace_period)>0)?max_grace_period:null), "data-msg-min":"Grace period is less than "+parseInt(min_grace_period), "data-msg-max":"Grace period is more than "+parseInt(max_grace_period)},enable:(parseInt($root.loan_detail().state_id) < 6)' required />
                                <div class="blueText">
                                    <p>
                                        <span data-bind="visible: (parseFloat(min_grace_period)>0)">Min: </span>
                                        <span data-bind="visible: (parseFloat(min_grace_period)>0), text: parseInt(min_grace_period)"></span>&nbsp;
                                        <span data-bind='visible: (parseFloat(max_grace_period)>0)'>Max: </span>
                                        <span data-bind="visible: (parseFloat(max_grace_period)>0), text: parseInt(max_grace_period)"></span>
                                    </p>
                                </div>
                            </div>
                            <label class="col-lg-2 col-form-label">Offset period<span class="text-danger">*</span></label>
                            <div class="col-lg-2 form-group">
                                <input required class="form-control" name="offset_period" type="number" data-bind='textInput: def_offset, attr: {"data-rule-min":((parseFloat(min_offset)>0)?min_offset:null), "data-rule-max": ((parseFloat(max_offset)>0)?max_offset:null), "data-msg-min":"Offset period is less than "+parseInt(min_offset), "data-msg-max":"Offset period is more than "+parseInt(max_offset)},enable:(parseInt($root.loan_detail().state_id) < 6)' required />
                                <div class="blueText">
                                    <p>
                                        <span data-bind="visible: (parseFloat(min_offset)>0)">Min: </span>
                                        <span data-bind="visible: (parseFloat(min_offset)>0), text: parseInt(min_offset)"></span>&nbsp;
                                        <span data-bind='visible: (parseFloat(max_offset)>0)'>Max: </span>
                                        <span data-bind="visible: (parseFloat(max_offset)>0), text: parseInt(max_offset)"></span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-3">

                                <select class="form-control" name="offset_made_every" data-bind='options: $root.repayment_made_every_detail, optionsText: "made_every_name", optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"),optionsValue:"id", value: $root.product_name().offset_made_every,enable:(parseInt($root.loan_detail().state_id) < 6)' required data-msg-required="This field is required">
                                </select>
                            </div>
                        </div>
                        <!--/row -->
                        <span class="text-danger"><small>The loan term should not exceed <span data-bind="text: $parent.loan_product_length" class="blueText"></span> which is the maximum loan period for this loan product</small></span>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">No of Installments<span class="text-danger">*</span></label>
                            <div class="col-lg-2">
                                <input type="number" id="installment" name="installments" class="form-control" data-bind='textInput: def_repayment_installments, attr: {"data-rule-min":((parseFloat(min_repayment_installments)>0)?min_repayment_installments:null), "data-rule-max": ((parseFloat(max_repayment_installments)>0)?max_repayment_installments:null), "data-msg-min":"Installment is less than "+parseInt(min_repayment_installments), "data-msg-max":"Installment is more than "+parseInt(max_repayment_installments)},enable:(parseInt($root.loan_detail().state_id) < 6)' data-rule-mustbelessthanProductMaxLoanPeriod required />
                                <div class="blueText">
                                    <p>
                                        <span data-bind="visible: (parseFloat(min_repayment_installments)>0)">Min: </span>
                                        <span data-bind="visible: (parseFloat(min_repayment_installments)>0), text: parseInt(min_repayment_installments)"></span>&nbsp;
                                        <span data-bind='visible: (parseFloat(max_repayment_installments)>0)'>Max: </span>
                                        <span data-bind="visible: (parseFloat(max_repayment_installments)>0), text: parseInt(max_repayment_installments)"></span>
                                    </p>
                                </div>
                            </div>
                            <label class="col-lg-2 col-form-label">Paid every after<span class="text-danger">*</span></label>
                            <div class="col-lg-2">
                                <input class="form-control" type="number" id="paid_every" min="0" step="1" name="repayment_frequency" data-rule-mustbelessthanProductMaxLoanPeriod data-bind="value:repayment_frequency,enable:(parseInt($root.loan_detail().state_id) < 6)" required>
                            </div>
                            <div class="col-lg-3">
                                <select data-rule-mustbelessthanProductMaxLoanPeriod class="form-control" name="repayment_made_every" id="period_id" data-bind='options: $root.repayment_made_every_detail, optionsText: "made_every_name", optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"),optionsValue:"id", value: $root.product_name().repayment_made_every,enable:(parseInt($root.loan_detail().state_id) < 6)' required data-msg-required="This field is required">
                                </select>
                            </div>
                        </div>
                        <!--/row -->
                        <div class="form-group row">
                            <!--ko if: penalty_applicable==1 && parseInt(penalty_calculation_method_id) === 1 -->
                            <label class="col-lg-3 col-form-label">Penalty rate<span class="text-danger">*</span></label>
                            <div class="col-lg-3 form-group">
                                <input min="0" step="0.01" required class="form-control" name="penalty_rate" type="number" data-bind='textInput: def_penalty_rate, attr: {"data-rule-min":((parseFloat(min_penalty_rate)>0)?min_penalty_rate:null), "data-rule-max": ((parseFloat(max_penalty_rate)>0)?max_penalty_rate:null), "data-msg-min":"Penalty rate is less than "+parseFloat(min_penalty_rate), "data-msg-max":"Penalty rate is more than "+parseFloat(max_penalty_rate)},enable:(parseInt($root.loan_detail().state_id) < 6)' required />
                                <div class="blueText">
                                    <p>
                                        <span data-bind="visible: (parseFloat(min_penalty_rate)>0)">Min: </span>
                                        <span data-bind="visible: (parseFloat(min_penalty_rate)>0), text: parseFloat(min_penalty_rate)"></span>&nbsp;
                                        <span data-bind='visible: (parseFloat(max_penalty_rate)>0)'>Max: </span>
                                        <span data-bind="visible: (parseFloat(max_penalty_rate)>0), text: parseFloat(max_penalty_rate)"></span>
                                    </p>
                                </div>
                            </div>
                            <!--/ko-->
                            <!--ko if: penalty_applicable==1 && parseInt(penalty_calculation_method_id) === 2 -->
                            <label class="col-lg-3 col-form-label">Penalty rate<span class="text-danger">*</span></label>
                            <div class="col-lg-3 form-group">
                                <input min="0" required class="form-control" name="penalty_rate" type="number" data-bind='value: $root.product_name().fixed_penalty_amount' required />

                            </div>
                            <!--/ko-->
                            <input data-bind="value: link_toDeposit_account" name="link_to_deposit_account" id="link_to_deposit_account" type="hidden">
                            <!--ok if: link_toDeposit_account==1-->
                            <!-- <label class="col-lg-3 col-form-label">Link to deposit account?<span class="text-danger"></span></label>
                            <div class="col-lg-3 form-group">
                                <label data-bind="text: link_toDeposit_account ==='1' ? 'Yes' : 'No'" class="col-form-label" name="link_to_deposit_account" id="link_to_deposit_account"></label>
                                <input data-bind="value: link_toDeposit_account" name="link_to_deposit_account" id="link_to_deposit_account" type="hidden">
                            </div> -->
                            <!--/ok-->
                        </div>
                        <!--/row -->
                        <div class="form-group row" data-bind="if: penalty_applicable==1">
                            <label class="col-lg-4 col-form-label">Penalty calculation method<span class="text-danger">*</span></label>
                            <label class="col-lg-8 col-form-label" data-bind="text: method_description"></label>
                            <input data-bind="value: penalty_calculation_method_id" name="penalty_calculation_method_id" id="penalty_calculation_method_id" type="hidden">
                            <div class="clearfix"></div>
                            <label class="col-lg-4 col-form-label">Penalty rate charged<span class="text-danger">*</span></label>
                            <input data-bind="value: penalty_rate_chargedPer" name="penalty_rate_charged_per" id="penalty_rate_charged_per" type="hidden"></label><label class="col-lg-8 col-form-label" data-bind="text: (penalty_rate_chargedPer==1)?'Daily':((penalty_rate_chargedPer==2)?'Weekly':((penalty_rate_chargedPer==3)?'Monthly':'None'))"></label>
                            <div class="clearfix"></div>
                        </div>
                        <!--/row -->
                        <!--/ko-->
                    </fieldset>
                    <!-- /ko -->
                    <hr />
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Loan Purpose<span class="text-danger">*</span></label>
                        <div class="col-lg-4 form-group">
                            <textarea class="form-control" data-bind="enable:(parseInt($root.loan_detail().state_id) < 6)" rows="3" required name="loan_purpose"></textarea>
                        </div>
                        <label class="col-lg-2 col-form-label">Comment</label>
                        <div class="col-lg-4 form-group">
                            <textarea class="form-control" rows="3" name="comment"></textarea>
                        </div>
                    </div>
                    <!--/row -->
                </div>
                <div class="modal-footer">
                    <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                        <i class="fa fa-check"></i> <?php
                                                    if (isset($saveButton)) {
                                                        echo $saveButton;
                                                    } else {
                                                        echo "Save";
                                                    }
                                                    ?></button>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>