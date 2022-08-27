<!-- bootstrap modal -->
<div class="modal inmodal fade" id="assign_moduleprivilege-modal" tabindex="-1" privilege="dialog"  aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <?php echo form_open_multipart("moduleprivilege/create", array('id' => 'formModulePrivilege', 'class' => 'formValidate','method' => 'post','name' => 'formModulePrivilege', 'data-toggle' => 'validator', 'role' => 'form')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">X</span></button>
                <h4 class="modal-title">Define <?php echo $module['module_name']; ?> Privileges </h4>
                 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
                <input type="hidden" name="module_id" value="<?php echo $module['id']; ?>">
                <div class="form-group row">
                   <label class="col-lg-3 col-form-label">Privilege Code</label>
                        <div class="col-lg-9 form-group">
                    
                          <input class="form-control" type="number" name="privilege_code" required >
                        </div>      
                </div>
                
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Short Description</label>
                        <div class="col-lg-9 form-group">
                              <textarea class="form-control" required rows="1" name="description" id="description"></textarea>
                            </div>                                                  
                    </div>                       
                </div>
                <div class="modal-footer">
                <?php if((in_array('1', $privileges))||(in_array('3', $privileges))){ ?>
                    <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                        <i class="fa fa-check"></i> <?php
                                            if (isset($saveButton)) {
                                                echo $saveButton;
                                            }else{
                                                echo "Save";
                                            }
                                         ?></button>
                    <?php } ?>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                    </div>
        </form>
        </div>
    </div>
</div>