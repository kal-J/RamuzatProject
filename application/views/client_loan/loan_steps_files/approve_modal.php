<div class="modal inmodal fade" id="approve-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form method="post" class="formValidate" action="<?php echo base_url();?>client_loan/approve" id="formApprove">

<div class="modal-header">
     <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
     <h4 class="modal-title">
        <?php
        if (isset($modalTitle1)) {
            echo $modalTitle;
        }else{
            echo "Approving Loan Application--";
        }
     ?></h4>
     <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
    </div>
        <!-- ko with: loan_details -->
          <input type="hidden" name="client_loan_id" data-bind="attr:{value: id}" id="client_loan_id">
          <input type="hidden" name="group_loan_id" data-bind="attr:{value: (typeof group_loan_id !='undefined')?group_loan_id:''}" id="group_loan_id">
          <input type="hidden" name="requested_amount" data-bind="attr:{value: requested_amount}">
        <!-- /ko -->

        <!-- ko with: approval_data -->
        <div class="modal-body"><!-- Start of the modal body -->
                <input type="hidden" name="state_id" value="6">
                <div class="form-group row"><!-- start of approval note row -->
                    <div class="col-lg-1"></div>
                        <label class="col-lg-3 col-form-label">Requested Amount is</label>
                        <div class="col-lg-6 form-group">
                        <span placeholder="" class="form-control" type="readonly" data-bind="text: 'UGX '+curr_format(($root.loan_details().requested_amount)*1)"></span>
                        </div>
                    </div>
                <div class="alert"  data-bind=" css:{'alert-default':(parseInt($root.loan_details().min_collateral)<=(((guarantor_amount_locked_sum+collateral_sum)/(parseInt($root.loan_details().requested_amount)))*100)), 'alert-default':(parseInt($root.loan_details().min_collateral)>(((guarantor_amount_locked_sum+collateral_sum)/(parseInt($root.loan_details().requested_amount)))*100))} ">
                  <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Security</th>
                        <th>Amount/Number</th>
                        <th>%age of Requested Amount/Number</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Collateral security</td>
                        <td data-bind="text: (collateral_sum>=0)?curr_format(collateral_sum):'Unable to compute'"></td>
                        <td data-bind="text: round(((collateral_sum)/(parseInt($root.loan_details().requested_amount)))*100,2)+'%'"></td>
                      </tr>
                      <tr>
                      <?php if (in_array('15', $modules)){?>
                        <td>Money from Guarantor</td>
                        <td data-bind="text: (guarantor_amount_locked_sum>=0)?curr_format(guarantor_amount_locked_sum):'Unable to compute'"></td>
                        <td data-bind="text: round(((guarantor_amount_locked_sum)/(parseInt($root.loan_details().requested_amount)))*100,3)+'%'"></td>
                      </tr>
                      <tr>
                        <td>Attached Guarantor(s)</td>
                        <td data-bind="text: (guarantor_count>=0)?guarantor_count:'Unable to Count'"></td>
                        <td data-bind="text: round(((guarantor_count)/(parseInt($root.loan_details().min_guarantor)))*100,2)+'%'"></td>
                      </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                  </div>
                <?php if((in_array('6', $modules)) && (in_array('5', $modules))){?>
                  <div  class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Account #</th>
                        <th>Account Balance</th>
                        <th>%age of Requested Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr data-bind="visible: savings_details.length==0">
                        <td colspan="3">No Savings Accounts found for the client</td>
                      </tr> 
                      <tr data-bind="foreach: savings_details">
                        <td data-bind="text: 'Savings A/C '+account_no"></td>
                        <td data-bind="text: (real_bal)?curr_format(real_bal*1):'Unable to compute'"></td>
                        <td data-bind="text: round((parseInt(real_bal)/(parseInt($root.loan_details().requested_amount)))*100,2)+'%'"></td>
                      </tr>

                    </tbody>
                  </table>
                  </div>
                <?php } ?>
                <p><center><small  data-bind=" css:{'text-success':(parseInt($root.loan_details().min_collateral)<=(((guarantor_amount_locked_sum+collateral_sum)/(parseInt($root.loan_details().requested_amount)))*100)), 'text-danger':(parseInt($root.loan_details().min_collateral)>(((guarantor_amount_locked_sum+collateral_sum)/(parseInt($root.loan_details().requested_amount)))*100))}"><span data-bind="text: (parseInt($root.loan_details().min_collateral)<=(((guarantor_amount_locked_sum+collateral_sum)/(parseInt($root.loan_details().requested_amount)))*100))?'Collateral attached is valued at   '+round((((guarantor_amount_locked_sum+collateral_sum)/(parseInt($root.loan_details().requested_amount)))*100),2)+'% / '+parseInt($root.loan_details().min_collateral)+'% Minimum collateral percentage required ':'Loan application can not be approved because the value from the collateral is less than required.Its at  '+(((guarantor_amount_locked_sum+collateral_sum)/(parseInt($root.loan_details().requested_amount)))*100)+'% / '+parseInt($root.loan_details().min_collateral)+'% Minimum collateral percentage required '" ></span> <i class="fa" data-bind="css:{'fa-check':(parseInt($root.loan_details().min_collateral)<(((guarantor_amount_locked_sum+collateral_sum)/(parseInt($root.loan_details().requested_amount)))*100)), 'fa-times':(parseInt($root.loan_details().min_collateral)>(((guarantor_amount_locked_sum+collateral_sum)/(parseInt($root.loan_details().requested_amount)))*100))}"></i></small></center></p>
                </div>

                <!-- we only allow approving loans whose collateral attached is equal or exceeds the  -->
                <div data-bind="visible: (parseInt($root.loan_details().min_collateral)<=(((guarantor_amount_locked_sum+collateral_sum)/(parseInt($root.loan_details().requested_amount)))*100))" >
                    <div class="form-group row">
                      <label class="col-lg-2 col-form-label">Date of Approving<span class="text-danger">*</span></label>
                      <div class="col-lg-4 form-group">
                          <div class="input-group date" data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>">
                            <input class="form-control"  onkeydown="return false" autocomplete="off" data-date-end-date="+0d" data-bind="datepicker: $root.approval_date" required  name="action_date" type="text"><span data-bind="datepicker: $root.approval_date" class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <span  class="help-block with-errors" aria-hidden="true"></span>
                          </div>
                      </div>
                      <label class="col-lg-2 col-form-label">Suggested Disbursement Date<span class="text-danger">*</span></label>
                      <div class="col-lg-4 form-group">
                          <div class="input-group date"  data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>">
                            <input class="form-control" autocomplete="off" onkeydown="return false" data-bind="datepicker: $root.suggested_disbursement_date" required name="suggested_disbursement_date" type="text"><span data-bind="datepicker: $root.suggested_disbursement_date" class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <span  class="help-block with-errors" aria-hidden="true"></span>
                          </div>
                        </div>

                    </div>
                    <span class="text-danger"><small>The loan term should not exceed <span data-bind="text: $parent.loan_product_length" class="blueText"></span> which is the maximum loan period for this loan product</small></span>
                    <div class="form-group row">
                            <label class="col-lg-3 col-form-label">No of Installments<span class="text-danger">*</span></label>
                            <div class="col-lg-2 form-group">
                                <input data-rule-mustbelessthantheProductMaxLoanPeriod id="approved_installments" required class="form-control" name="approved_installments" type="number" data-bind='textInput: ($root.loan_details().approved_installments)?$root.loan_details().approved_installments:$root.loan_details().installments, attr: {"data-rule-min":((parseInt($parent.selected_product().min_repayment_installments)>0)?$parent.selected_product().min_repayment_installments:null), "data-rule-max": ((parseInt($parent.selected_product().max_repayment_installments)>0)?$parent.selected_product().max_repayment_installments:null), "data-msg-min":"Installment is less than "+parseInt($parent.selected_product().min_repayment_installments), "data-msg-max":"Installment is more than "+parseInt($parent.selected_product().max_repayment_installments)}' required />
                                <div class="blueText"><p>
                                  <span data-bind="visible: (parseInt($parent.selected_product().min_repayment_installments)>0)">Min: </span>
                                  <span data-bind="visible: (parseInt($parent.selected_product().min_repayment_installments)>0), text: parseInt($parent.selected_product().min_repayment_installments)"></span>&nbsp;
                                  <span data-bind='visible: (parseInt($parent.selected_product().max_repayment_installments)>0)'>Max: </span>
                                  <span data-bind="visible: (parseInt($parent.selected_product().max_repayment_installments)>0), text: parseInt($parent.selected_product().max_repayment_installments)"></span></p>
                                </div>
                            </div>
                            <label class="col-lg-2 col-form-label">Paid every<span class="text-danger">*</span></label>
                            <div class="col-lg-2 form-group">
                                <input data-rule-mustbelessthantheProductMaxLoanPeriod data-bind="value: ($root.loan_details().approved_repayment_frequency)?$root.loan_details().approved_repayment_frequency:$root.loan_details().repayment_frequency" required class="form-control" name="approved_repayment_frequency" id="approved_repayment_frequency" type="number">
                            </div>
                            <div class="col-lg-3 form-group">
                                <select class="form-control" id="approved_repayment_made_every" name="approved_repayment_made_every" 
                                    data-bind='options: $root.repayment_made_every_detail, optionsText: "made_every_name", optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"),optionsValue:"id", value: ($root.loan_details().approved_repayment_made_every)?$root.loan_details().approved_repayment_made_every:$root.loan_details().repayment_made_every' 
                                    required data-msg-required="This field is required" data-rule-mustbelessthantheProductMaxLoanPeriod>
                                    </select>
                            </div>
                        </div><!--/row -->
                      <div class="form-group row"><!-- start of approval note row -->
                            <label class="col-lg-2 col-form-label">Amount Approved<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                              <input required class="form-control" name="amount_approved" type="number" data-bind="value: $root.loan_details().requested_amount">
                              <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                            <label class="col-lg-2 col-form-label">Approval&nbsp;Note<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                              <textarea required class="form-control" rows="3" name="comment" id="comment"></textarea>                           
                            </div>
                      </div><!--/row -->
                    <input type="hidden" name="rank" data-bind="attr:{value: parseInt(1)}">

                    </div><!-- end of visiblity ko -->

                </div><!-- End of the modal body -->
                    <div class="modal-footer"><!-- start of the modal footer -->
                        <?php if((in_array('1', $client_loan_privilege))||(in_array('3', $client_loan_privilege))){ ?>
                            <button data-bind="enable: (parseFloat($root.loan_details().min_collateral)<=(((parseFloat(guarantor_amount_locked_sum)+parseFloat(collateral_sum))/(parseInt($root.loan_details().requested_amount)))*100))" id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                            <i class="fa fa-check"></i> 
                            <?php
                                if (isset($saveButton)) {
                                    echo $saveButton;
                                }else{
                                    echo "Approve";
                                }
                             ?>
                        </button>
                        <?php } ?>
                        <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                            <i class="fa fa-times"></i> Cancel</button>
                    </div><!-- End of the modal footer -->
                    <!-- /ko -->
            </form>
        </div>
    </div>
</div>
