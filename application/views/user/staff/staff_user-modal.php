<div class="modal inmodal fade" id="add_staff-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post" class="formValidate" action="<?php echo base_url(); ?>staff/Create" id="formStaff">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "Add New Staff";
                        }
                        ?></h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id"><input type="hidden" name="user_id">
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Staff No<span class="text-danger">*</span></label>
                            <div class="col-lg-2 form-group">
                                <input class="form-control" required name="client_no" value="<?php echo $new_staff_no !== FALSE?$new_staff_no:"";?>" type="text" <?php echo $new_staff_no !== FALSE?"readonly":"";?> />
                            </div>
                            <label class="col-lg-2 col-form-label">Salutation<span class="text-danger">*</span></label>
                            <div class="col-lg-2 form-group" >
                                <select class="form-control" name="salutation" required>
                                    <option value="" selected>--Select--</option>
                                   <option value="Mr" selected>Mr</option>
                                    <option value="Mrs">Mrs</option>
                                    <option value="Ms">Ms</option>
                                    <option value="Miss">Miss</option>
                                    <option value="Dr">Dr</option>
                                    <option value="Sr">Sr</option>
                                    <option value="Rev">Rev</option>
                                    <option value="Canon">Canon</option>
                                </select>
                            </div>

                            <label class="col-lg-2 col-form-label">First Name<span class="text-danger">*</span></label>
                            <div class="col-lg-2 form-group" >
                                <input placeholder="" required class="form-control" name="firstname" type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Last Name<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <input placeholder="" required class="form-control" name="lastname" type="text">
                            </div>

                            <label class="col-lg-2 col-form-label">Other Name(s)</label>
                            <div class="col-lg-4 form-group">
                                <input placeholder="" class="form-control" name="othernames" type="text"> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Gender<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <center>
                                    <label> <input checked value="1" name="gender" type="radio" > Male</label>
                                    <label> <input value="0" name="gender" type="radio"> Female</label>
                                </center>
                            </div>
                            <label class="col-lg-2 col-form-label">Marital Status<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <select class="form-control m-b" name="marital_status_id" required>
                                    <option value="">-- select --</option>
                                    <?php
                                    foreach ($marital_statuses as $marital_status) {
                                        echo "<option value='" . $marital_status['id'] . "'>" . $marital_status['marital_status_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Date Of Birth<span class="text-danger"></span></label>

                            <div class="col-lg-4 form-group">
                                <div class="input-group date" data-date-end-date="-15y">
                                    <input class="form-control"  autocomplete="off"  name="date_of_birth" type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>

                            <label class="col-lg-2 col-form-label">Disability?<span class="text-danger">*</span></label>

                            <div class="col-lg-4 form-group">
                                <center>
                                    <label><input checked value="0" name="disability" type="radio" > No</label>
                                    <label> <input value="1" name="disability" type="radio"> Yes</label>
                                </center>
                            </div>

                        </div>


                        <div class="form-group row">                            
                            <?php if(!isset($user['id'])){ ?>
                            <label class="col-lg-2 col-form-label">Contact<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group" >
                                <input name="mobile_number" type="tel" class="form-control" placeholder="phone number" pattern="^(0|\+256)[2347]([0-9]{8})" data-pattern-error="Wrong number format, start with + 0" required /> 
                            </div>
                            <?php } ?>
                            <label class="col-lg-2 col-form-label">Email</label>

                            <div class="col-lg-4 form-group">
                                <input placeholder="" class="form-control" name="email" type="email" autocomplete="off" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Number of Children</label>

                            <div class="col-lg-4">
                                <input class="form-control" name="children_no" type="number" min="0">
                            </div>

                            <label class="col-lg-2 col-form-label">Number of dependants</label>

                            <div class="col-lg-4">
                                <input class="form-control" name="dependants_no" type="number" min="0">
                            </div>
                        </div>

                        <div class="form-group row">

                            <label class="col-lg-2 col-form-label">CRB Card No.</label>

                            <div class="col-lg-4 form-group">
                                <input placeholder="" class="form-control" name="crb_card_no" type="text">
                            </div>
                            <label class="col-lg-2 col-form-label">Position<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
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
                        <?php if(!isset($user['id'])){ ?>
                        <div class="form-group row">
                        <label class="col-lg-2 col-form-label">New Password<span class="text-danger">*</span></label>
                        <div class="col-lg-4 form-group">
                            <input type="password" name="password"  placeholder="*********" id="password" class="form-control m-b" required autocomplete="off" />
                        </div>
                        <label class="col-lg-2 col-form-label">Confirm password<span class="text-danger">*</span></label>
                        <div class="col-lg-4 form-group">
                            <input type="password" name="confirmpassword" placeholder="*********" id="confirmpassword" class="form-control m-b" required autocomplete="off" />
                        </div>					
                        </div>
                        <?php } ?>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Comment</label>
                            <div class="col-lg-10 form-group">
                                <textarea class="form-control" rows="3" name="comment" id="comment"></textarea>
                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                        <?php if ((in_array('1', $staff_privilege)) || (in_array('3', $staff_privilege))) { ?>
                            <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                            <i class="fa fa-check"></i> <?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            } else {
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
