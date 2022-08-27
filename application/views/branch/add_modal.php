<div id="add_branch-modal" class="modal fade" role="dialog" aria-labelledby="modal_branch" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h3>Add Branch  </h3>
                        <div class="ibox-tools">
                            <a data-dismiss="modal" class="close">&times;</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <?php echo form_open_multipart("branch/create", array('id' => 'formBranch', 'class' => 'formValidate', 'name' => 'formBranch', 'data-toggle' => 'validator', 'role' => 'form')); ?>
                        <input type="hidden" name="id" id="id" >
                        <input type="hidden" name="organisation_id" id="organisation_id" value="<?php echo $_SESSION['organisation_id']; ?>" >
                        <!--input type="hidden" name="tbl" id="tbl" value="tblBranch" -->
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label" >Branch Code
                            </div>
                            <div class="col-sm-8">
                                <input type="text" name="branch_number" id="branch_number" class="form-control" placeholder="Branch code" required/>
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="hr-line-dashed"></div>
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label"> Branch Name</div>
                            <div class="col-sm-8">
                                <input name="branch_name" id="branch_name" class="form-control" placeholder="Name of the branch" required />
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="hr-line-dashed"></div>
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label"> Phone</div>
                            <div class="col-sm-8">
                                <input name="office_phone" id="office_phone" type="tel" class="form-control" placeholder="Branch phone contact" pattern="^(0|\+\d{1,4})(\d{8,11})" data-pattern-error="Wrong number format, start with + 0" required />
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="hr-line-dashed"></div>
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label"> Email</div>
                            <div class="col-sm-8">
                                <input name="email_address" id="email_address" type="email" class="form-control" placeholder="Branch email contact" required />
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="hr-line-dashed"></div>
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label">Postal Address
                            </div>
                            <div class="col-sm-8">
                                <textarea name="postal_address" id="postal_address" class="form-control" placeholder="Postal address" required ></textarea>
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="hr-line-dashed"></div>
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label">Physical Address
                            </div>
                            <div class="col-sm-8">
                                <textarea name="physical_address" id="physical_address" class="form-control" placeholder="Physical address" required ></textarea>
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label"> </div>
                            <div class="col-sm-8 input-group">
                                <input type="checkbox" name="main_branch" id="main_branch" value="1" class="form-control" /> <span> Check this if it's the Main Branch </span>
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
            </div><!--/modal-body -->
        </div><!--/modal-content -->
    </div><!--/col-lg-6 -->
</div><!--/add_branch-modal -->
