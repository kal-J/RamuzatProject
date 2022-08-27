<!-- add bootstrap modal -->
<div class="modal inmodal fade" id="add_children-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <?php echo form_open_multipart("children/create", array('id' => 'formChildren', 'class' => 'formValidate', 'name' => 'formChildren', 'method' => 'post', 'role' => 'form')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title">Guardian</h3>
                <small class="font-bold">Please Make sure you enter all the required fields correctly</small>
            </div>
            <div class="modal-body">
                <input type="hidden"  name="id" id ="id">
                <input type="hidden"  name="member_id" id ="member_id" value="<?php echo $user['id']; ?>">
                <div class="row">
                    <div class="col">
                        <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">First name</label> 
                            <div class="input-group">
                                <input type="text" name="firstname" id="firstname" class="form-control m-b" required="required">
                            </div>
                            <span  class="help-block with-errors" aria-hidden="true"></span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Last name</label> 
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
                        <div class="form-group row m-xxs"><label class="col-xxl-3 col-form-label">Gender</label>
                            <div class="input-group"><select class="form-control m-b" name="gender" id="gender">
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
                            <label class="col-lg-5 col-form-label">Date Of Birth<span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-lg-9">
                                    <div class="input-group date" data-date-end-date="+0d">
                                        <input class="form-control"  onkeydown="return false" autocomplete="off" name="date_of_birth" type="text" data-bind="value: date_of_birth" required><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <span data-bind="text: typeof date_of_birth() !== 'undefined'?(moment().diff(moment(date_of_birth(), 'DD-MM-YYYY'),'years')+'yrs'):''"> </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                <?php if ((in_array('1', $member_privilege)) || (in_array('3', $member_privilege))) { ?>
                    <button type="submit" class="btn btn-success btn-sm save_data"><?php
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
<!-- bootstrap modal ends -->
