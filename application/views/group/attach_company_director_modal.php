<div id="attach_company_director_modal" class="modal inmodal fade"  tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">×</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">Attach Company Director</h4>
                <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <form id="formGroup" name="formGroup" action="<?php echo site_url("group/attach_director");?>" class="wizard-big formValidate">
                      
                        <input type="text" name="id" id="id" >
                        <input type="hidden" name="group_client_type" value="3" >
                       
                        <div class="form-group row">
                        <div class="col-lg-6">
                        <label>Full Name</label>
                            <label class="req text-danger">*</label>
                         
                            <input  name="owner_name" id="owner_name" type="text" class="form-control"required>
                        
                        </div>
                        <div class="col-lg-6">
                        <label>Primary Contact</label>
                            <label class="req text-danger">*</label>
                            <input  name="phone_number" id="phone_number" type="text" class="form-control"required>
                        
                        </div>
                        </div>
                        <div class="form-group row">
                        <div class="col-lg-6">
                        <label>NIN</label>
                            <label class="text-danger"></label>
                            <input  name="nin" id="nin" type="text" class="form-control">
                        
                        </div>
                        
                        <div class="col-lg-6">
                        <label>Email</label>
                            <label class=" text-danger"></label>
                            <input  name="email" id="email" type="text" class="form-control">
                        
                        </div>
                         
                        </div>
                       
                        <div class="form-group ">
                            <div class="col-sm-6 col-sm-offset-3">
                            <?php if((in_array('1', $group_privilege))||(in_array('3', $group_privilege))){ ?>
                                <button class="btn btn-primary btn-sm" type="submit">Submit</button>
                            <?php } ?>
                            </div>
                        </div>
                   
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

