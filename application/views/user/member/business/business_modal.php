<!-- bootstrap modal -->
<div class="modal inmodal fade" id="add_business-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <?php echo form_open_multipart("business/create", array('id' => 'formBusiness', 'class' => 'formValidate', 'name' => 'formBusiness', 'method' => 'post', 'role' => 'form')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title">Business Details</h3>
                <small class="font-bold">Please Make sure you enter all the required fields correctly</small>
            </div>
            <div class="modal-body">
                <input type="text" hidden name="id" id="id">
                <input type="text" hidden name="member_id" id="member_id"  value="<?php echo $user['id']; ?>">
                    <div class="row">
                        <div class="col">
                            <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Business name</label> 
                                <div class="input-group">
                                <input type="text" name="businessname" id="businessname" class="form-control m-b" required="required">
                                </div>
                           </div>
                        </div>
                        <div class="col">
                            <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Nature of business</label> 
                                <div class="input-group">
                                <input type="text" name="natureofbusiness" id="natureofbusiness" class="form-control m-b" required="required">
                                </div>
                            </div>
                        </div>
                        <div class="col">                             
                            <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Business Location</label> 
                                <div class="input-group">
                                <input type="text" name="businesslocation" id="businesslocation" class="form-control m-b" required="required">
                                </div>
                           </div>
                        </div>
                     
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="row">

                        
                        <div class="col">
                            <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Business worth(SHS)</label> 
                                <div class="input-group">
                                <input type="number" name="businessworth" id="businessworth" class="form-control m-b">
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">URSB No.</label> 
                                <div class="input-group">
                                <input type="number" name="ursbnumber" id="ursbnumber" class="form-control m-b" >
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Number of employees</label> 
                                <div class="input-group">
                                <input type="number" name="numberofemployees" id="numberofemployees" class="form-control m-b" required="required">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="row">
                    <div class="col">
                            <div class="form-group row m-xxs"><label class="col-xxl-8 col-form-label">Cert. of incorporation</label> 
                                <div class="custom-file">
                                    <input id="certificateofincorporation" name="certificateofincorporation"  type="file" class="custom-file-input" >
                                    <label class="custom-file-label">Choose file...</label>
                                </div>
                                <script>
                                $('.custom-file-input').on('change', function() {
                                let fileName = $(this).val().split('\\').pop();
                                $(this).next('.custom-file-label').addClass("selected").html(fileName);
                                }); 
                                </script>
                            </div>
                        </div>
                    </div>
              </div>
              <div class="modal-footer">
              <?php if((in_array('1', $member_privilege))||(in_array('3', $member_privilege))){ ?>
                <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                    <i class="fa fa-check"></i> Save</button>
              <?php } ?>
                <button type="button" id="btn-cancel" name="btn_cancel" data-dismiss="modal" class="btn btn-danger btn-sm cancel">
                    <i class="fa fa-times"></i> Cancel</button>
               </div>
        </form>
        </div>
    </div>
</div>
<!-- bootstrap modal ends -->