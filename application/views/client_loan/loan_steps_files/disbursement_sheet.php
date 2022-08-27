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
<!-- ko with: payment_mode -->
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

      <tr data-bind="if:parseInt(fee_applied_to)===0 && parseInt(chargetrigger_id)<=3 || (parseInt(chargetrigger_id)===(parseInt($parent.preferred_payment_id)===1?4:0 || parseInt($parent.preferred_payment_id)===2?5:0 ||parseInt($parent.preferred_payment_id)===7?9:0||parseInt($parent.preferred_payment_id)===6?8:0||parseInt($parent.preferred_payment_id)===8?10:0|| parseInt($parent.preferred_payment_id)===4?6:0))">

        <td>
          <span data-bind="text:feename"></span>
        </td>
        <td>
          <span data-bind="text:feetype"></span>
        </td>
        <td>
          <label data-bind="text: (parseInt(amountcalculatedas_id)==3)?curr_format( $root.compute_fee_amount(loanfee_id,$root.app_amount())):( curr_format(parseInt(amountcalculatedas_id)==1?(parseFloat(amount)*$root.app_amount()/100):curr_format(round(amount,2))) ) "></label>

          <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][amount]"}, value: (parseInt(amountcalculatedas_id)==3)?$root.compute_fee_amount(loanfee_id,$root.app_amount()):( parseInt(amountcalculatedas_id)==1?(parseFloat(amount)*$root.app_amount()/100):amount ) ' />

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
  <center>
    <!-- <div class="row">
        <label class="col-lg-3 col-form-label">Select Fees Payment Method<span class="text-danger">*</span></label>
     <select class=" col-lg-4 form-control form-control-sm" required name="fees_payment_method" style="width: 300px;">
        <option value="">---Fees Payment Method----</option>
        <option value="1">CASH</option>
        <option value="2">ATTACHED SAVINGS ACCOUNT</option>
      </select>
     </div> -->
  </center>
</fieldset>
<!-- /ko-->

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