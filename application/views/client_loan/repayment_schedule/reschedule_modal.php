<div class="modal inmodal fade" id="reschedule_payment-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <form method="post" class="formValidate" action="<?php echo base_url(); ?>repayment_schedule/reschedule" id="formReschedule_payment">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title">
            <?php
            if (isset($modalTitle)) {
              echo $modalTitle;
            } else {
              echo "Reschedule Loan Repayment";
            }
            ?>

          </h4>
          <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
        </div>
        <div class="modal-body">
          <!-- ko with: schedule_detail -->
          <input type="hidden" name="client_loan_id" data-bind="value: client_loan_id" id="client_loan_id">
          <input type="hidden" name="end_id" data-bind="value: (parseInt(id)+(parseInt($root.loan_detail().approved_installments)-parseInt(installment_number))) ">
          <div class="form-group row">
            <label class="col-lg-2 col-form-label">From Installment<span class="text-danger">*</span></label>
            <div class="col-lg-3 form-group">
              <div class="input-group">
                <span class="form-control" data-bind="text: $root.current_installment"></span>
                <input type="hidden" class="form-control" name="current_installment" data-bind="value: $root.current_installment" id="current_installment">
              </div>
            </div>
            <label class="col-lg-3 col-form-label">Current Date<span class="text-danger">*</span></label>
            <div class="col-lg-4 form-group">
              <div class="input-group date">
                <span class="form-control" data-bind="text: moment(repayment_date,'YYYY-MM-DD').format('DD-MM-YYYY')" type="text"></span><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
              </div>
            </div>
          </div>
          <!--/row -->

          <div class="form-group row">
            <label class="col-lg-2 col-form-label">Interest Rate<span class="text-danger">*</span></label>
            <div class="col-lg-3 form-group">
              <input placeholder="" min="0.0" max="1000" step="0.01" required class="form-control" data-bind="value: $root.interest_rate" name="interest_rate" type="number">
            </div>
            <label class="col-lg-3 col-form-label">Repayment Every<span class="text-danger">*</span></label>
            <div class="col-lg-2 form-group">
              <input placeholder="" min="1" max="20" required class="form-control" data-bind="value: $root.repayment_frequency" name="repayment_frequency" type="number">
            </div>
            <select class="col-lg-2 form-control" name="repayment_made_every" data-bind='options: $root.repayment_made_every_detail, optionsText: "made_every_name", optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"),optionsValue:"id", value: $root.repayment_made_every' required data-msg-required="This field is required">
            </select>
          </div>
          <!--/row -->

          <div class="form-group row">
            <label class="col-lg-2 col-form-label">Installments<span class="text-danger">*</span></label>

            <div class="col-lg-3">
              <input min="0" step="0.01" required class="form-control" data-bind="value: $root.installments" name="installments" type="number">
            </div>

            <label class="col-lg-3 col-form-label">Grace Period<span class="text-danger">*</span></label>

            <div class="col-lg-4">
              <input min="0" step="0.01" data-bind="value: grace_period_after" required class="form-control" name="grace_period_after" type="number">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-lg-2 col-form-label">New Payment Date<span class="text-danger">*</span></label>
            <div class="col-lg-3 form-group">
              <div class="input-group date">
                <input class="form-control" data-date-start-date="+1d" onkeydown="return false" autocomplete="off" required data-bind="datepicker: $root.new_date" name="new_repayment_date" type="text"><span data-bind="datepicker: $root.new_date" class="input-group-addon"><i class="fa fa-calendar"></i></span>
              </div>
            </div>
           <!--  <div class="col-lg-7">
              <label class="col-form-label" style="font-size: 1.1em; font-weight: bold;">Compute Interest starting from disbursement date? <span class="text-danger">*</span></label>
              <div class="form-group d-flex flex-row-reverse col-lg-11">
                <span>
                  <label> <input checked value="0" name="compute_interest_from_disbursement_date" type="radio" data-bind="checked: $root.compute_interest_from_disbursement_date"> No </label>
                <label> <input value="1" type="radio" name="compute_interest_from_disbursement_date" data-bind="checked: $root.compute_interest_from_disbursement_date"> Yes</label>
                </span>
                
              </div>
            </div> -->
          </div>
          <!--/row -->
          <!--/ko-->
          <div class="form-group row">
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover">
                <caption class="text-success" style=" font-size: 1.5em; font-weight: bold; text-align: center; caption-side: top;">Payment Reschedule</caption>
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
                    <th data-bind="text: 'Total '+ curr_format(round(principal_amount,0))"> </th>
                    <th data-bind="text: 'Total '+ curr_format(round(paid_principal,1))"></th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          <!--/row -->

          <div class="form-group row">
            <label class="col-lg-2 col-form-label">Comment<span class="text-danger">*</span></label>

            <div class="col-lg-10 form-group">
              <textarea required class="form-control" rows="4" name="comment" id="comment"></textarea>
            </div>
          </div>
          <!--/row -->
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success btn-sm">
            <i class="fa fa-check"></i> <?php
                                        if (isset($saveButton)) {
                                          echo $saveButton;
                                        } else {
                                          echo "Reschedule";
                                        }
                                        ?></button>
          <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
            <i class="fa fa-times"></i> Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>