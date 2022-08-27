<div id="add_company-modal" class="modal inmodal fade"  tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">×</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">New Company</h4>
                <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <form id="formCompany" name="formCompany" action="<?php echo site_url("group/create");?>" class="wizard-big formValidate">
                        <h1>Company Details</h1>
                        <input type="hidden" name="id" id="id" >
                        <input type="hidden" name="group_client_type" value="3" >
                        <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Company Name</label>
                            <label class="req text-danger">*</label>
                            <input  name="group_name" type="text" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label>Description</label>
                            <label class="req text-danger">*</label>
                            <textarea  name="description" class="form-control" required></textarea>
                        </div>
                        </div>
                        <div class="form-group row">
                        <div class="col-lg-6">
                        <label>Owner Name</label>
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
                        <div class="form-group row">
                        <div class="col-lg-6">
                        <label>Company Address</label>
                            <label class="req text-danger"></label>
                            <textarea  name="address"  colspan="4" rowspan="5"class="form-control"required></textarea>
                         
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

