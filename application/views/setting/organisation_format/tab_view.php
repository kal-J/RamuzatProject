<div class="tab-pane" style="margin-top:10px;" id="tab_account_format">
                <div class="ibox ">
                    <div class="ibox-title">
                        <div class="row">
                        <div class="col-sm-4"><h3>&nbsp;Account</h3></div>
                        <div class="col-sm-4"><h3>&nbsp;Initials</h3></div>
                        <div class="col-sm-4"><h3>Number</h3></div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <?php echo form_open("organisation_format/set_formats", array('id' => 'formNumFormats', 'class' => 'formValidate', 'name' => 'formNumFormats', 'data-toggle' => 'validator', 'role' => 'form')); ?>
                        <input type="hidden" name="id" id="id" >
                        <input type="hidden" name="id" id="organisation_id" value="<?php echo $_SESSION['organisation_id']; ?>" >
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label" >SAVINGS ACCOUNT Number
                            </div>
                            
                             <div class="col-sm-3">

                                <input type="text" name="account_format_initials" id="account_format_initials" value="<?php echo $format_types['account_format_initials']; ?>" class="form-control" placeholder="Account Initials"/>
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                             
                            <div class="col-sm-5">
                                <input type="text" name="account_format" id="account_format" value="<?php echo $format_types['account_format']; ?>" class="form-control" placeholder="Savings Account Format Initials"/>
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label">FIXED DEPOSIT ACCOUNT Number
                            </div>
                              <div class="col-sm-3">
                                <input type="text" name="fixed_dep_format_initials" id="fixed_dep_format_initials" value="<?php echo $format_types['fixed_dep_format_initials']; ?>" class="form-control" placeholder="Account Initials"/>
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                            <div class="col-sm-5">
                                <input type="text" name="fixed_dep_format" id="fixed_dep_format" value="<?php echo $format_types['fixed_dep_format']; ?>" class="form-control" placeholder="Fixed Deposit Account No. Format"/>
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label"> LOAN ACCOUNT Number</div>
                             <div class="col-sm-3">
                                <input name="loan_format_initials" id="loan_format_initials" value="<?php echo $format_types['loan_format_initials']; ?>" class="form-control" placeholder="Account Initials" />
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                            <div class="col-sm-5">
                                <input name="loan_format" id="loan_format" value="<?php echo $format_types['loan_format']; ?>" class="form-control" placeholder="Loan Account No. Format" />
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label">GROUP LOAN Number</div>
                              <div class="col-sm-3">
                                <input name="group_loan_format_initials" id="group_loan_format_initials" value="<?php echo $format_types['group_loan_format_initials']; ?>" class="form-control" placeholder="Account Initials" />
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                            <div class="col-sm-5">
                                <input name="group_loan_format" id="loan_format" value="<?php echo $format_types['group_loan_format']; ?>" class="form-control" placeholder="Group Loan No. Format" />
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label"> CLIENT Number</div>
                              <div class="col-sm-3">
                                <input name="client_format_initials" id="client_format_initials" type="text" value="<?php echo $format_types['client_format_initials']; ?>" class="form-control" placeholder="Account Initials" />
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                            <div class="col-sm-5">
                                <input name="client_format" id="client_format" type="text" value="<?php echo $format_types['client_format']; ?>" class="form-control" placeholder="Client No. Format" />
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label"> STAFF Number</div>
                             <div class="col-sm-3">
                                <input name="staff_format_initials" id="staff_format_initials" type="text" value="<?php echo $format_types['staff_format_initials']; ?>" class="form-control" placeholder="Account Initials" />
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                            <div class="col-sm-5">
                                <input name="staff_format" id="staff_format" type="text" value="<?php echo $format_types['staff_format']; ?>" class="form-control" placeholder="Staff Number Format" />
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label">GROUP Number
                            </div>
                             <div class="col-sm-3">
                                <input name="group_format_initials" id="group_format_initials" value="<?php echo $format_types['group_format_initials']; ?>" class="form-control" placeholder="Account Initials" />
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                            <div class="col-sm-5">
                                <input name="group_format" id="group_format" value="<?php echo $format_types['group_format']; ?>" class="form-control" placeholder="Group No. Format" />
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label">SHARE ACCOUNT Number
                            </div>
                             <div class="col-sm-3">
                                <input type="text" name="share_format_initials" id="share_format_initials" value="<?php echo $format_types['share_format_initials']; ?>" class="form-control" placeholder="Account Initials"/>
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                            <div class="col-sm-5">
                                <input type="text" name="share_format" id="share_format" value="<?php echo $format_types['share_format']; ?>" class="form-control" placeholder="Share No. Format"/>
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label">PARTNER ACCOUNT Number
                            </div>

                            <div class="col-sm-8">
                                <input type="text" name="partner_format" id="partner_format" value="<?php echo $format_types['partner_format']; ?>" class="form-control" placeholder="Partner No. Format"/>
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="hr-line-dashed"></div>
                        <div class="form-group row">
                            <div class="col-sm-4 col-sm-offset-3">
                            <?php if((in_array('1', $privileges))||(in_array('3', $privileges))){ ?>
                                <button id="btn-submit" name="btn_submit" class="btn btn-primary btn-sm save_data">
                                    <i class="fa fa-check"></i> Save</button>
                            <?php } ?>
                                <button type="button" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm cancel">
                                    <i class="fa fa-times"></i> Cancel</button>
                            </div>
                        </div>
                       
                        </form>
                    </div><!--/.ibox-content -->
                </div><!--/ibox -->
</div>