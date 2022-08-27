<div id="tab-loan_installment_payment" class="tab-pane loans">
  <div class="panel-title">
    <center>
      <h3>Loan Installment Payments</h3>
    </center>
  </div>
  <div class="row">
    <div class="col-lg-7">
      <center>
        <table class="table table-bordered table-hover table-user-information  table-stripped  m-t-md">
          <tbody data-bind="with: loan_detail">
            <td colspan="2">
              <h4>
                <strong>Unpaid Principal: </strong>
                <span class="text-danger" style="font-weight: bold;" data-bind="text: (expected_principal)?curr_format(round(parseFloat(expected_principal)-parseFloat(paid_principal)),2):0"></span>
              </h4>
            </td>
            <td colspan="2">
              <h4>
                <strong>Unpaid Interest: </strong>
                <span class="text-danger" style="font-weight: bold;" data-bind="text: (expected_interest)?curr_format(round(parseFloat(expected_interest)-parseFloat(paid_interest)),2):0"></span>
              </h4>
            </td>
            <td colspan="2">
              <h4>
                <strong>Unpaid Penalty: </strong>
                <span class="text-danger" style="font-weight: bold;" data-bind="text: (total_penalty)?curr_format(total_penalty):0"></span>
              </h4>
            </td>

            <td colspan="3">
              <h4>
                <strong>Total: </strong>
                <span class="text-danger" style="font-weight: bold;" data-bind="text: (expected_principal)?curr_format(round((parseFloat(expected_principal)+parseFloat(expected_interest)+parseFloat(total_penalty))-parseFloat(paid_amount)),2):0"></span>
              </h4>
            </td>

            </tr>
          </tbody>
        </table>
      </center>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover dataTables-example" id="tblLoan_installment_payment" style="width: 100%">
      <thead>
        <tr>
          <th>Loan Ref #</th>
          <th>Installment Number</th>
          <th>Interest Paid</th>
          <th>Principal Paid (UGX)</th>
          <th>Penalty Paid (UGX)</th>
          <th>Date Paid</th>
          <th>Received By</th>
          <th>Comment</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th colspan="2">Total</th>
          <th>0</th>
          <th>0</th>
          <th>0</th>
          <th colspan="3">&nbsp;</th>
        </tr>
      </tfoot>
      <tbody>
      </tbody>
    </table>
  </div>
</div><!-- ==END TAB-PENDING APPROVAL =====-->