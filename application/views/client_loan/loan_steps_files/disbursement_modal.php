<?php $principal_interest_bf_on_topup_loans = isset($org['principal_interest_bf_on_topup_loans']) ? $org['principal_interest_bf_on_topup_loans'] : false; ?>

<div class="modal inmodal fade" id="disburse-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <form method="post" class="formValidate" action="<?php echo base_url(); ?>loan_state/disburse" id="formActive">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title">
            <?php
            if (isset($modalTitle1)) {
              echo $modalTitle;
            } else {
              echo "Disbursing a Loan";
            }
            ?></h4>
          <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
        </div>
        <div class="modal-body">
          <!-- Start of the modal body -->

          <!-- ko with: loan_details -->
          <input type="hidden" name="client_loan_id" data-bind="attr:{value: id}" id="client_loan_id">
          <input type="hidden" name="group_loan_id" data-bind="attr:{value: (typeof group_loan_id !='undefined')?group_loan_id:''}" id="group_loan_id">
          <input type="hidden" name="state_id" value="7">
          <input type="hidden" name="interest_rate" data-bind="attr:{value: interest_rate}">
          <input type="hidden" name="repayment_frequency" data-bind="attr:{value: repayment_frequency}">
          <input type="hidden" name="repayment_made_every" data-bind="attr:{value: repayment_made_every}">
          <input type="hidden" name="grace_period" data-bind="attr:{value: grace_period}">
          <input type="hidden" name="requested_amount" data-bind="attr:{value: requested_amount}">
          <input type="hidden" name="installments" data-bind="attr:{value: installments}">
          <input type="hidden" name="steps" data-bind="attr:{value: parseInt(1)}">
          <input type="hidden" name="preffered_payment_id" data-bind="attr:{value: parseInt(preferred_payment_id)}">
          <div class="form-group row">
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <caption class="text-success" style=" font-size: 1.5em; font-weight: bold; text-align: center; caption-side: top;"> Loan Info</caption>
                <thead>
                  <tr>
                    <th>Requested Amount</th>
                    <th>Interest Rate</th>
                    <th>Installments</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td data-bind="text: 'UGX '+curr_format((requested_amount)*1)"></td>
                    <td data-bind="text: (interest_rate)*1+'%'"></td>
                    <td data-bind="text: installments"></td>
                  </tr>
                  <tr>
                    <td class="text-danger" style=" font-size: 0.9em; font-weight: bold; text-align: center; caption-side: bottom;" class="font-bold" colspan="4">Note: Each installment is after every<span data-bind="text: (repayment_frequency)?' '+repayment_frequency+' '+made_every_name+', ':'None'"></span> the loan's offset is <span data-bind="text: (offset_period)?(offset_period+' '+offset_every+' ' ):''"></span> from the disbursement date [<span data-bind="text: moment($root.action_date(),'DD-MM-YYYY').add(offset_period?offset_period:0,offset_made_every?periods[offset_made_every-1]:'days').format('DD-MM-YYYY')"></span>]</td>
                  </tr>
                </tbody>
              </table>
              <!-- ko if: parseInt(topup_application) ==1 -->
              <input type="hidden" name="linked_loan_id" data-bind="value: parseInt(linked_loan_id)">
              <input type="hidden" name="unpaid_interest" data-bind="value: round(parseFloat(parent_expected_interest)-parseFloat(parent_paid_interest),2) ">
              <input type="hidden" name="unpaid_principal" data-bind="value: round(parseFloat(disbursed_amount)-parseFloat(parent_paid_principal),0) ">
              <table class="table table-bordered table-hover">
                <caption class="text-success" style=" font-size: 1.5em; font-weight: bold; text-align: center; caption-side: top;">Parent Loan Info</caption>
                <thead>
                  <tr>
                    <th>Disbursed Amount (UGX)</th>
                    <th>Paid Principal (UGX)</th>
                    <?php if($principal_interest_bf_on_topup_loans) { ?>
                    <th>UnPaid Interest B/F (UGX)</th>
                    <?php } ?>
                    <th>Remaining bal (UGX)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td data-bind="text: curr_format((disbursed_amount)*1)"></td>
                    <td data-bind="text: curr_format((parent_paid_principal)*1)"></td>
                    <?php if($principal_interest_bf_on_topup_loans) { ?>
                    <td data-bind="text: curr_format((interest_amount_bf)*1)"></td>
                    <?php } ?>
                    <td data-bind="text: curr_format( (parseFloat(disbursed_amount)-parseFloat(parent_paid_principal)) + parseFloat(interest_amount_bf) )"></td>
                  </tr>
                </tbody>
              </table>
              <!-- /ko-->
            </div>
          </div>

          <input type="hidden" class="form-control" name="amount_approved" data-bind="attr:{value: requested_amount}">



          <div class="form-group row">
            <label class="col-lg-2 col-form-label">Date of Disbursing<span class="text-danger">*</span></label>
            <div class="col-lg-4 form-group">
              <div class="input-group date">
                <input class="form-control" data-date-start-date="<?php echo isset($active_month) ? date('d-m-Y', strtotime($active_month['month_start'])) : date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month) ? ((strtotime(date('d-m-Y')) < (strtotime($active_month['month_end']))) ? date('d-m-Y') : date('d-m-Y', strtotime($active_month['month_end']))) : ((strtotime(date('d-m-Y')) < (strtotime($fiscal_active['end_date']))) ? date('d-m-Y') : date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>" onkeydown="return false" autocomplete="off" data-bind="datepicker: $root.app_action_date,attr:{value:$root.app_action_date}" required name="action_date" type="text"><span data-date-start-date="<?php echo isset($active_month) ? date('d-m-Y', strtotime($active_month['month_start'])) : date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month) ? ((strtotime(date('d-m-Y')) < (strtotime($active_month['month_end']))) ? date('d-m-Y') : date('d-m-Y', strtotime($active_month['month_end']))) : ((strtotime(date('d-m-Y')) < (strtotime($fiscal_active['end_date']))) ? date('d-m-Y') : date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>" data-bind="datepicker: $root.app_action_date, attr:{value:$root.app_action_date}" class="input-group-addon"><i class="fa fa-calendar"></i></span>
              </div>
            </div>
            <label class="col-lg-2 col-form-label">Fund Source A/C<span class="text-danger">*</span></label>
            <div class="col-lg-4">

              <select class=" form-control " required="required" id="source_fund_account_id" name="source_fund_account_id" data-bind='options: $root.pay_with, optionsText: function(account){return account.account_code + " " + account.account_name}, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")' style="width: 100%" required>
                <option value="">--select--</option>
              </select>
            </div>
          </div>

          <fieldset class="col-lg-12">
            <legend class="text-success" style=" font-size: 1.5em; font-weight: bold; text-align: center;">Attached Loan Fees</legend>

            <table class="table table-striped table-condensed table-hover">
              <thead>
                <tr>
                  <th>Fee</th>
                  <th>Type</th>
                  <th>Amount</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody data-bind='foreach: $root.available_loan_fees'>

                <tr data-bind="if:parseInt(fee_applied_to)===0 && parseInt(chargetrigger_id)<=3 || (parseInt(chargetrigger_id)===(parseInt($parent.preferred_payment_id)===1?4:0 || parseInt($parent.preferred_payment_id)===2?5:0 ||parseInt($parent.preferred_payment_id)===7?9:0||parseInt($parent.preferred_payment_id)===6?8:0||parseInt($parent.preferred_payment_id)===8?10:0|| parseInt($parent.preferred_payment_id)===4?6:0)) && parseInt(fee_applied_to)===0">

                  <td>
                    <span data-bind="text:feename"></span>
                  </td>
                  <td>
                    <span data-bind="text:feetype"></span>
                  </td>
                  <td>

                  <?php if($principal_interest_bf_on_topup_loans) { ?>
                  <!--  -->
                  <label data-bind="text: (parseInt(amountcalculatedas_id)==3)?curr_format( $root.compute_fee_amount(loanfee_id, parseFloat($parent.amount_approved) + parseFloat($parent.interest_amount_bf) +(parseFloat($parent.disbursed_amount)-parseFloat($parent.parent_paid_principal)) )):( curr_format(parseInt(amountcalculatedas_id)==1?(parseFloat(amount)*(parseFloat($parent.amount_approved) + parseFloat($parent.interest_amount_bf) +(parseFloat($parent.disbursed_amount)-parseFloat($parent.parent_paid_principal)))/100):curr_format(round(amount,2))) ) "></label>

                  <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][amount]"}, value: (parseInt(amountcalculatedas_id)==3)?$root.compute_fee_amount(loanfee_id, parseFloat($parent.amount_approved) + parseFloat($parent.interest_amount_bf) +(parseFloat($parent.disbursed_amount)-parseFloat($parent.parent_paid_principal)) ):( parseInt(amountcalculatedas_id)==1?(parseFloat(amount)*(parseFloat($parent.amount_approved) + parseFloat($parent.interest_amount_bf) + (parseFloat($parent.disbursed_amount)-parseFloat($parent.parent_paid_principal)))/100):amount ) ' />
                    <!--  -->
                  <?php }else { ?>

                    <label data-bind="text: (parseInt(amountcalculatedas_id)==3)?curr_format( $root.compute_fee_amount(loanfee_id,$parent.amount_approved)):( curr_format(parseInt(amountcalculatedas_id)==1?(parseFloat(amount)*$parent.amount_approved/100):curr_format(round(amount,2))) ) "></label>

                    <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][amount]"}, value: (parseInt(amountcalculatedas_id)==3)?$root.compute_fee_amount(loanfee_id,$parent.amount_approved):( parseInt(amountcalculatedas_id)==1?(parseFloat(amount)*$parent.amount_approved/100):amount ) ' />

                  <?php } ?>

                    <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][loan_product_fee_id]"}, value: id' />

                    <input type="hidden" value="0" data-bind='attr:{name:"loanFees["+$index()+"][paid_or_not]"}' />

                  </td>

                  <td>
                    <div data-bind="if:parseInt(feetype_id)===1">
                      <input type="hidden" value="on" data-bind='attr:{name:"loanFees["+$index()+"][remove_or_not]"}' />
                    </div>
                    <div data-bind="if:parseInt(feetype_id)===2">
                      <input type="checkbox" checked="checked" data-bind='attr:{name:"loanFees["+$index()+"][remove_or_not]"}' />
                    </div>
                  </td>

                </tr>

                <tr data-bind="if:parseInt(fee_applied_to)===1 && parseInt(chargetrigger_id)<=3 || (parseInt(chargetrigger_id)===(parseInt($parent.preferred_payment_id)===1?4:0 || parseInt($parent.preferred_payment_id)===2?5:0 ||parseInt($parent.preferred_payment_id)===7?9:0||parseInt($parent.preferred_payment_id)===6?8:0||parseInt($parent.preferred_payment_id)===8?10:0|| parseInt($parent.preferred_payment_id)===4?6:0)) && parseInt(fee_applied_to)===1">

                  <td>
                    <span data-bind="text:feename"></span>
                  </td>
                  <td>
                    <span data-bind="text:feetype"></span>
                  </td>
                  <td>

                  <?php if($principal_interest_bf_on_topup_loans) { ?>

                    <label data-bind="text: (parseInt(amountcalculatedas_id)==3)?curr_format( $root.compute_fee_amount(loanfee_id, parseFloat($parent.disbursed_amount) + parseFloat($parent.interest_amount_bf) - parseFloat($parent.parent_paid_principal) )):( curr_format(parseInt(amountcalculatedas_id)==1?(parseFloat(amount)*( parseFloat($parent.disbursed_amount) + parseFloat($parent.interest_amount_bf) - parseFloat($parent.parent_paid_principal) )/100):curr_format(round(amount,2))) ) "></label>

                    <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][amount]"}, value: (parseInt(amountcalculatedas_id)==3)?$root.compute_fee_amount(loanfee_id,( parseFloat($parent.disbursed_amount) + parseFloat($parent.interest_amount_bf) - parseFloat($parent.parent_paid_principal) )):( parseInt(amountcalculatedas_id)==1?(parseFloat(amount)*( parseFloat($parent.disbursed_amount) + parseFloat($parent.interest_amount_bf) - parseFloat($parent.parent_paid_principal) )/100):amount ) ' />

                    <?php }else { ?>

                    <label data-bind="text: (parseInt(amountcalculatedas_id)==3)?curr_format( $root.compute_fee_amount(loanfee_id,$parent.disbursed_amount-($parent.parent_paid_principal))):( curr_format(parseInt(amountcalculatedas_id)==1?(parseFloat(amount)*(($parent.disbursed_amount)-($parent.parent_paid_principal))/100):curr_format(round(amount,2))) ) "></label>

                    <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][amount]"}, value: (parseInt(amountcalculatedas_id)==3)?$root.compute_fee_amount(loanfee_id,(($parent.disbursed_amount)-($parent.parent_paid_principal))):( parseInt(amountcalculatedas_id)==1?(parseFloat(amount)*(($parent.disbursed_amount)-($parent.parent_paid_principal))/100):amount ) ' />

                    <?php } ?>

                    <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][loan_product_fee_id]"}, value: id' />

                    <input type="hidden" value="0" data-bind='attr:{name:"loanFees["+$index()+"][paid_or_not]"}' />
                  </td>

                  <td>
                    <div data-bind="if:parseInt(feetype_id)===1">
                      <input type="hidden" value="on" data-bind='attr:{name:"loanFees["+$index()+"][remove_or_not]"}' />
                    </div>
                    <div data-bind="if:parseInt(feetype_id)===2">
                      <input type="checkbox" checked="checked" data-bind='attr:{name:"loanFees["+$index()+"][remove_or_not]"}' />
                    </div>
                  </td>

                </tr>




              </tbody>
            </table>
          </fieldset>

          <div class="form-group row">
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover">
                <caption class="text-success" style=" font-size: 1.5em; font-weight: bold; text-align: center; caption-side: top;">Payment Schedule</caption>
                <thead>
                  <tr>
                    <th>## </th>
                    <th>Date of Payment</th>
                    <th>Interest Amount(UGX)</th>
                    <th>Principal Amount(UGX)</th>
                    <th>Total Installment(UGX)</th>
                  </tr>
                </thead>
                <tbody data-bind="foreach: $root.payment_schedule">
                  <tr>

                    <td><span data-bind="text: (installment_number)?installment_number:''"></span><input type="hidden" data-bind="attr: {name:'repayment_schedule['+$index()+'][installment_number]', value: installment_number}"></td>
                    <td><span data-bind="text: (payment_date)?moment(payment_date,'X').format('D-MMM-YYYY'):'None';"></span><input type="hidden" data-bind="attr:{name:'repayment_schedule['+$index()+'][repayment_date]', value:payment_date}"></td>
                    <td><span data-bind="text: curr_format(round(interest_amount,2))"></span><input type="hidden" data-bind="attr: {name:'repayment_schedule['+$index()+'][interest_amount]', value: round(interest_amount,2)}"></td>
                    <td> <span data-bind="text: curr_format(round(principal_amount,2))"></span><input type="hidden" data-bind="attr: {name:'repayment_schedule['+$index()+'][principal_amount]', value: round(principal_amount,2)}"></td>
                    <td data-bind="text: curr_format(round(paid_principal,2))"></td>
                  </tr>
                </tbody>
                <tfoot data-bind="with: $root.payment_summation">
                  <tr>
                    <th></th>
                    <th data-bind="text: 'Period '+ payment_date"></th>
                    <th data-bind="text: 'Total '+ curr_format(round(interest_amount,2))"> </th>
                    <th data-bind="text: 'Total '+ curr_format(round(principal_amount,2))"> </th>
                    <th data-bind="text: 'Total '+ curr_format(round(paid_principal,2))"></th>

                    <input type="hidden" data-bind="attr:{name:'principal_value',value: round(principal_amount,2)}" />
                    <input type="hidden" data-bind="attr:{name:'interest_value',value: round(interest_amount,2)}" />
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>



          <?php //$this->load->view('client_loan/loan_steps_files/disbursement_sheet.php'); 
          ?>
          <!--/ko-->

          <div class="form-group row">
            <label class="col-lg-2 col-form-label">Comment<span class="text-danger">*</span></label>

            <div class="col-lg-10 form-group">
              <textarea required class="form-control" rows="4" name="comment" id="comment"></textarea>
            </div>
          </div>
          <!--/row -->

        </div><!-- End of the modal body -->
        <div class="modal-footer">
          <!-- start of the modal footer -->
          <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
            <i class="fa fa-check"></i>
            <?php
            if (isset($saveButton)) {
              echo $saveButton;
            } else {
              echo "Disburse";
            }
            ?>
          </button>
          <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
            <i class="fa fa-times"></i> Cancel</button>
        </div><!-- End of the modal footer -->
      </form>
    </div>
  </div>
</div>