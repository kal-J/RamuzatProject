<div class="modal inmodal fade" id="add_member-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post" class="formValidate" action="<?php echo site_url("staff/make_member"); ?>" id="formMember">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "Register as Member";
                        }
                        ?></h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                <input type="hidden" name="id">
                <input type="hidden" name="user_id">
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Staff No:</label>
                            <div class="col-lg-4 form-group" >
                                <input placeholder="" disabled   class="form-control" name="staff_no" type="text">
                            </div>
                        </div>
                       
                        <div class="form-group row">
                            
                            <label class="col-lg-2 col-form-label">Occupation<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group" >
                                <input placeholder="Occupation" required class="form-control" name="occupation" type="text"> 
                            </div>
                             <?php  if(in_array('9', $modules)){ ?>
                            <label class="col-lg-2 col-form-label">Subscription Plan</label>
                            <div class="col-lg-4 form-group" >
                                <select class="form-control" name="subscription_plan_id" id="subscription_plan_id">
                                        <option value="">--select--</option>
                                        <?php foreach ($subscription_plans as $subscription_plan):?>
                                        <option value="<?php echo $subscription_plan['id']; ?>"><?php echo $subscription_plan['plan_name']; ?></option>
                                            <?php endforeach;?>
                                    </select>
                            </div>
                        <?php } ?>
                        </div>
                        <div class="form-group row">

                            <label class="col-lg-2 col-form-label">Date Registered<span class="text-danger">*</span></label>

                            <div class="col-lg-4 form-group" data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>"   >
                                <div class="input-group date" data-date-end-date="+0d">
                                    <input  type="text"  onkeydown="return false" class="form-control" name="date_registered" value="<?php echo mdate("%d-%m-%Y"); ?>" required/><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>

                            </div>

                            <label class="col-lg-2 col-form-label">Registered By<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group" >
                                <select id='registered_by' class="form-control" name="registered_by" required>
                                    <option  value="" >select staff member</option>
                                    <?php
                                    foreach ($staff_list as $staff) {
                                    ?>
                                        <option <?php if($staff['id']==$_SESSION['id']){ echo "selected"; } ?> value="<?php echo $staff['id']; ?>"><?php echo $staff['salutation'] . ". " . $staff['firstname'] . " " . $staff['lastname'] . " " . $staff['othernames']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                       
                    
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Comment</label>
                            <div class="col-lg-10 form-group">
                                <textarea class="form-control" rows="2" name="comment" id="comment"></textarea>
                            </div>
                        </div>
                </div>

                <div class="modal-footer">
                        <?php if ((in_array('1', $member_privilege)) || (in_array('3', $member_privilege))) { ?>
                        <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data"><i class="fa fa-check"></i><?php
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
