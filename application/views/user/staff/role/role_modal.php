<!-- bootstrap modal -->
<div class="modal inmodal fade" id="add_role-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <form class="formValidate" id ="formUser_role" method="post" action="<?php echo site_url()?>User_role/create">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title">Role Assignment</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" name="staff_id" id="user_id" value="<?php echo isset($user['id'])? $user['id']:'';?>">
                    <div class="row">
                        <div class="col">
                            <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Role</label>
                                  <select id='role_id' class="form-control" name="role_id" required>
                                    <option selected>--Select --</option>
                                   <?php
                                   foreach($roles as $role){
                                      echo "<option value='".$role['id']."'>".$role['role']."</option>";
                                   }
                                   ?>
                                 </select>
                            </div>
                        </div>                               
                    </div>
                </div>
                    <div class="modal-footer"><!-- start of the modal footer -->
                    <?php if((in_array('1', $staff_privilege))||(in_array('3', $staff_privilege))){ ?>
                        <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                            <i class="fa fa-check"></i> 
                            <?php
                                if (isset($saveButton)) {
                                    echo $saveButton;
                                }else{
                                    echo "Save";
                                }
                             ?>
                        </button>
                        <?php } ?>
                        <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                            <i class="fa fa-times"></i> Cancel</button>
                    </div><!-- End of the modal footer -->
            
        </form>
        </div>
    </div>
</div>
<!-- bootstrap modal ends -->
