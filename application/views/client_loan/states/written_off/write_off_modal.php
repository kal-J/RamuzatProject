<div class="modal inmodal fade" id="write_off-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form method="post" class="formValidate" action="<?php echo base_url();?>loan_state/write_off" id="formWrite_off">

<div class="modal-header">
     <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
     <h4 class="modal-title">
        <?php
        if (isset($modalTitle1)) {
            echo $modalTitle;
        }else{
            echo "Writting off a Loan";
        }
     ?></h4>
     <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
    </div>
        
          <div class="modal-body"><!-- Start of the modal body -->
              <!-- ko with: loan_details -->
                    <div class="form-group row"> 
                        <label class="col-lg-2 col-form-label">Client</label>
                        <div class="col-lg-4">  
                        <span class="form-control" data-bind="text: member_name"></span>
                    </div>
                    <label class="col-lg-2 col-form-label">Loan Ref No.</label>
                        <div class="col-lg-4">
                            <input type="hidden" name="loan_ref_no" data-bind="attr:{value: loan_no}" >
                            <input type="hidden" name="member_id" data-bind="attr:{value: member_id}">
                            <span class="form-control" data-bind="text: loan_no"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-lg-2 col-form-label">Date of Witting off<span class="text-danger">*</span></label>
                      <div class="col-lg-4 form-group">
                          <div class="input-group date" data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>">
                            <input data-bind="datepicker: $root.pay_off_action_date" required class="form-control"  onkeydown="return false" autocomplete="off" name="action_date"  type="text"><span data-bind="datepicker: $root.pay_off_action_date" class="input-group-addon" ><i class="fa fa-calendar"></i></span>
                          </div>
                      </div>
                        <label class="col-lg-2 col-form-label">Comment<span class="text-danger">*</span></label>                        
                        <div class="col-lg-4 form-group">
                          <textarea  class="form-control" rows="3" name="comment" id="comment" required></textarea>                           
                        </div>
                    </div><!--/row -->
                    <input type="hidden" name="state_id" value="8">
                    <input type="hidden" name="member_id" data-bind="attr:{value: member_id}">
                    <input type="hidden" name="client_loan_id" data-bind="attr:{value: id}" id="client_loan_id">
                    <input type="hidden" name="group_loan_id" data-bind="attr:{value: (typeof group_loan_id !='undefined')?group_loan_id:''}" id="group_loan_id">
                    <!--/ko--> 

                     <!-- ko with: pay_off_data -->
                      <div class="row">
                        <div class="col-lg-7">
                                    <fieldset class="">
                                        <legend style=" text-align: right;"> Loan Details</legend>
                                        <table class='table table-hover'>
                                            <thead>
                                                <tr>
                                                    <th class="border-right">#</th>
                                                    <th>Particular</th>
                                                    <th>Amount(UGX)</th>
                                                </tr>
                                            </thead>
                                            <tbody >
                                              <tr>
                                                    <td class="border-right">1</td> 
                                                    <td >
                                                        <span class="input-xs">Disbursed Amount</span>
                                                    </td>
                                                    <td data-bind="text:curr_format(($root.loan_details().amount_approved)*1)"></td>
                                                </tr>
                                                <tr>
                                                    <td class="border-right">2</td> 
                                                    <td >
                                                        <span class="input-xs">Unpaid Intrest <small>(Total)</small></span> 
                                                    </td>
                                                    <input type="hidden" name="un_paid_interest" data-bind="value: round((parseFloat(to_date_interest_sum)-parseFloat(already_interest_amount)),2)">
                                                    <td data-bind="text: curr_format((parseFloat(to_date_interest_sum)-parseFloat(already_interest_amount))*1)"></td>
                                                </tr>
                                                <tr> 
                                                    <td class="border-right">3</td>
                                                    <td >
                                                        <span class="input-xs" >Unpaid Principal <small>(Total)</small></span> 
                                                    </td>
                                                        <input required min="0" step="0.000001"  type="hidden" name="un_paid_principal" data-bind="value: round(parseFloat(principal_sum)-parseFloat(already_principal_amount),2)" >
                                                     <td data-bind="text: curr_format( round((parseFloat(principal_sum)-parseFloat(already_principal_amount))*1,2) )"></td>
                                                    </tr>
                                                <tr> 
                                                  <tr> 
                                                    <td class="border-right">4</td>
                                                    <td >
                                                        <span class="input-xs" >Paid Amount <small>(Principal & Interest)</small></span>
                                                    </td>
                                                     <td data-bind="text: curr_format((already_paid_sum)*1)"></td>
                                                    </tr>
                                                <tr>
                                                    <td class="border-right">5</td>
                                                    <td >
                                                        <span class="input-xs" >Charges</span>
                                                    </td>
                                                    <td data-bind="text: (penalty_value)?curr_format(parseFloat(penalty_value)):'No charge'"></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td><span class="pull-right ">Total Loan Amount <small>Unpaid</small> </span>
                                                        
                                                    </td>
                                                    <input data-bind="value: (penalty_value)?round( ((parseFloat(penalty_value)+parseFloat(to_date_interest_sum)+parseFloat(principal_sum))*1)-(parseFloat(already_paid_sum)*1),2) : round( ((parseFloat(to_date_interest_sum)+parseFloat(principal_sum))*1)-(parseFloat(already_paid_sum)*1),2)" type="hidden" name="expected_total" required >
                                                    <th data-bind="text: (penalty_value)? curr_format(round( ((parseFloat(penalty_value)+parseFloat(to_date_interest_sum)+parseFloat(principal_sum))*1)-(parseFloat(already_paid_sum)*1),2)) :
                                                    curr_format(round( ((parseFloat(to_date_interest_sum)+parseFloat(principal_sum))*1)-(parseFloat(already_paid_sum)*1),2))">
                                                    
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </fieldset>
                                </div>
                        <div class="col-lg-5">
                            <fieldset >
                              <legend style="min-width:250px;">Charges</legend>
                                  <table class='table table-hover' width="100%">
                                            <thead>
                                                <tr>
                                                    <th class="border-right">#</th>
                                                    <th>Name</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody >
                                                <tr>
                                                    <td class="border-right">1</td> 
                                                    <td >
                                                        <span class="input-xs">Penalty</span>
                                                        <input min="0" step="0.000001"  type="hidden" name="un_paid_penalty" data-bind="value:  penalty_value">
                                                    </td>
                                                    <td data-bind="text: (penalty_value)?curr_format(parseFloat(penalty_value)):'No penalty charged'"></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td><span class="pull-right ">Total Charges :</span></td>
                                                    <th data-bind="text: (penalty_value)?curr_format(parseFloat(penalty_value)):'No charge'"></th>
                                                </tr>
                                                <tr>
                                                    <td style=" font-size: 0.9em; font-weight: bold; text-align: center;" class="text-danger" colspan="3" data-bind="text: penalty_message">
                                                    </td>
                                                    <input type="hidden" data-bind="value: round(((to_date_interest_sum+principal_sum)*1)-((already_paid_sum)*1),2)" name="unpaid_total">
                                                  </tr>
                                            </tfoot>
                                        </table>
                                    </fieldset>
                          </div>
                        </div>
            <!--/ko--> 

                </div><!-- End of the modal body -->
                    <div class="modal-footer"><!-- start of the modal footer -->
                            <button  id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                            <i class="fa fa-check"></i> 
                            <?php
                                if (isset($saveButton)) {
                                    echo $saveButton;
                                }else{
                                    echo "Writte off";
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
