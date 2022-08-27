<div class="modal inmodal fade" id="apply_loan_fee-modal"  role="dialog" aria-hidden="true" style="display: none;">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="post" class="formValidate" action="<?php echo base_url();?>applied_loan_fee/apply" id="formLoan_fee_application">
<div class="modal-header">
 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
 <h4 class="modal-title"> Apply Loan Fee(s) </h4>
 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
</div>

<input class="form-control" name="id" id="id" type="hidden">
<input class="form-control" name="loan_id" value="<?php echo $loan_detail['id']; ?>" type="hidden">
<input class="form-control" name="loan_product_id" value="<?php echo $loan_detail['loan_product_id']; ?>" type="hidden">
                <div class="modal-body">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table  class="table table-striped table-condensed table-hover m-t-md">
                                <thead>
                                    <tr>
                                        <th>Fee</th>
                                        <th>Amount</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody data-bind='foreach: $root.applied_loan_fee'>
                                    <tr>
                                        <td>
                                            <select data-bind='
                                            options: $root.available_loan_fees, 
                                            optionsText: function(data_item){return data_item.feename}, 
                                            optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"), 
                                            value: selected_fee' class="form-control"  style="width: 250px"> </select>
                                        </td>
                                        <td data-bind="with: selected_fee">
                                            <label data-bind="text: (parseInt(amountcalculatedas_id)==3)?curr_format( $root.compute_fee_amount(loanfee_id,(($root.loan_detail().amount_approved)?$root.loan_detail().amount_approved:$root.loan_detail().requested_amount))):( curr_format(parseInt(amountcalculatedas_id)==1?(parseFloat(amount)*(($root.loan_detail().amount_approved)?$root.loan_detail().amount_approved:$root.loan_detail().requested_amount)/100):amount) ) "></label>
                                            <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][amount]"}, value: (parseInt(amountcalculatedas_id)==3)?
                                             $root.compute_fee_amount(loanfee_id,(($root.loan_detail().amount_approved)?$root.loan_detail().amount_approved:$root.loan_detail().requested_amount)):(
                                             parseInt(amountcalculatedas_id)==1?(parseFloat(amount)*(($root.loan_detail().amount_approved)?$root.loan_detail().amount_approved:$root.loan_detail().requested_amount)/100):amount ) '/>
                                            <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][loan_product_fee_id]"}, value: id'/>  
                                            
                                            <input type="hidden"  value="0" data-bind='attr:{name:"loanFees["+$index()+"][paid_or_not]"}'>
                                        </td>
                                        <td>
                                            <span title="Remove item" class="btn text-danger" data-bind='click: $root.removeLoanFee'><i class="fa fa-minus"></i></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-bind='click: $root.addLoanFee, enable:$root.available_loan_fees().length > 0' class="btn-white btn-sm"><i class="fa fa-plus"></i> Apply Another Fee</button>
                    <?php if((in_array('1', $client_loan_privilege))||(in_array('3', $client_loan_privilege))){ ?>
                    <button class="btn btn-primary btn-flat" data-bind="enable:$root.available_loan_fees().length > 0" type="submit">Apply Fees</button>
                    <?php } ?>
                </div>
            </form>
            </div>
            </div>
            </div>
