<div class="modal inmodal fade" id="installment_payment-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data" class="formValidate" action="<?php echo base_url();?>loan_installment_payment/loan_installment_repayment" id="formInstallment_payment">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">  
                        Installment Payment 
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                <input type="hidden" name="state_id" value="10">
                    <div class="form-group row">
                    <input type="hidden" id="call_type" name="call_type" value="<?php echo (isset($case2))?$case2:'';?>">
                        <label class="col-lg-2 col-form-label">Loan Ref No.<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                                <select style="width:100%;" name="loan_ref_no"  id="loan_ref_no"  class="form-control" data-bind='options: $root.active_loans, optionsText: function(data_item){ return data_item.member_name+"-"+data_item.loan_no;}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("loan_no"), value: $root.active_loan' >
                            </select>
                        </div>
                        <label class="col-lg-2 col-form-label">Installment #<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <div class="input-group">
                                <select style="width:70%;" name="installment_number" id="installment_number"  class="form-control" 
                                data-bind='options: $root.filtered_active_loan_installment(), optionsText: "installment_number", optionsCaption: "---select---", optionsAfterRender: setOptionValue("installment_number"), value: $root.loan_installment, 
                                '>
                            </select>
                            <!-- optionsBind: "attr: { disabled: function(item){ return ($root.filtered_active_loan_installmen.indexOf(item.installment_number) == 0)?false:true; }}" -->
                            </div>
                        </div>
                    </div>
                    <!-- ko if:( (typeof $root.payment_data() !=='undefined')) -->
                    <!-- ko with: payment_data -->
                    <div class="form-group row">
                        <input required data-bind="value: id" name="client_loan_id" id="client_loan_id" type="hidden">  
                        <input required data-bind="value: repayment_schedule_id" name="repayment_schedule_id" id="repayment_schedule_id" type="hidden">  
                        <label class="col-lg-2 col-form-label">Client<span class="text-danger">*</span></label>
                        <div class="col-lg-4">  
                        <span class="form-control" data-bind="text: (typeof client_no !== 'undefined')?client_name+'-'+client_no:client_name"></span>
                    </div> 
                        <label class="col-lg-2 col-form-label">Date Received</label>
                      <div class="col-lg-4 form-group">
                        <div class="input-group date">
                            <input class="form-control"  autocomplete="off" data-bind="datepicker: $root.installment_payment_date" required  name="payment_date" type="text" ><span data-bind="datepicker: $root.installment_payment_date" class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                    </div>
                    
                    <div class="form-group row">  
                        <label class="col-lg-2 col-form-label">Principal Amount<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <input class="form-control" autocomplete="off" type="number" name="paid_principal" data-bind="textInput: $parent.principal_amount,valueUpdate:'afterkeydown',  attr: {'data-rule-max':(parseFloat(remaining_principal)>parseInt(0))?Math.ceil(parseFloat(remaining_principal)/100)*100:0,'data-msg-max':'Amount is greater than '+curr_format(Math.ceil(parseFloat(remaining_principal)/100)*100)}" >
                        <p class="blueText">
                            <span data-bind="">Min: </span>
                            <span data-bind="text: curr_format(parseInt(0))"></span>&nbsp;
                            <span data-bind=''>Max: </span>
                            <span data-bind="text: curr_format(Math.ceil(parseFloat(remaining_principal)/100)*100)"></span>
                        </p>

                        </div>

                         <label class="col-lg-2 col-form-label">Interest Amount<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <input class="form-control" autocomplete="off" type="number" name="paid_interest" data-bind="textInput: $parent.interest_amount,valueUpdate:'afterkeydown',  attr: {'data-rule-max':(parseFloat(remaining_interest)>parseInt(0))?Math.ceil(parseFloat(remaining_interest)/100)*100:0,'data-msg-max':'Amount is greater than '+curr_format(Math.ceil(parseFloat(remaining_interest)/100)*100)}" required >
                            <p class="blueText">
                            <span data-bind="">Min: </span>
                            <span data-bind="text: curr_format(parseInt(0))"></span>&nbsp;
                            <span data-bind=''>Max: </span>
                            <span data-bind="text: curr_format(Math.ceil(parseFloat(remaining_interest)/100)*100)"></span>
                        </p>
                        </div>

                    </div>  
                    <div class="form-group row">

                         <label data-bind="visible: typeof $root.penalty_amount() !== 'undefined'" class="col-lg-2 col-form-label">Penalty Charged<span class="text-danger">*</span></label>
                        <div  data-bind="visible: typeof $root.penalty_amount() !== 'undefined'" class="col-lg-4">
                            <input min="0" step="0.000001" class="form-control" autocomplete="off" type="number" name="paid_penalty" data-bind="textInput: $parent.received_penalty_amount,valueUpdate:'afterkeydown'">

                            <p class="blueText">
                            <span data-bind="">Min: </span>
                            <span data-bind="text: curr_format(parseInt(0))"></span>&nbsp;
                            <span data-bind=''>Max: </span>
                            <span data-bind="text: (typeof $root.penalty_amount() !== 'undefined')?curr_format(Math.ceil(parseFloat($root.penalty_amount().penalty_value)/100)*100):''"></span>
                        </p>
                        </div> 

                        <label class="col-lg-2 col-form-label">Transaction channel<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <select  class="form-control" id="transaction_channel_id" name="transaction_channel_id" data-bind='options: $root.transaction_channel, optionsText: "channel_name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:$root.tchannels' data-msg-required="Transaction must be selected" style="width: 100%" required >
                            </select>
                        </div>
                    </div>  
                    <div class="form-group row">  
                        <label class="col-lg-2 col-form-label">Narrative</label>
                        <div class="col-lg-4">
                            <textarea required rows="3" class="form-control" id="comment" name="comment"></textarea>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <legend style=" text-align: right;"> Received Amount</legend>
                                <input class="form-control" data-bind="value: parseFloat($parent.interest_amount()) + parseFloat($parent.principal_amount()) + parseFloat($parent.received_penalty_amount())" name="paid_total" type="text" required hidden>
                                <h2 class="pull-right" data-bind="text: curr_format( parseFloat($parent.interest_amount()) + parseFloat($parent.principal_amount()) + parseFloat($parent.received_penalty_amount())) "></h2>
                            </fieldset>
                        </div>
                    </div>
                            <br>
                            <div class="form-group row">
                                <div class="col-lg-7">
                                    <fieldset class="">
                                        <legend style=" text-align: right;"> Installment Details</legend>
                                        <table class='table table-hover'>
                                            <thead>
                                                <tr>
                                                    <th class="border-right">#</th>
                                                    <th>Particular</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody >
                                                <tr>
                                                    <td class="border-right">1</td> 
                                                    <td >
                                                        <span class="input-xs" required >Intrest Payable</span>
                                                        <input required min="0" step="0.01"  type="hidden" name="expected_interest" data-bind="value: parseFloat(remaining_interest)">
                                                    </td>
                                                    <td data-bind="text: curr_format(parseFloat(remaining_interest))"></td>
                                                </tr>
                                                <tr> 
                                                    <td class="border-right">2</td>
                                                    <td >
                                                        <span class="input-xs" required >Principal Payable</span>
                                                        <input required min="0" step="0.000001"  type="hidden" name="expected_principal" data-bind="value: parseFloat(remaining_principal)" >
                                                    </td>
                                                     <td data-bind="text: curr_format(parseFloat(remaining_principal))"></td>
                                                    </tr>
                                                <tr> 
                                                    <td class="border-right">3</td>
                                                    <td >
                                                        <span class="input-xs" required >Charges</span>
                                                    </td>
                                                    <td data-bind="text: (typeof $root.penalty_amount() !== 'undefined')?curr_format(parseFloat($root.penalty_amount().penalty_value)):''"></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td><span class="pull-right ">Total Amount :</span>
                                                        <input min="0" step="0.000001" type="hidden" name="expected_total" data-bind="value: ((typeof $root.penalty_amount() !== 'undefined') && (typeof $root.penalty_amount()))? parseFloat($root.penalty_amount().penalty_value) + parseFloat(total_amount):parseFloat(total_amount)">
                                                    </td>
                                                    <th data-bind="text: (typeof $root.penalty_amount() !== 'undefined')
                                                    ? curr_format( Math.round( (( parseFloat($root.penalty_amount().penalty_value) + parseFloat(total_amount) )*100))/100): curr_format( Math.round((parseFloat(total_amount)*100)/100))">
                                                    </th>

                                                </tr>
                                                <tr>
                                                    <td style=" font-size: 0.9em; font-weight: bold; text-align: center;" colspan="2" class="text-danger"   data-bind="text: 'Installment Number: '+installment_number"></td>
                                                    <td style=" font-size: 0.9em; font-weight: bold; text-align: center;" class="text-danger" data-bind="text: 'Due date: '+moment(repayment_date,'YYYY-MM-DD').format('DD-MMM-YYYY')"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </fieldset>
                                </div>
                                <div class="col-lg-5">
                                    <fieldset >
                                        <legend style="min-width:250px;">Charges</legend>
                                        <table class='table table-hover'>
                                            <thead>
                                                <tr>
                                                    <th class="border-right">#</th>
                                                    <th>Name</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody >
                                                <tr>
                                                    <td class="border-right"></td> 
                                                    <td >
                                                        <span class="input-xs" required >Penalty</span>
                                                        <input min="0" step="0.000001"  type="hidden" name="expected_penalty" data-bind="value:  ((typeof $root.penalty_amount() !== 'undefined') && (typeof $root.penalty_amount()))?parseFloat($root.penalty_amount().penalty_value):''">
                                                    </td>
                                                    <td data-bind="text: (typeof $root.penalty_amount() !== 'undefined')?curr_format(parseFloat($root.penalty_amount().penalty_value)):'No penalty charged'"></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td><span class="pull-right ">Total Charges :</span></td>
                                                    <th data-bind="text: (typeof $root.penalty_amount() !== 'undefined')?curr_format(parseFloat($root.penalty_amount().penalty_value)):'No penalty charged'"></th>
                                                </tr>
                                                <tr>
                                                    <td style=" font-size: 0.9em; font-weight: bold; text-align: center;" class="text-danger" colspan="3" data-bind="text: (typeof $root.penalty_amount() !== 'undefined')?$root.penalty_amount().message:''"></td>
                                                  </tr>
                                            </tfoot>
                                        </table>
                                    </fieldset>
                                </div>
                            </div>
                        <!--/ko-->
                        <!--/ko-->
                        </div> 
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            } else {
                                echo "Save";
                            }
                            ?>
                        </button>
                    </div>
            </form>
        </div>
    </div>
</div>
