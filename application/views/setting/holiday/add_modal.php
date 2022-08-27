<!-- bootstrap modal -->
<div class="modal inmodal fade" id="add_holiday-modal" tabindex="-1" privilege="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url();?>holiday/create" id="formHoliday">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">X</span></button>
                <h3 class="modal-title">Add Holiday</h3>
                 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id">
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">Holiday Name<span class="text-danger">*</span></label>
                    <div class="col-lg-4 form-group">
                    <input placeholder="" required class="form-control" name="holiday_name" type="text">
                    </div>
                    <label class="col-lg-2 col-form-label">Holiday Date</label>
                        <div class="col-lg-4 form-group">
                          <div class="input-group date">
                            <input class="form-control" required name="holiday_date" type="text" ><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          </div>
                      </div>                                                  
                    </div>

                    <fieldset class="col-lg-12">     
                        <legend>Frequecy Description</legend>
                        <div class="form-group row">      
                            <label class="col-lg-1 col-form-label">Every<span class="text-danger">*</span></label>
                            <div class="col-lg-3 form-group" >
                                <select id='frequency_every_id' class="form-control" name="frequency_every_id" required>
                                    <option selected value="" >---Select---</option>
                                   <?php
                                   foreach($holiday_frequency_every as $value){
                                      echo "<option value='".$value['id']."'>".$value['every']."</option>";
                                   }
                                   ?>
                                 </select>
                            </div>

                            <label class="col-lg-1 col-form-label">Day<span class="text-danger">*</span></label>
                            <div class="col-lg-3 form-group" >
                                <select id='frequency_day_id' class="form-control" name="frequency_day_id" required>
                                    <option selected value="" >---Select---</option>
                                   <?php
                                   foreach($holiday_frequency_day as $value){
                                      echo "<option value='".$value['id']."'>".$value['day']."</option>";
                                   }
                                   ?>
                                 </select>
                            </div>
                            <label class="col-lg-1 col-form-label">Of<span class="text-danger">*</span></label>
                            <div class="col-lg-3 form-group" >
                                <select id='frequency_of_id' class="form-control" name="frequency_of_id" required>
                                    <option selected value="" >---Select---</option>
                                   <?php
                                   foreach($holiday_frequency_of as $value){
                                      echo "<option value='".$value['id']."'>".$value['month']."</option>";
                                   }
                                   ?>
                                 </select>
                            </div>
                        </div>                            
                    </fieldset><!--/col-lg-12 -->                        
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-check"></i> <?php
                                            if (isset($saveButton)) {
                                                echo $saveButton;
                                            }else{
                                                echo "Save";
                                            }
                                         ?></button>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                    </div>
        </form>
        </div>
    </div>
</div>
