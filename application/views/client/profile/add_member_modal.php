<div class="modal inmodal fade" id="add_member-modal" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post" class="formValidate" action="<?php echo site_url("u/member/create"); ?>" id="formMember">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Edit Your Profile</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                <input type="hidden" name="id">
                <input type="hidden" name="user_id">
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label"><?php echo $this->lang->line('cont_client_no'); ?><span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                  <input class="form-control" required name="client_no" type="text" readonly />

                                <!-- <input class="form-control" required name="client_no" data-bind="attr: {value:client_no, readonly:client_no()!==false?'readonly':'readonly'}" readonly /> -->
                            </div>
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
                            <div class="col-lg-4 form-group" >
                                <input placeholder="" required class="form-control" name="lastname" type="text" required>
                            </div>

                            <label class="col-lg-2 col-form-label">Other Name(s)</label>
                            <div class="col-lg-4 form-group" >
                                <input placeholder="" class="form-control" name="othernames" type="text"> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Gender<span class="text-danger">*</span></label>

                            <div class="col-lg-4 form-group" >
                                <center>
                                    <label> <input checked value="1" name="gender" type="radio" > Male</label>
                                    <label> <input value="0" name="gender" type="radio" required> Female</label>
                                </center>
                            </div>
                            <label class="col-lg-2 col-form-label">Marital Status<span class="text-danger">*</span></label>

                            <div class="col-lg-4 form-group" >      
                                <select class="form-control m-b" name="marital_status_id" data-bind="value: marital_status_id" required>
                                    <option value="">-- select --</option>
                                    <?php
                                    foreach ($marital_statuses as $marital_status) {
                                        echo "<option value='" . $marital_status['id'] . "'>" . $marital_status['marital_status_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div  id="spouse_data" data-bind="visible: typeof marital_status_id() !== 'undefined' && parseInt(marital_status_id())===3">
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Spouse Name</label>
                            <div class="col-lg-4 form-group" >
                                <input placeholder="Full Name" class="form-control" name="spouse_names" id="spouse_names" type="text">
                            </div>
                            <label class="col-lg-2 col-form-label">Contact</label>
                            <div class="col-lg-4 form-group" >
                                <input placeholder="Phone Number" class="form-control" name="spouse_contact" type="text"> 
                            </div>
                        </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Date Of Birth</label>
                            <div class="col-lg-4 form-group" >
                                <div class="input-group date" data-date-end-date="-12y">
                                    <input class="form-control" onkeydown="return false" autocomplete="off"  name="date_of_birth" type="text" ><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                            <label class="col-lg-2 col-form-label">Disability?<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group" >
                                <center>
                                    <label><input checked value="0" name="disability" type="radio" > No</label>
                                    <label> <input value="1" name="disability" type="radio" required> Yes</label>
                                </center>
                            </div>
                        </div>
                        <div class="form-group row">
                            <?php if(!isset($user['id'])){ ?>
                            <label class="col-lg-2 col-form-label">Contact<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group" >
                                <input name="mobile_number" type="tel" class="form-control" placeholder="" pattern="^(0|\+256)[2347]([0-9]{8})" data-pattern-error="Wrong number format, start with + 0" required /> 
                            </div>
                            <?php } ?>
                            <label class="col-lg-2 col-form-label">Email</label>

                            <div class="col-lg-4 form-group" >
                                <input placeholder="" class="form-control" name="email" type="email">
                            </div>
                        </div>
                        <?php if($org['children_comp']==1){ ?>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Number of Children</label>
                            <div class="col-lg-4">
                                <input placeholder="No. of children" class="form-control" name="children_no" type="number" min="0">
                            </div>
                            <label class="col-lg-2 col-form-label">Number of dependants</label>
                            <div class="col-lg-4">
                                <input class="form-control" name="dependants_no" type="number" min="0">
                            </div>
                        </div>
                        <?php } ?>

                        <div class="form-group row">

                            <label class="col-lg-2 col-form-label">CRB Card No.</label>

                            <div class="col-lg-4 form-group" >
                                <input placeholder="CRB Card No." class="form-control" name="crb_card_no" type="text">
                            </div>
                            <label class="col-lg-2 col-form-label">Occupation<span class="text-danger"></span></label>
                            <div class="col-lg-4 form-group" >
                                <input placeholder="Occupation" class="form-control" name="occupation" type="text"> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">NIN</label>
                            <div class="col-lg-4 form-group" >
                                <input placeholder="National ID Number" class="form-control" name="nid_card_no" type="text"> 
                            </div>
                           
                        </div>
                        

                
                      
                       
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Comment</label>
                            <div class="col-lg-10 form-group">
                                <textarea class="form-control" rows="3" name="comment" id="comment"></textarea>
                            </div>
                        </div>
                </div>

                <div class="modal-footer">
                        
                        <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data"><i class="fa fa-check"></i><?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            } else {
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
