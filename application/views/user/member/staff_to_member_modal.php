<div class="modal inmodal fade" id="add_staff-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <form method="post" class="formValidate" action="<?php echo site_url("member/make_staff"); ?>" id="formStaff">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "Register as Staff";
                        }
                        ?></h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                <input type="hidden" name="id">
                <input type="hidden" name="user_id">
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">Client No:</label>
                            <div class="col-lg-8 form-group" >
                                <input placeholder="" disabled   class="form-control" name="client_no" type="text">
                            </div>
                        </div>
                       
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">Position<span class="text-danger">*</span></label>
                            <div class="col-lg-8 form-group">
                                <select id='position_id' class="form-control" name="position_id" required>
                                    <option selected value="" >Please select position</option>
                                    <?php
                                    foreach ($positions as $position) {
                                        echo "<option value='" . $position['id'] . "'>" . $position['position'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">System Role<span class="text-danger">*</span></label>
                            <div class="col-lg-8 form-group">
                                <select id='role_id' class="form-control" name="role_id" required>
                                    <option selected value="" >Select system role</option>
                                    <?php
                                    foreach($roles as $role){
                                      echo "<option value='".$role['id']."'>".$role['role']."</option>";
                                   }
                                    ?>
                                </select>
                            </div>
                        </div>

                     
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">Comment</label>
                            <div class="col-lg-8 form-group">
                                <textarea class="form-control" rows="2" name="comment" id="comment"></textarea>
                            </div>
                        </div>
                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                        <?php if ((in_array('1', $member_privilege)) || (in_array('3', $member_privilege))) { ?>
                        <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data"><i class="fa fa-check"></i><?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            } else {
                                echo "Save";
                            }
                            ?></button>
                   <?php } ?>
                        
                </div>
            </form>
        </div>
    </div>
</div>
