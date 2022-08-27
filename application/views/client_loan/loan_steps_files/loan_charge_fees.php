 <!-- ko with: payment_mode -->
<div class="form-group row">
  <div class="table-responsive">
  <table class="table table-striped table-bordered table-hover" >
    <caption class="text-success" style=" font-size: 1.5em; font-weight: bold; text-align: center; caption-side: top;">Required fees payment after submitting the loan application</caption>
      <thead>
          <tr>
            <th>Fee</th>
            <th>Amount(UGX)</th>
          </tr>
      </thead>
      <!-- ko if: !($root.filtered_loan_fees )-->
        <tbody>
          <tr>
            <td colspan="2"><span class="text-center">No charge fees for this loan</span></td>
          </tr> 
        </tbody>
       <!--/ko-->
      <tbody data-bind="foreach: $root.filtered_loan_fees">
        <tr data-bind="if:parseInt(chargetrigger_id)<=3 || (parseInt(chargetrigger_id)===(parseInt($parent.id)===1?4:0 || parseInt($parent.id)===2?5:0 ||parseInt($parent.id)===7?9:0||parseInt($parent.id)===6?8:0||parseInt($parent.id)===8?10:0|| parseInt($parent.id)===4?6:0))">
          <td><span data-bind="text: feename"></span></td>
          <td><span data-bind="text: (parseInt(amountcalculatedas_id)==3) ? curr_format( round($root.compute_fee_amount(loanfee_id,$root.app_amount()) , 2) ) : ( curr_format( parseInt(amountcalculatedas_id)==1 ? round(parseFloat(amount)*$root.app_amount()/100 , 2) : round(amount , 2) ) ) "></span></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <th>Total</th>
          <th data-bind="text: $root.filtered_loan_fees_total"></th>
        </tr>
      </tfoot>
  </table>
  </div>                        
</div>
<!--/ko-->