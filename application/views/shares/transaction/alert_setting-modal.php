<!-- bootstrap modal -->
<div class="modal inmodal fade" id="alert_setting-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" enctype="multipart/form-data" action="<?php echo base_url();?>alert_setting/create" id="formAlert_setting">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">X</span></button>
                <h3 class="modal-title">New Alert Setting</h3>
                 <small class="font-bold">Note: Required fields are marked with<span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
              <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Alert Method<span class="text-danger">*</span></label>
                    <div class="col-lg-3 form-group">
                    <div>
                     <select name="alert_method" class="form-control" required>
                         <option value="1">Email</option>
                         <option value="2">SMS</option>
                         <option value="2">Both</option>
                         
                     </select>
                       </div>
                    </div>

                    <label class="col-lg-2 col-form-label">Alert Type<span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <select  class="form-control" id="alert_type" name="alert_type"   style="width: 100%" required >
                            <option selected disabled>--select--</option>
                            <?php foreach($alert_types as $row){
                                echo $row['id'] ;?>
                                <option value="<?php echo  $row['id']?>"><?php echo  $row['alert_type']?></option>
                            <?php }?>

                        </select>
                        <input type="hidden" name="alert_id" value="<?php //echo  $row['id']?>">
                    </div>
                </div>
                    
                    <div class="form-group row">
                    <label class="col-lg-3 col-form-label">No. of days to due date<span class="text-danger">*</span></label></label>
                        <div class="col-lg-3">
                        <input type="number" name="number_of_days_to_duedate" id="number_of_days_to_duedate" min="1" class="form-control" required>
                        </div>
                     <label class="col-lg-2 col-form-label">Alert Interval<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <select  class="form-control" id="interval_of_reminder" name="interval_of_reminder" data-bind='options: reminder_types, optionsText: "reminder_name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:reminder_type' data-msg-required="Reminder type  must be selected" style="width: 100%" required >
                        </select>
                        
                        </div>
                    </div> 
                          
                </div>
                <div class="modal-footer">
                <?php if(in_array('3', $share_privilege)){ ?>
                     <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data" onclick="update_alert_setting()">
                        <i class="fa fa-check"></i> Submit
                    </button>
                    <?php } ?>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                    </div>
        </form>
        </div>
    </div>
</div>
