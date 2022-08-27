 <input name="group_loan_id" id="group_loan_id" type="hidden">
 <fieldset class="col-lg-12">
     <legend>Application Details</legend>

     <div class="form-group row">
         <?php if ((isset($case2) && $case2 != 'group_loan') && ($type == 'client_loan' || (isset($case2) && $case2 == 'client_loan'))) : ?>
             <label class="col-lg-2 col-form-label">Client<span class="text-danger">*</span></label>
             <div class="col-lg-2 form-group">
                 <select class="form-control" id="member_id1" name="member_id" data-bind='options: member_names, optionsText: function(item){ return item.member_name+"-"+item.client_no;}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: member_name' required data-msg-required="Member is required" style="width: 100%">
                 </select>
             </div>
         <?php elseif (isset($memberloan) && $memberloan == 'member_loan') : ?>
             <label class="col-lg-2 col-form-label">Client<span class="text-danger">*</span></label>
             <div class="col-lg-2 form-group">
                 <input id="member_id_2" type="hidden" name="member_id" value="<?php echo (isset($user['id']) ? $user['id'] : ''); ?>">
                 <input type="text" class="form-control" readonly value="<?php echo (isset($user['firstname']) ? $user['firstname'] . ' ' . $user['lastname'] . ' ' . $user['othernames'] . '- ' . $user['client_no'] : ''); ?>">
             </div>
         <?php endif; ?>

         <label class="col-lg-2 col-form-label">Loan No<span class="text-danger">*</span></label>
         <div class="col-lg-2 form-group">
             <input class="form-control" required name="loan_no" type="text" data-bind="attr: {value:loan_ref_no, readonly:loan_ref_no()!==false?'readonly':''}" />
         </div>
         <label class="col-lg-2 col-form-label">Application date<span class="text-danger">*</span></label>
         <div class="col-lg-2 form-group">
             <input class="form-control" readonly value="<?php echo date('d-m-Y'); ?>" required name="application_date" type="text">
         </div>
     </div>
     <div class="form-group row">
         <label class="col-lg-2 col-form-label">Loan Product<span class="text-danger">*</span></label>
         <div class="col-lg-4 form-group">
             <select class="form-control" id="loan_product_id1" name="loan_product_id" data-bind='options: product_names, optionsText: "product_name", optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: product_name' required data-msg-required="Loan Product is required" style="width: 100%">
             </select>
         </div>

         <!--ko if: $root.qualification_check() && $root.requestable_amounts() && (($root.loan_type2() == 'client_loan') || ($root.loan_type2() == 'My Loans')) -->
         <div class="col-lg-12 mb-3 ml-2">
             <span class="text-success text-large">Maximum amount that can be requested is &nbsp; <span data-bind="text: $root.requestable_amounts() ? curr_format($root.requestable_amounts().max) : 0"></span></span>
         </div>
         <!--/ko -->

         <!--ko ifnot: $root.qualification_check() -->
         <!--ko if: (($root.loan_type2() == 'client_loan') || ($root.loan_type2() == 'My Loans')) && $root.requestable_amounts() -->
         <div class="form-group row m-1">
             <div class="text-danger text-large">You do not qualify for this Loan </div>
             <div class="col-lg-12 text-danger">
                 Total Savings : <span data-bind="text: $root.requestable_amounts() ? curr_format($root.requestable_amounts().savings_total) : 0"></span>
             </div>
             <div class="col-lg-12 text-danger">
                 Total Shares : <span data-bind="text: $root.requestable_amounts() ? curr_format($root.requestable_amounts().shares_total) : 0"></span>
             </div>
             <div class="col-lg-12 text-danger">
                 Minimum Collateral required : UGX. <strong data-bind="text: $root.requestable_amounts() ? curr_format($root.requestable_amounts().min) : 0"></strong>
             </div>
         </div>
         <!--/ko -->
         <!--/ko -->

         <!--ko if: $root.qualification_check() || $root.loan_type2() == 'group_loan' -->

         <!-- ko with: product_name -->
         <label class="col-lg-3 col-form-label">Requested amount<span class="text-danger">*</span> </label>
         <div class="col-lg-3 form-group">
             <input required min="0" class="form-control" name="requested_amount" type="number" data-bind='value: $root.app_amount, attr: {"data-rule-min":((parseFloat(min_amount)>0)?min_amount:null), "data-rule-max": parseFloat($root.requestable_amounts().max), "data-msg-min":"Loan amount is less than "+curr_format(parseInt(min_amount)), "data-msg-max":"Loan amount is more than "+curr_format(parseInt($root.requestable_amounts().max))}' required />
             <div class="blueText">
                 <p>
                     <span data-bind="visible: (parseFloat(min_amount)>0)">Min: </span>
                     <span data-bind="visible: (parseFloat(min_amount)>0), text: curr_format(parseInt(min_amount))"></span>&nbsp;
                     <span data-bind='visible: (parseFloat(max_amount)>0)'>Max: </span>
                     <span data-bind="visible: (parseFloat(max_amount)>0), text: curr_format(parseInt($root.requestable_amounts().max))"></span>
                 </p>
             </div>
         </div>
         <!-- /ko-->

         <!--/ko -->


     </div>
     <!--/row -->

 </fieldset class="col-lg-12">
 <style type="text/css">
     .blueText {
         color: blue;
         font-size: 10px;
     }
 </style>

 <!--ko if: $root.qualification_check() || $root.loan_type2() == 'group_loan' -->

 <!-- ko with: product_name -->
 <fieldset class="col-lg-12">
     <legend>Loan Repayment details</legend>
     <div class="form-group row">
         <!--ko if: typeof $parent.loan_type()!=='undefined'&&parseInt($parent.loan_type())===1-->

         <div class="col-lg-3 form-group">
             <input min="0" required class="form-control" name="interest_rate" type="number" hidden data-bind='value: $root.app_interest, attr: {"data-rule-min":((parseFloat(min_interest)>0)?min_interest:null), "data-rule-max": ((parseFloat(max_interest)>0)?max_interest:null), "data-msg-min":"Interest rate is less than "+parseFloat(min_interest), "data-msg-max":"Interest rate is more than "+parseFloat(max_interest)}' required />
         </div>
         <!--/ko-->
     </div>
     <!--/row -->
     <!--ko if: typeof $parent.loan_type()!=='undefined'&&parseInt($parent.loan_type())===1-->

     <span class="text-danger"><small>The loan term should not exceed <span data-bind="text: $parent.loan_product_length" class="blueText"></span> which is the maximum loan period for this loan product</small></span>
     <div class="form-group row">
         <label class="col-lg-3 col-form-label">No of Installments<span class="text-danger">*</span></label>
         <div class="col-lg-2">
             <input type="number" min="1" id="installment" name="installments" class="form-control" data-bind='value: $root.app_installments, attr: {"data-rule-min":((parseFloat(min_repayment_installments)>0)?min_repayment_installments:null), "data-rule-max": ((parseFloat(max_repayment_installments)>0)?max_repayment_installments:null), "data-msg-min":"Installment is less than "+parseInt(min_repayment_installments), "data-msg-max":"Installment is more than "+parseInt(max_repayment_installments)}' data-rule-mustbelessthanProductMaxLoanPeriod required />
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
             <input class="form-control" type="number" id="paid_every" min="1" step="1" name="repayment_frequency" data-rule-mustbelessthanProductMaxLoanPeriod data-bind="value: $root.app_repayment_frequency" required>
         </div>
         <div class="col-lg-3">
             <select data-rule-mustbelessthanProductMaxLoanPeriod class="form-control" name="repayment_made_every" id="period_id" data-bind='options: $root.repayment_made_every_detail, optionsText: "made_every_name", optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"),optionsValue:"id", value: $root.app_repayment_made_every' required data-msg-required="This field is required">
             </select>
         </div>
     </div>
     <!--/row -->
     <div class="form-group row">
         <div class="col-lg-2 form-group">
             <input required min="0" class="form-control" name="grace_period" type="number" hidden data-bind='textInput: def_grace_period, attr: {"data-rule-min":((parseFloat(min_grace_period)>0)?min_grace_period:null), "data-rule-max": ((parseFloat(max_grace_period)>0)?max_grace_period:null), "data-msg-min":"Grace period is less than "+parseInt(min_grace_period), "data-msg-max":"Grace period is more than "+parseInt(max_grace_period)}' required />
         </div>
         <div class="col-lg-2 form-group">
             <input required min="0" class="form-control" name="offset_period" type="number" hidden data-bind='value: $root.app_offset_period, attr: {"data-rule-min":((parseFloat(min_offset)>0)?min_offset:null), "data-rule-max": ((parseFloat(max_offset)>0)?max_offset:null), "data-msg-min":"Offset period is less than "+parseInt(min_offset), "data-msg-max":"Offset period is more than "+parseInt(max_offset)}' required />
         </div>
         <div class="col-lg-3">
             <input type="hidden" name="offset_made_every" data-bind="attr:{value: $root.app_offset_every}">
         </div>
     </div>
     <!--/row -->
     <div class="form-group row">
         <!--ko if: penalty_applicable==1-->
         <div class="col-lg-3 form-group">
             <input min="0" readonly required class="form-control" name="penalty_rate" type="number" hidden data-bind='value: $root.app_penalty_rate, attr: {"data-rule-min":((parseFloat(min_penalty_rate)>0)?min_penalty_rate:null), "data-rule-max": ((parseFloat(max_penalty_rate)>0)?max_penalty_rate:null), "data-msg-min":"Penalty rate is less than "+parseFloat(min_penalty_rate), "data-msg-max":"Penalty rate is more than "+parseFloat(max_penalty_rate)}' required />
         </div>
         <!--/ko-->
         <input data-bind="value: link_toDeposit_account" name="link_to_deposit_account" id="link_to_deposit_account" type="hidden">
     </div>
     <!--/row -->
     <div class="form-group row" data-bind="if: penalty_applicable==1">
         <input data-bind="value: penalty_calculation_method_id" name="penalty_calculation_method_id" id="penalty_calculation_method_id" type="hidden">
         <div class="clearfix"></div>
         <input data-bind="value: penalty_rate_chargedPer" name="penalty_rate_charged_per" id="penalty_rate_charged_per" type="hidden">
         <div class="clearfix"></div>
     </div>
     <!--/row -->
     <!--/ko-->
 </fieldset>
 <!-- /ko -->

 <!--/ko -->