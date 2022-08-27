<!-- bootstrap modal -->
<div class="modal inmodal fade" id="add_nextofkin-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <?php echo form_open_multipart("nextOfKin/create", array('id' => 'formNextOfKin', 'class' => 'formValidate', 'name' => 'formNextOfkin', 'method' => 'post', 'role' => 'form')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title"><?php echo $this->lang->line('cont_nextofkin'); ?></h3>
                <small class="font-bold">Please Make sure you enter all the required fields correctly</small>
            </div>
            <div class="modal-body">
                <input type="hidden"  name="id" id ="id">
                <input type="hidden"  name="user_id" id ="user_id" value="<?php echo $user['user_id']; ?>">
                <div class="row">
                    <div class="col">
                        <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">First name <span class="text-danger">*</span></label> 
                            <div class="input-group">
                                <input type="text" name="firstname" id="firstname" class="form-control m-b" required="required">
                            </div>
                            <span  class="help-block with-errors" aria-hidden="true"></span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Last name <span class="text-danger">*</span></label> 
                            <div class="input-group">
                                <input type="text" name="lastname" id="lastname" class="form-control m-b" required="required">
                            </div>
                            <span  class="help-block with-errors" aria-hidden="true"></span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row m-xxs"><label class="col-xxl-4 col-form-label">Other names</label> 
                            <div class="input-group">
                                <input type="text" name="othernames" id="othernames" class="form-control m-b">
                            </div>
                        </div>
                        <span  class="help-block with-errors" aria-hidden="true"></span>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="row">
                    <div class="col">
                        <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Gender <span class="text-danger">*</span></label>
                            <div class="input-group"><select class="form-control m-b" name="gender" id="gender" required="required">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Prefer Not Say">Prefer not Say</option>
                                </select>
                            </div>
                            <span  class="help-block with-errors" aria-hidden="true"></span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group row m-xxs">
                        <label class="col-xxl-4 col-form-label">Relationship <span class="text-danger">*</span></label> 
                        <select id='relationship' class="form-control" name="relationship" required="required" >
                         <option selected value="" >Please select </option>
                        <?php
                        if (isset($relationship_types)) {
                            foreach($relationship_types as $type){
                            echo "<option value='".$type['id']."'>".$type['relationship_type']."</option>";
                        }
                        }
                        ?>
                        </select>
                        <span  class="help-block with-errors" aria-hidden="true"></span>
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Telphone</label> 
                            <div class="input-group">
                                <input name="telphone" id="telphone" type="tel" class="form-control" placeholder="070*******"  pattern="^(0|\+256)[2347]([0-9]{8})" data-pattern-error="Wrong number format, start with + 0" />
                            </div>
                            <span  class="help-block with-errors" aria-hidden="true"></span>
                        </div>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="row">
                    <div class="col">                    
                    <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Share-Portion <span class="text-danger">*</span></label> 
                        <div class="input-group">
                            <input type="number" step="0.1" name="share_portion"  class="form-control" required="required">
                        </div>
                       <span  class="help-block with-errors" aria-hidden="true"></span>
                    </div>
                    </div>
                    <div class="col">
                        <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Address <span class="text-danger">*</span></label> 
                            <div class="input-group">
                                <textarea name="address" id="address" class="form-control" placeholder="Address" required ></textarea>
                            </div>
                           <span  class="help-block with-errors" aria-hidden="true"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <?php if((in_array('1', $member_staff_privilege))||(in_array('3', $member_staff_privilege))){ ?>
                <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                    <i class="fa fa-check"></i> Save</button>
            <?php } ?>
                <button type="button" data-dismiss="modal" name="btn_cancel" class="btn btn-danger btn-sm cancel">
                    <i class="fa fa-times"></i> Cancel</button>
            </div>

            </form>
        </div>
    </div>
</div>
<!-- bootstrap modal ends -->
