<!--div class="modal inmodal fade" id="add_address-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;" commented out as a quick fix for the select2 search functionality-->
<div class="modal inmodal fade" id="add_address-modal" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="formValidate" action="<?php echo site_url("Address/Create"); ?>" id="formAddress" method="post" name="formAddress" data-toggle ='validator'>
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="user_id" value="<?php echo (isset($user['user_id']))?$user['user_id']:''; ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">New Address</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <div class="col-sm-12 row" >
                        <div class="col-sm-6" >
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Address Type<span class="text-danger">*</span></label>
                                <div class="col-lg-8 form-group">
                                    <select class="form-control m-b" name="address_type_id" >
                                        <option value="" >--select--</option>
                                        <?php
                                        if (count($address_types) > 0) {
                                            foreach ($address_types as $key => $type_value) {
                                                $val = $type_value['id'];
                                                $type_name = $type_value['address_type_name'];
                                                ?>
                                                <option value="<?php echo $val; ?>" > <?php echo $type_name; ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select >
                                </div>

                                <label class="col-lg-4 col-form-label">Plot<span class="text-danger">*</span></label>
                                <div class="col-lg-8 form-group">						
                                    <textarea class="form-control" name="address1" rows="1" ></textarea>
                                    <span class="help-block with-errors text-danger" aria-hidden="true"></span>
                                </div>

                                <label class="col-lg-4 col-form-label">Road<span class="text-danger"></span></label>
                                <div class="col-lg-8 form-group">
                                    <textarea class="form-control" name="address2" rows="1" ></textarea>
                                    <span class="help-block with-errors text-danger" aria-hidden="true"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 border-left">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">District<span class="text-danger">*</span></label>
                                <div class="col-lg-8 form-group">						
                                    <select class="form-control m-b" id="district_id" name="district" data-bind='options: districtsList, optionsText: "district", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:district' data-msg-required="district must be selected" style="width: 100% " required>
                                        <option  value="">--select--</option>
                                    </select>
                                </div>

                                <label class="col-lg-4 col-form-label">Sub County<span class="text-danger">*</span></label>
                                <div class="col-lg-8 form-group">						
                                    <select class="form-control m-b" id="subcounty_id" name="subcounty" data-bind='options: subcountiesList, optionsText: "subcounty", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:subcounty' data-msg-required="Subcounty must be selected" style="width: 100%" required>
                                        <option  value="">--select--</option>
                                    </select>
                                </div>
                                <label class="col-lg-4 col-form-label">Parish<span class="text-danger">*</span></label>
                                <div class="col-lg-8 form-group">
                                    <select class=" form-control" id="parish_id" name="parish_id" data-bind='options: parishesList, optionsText: "parish", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:parish' data-msg-required="Parish must be selected" style="width: 100%" required>
                                        <option value="">--select--</option>
                                    </select>
                                </div>
                                <label class="col-lg-4 col-form-label">Village<span class="text-danger">*</span></label>
                                <div class="col-lg-8 form-group">						
                                    <select class="form-control m-b" id="village_id" name="village_id"  data-bind='options: villagesList, optionsText: "village", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:village' data-msg-required="Village must be selected" style="width: 100%" required>
                                        <option  value="">--select--</option>								
                                    </select>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 row border-top"> 
                                <span class="col-sm-12 form-group row">
                                 <input type="checkbox" class="pull-left" data-bind="checked: checkbox, click: oncheck" />&nbsp;Please check this box if this is your current address
                                 </span>
                        <div class="col-sm-6 border-right">
                            <div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Start date</label>
                                    <div class="col-lg-8 input-group date" data-date-end-date="+0d">
                                        <input class="form-control"  onkeydown="return false" autocomplete="off"  name="start_date" type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                            </div>
                         </div>
                         <!--ko ifnot:checkbox-->
                         <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" >End date</label>
                                <div class="col-lg-8 input-group date" data-date-end-date="+0d">
                                    <input class="form-control"  onkeydown="return false"autocomplete="off" name="end_date" type="text"><span class="input-group-addon"><i class="fa fa-calendar" required ></i></span>
                                </div>
                            </div>
                         </div>
                         <!--/ko-->
                     </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <?php if((in_array('1', $member_staff_privilege))||(in_array('3', $member_staff_privilege))){ ?>
                    <button type="submit" class="btn btn-primary btn-flat" >Save </button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>
