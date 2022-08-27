<div id="add_department-modal" class="modal fade" role="dialog" aria-labelledby="modal_department" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h3>Add Department</h3>
                        <div class="ibox-tools">
                            <a data-dismiss="modal" class="close">&times;</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <?php echo form_open_multipart("department/create", array('id' => 'formDepartment', 'class' => 'formValidate', 'name' => 'formDepartment', 'data-toggle' => 'validator', 'role' => 'form')); ?>
                        <input type="hidden" name="id" id="id" >
                        <input type="hidden" name="branch_id" id="branch_id" value="<?php echo $branch['id']?>">
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label" >Department Code
                            </div>
                            <div class="col-sm-8">
                                <input type="text" name="department_number" id="department_number" class="form-control" placeholder="Department code" required/>
                                <span  class="help-block with-errors" aria-hidden="true"></span>
                            </div>
                        </div><!--/col-lg-12 -->
                        <div class="hr-line-dashed"></div>
                        <div class="form-group  row">
                            <div class="col-sm-4 col-form-label"> Department Name</div>
                            <div class="col-sm-8">
                                <input name="department_name" id="department_name" class="form-control" placeholder="Name of the department" required />
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
            </div><!--/modal-body -->
        </div><!--/modal-content -->
    </div><!--/col-lg-6 -->
</div><!--/add_branch-modal -->
