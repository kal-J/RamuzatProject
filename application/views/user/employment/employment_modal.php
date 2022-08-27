<!-- datecreated == int
date modified == datetime, on update updte_current_timestamp -->
<!-- bootstrap modal -->
<div class="modal inmodal fade" id="add_employment-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url();?>index.php/employment/Create" id="formEmployment">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title">Employment History</h3>
                <small class="font-bold">Please Make sure you enter all the required fields correctly</small>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id" >
                <input type="hidden" name="user_id" id="user_id" value="<?php echo isset($user['user_id'])? $user['user_id']:'';?>">

                    <div class="row">
                        <div class="col">
                            <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Position</label> 
                                <div class="input-group">
                                <input type="text" name="position" id="position" class="form-control m-b" required="required">
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Employer</label> 
                                <div class="input-group">
                                <input type="text" name="employer" id="employer" class="form-control m-b" required="required">
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Nature of employment</label>
                                    <div class="input-group"><select class="form-control m-b" name="nature_of_employment_id" required>
                                        <option selected value="" >Please select Nature of work</option>
                                           <?php
                                           foreach($nature_of_employment as $value){
                                              echo "<option value='".$value['id']."'>".$value['name']."</option>";
                                           }
                                           ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                        <span class="col-sm-12 form-group row">
                         <input type="checkbox" class="pull-left" data-bind="checked: checkbox, click: oncheck" />&nbsp;Please check this box if this is your current address
                         </span>
                    </div>
                    <div class="row">

                        <div class="col">
                            <div class="form-group">
                                <label class="col-xxl-4 col-form-label">Start date</label>
                                <div class="input-group date" data-date-end-date="+0d">
                                    <input class="form-control" onkeydown="return false" autocomplete="off" required  name="start_date" type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>

                        <!--ko ifnot:checkbox-->
                        <div class="col">
                            <div class="form-group">
                                <label class="col-xxl-4 col-form-label">End date</label>
                                <div class="input-group date" data-date-end-date="+0d">
                                    <input type="text" onkeydown="return false" data-bind="datepicker: $root.end_date"  autocomplete="off" class="form-control" name="end_date"><span  data-bind="datepicker: $root.end_date" class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <!--/ko-->
                        <div class="col">
                            <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Monthly salary</label> 
                                <div class="input-group">
                                <input type="text" autocomplete="off" name="monthly_salary" id="monthly_salary" class="form-control m-b" required="required">
                                </div>
                           </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    
                    <div class="hr-line-dashed"></div>
            </div>
                    <div class="modal-footer">
                    <?php if((in_array('1', $member_staff_privilege))||(in_array('3', $member_staff_privilege))){ ?>
                        <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                            <i class="fa fa-check"></i> Save</button>
                    <?php } ?>
                        <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                            <i class="fa fa-times"></i> Cancel</button>
                        </div>
            
        </form>
        </div>
    </div>
</div>
<!-- bootstrap modal ends -->
