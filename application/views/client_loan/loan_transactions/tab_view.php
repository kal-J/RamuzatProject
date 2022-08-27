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
        <div class="col-lg-5">
            <div class="d-flex flex-row-reverse align-items-center mb-2 pull-right">
                <button id="btn_print_loan_transactions" onclick="handlePrint()" class="btn btn-sm btn-secondary"><i class="fa fa-print fa-2x"></i></button>
                <button id="btn_printing_loan_transactions" class="btn btn-primary" type="button" disabled>
                    <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                    Printing...
                </button>
            </div>

            <div class="pull-right add-record-btn mr-2">
                <!--  <div style=" font-size: 0.9em; font-weight: bold; text-align: center;"
                                                    class="text-danger">  
                                                    NOTE Making payments from this page has been temporarily disabled, Please make payments from the Loan list page 
                                                   
                                                </div> -->
                <div class="panel-title">
                    <?php if (in_array('13', $client_loan_privilege)) { ?>
                        <a data-bind="visible: (parseInt($root.loan_detail().state_id) ==7 || parseInt($root.loan_detail().state_id) ==13)" class="btn btn-sm btn-primary text-white mr-2" data-toggle="modal" data-target="#multiple_installment_payment-modal"><i class="fa fa-money"></i> Multiple Installment Payment</a>
                        <a data-bind="visible: (parseInt($root.loan_detail().state_id) ==7 || parseInt($root.loan_detail().state_id) ==13)" class="btn btn-primary btn-sm pull-right text-white" data-toggle="modal" data-target="#installment_payment-modal"><i class="fa fa-money"></i> Payment</a>
                    <?php }  ?>
                </div>
            </div>
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
                    <th>Written Off Interest (UGX)</th>
                    <th>Total Payment</th>
                    <th>Balance</th>
                    <th>Date Paid</th>
                    <th>Received By</th>
                    <th>Comment</th>
                    <th></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th>0</th>
                    <th>0</th>
                    <th>0</th>
                    <th>0</th>
                    <th>0</th>
                    <th></th>
                    <th colspan="2">&nbsp;</th>
                </tr>
            </tfoot>
            <tbody>
            </tbody>
        </table>
    </div>
</div><!-- ==END TAB-PENDING APPROVAL =====-->