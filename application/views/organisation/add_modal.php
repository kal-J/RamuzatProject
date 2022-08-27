<div id="organisation-modal" class="modal fade" role="dialog" aria-labelledby="modal_branch" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h3>Organisation</h3>
                        <div class="ibox-tools">
                            <a data-dismiss="modal" class="close">&times;</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <?php echo form_open_multipart("Organisation/create", array('id' => 'formOrganisation', 'class' => 'formValidate', 'name' => 'formOrganisation', 'data-toggle' => 'validator', 'role' => 'form')); ?>
                        <input type="hidden" name="id" id="id" >
                        <!--input type="hidden" name="tbl" id="tbl" value="tblBranch" -->
                        
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label"> Organisation </div>
                            <div class="col-sm-8">
                                <input name="name" id="name" class="form-control" placeholder="Name of the organisation" required />
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label"> Initial </div>
                            <div class="col-sm-8">
                                <input name="org_initial" id="org_initial" class="form-control" placeholder="organisation initial" required />
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="hr-line-dashed"></div>
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label"> Description</div>
                            <div class="col-sm-8">
                                <input name="description" id="description" type="tel" class="form-control" placeholder="Description" required />
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="hr-line-dashed"></div>
                        <h4><center>Loan Application Settings </center></h4>
                        <div class="form-group row">
                            <label class="col-lg-5 col-form-label">Loan Application Flow </label>
                            <div class="col-lg-7 form-group">
                                <select name="loan_app_stage"  class="form-control required">
                                <option value=''>--Select-- </option>  
                                <option value='0'> Application => Pending Approval </option>    
                                <option value='1'> Application => Approved </option>
                                <option value='2' > Application => Disburse </option>
                                </select>
                            </div> 
                            <label class="col-lg-5 col-form-label">Member Maximum Loans to Guarantee</label> 
                            <div class="col-lg-7 form-group">
                                <input class="form-control" min="1" type="number" name="max_loans_to_guarantee" required>
                            </div>    
                        </div>
                     
                        <div class="hr-line-dashed"></div>
                        <h4><center>Client Registration Settings </center></h4>
                        <small>Please select whether to include the following </small>
                        
                        <div class="form-group row">
                        <table width="100%">
                            <tr>
                            <td><label class=" form-label"> Children </label></td>
                            <td> Yes <input type="radio" name="children_comp" required value="1" > 
                            No <input type="radio" name="children_comp" required value="0">
                                </td>
                            </tr>
                            <tr>
                            <td><label class="form-label"> Next of Kin </label></td>
                            <td> Yes <input type="radio" name="nextofkin_comp"  required  value="1"> 
                                No <input type="radio" name="nextofkin_comp"  required value="0">
                                </td>
                            </tr>
                            <tr>
                            <td><label class=" form-label"> Employment History  </label></td>
                            <td> Yes <input type="radio" name="employ_hist_comp" required value="1"> 
                                No <input type="radio" name="employ_hist_comp" required value="0">
                                </td>
                            </tr>
                            <tr>
                            <td><label class="form-label"> Business </label></td>
                            <td> Yes <input type="radio" name="business_comp" required value="1"> 
                                No <input type="radio" name="business_comp" required  value="0">
                                </td>
                            </tr>
                            <tr>
                            <td><label class="form-label">Allow Online Loan Application? </label></td>
                            <td> Yes <input type="radio" name="loan_app_comp" required value="1"> 
                                No <input type="radio" name="loan_app_comp" required value="0">
                                </td>
                            </tr>
                            <tr>
                            <td><label class="form-label">Mobile Money Payments? </label></td>
                            <td> Yes <input type="radio" name="mobile_payments" required value="1"> 
                                No <input type="radio" name="mobile_payments" required value="0">
                                </td>
                            </tr>
                            <tr>
                            <td><label class="form-label">Loan Funds to Savings A/C? </label></td>
                            <td> Yes <input type="radio" name="loans_to_savings" required value="1"> 
                                No <input type="radio" name="loans_to_savings" required value="0">
                                </td>
                            </tr>
                            <tr>
                            <td><label class="form-label">Deduct Disbursed Loan from Savings A/C? </label></td>
                            <td> Yes <input type="radio" name="deduct_loan" required value="1"> 
                                No <input type="radio" name="deduct_loan" required value="0">
                                </td>
                            </tr>
                            <tr>
                            <tr>
                            <td><label class="form-label">Pay Fees With Savings and Cash</label></td>
                            <td> Yes <input type="radio" name="loan_fees_payment_method" required value="1"> 
                                No <input type="radio" name="loan_fees_payment_method" required value="0">
                                </td>
                            </tr>
                            <tr>
                            <td><label class="form-label">Allow Savings as shares? </label></td>
                            <td> Yes <input type="radio" name="savings_shares" required value="1"> 
                                No <input type="radio" name="savings_shares" required value="0">
                                </td>
                            </tr>
                            <tr>
                            <td><label class="form-label">Loan Curtailment? </label></td>
                            <td> Yes <input type="radio" name="loan_curtailment" required value="1"> 
                                No <input type="radio" name="loan_curtailment" required value="0">
                                </td>
                            </tr>
                            <tr>
                            <td><label class="form-label">Deduct Loan Fees from Loan? </label></td>
                            <td> Yes <input type="radio" name="deduct_loan_fees_from_loan" required value="1"> 
                                No <input type="radio" name="deduct_loan_fees_from_loan" required value="0">
                                </td>
                            </tr>
                            <tr>
                            <td><label class="form-label">Allow Member Referral? </label></td>
                            <td> Yes <input type="radio" name="member_referral" required value="1"> 
                                No <input type="radio" name="member_referral" required value="0">
                                </td>
                            </tr>
                            <tr>
                            <td><label class="form-label">Exclude Current Installment Interest on payoff? </label></td>
                            <td> Yes <input type="radio" name="no_current_interest" required value="1"> 
                                No <input type="radio" name="no_current_interest" required value="0">
                                </td>
                            </tr>
                            <tr>
                            <td><label class="form-label">Charge Loan Termination fees on Top Up ? </label></td>
                            <td> Yes <input type="radio" name="topup_loan_termination_fees" required value="1"> 
                                No <input type="radio" name="topup_loan_termination_fees" required value="0">
                                </td>
                            </tr>
                        </table>
                        </div>
                        <div class="hr-line-dashed"></div>
                         <div class="hr-line-dashed"></div>
                        <h4><center>Authentication </center></h4>
                        <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Two-Factor (2FA) </label>
                            <div class="col-lg-3 form-group">
                                <select id="2factor" name="two_factor"  class="form-control required" >
                                <option value='0'>No </option>    
                                <option value='1'>Yes</option>
                                </select>
                            </div>     
                            <label id="states_name" class="col-lg-3 col-form-label">Authenticate With</label>
                            <div class="col-lg-3 form-group">
                                <select id="states" name="two_factor_choice"  class="form-control required">
                                <option value=''>--Select--</option>    
                                <option value='1'>SMS </option>    
                                <option value='2'>Email</option>
                                </select>
                            </div>    
                        </div>
                         <div class="row">
                        <div class="col">
                                <div class="form-group row m-xxs"><label class="col-xxl-8 col-form-label">Organisation Logo</label> 
                                    <div class="custom-file">
                                        <input id="organisation_logo" name="organisation_logo"  type="file" class="custom-file-input" >
                                        <label class="custom-file-label">Choose file...</label>
                                    </div>
                                    <script>
                                    $('.custom-file-input').on('change', function() {
                                    let fileName = $(this).val().split('\\').pop();
                                    $(this).next('.custom-file-label').addClass("selected").html(fileName);
                                    }); 
                                    </script>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        
                        <div class="form-group row">
                            <div class="col-sm-12 pull-right">
                            <?php if((in_array('1', $privileges))||(in_array('3', $privileges))){ ?>
                                <button id="btn-submit" name="btn_submit" class="btn btn-primary pull-right btn-sm save_data">
                                    <i class="fa fa-check"></i> Save</button>
                            <?php } ?>
                            </div>
                        </div>
                        </form>
                    </div><!--/.ibox-content -->
                </div><!--/ibox -->
            </div><!--/modal-body -->
        </div><!--/modal-content -->
    </div><!--/col-lg-6 -->
</div><!--/add_organisation-modal -->