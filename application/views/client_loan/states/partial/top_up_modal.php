<style type="text/css">
    section{
    overflow-y : auto;
    }
    @media (min-width: 992px) {
      .modal-lg,
      .modal-xl {
        max-width: 800px;
      }
    }

    @media (min-width: 1200px) {
      .modal-xl {
        max-width: 1140px;
      }
    }
</style>
<div class="modal bd-example-modal-xl inmodal fade" id="top_client_loan-modal" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
          
                <div class="modal-header" style="padding: 0px 15px; text-align: center; display: block;">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Topup Application Form</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>

                <div class="modal-body" style="padding: 20px 10px 0px 10px;"> 
                <form method="post" class="formValidate wizard-big" id="formTopup_loan" action="<?php 
                if( isset($case2) && $case2 =='My Loans'){
                    echo base_url('u/loans/Create2');
                }else{ 
                    echo base_url('client_loan/Create2'); 
                } ?>" enctype="multipart/form-data">
                    <h1>Top Up <small>Details</small></h1> <!-- Step one -->
                    <section>
                        <input type="hidden" name="topup_application" value="1">
                        <?php 
                        if( isset($case2) && $case2 =='My Loans'){
                            $this->load->view('client_loan/loan_steps_files/member_loan_application.php'); 
                        }else{ 
                            $this->load->view('client_loan/loan_steps_files/top_up_details.php'); 
                        }?>
                    </section>

                    <!-- <h1>Security & Fees</h1><-- Step three -->
                    <!-- <section class="section">
                        <?php //$this->load->view('client_loan/loan_steps_files/security_fees.php'); ?>
                    </section> -->

                    <!-- <h1>Incomes, Expenses & Loan Docs</h1> <-- Step two --> 
                    <!-- <section class="section">
                        <?php //$this->load->view('client_loan/loan_steps_files/income_and_expense.php'); ?>
                    </section> -->


                    <h1>Finish</h1>
                    <section>
                        <?php if (isset($case2) && $case2 !='My Loans') { ?>
                        <!-- <fieldset class="col-lg-12">
                            <legend>Attach Loan Fees</legend>
                            <table  class="table table-striped table-condensed table-hover m-t-md">
                                <thead>
                                    <tr>
                                        <th>Fee</th>
                                        <th>Amount</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <div class="col-sm-2 pull-right">
                                        <a data-bind='click: $root.addLoanFee' class="btn-info btn-sm"><i class="fa fa-plus"></i></a>
                                    </div>
                                <tbody data-bind='foreach: $root.applied_loan_fee'>
                                    <tr>
                                        <td>
                                            <select data-bind='
                                            options: $root.filtered_loan_fees, 
                                            optionsText: function(data_item){return data_item.feename}, 
                                            optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"), 
                                            value: selected_fee' class="form-control"  style="width: 250px"> </select>
                                        </td>
                                        <td data-bind="with: selected_fee">
                                            <label data-bind="text: (parseInt(amountcalculatedas_id)==3)?curr_format( $root.compute_fee_amount(loanfee_id,$root.app_amount())):( curr_format(parseInt(amountcalculatedas_id)==1?(parseFloat(amount)*$root.app_amount()/100):amount) ) "></label>
                                            <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][amount]"}, value: (parseInt(amountcalculatedas_id)==3)?$root.compute_fee_amount(loanfee_id,$root.app_amount()):( parseInt(amountcalculatedas_id)==1?(parseFloat(amount)*$root.app_amount()/100):amount ) '/>
                                            <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][loan_product_fee_id]"}, value: id'/>  
                                            <input type="hidden" value="0" data-bind='attr:{name:"loanFees["+$index()+"][paid_or_not]"}'>
                                        </td>
                                        <td>
                                            <span title="Remove item" class="btn text-danger" data-bind='click: $root.removeLoanFee'><i class="fa fa-minus"></i></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </fieldset>
                        <hr/>  -->
                        <?php } ?>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Preferred Payment Option<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <select class="form-control" name="preferred_payment_id" data-bind='options: payment_modes, optionsText: "payment_mode", optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: payment_mode' required data-msg-required="Payment mode is required" style="width: 100%">
                                </select>
                            </div>
                        </div>
                        <!-- ko with: payment_mode -->
                        <!-- ko if: parseInt(id)===2 -->
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">A/C Name<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <input class="form-control" name="ac_name" type="text" required />
                            </div>
                            <label class="col-lg-2 col-form-label">A/C Number<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <input class="form-control" name="ac_number" type="text" required />
                            </div>
                            <label class="col-lg-2 col-form-label">Bank Branch<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <input class="form-control" name="bank_branch" type="text" required />
                            </div>
                            <label class="col-lg-2 col-form-label">Bank Name<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <input class="form-control" name="bank_name" type="text" required />
                            </div>
                        </div>
                        <!-- /ko -->
                        <!-- ko if: parseInt(id)===4 -->
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Phone Number<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <input class="form-control" name="phone_number" type="text" required />
                            </div>
                        </div>
                        <!-- /ko -->
                        <!-- /ko -->

                        <?php if (isset($case2) && $case2 !='My Loans') { ?>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Complete Application?  <span class="text-danger">*</span></label>
                            <div class="col-lg-1 form-group">
                                <center>
                                    <label> <input checked value="0" name="complete_application" type="radio" data-bind="checked: complete_application"> No</label>
                                    <label> <input value="1" type="radio" name="complete_application" data-bind="checked: complete_application" > Yes</label>
                                </center>
                            </div>
                        </div><!--/row-->

                        <!-- ko if: parseInt(complete_application())===1 -->
                            <input type="hidden" name="loan_app_stage" value="<?php echo $org['loan_app_stage']; ?>">

                            <?php
                            if($org['loan_app_stage']==2){ 
                                ?>
                                <!-- ko with: $root.selected_active_loan -->
                                <input type="hidden" name="unpaid_interest" data-bind="value: round(parseFloat(expected_interest)-parseFloat(paid_interest),2) ">
                                <input type="hidden" name="unpaid_principal" data-bind="value: round(parseFloat(expected_principal)-parseFloat(paid_principal),0) ">
                                <table class="table table-bordered table-hover" >
                                    <caption class="text-success" style=" font-size: 1.5em; font-weight: bold; text-align: center; caption-side: top;">Parent Loan Info</caption>
                                    <thead>
                                        <tr>
                                          <th>Disbursed Amount (UGX)</th>
                                          <th>Paid Principal (UGX)</th>
                                          <th>Remaining bal (UGX)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      <tr>
                                        <td data-bind="text: curr_format((expected_principal)*1)"></td>
                                        <td data-bind="text: curr_format((paid_principal)*1)"></td>
                                        <td data-bind="text: curr_format(round((parseFloat(expected_principal)-parseFloat(paid_principal)),2))"></td>
                                      </tr>
                                    </tbody>
                                </table>
                                <!--/ko -->
                                <?php
                                $this->load->view('client_loan/loan_steps_files/disbursement_sheet.php'); 
                            } ?>
                        <!--/ko -->
                        <?php } ?>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Loan Purpose<span class="text-danger">*</span></label>
                            <div class="col-lg-6 form-group">
                                <textarea class="form-control" rows="5"  required  name="loan_purpose" ></textarea>
                            </div>
                        </div><!--/row -->
                        <?php if( isset($case2) && $case2 =='My Loans'){
                            $this->load->view('client_loan/loan_steps_files/loan_charge_fees.php'); 
                        } ?>                        
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Comment</label>
                            <div class="col-lg-6 form-group">
                                <textarea class="form-control" rows="5"  name="comment" ></textarea>
                            </div>
                        </div><!--/row -->
                    </section>
                        </form>
                </div>
        </div>
    </div>
</div>
