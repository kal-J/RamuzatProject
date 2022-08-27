<style type="text/css">
    section{
    overflow-y : auto;
    }
    /*.inmodal.modal-header{
        padding: 0px 15px;
        text-align: center;
        display: block;
    }

    .modal-body{
        padding: 20px 10px 0px 10px;
    }*/
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
<div class="modal bd-example-modal-xl inmodal fade" id="add_client_loan-modal" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
          
                <div class="modal-header" style="padding: 0px 15px; text-align: center; display: block;">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modal_title)) {
                            echo $modal_title;
                        } else {
                            echo "New Loan";
                        }
                        ?> Application Form</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>

                <div class="modal-body" style="padding: 20px 10px 0px 10px;"> 
                <form method="post" class="formValidate wizard-big" id="formClient_loan1" action="<?php 
                if( isset($case2) && $case2 =='My Loans'){
                    echo base_url('u/loans/Create2');
                }else{ 
                    echo base_url('client_loan/Create2'); 
                } ?>" enctype="multipart/form-data">
                    <h1>Loan Application <small>Details</small></h1> <!-- Step one -->
                    <section>
                        <input type="hidden" name="topup_application" value="0">
                        <?php 
                        if( isset($case2) && $case2 =='My Loans'){
                            $this->load->view('client_loan/loan_steps_files/member_loan_application.php'); 
                        }else{ 
                            $this->load->view('client_loan/loan_steps_files/loan_application_details.php'); 
                        }?>
                    </section>
                    <!-- ko if: parseInt(top_up_application()) ===0 -->
                    <!-- /ko -->
                        <h1>Security, Fees & Loan Docs</h1><!-- Step three -->
                        <section class="section">
                            <?php $this->load->view('client_loan/loan_steps_files/security_fees.php'); ?>
                        </section>

                        <!-- <h1>Incomes, Expenses & Loan Docs</h1> Step two --> 
                        <!-- <section class="section"> -->
                            <?php //$this->load->view('client_loan/loan_steps_files/income_and_expense.php'); ?>
                        <!-- </section> -->

                    <h1>Finish</h1>
                    <section>
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
