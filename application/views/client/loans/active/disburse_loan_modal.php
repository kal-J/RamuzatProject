<div class="modal inmodal fade" id="disburse-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form method="post" class="formValidate" action="<?php echo base_url();?>loan_state/disburse" id="formActive">

<div class="modal-header">
     <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
     <h4 class="modal-title">
        <?php
        if (isset($modalTitle1)) {
            echo $modalTitle;
        }else{
            echo "Disbursing a Loan";
        }
     ?></h4>
     <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
    </div>
        <div class="modal-body"><!-- Start of the modal body -->
            
            <!-- ko with: loan_details -->
                    <input type="hidden" name="client_loan_id" data-bind="attr:{value: id}" id="client_loan_id">
                    <input type="hidden" name="group_loan_id" data-bind="attr:{value: (typeof group_loan_id !='undefined')?group_loan_id:''}" id="group_loan_id">
                    <input type="hidden" name="state_id" value="7">
                    <input type="hidden" name="interest_rate" data-bind="attr:{value: interest_rate}">
                    <input type="hidden" name="repayment_frequency" data-bind="attr:{value: approved_repayment_frequency}">
                    <input type="hidden" name="repayment_made_every" data-bind="attr:{value: approved_repayment_made_every}">
                    <input type="hidden" name="grace_period" data-bind="attr:{value: grace_period}">
                    <div class="form-group row">
                      <div class="table-responsive">
                        <table class="table table-bordered table-hover" >
                          <caption class="text-success" style=" font-size: 1.5em; font-weight: bold; text-align: center; caption-side: top;"> Loan Info</caption>
                            <thead>
                                <tr>
                                  <th>Requested Amount</th>
                                  <th>Approved Amount</th>
                                  <th>Interest Rate</th>
                                  <th>Approved Installments</th>
                                </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td data-bind="text: 'UGX '+curr_format((requested_amount)*1)"></td>
                                <td data-bind="text: 'UGX '+curr_format((amount_approved)*1)"></td>
                                <td data-bind="text: (interest_rate)*1+'%'"></td>
                                <td data-bind="text: approved_installments"></td>
                              </tr>
                              <tr><td class="text-danger" style=" font-size: 0.9em; font-weight: bold; text-align: center; caption-side: bottom;" class="font-bold" colspan="4">Note: Each installment is after every<span data-bind="text: (approved_repayment_frequency)?' '+approved_repayment_frequency+' '+approved_made_every_name+', ':'None'" ></span> the loan's offset is <span data-bind="text: (offset_period)?(offset_period+' '+offset_every+' ' ):''" ></span> from the disbursement date [<span data-bind="text: moment($root.action_date(),'DD-MM-YYYY').add(offset_period?offset_period:0,offset_made_every?periods[offset_made_every-1]:'days').format('DD-MM-YYYY')"></span>]</td></tr>
                            </tbody>
                        </table>

                        </div>
                    </div> 
                    <div class="form-group row">
                      <label class="col-lg-2 col-form-label">Date of Disbursing<span class="text-danger">*</span></label>
                      <div class="col-lg-4 form-group">
                          <div class="input-group date">
                            <input class="form-control"  autocomplete="off" data-date-end-date="+0d" data-bind="datepicker: $root.action_date, attr:{value:$root.action_date}" required  name="action_date" type="text" ><span data-bind="datepicker: $root.action_date, attr:{value:$root.action_date}" class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          </div>
                          </div>
                            <label class="col-lg-2 col-form-label">Fund Source A/C<span class="text-danger">*</span></label>
                            <div class="col-lg-4">                              
                            <input type="hidden" class="form-control" name="amount_approved" data-bind="attr:{value: amount_approved}">
                               <input type="hidden" required="required" name="source_fund_account_id" data-bind="attr:{value: fund_source_account_id}">
                                <input class="form-control" type="text" name="fund_source_account" data-bind="attr:{value: fund_source_account}" readonly="readonly">
                            </div>
                        </div>          
            <!--/ko-->
                    <div class="form-group row">
                        <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" >
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
                            <tbody data-bind="foreach: payment_schedule">
                              <tr>

                                <td><span data-bind="text: (installment_number)?installment_number:''"></span><input type="hidden" data-bind="attr: {name:'repayment_schedule['+$index()+'][installment_number]', value: installment_number}"></td>
                                <td><span data-bind="text: (payment_date)?moment(payment_date,'X').format('D-MMM-YYYY'):'None';"></span><input type="hidden" data-bind="attr:{name:'repayment_schedule['+$index()+'][repayment_date]', value:moment(payment_date,'X').format('YYYY-MM-DD')}"></td>
                                <td><span data-bind="text: curr_format((interest_amount)*1)"></span><input type="hidden" data-bind="attr: {name:'repayment_schedule['+$index()+'][interest_amount]', value: interest_amount}"></td>
                                <td> <span data-bind="text: curr_format((principal_amount)*1)"></span><input type="hidden" data-bind="attr: {name:'repayment_schedule['+$index()+'][principal_amount]', value: principal_amount}"></td>
                                <td data-bind="text: curr_format((paid_principal)*1)"></td>
                              </tr>
                            </tbody>
                            <tfoot data-bind="with: payment_summation">
                              <tr>
                                <th></th>
                                <th data-bind="text: 'Period '+ payment_date"></th>
                                <th data-bind="text: 'Total '+ curr_format((interest_amount)*1)"> </th>
                                <th data-bind="text: 'Total '+ curr_format((principal_amount)*1)"> </th>
                                <th data-bind="text: 'Total '+ curr_format((paid_principal)*1)"></th>
                                <input type="hidden" data-bind="attr:{name:'principal_value',value: principal_amount}"/>
                                <input type="hidden" data-bind="attr:{name:'interest_value',value: interest_amount}"/>
                              </tr>
                            </tfoot>
                        </table>
                        </div>                        
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Comment<span class="text-danger">*</span></label>
                        
                        <div class="col-lg-10 form-group">
                            <textarea required class="form-control" rows="4" name="comment" id="comment"></textarea>                           
                          </div>
                    </div><!--/row -->

                </div><!-- End of the modal body -->
                    <div class="modal-footer"><!-- start of the modal footer -->
                            <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                            <i class="fa fa-check"></i> 
                            <?php
                                if (isset($saveButton)) {
                                    echo $saveButton;
                                }else{
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
